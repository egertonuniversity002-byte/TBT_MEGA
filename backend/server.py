from flask import Flask, request, jsonify
from flask_cors import CORS
from pymongo import MongoClient
from bson import ObjectId
import bcrypt
import jwt
import datetime
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import random
import os
import requests
import base64
from functools import wraps
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Configuration
app.config['SECRET_KEY'] = os.environ.get('SECRET_KEY', 'dev-secret-change')
app.config['MONGO_URI'] = os.environ.get('MONGO_URI', os.environ.get('MONGO_URL', 'mongodb+srv://felixtanzaotienoofficial:KFOMrG4tuoUOeHWZ@cluster0.cllprpq.mongodb.net/mulapal_db?retryWrites=true&w=majority&appName=Cluster0'))
app.config['SMTP_SERVER'] = os.environ.get('SMTP_SERVER', 'smtp.gmail.com')
app.config['SMTP_PORT'] = int(os.environ.get('SMTP_PORT', 587))
app.config['SMTP_USERNAME'] = os.environ.get('SMTP_USERNAME', 'your-email@gmail.com')
app.config['SMTP_PASSWORD'] = os.environ.get('SMTP_PASSWORD', 'your-app-password')
app.config['BRAND_NAME'] = os.environ.get('BRAND_NAME', 'Matrix Platform')

# PesaPal Configuration
app.config['PESAPAL_ENVIRONMENT'] = os.environ.get('PESAPAL_ENVIRONMENT', 'sandbox')  # sandbox or live
app.config['PESAPAL_CONSUMER_KEY'] = os.environ.get('PESAPAL_CONSUMER_KEY', 'your-consumer-key')
app.config['PESAPAL_CONSUMER_SECRET'] = os.environ.get('PESAPAL_CONSUMER_SECRET', 'your-consumer-secret')
app.config['PESAPAL_CALLBACK_URL'] = os.environ.get('PESAPAL_CALLBACK_URL', 'https://yourdomain.com/payment-callback')

# Initialize MongoDB
client = MongoClient(app.config['MONGO_URI'])
db = client['mulapal_db']  # Explicitly specify database name

# Collections
users_collection = db.users
tokens_collection = db.tokens
earnings_collection = db.earnings
withdrawals_collection = db.withdrawals
vouchers_collection = db.vouchers
payments_collection = db.payments
ipn_collection = db.pesapal_ipn
referrals_collection = db.referrals
transactions_collection = db.transactions
tasks_collection = db.tasks
notifications_collection = db.notifications

# PesaPal API URLs
if app.config['PESAPAL_ENVIRONMENT'] == 'live':
    PESAPAL_API = 'https://www.pesapal.com/api'
    PESAPAL_AUTH_URL = 'https://www.pesapal.com/api/Auth/RequestToken'
    PESAPAL_ORDER_URL = 'https://www.pesapal.com/api/PostPesapalDirectOrderV4'
    PESAPAL_IPN_URL = 'https://www.pesapal.com/api/APIQuery/GetTransactionStatus'
else:
    PESAPAL_API = 'https://cybqa.pesapal.com/pesapalv3'
    PESAPAL_AUTH_URL = 'https://cybqa.pesapal.com/pesapalv3/api/Auth/RequestToken'
    PESAPAL_ORDER_URL = 'https://cybqa.pesapal.com/pesapalv3/api/PostPesapalDirectOrderV4'
    PESAPAL_IPN_URL = 'https://cybqa.pesapal.com/pesapalv3/api/APIQuery/GetTransactionStatus'

# Currency conversion rates (KES as base)
CURRENCY_RATES = {
    'KES': 1.0,
    'USD': 0.0071,  # 1 KES = 0.0071 USD
    'EUR': 0.0065,  # 1 KES = 0.0065 EUR
    'GBP': 0.0055,  # 1 KES = 0.0055 GBP
    'UGX': 26.5,    # 1 KES = 26.5 UGX
    'TZS': 18.5,    # 1 KES = 18.5 TZS
}

def convert_ksh_to_currency(amount_ksh, target_currency):
    """Convert KSH amount to target currency"""
    if target_currency not in CURRENCY_RATES:
        return amount_ksh  # Return original if currency not supported

    # Convert KSH to target currency
    return amount_ksh * CURRENCY_RATES[target_currency]

def convert_currency_to_ksh(amount, source_currency):
    """Convert amount from source currency to KSH"""
    if source_currency not in CURRENCY_RATES:
        return amount  # Return original if currency not supported

    # Convert source currency to KSH
    return amount / CURRENCY_RATES[source_currency]

def get_withdrawal_limit_ksh():
    """Get the base withdrawal limit in KSH"""
    return 150.0

def get_user_referral_balance(user_id):
    """Get user's referral/affiliate balance"""
    try:
        # Check if user has referral earnings in earnings collection
        earnings = earnings_collection.find_one({'user_id': user_id})
        if earnings and 'affiliate_earnings' in earnings:
            return float(earnings.get('affiliate_earnings', 0))

        # Fallback: check transactions for referral bonuses
        referral_transactions = transactions_collection.find({
            'user_id': user_id,
            'type': 'referral_bonus',
            'status': 'completed'
        })

        total_referral = 0
        for transaction in referral_transactions:
            total_referral += float(transaction.get('amount', 0))

        return total_referral
    except Exception as e:
        print(f"Error getting referral balance: {e}")
        return 0

# Helper Functions
def get_pesapal_token():
    """Get authentication token from PesaPal"""
    try:
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        data = {
            'consumer_key': app.config['PESAPAL_CONSUMER_KEY'],
            'consumer_secret': app.config['PESAPAL_CONSUMER_SECRET']
        }
        response = requests.post(PESAPAL_AUTH_URL, json=data, headers=headers)
        response_data = response.json()
        if response.status_code == 200 and 'token' in response_data:
            return response_data['token']
        else:
            print(f"PesaPal auth failed: {response_data}")
            return None
    except Exception as e:
        print(f"Error getting PesaPal token: {e}")
        return None

def token_required(f):
    """Decorator to verify JWT tokens"""
    @wraps(f)
    def decorated(*args, **kwargs):
        token = request.headers.get('Authorization')
        if not token:
            return jsonify({'message': 'Token is missing'}), 401
        
        try:
            # Remove 'Bearer ' prefix if present
            if token.startswith('Bearer '):
                token = token[7:]
            
            data = jwt.decode(token, app.config['SECRET_KEY'], algorithms=['HS256'])
            current_user = users_collection.find_one({'_id': ObjectId(data['user_id'])})
            if not current_user:
                return jsonify({'message': 'User not found'}), 401
        except jwt.ExpiredSignatureError:
            return jsonify({'message': 'Token has expired'}), 401
        except jwt.InvalidTokenError:
            return jsonify({'message': 'Invalid token'}), 401
        
        return f(current_user, *args, **kwargs)
    return decorated

@app.route('/payments/deposit/initiate', methods=['POST'])
@token_required
def initiate_deposit(current_user):
    data = request.get_json() or {}
    amount = float(data.get('amount', 0))
    currency = current_user.get('currency', 'KES')
    if amount <= 0:
        return jsonify({'status':'error','message':'Invalid amount'}), 400

    token = get_pesapal_token()
    if not token:
        return jsonify({'status':'error','message':'PesaPal auth failed'}), 500

    order = {
        'id': str(ObjectId()),
        'amount': amount,
        'currency': currency,
        'description': f"Deposit by {current_user.get('email')}",
        'callback_url': app.config['PESAPAL_CALLBACK_URL'],
        'notification_id': '',
        'billing_address': {
            'email_address': current_user.get('email'),
            'phone_number': current_user.get('phone', ''),
            'first_name': current_user.get('first_name',''),
            'last_name': current_user.get('last_name',''),
            'line_1': '', 'line_2': '', 'city': '', 'state': '', 'postal_code': '', 'country_code': ''
        }
    }

    headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': f'Bearer {token}' }
    try:
        # PesaPal v3 uses /api/Transactions/SubmitOrderRequest endpoint
        submit_url = f"{PESAPAL_API}/api/Transactions/SubmitOrderRequest"
        res = requests.post(submit_url, json=order, headers=headers)
        data = res.json()
        if res.status_code not in (200, 201):
            return jsonify({'status':'error','message': data}), 500

        # Save payment request
        payments_collection.insert_one({
            'user_id': current_user['_id'],
            'order_tracking_id': data.get('order_tracking_id') or data.get('orderTrackingId'),
            'merchant_reference': order['id'],
            'amount': amount,
            'currency': currency,
            'status': 'pending',
            'provider': 'pesapal',
            'created_at': datetime.datetime.utcnow()
        })
        return jsonify({'status':'success','data': data}), 200
    except Exception as e:
        return jsonify({'status':'error','message': str(e)}), 500

