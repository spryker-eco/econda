import Component from 'ShopUi/models/component';

declare var econda: any;

export default class EcondaCrossSellWidget extends Component {
    protected econdaContainer: HTMLElement;

    protected readyCallback(): void {}

    protected connectedCallback(): void {
        this.econdaContainer = this.querySelector(`.${this.jsName}__container`);
        this.initEcondaCrossSellWidget();
    }

    protected initEcondaCrossSellWidget(): void {
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
                    products: [{id: this.productSku}],
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

    protected getWidgetTemplate(data, element, escape): string {
        const widgetTitleTemplate = `<h3>${this.widgetTitle}</h3>`;
        let widgetProducts = ``;

        data.products.forEach((product) => {
            const productImageTemplate = `<img class="thumbnail" src="${product.iconurl}" alt="${product.name}">`
            const productLinkTemplate = `<a class="link" href="${this.getPathname(product.deeplink)}">${escape.html(product.name)}${productImageTemplate}</a>`;
            const productPriceTemplate = `<p><strong>${product.price}</strong></p>`;
            const productActionTemplate = `<div><a class="button button--expand" href="${this.getPathname(product.deeplink)}" tabindex="0">${this.viewButtonText} \»</a></div>`

            widgetProducts += `<div class="col--sm-4"><div class="spacing">${productLinkTemplate}${productPriceTemplate}${productActionTemplate}</div></div>`;
        })

        const widgetTemplate = `${widgetTitleTemplate}<div class="grid">${widgetProducts}</div>`;

        return widgetTemplate;
    }

    protected getPathname(url): string {
        return url.replace(document.location.hostname, '');
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

    get viewButtonText(): string {
        return this.getAttribute('product-view-button-text');
    }
}
