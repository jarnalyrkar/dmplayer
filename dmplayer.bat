@echo off
:: start servers
start /B php -S localhost:8008 -q
start /B php websockets.php -q

:: Open default browser
start http://localhost:8008

:: ping for kill-file
:loop
timeout /T 1 >nul
if exist "stopserver" (
  del stopserver
  taskkill /F /IM "php websockets.php -q"
  taskkill /F /IM "php -S localhost:8008 -q"
  exit /B
)
goto loop

exit /B