@app.route('/payments/deposit/callback', methods=['POST','GET'])
def pesapal_callback():
    try:
        payload = request.get_json(silent=True) or {}
        # Persist raw notification
        ipn_collection.insert_one({ 'payload': payload, 'created_at': datetime.datetime.utcnow() })
        # Extract tracking ID (may come as order_tracking_id or TrackingId)
        tracking_id = payload.get('order_tracking_id') or payload.get('TrackingId') or request.args.get('OrderTrackingId')
        if not tracking_id:
            return jsonify({'status':'error','message':'Missing tracking id'}), 400

        # Query transaction status
        token = get_pesapal_token()
        headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': f'Bearer {token}' }
        status_url = f"{PESAPAL_API}/api/Transactions/GetTransactionStatus?orderTrackingId={tracking_id}"
        sres = requests.get(status_url, headers=headers)
        sdata = sres.json()

        # Update payment record
        pay = payments_collection.find_one({'order_tracking_id': tracking_id})
        if not pay:
            payments_collection.insert_one({ 'order_tracking_id': tracking_id, 'status_payload': sdata, 'status': 'unknown', 'created_at': datetime.datetime.utcnow() })
        else:
            payments_collection.update_one({'_id': pay['_id']}, {'$set': {'status_payload': sdata}})

        status = (sdata.get('payment_status_description') or sdata.get('status') or '').upper()
        if status in ('COMPLETED','PAID','SUCCESS'):
            # Credit user balance once
            if not pay or pay.get('status') != 'completed':
                payments_collection.update_one({'order_tracking_id': tracking_id}, {'$set': {'status': 'completed', 'completed_at': datetime.datetime.utcnow()}})

                user = users_collection.find_one({'_id': pay['user_id']})
                amount = float(pay['amount'])
                currency = pay.get('currency','KES')

                # Calculate bonus based on amount
                bonus = 0
                if amount >= 5000:
                    bonus = amount * 0.20
                elif amount >= 2000:
                    bonus = amount * 0.15
                elif amount >= 1000:
                    bonus = amount * 0.10
                elif amount >= 500:
                    bonus = amount * 0.05
                elif amount >= 200:
                    bonus = amount * 0.02

                total_credit = amount + bonus
                # Update user balance with bonus
                users_collection.update_one({'_id': pay['user_id']}, {'$inc': {'balance': total_credit, 'total_earnings': total_credit}})

                # Record main transaction
                transactions_collection.insert_one({
                    'user_id': pay['user_id'], 'type': 'deposit', 'amount': amount, 'currency': currency,
                    'status':'completed', 'provider':'pesapal', 'tracking_id': tracking_id, 'created_at': datetime.datetime.utcnow()
                })

                # Record bonus transaction if any
                if bonus > 0:
                    transactions_collection.insert_one({
                        'user_id': pay['user_id'], 'type': 'bonus', 'amount': bonus, 'currency': currency,
                        'status':'completed', 'provider':'system', 'tracking_id': tracking_id, 'created_at': datetime.datetime.utcnow()
                    })

                # Notify user in-app
                try:
                    msg = f"Deposit of {format_amount(amount, currency)} successful."
                    if bonus > 0:
                        msg += f" Bonus {format_amount(bonus, currency)} credited."
                    create_notification(pay['user_id'], 'Deposit Successful', msg, 'success', {'tracking_id': tracking_id})
                except Exception:
                    pass

                # Handle referral bonus (45% of amount paid)
                if user and user.get('ref_code'):
                    referrer = users_collection.find_one({'referral_code': user['ref_code']})
                    if referrer:
                        referral_bonus = amount * 0.45
                        users_collection.update_one({'_id': referrer['_id']}, {'$inc': {'balance': referral_bonus, 'total_earnings': referral_bonus}})
                        transactions_collection.insert_one({
                            'user_id': referrer['_id'], 'type': 'referral_bonus', 'amount': referral_bonus, 'currency': currency,
                            'status':'completed', 'provider':'system', 'referred_user': str(user['_id']), 'created_at': datetime.datetime.utcnow()
                        })
                        # Notify referrer
                        try:
                            create_notification(referrer['_id'], 'Referral Bonus', f"You earned {format_amount(referral_bonus, currency)} from a referral deposit.", 'success', {'referred_user': str(user['_id'])})
                        except Exception:
                            pass
        elif status in ('FAILED','CANCELLED'):
            payments_collection.update_one({'order_tracking_id': tracking_id}, {'$set': {'status': status.lower(), 'completed_at': datetime.datetime.utcnow()}})
            # Notify user if we can find them
            if pay and pay.get('user_id'):
                try:
                    create_notification(pay['user_id'], 'Deposit Failed', 'Your deposit attempt failed or was cancelled.', 'error', {'tracking_id': tracking_id})
                except Exception:
                    pass
        else:
            payments_collection.update_one({'order_tracking_id': tracking_id}, {'$set': {'status': 'pending'}})

        return jsonify({'status':'success'}), 200
    except Exception as e:
        return jsonify({'status':'error','message': str(e)}), 500

@app.route('/withdrawals', methods=['POST'])
@token_required
def request_withdrawal(current_user):
    data = request.get_json() or {}
    amount = float(data.get('amount', 0))
    wallet = data.get('wallet', 'main')
    if amount <= 0:
        return jsonify({'status':'error','message':'Invalid amount'}), 400
    # Simple balance check
    user = users_collection.find_one({'_id': current_user['_id']})
    balance = float(user.get('balance', 0)) if wallet == 'main' else float(user.get('wallets', {}).get(wallet, 0))
    if amount > balance:
        return jsonify({'status':'error','message':'Insufficient balance'}), 400

    wid = withdrawals_collection.insert_one({
        'user_id': current_user['_id'],
        'amount': amount,
        'currency': current_user.get('currency','KES'),
        'wallet': wallet,
        'status': 'pending',
        'method': 'pesapal',
        'created_at': datetime.datetime.utcnow()
    }).inserted_id

    transactions_collection.insert_one({
        'user_id': current_user['_id'], 'type': 'withdraw_request', 'amount': amount, 'currency': current_user.get('currency','KES'),
        'wallet': wallet, 'status':'pending', 'created_at': datetime.datetime.utcnow()
    })

    # Notify user
    try:
        create_notification(
            current_user['_id'],
            'Withdrawal Requested',
            f"Your withdrawal request for {format_amount(amount, current_user.get('currency','KES'))} is pending.",
            'info',
            {'withdrawal_id': str(wid), 'wallet': wallet}
        )
    except Exception:
        pass

    return jsonify({'status':'success','id': str(wid), 'message':'Withdrawal request submitted'}), 200

def send_email(to_email, subject, body):
    """Send email using SMTP"""
    try:
        msg = MIMEMultipart()
        msg['From'] = app.config['SMTP_USERNAME']
        msg['To'] = to_email
        msg['Subject'] = subject
        
        msg.attach(MIMEText(body, 'html'))
        
        server = smtplib.SMTP(app.config['SMTP_SERVER'], app.config['SMTP_PORT'])
        server.starttls()
        server.login(app.config['SMTP_USERNAME'], app.config['SMTP_PASSWORD'])
        server.send_message(msg)
        server.quit()
        return True
    except Exception as e:
        print(f"Email error: {e}")
        return False

def create_notification(user_id, title, message, ntype='info', extra=None):
    """Create a notification for a user"""
    try:
        doc = {
            'user_id': user_id,
            'title': title,
            'message': message,
            'type': ntype,  # info | success | warning | error
            'is_read': False,
            'extra': extra or {},
            'created_at': datetime.datetime.utcnow()
        }
        notifications_collection.insert_one(doc)
    except Exception as e:
        # Don't crash main flow on notification failure
        print(f"Notification error: {e}")

@app.route('/notifications', methods=['GET'])
@token_required
def get_user_notifications(current_user):
    try:
        # Pagination (optional)
        page = int(request.args.get('page', 1))
        limit = max(1, min(int(request.args.get('limit', 20)), 100))
        skip = (page - 1) * limit

        cursor = notifications_collection.find({'user_id': current_user['_id']}).sort('created_at', -1).skip(skip).limit(limit)
        items = []
        for n in cursor:
            items.append({
                'id': str(n.get('_id')),
                'title': n.get('title'),
                'message': n.get('message'),
                'type': n.get('type', 'info'),
                'is_read': n.get('is_read', False),
                'extra': n.get('extra', {}),
                'createdAt': n.get('created_at').strftime('%Y-%m-%d %H:%M') if n.get('created_at') else ''
            })
        unread_count = notifications_collection.count_documents({'user_id': current_user['_id'], 'is_read': {'$ne': True}})
        total = notifications_collection.count_documents({'user_id': current_user['_id']})
        return jsonify({'status': 'success', 'data': {'items': items, 'unread_count': unread_count, 'page': page, 'limit': limit, 'total': total}}), 200
    except Exception as e:
        return jsonify({'message': f'Failed to fetch notifications: {str(e)}', 'status': 'error'}), 500

