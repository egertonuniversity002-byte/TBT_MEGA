"""
FastAPI Server - Modern Python Web API
Replaces the Flask server with FastAPI for better performance and type safety
"""

from fastapi import FastAPI, HTTPException, Depends, status, Request, BackgroundTasks, Query
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from motor.motor_asyncio import AsyncIOMotorClient
from pydantic import BaseModel, EmailStr, Field, validator
from typing import Optional, List, Dict, Any
from datetime import datetime, timedelta
from passlib.context import CryptContext
from jose import JWTError, jwt
from bson import ObjectId
import os
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import random
import requests
import base64
from dotenv import load_dotenv
import json
import re
import hashlib
import secrets
from io import BytesIO

# Load environment variables
load_dotenv()

# FastAPI app instance
app = FastAPI(
    title="MULAPAL Platform API",
    description="Modern FastAPI backend for MULAPAL platform",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure appropriately for production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Security
security = HTTPBearer()
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

# Configuration
class Settings:
    SECRET_KEY: str = os.environ.get('SECRET_KEY', 'dev-secret-change')
    MONGO_URI: str = os.environ.get('MONGO_URI', os.environ.get('MONGO_URL', 'mongodb+srv://felixtanzaotienoofficial:KFOMrG4tuoUOeHWZ@cluster0.cllprpq.mongodb.net/mulapal_db?retryWrites=true&w=majority&appName=Cluster0'))
    SMTP_SERVER: str = os.environ.get('SMTP_SERVER', 'smtp.gmail.com')
    SMTP_PORT: int = int(os.environ.get('SMTP_PORT', 587))
    SMTP_USERNAME: str = os.environ.get('SMTP_USERNAME', 'your-email@gmail.com')
    SMTP_PASSWORD: str = os.environ.get('SMTP_PASSWORD', 'your-app-password')
    BRAND_NAME: str = os.environ.get('BRAND_NAME', 'Matrix Platform')
    ALGORITHM: str = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 30

    # PesaPal Configuration
    PESAPAL_ENVIRONMENT: str = os.environ.get('PESAPAL_ENVIRONMENT', 'sandbox')
    PESAPAL_CONSUMER_KEY: str = os.environ.get('PESAPAL_CONSUMER_KEY', 'your-consumer-key')
    PESAPAL_CONSUMER_SECRET: str = os.environ.get('PESAPAL_CONSUMER_SECRET', 'your-consumer-secret')
    PESAPAL_CALLBACK_URL: str = os.environ.get('PESAPAL_CALLBACK_URL', 'https://yourdomain.com/payment-callback')

settings = Settings()

# Database connection
client = AsyncIOMotorClient(settings.MONGO_URI)
db = client['mulapal_db']

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
if settings.PESAPAL_ENVIRONMENT == 'live':
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

# Pydantic Models
class UserBase(BaseModel):
    name: str
    email: EmailStr
    phone: str
    currency: str = "USD"
    country: Optional[str] = None
    language: str = "en"

    @validator('name')
    def validate_name(cls, v):
        if not re.match(r'^[a-zA-Z\s]{2,50}$', v):
            raise ValueError('Name must be 2-50 characters and contain only letters and spaces')
        return v.strip()

    @validator('phone')
    def validate_phone(cls, v):
        if not re.match(r'^\+?[\d\s\-\(\)]{10,15}$', v):
            raise ValueError('Invalid phone number format')
        return v.strip()

class UserCreate(UserBase):
    password: str
    ref: Optional[str] = None

    @validator('password')
    def validate_password(cls, v):
        if len(v) < 8:
            raise ValueError('Password must be at least 8 characters long')
        if not re.match(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]', v):
            raise ValueError('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character')
        return v

class UserLogin(BaseModel):
    email: EmailStr
    password: str

class UserResponse(BaseModel):
    id: str
    name: str
    email: EmailStr
    phone: str
    balance: float
    total_earnings: float
    currency: str
    language: str
    joined_date: str

class Token(BaseModel):
    access_token: str
    token_type: str

class TokenData(BaseModel):
    user_id: Optional[str] = None

class ForgotPasswordRequest(BaseModel):
    email: EmailStr

class VerifyResetCodeRequest(BaseModel):
    email: EmailStr
    resetCode: str

class ResetPasswordRequest(BaseModel):
    newPassword: str
    resetToken: Optional[str] = None

class UpdateProfileRequest(BaseModel):
    name: Optional[str] = None
    phone: Optional[str] = None

class TeamMember(BaseModel):
    id: str
    name: str
    email: str
    status: str
    joined: str

class VoucherPurchase(BaseModel):
    type: str
    amount: float

class TaskCreate(BaseModel):
    title: str
    category: str
    price: float
    reward_wallet: str = "main"
    instructions: Optional[str] = None
    target_url: Optional[str] = None
    image_url: Optional[str] = None
    active: bool = True

class AdminEmailBroadcast(BaseModel):
    subject: str
    message: str
    filter: Optional[Dict[str, Any]] = None

class AdminNotificationBroadcast(BaseModel):
    title: str
    message: str
    type: str = "info"

# Authentication functions
def verify_password(plain_password: str, hashed_password: str) -> bool:
    """Verify a password against its hash"""
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password: str) -> str:
    """Hash a password"""
    return pwd_context.hash(password)

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    """Create JWT access token"""
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, settings.SECRET_KEY, algorithm=settings.ALGORITHM)
    return encoded_jwt

