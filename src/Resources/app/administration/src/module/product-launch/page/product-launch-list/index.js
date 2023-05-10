import template from './product-launch-list.html.twig';

const { Component,Mixin } = Shopware;

Component.register('product-launch-list', {
    template, // ES6 shorthand for: 'template: template'

    inject: [
        'configService',
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    methods: {
        releaseProduct() {
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/product-launch/releaseProduct',
                {headers}
            )
        }
    }
 });