@app.route('/notifications/<notification_id>/read', methods=['POST'])
@token_required
def mark_notification_read(current_user, notification_id):
    try:
        notifications_collection.update_one({'_id': ObjectId(notification_id), 'user_id': current_user['_id']}, {'$set': {'is_read': True, 'read_at': datetime.datetime.utcnow()}})
        return jsonify({'status': 'success'}), 200
    except Exception as e:
        return jsonify({'message': f'Failed to mark notification as read: {str(e)}', 'status': 'error'}), 500

@app.route('/notifications/mark-all-read', methods=['POST'])
@token_required
def mark_all_notifications_read(current_user):
    try:
        result = notifications_collection.update_many({'user_id': current_user['_id'], 'is_read': {'$ne': True}}, {'$set': {'is_read': True, 'read_at': datetime.datetime.utcnow()}})
        return jsonify({'status': 'success', 'message': f'Marked {result.modified_count} as read'}), 200
    except Exception as e:
        return jsonify({'message': f'Failed to mark notifications as read: {str(e)}', 'status': 'error'}), 500

# Currency formatting helpers
currency_symbols = {
    'KES': 'KSh',
    'UGX': 'USh',
    'TZS': 'TSh',
    'USD': '$',
    'GBP': '£'
}

def format_amount(amount, currency):
    symbol = currency_symbols.get(currency)
    try:
        amt = float(amount)
    except Exception:
        amt = amount
    if symbol:
        return f"{symbol} {amt}"
    return f"{currency} {amt}"

def generate_reset_code():
    """Generate a 6-digit reset code"""
    return str(random.randint(100000, 999999))

def generate_token(user_id):
    """Generate JWT token"""
    payload = {
        'user_id': str(user_id),
        'exp': datetime.datetime.utcnow() + datetime.timedelta(days=7)
    }
    return jwt.encode(payload, app.config['SECRET_KEY'], algorithm='HS256')

# Referral helpers

def get_or_create_referral_code(user):
    code = user.get('referral_code')
    if not code:
        base = str(user.get('_id'))[-6:].upper()
        code = f"{base}{random.randint(100,999)}"
        users_collection.update_one({'_id': user['_id']}, {'$set': {'referral_code': code}})
    return code

def get_children_by_ref_code(code):
    return list(users_collection.find({'ref_code': code}))

@app.route('/team/list', methods=['GET'])
@token_required
def team_list(current_user):
    try:
        level = max(1, min(int(request.args.get('level', 1)), 3))
        status_filter = request.args.get('status')  # active | inactive | None

        # Seed level 1
        root_code = get_or_create_referral_code(current_user)
        level1 = get_children_by_ref_code(root_code)

        # Build next levels
        def ensure_codes(users):
            out = []
            for u in users:
                # ensure each referred user has its own referral_code for downstream levels
                code = get_or_create_referral_code(u)
                out.append((u, code))
            return out

        targets = []
        if level == 1:
            targets = [u for u in level1]
        elif level == 2:
            l1 = ensure_codes(level1)
            agg = []
            for u, code in l1:
                agg.extend(get_children_by_ref_code(code))
            targets = agg
        else:  # level 3
            l1 = ensure_codes(level1)
            l2 = []
            for u, code in l1:
                l2.extend(get_children_by_ref_code(code))
            l2 = ensure_codes(l2)
            l3 = []
            for u, code in l2:
                l3.extend(get_children_by_ref_code(code))
            targets = l3

        def user_status(u):
            if not u.get('is_active', True):
                return 'inactive'
            has_deposit = payments_collection.count_documents({'user_id': u['_id'], 'status': 'completed'}) > 0
            return 'active' if has_deposit else 'inactive'

        items = []
        for u in targets:
            st = user_status(u)
            if status_filter in ('active','inactive') and st != status_filter:
                continue
            items.append({
                'id': str(u.get('_id')),
                'name': u.get('name',''),
                'email': u.get('email',''),
                'status': st,
                'joined': u.get('created_at').strftime('%Y-%m-%d') if u.get('created_at') else ''
            })
        return jsonify({'status':'success','data': {'items': items}}), 200
    except Exception as e:
        return jsonify({'status':'error','message': str(e)}), 500

# Routes
@app.route('/')
def home():
    return jsonify({'message': f'{app.config["BRAND_NAME"]} Backend API', 'status': 'running'})

@app.route('/register', methods=['POST'])
def register():
    try:
        data = request.get_json()
        
        # Check if user already exists
        if users_collection.find_one({'email': data['email']}):
            return jsonify({'message': 'User already exists', 'status': 'error'}), 400
        
        # Hash password
        hashed_password = bcrypt.hashpw(data['password'].encode('utf-8'), bcrypt.gensalt())
        
        # Create user document
        user_data = {
            'name': data['name'],
            'email': data['email'],
            'phone': data['phone'],
            'password': hashed_password,
            'currency': data.get('currency', 'USD'),
            'country': data.get('country'),
            'ref_code': data.get('ref'),
            'balance': 0,
            'total_earnings': 0,
            'created_at': datetime.datetime.utcnow(),
            'is_active': True,
            'role': 'user'  # default role
        }
        
        # Insert user
        result = users_collection.insert_one(user_data)
        user_id = result.inserted_id
        
        # Generate token
        token = generate_token(user_id)
        
        # Send welcome email
        email_body = f"""
        <h2>Welcome to MULAPAL, {data['name']}!</h2>
        <p>Your account has been successfully created.</p>
        <p>Start exploring our platform and boost your online impact with our 10+ digital tools!</p>
        <br>
        <p>Best regards,<br>MULAPAL Team</p>
        """
        send_email(data['email'], f'Welcome to {app.config["BRAND_NAME"]}', email_body)
        
        return jsonify({
            'message': 'Registration successful',
            'status': 'success',
            'token': token,
            'user_id': str(user_id),
            'role': 'user'
        }), 201
        
    except Exception as e:
        return jsonify({'message': f'Registration failed: {str(e)}', 'status': 'error'}), 500

@app.route('/login', methods=['POST'])
def login():
    try:
        data = request.get_json()
        user = users_collection.find_one({'email': data['email']})

        if not user:
            return jsonify({'message': 'User not found', 'status': 'error'}), 404

        if not bcrypt.checkpw(data['password'].encode('utf-8'), user['password']):
            return jsonify({'message': 'Invalid credentials', 'status': 'error'}), 401

        # Check if user has made payment (required before accessing dashboard)
        has_payment = payments_collection.count_documents({'user_id': user['_id'], 'status': 'completed'}) > 0

        # Generate token
        token = generate_token(user['_id'])

        return jsonify({
            'message': 'Login successful',
            'status': 'success',
            'token': token,
            'user_id': str(user['_id']),
            'user_name': user['name'],
            'role': user.get('role', 'user'),
            'has_payment': has_payment
        }), 200

    except Exception as e:
        return jsonify({'message': f'Login failed: {str(e)}', 'status': 'error'}), 500

@app.route('/forgot-password', methods=['POST'])
def forgot_password():
    try:
        data = request.get_json()
        email = data['email']
        
        user = users_collection.find_one({'email': email})
        if not user:
            return jsonify({'message': 'Email not found', 'status': 'error'}), 404
        
        # Generate reset code
        reset_code = generate_reset_code()
        
        # Store reset code in database (expires in 1 hour)
        tokens_collection.update_one(
            {'user_id': user['_id'], 'type': 'password_reset'},
            {
                '$set': {
                    'code': reset_code,
                    'expires_at': datetime.datetime.utcnow() + datetime.timedelta(hours=1),
                    'used': False
                },
                '$setOnInsert': {
                    'user_id': user['_id'],
                    'type': 'password_reset',
                    'created_at': datetime.datetime.utcnow()
                }
            },
            upsert=True
        )
        
        # Send email with reset code
        email_body = f"""
        <h2>Password Reset Request</h2>
        <p>You requested to reset your password. Use the code below to proceed:</p>
        <h3 style="background: #f4f4f4; padding: 10px; border-radius: 5px; letter-spacing: 3px;">
            {reset_code}
        </h3>
        <p>This code will expire in 1 hour.</p>
        <br>
        <p>If you didn't request this, please ignore this email.</p>
        """
        
        if send_email(email, f'{app.config["BRAND_NAME"]} - Password Reset Code', email_body):
            return jsonify({'message': 'Reset code sent to email', 'status': 'success'}), 200
        else:
            return jsonify({'message': 'Failed to send email', 'status': 'error'}), 500
            
    except Exception as e:
        return jsonify({'message': f'Error: {str(e)}', 'status': 'error'}), 500

