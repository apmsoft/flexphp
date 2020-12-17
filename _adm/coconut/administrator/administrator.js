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
        "adm" : {
            "list" : {
                "panel": "left",
                "title": null,
                "frame": null,
                "template": "/_adm/coconut/administrator/list#template_adm_list",
                "value": app.src+"/adm/adm/list"
            },
            "write" : {
                "panel": "modal",
                "title": "관리자 등록",
                "frame": null,
                "template": "/_adm/coconut/administrator/write#template_adm_write",
                "value": app.src+"/adm/adm/write"
            },
            "modify" : {
                "panel": "modal",
                "title": "관리자 수정",
                "frame": null,
                "template": "/_adm/coconut/administrator/modify#template_adm_modify",
                "value": app.src+"/adm/adm/modify"
            }
        }
    });

    _.extend(Activity, {
        onCreateView: function() 
        {    
            // contents
            var AdmActivity = {
                doList: function(params) {
                    var self = this;
    
                    var send_params = {
                        page:1,
                        cache:false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm.list; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // 스크롤포지션
                                $("#left .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");
    
                                // 삭제
                                $(panel.id+'_docs_contents .btn_adm_delete').unbind('click');
                                $(panel.id+'_docs_contents .btn_adm_delete').on('click', function() {
                                    var vr = confirm("관리자 권한을 삭제 하시겠습니까?");
                                    if (vr == true) {
                                        var this_data_id = $(this).data('id');
                                        self.doDelete({id:this_data_id});
                                        return;
                                    }
                                });
    
                                // 추가
                                $(panel.id+'_docs_contents #btn_adm_add').unbind('click');
                                $(panel.id+'_docs_contents #btn_adm_add').on('click',function(){
                                    self.doWrite(params);
                                });
    
                                // 추가
                                $(panel.id+'_docs_contents .btn_adm_edit').unbind('click');
                                $(panel.id+'_docs_contents .btn_adm_edit').on('click',function(){
                                    var this_data_id = $(this).data('id');
                                    self.doModify({id:this_data_id});
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
                doWrite: function(params) {
                    var self = this;
    
                    var send_params = {
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm.write; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);
    
                    // url
                    UrlUtil.pushUrlParams({
                        mode : 'adm_write'
                    });
                    UrlUtil.pushState('adm_write', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $('#myModal_docs_contents').html(tpl).promise().done(function() {
                                // submit
                                DocAsyncTask.doSubmit('#theADMForm', function(form_params){
                                    ProgressBar.show_progress();
    
                                    _.extend(form_params, send_params);
                                    DocAsyncTask.doPostMessage(app.src+"/adm/adm/write.regi", form_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            // toast
                                            Toast.show('',resp.msg,2000,{style:'success'});
    
                                            self.doList({ page: 1 });
                                            setTimeout(function(){
                                                history.go(-1);
                                            },300);
                                        },
    
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);                                    
                                            if(!_.isUndefined(resp.fieldname)){
                                                $('#theADMForm #'+resp.fieldname).focus();
                                            }
                                        }
                                    });
                                });
    
                                ProgressBar.close_progress();
                            });
                        },
    
                        fail : function(resp){
                            alert(resp.msg);
                        }
                    });
                },
                doModify: function(params) {
                    var self = this;
    
                    var send_params = {
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm.modify; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // url
                    UrlUtil.pushUrlParams({
                        mode : 'adm_modify'
                    });
                    UrlUtil.pushState('adm_modify', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $('#myModal_docs_contents').html(tpl).promise().done(function() {
                                // submit
                                DocAsyncTask.doSubmit('#theADMForm', function(form_params){
                                    ProgressBar.show_progress();
    
                                    _.extend(form_params, send_params);
                                    DocAsyncTask.doPostMessage(app.src+"/adm/adm/modify.regi", form_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            // toast
                                            Toast.show('',resp.msg,2000,{style:'success'});
    
                                            self.doList({ page: 1 });
                                            setTimeout(function(){
                                                history.go(-1);
                                            },300);
                                        },
    
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);                                    
                                            if(!_.isUndefined(resp.fieldname)){
                                                $('#theADMForm #'+resp.fieldname).focus();
                                            }
                                        }
                                    });
                                });
    
                                ProgressBar.close_progress();
                            });
                        },
    
                        fail : function(resp){
                            alert(resp.msg);
                        }
                    });
                },
                doDelete: function(params) {
                    var self = this;
    
                    var send_params = {
                        page : 1
                    };
                    _.extend(send_params, params);
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doPostMessage(app.src+"/adm/adm/delete.regi", send_params, {
                        success : function(resp){
                            ProgressBar.close_progress();
    
                            self.doList(send_params);
    
                            // toast
                            Toast.show('',resp.msg,2000,{style:'success'});                        
                        },
    
                        fail : function(resp){
                            ProgressBar.close_progress();
                            alert(resp.msg);
                        }
                    });
                }
            };
    
            AdmActivity.doList(UrlUtil._url_params);
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