async def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security)):
    """Get current authenticated user"""
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(credentials.credentials, settings.SECRET_KEY, algorithms=[settings.ALGORITHM])
        user_id: str = payload.get("sub")
        if user_id is None:
            raise credentials_exception
        token_data = TokenData(user_id=user_id)
    except JWTError:
        raise credentials_exception

    # Convert string user_id back to ObjectId for database query
    try:
        user_object_id = ObjectId(user_id)
    except Exception:
        raise credentials_exception

    user = await users_collection.find_one({"_id": user_object_id})
    if user is None:
        raise credentials_exception
    return user

async def get_current_admin_user(current_user: dict = Depends(get_current_user)):
    """Get current admin user"""
    admin_emails = os.environ.get('ADMIN_EMAILS', '').split(',') if os.environ.get('ADMIN_EMAILS') else []
    if current_user.get('email') not in admin_emails:
        raise HTTPException(status_code=403, detail="Not enough permissions")
    return current_user

# Utility functions
def send_email(to_email: str, subject: str, body: str) -> bool:
    """Send email using SMTP"""
    try:
        msg = MIMEMultipart()
        msg['From'] = settings.SMTP_USERNAME
        msg['To'] = to_email
        msg['Subject'] = subject

        msg.attach(MIMEText(body, 'html'))

        server = smtplib.SMTP(settings.SMTP_SERVER, settings.SMTP_PORT)
        server.starttls()
        server.login(settings.SMTP_USERNAME, settings.SMTP_PASSWORD)
        server.send_message(msg)
        server.quit()
        return True
    except Exception as e:
        print(f"Email error: {e}")
        return False

def generate_reset_code() -> str:
    """Generate a 6-digit reset code"""
    return str(random.randint(100000, 999999))

