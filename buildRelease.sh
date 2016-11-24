#!/bin/bash

if (( $# < 1 )); then
    echo "Must supply the name of the component(s) (less the 'alder_' prefix) to include in the build."
    exit 1
fi

echo "Beginning build process..."

if [ ! -d "build" ]; then
    mkdir "build"
fi
if [ ! -d "build/artefacts" ]; then
    mkdir "build/artefacts"
fi
if [ ! -d "build/test_results" ]; then
    mkdir "build/test_results"
fi
if [ ! -d "build/tmp" ]; then
    mkdir "build/tmp"
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

tmp_dir="build/tmp"
tmp_build_dir="${tmp_dir}/$build_name"
test_results_dir="build/test_results/$build_name"

if [ -d "$tmp_build_dir" ]; then
    rm -r "$tmp_build_dir"
fi
mkdir "$tmp_build_dir"

echo
echo "Copying over third-party libraries..."
cp -r vendor/. "${tmp_build_dir}/vendor"

echo "Copying over core library..."
cp alder_core/global.php "${tmp_build_dir}/global.php" 2>/dev/null
cp alder_core/phpunit.xml "${tmp_build_dir}/phpunit.xml" 2>/dev/null
cp -r alder_core/config/. "${tmp_build_dir}/config" 2>/dev/null
cp -r alder_core/global/. "${tmp_build_dir}/global" 2>/dev/null
cp -r alder_core/public/. "${tmp_build_dir}/public" 2>/dev/null
cp -r alder_core/src/. "${tmp_build_dir}/src" 2>/dev/null
cp -r alder_core/tests/. "${tmp_build_dir}/tests" 2>/dev/null

for v in "$@"
do
    echo "Copying over $v component..."
    cp -r "alder_${v}/apimap/." "${tmp_build_dir}/apimap" 2>/dev/null
    cp -r "alder_${v}/config/." "${tmp_build_dir}/config" 2>/dev/null
    cp -r "alder_${v}/global/." "${tmp_build_dir}/global" 2>/dev/null
    cp -r "alder_${v}/languages/." "${tmp_build_dir}/languages" 2>/dev/null
    cp -r "alder_${v}/public/." "${tmp_build_dir}/public" 2>/dev/null
    cp -r "alder_${v}/src/." "${tmp_build_dir}/src" 2>/dev/null
    cp -r "alder_${v}/tests/." "${tmp_build_dir}/tests" 2>/dev/null
done

echo
echo "Running tests for build..."
./runTests.sh $tmp_build_dir $test_results_dir
echo "Tests complete."

echo
echo "Creating build archive..."
curr_dir=$(pwd)
cd $tmp_dir
artefacts_dir="../artefacts/$build_name"
build_archive_dir="${artefacts_dir}/${build_name}.tar.gz"

if [ ! -d $artefacts_dir ]; then
    mkdir $artefacts_dir
fi

if [ -f $build_archive_dir ]; then
    rm $build_archive_dir
fi

tar -cvzf $build_archive_dir $build_name 1>/dev/null
cd $curr_dir

echo
echo "Clearing up..."
rm -r $tmp_dir

echo
echo "Build complete!"
exit 0