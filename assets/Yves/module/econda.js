/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

'use strict;'
require('./js/emos2');
require('./js/econda-recommendations');

var econdaTracking = require('./econda-tracking');
econdaTracking.init();

var econdaWidget = require('./econda-widget');
econdaWidget.init();