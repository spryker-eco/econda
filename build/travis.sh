#!/usr/bin/env bash
moduleName=$MODULE_NAME
modulePath="$HOME/$BUILD_MODULE_FOLDER"
shopPath="$HOME/$BUILD_SHOP_FOLDER"
globalResult=1
message=""

echo "$HOME"
echo "$modulePath"
echo "shopPath"

function runTests {
    echo "define('APPLICATION_ROOT_DIR', '$shopPath');" >> "$shopPath/vendor/composer/autoload_real.php"
    echo "Running tests..."
    cd "vendor/spryker-eco/$moduleName/"
    "$shopPath/vendor/bin/codecept" run
    if [ "$?" = 0 ]; then
        newMessage=$'\nTests are green'
        message="$message$newMessage"
        testResult=0
    else
        newMessage=$'\nTests are failing'
        message="$message$newMessage"
        testResult=1
    fi
    cd "$shopPath"
    echo "Done tests"
    return $testResult
}

function checkWithLatestDemoShop {
    echo "Checking module with latest DemoShop"
    composer config repositories.ecomodule path $modulePath
    composer require "spryker-eco/$moduleName @dev"
    result=$?
    if [ "$result" = 0 ]; then
        newMessage=$'\nCurrent version of module is COMPATIBLE with latest DemoShop modules\' versions'
        message="$message$newMessage"
        if runTests; then
            globalResult=0
            checkModuleWithLatestVersionOfDemoShop
        fi
    else
        newMessage=$'\nCurrent version of module is NOT COMPATIBLE with latest DemoShop due to modules\' versions'
        message="$message$newMessage"
        checkModuleWithLatestVersionOfDemoShop
    fi
}

function checkModuleWithLatestVersionOfDemoShop {
    echo "Merging composer.json dependencies..."
    updates=`php "$modulePath/build/merge-composer.php" "$modulePath/composer.json" composer.json "$modulePath/composer.json"`
    if [ "$updates" = "" ]; then
        newMessage=$'\nModule is COMPATIBLE with latest versions of modules used in DemoShop'
        message="$message$newMessage"
        return
    fi
    newMessage=$'\nUpdated dependencies in module to match DemoShop\n'
    message="$message$newMessage$updates"
    echo "Installing module with updated dependencies..."
    composer require "spryker-eco/$moduleName @dev"
    result=$?
    if [ "$result" = 0 ]; then
        newMessage=$'\nModule is COMPATIBLE with latest versions of modules used in DemoShop'
        message="$message$newMessage"
        runTests
    else
        newMessage=$'\nModule is NOT COMPATIBLE with latest versions of modules used in DemoShop'
        message="$message$newMessage"
    fi
}

cd $BUILD_SHOP_FOLDER
checkWithLatestDemoShop
echo "$message"
exit $globalResult
