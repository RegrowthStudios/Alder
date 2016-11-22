#!/bin/bash

ask_yes_or_no() {
    read -p "$1 (y/n)? " -n 1
    case $(echo $REPLY | tr '[A-Z]' '[a-z]') in
        y ) return 0 ;;
        n ) return 1 ;;
        * ) (
            echo
            return $(ask_yes_or_no "$@")
        ) ;;
    esac
}

if ! hash composer 2>/dev/null; then
    echo "Composer has not been set up yet."
    exit 1
fi

if [ ! -f composer.lock ]; then
    echo "Vendors have not yet been installed."
    if ( ask_yes_or_no "Install vendors now" ); then
        echo "Installing vendors..."
        composer install
        echo
    else
        echo "No vendors to refresh."
        exit 2
    fi
else
    echo "Updating vendors..."
    composer update
    echo
fi

echo "Optimising autoloader..."
composer dump-autoload --optimize
echo

if [ -d VENDOR-PATCH ]; then
    echo "Patching vendors..."
    cp -r -f VENDOR-PATCH/. vendor/
    echo
fi
