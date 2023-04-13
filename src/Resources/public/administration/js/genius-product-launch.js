!function(e){var n={};function t(i){if(n[i])return n[i].exports;var r=n[i]={i:i,l:!1,exports:{}};return e[i].call(r.exports,r,r.exports,t),r.l=!0,r.exports}t.m=e,t.c=n,t.d=function(e,n,i){t.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:i})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,n){if(1&n&&(e=t(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(t.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var r in e)t.d(i,r,function(n){return e[n]}.bind(null,r));return i},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},t.p="/bundles/geniusproductlaunch/",t(t.s="BCdf")}({"80w7":function(e,n,t){},BCdf:function(e,n,t){"use strict";t.r(n);var i=Shopware,r=i.Component,a=i.Mixin;r.register("search-wizzy-list",{template:'{% block search_wizzy_list_page %}\n    <sw-page class="search-wizzy-list">\n        {#Cron Button#}\n        <template slot="content" >\n            <sw-card class="sw-settings-shipping-detail__condition_container">\n                <div class="collection-container">\n                    <div style="width:100%;"><h1>{{ $t(\'search-wizzy.list.importProductBtnTitle\') }}</h1></div>\n                    <sw-button variant="primary" @click="releaseProduct">\n                        {{ $t(\'search-wizzy.list.ProductImportCronBtnLabel\') }}\n                    </sw-button>\n                </div>\n            </sw-card>\n        </template>\n    </sw-page>\n{% endblock %}\n',inject:["configService","repositoryFactory"],mixins:[a.getByName("notification")],methods:{importProduct:function(){var e=this,n=this.configService.getBasicHeaders();return this.configService.httpClient.get("/search-wizzy/productimport",{headers:n}).then((function(n){"successs"===n.data.type?e.createNotificationError({title:n.data.type,message:n.data.message}):e.createNotificationSuccess({title:n.data.type,message:n.data.message})}))},releaseProduct:function(){var e=this.configService.getBasicHeaders();return this.configService.httpClient.get("/search-wizzy/releaseProduct",{headers:e})}}});var o=t("Kn3+"),c=t("ZtlY");Shopware.Module.register("search-wizzy",{type:"plugin",name:"search-wizzy.general.mainMenuItemGeneral",title:"search-wizzy.general.mainMenuItemGeneral",description:"search-wizzy.general.descriptionTextModule",color:"#ff3d58",icon:"default-action-cloud-download",snippets:{"de-DE":o,"en-GB":c},routes:{list:{component:"search-wizzy-list",path:"list"}},navigation:[{label:"search-wizzy.general.mainMenuItemGeneral",color:"#ff3d58",path:"search.wizzy.list",parent:"sw-order",icon:"default-shopping-paper-bag-product",position:100}]});t("s29/");Shopware.Component.register("genius-product-configuration-icon",{template:'{% block genius_product_configuration_icon %}\n    <img class="genius-product-configuration-icon__gif" alt="genius_product_icon" :src="\'/geniusproductlaunch/static/img/plugin.png\' | asset">\n{% endblock %}\n'});function s(e){return s="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},s(e)}function l(e,n){var t=Object.keys(e);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);n&&(i=i.filter((function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable}))),t.push.apply(t,i)}return t}function u(e){for(var n=1;n<arguments.length;n++){var t=null!=arguments[n]?arguments[n]:{};n%2?l(Object(t),!0).forEach((function(n){p(e,n,t[n])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(t)):l(Object(t)).forEach((function(n){Object.defineProperty(e,n,Object.getOwnPropertyDescriptor(t,n))}))}return e}function p(e,n,t){return(n=function(e){var n=function(e,n){if("object"!==s(e)||null===e)return e;var t=e[Symbol.toPrimitive];if(void 0!==t){var i=t.call(e,n||"default");if("object"!==s(i))return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===n?String:Number)(e)}(e,"string");return"symbol"===s(n)?n:String(n)}(n))in e?Object.defineProperty(e,n,{value:t,enumerable:!0,configurable:!0,writable:!0}):e[n]=t,e}var d=Shopware,h=d.Component,f=d.Mixin,g=d.Defaults,m=Shopware.Data.Criteria,y=Shopware.Component.getComponentHelper().mapPropertyErrors;h.register("genius-product-launch-configuration",{template:'{% block genius_product_launch_configuration %}\n    <sw-page class="genius-product-launch-configuration">\n\n        {% block genius_product_launch_configuration_header %}\n            <template #smart-bar-header>\n                <h2>\n                    {{ $tc(\'genius-product-launch-configuration.header\') }}\n                </h2>\n            </template>\n        {% endblock %}\n\n        {% block genius_product_launch_configuration_actions %}\n            <template #smart-bar-actions>\n                {% block genius_product_launch_configuration_actions_save %}\n                    <sw-button-process v-model="isSaveSuccessful"\n                                       class="sw-settings-login-registration__save-action"\n                                       variant="primary"\n                                       :isLoading="isLoading"\n                                       :disabled="isLoading || !acl.can(\'productLaunch.editor\')"\n                                       @click="onSave">\n                        {{ $tc(\'global.default.save\') }}\n                    </sw-button-process>\n                {% endblock %}\n            </template>\n        {% endblock %}\n\n        {% block genius_product_launch_configuration_content %}\n            <template #content>\n                {% block genius_product_launch_configuration_content_card %}\n                    <sw-card-view>\n                        {% block genius_product_launch_configuration_content_card_channel_config %}\n                            <sw-sales-channel-config v-model="config"\n                                                     ref="configComponent"\n                                                     domain="productLaunch.settings">\n\n                                {% block genius_product_launch_configuration_content_card_channel_config_sales_channel %}\n                                    <template #select="{ onInput, selectedSalesChannelId }">\n                                        <sw-card :title="$tc(\'global.entities.sales_channel\', 2)">\n                                            {% block genius_product_launch_configuration_content_card_channel_config_sales_channel_card_title %}\n                                                <sw-single-select v-model="selectedSalesChannelId"\n                                                                  labelProperty="translated.name"\n                                                                  valueProperty="id"\n                                                                  :isLoading="isLoading"\n                                                                  :options="salesChannels"\n                                                                  :disabled="!acl.can(\'productLaunch.editor\')"\n                                                                  @change="onInput">\n                                                </sw-single-select>\n                                            {% endblock %}\n                                        </sw-card>\n                                </template>\n                                {% endblock %}\n\n                                {% block genius_product_launch_configuration_content_card_channel_config_cards %}\n                                    <template #content="{ actualConfigData, allConfigs, selectedSalesChannelId }">\n                                        <div v-if="actualConfigData">\n                                            <sw-card :title="$tc(\'genius-product-launch-configuration.card.selectActivate\')">\n                                                <sw-container>\n                                                    <div class="switch-field">\n                                                        <sw-inherit-wrapper v-model="actualConfigData[\'productLaunch.settings.active\']"\n                                                                            :inheritedValue="selectedSalesChannelId == null ? null : allConfigs[\'null\'][\'productLaunch.settings.active\']"\n                                                                            :customInheritationCheckFunction="checkTextFieldInheritance">\n                                                            <template #content="props">\n                                                                <sw-switch-field name="productLaunch.settings.active"\n                                                                                 :value="props.currentValue"\n                                                                                 :label="$tc(\'genius-product-launch-configuration.card.active\')"\n                                                                                 @change="props.updateCurrentValue">\n                                                                </sw-switch-field>\n                                                            </template>\n                                                        </sw-inherit-wrapper>\n                                                    </div>\n\n                                                    <div class="select-field">\n                                                        <sw-inherit-wrapper v-model="actualConfigData[\'productLaunch.settings.mailTemplate\']"\n                                                                            :inheritedValue="selectedSalesChannelId == null ? null : allConfigs[\'null\'][\'productLaunch.settings.mailTemplate\']"\n                                                                            :customInheritationCheckFunction="checkTextFieldInheritance"\n                                                                            :label="$tc(\'genius-product-launch-configuration.card.mailTemplate\')">\n                                                            <template #content="props">\n{#                                                                <sw-single-select name="productLaunch.settings.mailTemplate"#}\n{#                                                                                  :options="mailTemplateOptions"#}\n{#                                                                                  labelProperty="name"#}\n{#                                                                                  valueProperty="name"#}\n{#                                                                                  :isInherited="props.isInherited"#}\n{#                                                                                  :value="props.currentValue"#}\n{#                                                                                  @change="props.updateCurrentValue"#}\n{#                                                                                  :disabled="props.isInherited || !acl.can(\'productLaunch.editor\')">#}\n{#                                                                </sw-single-select>#}\n\n                                                                <sw-single-select\n                                                                    :options="mailTemplateOptions"\n                                                                    :required="isTitleRequired"\n                                                                    :error="mailTemplateIdError"\n                                                                    labelProperty="name"\n                                                                    valueProperty="name"\n                                                                    :isInherited="props.isInherited"\n                                                                    :value="props.currentValue"\n                                                                    @change="props.updateCurrentValue"\n                                                                    show-clearable-button\n                                                                />\n\n                                                            </template>\n                                                        </sw-inherit-wrapper>\n                                                    </div>\n\n                                                    <div class="select-field">\n                                                        <sw-inherit-wrapper v-model="actualConfigData[\'productLaunch.settings.mailTemplate\']"\n                                                                            :inheritedValue="selectedSalesChannelId == null ? null : allConfigs[\'null\'][\'productLaunch.settings.mailTemplate\']"\n                                                                            :customInheritationCheckFunction="checkTextFieldInheritance"\n                                                                            :label="$tc(\'genius-product-launch-configuration.card.cronTime\')">\n                                                            <template #content="props">\n                                                                <sw-datepicker dateType="datetime-local"\n                                                                               v-model="frquency.nextExecutionTime">\n                                                                </sw-datepicker>\n                                                            </template>\n                                                        </sw-inherit-wrapper>\n                                                    </div>\n                                                </sw-container>\n                                            </sw-card>\n                                        </div>\n                                    </template>\n                                {% endblock %}\n                            </sw-sales-channel-config>\n                        {% endblock %}\n                    </sw-card-view>\n                {% endblock %}\n            </template>\n        {% endblock %}\n\n    </sw-page>\n{% endblock %}\n',inject:["repositoryFactory","configService","acl"],mixins:[f.getByName("notification")],data:function(){return{productLaunchData:null,isLoading:!1,isSaveSuccessful:!1,config:null,salesChannels:[],mailTemplateOptions:[],mailTemplateIdError:null,frquency:[]}},computed:u(u({},y("productLaunchData",["mailTemplateOptions"])),{},{isTitleRequired:function(){return Shopware.State.getters["context/isSystemDefaultLanguage"]},salesChannelRepository:function(){return this.repositoryFactory.create("sales_channel")},systemConfigRepository:function(){return this.repositoryFactory.create("system_config")},mailTemplateRepository:function(){return this.repositoryFactory.create("mail_template")}}),created:function(){this.createdComponent(),this.getMailTemplates(),this.repository=this.repositoryFactory.create("scheduled_task"),this.getLaunchProductSheduled()},methods:{getLaunchProductSheduled:function(){var e=this,n=new m;n.addFilter(m.equals("name","launch_new_product")),this.repository.search(n,Shopware.Context.api).then((function(n){e.frquency=n[0]}))},createdComponent:function(){var e=this;this.isLoading=!0;var n=new m;n.addFilter(m.equalsAny("typeId",[g.storefrontSalesChannelTypeId,g.apiSalesChannelTypeId])),this.salesChannelRepository.search(n,Shopware.Context.api).then((function(n){n.add({id:null,translated:{name:e.$tc("sw-sales-channel-switch.labelDefaultOption")}}),e.salesChannels=n})).finally((function(){e.isLoading=!1}))},checkTextFieldInheritance:function(e){return"string"!=typeof e||e.length<=0},checkBoolFieldInheritance:function(e){return"boolean"!=typeof e},onSave:function(){var e=this;this.isLoading=!0;var n=[];console.log(this.$refs.configComponent),this.$refs.configComponent.save(this.systemConfigRepository,Shopware.Context.api).then((function(){e.isSaveSuccessful=!0,e.isLoading=!1})).catch((function(){e.isLoading=!1,e.createNotificationError({title:e.$tc("global.default.error"),message:e.$tc("genius-product-launch-configuration.save.errorTitle")})})),n.push(this.repository.save(this.frquency).then((function(){console.log("updatePromises",e.frquency),Promise.all(n).then((function(){e.createNotificationSuccess({message:e.$tc("genius-product-launch-configuration.save.success")}),e.isLoading=!1}))})).catch((function(){e.isLoading=!1,e.createNotificationError({title:e.$tc("global.default.error"),message:e.$tc("genius-product-launch-configuration.save.errorTitle")})})))},getMailTemplates:function(){var e=this,n=new m;n.addFilter(m.equals("systemDefault",!1)),n.addAssociation("mailTemplateType.translations"),this.mailTemplateRepository.search(n,Shopware.Context.api).then((function(n){return n.forEach((function(n){n.mailTemplateType&&e.mailTemplateOptions.push(n.mailTemplateType)})),e.mailTemplateOptions}))}}});var v=t("rJzz"),b=t("HoJX");Shopware.Module.register("genius-product-launch-configuration",{type:"plugin",name:"Genius Product Launch Configuration",title:"genius-product-launch-configuration.general.mainMenuItemGeneral",description:"genius-product-launch-configuration.general.descriptionTextModule",color:"#ff3d58",icon:"default-action-settings",snippets:{"de-DE":v,"en-GB":b},routes:{index:{component:"genius-product-launch-configuration",path:"index",meta:{parentPath:"sw.settings.index"}}},settingsItem:{group:"plugins",to:"genius.product.launch.configuration.index",iconComponent:"genius-product-configuration-icon",backgroundEnabled:!0}})},HoJX:function(e){e.exports=JSON.parse('{"genius-product-launch-configuration":{"header":"Product Launch","card":{"selectTemplate":"Select MailTemplate","selectTime":"Select Time","mailTemplate":"Mail Template","cronTime":"Select time for set cron","messageSaveSuccess":"Details saved successfully","selectActivate":"Select General Configurations","active":"Active"},"frequency":{"labelLastExecutionTime":"Last Execution"},"save":{"errorTitle":"Configuration could not save","errorTitleSalesChannel":"Please Select MailTemplate","success":"Configuration is saved"},"general":{"mainMenuItemGeneral":"GeniusProductLaunch","descriptionTextModule":"GeniusProductLaunch settings"}}}')},"Kn3+":function(e){e.exports=JSON.parse('{"search-wizzy":{"general":{"mainMenuItemGeneral":"Search Wizzy","descriptionTextModule":"Display search wizzy list"},"list":{"importProductBtnTitle":"Import all products","importProductBtnLabel":"Import Wizzy","ProductImportCronBtnLabel":"Product Import Cron"}}}')},SZ7m:function(e,n,t){"use strict";function i(e,n){for(var t=[],i={},r=0;r<n.length;r++){var a=n[r],o=a[0],c={id:e+":"+r,css:a[1],media:a[2],sourceMap:a[3]};i[o]?i[o].parts.push(c):t.push(i[o]={id:o,parts:[c]})}return t}t.r(n),t.d(n,"default",(function(){return f}));var r="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!r)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var a={},o=r&&(document.head||document.getElementsByTagName("head")[0]),c=null,s=0,l=!1,u=function(){},p=null,d="data-vue-ssr-id",h="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function f(e,n,t,r){l=t,p=r||{};var o=i(e,n);return g(o),function(n){for(var t=[],r=0;r<o.length;r++){var c=o[r];(s=a[c.id]).refs--,t.push(s)}n?g(o=i(e,n)):o=[];for(r=0;r<t.length;r++){var s;if(0===(s=t[r]).refs){for(var l=0;l<s.parts.length;l++)s.parts[l]();delete a[s.id]}}}}function g(e){for(var n=0;n<e.length;n++){var t=e[n],i=a[t.id];if(i){i.refs++;for(var r=0;r<i.parts.length;r++)i.parts[r](t.parts[r]);for(;r<t.parts.length;r++)i.parts.push(y(t.parts[r]));i.parts.length>t.parts.length&&(i.parts.length=t.parts.length)}else{var o=[];for(r=0;r<t.parts.length;r++)o.push(y(t.parts[r]));a[t.id]={id:t.id,refs:1,parts:o}}}}function m(){var e=document.createElement("style");return e.type="text/css",o.appendChild(e),e}function y(e){var n,t,i=document.querySelector("style["+d+'~="'+e.id+'"]');if(i){if(l)return u;i.parentNode.removeChild(i)}if(h){var r=s++;i=c||(c=m()),n=w.bind(null,i,r,!1),t=w.bind(null,i,r,!0)}else i=m(),n=_.bind(null,i),t=function(){i.parentNode.removeChild(i)};return n(e),function(i){if(i){if(i.css===e.css&&i.media===e.media&&i.sourceMap===e.sourceMap)return;n(e=i)}else t()}}var v,b=(v=[],function(e,n){return v[e]=n,v.filter(Boolean).join("\n")});function w(e,n,t,i){var r=t?"":i.css;if(e.styleSheet)e.styleSheet.cssText=b(n,r);else{var a=document.createTextNode(r),o=e.childNodes;o[n]&&e.removeChild(o[n]),o.length?e.insertBefore(a,o[n]):e.appendChild(a)}}function _(e,n){var t=n.css,i=n.media,r=n.sourceMap;if(i&&e.setAttribute("media",i),p.ssrId&&e.setAttribute(d,n.id),r&&(t+="\n/*# sourceURL="+r.sources[0]+" */",t+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(r))))+" */"),e.styleSheet)e.styleSheet.cssText=t;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(t))}}},ZtlY:function(e){e.exports=JSON.parse('{"search-wizzy":{"general":{"mainMenuItemGeneral":"Update Product Price","descriptionTextModule":"Display search wizzy list"},"list":{"importProductBtnTitle":"Import all products","importProductBtnLabel":"Import Wizzy","ProductImportCronBtnLabel":"Product Import Cron"}}}')},rJzz:function(e){e.exports=JSON.parse('{"genius-product-launch-configuration":{"header":"Produkteinführung","card":{"selectTemplate":"MailTemplate auswählen","selectTime":"Zeit auswählen","mailTemplate":"Mail-Vorlage","cronTime":"Zeit für Set-Cron auswählen","messageSaveSuccess":"Details erfolgreich gespeichert","selectActivate":"Allgemeine Konfigurationen auswählen","active":"Aktiv"},"frequency":{"labelLastExecutionTime":"Letzte Hinrichtung"},"save":{"errorTitle":"Konfiguration konnte nicht gespeichert werden","errorTitleSalesChannel":"Bitte E-Mail-Vorlage auswählen","success":"onfiguration wird gespeichert"},"general":{"mainMenuItemGeneral":"Produkteinführung","descriptionTextModule":"Einstellungen Produkteinführung"}}}')},"s29/":function(e,n,t){var i=t("80w7");i.__esModule&&(i=i.default),"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);(0,t("SZ7m").default)("6b08bf97",i,!0,{})}});