# Matrix Platform - Frontend Deployment

This is the frontend for the Matrix Platform, a comprehensive web application built with PHP, HTML, CSS, and JavaScript.

## 🚀 Deployment on Render.com

### Prerequisites
- A Render.com account
- Your project repository pushed to GitHub

### Deployment Steps

1. **Connect Repository to Render**
   - Go to [Render.com](https://render.com) and sign in
   - Click "New" → "Web Service"
   - Connect your GitHub repository

2. **Configure Service**
   - **Name**: `matrix-platform-frontend` (or your preferred name)
   - **Runtime**: `PHP`
   - **Build Command**: `echo 'No build step required'`
   - **Start Command**: `php -S 0.0.0.0:$PORT -t frontend`

3. **Environment Variables**
   - Add the following environment variable:
     - `API_BASE_URL`: `https://tbt-mega.onrender.com` (your backend API URL)

4. **Deploy**
   - Click "Create Web Service"
   - Render will automatically deploy your frontend

### Alternative: Using render.yaml

If you prefer using the `render.yaml` file for configuration:

1. Push the `render.yaml` file to your repository
2. In Render, select "Blueprint" when creating a new service
3. The configuration will be automatically applied

## 📁 Project Structure

```
frontend/
├── index.php          # Entry point
├── .htaccess          # Apache configuration
├── config.js          # API configuration
├── auth/              # Authentication pages
│   ├── login.php
│   ├── register.php
│   ├── admin-dashboard.php
│   └── ...
├── user/              # User dashboard pages
│   ├── dashboard.php
│   ├── deposit.php
│   └── ...
└── pay/               # Payment related pages
    ├── payment.php
    └── payout.php
```

## 🔧 Configuration

The frontend is configured to work with the backend API. Make sure to update the `API_BASE_URL` in:
- `frontend/config.js`
- Render environment variables

## 🌐 Accessing Your Deployed App

Once deployed, your frontend will be available at:
`https://your-service-name.onrender.com`

The app will automatically redirect to the login page.

## 🐛 Troubleshooting

- **404 Errors**: Check that all file paths are correct
- **API Connection Issues**: Verify the `API_BASE_URL` is set correctly
- **PHP Errors**: Check the Render logs for detailed error messages

## 📞 Support

If you encounter any issues during deployment, check the Render documentation or contact their support team.