# PesaPal helper functions
async def get_pesapal_token() -> Optional[str]:
    """Get authentication token from PesaPal"""
    try:
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        data = {
            'consumer_key': settings.PESAPAL_CONSUMER_KEY,
            'consumer_secret': settings.PESAPAL_CONSUMER_SECRET
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

def convert_ksh_to_currency(amount_ksh: float, target_currency: str) -> float:
    """Convert KSH amount to target currency"""
    if target_currency not in CURRENCY_RATES:
        return amount_ksh  # Return original if currency not supported

    # Convert KSH to target currency
    return amount_ksh * CURRENCY_RATES[target_currency]

def convert_currency_to_ksh(amount: float, source_currency: str) -> float:
    """Convert amount from source currency to KSH"""
    if source_currency not in CURRENCY_RATES:
        return amount  # Return original if currency not supported

    # Convert source currency to KSH
    return amount / CURRENCY_RATES[source_currency]

def format_amount(amount: float, currency: str) -> str:
    """Format amount with currency symbol"""
    currency_symbols = {
        'KES': 'KSh',
        'UGX': 'USh',
        'TZS': 'TSh',
        'USD': '$',
        'GBP': '£'
    }
    symbol = currency_symbols.get(currency, currency)
    return f"{symbol} {amount}"

# API Routes
@app.get("/")
async def root():
    """Root endpoint"""
    return {"message": f"{settings.BRAND_NAME} FastAPI Backend", "status": "running"}

@app.post("/register", response_model=Token)
async def register(user_data: UserCreate, background_tasks: BackgroundTasks):
    """Register a new user"""
    # Check if user already exists
    existing_user = await users_collection.find_one({"email": user_data.email})
    if existing_user:
        raise HTTPException(status_code=400, detail="User already exists")

    # Hash password
    hashed_password = get_password_hash(user_data.password)

    # Create user document
    user_doc = {
        "name": user_data.name,
        "email": user_data.email,
        "phone": user_data.phone,
        "password": hashed_password,
        "currency": user_data.currency,
        "country": user_data.country,
        "language": user_data.language,
        "ref_code": user_data.ref,
        "balance": 0,
        "total_earnings": 0,
        "created_at": datetime.utcnow(),
        "is_active": True,
        "role": "user"
    }

    # Insert user
    result = await users_collection.insert_one(user_doc)
    user_id = str(result.inserted_id)

    # Generate token
    access_token = create_access_token(
        data={"sub": user_id},
        expires_delta=timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    )

    # Send welcome email in background
    email_body = f"""
    <h2>Welcome to MULAPAL, {user_data.name}!</h2>
    <p>Your account has been successfully created.</p>
    <p>Start exploring our platform and boost your online impact with our 10+ digital tools!</p>
    <br>
    <p>Best regards,<br>MULAPAL Team</p>
    """
    background_tasks.add_task(send_email, user_data.email, f'Welcome to {settings.BRAND_NAME}', email_body)

    return Token(access_token=access_token, token_type="bearer")

@app.post("/login", response_model=Token)
async def login(user_credentials: UserLogin):
    """Authenticate user and return token"""
    user = await users_collection.find_one({"email": user_credentials.email})

    if not user:
        raise HTTPException(status_code=404, detail="User not found")

    if not verify_password(user_credentials.password, user["password"]):
        raise HTTPException(status_code=401, detail="Invalid credentials")

    # Check if user has made payment (required before accessing dashboard)
    has_payment = await payments_collection.count_documents({"user_id": user["_id"], "status": "completed"}) > 0

    # Generate token
    access_token = create_access_token(
        data={"sub": str(user["_id"])},
        expires_delta=timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    )

    return Token(access_token=access_token, token_type="bearer")

@app.get("/profile", response_model=UserResponse)
async def get_profile(current_user: dict = Depends(get_current_user)):
    """Get user profile"""
    return UserResponse(
        id=str(current_user["_id"]),
        name=current_user["name"],
        email=current_user["email"],
        phone=current_user["phone"],
        balance=current_user.get("balance", 0),
        total_earnings=current_user.get("total_earnings", 0),
        currency=current_user.get("currency", "USD"),
        language=current_user.get("language", "en"),
        joined_date=current_user["created_at"].strftime('%Y-%m-%d')
    )

@app.put("/profile/language")
async def update_language(language: str, current_user: dict = Depends(get_current_user)):
    """Update user language preference"""
    try:
        # Validate language code
        supported_languages = ["en", "sw"]
        if language not in supported_languages:
            raise HTTPException(status_code=400, detail="Unsupported language code")

        # Update user's language preference
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {"$set": {"language": language}}
        )

        return {"status": "success", "message": "Language updated successfully", "language": language}
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to update language: {str(e)}")

