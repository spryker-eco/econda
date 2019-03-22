import Component from 'ShopUi/models/component';

declare global {
    interface Window {
        ecWidgets: object[];
    }
}

export default class EcondaCrossSellWidget extends Component {

    protected econdaContainer: HTMLElement;
    protected econdaAid: string;
    protected productSku: string;
    protected categoryName: string;
    protected widgetTemplate: HTMLElement;

    protected readyCallback(): void {

        this.econdaContainer = document.getElementById('econda_widget_container');
        this.econdaAid = (<HTMLInputElement> document.getElementsByName('econda_aid')[0]).value;
        this.productSku = (<HTMLInputElement> document.getElementsByName('econda_product_sku')[0]).value;
        this.categoryName = (<HTMLInputElement> document.getElementsByName('econda_category_name')[0]).value;
        this.widgetTemplate = this.querySelector(`.${this.jsName}__widget-template`);

        this.initEcondaCrossSell();
    }

    protected initEcondaCrossSell(): void{
        if(typeof window['ecWidgets'] == 'undefined') {
            window.ecWidgets = [];
        }

        if (this.econdaContainer) {
            window.ecWidgets.push({
                element: this.econdaContainer,
                renderer: {
                    type: 'function',
                    rendererFn: this.getWidgetTemplate},
                    accountId: this.econdaAid,
                    id: 2, //id of widget you defined in Econda UI
                    context: {
                        products: [{id: this.productSku }],
                        categories: [{
                        type: 'productcategory',
                        path: this.categoryName
                    }]
                },
                chunkSize: 3
            });
        }
    }

    public getWidgetTemplate(): HTMLElement {
        const widgetTemplate = document.createElement('div');
        widgetTemplate.innerHTML = this.widgetTemplateContent;
        return widgetTemplate;
    }

    get widgetTemplateContent(): string {
        return this.getAttribute('widget-template-content');
    }
}
