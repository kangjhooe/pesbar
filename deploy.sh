#!/bin/bash

# Script untuk build assets sebelum deploy
# Usage: ./deploy.sh

echo "🚀 Starting deployment preparation..."
echo ""

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Build for production
echo "🔨 Building assets for production..."
npm run build

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Build successful!"
    echo ""
    echo "📁 Build files created in public/build/"
    echo ""
    echo "📝 Next steps:"
    echo "   1. Commit and push to GitHub:"
    echo "      git add public/build"
    echo "      git commit -m 'Update production build'"
    echo "      git push origin main"
    echo ""
    echo "   2. Pull changes on your hosting server"
    echo "   3. Run Laravel optimizations:"
    echo "      php artisan config:cache"
    echo "      php artisan route:cache"
    echo "      php artisan view:cache"
    echo ""
else
    echo "❌ Build failed! Please check the errors above."
    exit 1
fi