# Dashboard endpoint
@app.get("/dashboard")
async def get_dashboard_data(current_user: dict = Depends(get_current_user)):
    """Get dashboard data for authenticated user"""
    # Check if user has made payment (required before accessing dashboard)
    has_payment = await payments_collection.count_documents({"user_id": current_user["_id"], "status": "completed"}) > 0

    if not has_payment:
        raise HTTPException(
            status_code=403,
            detail="Payment required to access dashboard. Please make a deposit first."
        )

    # Get user's earnings data
    earnings_data = await earnings_collection.find_one({"user_id": current_user["_id"]})

    if not earnings_data:
        # Initialize earnings data if not exists
        earnings_data = {
            'user_id': current_user["_id"],
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
            'last_updated': datetime.utcnow()
        }
        await earnings_collection.insert_one(earnings_data)

    # Calculate invested amount (first deposit/registration payment)
    first_deposit = await payments_collection.find_one(
        {"user_id": current_user["_id"], "status": "completed"},
        sort=[("created_at", 1)]
    )
    invested_amount = float(first_deposit["amount"]) if first_deposit else 0

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

    return {"status": "success", "data": dashboard_data, "has_payment": True}

# Payment endpoints
class PaymentRequest(BaseModel):
    amount: float
    currency: str = "USD"
    description: str = "MULAPAL Platform Deposit"

class WithdrawalRequest(BaseModel):
    amount: float
    method: str  # mpesa, airtel, paypal, bank
    account_details: Dict[str, Any]

@app.post("/payments/initiate")
async def initiate_payment(payment_data: PaymentRequest, current_user: dict = Depends(get_current_user)):
    """Initiate a payment using PesaPal"""
    try:
        # Convert amount to KSH for PesaPal
        amount_ksh = convert_currency_to_ksh(payment_data.amount, payment_data.currency)

        # Get PesaPal token
        token = await get_pesapal_token()
        if not token:
            raise HTTPException(status_code=500, detail="Payment service unavailable")

        # Create payment record
        payment_doc = {
            "user_id": current_user["_id"],
            "amount": payment_data.amount,
            "amount_ksh": amount_ksh,
            "currency": payment_data.currency,
            "description": payment_data.description,
            "status": "pending",
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }

        payment_result = await payments_collection.insert_one(payment_doc)
        payment_id = str(payment_result.inserted_id)

        # Prepare PesaPal order data
        order_data = {
            "id": payment_id,
            "currency": "KES",  # PesaPal uses KES
            "amount": amount_ksh,
            "description": payment_data.description,
            "callback_url": settings.PESAPAL_CALLBACK_URL,
            "notification_id": payment_id,
            "billing_address": {
                "email_address": current_user["email"],
                "phone_number": current_user["phone"],
                "country_code": "KE",
                "first_name": current_user["name"].split()[0],
                "last_name": current_user["name"].split()[-1] if len(current_user["name"].split()) > 1 else ""
            }
        }

        # Submit order to PesaPal
        headers = {
            'Content-Type': 'application/json',
            'Authorization': f'Bearer {token}'
        }

        response = requests.post(PESAPAL_ORDER_URL, json=order_data, headers=headers)

        if response.status_code == 200:
            response_data = response.json()
            if response_data.get('status') == '200':
                # Update payment with PesaPal order tracking ID
                await payments_collection.update_one(
                    {"_id": payment_result.inserted_id},
                    {"$set": {
                        "pesapal_order_tracking_id": response_data.get('order_tracking_id'),
                        "pesapal_redirect_url": response_data.get('redirect_url')
                    }}
                )

                return {
                    "status": "success",
                    "payment_id": payment_id,
                    "redirect_url": response_data.get('redirect_url'),
                    "order_tracking_id": response_data.get('order_tracking_id'),
                    "amount": payment_data.amount,
                    "currency": payment_data.currency
                }
            else:
                raise HTTPException(status_code=400, detail="Payment initiation failed")
        else:
            raise HTTPException(status_code=500, detail="Payment service error")

    except Exception as e:
        print(f"Payment initiation error: {e}")
        raise HTTPException(status_code=500, detail="Payment initiation failed")

