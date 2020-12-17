// 콜백함수
function onReady($, _, Backbone) {
    _url_params = UrlUtil.getURL2JSON();
    app.log(JSON.stringify(_url_params));

    ProgressBar.initialize();

    // init 
    app.initialize({});

    // event
    $('#left_back_button, #left_title').on('click', function() {
        history.back();
    });

    // mdl input
	$('.mdl-textfield__input').on('focusin', function(){
		$(this).parent().addClass("is-focused");
	});
	$('.mdl-textfield__input').on('focusout', function(){
		$(this).parent().removeClass("is-focused");
		if($(this).val()){
			$(this).parent().addClass('is-dirty');
		}else{
			$(this).parent().removeClass('is-dirty');
		}
    });
    
    // var classes = ["bounceIn", "pulse"];
    $(".container-sm").each(function(index) {
        // var rno = Math.round(Math.random()*1);
        // var _classname = classes[rno];
        $(this).animateCss('slideInUp');
    });

    // login
    DocAsyncTask.doSubmit('#theLoginForm', function(form_params){
        var send_params = {
            doc_id : 'adm/login'
        };
        _.extend(send_params, form_params);

        ProgressBar.show_progress();
        DocAsyncTask.doPostMessage(app.src+"/adm/adm/login.regi", send_params, 
            {
                success : function(resp){
                    ProgressBar.close_progress();
                    app.go_url(app.service_root_dir+'_adm/');
                },
                fail : function(resp){
                    ProgressBar.close_progress();
                    alert(resp.msg);
                    if (resp.msg_code == 'w_stay_logged_in') {
                        app.go_url(app.service_root_dir+'_adm/logout.php');
                    }else{
                        $('#theLoginForm #'+resp.fieldname).focus();
                    }
                }
            }
        );
    });

    $('#userid').focus();

    ProgressBar.close_progress();
}