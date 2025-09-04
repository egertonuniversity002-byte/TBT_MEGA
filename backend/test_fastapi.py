"""
Test suite for FastAPI server
"""

import pytest
from httpx import Client
import json
import uuid

class TestFastAPIServer:
    def setup_method(self):
        self.client = Client(base_url="http://localhost:8001")
        self.access_token = None
        self.test_email = f"test{uuid.uuid4().hex[:8]}@example.com"

    def teardown_method(self):
        self.client.close()

    def test_root_endpoint(self):
        """Test root endpoint returns correct response"""
        response = self.client.get("/")
        assert response.status_code == 200
        data = response.json()
        assert "message" in data
        assert "status" in data
        assert data["status"] == "running"

    def test_user_registration(self):
        """Test user registration endpoint"""
        user_data = {
            "name": "Test User",
            "email": self.test_email,
            "phone": "+1234567890",
            "password": "TestPass123!"
        }

        response = self.client.post("/register", json=user_data)
        assert response.status_code in [200, 201]  # Success or created

        if response.status_code == 200:
            data = response.json()
            assert "access_token" in data
            assert "token_type" in data
            assert data["token_type"] == "bearer"

    def test_user_registration_duplicate_email(self):
        """Test duplicate email registration fails"""
        user_data = {
            "name": "Test User 2",
            "email": self.test_email,  # Same email as previous test
            "phone": "+1234567891",
            "password": "TestPass123!"
        }

        response = self.client.post("/register", json=user_data)
        assert response.status_code == 400
        data = response.json()
        assert "detail" in data
        assert "already exists" in data["detail"].lower()

    def test_user_login_invalid_credentials(self):
        """Test login with invalid credentials"""
        login_data = {
            "email": "nonexistent@example.com",
            "password": "wrongpassword"
        }

        response = self.client.post("/login", json=login_data)
        assert response.status_code == 404
        data = response.json()
        assert "detail" in data
        assert "not found" in data["detail"].lower()

    def test_user_login_valid_credentials(self):
        """Test login with valid credentials"""
        login_data = {
            "email": self.test_email,
            "password": "TestPass123!"
        }

        response = self.client.post("/login", json=login_data)
        assert response.status_code == 200
        data = response.json()
        assert "access_token" in data
        assert "token_type" in data
        assert data["token_type"] == "bearer"

        # Store token for authenticated tests
        self.access_token = data["access_token"]

    def test_profile_endpoint_unauthenticated(self):
        """Test profile endpoint without authentication"""
        response = self.client.get("/profile")
        assert response.status_code == 401
        data = response.json()
        assert "detail" in data
        assert "credentials" in data["detail"].lower()

    def test_dashboard_endpoint_unauthenticated(self):
        """Test dashboard endpoint without authentication"""
        response = self.client.get("/dashboard")
        assert response.status_code == 401
        data = response.json()
        assert "detail" in data
        assert "credentials" in data["detail"].lower()

    def test_tasks_endpoint_unauthenticated(self):
        """Test tasks endpoint without authentication"""
        response = self.client.get("/tasks")
        assert response.status_code == 401
        data = response.json()
        assert "detail" in data
        assert "credentials" in data["detail"].lower()

    def test_withdrawals_endpoint_unauthenticated(self):
        """Test withdrawals endpoint without authentication"""
        response = self.client.post("/withdrawals", json={})
        assert response.status_code == 401
        data = response.json()
        assert "detail" in data
        assert "credentials" in data["detail"].lower()

    def test_admin_dashboard_unauthenticated(self):
        """Test admin dashboard endpoint without authentication"""
        response = self.client.get("/admin/dashboard")
        assert response.status_code == 401
        data = response.json()
        assert "detail" in data
        assert "credentials" in data["detail"].lower()

    def test_cors_headers(self):
        """Test CORS headers are present"""
        response = self.client.options("/")
        assert "access-control-allow-origin" in response.headers
        assert "access-control-allow-methods" in response.headers
        assert "access-control-allow-headers" in response.headers

    def test_profile_authenticated(self):
        """Test profile endpoint with authentication"""
        if not self.access_token:
            pytest.skip("No access token available")
        response = self.client.get("/profile", headers={"Authorization": f"Bearer {self.access_token}"})
        assert response.status_code == 200
        data = response.json()
        assert "id" in data
        assert "name" in data
        assert data["name"] == "Test User"

    def test_dashboard_authenticated_no_payment(self):
        """Test dashboard endpoint with authentication but no payment"""
        if not self.access_token:
            pytest.skip("No access token available")
        response = self.client.get("/dashboard", headers={"Authorization": f"Bearer {self.access_token}"})
        # Should return 403 because no payment
        assert response.status_code == 403
        data = response.json()
        assert "detail" in data
        assert "payment required" in data["detail"].lower()

    def test_withdrawals_authenticated_insufficient_balance(self):
        """Test withdrawal with insufficient balance"""
        if not self.access_token:
            pytest.skip("No access token available")
        withdrawal_data = {
            "amount": 100,
            "method": "mpesa",
            "account_details": {"phone": "+1234567890"}
        }
        response = self.client.post("/withdrawals", json=withdrawal_data, headers={"Authorization": f"Bearer {self.access_token}"})
        assert response.status_code == 400
        data = response.json()
        assert "detail" in data
        assert "insufficient balance" in data["detail"].lower()

    def test_tasks_authenticated_no_payment(self):
        """Test tasks endpoint with authentication but no payment"""
        if not self.access_token:
            pytest.skip("No access token available")
        response = self.client.get("/tasks", headers={"Authorization": f"Bearer {self.access_token}"})
        # Should return 403 because no payment
        assert response.status_code == 403
        data = response.json()
        assert "detail" in data
        assert "payment required" in data["detail"].lower()

if __name__ == "__main__":
    pytest.main([__file__, "-v"])
