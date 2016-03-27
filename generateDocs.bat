@ECHO OFF

IF EXIST modules\Sycamore ( GOTO DIR_EXISTS )

ECHO No path was found for the modules of this Sycamore project to generate docs from.
PAUSE
EXIT 0

:DIR_EXISTS
ECHO Generating docs...
PHPDOC run -d "%~dp0modules\Sycamore" -t "%~dp0docs" --title="Sycamore" --sourcecode --template="responsive-twig"