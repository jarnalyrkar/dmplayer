@echo off
setlocal

REM Define variables
set "phpDownloadUrl=https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x86-latest.zip"
set "phpZipFile=php.zip"
set "phpExtractDir=php"

REM Download PHP
echo Downloading PHP...
powershell -Command "(New-Object System.Net.WebClient).DownloadFile('%phpDownloadUrl%', '%phpZipFile%')"

REM Extract PHP
echo Extracting PHP...
powershell -Command "Expand-Archive -Path '%phpZipFile%' -DestinationPath '%phpExtractDir%' -Force"

REM Add PHP directory to PATH
echo Adding PHP directory to PATH...
setx PATH "%cd%\%phpExtractDir%\;%PATH%" /M

REM Cleanup
echo Cleanup...
del "%phpZipFile%"

echo PHP installation completed.

endlocal