/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

require('./html/cross-sell-widget.html');

var econda_aid = document.getElementsByName('econda_aid')[0].value;

module.exports = {
    init: function() {
        /**
         * Setup widget, load data and render using defined rendering function
         */
        if(typeof window.ecWidgets == 'undefined') {
            window.ecWidgets = [];
        }
        if (document.getElementById('econda_widget_container')) {
            var product_sku = document.getElementsByName('econda_product_sku')[0].value;
            var category_name = document.getElementsByName('econda_category_name')[0].value;
            window.ecWidgets.push({
                element: document.getElementById('econda_widget_container'),
                renderer: {type: 'template', uri: '/assets/default/html/cross-sell-widget.html'},
                accountId: econda_aid,
                id: 2, //id of widget you defined in Econda UI
                context: {
                    products: [{id: product_sku }],
                    categories: [{
                        type: 'productcategory',
                        path: category_name
                    }]
                },
                chunkSize: 3
            });
        }
    }
};