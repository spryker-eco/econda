#!/usr/bin/env bash

buildResult=1
buildMessage=""

function runTests {
    echo "define('APPLICATION_ROOT_DIR', '$TRAVIS_BUILD_DIR/$SHOP_FOLDER');" >> "$TRAVIS_BUILD_DIR/$SHOP_FOLDER/vendor/composer/autoload_real.php"
    echo "Running tests..."
    cd "vendor/spryker-eco/$MODULE_NAME/"
    "$TRAVIS_BUILD_DIR/$SHOP_FOLDER/vendor/bin/codecept" run
    if [ "$?" = 0 ]; then
        newMessage=$'\nTests are green'
        buildMessage="$buildMessage$newMessage"
        testResult=0
    else
        newMessage=$'\nTests are failing'
        buildMessage="$buildMessage$newMessage"
        testResult=1
    fi
    cd "$TRAVIS_BUILD_DIR/$SHOP_FOLDER"
    echo "Done tests"
    return $testResult
}

function checkWithLatestDemoShop {
    echo "Checking module with latest DemoShop"
    composer config repositories.ecomodule path "$TRAVIS_BUILD_DIR/$MODULE_FOLDER"
    composer require "spryker-eco/$MODULE_NAME @dev"
    result=$?
    if [ "$result" = 0 ]; then
        newMessage=$'\nCurrent version of module is COMPATIBLE with latest DemoShop modules\' versions'
        buildMessage="$buildMessage$newMessage"
        if runTests; then
            buildResult=0
            checkModuleWithLatestVersionOfDemoShop
        fi
    else
        newMessage=$'\nCurrent version of module is NOT COMPATIBLE with latest DemoShop due to modules\' versions'
        buildMessage="$buildMessage$newMessage"
        checkModuleWithLatestVersionOfDemoShop
    fi
}

function checkModuleWithLatestVersionOfDemoShop {
    echo "Merging composer.json dependencies..."
    updates=`php "$TRAVIS_BUILD_DIR/$MODULE_FOLDER/build/merge-composer.php" "$TRAVIS_BUILD_DIR/$MODULE_FOLDER/composer.json" composer.json "$TRAVIS_BUILD_DIR/$MODULE_FOLDER/composer.json"`
    if [ "$updates" = "" ]; then
        newMessage=$'\nModule is COMPATIBLE with latest versions of modules used in DemoShop'
        buildMessage="$buildMessage$newMessage"
        return
    fi
    newMessage=$'\nUpdated dependencies in module to match DemoShop\n'
    buildMessage="$buildMessage$newMessage$updates"
    echo "Installing module with updated dependencies..."
    composer require "spryker-eco/$MODULE_NAME @dev"
    result=$?
    if [ "$result" = 0 ]; then
        newMessage=$'\nModule is COMPATIBLE with latest versions of modules used in DemoShop'
        buildMessage="$buildMessage$newMessage"
        runTests
    else
        newMessage=$'\nModule is NOT COMPATIBLE with latest versions of modules used in DemoShop'
        buildMessage="$buildMessage$newMessage"
    fi
}

cd $SHOP_FOLDER
checkWithLatestDemoShop
echo "$buildMessage"
exit $buildResult
