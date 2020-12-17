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
        "adm_qtables" : {
            "list" : {
                "panel": "left",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/query/list_tables#tpl_query_tablesview",
                "value": app.src+"/adm/query/tables/list"
            },
            // "edit" : {
            //     "panel": "bottom",
            //     "title":"수정",
            //     "frame": null,
            //     "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
            //     "value": app.src+"/adm/query/tables/edit"
            // },
            "post" : {
                "panel": "bottom",
                "title":"등록",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            },
            "create_scheme" : {
                "panel": "bottom",
                "title":"테이블생성(스키마)",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            }
        },
        "adm_qdata" : {
            "list" : {
                "panel": "bottom",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/query/data/list#tpl_data_listview",
                "value": app.src+"/adm/query/tables/data/list"
            },
            "edit" : {
                "panel": "bottom",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/query/data/edit#tpl_data_editview",
                "value": app.src+"/adm/query/tables/data/edit"
            },
            "scheme" : {
                "panel": "right",
                "title":null,
                "frame": null,
                "template": "/_adm/coconut/query/data/scheme#tpl_data_scheme",
                "value": app.src+"/adm/query/tables/data/scheme_info"
            },
            "createbycopy":{
                "panel": "rightside",
                "title": "테이블 복사 생성(CREATE COPY TABLE)",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            },
            "rename":{
                "panel": "rightside",
                "title": "테이블명 변경(RENAME TABLE NAME)",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/document/queue"
            },
            "showtablescheme":{
                "panel": "bottom",
                "title": "테이블 생성 스키마",
                "frame": null,
                "template": "/_adm/coconut/query/data/show_scheme#tpl_table_scheme",
                "value": app.src+"/adm/query/tables/data/show_table_scheme"
            },            
            "column_edit":{
                "panel": "rightside",
                "title": "퀄럼 정보변경",
                "frame": null,
                "template": "/_adm/coconut/query/data/column_edit#tpl_column_edit",
                "value": app.src+"/adm/query/tables/data/column_edit"
            },
            "column_post":{
                "panel": "rightside",
                "title": "퀄럼 추가",
                "frame": null,
                "template": "/_adm/coconut/query/data/column_post#tpl_column_post",
                "value": app.src+"/adm/query/tables/data/column_post"
            },
            "column_index_post":{
                "panel": "rightside",
                "title": "INDEX 퀄럼 등록",
                "frame": null,
                "template": "/_adm/coconut/template/default/post#tpl_default_post",
                "value": app.src+"/adm/query/tables/data/column_index_post"
            }
        }
    });

    _.extend(Activity, {
        onCreateView: function() 
        {
            // contents
            var QTablesActivity = {
                doList: function(params) {
                    var self = this;
    
                    var send_params = {
                        doc_id: 'adm_qtables/list',
                        page : (!_.isUndefined(params.page)) ? params.page : 1,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qtables.list; // SETTING VALUE
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

                                // create table scheme
                                $(panel.id+'_docs_contents #btn_create_table_scheme').unbind('click');
                                $(panel.id+'_docs_contents #btn_create_table_scheme').on('click', function() {
                                    self.doCreateTableScheme();
                                });
    
                                // Edit
                                $(panel.id+'_docs_contents .raw').unbind('click');
                                $(panel.id+'_docs_contents .raw').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        _.extend(params,{id:this_data_id});
                                        self.doEdit(params);
                                    }
                                });

                                //data-list
                                $(panel.id+'_docs_contents .data-list').unbind('click');
                                $(panel.id+'_docs_contents .data-list').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        _.extend(params,{tname:this_data_id});
                                        QTablesDataActivity.doList(params);
                                    }
                                });

                                //data-scheme
                                $(panel.id+'_docs_contents .data-scheme').unbind('click');
                                $(panel.id+'_docs_contents .data-scheme').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    if (!_.isUndefined(this_data_id)) {
                                        _.extend(params,{tname:this_data_id});
                                        QTablesDataActivity.doShowScheme(params);
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
                                                doc_id: "adm_qtables/delete",
                                                id : this_data_id
                                            };
                                            _.extend(send_params, params);
                            
                                            ProgressBar.show_progress();
                                            DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/delete", send_params, {
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
                doCreateTableScheme : function(){
                    var self = this;
                    var send_params = {
                        doc_id: 'adm_qtables/create_scheme'
                    };
    
                    // panel
                    var panel_setting = app.docs.adm_qtables.create_scheme; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'create_scheme'
                    });
                    UrlUtil.pushState('doCreateScheme', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    ProgressBar.show_progress();
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() {
    
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/create_scheme", _send_params, {
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
                doPost: function() {
                    var self = this;
                    var send_params = {
                        doc_id: 'adm_qtables/post'
                    };
    
                    // panel
                    var panel_setting = app.docs.adm_qtables.post; // SETTING VALUE
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
                                        doc_id : 'adm_qtables/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/insert", _send_params, {
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
                }
                // ,
                // doEdit: function(params) {
                //     var self = this;
                //     var send_params = {
                //         doc_id: "adm_qtables/edit",
                //         cache :false
                //     };
                //     _.extend(send_params, params);
    
                //     // panel
                //     var panel_setting = app.docs.adm_qtables.edit; // SETTING VALUE
                //     var panel = Panel.onStart(panel_setting);

                //     // make url
                //     UrlUtil.pushUrlParams({
                //         mode : 'edit',
                //         id : params.id
                //     });
                //     UrlUtil.pushState('doEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                //     ProgressBar.show_progress();
                //     DocAsyncTask.doGetContents(panel_setting, send_params,{
                //         success: function(tpl, resp) {
                //             // $(panel.id+'_title').text(resp.msg.name.value);
                //             $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                //             {
                //                 // submit
                //                 DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                //                 {
                //                     ProgressBar.show_progress();

                //                     var _send_params = {
                //                         doc_id : 'adm_qtables/update'
                //                     };
                //                     _.extend(_send_params,form_params);

                //                     DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/update", _send_params, {
                //                         success : function(resp){
                //                             ProgressBar.close_progress();
                //                             Toast.show('',resp.msg,2000, {style:'success'});
    
                //                             self.doList(UrlUtil._url_params);
                //                             setTimeout(function(){
                //                                 history.go(-1);
                //                             },300);
                //                         },
                //                         fail : function(resp){
                //                             ProgressBar.close_progress();
                //                             alert(resp.msg);
                //                             if(!_.isUndefined(resp.fieldname)){
                //                                 $(panel.id+'_docs_contents #theDefaultForm #'+resp.fieldname).focus();
                //                             }
                //                         }
                //                     });
                //                 });
    
                //                 // close
                //                 ProgressBar.close_progress();
                //             });
                //         },
    
                //         fail : function(resp){
                //             ProgressBar.close_progress();
                //             alert(resp.msg);
                //         }
                //     });
                // }
            };

            QTablesActivity.doList(UrlUtil._url_params);

            // contents
            var QTablesDataActivity = 
            {
                doList: function(params) {
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        tname: params.tname,
                        page : (!_.isUndefined(params.page)) ? params.page : 1,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.list; // SETTING VALUE
                    panel_setting.title = params.tname;
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'data-list'
                    });
                    UrlUtil.pushState('doDataList', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // add
                                // $(panel.id+'_docs_contents #btn_add').unbind('click');
                                // $(panel.id+'_docs_contents #btn_add').on('click', function() {
                                //     self.doPost();
                                // });

                                // 검색
                                $(panel.id+'_docs_contents #data_search_from').submit(function(event) {
                                    event.preventDefault();
                                    var q = $('#q').val();
                                    var field = $('#field').val();
                                    _.extend(params, {page:1,field:field,q:q});
                                    self.doList(params);
                                });

                                // chk_all
                                $(panel.id+'_docs_contents #chk_all').unbind('click');
                                $(panel.id+'_docs_contents #chk_all').on('click', function() 
                                {
                                    if( $(this).is(':checked') ){ //전체선택
                                        $("input[name=chkid]:checkbox").prop("checked", true);
                                    }else{ // 전체해제                                
                                        $("input[name=chkid]:checkbox").prop("checked", false);
                                    }
                                });

                                // edit 
                                $(panel.id+'_docs_contents .data-list-edit').unbind('click');
                                $(panel.id+'_docs_contents .data-list-edit').on('click', function() 
                                {
                                    var this_id = $(this).data('id');
                                    self.doEdit({
                                        id : this_id,
                                        tname : params.tname
                                    });
                                });

                                // download
                                $(panel.id+'_docs_contents #btn_dwn_excel_data').unbind('click');
                                $(panel.id+'_docs_contents #btn_dwn_excel_data').on('click', function() 
                                {
                                    app.go_url(app.src+'/adm/query/tables/data/excel_download.php?tname='+params.tname);
                                });

                                // 전체 데이터 삭제 기능 
                                $(panel.id+'_docs_contents #btn_truncate_data').unbind('click');
                                $(panel.id+'_docs_contents #btn_truncate_data').on('click', function() 
                                {
                                    var cf = confirm('전체 데이터를 삭제하시겠습니까?'+"\n"+'삭제된 데이터는 복구 할 수 없습니다.');
                                    if(cf)
                                    {
                                        ProgressBar.show_progress();
                                        var _send_params = {
                                            tname : params.tname
                                        };
    
                                        DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/truncate", _send_params, {
                                            success : function(resp){
                                                ProgressBar.close_progress();
                                                Toast.show('',resp.msg,2000, {style:'success'});
        
                                                params.page = 1;
                                                self.doList(params);
                                            },
                                            fail : function(resp){
                                                ProgressBar.close_progress();
                                                alert(resp.msg);
                                            }
                                        });
                                    }
                                });

                                //선택 데이터 삭제 기능
                                $(panel.id+'_docs_contents #btn_del_data').unbind('click');
                                $(panel.id+'_docs_contents #btn_del_data').on('click', function() 
                                {
                                    var dellists = [];
                                    $('input[name="chkid"]').each(function() {
                                        if($(this).is(':checked')){
                                            dellists.push($(this).val());
                                        }
                                    });

                                    var dellists_str ='';
                                    if(dellists.length > 0){
                                        dellists_str = dellists.join(',');
                                    }

                                    if(dellists_str ==''){
                                        alert('삭제할 데이터를 선택하세요.');
                                        return;
                                    }

                                    var cf = confirm('선택한 데이터를 삭제하시겠습니까?'+"\n"+'삭제된 데이터는 복구 할 수 없습니다.');
                                    if(cf)
                                    {
                                        ProgressBar.show_progress();
                                        var _send_params = {
                                            tname : params.tname,
                                            dd : dellists_str
                                        };
    
                                        DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/delete", _send_params, {
                                            success : function(resp){
                                                ProgressBar.close_progress();
                                                Toast.show('',resp.msg,2000, {style:'success'});
        
                                                params.page = 1;
                                                self.doList(params);
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
                                    _.extend(params, {page:page});
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
                doEdit : function(params){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        tname: params.tname,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.edit; // SETTING VALUE
                    panel_setting.title = params.tname;
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'data-edit',
                        did : params.id
                    });
                    UrlUtil.pushState('doDataEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                
                                // close progress
                                ProgressBar.close_progress();
                            });
                        },
    
                        fail : function(resp){
                            ProgressBar.close_progress();
                            alert(resp.msg);
                        }
                    });
                },
                doShowScheme : function(params){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        tname: params.tname,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.scheme; // SETTING VALUE
                    panel_setting.title = params.tname;
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'data-scheme'
                    });
                    UrlUtil.pushState('doDataScheme', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // create by copy table scheme
                                $(panel.id+'_docs_contents #btn_create_bytable').unbind('click');
                                $(panel.id+'_docs_contents #btn_create_bytable').on('click', function(){
                                    self.doCreateByCopyTable(params, function(){
                                        QTablesActivity.doList(UrlUtil._url_params);
                                        history.go(-1);
                                    });
                                });

                                // 테이블명 변경하기
                                $(panel.id+'_docs_contents #btn_rename_table').unbind('click');
                                $(panel.id+'_docs_contents #btn_rename_table').on('click', function(){
                                    self.doRenameTable(params, function(){
                                        QTablesActivity.doList(UrlUtil._url_params);
                                        history.go(-1);
                                    });
                                });

                                // 데이터 목록 보기
                                $(panel.id+'_docs_contents #btn_show_datalist').unbind('click');
                                $(panel.id+'_docs_contents #btn_show_datalist').on('click', function(){
                                    self.doList(params);
                                });

                                // 테이블명 변경하기
                                $(panel.id+'_docs_contents #btn_show_table_scheme').unbind('click');
                                $(panel.id+'_docs_contents #btn_show_table_scheme').on('click', function(){
                                    self.doShowTableScheme(params);
                                });

                                // drop table
                                $(panel.id+'_docs_contents #btn_drop_table').unbind('click');
                                $(panel.id+'_docs_contents #btn_drop_table').on('click', function(){
                                    var cfd = confirm('테이블을 삭제(DROP)하시겠습니까?'+"\n"+'데이터를 복구 할 수 없습니다');
                                    if(cfd){
                                        DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/drop_table", {tname : params.tname}, {
                                            success : function(resp){
                                                ProgressBar.close_progress();
                                                Toast.show('',resp.msg,2000, {style:'success'});

                                                QTablesActivity.doList(UrlUtil._url_params);
                                                setTimeout(function(){
                                                    history.go(-1);
                                                },150);
                                            },
                                            fail : function(resp){
                                                ProgressBar.close_progress();
                                                alert(resp.msg);
                                            }
                                        });
                                    }
                                });

                                // 퀄럼 추가 
                                $(panel.id+'_docs_contents #btn_column_add').unbind('click');
                                $(panel.id+'_docs_contents #btn_column_add').on('click', function(){
                                    self.doColumnPost({tname:params.tname});
                                });

                                // 매직 퀄럼추가
                                $(panel.id+'_docs_contents #btn_column_add_magic').unbind('click');
                                $(panel.id+'_docs_contents #btn_column_add_magic').on('click', function(){
                                    alert('가자');
                                });

                                // 퀄럼 삭제                                
                                $(panel.id+'_docs_contents .btn_column_del').unbind('click');
                                $(panel.id+'_docs_contents .btn_column_del').on('click', function(){
                                    var column_name = $(this).data('id');
                                    var cfd = confirm('선택한 퀄럼을 삭제(DROP)하시겠습니까?'+"\n"+'다시 복구 할 수 없습니다');
                                    if(cfd){
                                        DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/drop_column", {
                                            tname : params.tname,
                                            column_name : column_name
                                        }, {
                                            success : function(resp){
                                                ProgressBar.close_progress();
                                                Toast.show('',resp.msg,2000, {style:'success'});

                                                self.doShowScheme(params);
                                            },
                                            fail : function(resp){
                                                ProgressBar.close_progress();
                                                alert(resp.msg);
                                            }
                                        });
                                    }
                                });

                                // 쿼럼 수정
                                $(panel.id+'_docs_contents .btn_column_edit').unbind('click');
                                $(panel.id+'_docs_contents .btn_column_edit').on('click', function(){
                                    var column_name = $(this).data('id');
                                    params.column_name = column_name;
                                    self.doColumnEdit(params);
                                });

                                // 퀄럼 순서                             
                                $(panel.id+'_docs_contents .up_ordinal_position').unbind('click');
                                $(panel.id+'_docs_contents .up_ordinal_position').on('click', function(){
                                    var column_name = $(this).data('id');
                                    var after_id = $(this).data('afterid');
                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/column_position", {
                                        tname : params.tname,
                                        column_name : column_name,
                                        after_id : after_id
                                    }, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});

                                            self.doShowScheme(params);
                                        },
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);
                                        }
                                    });
                                });

                                // index 추가 btn_column_index
                                $(panel.id+'_docs_contents #btn_column_index').unbind('click');
                                $(panel.id+'_docs_contents #btn_column_index').on('click', function()
                                {
                                    var idxlists = [];
                                    $('input[name="chk_column"]').each(function() {
                                        if($(this).is(':checked')){
                                            idxlists.push($(this).val());
                                        }
                                    });

                                    var idxlists_str ='';
                                    var idexlists_cnt = idxlists.length;
                                    if(idexlists_cnt > 0){
                                        idxlists_str = idxlists.join(',');
                                    }

                                    if(idxlists_str ==''){
                                        alert('INDEX에 등록한 퀄럼을 선택하세요.');
                                        return;
                                    }

                                    var send_params = {
                                        'idxlen' : idexlists_cnt,
                                        'idxlist' : idxlists_str,
                                        'tname' : params.tname
                                    };
                                    _.extend(params, send_params);

                                    self.doIndexColumnPost(params);
                                });

                                // index remove btn-rm-indexkey
                                $(panel.id+'_docs_contents .btn-rm-indexkey').unbind('click');
                                $(panel.id+'_docs_contents .btn-rm-indexkey').on('click', function()
                                {
                                    var this_idx_keyname = $(this).data('idxkeyname');
                                    var cfd = confirm('선택한 INDEX를 삭제(DROP)하시겠습니까?'+"\n"+'다시 복구 할 수 없습니다');
                                    if(cfd){
                                        DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/column_index_drop", {
                                            tname : params.tname,
                                            idx_key_name : this_idx_keyname
                                        }, {
                                            success : function(resp){
                                                ProgressBar.close_progress();
                                                Toast.show('',resp.msg,2000, {style:'success'});

                                                self.doShowScheme(params);
                                            },
                                            fail : function(resp){
                                                ProgressBar.close_progress();
                                                alert(resp.msg);
                                            }
                                        });
                                    }
                                });

                                // close progress
                                ProgressBar.close_progress();
                            });
                        },
    
                        fail : function(resp){
                            ProgressBar.close_progress();
                            alert(resp.msg);
                        }
                    });
                },
                doCreateByCopyTable : function(params, callbck){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        doc_id: 'adm_qtables/createbycopy',
                        tname: params.tname,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.createbycopy; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'createbycopy'
                    });
                    UrlUtil.pushState('doCreateByCopyScheme', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        // doc_id : 'adm_qtables/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/create_by_copyscheme", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});

                                            history.go(-1);
                                            
                                            setTimeout(function(){
                                                callbck();
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
                doRenameTable : function(params, callbck){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        doc_id: 'adm_qtables/rename',
                        tname: params.tname,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.rename; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'rename'
                    });
                    UrlUtil.pushState('doRenameTable', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        // doc_id : 'adm_qtables/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/rename_table", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});

                                            history.go(-1);
                                            
                                            setTimeout(function(){
                                                callbck();
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
                doShowTableScheme : function(params){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        tname: params.tname,
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.showtablescheme; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'showtablescheme'
                    });
                    UrlUtil.pushState('doShowTableScheme', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                $(panel.id+'_docs_contents #view_scheme_table').focus().select();
                                ProgressBar.close_progress();
                            });
                        },
    
                        fail : function(resp){
                            ProgressBar.close_progress();
                            alert(resp.msg);
                        }
                    });
                },
                doColumnEdit : function(params){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.column_edit; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'column_edit'
                    });
                    UrlUtil.pushState('doColumnEdit', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // data_type select
                                var int_type = resp.scheme.data_type.default[0].type;
                                var text_type = ["TEXT","MEDIUMTEXT","TINYTEXT","LONGTEXT","JSON","BLOB"];
                                $(panel.id+'_docs_contents #data_type').on('change', function(){
                                    var this_val = $(this).val();

                                    // unsigned
                                    var unsiged_val = _.indexOf(int_type, this_val);
                                    if(unsiged_val != -1){
                                        $(panel.id+'_docs_contents input[name="data_unsigned"').removeAttr('disabled');
                                    }else{
                                        $(panel.id+'_docs_contents input[name="data_unsigned"').attr('disabled','true');
                                    }

                                    // 데이터 길이
                                    var legnth_val = _.indexOf(text_type, this_val);
                                    if(legnth_val != -1){
                                        $(panel.id+'_docs_contents #data_length').attr('disabled','true');
                                    }else{
                                        $(panel.id+'_docs_contents #data_length').removeAttr('disabled');
                                    }
                                });

                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theColumnEditForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        // doc_id : 'adm_qtables/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/column_update", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});

                                            history.go(-1);
                                            
                                            setTimeout(function(){
                                                self.doShowScheme(params);
                                            },300);
                                        },
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);
                                            if(!_.isUndefined(resp.fieldname)){
                                                $(panel.id+'_docs_contents #theColumnEditForm #'+resp.fieldname).focus();
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
                doColumnPost : function(params){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.column_post; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'column_post'
                    });
                    UrlUtil.pushState('doColumnPost', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // data_type select
                                var int_type = resp.scheme.data_type.default[0].type;
                                var text_type = ["TEXT","MEDIUMTEXT","TINYTEXT","LONGTEXT","JSON","BLOB"];
                                $(panel.id+'_docs_contents #data_type').on('change', function(){
                                    var this_val = $(this).val();

                                    // unsigned
                                    var unsiged_val = _.indexOf(int_type, this_val);
                                    if(unsiged_val != -1){
                                        $(panel.id+'_docs_contents input[name="data_unsigned"').removeAttr('disabled');
                                    }else{
                                        $(panel.id+'_docs_contents input[name="data_unsigned"').attr('disabled','true');
                                    }

                                    // 데이터 길이
                                    var legnth_val = _.indexOf(text_type, this_val);
                                    if(legnth_val != -1){
                                        $(panel.id+'_docs_contents #data_length').attr('disabled','true');
                                    }else{
                                        $(panel.id+'_docs_contents #data_length').removeAttr('disabled');
                                    }
                                });

                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theColumnPostForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        // doc_id : 'adm_qtables/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/column_insert", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});

                                            history.go(-1);
                                            
                                            setTimeout(function(){
                                                self.doShowScheme(params);
                                            },300);
                                        },
                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);
                                            if(!_.isUndefined(resp.fieldname)){
                                                $(panel.id+'_docs_contents #theColumnPostForm #'+resp.fieldname).focus();
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
                doIndexColumnPost : function(params){
                    var self = this;
                    ProgressBar.show_progress();
    
                    var send_params = {
                        cache :false
                    };
                    _.extend(send_params, params);
    
                    // panel
                    var panel_setting = app.docs.adm_qdata.column_index_post; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'column_index_post'
                    });
                    UrlUtil.pushState('doColumnIndexPost', '', app.service_root_dir+'_adm/?'+$.param(params));
    
                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #theDefaultForm', function(form_params)
                                {
                                    ProgressBar.show_progress();

                                    var _send_params = {
                                        // doc_id : 'adm_qtables/insert'
                                    };
                                    _.extend(_send_params,form_params);

                                    DocAsyncTask.doPostMessage(app.src+"/adm/query/tables/data/column_index_insert", _send_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});

                                            history.go(-1);
                                            
                                            setTimeout(function(){
                                                self.doShowScheme(params);
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
                }
            };
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