@app.route('/verify-reset-code', methods=['POST'])
def verify_reset_code():
    try:
        data = request.get_json()
        email = data['email']
        code = data['resetCode']
        
        user = users_collection.find_one({'email': email})
        if not user:
            return jsonify({'message': 'User not found', 'status': 'error'}), 404
        
        # Check reset code
        reset_record = tokens_collection.find_one({
            'user_id': user['_id'],
            'type': 'password_reset',
            'code': code,
            'used': False,
            'expires_at': {'$gt': datetime.datetime.utcnow()}
        })
        
        if not reset_record:
            return jsonify({'message': 'Invalid or expired reset code', 'status': 'error'}), 400
        
        # Mark code as used and issue one-time reset token
        reset_token = jwt.encode({
            'user_id': str(user['_id']),
            'purpose': 'password_reset',
            'exp': datetime.datetime.utcnow() + datetime.timedelta(minutes=15)
        }, app.config['SECRET_KEY'], algorithm='HS256')
        
        tokens_collection.update_one(
            {'_id': reset_record['_id']},
            {'$set': {'used': True, 'reset_token': reset_token, 'reset_token_expires': datetime.datetime.utcnow() + datetime.timedelta(minutes=15)}}
        )
        
        return jsonify({'message': 'Reset code verified', 'status': 'success', 'reset_token': reset_token}), 200
        
    except Exception as e:
        return jsonify({'message': f'Error: {str(e)}', 'status': 'error'}), 500

@app.route('/reset-password', methods=['POST'])
def reset_password():
    try:
        data = request.get_json()
        new_password = data['newPassword']
        reset_token = data.get('resetToken')
        if not reset_token:
            return jsonify({'message': 'Reset token missing', 'status': 'error'}), 400
        
        # Validate reset token
        try:
            payload = jwt.decode(reset_token, app.config['SECRET_KEY'], algorithms=['HS256'])
            if payload.get('purpose') != 'password_reset':
                return jsonify({'message': 'Invalid reset token', 'status': 'error'}), 400
            user_id = payload['user_id']
        except jwt.ExpiredSignatureError:
            return jsonify({'message': 'Reset token expired', 'status': 'error'}), 400
        except jwt.InvalidTokenError:
            return jsonify({'message': 'Invalid reset token', 'status': 'error'}), 400
        
        user = users_collection.find_one({'_id': ObjectId(user_id)})
        if not user:
            return jsonify({'message': 'User not found', 'status': 'error'}), 404
        
        # Hash new password
        hashed_password = bcrypt.hashpw(new_password.encode('utf-8'), bcrypt.gensalt())
        
        # Update password
        users_collection.update_one(
            {'_id': user['_id']},
            {'$set': {'password': hashed_password}}
        )
        
        # Invalidate token (optional, cleanup)
        tokens_collection.update_many(
            {'user_id': user['_id'], 'type': 'password_reset'},
            {'$set': {'used': True}}
        )
        
        # Send confirmation email
        email_body = f"""
        <h2>Password Changed Successfully</h2>
        <p>Your {app.config['BRAND_NAME']} account password has been successfully reset.</p>
        <p>If you did not make this change, please contact support immediately.</p>
        """
        send_email(user['email'], f"{app.config['BRAND_NAME']} - Password Changed", email_body)
        
        return jsonify({'message': 'Password reset successful', 'status': 'success'}), 200
        
    except Exception as e:
        return jsonify({'message': f'Error: {str(e)}', 'status': 'error'}), 500

@app.route('/dashboard', methods=['GET'])
@token_required
def get_dashboard_data(current_user):
    try:
        # Check if user has made payment (required before accessing dashboard)
        has_payment = payments_collection.count_documents({'user_id': current_user['_id'], 'status': 'completed'}) > 0

        if not has_payment:
            return jsonify({
                'message': 'Payment required to access dashboard. Please make a deposit first.',
                'status': 'payment_required',
                'has_payment': False
            }), 403

        # Get user's earnings data
        earnings_data = earnings_collection.find_one({'user_id': current_user['_id']})

        if not earnings_data:
            # Initialize earnings data if not exists
            earnings_data = {
                'user_id': current_user['_id'],
                'today_earnings': 500,
                'total_earnings': 1080,
                'balance': 5,
                'withdrawn': 725,
                'affiliate_earnings': 770,
                'agent_bonus': 0,
                'ads_earnings': 0,
                'tiktok_earnings': 0,
                'youtube_earnings': 0,
                'trivia_earnings': 50,
                'blog_earnings': 0,
                'last_updated': datetime.datetime.utcnow()
            }
            earnings_collection.insert_one(earnings_data)

        # Calculate invested amount (first deposit/registration payment)
        first_deposit = payments_collection.find_one(
            {'user_id': current_user['_id'], 'status': 'completed'},
            sort=[('created_at', 1)]
        )
        invested_amount = float(first_deposit['amount']) if first_deposit else 0

        # Calculate profit (total earnings - invested amount)
        total_earnings = float(earnings_data['total_earnings'])
        profit_amount = max(0, total_earnings - invested_amount)

        # Prepare response
        dashboard_data = {
            'user': {
                'name': current_user['name'],
                'todayEarnings': earnings_data['today_earnings'],
                'totalEarnings': earnings_data['total_earnings'],
                'earningsGrowth': '+100%',
                'balance': earnings_data['balance'],
                'withdrawn': earnings_data['withdrawn'],
                'invested': invested_amount,
                'profit': profit_amount,
                'currency': current_user.get('currency', 'USD'),
                'currencySymbol': currency_symbols.get(current_user.get('currency', 'USD')),
                'affiliateEarnings': earnings_data['affiliate_earnings'],
                'agentBonus': earnings_data['agent_bonus'],
                'adsEarnings': earnings_data['ads_earnings'],
                'tiktokEarnings': earnings_data['tiktok_earnings'],
                'youtubeEarnings': earnings_data['youtube_earnings'],
                'triviaEarnings': earnings_data['trivia_earnings'],
                'blogEarnings': earnings_data['blog_earnings'],
                'affiliateLink': f"https://mulapal.com/register.php?ref={current_user['_id']}"
            },
            'timetable': [
                {'id': 1, 'name': 'Youtube', 'day1': 'Tuesday', 'day2': 'Wednesday', 'day3': 'Thursday', 'day4': 'Friday'},
                {'id': 2, 'name': 'Trivia', 'day1': 'Saturday', 'day2': 'Sunday', 'day3': 'Monday', 'day4': 'Tuesday'},
                {'id': 3, 'name': 'TikTok', 'day1': 'Tuesday', 'day2': 'Wednesday', 'day3': 'Thursday', 'day4': 'Wednesday'},
                {'id': 4, 'name': 'Whatsapp', 'day1': 'Monday', 'day2': 'Wednesday', 'day3': 'Thursday', 'day4': 'Friday'},
                {'id': 5, 'name': 'Trivia', 'day1': 'Monday', 'day2': 'Tuesday', 'day3': 'Wednesday', 'day4': 'Thursday'},
                {'id': 6, 'name': 'Ads', 'day1': 'Tuesday', 'day2': 'Monday', 'day3': 'Thursday', 'day4': 'Friday'},
                {'id': 7, 'name': 'Blogs', 'day1': 'Monday', 'day2': 'Wednesday', 'day3': 'Thursday', 'day4': 'Friday'}
            ]
        }

        return jsonify({'status': 'success', 'data': dashboard_data, 'has_payment': True}), 200

    except Exception as e:
        return jsonify({'message': f'Error fetching dashboard data: {str(e)}', 'status': 'error'}), 500

# PesaPal Payment Integration
@app.route('/pesapal', methods=['POST'])
@token_required
def initiate_pesapal_payment(current_user):
    try:
        data = request.get_json()
        amount = float(data['amount'])
        phone = data.get('phone', '')
        
        # Generate unique transaction ID
        trx_prefix = app.config['BRAND_NAME'].upper().replace(' ', '')
        transaction_id = f"{trx_prefix}_{datetime.datetime.utcnow().strftime('%Y%m%d%H%M%S')}_{random.randint(1000, 9999)}"
        
        # Get PesaPal token
        token = get_pesapal_token()
        if not token:
            return jsonify({'message': 'Payment service temporarily unavailable', 'status': 'error'}), 500
        
        # Prepare payment data
        # Set callback_url to frontend for user redirect after payment
        frontend_callback_url = 'http://localhost/frontend/pay/payment.php'  # Adjust for production
        payment_data = {
            'id': transaction_id,
            'currency': current_user.get('currency', 'USD'),
            'amount': amount,
            'description': f"{app.config['BRAND_NAME']} Deposit",
            'callback_url': frontend_callback_url,
            'notification_id': app.config['PESAPAL_CALLBACK_URL'],  # Keep backend for IPN
            'billing_address': {
                'email_address': current_user['email'],
                'phone_number': phone or current_user.get('phone', ''),
                'first_name': current_user['name'].split(' ')[0],
                'last_name': ' '.join(current_user['name'].split(' ')[1:]) if len(current_user['name'].split(' ')) > 1 else 'User'
            }
        }
        
        # Create payment record
        payment_record = {
            'user_id': current_user['_id'],
            'transaction_id': transaction_id,
            'amount': amount,
            'currency': current_user.get('currency', 'USD'),
            'amount_display': format_amount(amount, current_user.get('currency', 'USD')),
            'status': 'pending',
            'created_at': datetime.datetime.utcnow(),
            'updated_at': datetime.datetime.utcnow()
        }
        payments_collection.insert_one(payment_record)
        
        # Send request to PesaPal
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': f'Bearer {token}'
        }
        
        response = requests.post(PESAPAL_ORDER_URL, json=payment_data, headers=headers)
        
        if response.status_code == 200:
            response_data = response.json()
            if 'redirect_url' in response_data:
                # Update payment record with order tracking ID
                payments_collection.update_one(
                    {'transaction_id': transaction_id},
                    {'$set': {
                        'pesapal_order_tracking_id': response_data.get('order_tracking_id'),
                        'redirect_url': response_data.get('redirect_url')
                    }}
                )
                
                return jsonify({
                    'status': 'success',
                    'message': 'Payment initiated successfully',
                    'checkout_url': response_data['redirect_url'],
                    'transaction_id': transaction_id
                }), 200
            else:
                return jsonify({'message': 'Failed to initiate payment', 'status': 'error'}), 400
        else:
            return jsonify({'message': 'Payment service error', 'status': 'error'}), 500
            
    except Exception as e:
        return jsonify({'message': f'Payment initiation failed: {str(e)}', 'status': 'error'}), 500