@app.post("/payments/callback")
async def payment_callback(request: Request):
    """Handle PesaPal payment callback"""
    try:
        callback_data = await request.json()

        order_tracking_id = callback_data.get('OrderTrackingId')
        order_notification_type = callback_data.get('OrderNotificationType')
        order_merchant_reference = callback_data.get('OrderMerchantReference')

        if not order_tracking_id:
            raise HTTPException(status_code=400, detail="Invalid callback data")

        # Get payment details
        payment = await payments_collection.find_one({"pesapal_order_tracking_id": order_tracking_id})
        if not payment:
            raise HTTPException(status_code=404, detail="Payment not found")

        # Get transaction status from PesaPal
        token = await get_pesapal_token()
        if not token:
            raise HTTPException(status_code=500, detail="Unable to verify payment")

        headers = {
            'Content-Type': 'application/json',
            'Authorization': f'Bearer {token}'
        }

        status_data = {
            "orderTrackingId": order_tracking_id
        }

        status_response = requests.post(PESAPAL_IPN_URL, json=status_data, headers=headers)

        if status_response.status_code == 200:
            status_result = status_response.json()

            if status_result.get('status') == '200':
                payment_status = status_result.get('payment_status_description', '').lower()

                # Update payment status
                update_data = {
                    "status": payment_status,
                    "updated_at": datetime.utcnow(),
                    "pesapal_response": status_result
                }

                if payment_status == 'completed':
                    # Update user balance and earnings
                    user_id = payment["user_id"]
                    amount = payment["amount"]

                    # Update user balance
                    await users_collection.update_one(
                        {"_id": user_id},
                        {"$inc": {"balance": amount, "total_earnings": amount}}
                    )

                    # Update earnings record
                    earnings_data = await earnings_collection.find_one({"user_id": user_id})
                    if earnings_data:
                        await earnings_collection.update_one(
                            {"user_id": user_id},
                            {"$inc": {"total_earnings": amount, "balance": amount}}
                        )
                    else:
                        # Create earnings record
                        earnings_doc = {
                            "user_id": user_id,
                            "today_earnings": amount,
                            "total_earnings": amount,
                            "balance": amount,
                            "withdrawn": 0,
                            "affiliate_earnings": 0,
                            "agent_bonus": 0,
                            "ads_earnings": 0,
                            "tiktok_earnings": 0,
                            "youtube_earnings": 0,
                            "trivia_earnings": 0,
                            "blog_earnings": 0,
                            "last_updated": datetime.utcnow()
                        }
                        await earnings_collection.insert_one(earnings_doc)

                    # Send success email
                    user = await users_collection.find_one({"_id": user_id})
                    if user:
                        email_body = f"""
                        <h2>Payment Successful!</h2>
                        <p>Dear {user['name']},</p>
                        <p>Your payment of {format_amount(amount, payment['currency'])} has been successfully processed.</p>
                        <p>Your account balance has been updated.</p>
                        <br>
                        <p>Best regards,<br>MULAPAL Team</p>
                        """
                        send_email(user['email'], 'Payment Successful', email_body)

                await payments_collection.update_one(
                    {"pesapal_order_tracking_id": order_tracking_id},
                    {"$set": update_data}
                )

                return {"status": "success", "message": "Payment callback processed"}

        raise HTTPException(status_code=500, detail="Payment verification failed")

    except Exception as e:
        print(f"Payment callback error: {e}")
        raise HTTPException(status_code=500, detail="Callback processing failed")

@app.get("/payments/history")
async def get_payment_history(current_user: dict = Depends(get_current_user)):
    """Get user's payment history"""
    payments = await payments_collection.find(
        {"user_id": current_user["_id"]},
        sort=[("created_at", -1)]
    ).to_list(length=None)

    # Convert ObjectId to string for JSON serialization
    for payment in payments:
        payment["_id"] = str(payment["_id"])
        payment["user_id"] = str(payment["user_id"])

    return {"status": "success", "payments": payments}

