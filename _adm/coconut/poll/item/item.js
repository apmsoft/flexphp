define(['jquery','underscore','backbone'],function($,_,Backbone) 
{
    _.extend(app.docs, {
        "adm_poll_item" : {
            "list" : {
                "panel": "right",
                "title": "설문조사 통계 및 항목관리",
                "frame": null,
                "template": "/_adm/coconut/poll/item/list#tpl_poll_item_list",
                "value": app.src+"/adm/document/queue"
            },
            "edit" : {
                "panel": "rightside",
                "title":"설문항목 수정",
                "frame": null,
                "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
                "value": app.src+"/adm/document/queue"
            },
            "post" : {
                "panel": "rightside",
                "title":"설문항목 추가",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            }
        }
    });

    // contents
    var PollItemActivity = {
        doList: function(params) {
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                doc_id: 'adm_poll_item/list',
                page : (!_.isUndefined(params.page)) ? params.page : 1,
                cache :false
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_poll_item.list; // SETTING VALUE
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushState('doPollItemList', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
            
            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                    {
                        // add
                        $(panel.id+'_docs_contents #btn-add-poll-item').unbind('click');
                        $(panel.id+'_docs_contents #btn-add-poll-item').on('click', function() {
                            self.doPost(params);
                        });

                        // edit
                        $(panel.id+'_docs_contents .poll-item-edit').unbind('click');
                        $(panel.id+'_docs_contents .poll-item-edit').on('click', function() {
                            params.pitem_id = $(this).data('id');
                            self.doEdit(params);
                        });

                        // delete
                        $(panel.id+'_docs_contents .poll-item-delete').unbind('click');
                        $(panel.id+'_docs_contents .poll-item-delete').on('click', function() {
                            var pitem_id = $(this).data('id');
                            var cf = confirm('삭제하시겠습니까?'+"\n"+'다시 복구 할 수 없습니다');
                            if(cf)
                            {
                                var _send_params = {
                                    doc_id : 'adm_poll_item/delete',
                                    pitem_id : pitem_id,
                                    poll_id : params.poll_id
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

                        ProgressBar.close_progress();
                    });
                },

                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                }
            });
        },
        doPost: function(params) {
            var self = this;
            ProgressBar.show_progress();

            var send_params = {
                doc_id: 'adm_poll_item/post'
            };
            _.extend(send_params,params);

            // panel
            var panel_setting = app.docs.adm_poll_item.post; // SETTING VALUE
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mode : 'post',
                mmode: 'item'
            });
            UrlUtil.pushState('doPollItemPost', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                    {
                        // event
                        $(panel.id+'_docs_contents input[name="pitem_type"]').on('click', function(){
                            var this_val = $(this).val();

                            for(var i=1; i<=10; i++){
                                var temp_id = 'q'+i;
                                if(this_val == 't'){
                                    $('#'+temp_id).attr('readonly',true);
                                }else{
                                    $('#'+temp_id).removeAttr('readonly');
                                }
                            }
                        });

                        // submit
                        DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                        {
                            ProgressBar.show_progress();

                            var _send_params = {
                                doc_id : 'adm_poll_item/insert',
                                poll_id : params.poll_id
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
                doc_id: "adm_poll_item/edit",
                cache :false,
                mmode: 'misson'
            };
            _.extend(send_params, params);

            // panel
            var panel_setting = app.docs.adm_poll_item.edit; // SETTING VALUE
            var panel = Panel.onStart(panel_setting);

            // make url
            UrlUtil.pushUrlParams({
                mode : 'edit',
                pitem_id : params.pitem_id,
                poll_id : params.poll_id
            });
            UrlUtil.pushState('doPollItemEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

            DocAsyncTask.doGetContents(panel_setting, send_params,{
                success: function(tpl, resp) {
                    // $(panel.id+'_title').text(resp.msg.name.value);
                    $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                    {
                        // event
                        $(panel.id+'_docs_contents input[name="pitem_type"]').on('click', function(){
                            var this_val = $(this).val();

                            for(var i=1; i<=10; i++){
                                var temp_id = 'q'+i;
                                if(this_val == 't'){
                                    $('#'+temp_id).attr('readonly',true);
                                }else{
                                    $('#'+temp_id).removeAttr('readonly');
                                }
                            }
                        });

                        // submit
                        DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                        {
                            ProgressBar.show_progress();

                            var _send_params = {
                                doc_id : 'adm_poll_item/update',
                                poll_id : params.poll_id
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


    return PollItemActivity;
});