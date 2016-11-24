#!/bin/bash

if (( $# != 2 )); then
    echo "Must supply the directory paths of the build to test and the location to dump test results."
    exit 1
fi

phpunit --log-json=$2/test_results.json --coverage-html=$2/coverage/ --coverage-php=$2/coverage.php --testdox-html=$2/test_results.html --configuration=$1/phpunit.xml