# Withdrawal endpoints
@app.post("/withdrawals")
async def request_withdrawal(withdrawal_data: WithdrawalRequest, current_user: dict = Depends(get_current_user)):
    """Request a withdrawal"""
    try:
        # Check user balance
        user_balance = current_user.get("balance", 0)
        if user_balance < withdrawal_data.amount:
            raise HTTPException(status_code=400, detail="Insufficient balance")

        # Check minimum withdrawal amount
        min_withdrawal = 10  # $10 minimum
        if withdrawal_data.amount < min_withdrawal:
            raise HTTPException(status_code=400, detail=f"Minimum withdrawal amount is ${min_withdrawal}")

        # Create withdrawal record
        withdrawal_doc = {
            "user_id": current_user["_id"],
            "amount": withdrawal_data.amount,
            "method": withdrawal_data.method,
            "account_details": withdrawal_data.account_details,
            "status": "pending",
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }

        result = await withdrawals_collection.insert_one(withdrawal_doc)

        # Deduct from user balance
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {"$inc": {"balance": -withdrawal_data.amount}}
        )

        # Update earnings record
        await earnings_collection.update_one(
            {"user_id": current_user["_id"]},
            {"$inc": {"balance": -withdrawal_data.amount, "withdrawn": withdrawal_data.amount}}
        )

        return {
            "status": "success",
            "message": "Withdrawal request submitted successfully",
            "withdrawal_id": str(result.inserted_id)
        }

    except Exception as e:
        print(f"Withdrawal error: {e}")
        raise HTTPException(status_code=500, detail="Withdrawal request failed")

@app.get("/withdrawals/history")
async def get_withdrawal_history(current_user: dict = Depends(get_current_user)):
    """Get user's withdrawal history"""
    withdrawals = await withdrawals_collection.find(
        {"user_id": current_user["_id"]},
        sort=[("created_at", -1)]
    ).to_list(length=None)

    # Convert ObjectId to string for JSON serialization
    for withdrawal in withdrawals:
        withdrawal["_id"] = str(withdrawal["_id"])
        withdrawal["user_id"] = str(withdrawal["user_id"])

    return {"status": "success", "withdrawals": withdrawals}

# Task management endpoints
@app.get("/tasks")
async def list_tasks(current_user: dict = Depends(get_current_user)):
    """List available tasks"""
    # Check if user has made payment
    has_payment = await payments_collection.count_documents({"user_id": current_user["_id"], "status": "completed"}) > 0
    if not has_payment:
        raise HTTPException(status_code=403, detail="Payment required to access tasks")

    # Get available tasks
    tasks = await tasks_collection.find({"is_active": True}).to_list(length=None)

    # Convert ObjectId to string
    for task in tasks:
        task["_id"] = str(task["_id"])

    return {"status": "success", "tasks": tasks}

@app.post("/tasks/{task_id}/complete")
async def complete_task(task_id: str, current_user: dict = Depends(get_current_user)):
    """Mark a task as completed"""
    try:
        # Check if task exists
        task = await tasks_collection.find_one({"_id": task_id, "is_active": True})
        if not task:
            raise HTTPException(status_code=404, detail="Task not found")

        # Check if user already completed this task
        existing_completion = await transactions_collection.find_one({
            "user_id": current_user["_id"],
            "task_id": task_id,
            "type": "task_completion"
        })

        if existing_completion:
            raise HTTPException(status_code=400, detail="Task already completed")

        # Award earnings
        earnings_amount = task.get("reward_amount", 0)

        # Create transaction record
        transaction_doc = {
            "user_id": current_user["_id"],
            "task_id": task_id,
            "type": "task_completion",
            "amount": earnings_amount,
            "description": f"Completed task: {task.get('title', 'Unknown task')}",
            "created_at": datetime.utcnow()
        }

        await transactions_collection.insert_one(transaction_doc)

        # Update user earnings
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {"$inc": {"balance": earnings_amount, "total_earnings": earnings_amount}}
        )

        # Update earnings record
        await earnings_collection.update_one(
            {"user_id": current_user["_id"]},
            {"$inc": {"balance": earnings_amount, "total_earnings": earnings_amount}}
        )

        return {
            "status": "success",
            "message": "Task completed successfully",
            "earnings": earnings_amount
        }

    except Exception as e:
        print(f"Task completion error: {e}")
        raise HTTPException(status_code=500, detail="Task completion failed")

