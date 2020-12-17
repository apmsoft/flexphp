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
        "adm_popup" : {
            "list" : {
                "panel": "left",
                "title": null,
                "frame": null,
                "template": "/_adm/coconut/popup/list#tpl_popup_list",
                "value": app.src+"/adm/document/queue"
            },
            "write" : {
                "panel": "bottom",
                "title": "팝업등록",
                "frame": null,
                "template": "/_adm/coconut/popup/post#tpl_popup_post",
                "value": app.src+"/adm/document/queue"
            },
            "modify" : {
                "panel": "bottom",
                "title": "팝업수정",
                "frame": null,
                "template": "/_adm/coconut/template/default/edit#tpl_default_edit",
                "value": app.src+"/adm/document/queue"
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
            var PopupActivity = {
                doc_id: 'adm_popup/list',
                doList: function(params) {
                    var self = this;
                    ProgressBar.show_progress();

                    var send_params = {
                        doc_id: self.doc_id,
                        page:(!_.isUndefined(params.page)) ? params.page : 1,
                        cache:false
                    };
                    _.extend(send_params, params);

                    // panel
                    var panel_setting = app.docs.adm_popup.list; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                // 스크롤포지션
                                $("#left .mdl-layout__content").animate({scrollTop: 0}, 100, "swing");

                                // 삭제
                                $(panel.id+'_docs_contents .btn_delete').unbind('click');
                                $(panel.id+'_docs_contents .btn_delete').on('click', function() {
                                    var vr = confirm("삭제 하시겠습니까?");
                                    if (vr == true) {
                                        var this_data_id = $(this).data('id');
                                        self.doDelete({id:this_data_id});
                                        return;
                                    }
                                });

                                // 추가
                                $(panel.id+'_docs_contents #btn_add').unbind('click');
                                $(panel.id+'_docs_contents #btn_add').on('click',function(){
                                    self.doWrite(params);
                                });

                                // raw
                                $(panel.id+'_docs_contents .raw').unbind('click');
                                $(panel.id+'_docs_contents .raw').on('click', function() {
                                    var this_data_id = $(this).data('id');
                                    self.doModify({ id: this_data_id });
                                });

                                // 페이징
                                $(panel.id+'_docs_contents .page-link').unbind('click');
                                $(panel.id+'_docs_contents .page-link').on('click', function() {
                                    var page = $(this).data('page');
                                    self.doList({page:page});
                                });

                                // 검색
                                $(panel.id+'_docs_contents #thePopupSearchForm').submit(function(event) {
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
                            ProgressBar.show_progress();
                            alert(resp.msg);
                        }
                    });
                },
                doWrite: function(params) {
                    var self = this;

                    var send_params = {
                        doc_id: 'adm_popup/post',
                        cache :false
                    };
                    _.extend(send_params, params);

                    // panel
                    var panel_setting = app.docs.adm_popup.write; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);

                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'post'
                    });
                    UrlUtil.pushState('doPopupWrite', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

                    DocAsyncTask.doGetContents(panel_setting, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() {
                                
                                require(['DateFormat', 'datepicker/datepicker'], function(DateFormat,datepicker)
                                {
                                    // event 날짜 기간
                                    $('.pop_term').unbind('click');
                                    $('.pop_term').on('click', function() {
                                        var this_term = $(this).data("term");
                                        var forday = parseInt(this_term);
                                        var new_date = new Date();
                                        var sDate = $('#start_date').val();
                                        if (!_.isUndefined(sDate) && sDate != '') {
                                            new_date = new Date(sDate);
                                        }
                                        $('#start_date, input[name=start_date]').val($.format.date(new_date, "yyyy-MM-dd"));
                                        $('#end_date, input[name=end_date]').val($.format.date(new Date(new_date.getTime() + (forday * 24 * 60 * 60 * 1000)), "yyyy-MM-dd"));

                                    });

                                    // datepicker
                                    datepicker.initialize(['#start_date', '#end_date'],function(){

                                    });
                                });

                                // submit
                                DocAsyncTask.doSubmit(panel.id+'_docs_contents #thePopupPostForm', function(form_params){
                                    ProgressBar.show_progress();

                                    _.extend(form_params, {
                                        doc_id : 'adm_popup/insert'
                                    });
                                    DocAsyncTask.doPostMessage(app.src+"/adm/document/stack", form_params, {
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
                                                $(panel.id+'_docs_contents #thePopupPostForm #'+resp.fieldname).focus();
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
                        doc_id: 'adm_popup/edit',
                        cache :false
                    };
                    _.extend(send_params, params);

                    // panel
                    var panel_setting = app.docs.adm_popup.modify; // SETTING VALUE
                    var panel = Panel.onStart(panel_setting);
                    
                    // make url
                    UrlUtil.pushUrlParams({
                        mode : 'edit'
                    });
                    UrlUtil.pushState('doPopupEdit', '', app.service_root_dir+'_adm/?'+$.param(UrlUtil._url_params));

                    DocAsyncTask.doGetContents(app.docs.adm_popup.modify, send_params,{
                        success: function(tpl, resp) {
                            $(panel.id+'_docs_contents').html(tpl).promise().done(function() 
                            {
                                require(['DateFormat', 'datepicker/datepicker'], function(DateFormat,datepicker)
                                {
                                    // event 날짜 기간
                                    $('.pop_term').unbind('click');
                                    $('.pop_term').on('click', function() {
                                        var this_term = $(this).data("term");
                                        var forday = parseInt(this_term);
                                        var new_date = new Date();
                                        var sDate = $('#start_date').val();
                                        if (!_.isUndefined(sDate) && sDate != '') {
                                            new_date = new Date(sDate);
                                        }
                                        $('#start_date, input[name=start_date]').val($.format.date(new_date, "yyyy-MM-dd"));
                                        $('#end_date, input[name=end_date]').val($.format.date(new Date(new_date.getTime() + (forday * 24 * 60 * 60 * 1000)), "yyyy-MM-dd"));

                                    });

                                    // datepicker
                                    datepicker.initialize(['#start_date', '#end_date'], function(){
                                        
                                    });
                                });

                                // uploadfiles regi
                                // require(['uploadfiles/regi', 'libs/js/function/image_manager'], function(uploadfiles_regi, fun) {
                                //     var is_print_file = [];
                                //     uploadfiles_regi.initialize(['#mulitplefileuploader', app.docs.adm_popup_uploadfiles.write, {
                                //             token: $('#extract_id').val(),
                                //             doc_id: self.doc_id,
                                //             dragDrop: 'true',
                                //             maxFileCount: 1,
                                //             acceptFiles: 'jpg,jpeg,png'
                                //         }],

                                //         // uploadfiles 준비되면 onload 콜백
                                //         function(obj) {
                                //             DocAsyncTask.doGetContents(app.docs.adm_popup_uploadfiles.load, {
                                //                 token: $('#extract_id').val(),
                                //                 doc_id: self.doc_id,
                                //                 cache : false
                                //             }, {
                                //                 success : function(tpl, resp){
                                //                     _.each(resp.msg, function(files) {
                                //                         obj.createProgress(
                                //                             files['sfilename'],
                                //                             files['ofilename'],
                                //                             files['fullname'].replace('/s/', '/'),
                                //                             files['file_size'],
                                //                             files['file_type'],
                                //                             files['image_size']
                                //                         );
                                //                     });

                                //                     $('.ajax-file-upload-preview').unbind('click');
                                //                     $('.ajax-file-upload-preview').on('click', function() {
                                //                         var data_filename = $(this).data('filename');
                                //                         if (!_.contains(is_print_file, data_filename)) {
                                //                             var data_width = $(this).data('width');
                                //                             var data_height = $(this).data('height');
                                //                             var img_src = $(this).attr('src');
                                //                             var image_resize = calculateAspectRatioFit(data_width, data_height, 400, 400);
                                //                             $('.note-editable').append('<div><a href="' + img_src + '" target="_blank"><img class="data-gallery lazy" data-filename="' + data_filename + '" data-original="' + img_src + '" src=' + img_src + ' style="width:' + image_resize.width + 'px; height:' + image_resize.height + 'px;"></a></div>');

                                //                             is_print_file.push(data_filename);
                                //                         }
                                //                     });
                                //                 },
                                //                 fail : function(resp){
                                //                     alert(resp.msg);
                                //                 }
                                //             });
                                //         },

                                //         // 등록 성공콜백
                                //         function(data) {
                                //             $('.ajax-file-upload-preview').unbind('click');
                                //             $('.ajax-file-upload-preview').on('click', function() {
                                //                 var data_filename = $(this).data('filename');
                                //                 if (!_.contains(is_print_file, data_filename)) {
                                //                     var data_width = $(this).data('width');
                                //                     var data_height = $(this).data('height');
                                //                     var img_src = $(this).attr('src');
                                //                     var image_resize = calculateAspectRatioFit(data_width, data_height, 400, 400);
                                //                     is_print_file.push(data_filename);
                                //                 }
                                //             });
                                //         },

                                //         // 삭제 이벤트 발생시 콜백
                                //         function(data) {
                                //             // 첨부파일삭제
                                //             DocAsyncTask.doPostMessage("/src/uploadfiles/uploadfiles_delete.regi",{
                                //                 token: $('#extract_id').val(),
                                //                 doc_id: self.doc_id,
                                //                 op: 'delete',
                                //                 name: data.sfilename
                                //             }, {
                                //                 success : function(resp){
                                //                     if (data.file_type == 'image'){
                                //                         findRemoveImage('.data-gallery', data.sfilename);
                                //                     }
                                //                 },
                                //                 fail : function(resp){
                                //                     alert(resp.msg);
                                //                 }
                                //             });
                                //         });
                                // });

                                // submit
                                DocAsyncTask.doSubmit('#theDefaultForm', function(form_params){
                                    ProgressBar.show_progress();

                                    _.extend(form_params, {
                                        doc_id : 'adm_popup/update'
                                    });
                                    DocAsyncTask.doPostMessage(app.src+"/adm/document/stack", form_params, {
                                        success : function(resp){
                                            ProgressBar.close_progress();
                                            Toast.show('',resp.msg,2000, {style:'success'});
                                            self.doList(UrlUtil._url_params);
                                        },

                                        fail : function(resp){
                                            ProgressBar.close_progress();
                                            alert(resp.msg);                                    
                                            if(!_.isUndefined(resp.fieldname)){
                                                $('#theDefaultForm #'+resp.fieldname).focus();
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
                        doc_id: self.doc_id,
                        page : 1
                    };
                    _.extend(send_params, params);

                    ProgressBar.show_progress();
                    DocAsyncTask.doPostMessage("/src/document/document_delete.regi", send_params, {
                        success : function(resp){
                            ProgressBar.close_progress();
                            Toast.show('',resp.msg,2000, {style:'success'});
                            self.doList(send_params);
                        },

                        fail : function(resp){
                            ProgressBar.close_progress();
                            alert(resp.msg);
                        }
                    });
                }
            };

            PopupActivity.doList(UrlUtil._url_params);
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
