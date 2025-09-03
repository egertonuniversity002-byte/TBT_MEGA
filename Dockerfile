# Use Python 3.9 slim image
FROM python:3.9-slim

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    gcc \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements and install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY backend/ ./backend/
COPY .env ./

# Expose port
EXPOSE 5000

# Set environment variables
ENV FLASK_APP=backend/server.py
ENV FLASK_ENV=production

# Run the application
CMD ["python", "backend/server.py"]
