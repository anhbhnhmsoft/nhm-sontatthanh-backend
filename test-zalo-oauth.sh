#!/bin/bash

# Zalo OAuth Flow Testing Script
# This script helps test the Zalo authentication flow

echo "=================================="
echo "Zalo OAuth Flow Testing"
echo "=================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if APP_URL is set
if [ -z "$APP_URL" ]; then
    echo -e "${YELLOW}Warning: APP_URL not set in .env${NC}"
    echo "Please set APP_URL in your .env file"
    echo "Example: APP_URL=https://your-domain.com"
    echo ""
    read -p "Enter your APP_URL: " APP_URL
fi

echo -e "${GREEN}Step 1: Testing Authorization URL${NC}"
echo "URL: $APP_URL/auth/zalo/redirect"
echo ""

echo -e "${GREEN}Step 2: Open this URL in your browser:${NC}"
echo -e "${YELLOW}$APP_URL/auth/zalo/redirect${NC}"
echo ""

echo "This will:"
echo "  1. Redirect you to Zalo OAuth page"
echo "  2. After login, redirect to callback"
echo "  3. Show a success page with deeplink"
echo ""

echo -e "${GREEN}Step 3: Expected Deeplink Format:${NC}"
echo "nhmapp://auth/zalo?token=xxx&success=true&message=..."
echo ""

echo -e "${GREEN}Step 4: Test Deeplink on Mobile:${NC}"
echo ""
echo "iOS (Safari):"
echo "  Open Safari and type: nhmapp://auth/zalo?token=test&success=true"
echo ""
echo "Android (ADB):"
echo "  adb shell am start -W -a android.intent.action.VIEW -d \"nhmapp://auth/zalo?token=test&success=true\""
echo ""

echo -e "${GREEN}Step 5: Test API Endpoint (Optional):${NC}"
echo ""
echo "Test with code:"
echo "  curl -X POST $APP_URL/api/auth/zalo-authenticate \\"
echo "    -H 'Content-Type: application/json' \\"
echo "    -d '{\"code\":\"your_zalo_code\"}'"
echo ""

echo "Test with access_token (deprecated):"
echo "  curl -X POST $APP_URL/api/auth/zalo-authenticate \\"
echo "    -H 'Content-Type: application/json' \\"
echo "    -d '{\"access_token\":\"your_zalo_token\"}'"
echo ""

echo -e "${GREEN}Troubleshooting:${NC}"
echo ""
echo "1. Check Laravel logs:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "2. Check routes:"
echo "   php artisan route:list | grep zalo"
echo ""
echo "3. Clear cache:"
echo "   php artisan cache:clear"
echo "   php artisan config:clear"
echo ""

echo "=================================="
echo "Testing Complete!"
echo "=================================="
