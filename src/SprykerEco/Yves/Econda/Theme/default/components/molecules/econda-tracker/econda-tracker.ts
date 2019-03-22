import Component from 'ShopUi/models/component';

declare global {
    interface Window {
        emosTrackVersion: number;
        econda_search_query_string: string;
        econda_siteid: any;
        econda_tracking_content: any;
        econda_search_number_results: any;
        econda_register_result: boolean;
        econda_login_result: boolean;
        econda_newsletter_subscription: any;
        econda_product_name: any;
        econda_product_sku: any;
        econda_product_price: any;
        econda_billing_order_value: any;
        econda_billing_invoice_number: any;
        econda_order_process: any;
        econda_bought_product_name: any;
        econda_bought_product_sku: any;
        econda_bought_product_price: any;
        econda_bought_product_count: any;
        emosPropertiesEvent(emospro: Emospro): any;
    }
}

interface Emospro {
    siteid: any;
    content: any;
    langid: string;
    pageId: string | number;
    search?: any;
    register?: [boolean | number, number];
    login?: [boolean | number, number];
    Target?: [string, string, number, string];
    ec_Event?: EmosproEvent[];
    billing?: any[];
    orderProcess?: any;
}

interface EmosproEvent {
    type: string;
    pid: any;
    sku: any;
    name: any;
    price: any;
    group?: any;
    count: number;
}

export default class EcondaTracker extends Component {

    public emospro: Emospro;

    protected readyCallback(): void {
        this.initEcondaTracker();
    }

    protected initEcondaTracker(): void {
        window.emosTrackVersion = 2; //version of tracking lib that you are using

        this.emospro = {
            siteid: window.econda_siteid,
            content: window.econda_tracking_content,
            langid: document.querySelector('html').getAttribute('lang'),
            pageId: this.gethashCode(window.location.href)
        };

        if (window.econda_search_query_string) {
            this.emospro.search = [window.econda_search_query_string, window.econda_search_number_results];
        }

        if (window.econda_register_result) {
            this.emospro.register = [window.econda_register_result, 0];
        } else if (window.econda_register_result == false) {
            this.emospro.register = [0, 1];
        }

        if (window.econda_login_result) {
            this.emospro.login = [window.econda_login_result, 0];
        } else if (window.econda_login_result == false) {
            this.emospro.login = [0, 1];
        }

        if (window.econda_newsletter_subscription) {
            this.emospro.Target =  ['newsletter', 'Default newsletter subscription', 1, 'd'];
        }

        if (window.econda_product_name) {
            this.emospro.ec_Event = [
                {
                    type: 'view' ,
                    pid: window.econda_product_sku,
                    sku: window.econda_product_sku,
                    name: window.econda_product_name,
                    price: window.econda_product_price,
                    group: window.econda_product_name,
                    count: 1
                }
            ];
        }

        if (window.econda_billing_order_value) {
            this.emospro.billing = [
                window.econda_billing_invoice_number,
                // econda_billing_customer_id,
                // econda_billing_location,
                // econda_billing_order_value
            ];
        }

        if (window.econda_order_process) {
            this.emospro.orderProcess = window.econda_order_process;
        }

        if (window.econda_bought_product_name && window.econda_bought_product_name.length > 0) {
            this.emospro.ec_Event = [];
            for (let i = 0, productNameLength = window.econda_bought_product_name.length; i < productNameLength; i++) {
                this.emospro.ec_Event.push({
                    type: 'buy' ,
                    pid: window.econda_bought_product_sku[i],
                    sku: window.econda_bought_product_sku[i],
                    name: window.econda_bought_product_name[i],
                    price: window.econda_bought_product_price[i],
                    count: window.econda_bought_product_count[i]
                });
            }
        }

        window.emosPropertiesEvent(this.emospro);
    }

    protected gethashCode(string: string): string | number {
        let hash = 0;
        let char;
        if (string.length == 0) return hash;
        for (let i = 0; i < string.length; i++) {
            char = string.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }

        if (hash < 0) hash = -hash;
        return String(hash);
    }


}