# Voucher system endpoints
@app.get("/vouchers")
async def list_vouchers(current_user: dict = Depends(get_current_user)):
    """List available vouchers"""
    vouchers = await vouchers_collection.find({"is_active": True}).to_list(length=None)

    # Convert ObjectId to string
    for voucher in vouchers:
        voucher["_id"] = str(voucher["_id"])

    return {"status": "success", "vouchers": vouchers}

@app.post("/vouchers/{voucher_id}/purchase")
async def purchase_voucher(voucher_id: str, current_user: dict = Depends(get_current_user)):
    """Purchase a voucher"""
    try:
        # Check if voucher exists
        voucher = await vouchers_collection.find_one({"_id": voucher_id, "is_active": True})
        if not voucher:
            raise HTTPException(status_code=404, detail="Voucher not found")

        # Check user balance
        user_balance = current_user.get("balance", 0)
        voucher_price = voucher.get("price", 0)

        if user_balance < voucher_price:
            raise HTTPException(status_code=400, detail="Insufficient balance")

        # Deduct from user balance
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {"$inc": {"balance": -voucher_price}}
        )

        # Update earnings record
        await earnings_collection.update_one(
            {"user_id": current_user["_id"]},
            {"$inc": {"balance": -voucher_price}}
        )

        # Generate voucher code
        voucher_code = f"MUL-{random.randint(100000, 999999)}"

        # Create voucher purchase record
        purchase_doc = {
            "user_id": current_user["_id"],
            "voucher_id": voucher_id,
            "voucher_code": voucher_code,
            "amount": voucher_price,
            "status": "active",
            "created_at": datetime.utcnow(),
            "expires_at": datetime.utcnow() + timedelta(days=30)  # 30 days validity
        }

        await transactions_collection.insert_one(purchase_doc)

        return {
            "status": "success",
            "message": "Voucher purchased successfully",
            "voucher_code": voucher_code
        }

    except Exception as e:
        print(f"Voucher purchase error: {e}")
        raise HTTPException(status_code=500, detail="Voucher purchase failed")

# Notification endpoints
@app.get("/notifications")
async def get_user_notifications(current_user: dict = Depends(get_current_user)):
    """Get user's notifications"""
    try:
        # Get user's notifications
        notifications = await notifications_collection.find(
            {"user_id": current_user["_id"]},
            sort=[("created_at", -1)]
        ).to_list(length=None)

        # Convert ObjectId to string for JSON serialization
        for notification in notifications:
            notification["_id"] = str(notification["_id"])
            notification["user_id"] = str(notification["user_id"])

        # Get unread count
        unread_count = await notifications_collection.count_documents({
            "user_id": current_user["_id"],
            "is_read": {"$ne": True}
        })

        return {
            "status": "success",
            "notifications": notifications,
            "unread_count": unread_count
        }

    except Exception as e:
        print(f"Error fetching notifications: {e}")
        raise HTTPException(status_code=500, detail="Failed to fetch notifications")

@app.post("/notifications/{notification_id}/read")
async def mark_notification_read(notification_id: str, current_user: dict = Depends(get_current_user)):
    """Mark a notification as read"""
    try:
        # Convert string ID to ObjectId
        try:
            notification_object_id = ObjectId(notification_id)
        except Exception:
            raise HTTPException(status_code=400, detail="Invalid notification ID")

        # Update notification
        result = await notifications_collection.update_one(
            {"_id": notification_object_id, "user_id": current_user["_id"]},
            {"$set": {"is_read": True, "read_at": datetime.utcnow()}}
        )

        if result.modified_count == 0:
            raise HTTPException(status_code=404, detail="Notification not found")

        return {"status": "success", "message": "Notification marked as read"}

    except Exception as e:
        print(f"Error marking notification as read: {e}")
        raise HTTPException(status_code=500, detail="Failed to mark notification as read")

