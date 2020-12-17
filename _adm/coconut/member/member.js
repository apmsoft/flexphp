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
        "adm_user" : {
            "list" : {
                "panel": "left",
                "title": null,
                "frame": null,
                "template": "/_adm/coconut/member/list#tpl_member_list",
                "value": app.src+"/adm/document/queue"
            },
            "view" : {
                "panel": "bottom",
                "title": "회원정보",
                "frame": null,
                "template": "/_adm/coconut/member/view#tpl_member_view",
                "value": app.src+"/adm/document/queue"
            },
            "post" : {
                "panel": "bottom",
                "title": "회원등록",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            },
            "edit" : {
                "panel": null,
                "title": null,
                "frame": null,
                "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
                "value": app.src+"/adm/document/queue"
            },
            "data_info" : {
                "panel": "left",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/member/info#template_member_datainfo",
                "value": app.src+"/adm/member/member_datainfo"
            },
            "uploadfiles_load": {
                "panel": null,
                "title": null,
                "frame": null,
                "template": null,
                "value": app.src+"/adm/uploadfiles/load"
            },
            "uploadfiles_edit": {
                "panel": null,
                "title": null,
                "frame": null,
                "template": null,
                "value": app.src+"/adm/uploadfiles/multiple"
            }
        }
    });

    _.extend(Activity, {
        onCreateView: function() 
        {
            // contents
            var MemberActivity = {
                doList: function(params) {
                    var self = this;

                    var send_params = {
                        doc_id: 'adm_user/list',
                        page : 1,
                        cache : false
                    };
                    _.extend(send_params, params);

                    // panel
                    var panel_setting = app.docs.adm_user.list; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // 스크롤포지션
                                $("#left .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");

                                // view
                                $(panel.id+'_docs_contents .raw').unbind('click');
                                $(panel.id+'_docs_contents .raw').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        _.extend(params, {id:this_data_id});
                                        self.doView(params);
                                    }
                                });

                                 // add
                                 $(panel.id+'_docs_contents #btn_add').unbind('click');
                                 $(panel.id+'_docs_contents #btn_add').on('click', function() {
                                     self.doPost();
                                 });

                                // 카테고리
                                $(panel.id+'_docs_contents .category-link').unbind('click');
                                $(panel.id+'_docs_contents .category-link').on('click', function() {
                                    var job = $(this).data('id');
                                    _.extend(params, {job:job,page:1});
                                    self.doList(params);
                                    return;
                                });

                                // 페이징
                                $(panel.id+'_docs_contents .page-link').on('click', function() {
                                    var page = $(this).data('page');
                                    _.extend(params, {page:page});
                                    self.doList(params);
                                });

                                // 검색
                                $("#member_search_from").submit(function(event) {
                                    event.preventDefault();
                                    var q = $('#q').val();
                                    _.extend(params, {page:1,q:q});
                                    self.doList(params);
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
                        doc_id: 'adm_user/post'
                    };
    
                    // panel
                    var panel_setting = app.docs.adm_user.post; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'post'
                    });
                    UrlUtil.pushState('doUserPost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() {
    
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_user/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/document/stack", _send_params, {
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
                doView: function(params) {
                    var self = this;

                    ProgressBar.show_progress();

                    var send_params = {
                        doc_id: 'adm_user/view'
                    };
                    _.extend(send_params, params);

                    // panel
                    var panel_setting = app.docs.adm_user.view; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'view',
                        id : params.id
                    });
                    UrlUtil.pushState('doMemberView', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

                    // core
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            // $(panel.id+'_title').text(resp.msg.name);
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {                            
                                // 접속차단
                                $(panel.id+'_docs_contents #btn_remove').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    var vr = confirm("접속 차단 하시겠습니까?");
                                    if (vr == true) {
                                        self.doRemove({id:this_data_id});
                                    }
                                });

                                $(panel.id+'_docs_contents #user_level').on('change', function() {
                                    var chg_level = $(this).val();
                                    var this_data_id = $(this).data('id');
                                    self.doChangeLevel({ level: chg_level, id:this_data_id});
                                });

                                // 접속해제
                                $(panel.id+'_docs_contents #btn_reset').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    var vrs = confirm("접속 차단을 해제 하시겠습니까?");
                                    if (vrs == true) {
                                        self.doReset({id:this_data_id});
                                    }
                                });

                                // 수정
                                self.doEdit(params);

                                // info
                                //self.doInfo(params);

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
                doEdit: function(params) {
                    var self = this;
                    var send_params = {
                        doc_id: 'adm_user/edit'
                    };
                    _.extend(send_params, params);

                    // panel
                    var panel_setting = app.docs.adm_user.edit; // SETTING VALUE
                    // var panel = Panel.onStart(panel_setting);

                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            // $(panel.id+'_title').text(resp.msg.name.value);
                            $('#lay_member_form').html(tpl).promise().done(function() 
                            {
                                // submit
                                DocAsyncTask.doSubmit('#lay_member_form #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();
                                    var _send_params = {
                                        doc_id: 'adm_user/update'
                                    };

                                    _.extend(_send_params,form_params);
                                    DocAsyncTask.doPostMessage(app.src+"/adm/document/stack", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});

                                            // call list
                                            self.doList(params);
                                        },
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);
                                            if(!_.isUndefined(resp.fieldname)){
                                                $('#lay_member_form #theMemberForm #'+resp.fieldname).focus();
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
                doRemove : function(params){
                    var self = this;
                    var send_params = {
                        doc_id: 'adm_user/remove'
                    };
                    _.extend(send_params, params);

                    DocAsyncTask.doPostMessage(app.src+"/adm/user_delete.regi", send_params, {
                        success : function(resp){
                            Toast.show('','성공적으로 접속 차단 하였습니다.',2000, {style:'success'});
                            self.doList(params);
                        },

                        fail : function(resp){
                            alert(resp.msg);
                        }
                    });
                },
                doChangeLevel : function(params){
                    var self = this;

                    DocAsyncTask.doPostMessage(app.src+"/adm/user_change_level.regi", params, {
                        success : function(resp){
                            Toast.show('',resp.msg,2000, {style:'success'});
                            self.doList(params);
                        },

                        fail : function(resp){
                            alert(resp.msg);
                        }
                    });
                    
                },
                doReset : function(params){
                    var self = this;

                    DocAsyncTask.doPostMessage(app.src+"/adm/user_reset.regi", params, {
                        success : function(resp){
                            Toast.show('','성공적으로 접속 차단 해제 하였습니다.',2000, {style:'success'});
                            self.doList(params);
                        },

                        fail : function(resp){
                            alert(resp.msg);
                        }
                    });
                },
                doInfo : function(params){
                    var self  = this;

                    var panel_setting = app.docs.adm_user.data_info; // SETTING VALUE
                    DocAsyncTask.doGetContents(panel_setting, _.extend(params,{cache:false}),{
                        success: function(tpl, resp) {
                            $(panel.id+'_docst_contents #byIdInfo').html(tpl).promise().done(function() 
                            {
                                //alarm
                                $('#byIdInfo #btn_alarm_more').on('click',function(){
                                    require(['/_adm/coconut/alarm.js'],function(AlarmActivity){
                                        AlarmActivity.doList({doc_id:'adm_alarm',id:params.id,page:1});
                                    });
                                });
                            });
                        },
                        fail : function(resp){
                            alert(resp.msg);
                        }
                    });
                }
            };

            MemberActivity.doList(UrlUtil._url_params);
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
