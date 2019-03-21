import './js/lib/ejs/ejs';
import './js/lib/ejs/view';
import './js/lib/ecwidget/econdawidget';
import './js/emos2';
import './js/econda-recommendations';
import register from 'ShopUi/app/registry';
export default register('econda-cross-sell-widget', () => import(/* webpackMode: "lazy" */'./econda-cross-sell-widget'));
