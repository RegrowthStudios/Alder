#!/bin/bash

if [ ! -d "src" ]; then
    echo "No src path was found!"
    exit 1
fi

if [ ! -d "docs" ]; then
    mkdir docs
fi

if hash phpdoc 2>/dev/null; then
    dest=$(readlink -f "src")
    targ=$(readlink -f "docs")
    phpdoc run -d "${dest}" -t "${targ}" --title="Alder" --sourcecode --template="responsive-twig"
else
    echo "PHPDocumentor has not been installed yet."
    exit 1
fi
