@ECHO OFF

WHERE COMPOSER >nul 2>nul
IF %ERRORLEVEL% NEQ 0 ( GOTO NO_COMPOSER )

IF NOT EXIST composer.lock ( GOTO VENDORS_NOT_INSTALLED )

ECHO Updating vendors...
CALL COMPOSER update
ECHO Updated vendors!
GOTO VENDORS_REFRESHED

:NO_COMPOSER
ECHO No Composer utility could be found, please add it to your environment path!
PAUSE
EXIT /B 1

:VENDORS_NOT_INSTALLED
ECHO Vendors have not been installed, install them now?
SET /P CONFIRM_INSTALL_VENDORS="(Y/N): "
IF /I "%CONFIRM_INSTALL_VENDORS%" EQU "Y" ( GOTO INSTALL_VENDORS )
IF /I "%CONFIRM_INSTALL_VENDORS%" EQU "N" ( 
    ECHO No vendors to refresh!
    EXIT /B 2
) ELSE (
    ECHO Unexpected input (expected 'Y' or 'N')!
    GOTO VENDORS_NOT_INSTALLED
)

:INSTALL_VENDORS
ECHO Installing vendors...
CALL COMPOSER install

:VENDORS_REFRESHED
ECHO Optimising autoloader...
CALL COMPOSER dump-autoload --optimize

ECHO Patching vendors...
ROBOCOPY /S "%~dp0VENDOR-PATCH" "%~dp0vendor"
