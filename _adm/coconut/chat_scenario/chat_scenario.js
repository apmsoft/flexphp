// 콜백함수
function onReady($, _, Backbone) {
    // urlutil init
    UrlUtil.initialize(UrlUtil.getURL2JSON());

    app.cache = false;
    app.debug = true;

    // progress init
    ProgressBar.initialize('');

    // init
    // alert(app_docs);
    app.initialize({
        "chat_scenario" : {
            "list" : {
                "panel": "left",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/chat_scenario/list#tpl_chat_scenario_list",
                "value": app.src+"/adm/document/queue"
            },
            "edit" : {
                "panel": "right",
                "title":"시나리오 편집",
                "frame": null,
                "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
                "value": app.src+"/adm/document/queue"
            },
            "post" : {
                "panel": "right",
                "title": "시나리오 등록",
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
            var ChatScenarioActivity = {
                doList: function(params) {
                    var self = this;

                    var send_params = {
                        doc_id: 'adm_scenario/list',
                        page : 1,
                        cache : false
                    };
                    _.extend(send_params, params);

                    // panel
                    var panel_setting = app.docs.chat_scenario.list; // SETTING VALUE
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

                                // edit
                                $(panel.id+'_docs_contents .raw-edit').unbind('click');
                                $(panel.id+'_docs_contents .raw-edit').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        _.extend(params, {id:this_data_id});
                                        self.doEdit(params);
                                    }
                                });

                                // a href 링크 묶사발
                                $('a[href="#"]').click(function(e) { e.preventDefault(); });

                                // 학습노트
                                $(panel.id+'_docs_contents .btn-chat-note').unbind('click');
                                $(panel.id+'_docs_contents .btn-chat-note').on('click', function(e) {
                                    e.stopPropagation();
                                    var this_data_id = $(this).data('id');
                                    
                                    // mission list
                                    require(['/_adm/coconut/chat_scenario/note/chat_note.js'], function(ScenarioNoteActivity) {
                                        UrlUtil.pushUrlParams({
                                            scenario_id : this_data_id
                                        });
                                        ScenarioNoteActivity.doList(UrlUtil._url_params);
                                    });
                                });

                                // 시나리오
                                $(panel.id+'_docs_contents .btn-chat-scenario').unbind('click');
                                $(panel.id+'_docs_contents .btn-chat-scenario').on('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    var this_data_id = $(this).data('id');
                                });

                                // delete
                                $(panel.id+'_docs_contents .raw-del').unbind('click');
                                $(panel.id+'_docs_contents .raw-del').on('click', function(e)
                                {
                                    e.stopPropagation();
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id))
                                    {
                                        var cfd = confirm('삭제하시겠습니까?'+"\n"+'다시 복구 할 수 없습니다.');
                                        if(cfd)
                                        {
                                            ProgressBar.show_progress();

                                            var _send_params = {
                                                doc_id : 'adm_scenario/delete',
                                                id:this_data_id
                                            };

                                            DocAsyncTask.doPostMessage(app.src+"/adm/document/stack", _send_params, {
                                                success : function(resp){
                                                    ProgressBar.close_progress();
                                                    Toast.show('',resp.msg,2000, {style:'success'});
            
                                                    UrlUtil.pushUrlParams({
                                                        page : 1
                                                    });
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

                                 // add
                                 $(panel.id+'_docs_contents #btn_add').unbind('click');
                                 $(panel.id+'_docs_contents #btn_add').on('click', function() {
                                     self.doPost();
                                 });

                                // 카테고리
                                $(panel.id+'_docs_contents .category-link').unbind('click');
                                $(panel.id+'_docs_contents .category-link').on('click', function() {
                                    var category = $(this).data('id');
                                    _.extend(params, {category:category,page:1});
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
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theSearchForm', function(form_params)
                                {
                                    _.extend(params, form_params);
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
                        doc_id: 'adm_scenario/post'
                    };
    
                    // panel
                    var panel_setting = app.docs.chat_scenario.post; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'post'
                    });
                    UrlUtil.pushState('doProgramPost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() {
    
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_scenario/insert'
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
                doEdit: function(params) {
                    var self = this;
                    var send_params = {
                        doc_id: 'adm_scenario/edit',
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.chat_scenario.edit; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'edit',
                        id : params.id
                    });
                    UrlUtil.pushState('doProgramEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
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
                                        doc_id : 'adm_scenario/update'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/document/stack", _send_params, {
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

            ChatScenarioActivity.doList(UrlUtil._url_params);
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
