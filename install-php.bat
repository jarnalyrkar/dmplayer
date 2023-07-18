@echo off
setlocal

REM Define variables
set "phpDownloadUrl=https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x86-latest.zip"
set "phpZipFile=php.zip"
set "phpExtractDir=php"

REM Check if PHP folder exists and delete it
if exist "%phpExtractDir%" (
    echo Deleting existing PHP folder...
    rmdir /S /Q "%phpExtractDir%"
)

REM Get the current script directory
for %%I in ("%~dp0") do set "scriptDir=%%~fI"

REM Download PHP
echo Downloading PHP...
powershell -Command "Invoke-WebRequest -Uri '%phpDownloadUrl%' -OutFile '%scriptDir%\%phpZipFile%'"

REM Extract PHP
echo Extracting PHP...
powershell -Command "Expand-Archive -Path '%scriptDir%\%phpZipFile%' -DestinationPath '%scriptDir%\%phpExtractDir%' -Force"

REM Add PHP directory to PATH
echo Adding PHP directory to PATH...
set "phpPath=%scriptDir%%phpExtractDir%"
setx PATH "%phpPath%;%PATH%" /M

REM Cleanup
echo Cleanup...
del "%scriptDir%\%phpZipFile%"

echo PHP installation completed.

endlocal