!function(e){var n={};function t(a){if(n[a])return n[a].exports;var i=n[a]={i:a,l:!1,exports:{}};return e[a].call(i.exports,i,i.exports,t),i.l=!0,i.exports}t.m=e,t.c=n,t.d=function(e,n,a){t.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:a})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,n){if(1&n&&(e=t(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(t.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var i in e)t.d(a,i,function(n){return e[n]}.bind(null,i));return a},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},t.p="/bundles/geniusproductlaunch/",t(t.s="BCdf")}({"80w7":function(e,n,t){},BCdf:function(e,n,t){"use strict";t.r(n);var a=Shopware,i=a.Component,r=a.Mixin;Shopware.Data.Criteria;i.register("search-wizzy-list",{template:'{% block search_wizzy_list_page %}\n    <sw-page class="search-wizzy-list">\n        {#Cron Button#}\n        <template slot="content" >\n            <sw-card class="sw-settings-shipping-detail__condition_container">\n                <div class="collection-container">\n                    <div style="width:100%;"><h1>{{ $t(\'search-wizzy.list.importProductBtnTitle\') }}</h1></div>\n                    <sw-button variant="primary" @click="releaseProduct">\n                        {{ $t(\'search-wizzy.list.ProductImportCronBtnLabel\') }}\n                    </sw-button>\n                </div>\n            </sw-card>\n        </template>\n    </sw-page>\n{% endblock %}\n',inject:["configService","repositoryFactory"],mixins:[r.getByName("notification")],methods:{importProduct:function(){var e=this,n=this.configService.getBasicHeaders();return this.configService.httpClient.get("/search-wizzy/productimport",{headers:n}).then((function(n){"error"!==n.data.type?"success"===n.data.type&&e.createNotificationError({title:n.data.type,message:"successfully called"}):e.createNotificationError({title:n.data.type,message:"error stop"})}))},releaseProduct:function(){var e=this.configService.getBasicHeaders();return this.configService.httpClient.get("/search-wizzy/releaseProduct",{headers:e})}}});var o=t("Kn3+"),c=t("ZtlY");Shopware.Module.register("search-wizzy",{type:"plugin",name:"search-wizzy.general.mainMenuItemGeneral",title:"search-wizzy.general.mainMenuItemGeneral",description:"search-wizzy.general.descriptionTextModule",color:"#ff3d58",icon:"default-action-cloud-download",snippets:{"de-DE":o,"en-GB":c},routes:{list:{component:"search-wizzy-list",path:"list"}},navigation:[{label:"search-wizzy.general.mainMenuItemGeneral",color:"#ff3d58",path:"search.wizzy.list",parent:"sw-catalogue",icon:"default-shopping-paper-bag-product",position:100}]});t("s29/");Shopware.Component.register("genius-product-configuration-icon",{template:'{% block genius_product_configuration_icon %}\n    <img class="genius-product-configuration-icon__gif" alt="genius_product_icon" :src="\'/geniusproductlaunch/static/img/plugin.png\' | asset">\n{% endblock %}\n'});var s=Shopware,l=s.Component,u=s.Mixin,d=s.Defaults,p=Shopware.Data.Criteria;l.register("genius-product-launch-configuration",{template:'{% block genius_product_launch_configuration %}\n    <sw-page class="genius-product-launch-configuration">\n\n        {% block genius_product_launch_configuration_header %}\n            <template #smart-bar-header>\n                <h2>\n                    {{ $tc(\'sw-settings.index.title\') }}\n                    <sw-icon name="small-arrow-medium-right" small></sw-icon>\n                    {{ $tc(\'genius-product-launch-configuration.header\') }}\n                </h2>\n            </template>\n        {% endblock %}\n\n        {% block genius_product_launch_configuration_actions %}\n            <template #smart-bar-actions>\n                {% block genius_product_launch_configuration_actions_save %}\n                    <sw-button-process v-model="isSaveSuccessful"\n                                       class="sw-settings-login-registration__save-action"\n                                       variant="primary"\n                                       :isLoading="isLoading"\n                                       :disabled="isLoading || !acl.can(\'productLaunch.editor\')"\n                                       @click="onSave">\n                        {{ $tc(\'global.default.save\') }}\n                    </sw-button-process>\n                {% endblock %}\n            </template>\n        {% endblock %}\n\n        {% block genius_product_launch_configuration_content %}\n            <template #content>\n                {% block genius_product_launch_configuration_content_card %}\n                    <sw-card-view>\n                        {% block genius_product_launch_configuration_content_card_channel_config %}\n                            <sw-sales-channel-config v-model="config"\n                                                     ref="configComponent"\n                                                     domain="productLaunch.settings">\n\n                                {% block genius_product_launch_configuration_content_card_channel_config_sales_channel %}\n                                <template #select="{ onInput, selectedSalesChannelId }">\n                                    <sw-card :title="$tc(\'global.entities.sales_channel\', 2)">\n                                        {% block genius_product_launch_configuration_content_card_channel_config_sales_channel_card_title %}\n                                            <sw-single-select v-model="selectedSalesChannelId"\n                                                              labelProperty="translated.name"\n                                                              valueProperty="id"\n                                                              :isLoading="isLoading"\n                                                              :options="salesChannels"\n                                                              :disabled="!acl.can(\'productLaunch.editor\')"\n                                                              @change="onInput">\n                                            </sw-single-select>\n                                        {% endblock %}\n                                    </sw-card>\n                                </template>\n                                {% endblock %}\n\n                                {% block genius_product_launch_configuration_content_card_channel_config_cards %}\n                                    <template #content="{ actualConfigData, allConfigs, selectedSalesChannelId }">\n                                        <div v-if="actualConfigData">\n                                            <sw-card :title="$tc(\'genius-product-launch-configuration.card.selectActivate\')">\n                                                <sw-container>\n                                                    <div class="switch-field">\n                                                        <sw-inherit-wrapper v-model="actualConfigData[\'productLaunch.settings.active\']"\n                                                                            :inheritedValue="selectedSalesChannelId == null ? null : allConfigs[\'null\'][\'productLaunch.settings.active\']"\n                                                                            :customInheritationCheckFunction="checkTextFieldInheritance">\n                                                            <template #content="props">\n                                                                <sw-switch-field name="productLaunch.settings.active"\n                                                                                 :value="props.currentValue"\n                                                                                 :label="$tc(\'genius-product-launch-configuration.card.active\')"\n                                                                                 @change="props.updateCurrentValue">\n                                                                </sw-switch-field>\n                                                            </template>\n                                                        </sw-inherit-wrapper>\n                                                    </div>\n\n                                                    <div class="select-field">\n                                                        <sw-inherit-wrapper v-model="actualConfigData[\'productLaunch.settings.mailTemplate\']"\n                                                                            :inheritedValue="selectedSalesChannelId == null ? null : allConfigs[\'null\'][\'productLaunch.settings.mailTemplate\']"\n                                                                            :customInheritationCheckFunction="checkTextFieldInheritance"\n                                                                            :label="$tc(\'genius-product-launch-configuration.card.mailTemplate\')">\n                                                            <template #content="props">\n                                                                <sw-single-select name="productLaunch.settings.mailTemplate"\n                                                                                  :options="mailTemplateOptions"\n                                                                                  labelProperty="name"\n                                                                                  valueProperty="name"\n                                                                                  :isInherited="props.isInherited"\n                                                                                  :value="props.currentValue"\n                                                                                  @change="props.updateCurrentValue"\n                                                                                  :disabled="props.isInherited || !acl.can(\'productLaunch.editor\')">\n                                                                </sw-single-select>\n\n                                                            </template>\n                                                        </sw-inherit-wrapper>\n                                                    </div>\n\n                                                    <div class="select-field">\n                                                        <sw-inherit-wrapper v-model="actualConfigData[\'productLaunch.settings.mailTemplate\']"\n                                                                            :inheritedValue="selectedSalesChannelId == null ? null : allConfigs[\'null\'][\'productLaunch.settings.mailTemplate\']"\n                                                                            :customInheritationCheckFunction="checkTextFieldInheritance"\n                                                                            :label="$tc(\'genius-product-launch-configuration.card.cronTime\')">\n                                                            <template #content="props">\n                                                                <sw-datepicker dateType="datetime-local"\n                                                                               v-model="frquency.nextExecutionTime">\n                                                                </sw-datepicker>\n                                                            </template>\n                                                        </sw-inherit-wrapper>\n                                                    </div>\n                                                </sw-container>\n                                            </sw-card>\n                                        </div>\n                                    </template>\n                                {% endblock %}\n\n                            </sw-sales-channel-config>\n                        {% endblock %}\n                    </sw-card-view>\n                {% endblock %}\n            </template>\n        {% endblock %}\n\n    </sw-page>\n{% endblock %}\n',inject:["repositoryFactory","configService","acl"],mixins:[u.getByName("notification")],data:function(){return{isLoading:!1,isSaveSuccessful:!1,config:null,salesChannels:[],mailTemplateOptions:[],frquency:[]}},computed:{salesChannelRepository:function(){return this.repositoryFactory.create("sales_channel")},mailTemplateRepository:function(){return this.repositoryFactory.create("mail_template")}},created:function(){this.createdComponent(),this.getMailTemplates(),this.repository=this.repositoryFactory.create("scheduled_task"),this.getLaunchProductSheduled()},methods:{getLaunchProductSheduled:function(){var e=this,n=new p;n.addFilter(p.equals("name","launch_new_product")),this.repository.search(n,Shopware.Context.api).then((function(n){e.frquency=n[0]}))},createdComponent:function(){var e=this;this.isLoading=!0;var n=new p;n.addFilter(p.equalsAny("typeId",[d.storefrontSalesChannelTypeId,d.apiSalesChannelTypeId])),this.salesChannelRepository.search(n,Shopware.Context.api).then((function(n){n.add({id:null,translated:{name:e.$tc("sw-sales-channel-switch.labelDefaultOption")}}),e.salesChannels=n})).finally((function(){e.isLoading=!1}))},checkTextFieldInheritance:function(e){return"string"!=typeof e||e.length<=0},checkBoolFieldInheritance:function(e){return"boolean"!=typeof e},onSave:function(){var e=this;this.isLoading=!0;var n=[];console.log(this.$refs.configComponent),this.$refs.configComponent.save().then((function(){e.isSaveSuccessful=!0,e.isLoading=!1})),n.push(this.repository.save(this.frquency).then((function(){Promise.all(n).then((function(){e.createNotificationSuccess({message:e.$tc("genius-product-launch-configuration.save.success")}),e.isLoading=!1}))})).catch((function(n){e.isLoading=!1,e.createNotificationError({title:e.$tc("global.default.error"),message:e.$tc("genius-product-launch-configuration.save.errorTitle")})})))},getMailTemplates:function(){var e=this,n=new p;n.addFilter(p.equals("systemDefault",!1)),n.addAssociation("mailTemplateType.translations"),this.mailTemplateRepository.search(n,Shopware.Context.api).then((function(n){return n.forEach((function(n){n.mailTemplateType&&e.mailTemplateOptions.push(n.mailTemplateType)})),e.mailTemplateOptions}))}}});var h=t("rJzz"),g=t("HoJX");Shopware.Module.register("genius-product-launch-configuration",{type:"plugin",name:"Genius Product Launch Configuration",title:"genius-product-launch-configuration.general.mainMenuItemGeneral",description:"genius-product-launch-configuration.general.descriptionTextModule",color:"#ff3d58",icon:"default-action-settings",snippets:{"de-DE":h,"en-GB":g},routes:{index:{component:"genius-product-launch-configuration",path:"index",meta:{parentPath:"sw.settings.index"}}},settingsItem:{group:"plugins",to:"genius.product.launch.configuration.index",iconComponent:"genius-product-configuration-icon",backgroundEnabled:!0}})},HoJX:function(e){e.exports=JSON.parse('{"genius-product-launch-configuration":{"header":"GeniusProductLaunch","card":{"selectTemplate":"Select MailTemplate","selectTime":"Select Time","mailTemplate":"MailTemplate","cronTime":"Select time for set cron","messageSaveSuccess":"Details saved successfully","selectActivate":"Select general configrations","active":"Active"},"frequency":{"labelLastExecutionTime":"Last Execution"},"save":{"errorTitle":"Configuration could not save","success":"Configuration is saved"},"general":{"mainMenuItemGeneral":"GeniusProductLaunch","descriptionTextModule":"GeniusProductLaunch settings"}}}')},"Kn3+":function(e){e.exports=JSON.parse('{"search-wizzy":{"general":{"mainMenuItemGeneral":"Search Wizzy","descriptionTextModule":"Display search wizzy list"},"list":{"importProductBtnTitle":"Import all products","importProductBtnLabel":"Import Wizzy"}}}')},SZ7m:function(e,n,t){"use strict";function a(e,n){for(var t=[],a={},i=0;i<n.length;i++){var r=n[i],o=r[0],c={id:e+":"+i,css:r[1],media:r[2],sourceMap:r[3]};a[o]?a[o].parts.push(c):t.push(a[o]={id:o,parts:[c]})}return t}t.r(n),t.d(n,"default",(function(){return g}));var i="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!i)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var r={},o=i&&(document.head||document.getElementsByTagName("head")[0]),c=null,s=0,l=!1,u=function(){},d=null,p="data-vue-ssr-id",h="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function g(e,n,t,i){l=t,d=i||{};var o=a(e,n);return f(o),function(n){for(var t=[],i=0;i<o.length;i++){var c=o[i];(s=r[c.id]).refs--,t.push(s)}n?f(o=a(e,n)):o=[];for(i=0;i<t.length;i++){var s;if(0===(s=t[i]).refs){for(var l=0;l<s.parts.length;l++)s.parts[l]();delete r[s.id]}}}}function f(e){for(var n=0;n<e.length;n++){var t=e[n],a=r[t.id];if(a){a.refs++;for(var i=0;i<a.parts.length;i++)a.parts[i](t.parts[i]);for(;i<t.parts.length;i++)a.parts.push(v(t.parts[i]));a.parts.length>t.parts.length&&(a.parts.length=t.parts.length)}else{var o=[];for(i=0;i<t.parts.length;i++)o.push(v(t.parts[i]));r[t.id]={id:t.id,refs:1,parts:o}}}}function m(){var e=document.createElement("style");return e.type="text/css",o.appendChild(e),e}function v(e){var n,t,a=document.querySelector("style["+p+'~="'+e.id+'"]');if(a){if(l)return u;a.parentNode.removeChild(a)}if(h){var i=s++;a=c||(c=m()),n=_.bind(null,a,i,!1),t=_.bind(null,a,i,!0)}else a=m(),n=b.bind(null,a),t=function(){a.parentNode.removeChild(a)};return n(e),function(a){if(a){if(a.css===e.css&&a.media===e.media&&a.sourceMap===e.sourceMap)return;n(e=a)}else t()}}var y,w=(y=[],function(e,n){return y[e]=n,y.filter(Boolean).join("\n")});function _(e,n,t,a){var i=t?"":a.css;if(e.styleSheet)e.styleSheet.cssText=w(n,i);else{var r=document.createTextNode(i),o=e.childNodes;o[n]&&e.removeChild(o[n]),o.length?e.insertBefore(r,o[n]):e.appendChild(r)}}function b(e,n){var t=n.css,a=n.media,i=n.sourceMap;if(a&&e.setAttribute("media",a),d.ssrId&&e.setAttribute(p,n.id),i&&(t+="\n/*# sourceURL="+i.sources[0]+" */",t+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(i))))+" */"),e.styleSheet)e.styleSheet.cssText=t;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(t))}}},ZtlY:function(e){e.exports=JSON.parse('{"search-wizzy":{"general":{"mainMenuItemGeneral":"Update Product Price","descriptionTextModule":"Display search wizzy list"},"list":{"importProductBtnTitle":"Import all products","importProductBtnLabel":"Import Wizzy","ProductImportCronBtnLabel":"Product Import Cron"}}}')},rJzz:function(e){e.exports=JSON.parse('{"genius-product-launch-configuration":{"header":"GeniusProductLaunch","card":{"selectTemplate":"Select MailTemplate","titleSaveSuccess":"Success","messageSaveSuccess":"Details saved successfully"},"general":{"mainMenuItemGeneral":"GeniusProductLaunch","descriptionTextModule":"GeniusProductLaunch settings"}}}')},"s29/":function(e,n,t){var a=t("80w7");a.__esModule&&(a=a.default),"string"==typeof a&&(a=[[e.i,a,""]]),a.locals&&(e.exports=a.locals);(0,t("SZ7m").default)("6b08bf97",a,!0,{})}});