import Component from 'ShopUi/models/component';

declare global {
    interface Window {
        emosTrackVersion: number;
        econda_search_query_string: string;
        econda_siteid: string;
        econda_tracking_content: string;
        econda_search_number_results: string;
        econda_register_result: string;
        econda_login_result: string;
        econda_newsletter_subscription: string;
        econda_product_name: string;
        econda_product_sku: string;
        econda_product_price: string;
        econda_billing_order_value: string;
        econda_billing_invoice_number: string;
        econda_order_process: string;
        econda_bought_product_name: string;
        econda_bought_product_sku: string;
        econda_bought_product_price: string;
        econda_bought_product_count: string;
        econda_billing_customer_id: string;
        econda_billing_location: string;
        emosPropertiesEvent(emospro: Emospro): any;
    }
}

interface Emospro {
    siteid: string;
    content: string;
    langid: string;
    pageId: string | number;
    search?: [string, number | string];
    register?: [number, number];
    login?: [number, number];
    Target?: [string, string, number, string];
    ec_Event?: EmosproEvent[];
    billing?: string[];
    orderProcess?: string;
}

interface EmosproEvent {
    type: string;
    pid: string;
    sku: string;
    name: string;
    price: string;
    group?: string;
    count: string | number;
}

export default class EcondaTracker extends Component {

    public emospro: Emospro;

    protected readyCallback(): void {
        this.getValues();
        this.initEcondaTracker();
    }

    protected getValues(): void {
        this.querySelectorAll('input').forEach((input: HTMLInputElement)=>{
            window[input.name] = input.value;
        })
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
            this.emospro.register = [Number(window.econda_register_result), 0];
        } else if (Boolean(window.econda_register_result) === false) {
            this.emospro.register = [0, 1];
        }

        if (window.econda_login_result) {
            this.emospro.login = [Number(window.econda_login_result), 0];
        } else if (Boolean(window.econda_login_result) === false) {
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
                window.econda_billing_customer_id,
                window.econda_billing_location,
                window.econda_billing_order_value
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
