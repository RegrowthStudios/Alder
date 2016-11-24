#!/bin/bash

if (( $# < 1 )); then
    echo "Must supply the name of the component(s) (less the 'alder_' prefix) to update the version(s) of."
    exit 1
fi

for v in "$@"
do
    if [ ! -d "alder_$v" ]; then
        echo "No module by the name '$v' exists!"
        continue
    fi

    version_file="alder_${v}/VERSION.txt"

    curr_version=$(head -n 1 $version_file)
    echo "Current version of alder_$v is: $curr_version"

    read -p "Please provide the new version: " new_version

    echo $new_version > $version_file
done
