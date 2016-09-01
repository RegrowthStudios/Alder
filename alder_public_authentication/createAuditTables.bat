:: TODO(Matthew): Implement generation of audit table files.

@ECHO OFF

GOTO :main

:clearAuditFiles
SETLOCAL
FOR %%i IN (%~dp0test\*.php) DO (
  ECHO.%%i | FINDSTR /C:"Audit">NUL && (
    DEL %%i
  )
)
ENDLOCAL
GOTO :EOF

:generateAuditFiles
SETLOCAL EnableDelayedExpansion
FOR %%i IN (%~dp0src\Alder\PublicAuthentication\Db\Row\*.php) DO (
  ECHO.%%i ^| FINDSTR /C:"Audit">NUL && (
    SET "filepath=%%~dpniAudit%%~xi"
    SET relfilepath=.\!filepath:%~dp0=!
    SET /A count=0
    FOR /F "tokens=*" %%l IN (%%i) DO (
      SET "line=%%l"
      IF "x%%l" NEQ "x<?php" (
        CALL :padLine "%%l" !count!
      )
      FOR %%t IN (%%l) DO (
        IF "%%tx" EQU "{x" (
          SET /A count=count+1
        ) ELSE (
          IF "%%tx" EQU "}x" (
            SET /A count=count-1
            CALL :padLine %%l !count!
            IF !count! LEQ 0 (
              ECHO.        public $editor_id>>"!relfilepath!"
              ECHO.        public $action>>"!relfilepath!"
              ECHO.        public $etag>>"!relfilepath!"
              ECHO.        public $timestamp>>"!relfilepath!"
            )
          )
        )
      )
      ECHO.!line!>>"!relfilepath!"
    )
  )
)
ENDLOCAL
GOTO :EOF

:padLine
SETLOCAL EnableDelayedExpansion
SET "padding="
FOR /L %%Z IN (0, 1, %2) DO (
  SET "padding=    !padding!"
)
ENDLOCAL & SET "line=%padding%%~1"
GOTO :EOF

:main
CALL :clearAuditFiles
CALL :generateAuditFiles
GOTO :EOF