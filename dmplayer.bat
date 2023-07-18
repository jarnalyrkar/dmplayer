@echo off
:: echo Starting local Dungeon Master Player server...
:: start servers
start /B "" php -S localhost:8008 -c php.ini -q >NUL 2>&1
start /B "" php -f ./websockets.php -c php.ini -q >NUL 2>&1


:: Open default browser
start http://localhost:8008

:: ping for kill-file
:loop
timeout /T 1 >nul
if exist "stopserver" (
  del stopserver
  taskkill /F /IM php.exe >nul 2>&1
  exit /B
)
goto loop

exit /B