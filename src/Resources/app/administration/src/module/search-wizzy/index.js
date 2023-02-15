import './page/search-wizzy-list';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('search-wizzy', {
    type: 'plugin',
    name: 'search-wizzy.general.mainMenuItemGeneral',
    title: 'search-wizzy.general.mainMenuItemGeneral',
    description: 'search-wizzy.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'default-action-cloud-download',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'search-wizzy-list',
            path: 'list'
        }
    },

    navigation: [{
        //id: 'search-wizzy-list',
        label: 'search-wizzy.general.mainMenuItemGeneral',
        color: '#ff3d58',
        path: 'search.wizzy.list',
        parent: 'sw-catalogue',
        icon: 'default-shopping-paper-bag-product',
        position: 100
    }]
});



