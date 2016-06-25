@ECHO OFF

SET /P CURR_VERSION_ID=<VERSION.txt
ECHO Current project version: %CURR_VERSION_ID%

SET /P NEW_VERSION_ID=Enter the new Version ID: 
ECHO Updating version of project...
> VERSION.txt ECHO %NEW_VERSION_ID%

PAUSE
EXIT /B 0