import './js/emos2';
import './js/econda-recommendations';
import register from 'ShopUi/app/registry';
export default register('econda-tracker', () => import(/* webpackMode: "lazy" */'./econda-tracker'));
