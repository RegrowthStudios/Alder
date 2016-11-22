@ECHO OFF

CALL updateVersion
IF %ERRORLEVEL% GEQ 1 (
    ECHO Updating the version of the project failed. Exiting.
    PAUSE
    EXIT 1
)

:REFRESH_VENDORS
ECHO Should vendors be refreshed?
SET /P CONFIRM_REFRESH_VENDORS="(Y/N): "
IF /I "%CONFIRM_REFRESH_VENDORS%" EQU "Y" (
    CALL refreshVendors
    IF %ERRORLEVEL% GEQ 1 (
        ECHO Refreshing the vendors of the project failed. Exiting.
        PAUSE
        EXIT 2
    )
)
IF /I "%CONFIRM_REFRESH_VENDORS%" NEQ "N" (
    IF /I "%CONFIRM_REFRESH_VENDORS%" NEQ "Y" (
        ECHO Unexpected input (Expected "Y" or "N"^)!
        GOTO REFRESH_VENDORS
    )
)

CALL runTests
IF %ERRORLEVEL% GEQ 1 (
    ECHO Tests failed to complete. Exiting.
    PAUSE
    EXIT 3
)

IF NOT EXIST build_template.zip (
    ECHO No build template exists, make sure to run refreshVendors at least once!
    PAUSE
    EXIT 4
)

SET /P VERSION=<VERSION.txt
SET BUILD_ARCHIVE=Sycamore-%VERSION%.zip

WHERE 7Z >nul 2>nul
IF %ERRORLEVEL% EQU 0 ( GOTO 7Z_METHOD )
WHERE WZZIP >nul 2>nul
IF %ERRORLEVEL% EQU 0 ( GOTO WZZIP_METHOD )
WHERE ZIP >nul 2>nul
IF %ERRORLEVEL% EQU 0 ( GOTO ZIP_METHOD )

ECHO No zipping utility was found for building, please add one to your environment variable 'PATH'.
PAUSE
EXIT 5

:ZIP_METHOD
ECHO Building project zip...

ECHO NOT YET IMPLEMENTED FOR ZIP!

PAUSE
EXIT 99

:7Z_METHOD
ECHO Building project zip...

COPY /Y "build_template.zip" %BUILD_ARCHIVE%
7Z a -r -y -tzip %BUILD_ARCHIVE% "LICENSE.txt" "VERSION.txt" "global.php" "public\*.php" "src\*.php" "config\*.php"

CD VENDOR-PATCH
7Z a -r -y -tzip "../%BUILD_ARCHIVE%" "*.php"

PAUSE
EXIT 0

:WZZIP_METHOD
ECHO Building project zip...

ECHO NOT YET IMPLEMENTED FOR WINZIP!

PAUSE
EXIT 99