/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict;'

require('./js/emos2');
require('./js/econda-recommendations');

var econdaTracking = require('./econda-tracking');
econdaTracking.init();

var econdaWidget = require('./econda-widget');
econdaWidget.init();