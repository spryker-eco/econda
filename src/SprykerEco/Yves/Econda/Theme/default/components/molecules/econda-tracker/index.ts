import register from 'ShopUi/app/registry';
export default register('econda-tracker', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "econda-tracker" */
    './econda-tracker'));
