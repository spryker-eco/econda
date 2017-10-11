#!/usr/bin/env bash
moduleName='econda'
moduleNiceName='Econda'
cpath=`pwd`
modulePath="$cpath/module"
globalResult=1
message=""

function runTests {
    echo "Copy configuration..."
    if [ -f "vendor/spryker-eco/$moduleName/config/Shared/config.dist.php" ]; then
        tail -n +2 "vendor/spryker-eco/$moduleName/config/Shared/config.dist.php" >> config/Shared/config_default-devtest.php
        php "$modulePath/fix-config.php" config/Shared/config_default-devtest.php
    fi
    echo "Setup test environment..."
    ./setup_test -f
    echo "Running tests..."
    cd "vendor/spryker-eco/$moduleName/"
    codecept run
    if [ "$?" = 0 ]; then
        newMessage=$'\nTests are green'
        message="$message$newMessage"
    else
        newMessage=$'\nTests are failing'
        message="$message$newMessage"
    fi
    echo "Done tests"
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
    updates=`php "$modulePath/merge-composer.php" "$modulePath/composer.json" composer.json "$modulePath/composer.json"`
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

cd demoshop/
composer install

checkWithLatestDemoShop

echo "$message"
exit $globalResult
