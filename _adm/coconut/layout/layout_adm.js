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
        "adm_layoutadm" : {
            "list" : {
                "panel": "left",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/layout/list_adm#tpl_layoutadm_listview",
                "value": app.src+"/adm/layout/layout_adm/list"
            },
            "edit" : {
                "panel": "bottom",
                "title":"수정",
                "frame": null,
                "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
                "value": app.src+"/adm/layout/layout_adm/edit"
            },
            "post" : {
                "panel": "bottom",
                "title":"등록",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            }
        }
    });

    _.extend(Activity, {
        onCreateView: function() 
        {
            // contents
            var LayoutAdmActivity = {
                doList: function(params) {
                    var self = this;
    
                    var send_params = {
                        doc_id: 'adm_layoutadm/list',
                        page : (!_.isUndefined(params.page)) ? params.page : 1,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_layoutadm.list; // SETTING VALUE
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
    
                                // raw
                                $(panel.id+'_docs_contents .raw').unbind('click');
                                $(panel.id+'_docs_contents .raw').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        _.extend(params,{id:this_data_id});
                                        self.doEdit(params);
                                    }
                                });

                                // raw-del
                                $(panel.id+'_docs_contents .raw-del').unbind('click');
                                $(panel.id+'_docs_contents .raw-del').on('click', function() 
                                {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        var cf = confirm(app.lang['i_confirm_delete']);
                                        if(cf){
                                            var send_params = {
                                                doc_id: "adm_layoutadm/delete",
                                                id : this_data_id
                                            };
                                            _.extend(send_params, params);
                            
                                            ProgressBar.show_progress();
                                            DocAsyncTask.doPostMessage(app.src+"/adm/layout/layout_adm/delete", send_params, {
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
                        doc_id: 'adm_layoutadm/post'
                    };
    
                    // panel
                    var panel_setting = app.docs.adm_layoutadm.post; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'post'
                    });
                    UrlUtil.pushState('doPost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() {
    
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_layoutadm/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/layout/layout_adm/insert", _send_params, {
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
                                                $(panel.id+'_docs_contents #theDefaultForm #'+resp.fieldname).focus();
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
                    var send_params = {
                        doc_id: "adm_layoutadm/edit",
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_layoutadm.edit; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'edit',
                        id : params.id
                    });
                    UrlUtil.pushState('doEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            // $(panel.id+'_title').text(resp.msg.name.value);
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_layoutadm/update'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/layout/layout_adm/update", _send_params, {
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
                                                $(panel.id+'_docs_contents #theDefaultForm #'+resp.fieldname).focus();
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

            LayoutAdmActivity.doList(UrlUtil._url_params);
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
