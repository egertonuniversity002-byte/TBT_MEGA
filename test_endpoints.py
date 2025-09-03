import requests
import json
import time

BASE_URL = "http://127.0.0.1:5000"

def test_endpoint(name, method, url, data=None, headers=None):
    """Test an endpoint and print results"""
    print(f"\n=== Testing {name} ===")
    try:
        if method.upper() == 'GET':
            response = requests.get(url, headers=headers)
        elif method.upper() == 'POST':
            response = requests.post(url, json=data, headers=headers)
        else:
            print(f"Unsupported method: {method}")
            return None

        print(f"Status Code: {response.status_code}")
        print(f"Response: {response.text[:500]}...")  # Truncate long responses

        if response.status_code in [200, 201]:
            print("✅ SUCCESS")
        else:
            print("❌ FAILED")

        return response.json() if response.headers.get('content-type') == 'application/json' else response.text

    except Exception as e:
        print(f"❌ ERROR: {str(e)}")
        return None

def main():
    print("🚀 Starting Backend API Tests")
    print("=" * 50)

    # Test basic endpoints
    test_endpoint("Home", "GET", f"{BASE_URL}/")
    test_endpoint("Live Status", "GET", f"{BASE_URL}/live-status")

    # Test register
    register_data = {
        "name": "Test User",
        "email": "test@example.com",
        "phone": "+1234567890",
        "password": "testpassword123",
        "currency": "USD",
        "country": "US"
    }
    register_response = test_endpoint("Register", "POST", f"{BASE_URL}/register", register_data)

    # Test login
    login_data = {
        "email": "test@example.com",
        "password": "testpassword123"
    }
    login_response = test_endpoint("Login", "POST", f"{BASE_URL}/login", login_data)

    # If login successful, test authenticated endpoints
    if login_response and login_response.get('status') == 'success':
        token = login_response.get('token')
        headers = {'Authorization': f'Bearer {token}'}

        # Test dashboard
        test_endpoint("Dashboard", "GET", f"{BASE_URL}/dashboard", headers=headers)

        # Test profile
        test_endpoint("Profile", "GET", f"{BASE_URL}/profile", headers=headers)

        # Test tasks
        test_endpoint("Tasks", "GET", f"{BASE_URL}/tasks", headers=headers)

        # Test withdrawals
        test_endpoint("Withdrawals", "GET", f"{BASE_URL}/withdrawals", headers=headers)

        # Test team list
        test_endpoint("Team List", "GET", f"{BASE_URL}/team/list", headers=headers)

        # Test deposit initiation (will fail without payment setup, but test endpoint)
        deposit_data = {"amount": 100}
        test_endpoint("Deposit Initiation", "POST", f"{BASE_URL}/payments/deposit/initiate", deposit_data, headers)

        # Test withdrawal request
        withdrawal_data = {"amount": 50, "wallet": "main"}
        test_endpoint("Withdrawal Request", "POST", f"{BASE_URL}/withdrawals", withdrawal_data, headers)

    # Test forgot password
    forgot_data = {"email": "test@example.com"}
    test_endpoint("Forgot Password", "POST", f"{BASE_URL}/forgot-password", forgot_data)

    # Test admin endpoints (will likely fail without admin token)
    admin_headers = {'Authorization': 'Bearer fake-admin-token'}
    test_endpoint("Admin Dashboard", "GET", f"{BASE_URL}/admin/dashboard", headers=admin_headers)

    print("\n" + "=" * 50)
    print("🎯 Backend API Tests Completed")

if __name__ == "__main__":
    main()
