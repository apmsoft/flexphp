define(['jquery','underscore','backbone'],function($,_,Backbone) 
{
    // app docs
    _.extend(app.docs, {
        "scenario_note" : {
            "list" : {
                "panel": "right",
                "title":"학습노트",
                "frame": null,
                "template": "/_adm/coconut/chat_scenario/note/list#tpl_scenario_note_list",
                "value": app.src+"/adm/document/queue"
            },
            "edit" : {
                "panel": "rightside",
                "title":"노트 편집",
                "frame": null,
                "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
                "value": app.src+"/adm/document/queue"
            },
            "post" : {
                "panel": "rightside",
                "title": "노트 등록",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            }
        }
    });

    // contents
    var ScenarioNoteActivity = {
        doList: function(params) {
            var self = this;

            var send_params = {
                doc_id: 'adm_scenario_note/list',
                page : 1,
                cache : false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.scenario_note.list; // SETTING VALUE
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams(params);
            UrlUtil.pushState('doProgramNoteList', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            ProgressBar.show_progress();
            DocAsyncTask.doGetContents(panel_setting, send_params,
            {
                success: function(tpl, resp) 
                {
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                    {
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
                                        doc_id : 'adm_scenario_note/delete',
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
                        DocAsyncTask.doSubmit(panel.id+'_docs_contents #theNoteSearchForm', function(form_params) {
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
                doc_id: 'adm_scenario_note/post',
                scenario_id : UrlUtil._url_params.scenario_id
            };

            // panel
            var panel_setting = app.docs.scenario_note.post; // SETTING VALUE
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mode : 'note_post'
            });
            UrlUtil.pushState('doProgramNotePost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            ProgressBar.show_progress();
            DocAsyncTask.doGetContents(panel_setting, send_params,
            {
                success: function(tpl, resp) 
                {
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                    {
                        // submit
                        DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                        {
                            ProgressBar.show_progress();

                            var _send_params = {
                                doc_id : 'adm_scenario_note/insert',
                                scenario_id : UrlUtil._url_params.scenario_id
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
                doc_id: 'adm_scenario_note/edit',
                scenario_id : UrlUtil._url_params.scenario_id,
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.scenario_note.edit; // SETTING VALUE
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mode : 'note_edit',
                id : params.id
            });
            UrlUtil.pushState('doProgramNoteEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            ProgressBar.show_progress();
            DocAsyncTask.doGetContents(panel_setting, send_params,
            {
                success: function(tpl, resp) {
                    // $(panel.id+'_title').text(resp.msg.name.value);
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                    {
                        // submit
                        DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                        {
                            ProgressBar.show_progress();

                            var _send_params = {
                                doc_id : 'adm_scenario_note/update',
                                scenario_id : UrlUtil._url_params.scenario_id
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

    return ScenarioNoteActivity;
});
