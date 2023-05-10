import './page/product-launch-list';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('product-launch', {
    type: 'plugin',
    name: 'product-launch.general.mainMenuItemGeneral',
    title: 'product-launch.general.mainMenuItemGeneral',
    description: 'product-launch.general.descriptionTextModule',
    color: '#0b1628',
    icon: 'default-action-cloud-download',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'product-launch-list',
            path: 'list'
        }
    },

    navigation: [{
        label: 'product-launch.general.mainMenuItemGeneral',
        color: '#121f36',
        path: 'product.launch.list',
        parent: 'sw-catalogue',
        icon: 'default-shopping-paper-bag-product',
        position: 100
    }]
});



