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
    });

    _.extend(Activity, {
        onCreate: function() {
            // event
            $('#myModal_btn_close').on('click', function() {
                history.go(-1);
            });
            $('#btn_bottom_close, #bottom_title').on('click', function() {
                history.go(-1);
            });
            $('#right_back_button, #right_title').on('click', function() {
                history.go(-1);
            });
            $('#rightside_back_button, #rightside_title').on('click', function() {
                history.go(-1);
            });

            // drawer menu            
            $('#btn-overview,#btn_draw_menu, #left_title, #drawer_menu, #back_drawer_menu,.drawer_menu_item').unbind('click');
            $('#btn-overview,#btn_draw_menu, #left_title, #drawer_menu, #back_drawer_menu,.drawer_menu_item').on('click',
                function(e) {
                    // e.preventDefault();

                    var this_id = $(this).attr('id');
                    switch (this_id) {
                        case 'left_title':
                            app.go_url('/_adm/');
                            break;

                        case 'btn-overview':
                            app.go_url('/_adm/');
                            break;
                        
                        case 'btn_draw_menu':
                            $("#drawer_menu").toggleClass('drawer_transitioned');
                            break;
                        
                        case 'drawer_menu':
                        case 'back_drawer_menu':
                            $('#drawer_menu').removeClass("drawer_transitioned");
                            break;
                        default :
                            var cur_href = $(this).attr('href');
                            app.go_url(url_href);
                    }
                });
        },
        onCreateView: function() 
        {
            // 게시판 통계
            require(['c3'],function(c3)
            {
                c3.generate({
                    bindto: "#bbs_statis",
                    data: {
                        columns: bbs_statics,
                        type: 'donut'
                    },
                    donut: {
                        title: "BBS Statistics"
                    }
                });
            });

            // 회원통계
            $('#member_statis').html('<h3>월별 회원가입 현황</h3><hr /><div id="mem_stat"></div><hr /><div id="mem_stat_table"></div>').promise().done(function(){
                require(['c3'],function(c3)
                {
                    c3.generate({
                        bindto: '#mem_stat',
                        data: {
                            x: 'x',
                            columns: member_statics,
                            type: 'area-spline'
                        },
                        zoom: {enabled: true}
                    });
                });

                var stable = member_statics;
                DocAsyncTask.doGetContents({
                    "panel": "left",
                    "title": null,
                    "frame": null,
                    "template": "/_adm/assets/apple/template/member/stat_table#tpl_member_stat_table",
                    "value": {result:'true',msg:stable}
                }, {cache:false}, {
                    success: function(tpl, resp) {
                        app.log(tpl);
                        $('#mem_stat_table').html(tpl);
                        ProgressBar.close_progress();
                    }
                });
            });
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

