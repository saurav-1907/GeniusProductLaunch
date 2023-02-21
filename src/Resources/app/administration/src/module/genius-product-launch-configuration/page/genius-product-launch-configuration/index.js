import template from './genius-product-launch-configuration.html.twig';

const { Component, Mixin, Defaults } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('genius-product-launch-configuration',{
    template,

    inject: [
        'repositoryFactory',
        'configService',
        'acl',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    data(){
        return {
            productLaunchData: null,
            isLoading: false,
            isSaveSuccessful: false,
            config: null,
            salesChannels: [],
            mailTemplateOptions: [],
            mailTemplateIdError: null,
            frquency: []
        }
    },

    computed: {
        ...mapPropertyErrors('productLaunchData', ['mailTemplateOptions']),

        isTitleRequired() {
            return Shopware.State.getters['context/isSystemDefaultLanguage'];
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },
        systemConfigRepository() {
            return this.repositoryFactory.create('system_config');
        },
        mailTemplateRepository() {
            return this.repositoryFactory.create('mail_template');
        },
    },

    created() {
        this.createdComponent();
        this.getMailTemplates();
        this.repository = this.repositoryFactory.create('scheduled_task');
        this.getLaunchProductSheduled();
    },

    methods: {
        getLaunchProductSheduled() {
            const customCriteria = new Criteria();
            customCriteria.addFilter(Criteria.equals('name', 'launch_new_product'));
            this.repository.search(customCriteria, Shopware.Context.api)
                .then((entity) => {
                    this.frquency = entity[0];
                });
        },
        createdComponent() {
            this.isLoading = true;
            const criteria = new Criteria();
            criteria.addFilter(
                Criteria.equalsAny('typeId', [
                    Defaults.storefrontSalesChannelTypeId,
                    Defaults.apiSalesChannelTypeId
                ])
            );

            this.salesChannelRepository.search(criteria, Shopware.Context.api).then(res => {
                res.add({
                    id: null,
                    translated: {
                        name: this.$tc('sw-sales-channel-switch.labelDefaultOption')
                    }
                });
                this.salesChannels = res;
            }).finally(() => {
                this.isLoading = false;
            });
        },

        checkTextFieldInheritance(value) {
            if (typeof value !== 'string') {
                return true;
            }

            return value.length <= 0;
        },

        checkBoolFieldInheritance(value) {
            return typeof value !== 'boolean';
        },

        onSave() {
            const selectedSalesChannelId = this.$refs.configComponent.selectedSalesChannelId;
            if (!this.$refs.configComponent.allConfigs[selectedSalesChannelId]['productLaunch.settings.mailTemplate']) {
                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: this.$tc(
                        'genius-product-launch-configuration.save.errorTitleSalesChannel'
                    )
                });

                return;
            }
            this.isLoading = true;
            const updatePromises = [];

            console.log(this.$refs.configComponent.allConfigs.null['productLaunch.settings.mailTemplate']);
            this.$refs.configComponent.save(this.systemConfigRepository, Shopware.Context.api).then(() => {
                this.isSaveSuccessful = true;
                this.isLoading = false;
            }).catch((e) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: this.$tc(
                        'genius-product-launch-configuration.save.errorTitle'
                    )
                });
            });
            updatePromises.push(this.repository.save(this.frquency).then(() => {
                Promise.all(updatePromises).then(() => {
                    this.createNotificationSuccess({
                        message: this.$tc('genius-product-launch-configuration.save.success'),
                    })
                    this.isLoading = false;
                });
            }).catch((e) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: this.$tc(
                        'genius-product-launch-configuration.save.errorTitle'
                    )
                });
            }));
        },

        getMailTemplates() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('systemDefault',false));
            criteria.addAssociation('mailTemplateType.translations');
            this.mailTemplateRepository.search(criteria, Shopware.Context.api)
                .then((entity) => {
                        entity.forEach((translatedName) => {
                            if(translatedName.mailTemplateType){
                                this.mailTemplateOptions.push(translatedName.mailTemplateType);
                            }
                        });
                        return this.mailTemplateOptions;
                });
        },
    }
});
