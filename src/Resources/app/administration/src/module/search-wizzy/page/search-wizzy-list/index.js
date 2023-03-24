import template from './search-wizzy-list.html.twig';

const { Component,Mixin } = Shopware;

Component.register('search-wizzy-list', {
    template, // ES6 shorthand for: 'template: template'

    inject: [
        'configService',
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    methods: {
        importProduct() {
            //alert("get all products");
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient
                .get('/search-wizzy/productimport',
                    {headers}
                ).then((response) => {
                    if(response.data.type === 'successs'){
                        this.createNotificationError({
                            title: response.data.type,
                            message: response.data.message
                        });
                    }else{
                        this.createNotificationSuccess({
                            title: response.data.type,
                            message: response.data.message
                        });
                    }
            });
        },

        releaseProduct() {
            // alert("releaseProduct Running");
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient.get('/search-wizzy/releaseProduct',
                {headers}
            )
        }
    }
 });
