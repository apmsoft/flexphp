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
        "adm_poll" : {
            "list" : {
                "panel": "left",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/poll/list#tpl_poll_listview",
                "value": app.src+"/adm/document/queue"
            },
            "edit" : {
                "panel": "bottom",
                "title":"설문조사 수정",
                "frame": null,
                "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
                "value": app.src+"/adm/document/queue"
            },
            "post" : {
                "panel": "bottom",
                "title":"설문조사 등록",
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
            var PollActivity = {
                doList: function(params) {
                    var self = this;
    
                    var send_params = {
                        doc_id: 'adm_poll/list',
                        page : (!_.isUndefined(params.page)) ? params.page : 1,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_poll.list; // SETTING VALUE
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

                                // edit btn_edit
                                $(panel.id+'_docs_contents .btn_edit').unbind('click');
                                $(panel.id+'_docs_contents .btn_edit').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    var send_params = {
                                        id:this_data_id
                                    };
                                    _.extend(send_params,params);
                                    self.doEdit(send_params);
                                });
    
                                // 문항 관리
                                $(panel.id+'_docs_contents .btn_item_list').unbind('click');
                                $(panel.id+'_docs_contents .btn_item_list').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    UrlUtil.pushUrlParams({
                                        s : 'pi',
                                        poll_id : this_data_id
                                    });

                                    require(['/_adm/coconut/poll/item/item.js'], function(PollItemActivity) {
                                        var view_params = UrlUtil._url_params;
                                        _.extend(view_params, {
                                            poll_id : this_data_id
                                        });
                                        PollItemActivity.doList(view_params);                          
                                    });                                    
                                });

                                // 문항별 결과 및 통계
                                $(panel.id+'_docs_contents .btn_item_log_list').unbind('click');
                                $(panel.id+'_docs_contents .btn_item_log_list').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    UrlUtil.pushUrlParams({
                                        s : 'pil',
                                        poll_id : this_data_id
                                    });

                                    require(['/_adm/coconut/poll/item/item_log.js'], function(PollItemLogActivity) {
                                        var view_params = UrlUtil._url_params;
                                        _.extend(view_params, {
                                            poll_id : this_data_id
                                        });
                                        PollItemLogActivity.doList(view_params);                          
                                    });                                    
                                });

                                // category
                                $(panel.id+'_docs_contents .category-link').unbind('click');
                                $(panel.id+'_docs_contents .category-link').on('click', function() {
                                    var this_category = $(this).data('id');
                                    UrlUtil.pushUrlParams({
                                        page : 1,
                                        category : this_category
                                    });
                                    self.doList(UrlUtil._url_params);
                                });

                                // delete btn_delete
                                $(panel.id+'_docs_contents .btn_delete').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    var cf = confirm('삭제하시겠습니까?'+"\n"+'다시 복구 할 수 없습니다');
                                    if(cf)
                                    {
                                        var _send_params = {
                                            doc_id : 'adm_poll/delete',
                                            id : this_data_id
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
                                });
    
                                // 페이징
                                $(panel.id+'_docs_contents .page-link').on('click', function() {
                                    var page = $(this).data('page');
                                    UrlUtil.pushUrlParams({
                                        page : 2
                                    });
                                    self.doList(UrlUtil._url_params);
                                });
    
                                // 검색
                                $(panel.id+'_docs_contents #thePollListViewForm').submit(function(event) {
                                    event.preventDefault();
                                    var q = $('#q').val();

                                    UrlUtil.pushUrlParams({
                                        page : 1,
                                        q : q
                                    });
                                    self.doList(UrlUtil._url_params);
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
                        doc_id: 'adm_poll/post'
                    };
    
                    // panel
                    var panel_setting = app.docs.adm_poll.post; // SETTING VALUE
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
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_poll/insert'
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
                    ProgressBar.show_progress();

                    var send_params = {
                        doc_id: "adm_poll/edit",
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_poll.edit; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'edit',
                        id : params.id
                    });
                    UrlUtil.pushState('doEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,
                    {
                        success: function(tpl, resp) 
                        {
                            // $(panel.id+'_title').text(resp.msg.name.value);
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        doc_id : 'adm_poll/update'
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

            PollActivity.doList(UrlUtil._url_params);
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
