@ECHO OFF

IF NOT EXIST modules ( GOTO NO_DIR_EXISTS )

WHERE PHPDOC >nul 2>nul
IF %ERRORLEVEL% NEQ 0 ( GOTO NO_PHPDOC )

ECHO Generating docs...
PHPDOC run -d "%~dp0modules" -t "%~dp0docs" --title="Alder" --sourcecode --template="responsive-twig"
EXIT /B 0

:NO_DIR_EXISTS
ECHO No path was found for the modules of this Alder project to generate docs from.
PAUSE
EXIT /B 1

:NO_PHPDOC
ECHO No PHPDOC utility could be found, please add it to your environment path!
PAUSE
EXIT /B 2