#!/usr/bin/env python3
"""
Run script for FastAPI server
"""

import uvicorn
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

if __name__ == "__main__":
    # Get port from environment or default to 8000
    port = int(os.environ.get("PORT", 8000))
    host = os.environ.get("HOST", "0.0.0.0")

    print(f"Starting FastAPI server on {host}:{port}")
    print(f"API Documentation: http://{host}:{port}/docs")
    print(f"Alternative Documentation: http://{host}:{port}/redoc")

    uvicorn.run(
        "fastapi_server:app",
        host=host,
        port=port,
        reload=True,  # Enable auto-reload for development
        log_level="info"
    )