@app.post("/notifications/mark-all-read")
async def mark_all_notifications_read(current_user: dict = Depends(get_current_user)):
    """Mark all user notifications as read"""
    try:
        # Update all unread notifications
        result = await notifications_collection.update_many(
            {"user_id": current_user["_id"], "is_read": {"$ne": True}},
            {"$set": {"is_read": True, "read_at": datetime.utcnow()}}
        )

        return {
            "status": "success",
            "message": f"Marked {result.modified_count} notifications as read"
        }

    except Exception as e:
        print(f"Error marking all notifications as read: {e}")
        raise HTTPException(status_code=500, detail="Failed to mark notifications as read")

@app.post("/admin/notifications")
async def create_notification(
    title: str,
    message: str,
    notification_type: str = "info",
    user_id: Optional[str] = None,
    current_user: dict = Depends(get_current_admin_user)
):
    """Create a notification (admin only)"""
    try:
        notification_doc = {
            "title": title,
            "message": message,
            "type": notification_type,
            "is_read": False,
            "created_at": datetime.utcnow()
        }

        if user_id:
            # Send to specific user
            try:
                notification_doc["user_id"] = ObjectId(user_id)
            except Exception:
                raise HTTPException(status_code=400, detail="Invalid user ID")

            result = await notifications_collection.insert_one(notification_doc)
        else:
            # Send to all users - get all user IDs
            users = await users_collection.find({}, {"_id": 1}).to_list(length=None)

            # Create notification for each user
            notifications_to_insert = []
            for user in users:
                user_notification = notification_doc.copy()
                user_notification["user_id"] = user["_id"]
                notifications_to_insert.append(user_notification)

            if notifications_to_insert:
                result = await notifications_collection.insert_many(notifications_to_insert)
                return {
                    "status": "success",
                    "message": f"Notification sent to {len(notifications_to_insert)} users"
                }

        return {
            "status": "success",
            "message": "Notification created successfully",
            "notification_id": str(result.inserted_id) if hasattr(result, 'inserted_id') else None
        }

    except Exception as e:
        print(f"Error creating notification: {e}")
        raise HTTPException(status_code=500, detail="Failed to create notification")

# Admin endpoints
@app.get("/admin/dashboard")
async def admin_dashboard(current_user: dict = Depends(get_current_admin_user)):
    """Admin dashboard with platform statistics"""
    # Get statistics
    total_users = await users_collection.count_documents({})
    active_users = await users_collection.count_documents({"is_active": True})
    total_deposits = await payments_collection.count_documents({"status": "completed"})
    total_withdrawals = await withdrawals_collection.count_documents({"status": "approved"})
    pending_withdrawals = await withdrawals_collection.count_documents({"status": "pending"})

    # Calculate total deposit amount
    deposit_pipeline = [
        {"$match": {"status": "completed"}},
        {"$group": {"_id": None, "total": {"$sum": "$amount"}}}
    ]
    deposit_result = await payments_collection.aggregate(deposit_pipeline).to_list(length=1)
    total_deposit_amount = deposit_result[0]["total"] if deposit_result else 0

    # Calculate total withdrawal amount
    withdrawal_pipeline = [
        {"$match": {"status": "approved"}},
        {"$group": {"_id": None, "total": {"$sum": "$amount"}}}
    ]
    withdrawal_result = await withdrawals_collection.aggregate(withdrawal_pipeline).to_list(length=1)
    total_withdrawal_amount = withdrawal_result[0]["total"] if withdrawal_result else 0

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

    return {"status": "success", "data": dashboard_data}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)
