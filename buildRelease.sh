#!/bin/bash

echo "Beginning build process..."

if [ ! -d "build" ]; then
    mkdir "build"
fi

component_str=""
build_name="alder_"
counter=1
for v in "$@"
do
    if [ ! -d "alder_$v" ]; then
        echo "No module by the name '$v' exists!"
        echo "Exiting..."
        exit 1
    fi
    if (( $# > $counter )); then
        component_str+="${v}, "
        build_name+="${v}_"
        counter+=1
    else
        component_str+="${v}"
        build_name+="${v}"
    fi
done

echo
echo "Building release with component(s): ${component_str}..."

build_dir="build/$build_name"

if [ -d "$build_dir" ]; then
    rm -r "$build_dir"
fi
mkdir "$build_dir"

echo
echo "Copying over third-party libraries..."
cp -r vendor/. "${build_dir}/vendor"

echo "Copying over core library..."
cp -r alder_core/config/. "${build_dir}/config" 2>/dev/null
cp -r alder_core/global/. "${build_dir}/global" 2>/dev/null
cp -r alder_core/public/. "${build_dir}/public" 2>/dev/null
cp -r alder_core/src/. "${build_dir}/src" 2>/dev/null

for v in "$@"
do
    echo "Copying over $v component..."
    cp -r "alder_${v}/apimap/." "${build_dir}/apimap" 2>/dev/null
    cp -r "alder_${v}/config/." "${build_dir}/config" 2>/dev/null
    cp -r "alder_${v}/global/." "${build_dir}/global" 2>/dev/null
    cp -r "alder_${v}/languages/." "${build_dir}/languages" 2>/dev/null
    cp -r "alder_${v}/public/." "${build_dir}/public" 2>/dev/null
    cp -r "alder_${v}/src/." "${build_dir}/src" 2>/dev/null
done

build_archive_dir="build/${build_name}.tar.gz"

if [ -f $build_archive_dir ]; then
    rm $build_archive_dir
fi

echo
echo "Creating build archive..."
tar -cvzf $build_archive_dir ${build_dir}/ 1>/dev/null

echo
echo "Build complete!"
exit 0