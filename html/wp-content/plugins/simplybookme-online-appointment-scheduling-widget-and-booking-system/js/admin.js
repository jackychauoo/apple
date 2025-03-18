
var SimplybookAdminInterface = function (options) {
    this.init(options);
};


jQuery.extend(SimplybookAdminInterface.prototype, {

    options : {
        login : 'simplydemo',
        apiUrl : 'https://user-api.simplybook.me/public',
        //apiUrl : 'http://user-api.dev.simplybook.me/public',
        //apiUrl : 'http://user-api.em.vetal.notando.com.ua/public',
        node : '#simplybook-page-container',
        template : 'default',
        themeparams : null,
    },

    api : null,
    apiReady : false,
    themes : null,
    timelines : null,
    domain : 'simplydemo.simplybook.me',

    node : null,
    errTimer : null,
    themeSettingsTemplate : null,
    ingoreSettingsKeys : ['main_page_mode'],
    sortSettingsTypes : ["select", "color", "base_color", "checkbox", "text"],

    init : function (opts) {
        this.options = jQuery.extend({}, this.options, opts);
        var instance = this;
        this.node = jQuery(this.options.node);

        if(this.options.login) {
            try {
                this.initApi(this.options.login, true, function () {
                    instance.initStartApiData(function () {
                        instance.initDom();
                        instance.initEvents();
                    });
                });
            } catch (e) {
                instance.showError(e);
            }
        } else {
            this.initDom();
            this.initEvents();
        }
    },

    initStartApiData : function(callback){
        var instance = this;

        var error = function (error) {
            instance.apiReady = false;
            instance.showError(error);
            instance.hideLoader();
            callback();
        };
        

        this.showLoader();
        this.api.getThemeList(function (themes) {
            instance.themes = themes;
            instance.api.getCompanyDomain(instance.options.login, function (domain) {
                instance.domain = instance._prepareDomain(domain);
                instance.api.getTimelineList( function (timelines) {
                    instance.timelines = timelines;
                    instance.hideLoader();
                    callback();
                }, error);
            }, error);
        }, error);
    },

    _prepareDomain : function(domain){
        if(domain && String(domain).length && String(domain).indexOf('.')){
            var domainArr = String(domain).split('.');

            if(domainArr && domainArr.length>2 && String(domainArr[1]).length === 2){
                domain = String(domain).replace('.' + domainArr[1] + '.', '.');
            }
        }

        console.log(domain);
        return domain;
    },

    setViewApiData : function(){
        var instance = this;

        if( this.apiReady) {
            var serverNode = this.node.find(':input[name=server]');
            var themesContainer = jQuery('#themes-container');
            var timelineSelector = jQuery('#timeline_type');

            jQuery.each(this.themes, function (num, theme) {
                themesContainer.append(
                    "<input type='radio' class='theme-select' name='template' id='theme"+theme.name+"' value='"+theme.name+"' />" +
                    "<label class='theme-item' for='theme"+theme.name+"'>" +
                        "<div class='name'>" + theme.title + "</div>" +
                        "<div class='image' style=\"background-image:url('http://" + instance.domain + theme.image + "')\"></div>" +
                    "</label>"
                );

                if(instance.options.template === theme.name){
                    jQuery("#theme" + theme.name).prop('checked', true);
                    instance.onTemplateSelect(theme.name);
                }
            });

            serverNode.val(this.domain);
            this.node.find('.api-data').removeClass('hidden');

            ///////////////////////////////////////////////

            timelineSelector.empty();

            jQuery.each(instance.timelines, function (num, value) {
                timelineSelector.append(
                    "<option " + (value==instance.options.timeline_type?'selected="selected"':'') + " value='" + value + "'>" + Locale.get(value) + "</option>"
                )
            });

        }
    },

    initDom : function(){
        //var res = this.api.getThemeList();
        //var res = this.api.getCompanyDomain(this.options.login);

        this.setViewApiData();
    },


    initEvents : function(){
        var instance = this;

        this.node.on('change keyup', ':input[name=login]', _.debounce(function () {
            var login = jQuery(this).val();
            instance.resetCompanyLogin(login);
        }, 2000));

        this.node.on('change', ':input[name=template]', function () {
            var template = jQuery(this).val();
            instance.options.themeparams = null;
            //todo: restore params if select old selected theme
            instance.onTemplateSelect(template);
        });

        this.node.on('change', 'input[type="checkbox"]', function(){
            jQuery(this).val(this.checked ? 1 : 0);
        }).change();
    },

    resetCompanyLogin : function(login){
        var instance = this;
        this.initApi(login, false, function () {
            instance.initStartApiData(function () {
                if( instance.apiReady) {
                    console.log('api ready');
                    instance.setViewApiData();
                }
            });
        });
    },

    onTemplateSelect : function(template){
        var instance = this;
        var themeSettings = this._getThemeSettings(template);

        if(themeSettings){
            if(!this.themeSettingsTemplate) {
                this.themeSettingsTemplate = this.node.find('.theme-data.template');
                this.themeSettingsTemplate.detach().removeClass('hidden').addClass('dynamic-item');
            }

            //console.log(instance.themeSettingsTemplate);

            if(!this.themeSettingsTemplate || !this.themeSettingsTemplate.length){
                return;
            }

            this.node.find('.theme-data.dynamic-item').remove(); //remove old items

            var settingsKeys = Object.keys(themeSettings);

            settingsKeys.sort(function(a,b) {
                var aType = themeSettings[a].config_type;
                var bType = themeSettings[b].config_type;
                var aPos = instance.sortSettingsTypes.indexOf(aType);
                var bPos = instance.sortSettingsTypes.indexOf(bType);

                if(aPos >= 0 && bPos == -1){
                    return -1;
                }
                if(bPos >= 0 && aPos == -1){
                    return 1;
                }
                return aPos-bPos;
            });


            jQuery.each(settingsKeys, function (num,key) {
                var param = themeSettings[key];


                if(instance.ingoreSettingsKeys.indexOf(key) >= 0){
                    return;
                }

                var itemNode = instance.themeSettingsTemplate.clone();
                itemNode.find('.title').text(Locale.get(param.config_title?param.config_title:param.config_key));

                if(instance.options.themeparams && _.isObject(instance.options.themeparams)){
                    if(!_.isUndefined(instance.options.themeparams[key])){
                        param.default_value = instance.options.themeparams[key];
                    }
                }

                switch (param.config_type){
                    case "color":
                    case "base_color":
                        itemNode.find('.data-input').html(
                            "<span class='color-view' style='background-color:" + param.default_value + "'></span>" +
                            "<input type='text' name='themeparams["+ key +"]' value='" + param.default_value + "'>"
                        );

                        itemNode.find(':input, .color-view').colpick({
                            onSubmit: function (hsb, hex, rgb, el, bySetColor) {
                                jQuery(el).closest('.data-input').find(':input').val('#' + hex);
                                jQuery(el).closest('.data-input').find('.color-view').css('background-color', '#' + hex);
                                jQuery(el).colpickHide();
                            }
                        });
                        break;

                    case "checkbox":
                        itemNode.find('.data-input').html(
                            "<input " + (parseInt(param.default_value)?'checked="checked"':'') + " type='checkbox' name='themeparams["+ key +"]' value='" + param.default_value + "' >"
                        );
                        break;

                    case "select":
                        var cnode = jQuery(
                            "<select name='themeparams["+ key +"]'></select>"
                        );
                        jQuery.each(param.values, function (num, value) {
                           cnode.append(
                               "<option " + (value==param.default_value?'selected="selected"':'') + " value='" + value + "'>" + Locale.get(value) + "</option>"
                           )
                        });
                        itemNode.find('.data-input').append(cnode);
                        break;

                    case "text":
                        break;

                    case "background_image":
                    case "logo_image":
                    case "image":
                        //@todo: upload image
                        return;
                        break;
                        //@todo: upload image
                        return;
                        break;
                    default:
                        console.log(key, param);
                        return;
                        break;
                }

                instance.node.find('.theme-data').last().after(itemNode);
            });

            this.node.find('.theme-data').removeClass('hidden');
        }

    },

    _getThemeSettings : function(template){
        var res = null;
        if( this.apiReady) {
            jQuery.each(this.themes, function (num, theme) {
                if(theme.name === template){
                    res = theme.config;
                }
            });
        }
        return res;
    },

    initApi : function (login, hideError, callback) {
        var instance = this;

        new JSONRpcClient({
            'url': this.options.apiUrl,
            'headers': {
                'X-Company-Login': login
            },
            'onerror': function (error) {
                if(!hideError) {
                    instance.showError(error);
                }
                instance.apiReady = false;
                callback(false);
            }, 
            'onready' : function () {
                instance.options.login = login;
                instance.api = this;
                instance.apiReady = true;
                callback(true);
            }
        });
    },

    _hideSettings : function(){
        this.node.find('.theme-data, .api-data').addClass('hidden');
    },

    showError(error){
        clearTimeout(this.errTimer);

        this._hideSettings();
        var errNode = jQuery('.error').first();
        errNode.empty();

        if(_.isObject(error)) {
            errNode.append("<p><strong>CODE</strong>: " + error.code + " <strong>ERROR</strong>: " + error.message + " </p>");
        } else{
            errNode.append("<p><strong>ERROR</strong>: " + error + "</p>");
        }

        this.errTimer = setTimeout(function () {
            errNode.empty();
        }, 10000);
        console.warn(error);
    },

    showLoader : function () {
        if(!this.loader){
            this.loader = jQuery(
                '<div class="loader-container">' +
                '<div class="loader-overlay"></div>' +
                '<div class="loader"><div style="width:100%;height:100%" class="lds-eclipse"><div></div></div>' +
                '</div>'
            ).hide();

            jQuery('BODY').append(this.loader);
        }

        this.loader.show();
    },

    hideLoader : function () {
        if(this.loader){
            this.loader.hide();
        }
    }
});

