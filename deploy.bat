@echo off
REM Script untuk build assets sebelum deploy (Windows)
REM Usage: deploy.bat

echo 🚀 Starting deployment preparation...
echo.

REM Check if node_modules exists
if not exist "node_modules" (
    echo 📦 Installing dependencies...
    call npm install
)

REM Build for production
echo 🔨 Building assets for production...
call npm run build

if %errorlevel% equ 0 (
    echo.
    echo ✅ Build successful!
    echo.
    echo 📁 Build files created in public/build/
    echo.
    echo 📝 Next steps:
    echo    1. Commit and push to GitHub:
    echo       git add public/build
    echo       git commit -m "Update production build"
    echo       git push origin main
    echo.
    echo    2. Pull changes on your hosting server
    echo    3. Run Laravel optimizations:
    echo       php artisan config:cache
    echo       php artisan route:cache
    echo       php artisan view:cache
    echo.
) else (
    echo ❌ Build failed! Please check the errors above.
    exit /b 1
)

