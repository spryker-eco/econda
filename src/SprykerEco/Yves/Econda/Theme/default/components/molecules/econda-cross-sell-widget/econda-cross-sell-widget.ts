import Component from 'ShopUi/models/component';

declare var econda: any;

export default class EcondaCrossSellWidget extends Component {
    protected econdaContainer: HTMLElement;

    protected readyCallback(): void {
        this.econdaContainer = this.querySelector(`.${this.jsName}__container`);
        this.initEcondaCrossSellWidget();
    }

    protected initEcondaCrossSellWidget(): void{
        if (this.econdaContainer) {
            const econdaWidget = new econda.recengine.Widget({
                element: this.econdaContainer,
                accountId: this.accountId,
                renderer: {
                    type: 'function',
                    rendererFn: this.getWidgetTemplate.bind(this)
                },
                id: 2,
                context: {
                    products: [{id: this.productSku }],
                    categories: [{
                        type: 'productcategory',
                        path: this.categoryName
                    }]
                },
                chunkSize: 3
            });
            econdaWidget.render();
        }
    }

    protected getWidgetTemplate(data, element, esc): string {
        let widgetTitle = `<h3>${this.widgetTitle}</h3>`;
        let widgetProducts = ``;

        data.products.forEach((product)=>{
            let productImage = `<img class="thumbnail" src="${product.iconurl}" alt="${product.name}"/>`
            let productLink = `<a class="link" href="${product.deeplink}">${esc.html(product.name)}${productImage}</a>`;
            let productPrice = `<p class=""><strong>${product.price}</strong></p>`;
            let productAction = `<div><a class="button button--expand" href="${product.deeplink}" tabindex="0">View »</a></div>`

            widgetProducts += `<div class="col--sm-4"><div class="spacing">${productLink}${productPrice}${productAction}</div></div>`;
        })

        let widgetTemplate = `${widgetTitle}<div class="grid">${widgetProducts}</div>`;

        return widgetTemplate;
    }

    get widgetTitle(): string {
        return this.getAttribute('widget-title');
    }

    get accountId(): string {
        return this.getAttribute('account-id');
    }

    get productSku(): string {
        return this.getAttribute('product-sku');
    }

    get categoryName(): string {
        return this.getAttribute('category-name');
    }
}