@app.route('/payment-callback', methods=['POST', 'GET'])
def pesapal_callback_v2():
    """Handle PesaPal payment callback"""
    try:
        if request.method == 'GET':
            # Handle GET callback (usually for user redirect)
            order_tracking_id = request.args.get('OrderTrackingId')
            order_merchant_reference = request.args.get('OrderMerchantReference')
            
            if order_tracking_id and order_merchant_reference:
                # Update payment status
                payment = payments_collection.find_one({'transaction_id': order_merchant_reference})
                if payment:
                    # Get payment status from PesaPal
                    token = get_pesapal_token()
                    if token:
                        headers = {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': f'Bearer {token}'
                        }
                        
                        status_url = f"{PESAPAL_IPN_URL}?orderTrackingId={order_tracking_id}"
                        response = requests.get(status_url, headers=headers)
                        
                        if response.status_code == 200:
                            status_data = response.json()
                            payment_status = status_data.get('status', 'PENDING')
                            
                            # Update payment record
                            payments_collection.update_one(
                                {'transaction_id': order_merchant_reference},
                                {'$set': {
                                    'status': payment_status,
                                    'updated_at': datetime.datetime.utcnow(),
                                    'pesapal_status_data': status_data
                                }}
                            )
                            
                            # If payment is completed, update user balance
                            if payment_status == 'COMPLETED':
                                user_id = payment['user_id']
                                amount = payment['amount']
                                
                                users_collection.update_one(
                                    {'_id': user_id},
                                    {'$inc': {'balance': amount}}
                                )
                                
                                # Record transaction
                                transaction_data = {
                                    'user_id': user_id,
                                    'type': 'deposit',
                                    'amount': amount,
                                    'method': 'PesaPal',
                                    'status': 'completed',
                                    'transaction_id': order_merchant_reference,
                                    'created_at': datetime.datetime.utcnow()
                                }
                                transactions_collection.insert_one(transaction_data)
                                
                                # Send confirmation email
                                user = users_collection.find_one({'_id': user_id})
                                if user:
                                    email_body = f"""
                                    <h2>Payment Confirmed</h2>
                                    <p>Your deposit of <strong>{format_amount(amount, user.get('currency', 'USD'))}</strong> has been successfully processed.</p>
                                    <p>Transaction ID: <strong>{order_merchant_reference}</strong></p>
                                    <p>Your new balance: <strong>{format_amount(user['balance'] + amount, user.get('currency', 'USD'))}</strong></p>
                                    <br>
                                    <p>Thank you for using {app.config['BRAND_NAME']}!</p>
                                    """
                                    send_email(user['email'], f"{app.config['BRAND_NAME']} - Payment Confirmed", email_body)
            
            return jsonify({'message': 'Callback processed', 'status': 'success'}), 200
        
        elif request.method == 'POST':
            # Handle IPN (Instant Payment Notification)
            data = request.get_json()
            order_tracking_id = data.get('OrderTrackingId')
            order_notification_type = data.get('OrderNotificationType')
            
            if order_tracking_id and order_notification_type == 'CHANGE':
                # Get payment status from PesaPal
                token = get_pesapal_token()
                if token:
                    headers = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': f'Bearer {token}'
                    }
                    
                    status_url = f"{PESAPAL_IPN_URL}?orderTrackingId={order_tracking_id}"
                    response = requests.get(status_url, headers=headers)
                    
                    if response.status_code == 200:
                        status_data = response.json()
                        payment_status = status_data.get('status', 'PENDING')
                        order_merchant_reference = status_data.get('order_merchant_reference', '')
                        
                        if order_merchant_reference:
                            # Update payment record
                            payments_collection.update_one(
                                {'pesapal_order_tracking_id': order_tracking_id},
                                {'$set': {
                                    'status': payment_status,
                                    'updated_at': datetime.datetime.utcnow(),
                                    'pesapal_status_data': status_data
                                }}
                            )
                            
                            # If payment is completed, update user balance
                            if payment_status == 'COMPLETED':
                                payment = payments_collection.find_one({'pesapal_order_tracking_id': order_tracking_id})
                                if payment:
                                    user_id = payment['user_id']
                                    amount = payment['amount']
                                    
                                    users_collection.update_one(
                                        {'_id': user_id},
                                        {'$inc': {'balance': amount}}
                                    )
                                    
                                    # Record transaction
                                    transaction_data = {
                                        'user_id': user_id,
                                        'type': 'deposit',
                                        'amount': amount,
                                        'method': 'PesaPal',
                                        'status': 'completed',
                                        'transaction_id': payment['transaction_id'],
                                        'created_at': datetime.datetime.utcnow()
                                    }
                                    transactions_collection.insert_one(transaction_data)
                                    
                                    # Send confirmation email
                                    user = users_collection.find_one({'_id': user_id})
                                    if user:
                                        email_body = f"""
                                        <h2>Payment Confirmed</h2>
                                        <p>Your deposit of <strong>{format_amount(amount, user.get('currency', 'USD'))}</strong> has been successfully processed.</p>
                                        <p>Transaction ID: <strong>{payment['transaction_id']}</strong></p>
                                        <p>Your new balance: <strong>{format_amount(user['balance'] + amount, user.get('currency', 'USD'))}</strong></p>
                                        <br>
                                        <p>Thank you for using {app.config['BRAND_NAME']}!</p>
                                        """
                                        send_email(user['email'], f"{app.config['BRAND_NAME']} - Payment Confirmed", email_body)
            
            return jsonify({'message': 'IPN processed', 'status': 'success'}), 200
            
    except Exception as e:
        print(f"PesaPal callback error: {e}")
        return jsonify({'message': 'Callback processing failed', 'status': 'error'}), 500

@app.route('/payment-status/<transaction_id>', methods=['GET'])
@token_required
def check_payment_status(current_user, transaction_id):
    """Check payment status for a transaction"""
    try:
        payment = payments_collection.find_one({
            'transaction_id': transaction_id,
            'user_id': current_user['_id']
        })
        
        if not payment:
            return jsonify({'message': 'Transaction not found', 'status': 'error'}), 404
        
        return jsonify({
            'status': 'success',
            'payment_status': payment['status'],
            'amount': payment['amount'],
            'transaction_id': payment['transaction_id'],
            'created_at': payment['created_at']
        }), 200
        
    except Exception as e:
        return jsonify({'message': f'Error checking payment status: {str(e)}', 'status': 'error'}), 500

@app.route('/deposit', methods=['POST'])
@token_required
def deposit(current_user):
    try:
        data = request.get_json()
        amount = float(data['amount'])
        
        # Update user balance
        users_collection.update_one(
            {'_id': current_user['_id']},
            {'$inc': {'balance': amount}}
        )
        
        # Record transaction
        transaction_data = {
            'user_id': current_user['_id'],
            'type': 'deposit',
            'amount': amount,
            'status': 'completed',
            'created_at': datetime.datetime.utcnow()
        }
        transactions_collection.insert_one(transaction_data)
        
        return jsonify({'message': 'Deposit successful', 'status': 'success'}), 200
        
    except Exception as e:
        return jsonify({'message': f'Deposit failed: {str(e)}', 'status': 'error'}), 500

