#!/bin/bash

if ! hash phpdoc 2>/dev/null; then
    echo "PHPDocumentor has not been installed yet."
    exit 1
fi

if (( $# < 1 )); then
    echo "Please supply a list of components (less the 'alder_' prefix) to generate docs for!"
    exit 1
fi

for v in "$@"
do
    if [ ! -d "alder_$v" ]; then
        echo "No module by the name '$v' exists!"
        continue
    fi

    src_dir="alder_${v}/src"
    docs_dir="alder_${v}/docs"

    if [ ! -d $src_dir ]; then
        echo "No src path was found, component ${v} is invalid!"
        exit 1
    fi

    if [ ! -d $docs_dir ]; then
        mkdir docs
    fi

    dest=$(readlink -f $src_dir)
    targ=$(readlink -f $docs_dir)
    phpdoc run -d "${dest}" -t "${targ}" --title="Alder" --sourcecode --template="responsive-twig"
done
