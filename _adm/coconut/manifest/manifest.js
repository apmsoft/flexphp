// 콜백함수
function onReady($, _, Backbone) {
    // urlutil init
    UrlUtil.initialize(UrlUtil.getURL2JSON());

    app.cache = false;
    app.debug = true;

    // progress init
    ProgressBar.initialize('');

    // init
    app.initialize({
        "adm_manifest" : {
            "list" : {
                "panel": "left",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/manifest/list#tpl_manifest_listview",
                "value": app.src+"/adm/manifest/manifest/list"
            },
            "edit" : {
                "panel": "bottom",
                "title":"수정",
                "frame": null,
                "template": "/_adm/coconut/manifest/edit#tpl_manifest_edit",
                "value": app.src+"/adm/manifest/manifest/edit"
            },
            "post" : {
                "panel": "bottom",
                "title":"등록",
                "frame": null,
                "template": "/_adm/coconut/manifest/post#tpl_manifest_post",
                "value": app.src+"/adm/manifest/manifest/post"
            }
        }
    });

    _.extend(Activity, {
        onCreateView: function() 
        {
            // contents
            var ManifasetAdmActivity = {
                doList: function(params) {
                    var self = this;
    
                    var send_params = {
                        doc_id: 'adm_manifest/list',
                        page : (!_.isUndefined(params.page)) ? params.page : 1,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_manifest.list; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // 스크롤포지션
                                $("#left .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");

                                // add
                                $(panel.id+'_docs_contents #btn_add').unbind('click');
                                $(panel.id+'_docs_contents #btn_add').on('click', function() {
                                    self.doPost();
                                });
    
                                // edit
                                $(panel.id+'_docs_contents .raw').unbind('click');
                                $(panel.id+'_docs_contents .raw').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        _.extend(params,{id:this_data_id});
                                        self.doEdit(params);
                                    }
                                });

                                // del
                                $(panel.id+'_docs_contents .raw-del').unbind('click');
                                $(panel.id+'_docs_contents .raw-del').on('click', function() 
                                {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        var cf = confirm(app.lang['i_confirm_delete']);
                                        if(cf){
                                            var send_params = {
                                                doc_id: "adm_manifest/delete",
                                                id : this_data_id
                                            };
                                            _.extend(send_params, params);
                            
                                            ProgressBar.show_progress();
                                            DocAsyncTask.doPostMessage(app.src+"/adm/manifest/manifest/delete", send_params, {
                                                success : function(resp){
                                                    ProgressBar.close_progress();
                                                    Toast.show('',resp.msg,2000, {style:'success'});
                                                    self.doList(UrlUtil._url_params);
                                                },
                            
                                                fail : function(resp){
                                                    ProgressBar.close_progress();
                                                    alert(resp.msg);
                                                }
                                            });
                                        }
                                    }
                                });

                                // config edit btn-edit-config
                                $(panel.id+'_docs_contents .btn-edit-config').unbind('click');
                                $(panel.id+'_docs_contents .btn-edit-config').on('click', function() {
                                    var this_manifid = $(this).data('id');
                                    var this_cfid = $(this).data('cfid');
                                    
                                    // mission list
                                    require(['/_adm/coconut/config/config.js'], function(ConfigActivity) {
                                        var send_params = UrlUtil._url_params;
                                        _.extend(send_params, {
                                            manifid : this_manifid,
                                            cfid : this_cfid
                                        });
                                        ConfigActivity.doEdit('manifest',send_params);                          
                                    });
                                });

                                // config edit btn-edit-config
                                $(panel.id+'_docs_contents .btn-post-config').unbind('click');
                                $(panel.id+'_docs_contents .btn-post-config').on('click', function() {
                                    var this_manifid = $(this).data('id');
                                    
                                    // mission list
                                    require(['/_adm/coconut/config/config.js'], function(ConfigActivity) {
                                        var send_params = UrlUtil._url_params;
                                        _.extend(send_params, {
                                            manifid : this_manifid
                                        });
                                        ConfigActivity.init('manifest',send_params, function(manifid){
                                            self.doList(params);
                                        });                          
                                    });
                                });

                                // config delete btn-remove-config
                                $(panel.id+'_docs_contents .btn-remove-config').unbind('click');
                                $(panel.id+'_docs_contents .btn-remove-config').on('click', function() {
                                    var this_manifid = $(this).data('id');
                                    var this_cfid = $(this).data('cfid');

                                    var cfn = confirm('실행('+this_cfid+') 파일을 삭제 하시겠습니까?'+"\n"+'다시 복구 할 수 없습니다');
                                    if(cfn)
                                    {
                                        ProgressBar.show_progress();
                                        DocAsyncTask.doPostMessage(app.src+"/adm/manifest/manifest/config/delete", {
                                            manifid : this_manifid,
                                            cfid : this_cfid
                                        }, 
                                        {
                                            success : function(resp){
                                                ProgressBar.close_progress();
                                                Toast.show('',resp.msg,2000, {style:'success'});
            
                                                self.doList(params);
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
                doPost: function() {
                    var self = this;
                    var send_params = {
                        doc_id: 'adm_manifest/post'
                    };
    
                    // panel
                    var panel_setting = app.docs.adm_manifest.post; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'post'
                    });
                    UrlUtil.pushState('doPost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                $(panel.id+'_docs_contents #uploadable_enable').on('click', function(){
                                    if( $(this).is(':checked') ){ //전체선택
                                        $(panel.id+'_docs_contents .uploadable').attr('readonly',true);
                                    }else{
                                        $(panel.id+'_docs_contents .uploadable').removeAttr('readonly');
                                    }
                                });
    
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theManifestForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_manifest/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/manifest/manifest/insert", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});
    
                                            UrlUtil.pushUrlParams({
                                                page : 1
                                            });
                                            self.doList(UrlUtil._url_params);
                                            setTimeout(function(){
                                                history.go(-1);
                                            },300);
                                        },
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);
                                            if(!_.isUndefined(resp.fieldname)){
                                                $(panel.id+'_docs_contents #theManifestForm #'+resp.fieldname).focus();
                                            }
                                        }
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
                doEdit: function(params) {
                    var self = this;
                    ProgressBar.show_progress();

                    var send_params = {
                        doc_id: "adm_manifest/edit",
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_manifest.edit; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'edit',
                        id : params.id
                    });
                    UrlUtil.pushState('doEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
                    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            // $(panel.id+'_title').text(resp.msg.name.value);
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                $(panel.id+'_docs_contents #uploadable_enable').on('click', function(){
                                    if( $(this).is(':checked') ){ //전체선택
                                        $(panel.id+'_docs_contents .uploadable').attr('readonly',true);
                                    }else{
                                        $(panel.id+'_docs_contents .uploadable').removeAttr('readonly');
                                    }
                                });

                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theManifestForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_manifest/update'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/manifest/manifest/update", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});
    
                                            self.doList(UrlUtil._url_params);
                                            setTimeout(function(){
                                                history.go(-1);
                                            },300);
                                        },
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);
                                            if(!_.isUndefined(resp.fieldname)){
                                                $(panel.id+'_docs_contents #theManifestForm #'+resp.fieldname).focus();
                                            }
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
                }
            };

            ManifasetAdmActivity.doList(UrlUtil._url_params);
        }
    });

    // layout
    Activity.onCreate();
    Activity.onCreateView();

    // back key
    Activity.onBackPressed();

    // swipe
    Activity.setTouchSwipe({target: '#left','gesture': 'right',threshold: 130}, function() {
        DrawerNavigation.drawer_menu_opened();
    });
    Activity.setTouchSwipe({target: '#rightside, #right, #bottom','gesture': 'right',threshold: 100}, function() {
        history.go(-1);
    });

    // close progress
    Runnable(function(){
        ProgressBar.close_progress();
    }, 300);
}