@app.route('/withdraw', methods=['POST'])
@token_required
def withdraw(current_user):
    try:
        data = request.get_json()
        amount = float(data['amount'])
        method = data.get('method', 'M-Pesa')
        
        # Check if user has sufficient balance
        if current_user['balance'] < amount:
            return jsonify({'message': 'Insufficient balance', 'status': 'error'}), 400
        
        # Update user balance
        users_collection.update_one(
            {'_id': current_user['_id']},
            {'$inc': {'balance': -amount, 'total_withdrawn': amount}}
        )
        
        # Record withdrawal
        withdrawal_data = {
            'user_id': current_user['_id'],
            'amount': amount,
            'method': method,
            'status': 'pending',
            'created_at': datetime.datetime.utcnow()
        }
        result = withdrawals_collection.insert_one(withdrawal_data)

        # In-app notification
        create_notification(
            current_user['_id'],
            'Withdrawal Requested',
            f"Your withdrawal request for {format_amount(amount, current_user.get('currency', 'USD'))} is pending.",
            'info',
            {'withdrawal_id': str(result.inserted_id)}
        )
        
        # Email notification
        email_body = f"""
        <h2>Withdrawal Request Received</h2>
        <p>Your withdrawal request for <strong>{format_amount(amount, current_user.get('currency', 'USD'))}</strong> has been received and is being processed.</p>
        <p>It usually takes 24-48 hours for the funds to reflect in your account.</p>
        <br>
        <p>Thank you for using {app.config['BRAND_NAME']}!</p>
        """
        send_email(current_user['email'], f"{app.config['BRAND_NAME']} - Withdrawal Request", email_body)
        
        return jsonify({'message': 'Withdrawal request submitted', 'status': 'success'}), 200
        
    except Exception as e:
        return jsonify({'message': f'Withdrawal failed: {str(e)}', 'status': 'error'}), 500

@app.route('/buy-voucher', methods=['POST'])
@token_required
def buy_voucher(current_user):
    try:
        data = request.get_json()
        voucher_type = data['type']
        amount = float(data['amount'])
        
        # Check if user has sufficient balance
        if current_user['balance'] < amount:
            return jsonify({'message': 'Insufficient balance', 'status': 'error'}), 400
        
        # Update user balance
        users_collection.update_one(
            {'_id': current_user['_id']},
            {'$inc': {'balance': -amount}}
        )
        
        # Generate voucher
        voucher_code = f"{app.config['BRAND_NAME'].upper().split()[0]}-VOUCHER-{random.randint(100000, 999999)}"
        voucher_data = {
            'user_id': current_user['_id'],
            'code': voucher_code,
            'type': voucher_type,
            'amount': amount,
            'created_at': datetime.datetime.utcnow(),
            'expires_at': datetime.datetime.utcnow() + datetime.timedelta(days=30),
            'is_used': False
        }
        vouchers_collection.insert_one(voucher_data)
        
        # Send voucher email
        email_body = f"""
        <h2>Your Voucher Purchase</h2>
        <p>You have successfully purchased a {voucher_type} voucher.</p>
        <p><strong>Voucher Code:</strong> {voucher_code}</p>
        <p><strong>Amount:</strong> KSH {amount}</p>
        <p><strong>Expires:</strong> {voucher_data['expires_at'].strftime('%Y-%m-%d')}</p>
        <br>
        <p>Thank you for using MULAPAL!</p>
        """
        send_email(current_user['email'], 'MULAPAL - Voucher Purchase', email_body)
        
        return jsonify({
            'message': 'Voucher purchased successfully',
            'voucher_code': voucher_code,
            'status': 'success'
        }), 200
        
    except Exception as e:
        return jsonify({'message': f'Voucher purchase failed: {str(e)}', 'status': 'error'}), 500

@app.route('/withdrawals', methods=['GET'])
@token_required
def get_withdrawals(current_user):
    try:
        # Pagination params
        page = int(request.args.get('page', 1))
        limit = max(1, min(int(request.args.get('limit', 10)), 100))
        skip = (page - 1) * limit

        # Query user's withdrawals
        cursor = withdrawals_collection.find({'user_id': current_user['_id']}).sort('created_at', -1).skip(skip).limit(limit)
        items = []
        for w in cursor:
            items.append({
                'id': str(w.get('_id')),
                'amount': w.get('amount', 0),
                'amountDisplay': format_amount(w.get('amount', 0), current_user.get('currency', 'USD')),
                'status': w.get('status', 'pending'),
                'method': w.get('method', ''),
                'createdAt': w.get('created_at').strftime('%Y-%m-%d') if w.get('created_at') else ''
            })

        total = withdrawals_collection.count_documents({'user_id': current_user['_id']})
        return jsonify({
            'status': 'success',
            'data': {
                'items': items,
                'page': page,
                'limit': limit,
                'total': total
            }
        }), 200
    except Exception as e:
        return jsonify({'message': f'Error fetching withdrawals: {str(e)}', 'status': 'error'}), 500

@app.route('/tasks', methods=['GET'])
@token_required
def list_tasks(current_user):
    try:
        category = request.args.get('category')  # e.g., tiktok, youtube, trivia, ads, whatsapp, blogs
        page = int(request.args.get('page', 1))
        limit = max(1, min(int(request.args.get('limit', 10)), 100))
        skip = (page - 1) * limit

        query = { 'active': True }
        if category:
            query['category'] = category

        cursor = tasks_collection.find(query).sort('created_at', -1).skip(skip).limit(limit)
        items = []
        for t in cursor:
            items.append({
                'id': str(t.get('_id')),
                'title': t.get('title', ''),
                'category': t.get('category', ''),
                'price': t.get('price', 0),
                'priceDisplay': format_amount(t.get('price', 0), current_user.get('currency', 'USD')),
                'rewardWallet': t.get('reward_wallet', 'main'),
                'instructions': t.get('instructions', ''),
                'target_url': t.get('target_url', ''),
                'image_url': t.get('image_url', ''),
            })
        total = tasks_collection.count_documents(query)
        return jsonify({'status': 'success', 'data': {'items': items, 'page': page, 'limit': limit, 'total': total}}), 200
    except Exception as e:
        return jsonify({'message': f'Error fetching tasks: {str(e)}', 'status': 'error'}), 500

@app.route('/tasks', methods=['POST'])
@token_required
def create_task(current_user):
    try:
        # Simple admin check by email for demo; replace with real role
        admin_emails = os.environ.get('ADMIN_EMAILS', '').split(',') if os.environ.get('ADMIN_EMAILS') else []
        if current_user['email'] not in admin_emails:
            return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

        data = request.get_json()
        task = {
            'title': data['title'],
            'category': data['category'], # tiktok/youtube/trivia/ads/whatsapp/blogs/etc
            'price': float(data['price']),
            'reward_wallet': data.get('reward_wallet', 'main'),
            'instructions': data.get('instructions', ''),
            'target_url': data.get('target_url', ''),
            'image_url': data.get('image_url', ''),
            'active': bool(data.get('active', True)),
            'created_at': datetime.datetime.utcnow()
        }
        res = tasks_collection.insert_one(task)
        return jsonify({'status': 'success', 'id': str(res.inserted_id)}), 201
    except Exception as e:
        return jsonify({'message': f'Error creating task: {str(e)}', 'status': 'error'}), 500

def is_admin(user):
    admin_emails = os.environ.get('ADMIN_EMAILS', '').split(',') if os.environ.get('ADMIN_EMAILS') else []
    return user.get('email') in admin_emails

@app.route('/tasks/admin', methods=['GET'])
@token_required
def admin_list_tasks(current_user):
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403
    page = int(request.args.get('page', 1))
    limit = max(1, min(int(request.args.get('limit', 20)), 100))
    skip = (page - 1) * limit
    query = {}
    category = request.args.get('category')
    if category:
        query['category'] = category
    active = request.args.get('active')
    if active is not None:
        query['active'] = active.lower() == 'true'
    cursor = tasks_collection.find(query).sort('created_at', -1).skip(skip).limit(limit)
    items = []
    for t in cursor:
        items.append({
            'id': str(t.get('_id')),
            'title': t.get('title',''),
            'category': t.get('category',''),
            'price': t.get('price',0),
            'reward_wallet': t.get('reward_wallet','main'),
            'instructions': t.get('instructions',''),
            'active': t.get('active', True),
            'created_at': t.get('created_at').strftime('%Y-%m-%d') if t.get('created_at') else ''
        })
    total = tasks_collection.count_documents(query)
    return jsonify({'status':'success','data':{'items':items,'page':page,'limit':limit,'total':total}}), 200

@app.route('/tasks/<task_id>', methods=['PATCH'])
@token_required
def admin_update_task(current_user, task_id):
    if not is_admin(current_user):
        return jsonify({'message':'Forbidden','status':'error'}), 403
    data = request.get_json()
    allowed = {k:v for k,v in data.items() if k in ['title','category','price','reward_wallet','instructions','target_url','image_url','active']}
    if 'price' in allowed:
        allowed['price'] = float(allowed['price'])
    if not allowed:
        return jsonify({'message':'No fields to update','status':'error'}), 400
    tasks_collection.update_one({'_id': ObjectId(task_id)}, {'$set': allowed})
    return jsonify({'status':'success','message':'Task updated'}), 200

@app.route('/tasks/<task_id>', methods=['DELETE'])
@token_required
def admin_delete_task(current_user, task_id):
    if not is_admin(current_user):
        return jsonify({'message':'Forbidden','status':'error'}), 403
    tasks_collection.delete_one({'_id': ObjectId(task_id)})
    return jsonify({'status':'success','message':'Task deleted'}), 200

