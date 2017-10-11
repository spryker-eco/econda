#!/usr/bin/env bash

RED='\e[101m'
GREEN='\e[102m'
buildResult=1
buildMessage=""

function runTests {
    echo "define('APPLICATION_ROOT_DIR', '$TRAVIS_BUILD_DIR/$SHOP_DIR');" >> "$TRAVIS_BUILD_DIR/$SHOP_DIR/vendor/composer/autoload_real.php"
    echo "Running tests..."
    cd "vendor/spryker-eco/$MODULE_NAME/"
    "$TRAVIS_BUILD_DIR/$SHOP_DIR/vendor/bin/codecept" run
    if [ "$?" = 0 ]; then
        buildMessage="${buildMessage}\n${GREEN}Tests are passing"
        result=0
    else
        buildMessage="${buildMessage}\n${RED}Tests are failing"
        result=1
    fi
    cd "$TRAVIS_BUILD_DIR/$SHOP_DIR"
    echo "Tests finished"
    return $result
}

function checkWithLatestDemoShop {
    echo "Checking module with latest Demo Shop..."
    composer config repositories.ecomodule path "$TRAVIS_BUILD_DIR/$MODULE_DIR"
    composer require "spryker-eco/$MODULE_NAME @dev"
    result=$?
    if [ "$result" = 0 ]; then
        buildMessage="${buildMessage}\n${GREEN}Current version of module is compatible with latest Demo Shop modules versions"
        if runTests; then
            buildResult=0
            checkModuleWithLatestVersionOfDemoShop
        fi
    else
        buildMessage="${buildMessage}\n${RED}Current version of module is not compatible with latest Demo Shop due to modules versions"
        checkModuleWithLatestVersionOfDemoShop
    fi
}

function checkModuleWithLatestVersionOfDemoShop {
    echo "Merging composer.json dependencies..."
    updates=`php "$TRAVIS_BUILD_DIR/$MODULE_DIR/build/merge-composer.php" "$TRAVIS_BUILD_DIR/$MODULE_DIR/composer.json" composer.json "$TRAVIS_BUILD_DIR/$MODULE_DIR/composer.json"`
    if [ "$updates" = "" ]; then
        buildMessage="${buildMessage}\n${GREEN}Module is compatible with latest versions of modules used in Demo Shop"
        return
    fi
    buildMessage="${buildMessage}\nUpdated dependencies in module to match Demo Shop\n"
    echo "Installing module with updated dependencies..."
    composer require "spryker-eco/$MODULE_NAME @dev"
    result=$?
    if [ "$result" = 0 ]; then
        buildMessage="${buildMessage}\n${GREEN}Module is compatible with latest versions of modules used in Demo Shop"
        runTests
    else
        buildMessage="${buildMessage}\n${RED}Module is not compatible with latest versions of modules used in Demo Shop"
    fi
}

cd $SHOP_DIR
checkWithLatestDemoShop
echo -e "$buildMessage"
exit $buildResult
