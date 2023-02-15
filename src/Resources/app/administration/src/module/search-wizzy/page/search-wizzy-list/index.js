import template from './search-wizzy-list.html.twig';


//const { Component } = Shopware;
const { Component,Mixin } = Shopware;
const { Criteria } = Shopware.Data;

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

                    if(response.data.type === 'error')
                    {
                        this.createNotificationError({
                            title: response.data.type,
                            message: "error stop"
                        });
                        return;
                    }
                    if(response.data.type === 'success')
                    {
                        this.createNotificationError({
                            title: response.data.type,
                            message: "successfully called"
                        });

                    }
                });
        },

        releaseProduct() {
            // alert("releaseProduct Running");
            let headers = this.configService.getBasicHeaders();
            return this.configService.httpClient
                .get('/search-wizzy/releaseProduct',
                    {headers}
                )
        }
    }

 });
