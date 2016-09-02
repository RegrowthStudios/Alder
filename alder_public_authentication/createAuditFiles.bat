:: TODO(Matthew): Implement generation of audit table files.

@ECHO OFF

GOTO :main

:clearAuditFiles
SETLOCAL
FOR /R %%i IN (.\*.php) DO (
    :: Determine if a file contains "Audit" as a substring, delete it if so.
    ECHO.%%~ni | FINDSTR /C:"Audit">NUL && (
        DEL %%i
    )
)
ENDLOCAL
GOTO :eof

:generateAuditRows
SETLOCAL EnableDelayedExpansion
FOR %%i IN (.\Row\*.php) DO (
    SET filepath=.\Row\%%~niAudit%%~xi
    SET /A count=0
    FOR /F "tokens=*" %%l IN (%%i) DO (
        SET "line=%%l"
        IF "x%%l" NEQ "x<?php" (
            CALL :tab "%%l" !count!
        )
        FOR %%t IN (%%l) DO (
            IF "x%%t" EQU "x{" (
                SET /A count=count+1
            ) ELSE (
                IF "x%%t" EQU "x}" (
                    SET /A count=count-1
                    CALL :tab %%l !count!
                    IF !count! LEQ 0 (
                        ECHO.        public $editor_id>>"!filepath!"
                        ECHO.        public $editor_ip>>"!filepath!"
                        ECHO.        public $editor_action>>"!filepath!"
                        ECHO.        public $last_etag>>"!filepath!"
                    )
                )
            )
        )
        ECHO.!line!>>"!filepath!"
    )
)
ENDLOCAL
GOTO :eof

:generateAuditTables
SETLOCAL EnableDelayedExpansion
FOR %%i IN (.\Table\*.php) DO (
    SET filepath=.\Table\%%~niAudit%%~xi
    SET /A count=0
    FOR /F "tokens=*" %%l IN (%%i) DO (
        SET "line=%%l"
        IF "x%%l" NEQ "x<?php" (
            CALL :tab "%%l" !count!
        )
        FOR %%t IN (%%l) DO (
            IF "x%%t" EQU "xnamespace" (
                ECHO.>>"!filepath!"
            )
            IF "x%%t" EQU "x{" (
                SET /A count=count+1
            ) ELSE (
                IF "x%%t" EQU "x}" (
                    SET /A count=count-1
                    CALL :tab %%l !count!
                )
            )
        )
        ECHO.!line!>>"!filepath!"
    )
)
ENDLOCAL
GOTO :eof

:tab
SETLOCAL EnableDelayedExpansion
SET "padding="
FOR /L %%Z IN (0, 1, %2) DO (
    SET "padding=     !padding!"
)
ENDLOCAL & SET "line=%padding%%~1"
GOTO :eof

:pad
SETLOCAL EnableDelayedExpansion
SET "padding="
FOR /L %%Z IN (0, 1, %2) DO (
    SET "padding= !padding!"
)
ENDLOCAL & SET "line=%padding%%~1"
GOTO :eof

:main
PUSHD %~dp0src\Alder\PublicAuthentication\Db

ECHO Clearing existing audit rows and tables...
CALL :clearAuditFiles

ECHO Generating audit rows...
CALL :generateAuditRows

ECHO Generating audit tables...
CALL :generateAuditTables

ECHO Completed audit file generation.
POPD