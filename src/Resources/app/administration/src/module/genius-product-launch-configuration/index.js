import './components/genius-product-configuration-icon';
import './page/genius-product-launch-configuration';

import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';

Shopware.Module.register('genius-product-launch-configuration', {
    type: 'plugin',
    name: 'Genius Product Launch Configuration',
    title: 'genius-product-launch-configuration.general.mainMenuItemGeneral',
    description: 'genius-product-launch-configuration.general.descriptionTextModule',
    color: '#121f36',
    icon: 'default-action-settings',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'genius-product-launch-configuration',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index',
            }
        }
    },

    settingsItem: {
        group: 'plugins',
        to: 'genius.product.launch.configuration.index',
        iconComponent: 'genius-product-configuration-icon',
        backgroundEnabled: true,
    }
});
