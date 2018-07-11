/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

'use strict';

var $ = require('jquery');

module.exports = {
    init: function () {
        window.emosTrackVersion = 2; //version of tracking lib that you are using
        var hashCode = function(str){
            var hash = 0, char;
            if (str.length == 0) return hash;
            for (i = 0; i < str.length; i++) {
                char = str.charCodeAt(i);
                hash = ((hash<<5)-hash)+char;
                hash = hash & hash; // Convert to 32bit integer
            }
            if (hash < 0) {hash = -hash;}
            return "" + hash;
        };

        var emospro = {
            siteid: window.econda_siteid,
            content: window.econda_tracking_content,
            langid: $("html").prop("lang"),
            pageId: hashCode(window.location.href)
        };

        if (window.econda_search_query_string) {
            emospro.search = [window.econda_search_query_string, window.econda_search_number_results];
        }

        if (window.econda_register_result) {
            emospro.register = [window.econda_register_result, 0];
        } else if (window.econda_register_result == false) {
            emospro.register = [0, 1];
        }

        if (window.econda_login_result) {
            emospro.login = [window.econda_login_result, 0];
        } else if (window.econda_login_result == false) {
            emospro.login = [0, 1];
        }

        if (window.econda_newsletter_subscription) {
            emospro.Target =  ['newsletter', 'Default newsletter subscription', 1, 'd'];
        }

        if (window.econda_product_name) {
            emospro.ec_Event = [
                {
                    type: 'view' ,
                    pid: window.econda_product_sku,
                    sku: window.econda_product_sku,
                    name: window.econda_product_name,
                    price: window.econda_product_price,
                    group: window.econda_category_name,
                    count: 1
                }
            ];
        }

        if (window.econda_billing_order_value) {
            emospro.billing = [
                window.econda_billing_invoice_number,
                econda_billing_customer_id,
                econda_billing_location,
                econda_billing_order_value
            ];
        }

        if (window.econda_order_process) {
            emospro.orderProcess = window.econda_order_process;
        }

        if (window.econda_bought_product_name && window.econda_bought_product_name.length > 0) {
            emospro.ec_Event = [];
            for (var i = 0, len = econda_bought_product_name.length; i < len; i++) {
                emospro.ec_Event.push({
                    type: 'buy' ,
                    pid: window.econda_bought_product_sku[i],
                    sku: window.econda_bought_product_sku[i],
                    name: window.econda_bought_product_name[i],
                    price: window.econda_bought_product_price[i],
                    count: window.econda_bought_product_count[i]
                });
            }
        }

        window.emosPropertiesEvent(emospro);
    }
};