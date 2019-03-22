import './econda-lib/lib/ejs/ejs';
import './econda-lib/lib/ejs/view';
import './econda-lib/lib/ecwidget/econdawidget';
import './econda-lib/emos2';
import './econda-lib/econda-recommendations';
import register from 'ShopUi/app/registry';
export default register('econda-cross-sell-widget', () => import(/* webpackMode: "lazy" */'./econda-cross-sell-widget'));
