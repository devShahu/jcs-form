#!/bin/bash

# JCS Form Filler - Setup Script for Linux/Mac
# This script helps set up the development environment

echo "=== JCS Membership Form - Setup Script ==="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Check PHP
echo -e "${YELLOW}Checking PHP...${NC}"
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2)
    echo -e "${GREEN}✓ PHP $PHP_VERSION found${NC}"
else
    echo -e "${RED}✗ PHP not found. Please install PHP 8.0 or higher.${NC}"
    exit 1
fi

# Check Composer
echo -e "${YELLOW}Checking Composer...${NC}"
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | cut -d " " -f 3)
    echo -e "${GREEN}✓ Composer $COMPOSER_VERSION found${NC}"
else
    echo -e "${RED}✗ Composer not found. Please install Composer.${NC}"
    exit 1
fi

# Check Node.js
echo -e "${YELLOW}Checking Node.js...${NC}"
if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v)
    echo -e "${GREEN}✓ Node.js $NODE_VERSION found${NC}"
else
    echo -e "${RED}✗ Node.js not found. Please install Node.js 18 or higher.${NC}"
    exit 1
fi

echo ""
echo -e "${CYAN}Installing dependencies...${NC}"

# Install PHP dependencies
echo -e "${YELLOW}Installing PHP dependencies with Composer...${NC}"
composer install
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ PHP dependencies installed${NC}"
else
    echo -e "${RED}✗ Failed to install PHP dependencies${NC}"
    exit 1
fi

# Install Node dependencies
echo -e "${YELLOW}Installing Node.js dependencies with npm...${NC}"
npm install
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Node.js dependencies installed${NC}"
else
    echo -e "${RED}✗ Failed to install Node.js dependencies${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Checking Bengali fonts...${NC}"

# Check and download fonts
if [ ! -f "fonts/Nikosh.ttf" ]; then
    echo -e "${YELLOW}Downloading Nikosh.ttf...${NC}"
    curl -L -o fonts/Nikosh.ttf https://github.com/potasiyam/Bangla-Fonts/raw/master/Nikosh.ttf
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Nikosh.ttf downloaded${NC}"
    else
        echo -e "${RED}✗ Failed to download Nikosh.ttf. Please download manually.${NC}"
    fi
else
    echo -e "${GREEN}✓ Nikosh.ttf found${NC}"
fi

if [ ! -f "fonts/NotoSansBengali-Regular.ttf" ]; then
    echo -e "${YELLOW}Downloading NotoSansBengali-Regular.ttf...${NC}"
    curl -L -o fonts/NotoSansBengali-Regular.ttf https://github.com/notofonts/bengali/raw/main/fonts/NotoSansBengali/hinted/ttf/NotoSansBengali-Regular.ttf
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ NotoSansBengali-Regular.ttf downloaded${NC}"
    else
        echo -e "${RED}✗ Failed to download NotoSansBengali-Regular.ttf. Please download manually.${NC}"
    fi
else
    echo -e "${GREEN}✓ NotoSansBengali-Regular.ttf found${NC}"
fi

# Set permissions
echo ""
echo -e "${YELLOW}Setting directory permissions...${NC}"
chmod -R 775 storage
echo -e "${GREEN}✓ Storage directory permissions set${NC}"

echo ""
echo -e "${CYAN}=== Setup Complete ===${NC}"
echo ""
echo -e "${YELLOW}To start development:${NC}"
echo -e "  1. Start the backend:  ${NC}php -S localhost:8000 -t api"
echo -e "  2. Start the frontend: ${NC}npm run dev"
echo ""
echo -e "${GREEN}The frontend will be available at: http://localhost:3000${NC}"
echo -e "${GREEN}The API will be available at: http://localhost:8000/api${NC}"
echo ""