@app.route('/tasks/claim', methods=['POST'])
@token_required
def claim_task(current_user):
    try:
        data = request.get_json()
        task_id = data.get('task_id')
        if not task_id:
            return jsonify({'message': 'task_id required', 'status': 'error'}), 400
        task = tasks_collection.find_one({'_id': ObjectId(task_id), 'active': True})
        if not task:
            return jsonify({'message': 'Task not found', 'status': 'error'}), 404

        price = float(task.get('price', 0))
        wallet = task.get('reward_wallet', 'main')

        # Update user wallet/balance
        if wallet == 'main':
            users_collection.update_one({'_id': current_user['_id']}, {'$inc': {'balance': price, 'total_earnings': price}})
        else:
            # store per-wallet earnings in users doc (nested)
            users_collection.update_one({'_id': current_user['_id']}, {'$inc': {f'wallets.{wallet}': price, 'total_earnings': price}})

        # Record transaction
        transactions_collection.insert_one({
            'user_id': current_user['_id'],
            'type': 'task_reward',
            'amount': price,
            'currency': current_user.get('currency', 'USD'),
            'wallet': wallet,
            'task_id': task['_id'],
            'status': 'completed',
            'created_at': datetime.datetime.utcnow()
        })

        return jsonify({'status': 'success', 'message': 'Reward credited'}), 200
    except Exception as e:
        return jsonify({'message': f'Error claiming task: {str(e)}', 'status': 'error'}), 500

@app.route('/profile', methods=['GET'])
@token_required
def get_profile(current_user):
    try:
        # Remove sensitive data
        user_data = {
            'name': current_user['name'],
            'email': current_user['email'],
            'phone': current_user['phone'],
            'balance': current_user.get('balance', 0),
            'total_earnings': current_user.get('total_earnings', 0),
            'currency': current_user.get('currency', 'USD'),
            'joined_date': current_user['created_at'].strftime('%Y-%m-%d')
        }
        
        return jsonify({'status': 'success', 'data': user_data}), 200
        
    except Exception as e:
        return jsonify({'message': f'Error fetching profile: {str(e)}', 'status': 'error'}), 500

@app.route('/profile/update', methods=['POST'])
@token_required
def update_profile(current_user):
    try:
        data = request.get_json()
        
        update_data = {}
        if 'name' in data:
            update_data['name'] = data['name']
        if 'phone' in data:
            update_data['phone'] = data['phone']
        
        if update_data:
            users_collection.update_one(
                {'_id': current_user['_id']},
                {'$set': update_data}
            )
        
        return jsonify({'message': 'Profile updated successfully', 'status': 'success'}), 200
        
    except Exception as e:
        return jsonify({'message': f'Profile update failed: {str(e)}', 'status': 'error'}), 500

# Live status endpoint for UI badge
@app.route('/live-status', methods=['GET'])
def live_status():
    # TODO: replace with real logic (DB flag, active sessions, etc.)
    return jsonify({'is_live': True}), 200

# ==========================================
# ADMIN ENDPOINTS
# ==========================================

