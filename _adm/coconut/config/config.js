define(['jquery','underscore','backbone'],function($,_,Backbone) 
{
    _.extend(app.docs, {
        "adm_config" : {
            "init" : {
                "panel": "right",
                "title": "프로그램 실행 기본",
                "frame": null,
                "template": "/_adm/coconut/config/init#tpl_config_init",
                "value": null
            },
            "edit" : {
                "panel": "right",
                "title":"프로그램 관리",
                "frame": "/_adm/coconut/config/lay#right_docs_contents",
                "template": null,
                "value": null
            },
            "structure" : {
                "panel": null,
                "title": "구조체",
                "frame": null,
                "template": "/_adm/coconut/config/structure#tpl_config_structure",
                "value": null
            },
            "validation" : {
                "panel": null,
                "title": "Validation",
                "frame": null,
                "template": "/_adm/coconut/config/structure_validation#tpl_structure_validation",
                "value": null
            },
            "authority" : {
                "panel": null,
                "title": "Authority",
                "frame": "/_adm/coconut/config/structure_authority_help#config_lay_rtop",
                "template": "/_adm/coconut/config/structure_authority#tpl_structure_authority",
                "value": null
            },
            "model" : {
                "panel": null,
                "title": "Model",
                "frame": "/_adm/coconut/config/structure_model_global#config_lay_rtop",
                "template": "/_adm/coconut/config/structure_model#tpl_structure_model",
                "value": null
            },
            "data" : {
                "panel": null,
                "title": "Data",
                "frame": null,
                "template": "/_adm/coconut/config/structure_data#tpl_structure_data",
                "value": null
            },
            "data_post" : {
                "panel": "rightside",
                "title": "DataPost",
                "frame": null,
                "template": "/_adm/coconut/config/structure_data_post#tpl_structure_data_post",
                "value": null
            },
            "alarm" : {
                "panel": null,
                "title": "Alarm",
                "frame": "/_adm/coconut/config/structure_alarm_help#config_lay_rtop",
                "template": "/_adm/coconut/config/structure_alarm#tpl_structure_alarm",
                "value": null
            },
            "output" : {
                "panel": null,
                "title": "Output",
                "frame": "/_adm/coconut/config/structure_model_global#config_lay_rtop",
                "template": "/_adm/coconut/config/structure_output#tpl_structure_output",
                "value": null
            }
        }
    });

    // contents
    var ConfigActivity = {
        folder : '', 
        init : function(folder,params, callback) {
            var self = this;
            self.folder = folder;

            ProgressBar.show_progress();

            var send_params = {
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.init; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+self.folder+"/config/init";
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mode : 'init',
                mmode: 'config'
            });
            UrlUtil.pushState('doMissonPost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                    {
                        $(panel.id+" .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");

                        $('.btn-config-newadd').on('click', function(){
                            var cfid = $(this).data('cfid');
                            if(!$(this).hasClass('active')){
                                var cfn = confirm('실행('+cfid+') 파일을 생성하시겠습니까?');
                                if(cfn){
                                    ProgressBar.show_progress();
                                    DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+self.folder+"/config/create", {
                                        manifid : params.manifid,
                                        cfid : cfid
                                    }, 
                                    {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});
        
                                            history.go(-1);
                                            setTimeout(function(){                                                
                                                callback(params.manifid);
                                            },300);
                                        },
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);
                                        }
                                    });
                                }
                            }
                        });

                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doEdit: function(folder,params) {
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.edit; // SETTING VALUE
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mode : 'config_edit',
                id : params.id
            });
            UrlUtil.pushState('doConfigEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {                    
                    // ProgressBar.close_progress();
                    self.getStructure(folder,params);
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        getStructure: function(folder,params) {
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.structure; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure";
            var panel = Panel.onStart(panel_setting);

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#config_lay_left').html(tpl).promise().done(function()
                    {
                        // 포스트
                        $('#config_lay_left .btn_feature_add').on('click', function()
                        {
                            var this_feature = $(this).data('feature');
                            switch(this_feature){
                                case 'validation':
                                    self.doValidation(folder,params,{});
                                    break;
                                case 'model':
                                    self.doModel(folder,params,'');
                                    break;
                                case 'data':
                                    self.doDataPost(folder,params);
                                    break;
                                case 'output':
                                    self.doOutput(folder,params,'');
                                    break;
                            }

                            // scroll top
                            $("#right .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");
                        });

                        // 편집
                        $('#config_lay_left .btn-feature-column').on('click',function()
                        {
                            var this_feature = $(this).data('feature');
                            var this_feature_column = $(this).data('id');
                            switch(this_feature){
                                case 'validation':
                                    var vali_data = resp.msg[this_feature][this_feature_column];
                                    // alert(JSON.stringify(resp.msg[this_feature][this_feature_column]));
                                    self.doValidation(folder,params,{
                                        name : this_feature_column,
                                        title : vali_data.title,
                                        isnull: (vali_data.required) ? 'NO' : 'YES',
                                        type : vali_data.filter
                                    });
                                    break;
                                case 'authority':
                                    self.doAuthority(folder,params,{});
                                    break;
                                case 'model':
                                    self.doModel(folder,params,this_feature_column);
                                    break;
                                case 'data':
                                    self.doData(folder,params,this_feature_column);
                                    break;
                                case 'alarm':
                                    self.doAlarm(folder,params,this_feature_column);
                                    break;
                                case 'output':
                                    self.doOutput(folder,params,this_feature_column);
                                    break;
                            }

                            // scroll top
                            $("#right .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");
                        });

                        $('.btn-feature-then-column').on('click', function(){
                            var this_feature = $(this).data('feature');
                            var this_feature_column = $(this).data('id');
                            var this_thenid = $(this).data('thenid');
                            alert(this_feature+' / '+this_feature_column+' / '+this_thenid);
                            alert(JSON.stringify(params));
                        });

                        // 삭제 del-feature-column
                        $('#config_lay_left .del-feature-column').on('click',function()
                        {
                            var ccurent = $(this);
                            var this_feature = $(this).data('feature');
                            var this_feature_column = $(this).data('id');
                            var cfd = confirm('삭제하시겠습니까?');
                            if(cfd){
                                app.log(this_feature+'/'+this_feature_column);
                                var _send_params = {
                                    manifid : params.manifid,
                                    cfid : params.cfid,
                                    feature : this_feature,
                                    feature_column : this_feature_column
                                };
                                var do_feature_filename = app.src+"/adm/manifest/"+folder+"/config";
                                switch(this_feature){
                                    case 'validation':
                                        do_feature_filename += "/structure_validation_delete";
                                        break;
                                    case 'model':
                                        do_feature_filename += "/structure_model_delete";
                                        break;
                                    case 'output':
                                        do_feature_filename += "/structure_output_delete";
                                        break;
                                }

                                ProgressBar.show_progress();

                                $(ccurent).animateCss('hinge',function(){});
                                DocAsyncTask.doPostMessage(do_feature_filename, _send_params, 
                                {
                                    success : function(resp){
                                        ProgressBar.close_progress();
                                        Toast.show('',resp.msg,2000, {style:'success'});

                                        // scroll top
                                        $("#right .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");
    
                                        self.getStructure(folder,params);
                                    },
                                    fail : function(resp){
                                        ProgressBar.close_progress();
                                        alert(resp.msg);
                                    }
                                });
                            }
                        });

                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doValidation : function(folder,params,data){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.validation; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure_validation";
            var panel = Panel.onStart(panel_setting);

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#config_lay_rbottom').html(tpl).promise().done(function()
                    {
                        $('#cf_title').animateCss('slideInLeft',function(){
                            $('#vali_field').animateCss('flipInX');
                        });

                        self.getTables(folder,params,'#config_lay_rtop',function(columns){
                            console.log(columns);
                            $('#theValidationForm #vali_field').val(columns.name);
                            $('#theValidationForm #vali_title').val(columns.title);
                            if(columns.isnull == 'NO'){
                                $("#theValidationForm input[name=vali_required]:checkbox").prop("checked", true);
                            }else{
                                $("#theValidationForm input[name=vali_required]:checkbox").prop("checked", false);
                            }
                        });

                        // set data
                        if(!_.isUndefined(data.title)){
                            // alert(JSON.stringify(data));
                            $('#theValidationForm #vali_field').val(data.name);
                            $('#theValidationForm #vali_title').val(data.title);
                            if(data.isnull == 'NO'){
                                $("#theValidationForm input[name=vali_required]:checkbox").prop("checked", true);
                            }else{
                                $("#theValidationForm input[name=vali_required]:checkbox").prop("checked", false);
                            }
                            $('#theValidationForm #vali_filter').val(data.type).change();
                        }

                        DocAsyncTask.doSubmit('#theValidationForm', function(form_params)
                        {
                            ProgressBar.show_progress();
                            
                            _.extend(form_params,{
                                manifid : params.manifid,
                                cfid : params.cfid,
                                feature : 'validation'
                            });
                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+folder+"/config/structure_validation_inup", form_params, {
                                success : function(resp){
                                    ProgressBar.close_progress();
                                    Toast.show('',resp.msg,2000, {style:'success'});

                                    self.getStructure(folder,params);
                                },
                        
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });

                        // close
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doAuthority : function(folder,params,data){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.authority; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure_authority";
            var panel = Panel.onStart(panel_setting);

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#config_lay_rbottom').html(tpl).promise().done(function()
                    {
                        $('#cf_title').animateCss('slideInLeft');

                        DocAsyncTask.doSubmit('#theAuthorityForm', function(form_params)
                        {
                            ProgressBar.show_progress();
                            
                            _.extend(form_params,{
                                manifid : params.manifid,
                                cfid : params.cfid,
                                feature : 'authority'
                            });
                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+folder+"/config/structure_authority_inup", form_params, {
                                success : function(resp){
                                    ProgressBar.close_progress();
                                    Toast.show('',resp.msg,2000, {style:'success'});

                                    self.getStructure(folder,params);
                                },
                        
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });

                        // close
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doModel : function(folder,params,feature_column){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false,
                feature_column : feature_column
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.model; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure_model";
            var panel = Panel.onStart(panel_setting);

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#config_lay_rbottom').html(tpl).promise().done(function()
                    {
                        $('#cf_title').animateCss('slideInLeft',function(){
                            $('#model_field').animateCss('flipInX');
                        });

                        // 상수
                        $('#user_defines').on('change', function(){
                            var this_val = $(this).val();
                            var print_val = '{_DEFINE.'+this_val+'}';
                            $('#theModelForm #model_value').val(print_val);
                        });

                        // 함수
                        $('#user_funcs').on('change', function(){
                            var this_val = $(this).val();
                            var funs = resp.funs[this_val];
                            var print_val = '{__FUNC__.'+funs.func_name+'} ('+funs.func_parameter+')';
                            $('#theModelForm #model_value').val(print_val);
                        });

                        // 맥직 함수
                        $('#magic_funcs').on('change', function(){
                            var this_val = $(this).val();
                            var print_val = '{'+this_val+'. }';
                            $('#theModelForm #model_value').val(print_val);
                        });

                        // globals
                        $('#globals').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{'+this_val+'. }';
                            $('#theModelForm #model_value').val(print_val);
                        });

                        // resources
                        $('#resources').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{'+this_val+'. }';
                            $('#theModelForm #model_value').val(print_val);

                            self.getResources(this_val,function(resp){
                                // alert(JSON.stringify(resp));
                                var print_val = '{'+this_val+'.'+resp.name+'}';
                                $('#theModelForm #model_value').val(print_val);
                            });
                        });

                        DocAsyncTask.doSubmit('#theModelForm', function(form_params)
                        {
                            ProgressBar.show_progress();
                            
                            _.extend(form_params,{
                                manifid : params.manifid,
                                cfid : params.cfid,
                                feature : 'model'
                            });
                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+folder+"/config/structure_model_inup", form_params, {
                                success : function(resp){
                                    ProgressBar.close_progress();
                                    Toast.show('',resp.msg,2000, {style:'success'});

                                    self.getStructure(folder,params);
                                },
                        
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });

                        // close
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doData : function(folder,params,feature_column){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false,
                feature_column : feature_column
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.data; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure_data";
            var panel = Panel.onStart(panel_setting);

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#config_lay_rbottom').html(tpl).promise().done(function()
                    {
                        $('#cf_title').animateCss('slideInLeft',function(){
                            $('#data_field').animateCss('flipInX');
                        });

                        self.getTables(folder,params,'#config_lay_rtop',function(columns){
                            console.log(columns);
                            $('#theValidationForm #vali_field').val(columns.name);
                            $('#theValidationForm #vali_title').val(columns.title);
                            // if(columns.isnull == 'NO'){
                            //     $("#theValidationForm input[name=vali_required]:checkbox").prop("checked", true);
                            // }else{
                            //     $("#theValidationForm input[name=vali_required]:checkbox").prop("checked", false);
                            // }
                        });

                        DocAsyncTask.doSubmit('#theDataForm', function(form_params)
                        {
                            ProgressBar.show_progress();
                            
                            _.extend(form_params,{
                                manifid : params.manifid,
                                cfid : params.cfid,
                                feature : 'data'
                            });
                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+folder+"/config/structure_data_inup", form_params, {
                                success : function(resp){
                                    ProgressBar.close_progress();
                                    Toast.show('',resp.msg,2000, {style:'success'});

                                    self.getStructure(folder,params);
                                },
                        
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });

                        // close
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doDataPost : function(folder,params){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.data_post; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure_data_post";
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mmode: 'datapost'
            });
            UrlUtil.pushState('doMissonPost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function()
                    {
                        // event
                        $(panel.id+'_docs_contents #query').on('change', function()
                        {
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var send_params2 = {
                                query : this_val
                            };
                            _.extend(send_params2, params);

                            DocAsyncTask.doGetContents({
                                "panel": null,
                                "title": null,
                                "frame": null,
                                "template": null,
                                "value": app.src+"/adm/manifest/"+folder+"/config/structure_data_chkqueryparams"
                            }, send_params2,{
                                success: function(tpl, resp) {
                                    console.log(resp);
                                },
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });


                        // submit
                        DocAsyncTask.doSubmit('#theDataPostForm', function(form_params)
                        {
                            // close progress
                            ProgressBar.show_progress();
                            
                            _.extend(form_params,{
                                manifid : params.manifid,
                                cfid : params.cfid,
                                feature : 'data'
                            });
                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+folder+"/config/structure_data_insert", form_params, {
                                success : function(resp){
                                    ProgressBar.close_progress();
                                    Toast.show('',resp.msg,2000, {style:'success'});

                                    // close
                                    history.go(-1);

                                    // reload
                                    Handler.post(function(){
                                        self.getStructure(folder,params);
                                    },300);                                    
                                },
                        
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });

                        // close
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doAlarm : function(folder,params,feature_column){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false,
                feature_column : feature_column
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.alarm; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure_alarm";
            var panel = Panel.onStart(panel_setting);

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#config_lay_rbottom').html(tpl).promise().done(function()
                    {
                        $('#cf_title').animateCss('slideInLeft',function(){
                            $('#alarm_field').animateCss('flipInX');
                        });

                        // 상수
                        $('#alarm_send_option').on('change', function(){
                            var this_val = $(this).val();
                            $('#theAlarmForm #alarm_value').val(this_val);
                        });

                        // 함수
                        $('#user_funcs').on('change', function(){
                            var this_val = $(this).val();
                            var funs = resp.funs[this_val];
                            var print_val = '{__FUNC__.'+funs.func_name+'} ('+funs.func_parameter+')';
                            $('#theAlarmForm #alarm_value').val(print_val);
                        });

                        // 맥직 함수
                        $('#magic_funcs').on('change', function(){
                            var this_val = $(this).val();
                            var print_val = '{'+this_val+'. }';
                            $('#theAlarmForm #alarm_value').val(print_val);
                        });

                        // globals
                        $('#globals').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{'+this_val+'. }';
                            $('#theAlarmForm #alarm_value').val(print_val);
                        });

                        // resources
                        $('#resources').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{'+this_val+'. }';
                            $('#theAlarmForm #alarm_value').val(print_val);

                            self.getResources(this_val,function(resp){
                                // alert(JSON.stringify(resp));
                                var print_val = '{'+this_val+'.'+resp.name+'}';
                                $('#theAlarmForm #alarm_value').val(print_val);
                            });
                        });

                        DocAsyncTask.doSubmit('#theAlarmForm', function(form_params)
                        {
                            ProgressBar.show_progress();
                            
                            _.extend(form_params,{
                                manifid : params.manifid,
                                cfid : params.cfid,
                                feature : 'alarm'
                            });
                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+folder+"/config/structure_alarm_inup", form_params, {
                                success : function(resp){
                                    ProgressBar.close_progress();
                                    Toast.show('',resp.msg,2000, {style:'success'});

                                    self.getStructure(folder,params);
                                },
                        
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });

                        // close
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doOutput : function(folder,params,feature_column){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false,
                feature_column : feature_column
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_config.output; // SETTING VALUE
            panel_setting.value = app.src+"/adm/manifest/"+folder+"/config/structure_output";
            var panel = Panel.onStart(panel_setting);

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#config_lay_rbottom').html(tpl).promise().done(function()
                    {
                        $('#cf_title').animateCss('slideInLeft',function(){
                            $('#output_field').animateCss('flipInX');
                        });

                        // 상수
                        $('#user_defines').on('change', function(){
                            var this_val = $(this).val();
                            var print_val = '{_DEFINE.'+this_val+'}';
                            $('#theOutputForm #output_value').val(print_val);
                        });

                        // 함수
                        $('#user_funcs').on('change', function(){
                            var this_val = $(this).val();
                            var funs = resp.funs[this_val];
                            var print_val = '{__FUNC__.'+funs.func_name+'} ('+funs.func_parameter+')';
                            $('#theOutputForm #output_value').val(print_val);
                        });

                        // 맥직 함수
                        $('#magic_funcs').on('change', function(){
                            var this_val = $(this).val();
                            var print_val = '{'+this_val+'. }';
                            $('#theOutputForm #output_value').val(print_val);
                        });

                        // 모델
                        $('#config_model').on('change', function(){
                            var this_val = $(this).val();
                            var print_val = '{__MODEL__.'+this_val+'}';
                            $('#theOutputForm #output_value').val(print_val);
                        });

                        // 데이터
                        $('#config_data').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{__DATA__.'+this_val+'}';
                            $('#theOutputForm #output_value').val(print_val);
                        });

                        // 데이터 스키마
                        $('#data_scheme').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{__SCHEME__.'+this_val+'}';
                            $('#theOutputForm #output_value').val(print_val);
                        });

                        // globals
                        $('#globals').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{'+this_val+'. }';
                            $('#theOutputForm #output_value').val(print_val);
                        });

                        // resources
                        $('#resources').on('change', function(){
                            var this_val = $(this).val();
                            // var funs = resp.funs[this_val];
                            var print_val = '{'+this_val+'. }';
                            $('#theOutputForm #output_value').val(print_val);

                            self.getResources(this_val,function(resp){
                                // alert(JSON.stringify(resp));
                                var print_val = '{'+this_val+'.'+resp.name+'}';
                                $('#theOutputForm #output_value').val(print_val);
                            });
                        });

                        DocAsyncTask.doSubmit('#theOutputForm', function(form_params)
                        {
                            ProgressBar.show_progress();
                            
                            _.extend(form_params,{
                                manifid : params.manifid,
                                cfid : params.cfid,
                                feature : 'output'
                            });
                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/"+folder+"/config/structure_output_inup", form_params, {
                                success : function(resp){
                                    ProgressBar.close_progress();
                                    Toast.show('',resp.msg,2000, {style:'success'});

                                    self.getStructure(folder,params);
                                },
                        
                                fail : function(resp){
                                    ProgressBar.close_progress();
                                    alert(resp.msg);
                                }
                            });
                        });

                        // close
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        getTables: function(folder,params,printid,callback){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false
            };
            _.extend(send_params, params);

            // table info
            DocAsyncTask.doGetContents({
                "panel": null,
                "title": "전송된 데이터 유효성 체크",
                "frame": null,
                "template": "/_adm/coconut/config/structure_tables#tpl_structure_tables",
                "value": app.src+"/adm/query/tables/list"
            }, send_params,{
                success: function(tpl, resp) 
                {
                    $(printid).html(tpl).promise().done(function(){
                        $(printid+' #_tables').on('change', function(){
                            var this_tname = $(this).val();
                            self.getColumns(this_tname, printid, function(column_info){
                                // alert(JSON.stringify(column_info));
                                callback(column_info);
                            });
                        });
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        getColumns : function(tname,printid, callback){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false,
                tname : tname
            };

            // table info
            DocAsyncTask.doGetContents({
                "panel": null,
                "title": "선택 테이블 퀄럼",
                "frame": null,
                "template": "/_adm/coconut/config/structure_columns#tpl_structure_columns",
                "value": app.src+"/adm/query/tables/data/scheme_info"
            }, send_params,{
                success: function(tpl, resp) {
                    $(printid+' #_tables_columns').html(tpl).promise().done(function(){
                        $(printid+' ._column-raw').on('click', function(){
                            var column_name = $(this).data('name');
                            var column_type = $(this).data('type');
                            var column_isnull = $(this).data('isnull');
                            var column_title = $(this).data('title');
                            callback({
                                name : column_name,
                                type : column_type,
                                isnull : column_isnull,
                                title : column_title
                            })
                        });
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        getResources : function(resid, callback){
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                cache :false,
                resid : resid
            };

            // panel
            var panel_setting = {
                "panel": "bottom",
                "title": "선택한 리소스 데이터",
                "frame": null,
                "template": "/_adm/coconut/config/structure_resources#tpl_structure_resources",
                "value": app.src+"/adm/manifest/resource"
            };
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mmode: 'resources'
            });
            UrlUtil.pushState('doRESOURCES', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            // table info
            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $('#bottom_docs_contents').html(tpl).promise().done(function()
                    {
                        $('#bottom_docs_contents ._res-raw').on('click', function()
                        {
                            var column_name = $(this).data('name');
                            var column_val = $(this).data('val');
                            callback({
                                name : column_name,
                                val : column_val
                            });

                            Handler.post(function(){
                                history.go(-1);
                            },30);
                        });
                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        }
    };

    return ConfigActivity;
});