@app.route('/admin/dashboard', methods=['GET'])
@token_required
def admin_dashboard(current_user):
    """Admin dashboard with platform statistics"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        # Get statistics
        total_users = users_collection.count_documents({})
        active_users = users_collection.count_documents({'is_active': True})
        total_deposits = payments_collection.count_documents({'status': 'completed'})
        total_withdrawals = withdrawals_collection.count_documents({'status': 'approved'})
        pending_withdrawals = withdrawals_collection.count_documents({'status': 'pending'})

        # Calculate total deposit amount
        deposit_pipeline = [
            {'$match': {'status': 'completed'}},
            {'$group': {'_id': None, 'total': {'$sum': '$amount'}}}
        ]
        deposit_result = list(payments_collection.aggregate(deposit_pipeline))
        total_deposit_amount = deposit_result[0]['total'] if deposit_result else 0

        # Calculate total withdrawal amount
        withdrawal_pipeline = [
            {'$match': {'status': 'approved'}},
            {'$group': {'_id': None, 'total': {'$sum': '$amount'}}}
        ]
        withdrawal_result = list(withdrawals_collection.aggregate(withdrawal_pipeline))
        total_withdrawal_amount = withdrawal_result[0]['total'] if withdrawal_result else 0

        dashboard_data = {
            'statistics': {
                'total_users': total_users,
                'active_users': active_users,
                'total_deposits': total_deposits,
                'total_withdrawals': total_withdrawals,
                'pending_withdrawals': pending_withdrawals,
                'total_deposit_amount': total_deposit_amount,
                'total_withdrawal_amount': total_withdrawal_amount,
                'platform_balance': total_deposit_amount - total_withdrawal_amount
            }
        }

        return jsonify({'status': 'success', 'data': dashboard_data}), 200

    except Exception as e:
        return jsonify({'message': f'Error fetching admin dashboard: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/users', methods=['GET'])
@token_required
def admin_list_users(current_user):
    """List all users with pagination and filters"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        page = int(request.args.get('page', 1))
        limit = max(1, min(int(request.args.get('limit', 20)), 100))
        skip = (page - 1) * limit

        # Filters
        query = {}
        status = request.args.get('status')
        if status == 'active':
            query['is_active'] = True
        elif status == 'inactive':
            query['is_active'] = False

        search = request.args.get('search')
        if search:
            query['$or'] = [
                {'name': {'$regex': search, '$options': 'i'}},
                {'email': {'$regex': search, '$options': 'i'}}
            ]

        cursor = users_collection.find(query).sort('created_at', -1).skip(skip).limit(limit)
        items = []
        for user in cursor:
            items.append({
                'id': str(user['_id']),
                'name': user.get('name', ''),
                'email': user.get('email', ''),
                'phone': user.get('phone', ''),
                'balance': user.get('balance', 0),
                'total_earnings': user.get('total_earnings', 0),
                'currency': user.get('currency', 'USD'),
                'is_active': user.get('is_active', True),
                'country': user.get('country', ''),
                'created_at': user.get('created_at').strftime('%Y-%m-%d %H:%M') if user.get('created_at') else ''
            })

        total = users_collection.count_documents(query)
        return jsonify({
            'status': 'success',
            'data': {
                'items': items,
                'page': page,
                'limit': limit,
                'total': total
            }
        }), 200

    except Exception as e:
        return jsonify({'message': f'Error fetching users: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/users/<user_id>/suspend', methods=['POST'])
@token_required
def admin_suspend_user(current_user, user_id):
    """Suspend user account"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        users_collection.update_one(
            {'_id': ObjectId(user_id)},
            {'$set': {
                'is_active': False,
                'suspended_at': datetime.datetime.utcnow(),
                'suspended_by': current_user['_id']
            }}
        )

        return jsonify({'status': 'success', 'message': 'User suspended successfully'}), 200

    except Exception as e:
        return jsonify({'message': f'Error suspending user: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/users/<user_id>/activate', methods=['POST'])
@token_required
def admin_activate_user(current_user, user_id):
    """Activate user account"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        users_collection.update_one(
            {'_id': ObjectId(user_id)},
            {'$set': {
                'is_active': True,
                'activated_at': datetime.datetime.utcnow(),
                'activated_by': current_user['_id']
            }}
        )

        return jsonify({'status': 'success', 'message': 'User activated successfully'}), 200

    except Exception as e:
        return jsonify({'message': f'Error activating user: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/withdrawals', methods=['GET'])
@token_required
def admin_list_withdrawals(current_user):
    """List all withdrawal requests"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        page = int(request.args.get('page', 1))
        limit = max(1, min(int(request.args.get('limit', 20)), 100))
        skip = (page - 1) * limit

        # Filters
        query = {}
        status = request.args.get('status')
        if status:
            query['status'] = status

        cursor = withdrawals_collection.find(query).sort('created_at', -1).skip(skip).limit(limit)
        items = []
        for w in cursor:
            user = users_collection.find_one({'_id': w['user_id']})
            items.append({
                'id': str(w['_id']),
                'user_id': str(w['user_id']),
                'user_name': user['name'] if user else 'Unknown',
                'user_email': user['email'] if user else 'Unknown',
                'amount': w.get('amount', 0),
                'currency': w.get('currency', 'USD'),
                'method': w.get('method', ''),
                'wallet': w.get('wallet', 'main'),
                'status': w.get('status', 'pending'),
                'created_at': w.get('created_at').strftime('%Y-%m-%d %H:%M') if w.get('created_at') else ''
            })

        total = withdrawals_collection.count_documents(query)
        return jsonify({
            'status': 'success',
            'data': {
                'items': items,
                'page': page,
                'limit': limit,
                'total': total
            }
        }), 200

    except Exception as e:
        return jsonify({'message': f'Error fetching withdrawals: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/withdrawals/<withdrawal_id>/approve', methods=['POST'])
@token_required
def admin_approve_withdrawal(current_user, withdrawal_id):
    """Approve withdrawal request"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        withdrawal = withdrawals_collection.find_one({'_id': ObjectId(withdrawal_id)})
        if not withdrawal:
            return jsonify({'message': 'Withdrawal not found', 'status': 'error'}), 404

        if withdrawal['status'] != 'pending':
            return jsonify({'message': 'Withdrawal already processed', 'status': 'error'}), 400

        # Update withdrawal status
        withdrawals_collection.update_one(
            {'_id': ObjectId(withdrawal_id)},
            {'$set': {
                'status': 'approved',
                'approved_at': datetime.datetime.utcnow(),
                'approved_by': current_user['_id']
            }}
        )

        # Update transaction status
        transactions_collection.update_one(
            {'user_id': withdrawal['user_id'], 'type': 'withdraw_request', 'created_at': withdrawal['created_at']},
            {'$set': {'status': 'approved'}}
        )

        # Send in-app notification and email to user
        user = users_collection.find_one({'_id': withdrawal['user_id']})
        if user:
            # In-app
            create_notification(
                withdrawal['user_id'],
                'Withdrawal Approved',
                f"Your withdrawal of {format_amount(withdrawal['amount'], withdrawal.get('currency', 'USD'))} has been approved.",
                'success',
                {'withdrawal_id': str(withdrawal['_id'])}
            )
            # Email
            email_body = f"""
            <h2>Withdrawal Approved</h2>
            <p>Your withdrawal request for <strong>{format_amount(withdrawal['amount'], withdrawal.get('currency', 'USD'))}</strong> has been approved.</p>
            <p>The funds will be processed according to your selected payment method.</p>
            <br>
            <p>Thank you for using {app.config['BRAND_NAME']}!</p>
            """
            send_email(user['email'], f"{app.config['BRAND_NAME']} - Withdrawal Approved", email_body)

        return jsonify({'status': 'success', 'message': 'Withdrawal approved successfully'}), 200

    except Exception as e:
        return jsonify({'message': f'Error approving withdrawal: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/withdrawals/<withdrawal_id>/reject', methods=['POST'])
@token_required
def admin_reject_withdrawal(current_user, withdrawal_id):
    """Reject withdrawal request"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        data = request.get_json()
        reason = data.get('reason', 'Request rejected by admin')

        withdrawal = withdrawals_collection.find_one({'_id': ObjectId(withdrawal_id)})
        if not withdrawal:
            return jsonify({'message': 'Withdrawal not found', 'status': 'error'}), 404

        if withdrawal['status'] != 'pending':
            return jsonify({'message': 'Withdrawal already processed', 'status': 'error'}), 400

        # Refund amount to user balance
        users_collection.update_one(
            {'_id': withdrawal['user_id']},
            {'$inc': {'balance': withdrawal['amount']}}
        )

        # Update withdrawal status
        withdrawals_collection.update_one(
            {'_id': ObjectId(withdrawal_id)},
            {'$set': {
                'status': 'rejected',
                'rejected_at': datetime.datetime.utcnow(),
                'rejected_by': current_user['_id'],
                'rejection_reason': reason
            }}
        )

        # Update transaction status
        transactions_collection.update_one(
            {'user_id': withdrawal['user_id'], 'type': 'withdraw_request', 'created_at': withdrawal['created_at']},
            {'$set': {'status': 'rejected'}}
        )

        # Send in-app notification and email to user
        user = users_collection.find_one({'_id': withdrawal['user_id']})
        if user:
            # In-app
            create_notification(
                withdrawal['user_id'],
                'Withdrawal Rejected',
                f"Your withdrawal for {format_amount(withdrawal['amount'], withdrawal.get('currency', 'USD'))} was rejected. Reason: {reason}.",
                'error',
                {'withdrawal_id': str(withdrawal['_id']), 'reason': reason}
            )
            # Email
            email_body = f"""
            <h2>Withdrawal Rejected</h2>
            <p>Your withdrawal request for <strong>{format_amount(withdrawal['amount'], withdrawal.get('currency', 'USD'))}</strong> has been rejected.</p>
            <p><strong>Reason:</strong> {reason}</p>
            <p>The amount has been refunded to your account balance.</p>
            <br>
            <p>If you have any questions, please contact support.</p>
            """
            send_email(user['email'], f"{app.config['BRAND_NAME']} - Withdrawal Rejected", email_body)

        return jsonify({'status': 'success', 'message': 'Withdrawal rejected and amount refunded'}), 200

    except Exception as e:
        return jsonify({'message': f'Error rejecting withdrawal: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/notifications/broadcast', methods=['POST'])
@token_required
def admin_broadcast_notification(current_user):
    """Send broadcast notification to all users"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        data = request.get_json()
        title = data.get('title')
        message = data.get('message')
        notification_type = data.get('type', 'info')  # info, warning, success, error

        if not title or not message:
            return jsonify({'message': 'Title and message are required', 'status': 'error'}), 400

        users = list(users_collection.find({}, {'_id': 1}))
        count = 0
        for u in users:
            try:
                create_notification(u['_id'], title, message, notification_type)
                count += 1
            except Exception:
                pass
        return jsonify({'status': 'success', 'message': f'Notification broadcast to {count} users'}), 200

    except Exception as e:
        return jsonify({'message': f'Error broadcasting notification: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/email/broadcast', methods=['POST'])
@token_required
def admin_broadcast_email(current_user):
    """Send broadcast email to all users"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        data = request.get_json()
        subject = data.get('subject')
        message = data.get('message')
        user_filter = data.get('filter', {})  # Optional filter for users

        if not subject or not message:
            return jsonify({'message': 'Subject and message are required', 'status': 'error'}), 400

        # Get users based on filter
        query = {'is_active': True}
        if user_filter.get('country'):
            query['country'] = user_filter['country']
        if user_filter.get('currency'):
            query['currency'] = user_filter['currency']

        users = list(users_collection.find(query, {'email': 1, 'name': 1}))

        # Send emails
        sent_count = 0
        failed_count = 0

        for user in users:
            personalized_message = message.replace('{{name}}', user.get('name', 'User'))
            email_body = f"""
            <h2>{subject}</h2>
            <p>{personalized_message}</p>
            <br>
            <p>Best regards,<br>{app.config['BRAND_NAME']} Team</p>
            """

            if send_email(user['email'], subject, email_body):
                sent_count += 1
            else:
                failed_count += 1

        return jsonify({
            'status': 'success',
            'message': f'Email broadcast completed. Sent: {sent_count}, Failed: {failed_count}',
            'sent_count': sent_count,
            'failed_count': failed_count
        }), 200

    except Exception as e:
        return jsonify({'message': f'Error broadcasting email: {str(e)}', 'status': 'error'}), 500

@app.route('/admin/statistics', methods=['GET'])
@token_required
def admin_statistics(current_user):
    """Get detailed platform statistics"""
    if not is_admin(current_user):
        return jsonify({'message': 'Forbidden', 'status': 'error'}), 403

    try:
        # User statistics
        total_users = users_collection.count_documents({})
        active_users = users_collection.count_documents({'is_active': True})
        inactive_users = total_users - active_users

        # Financial statistics
        total_deposits = payments_collection.count_documents({'status': 'completed'})
        total_withdrawals = withdrawals_collection.count_documents({'status': 'approved'})
        pending_withdrawals = withdrawals_collection.count_documents({'status': 'pending'})

        # Amount statistics
        deposit_pipeline = [
            {'$match': {'status': 'completed'}},
            {'$group': {'_id': None, 'total': {'$sum': '$amount'}}}
        ]
        deposit_result = list(payments_collection.aggregate(deposit_pipeline))
        total_deposit_amount = deposit_result[0]['total'] if deposit_result else 0

        withdrawal_pipeline = [
            {'$match': {'status': 'approved'}},
            {'$group': {'_id': None, 'total': {'$sum': '$amount'}}}
        ]
        withdrawal_result = list(withdrawals_collection.aggregate(withdrawal_pipeline))
        total_withdrawal_amount = withdrawal_result[0]['total'] if withdrawal_result else 0

        statistics = {
            'overview': {
                'total_users': total_users,
                'active_users': active_users,
                'inactive_users': inactive_users,
                'total_deposits': total_deposits,
                'total_withdrawals': total_withdrawals,
                'pending_withdrawals': pending_withdrawals,
                'total_deposit_amount': total_deposit_amount,
                'total_withdrawal_amount': total_withdrawal_amount,
                'platform_balance': total_deposit_amount - total_withdrawal_amount
            }
        }

        return jsonify({'status': 'success', 'data': statistics}), 200

    except Exception as e:
        return jsonify({'message': f'Error fetching statistics: {str(e)}', 'status': 'error'}), 500

if __name__ == '__main__':
    # Create indexes
    try:
        users_collection.create_index('email', unique=True)
        tokens_collection.create_index('expires_at', expireAfterSeconds=0)
        payments_collection.create_index('transaction_id', unique=True)
    except Exception as idx_err:
        print(f"Index creation warning: {idx_err}")
    
    # Run the server
    app.run(debug=True, host='0.0.0.0', port=5000)