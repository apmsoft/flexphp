<?php
# session_start();
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# model
$model = new \Flex\Model\Model();
$model->description = <<<EOF

        <ul class='parent-menu-list'>
                                    <li>
                <a href="ref.strings.php">String Functions</a>

                                    <ul class='child-menu-list'>

                                                <li class="">
                            <a href="function.addcslashes.php" title="addcslashes">addcslashes</a>
                        </li>
                                                <li class="">
                            <a href="function.addslashes.php" title="addslashes">addslashes</a>
                        </li>
                                                <li class="">
                            <a href="function.bin2hex.php" title="bin2hex">bin2hex</a>
                        </li>
                                                <li class="">
                            <a href="function.chop.php" title="chop">chop</a>
                        </li>
                        
                    </ul>
                
            </li>
                        
                        <li>
                <span class="header">Deprecated</span>
                <ul class="child-menu-list">
                                    <li class="">
                        <a href="function.convert-cyr-string.php" title="convert_&#8203;cyr_&#8203;string">convert_&#8203;cyr_&#8203;string</a>
                    </li>
                                    <li class="">
                        <a href="function.hebrevc.php" title="hebrevc">hebrevc</a>
                    </li>
                                </ul>
            </li>
                    </ul>
    </aside>


  </div><!-- layout -->

    
 <!-- External and third party libraries. -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="/cached.php?t=1657730402&amp;f=/js/ext/hogan-3.0.2.min.js"></script>
<script src="/cached.php?t=1421837618&amp;f=/js/ext/typeahead.min.js"></script>
<script src="/cached.php?t=1657876202&amp;f=/js/ext/mousetrap.min.js"></script>
<script src="/cached.php?t=1657730402&amp;f=/js/ext/jquery.scrollTo.min.js"></script>
<script src="/cached.php?t=1653918602&amp;f=/js/search.js"></script>
<script src="/cached.php?t=1657891202&amp;f=/js/common.js"></script>

EOF;

# auth
$htmlXssChars = new \Flex\Html\HtmlXssChars( $model->description );

# 태그 지우기
Log::d('>>>>>>>> 태그 지우기');
Log::d($htmlXssChars->cleanTags());

# 태그 지우기 : a 태그만 허용
Log::d('>>>>>>>> 태그 지우기 : a 태그만 허용');
$htmlXssChars->setAllowTags('<a>');
$htmlXssChars->setAllowTags('<li>');
Log::d($htmlXssChars->cleanTags());

# XSS 태그제거
Log::d('>>>>>>>> XSS 위험 태그 제거');
Log::d($htmlXssChars->cleanXssTags());

# mail, href 자동 링크 만들기
$htmlXssChars = new \Flex\Html\HtmlXssChars( 'ex@gmail.com <br/> https://www.naver.com' );
Log::d('>>>>>>>> mail, href 자동 링크 만들기');
Log::d( $htmlXssChars->setAutoLink() );

# 웹사이트 주소에 http 있는지 체크 및 없으면 붙이기
$htmlXssChars = new \Flex\Html\HtmlXssChars( 'www.naver.com' );
Log::d('>>>>>>>> 웹사이트 주소에 http 있는지 체크 및 없으면 붙이기');
Log::d( $htmlXssChars->setHttpUrl() );

# HTML 에 하이라이트 만들기 (코드)
$htmlXssChars = new \Flex\Html\HtmlXssChars( '<a href="www.naver.com">네이버</a><br /><?php echo "dddd"; ?>' );
Log::d('>>>>>>>> HTML 에 하이라이트 만들기 (코딩)');
$htmlXssChars->setAutoLink();
Log::d( $htmlXssChars->getXHtmlHighlight() );

#===========================
$model->contents = '<script>alert(1);</script> <span style="color:#000;"> 스판</span><p>한줄</p> <frameset>frameset</frameset><a href="www.naver.com">네이버</a><br /><?php echo "dddd"; ?>';

# 본문을 TEXT 로만 출력
$htmlXssChars = new \Flex\Html\HtmlXssChars( $model->contents );
Log::d('TEXT');
Log::d( $htmlXssChars->getContext('TEXT') );

# 본문을 XSS 로만 출력
$htmlXssChars = new \Flex\Html\HtmlXssChars( $model->contents );
Log::d('XSS');
Log::d( $htmlXssChars->getContext('XSS') );

# 본문을 HTML 로만 출력
$htmlXssChars = new \Flex\Html\HtmlXssChars( $model->contents );
Log::d('HTML');
Log::d( $htmlXssChars->getContext('HTML') );

# 본문을 XHTML (코드) 로만 출력
$htmlXssChars = new \Flex\Html\HtmlXssChars( $model->contents );
Log::d('XHTML');
Log::d( $htmlXssChars->getContext('XHTML') );

# 본문을 XSS 로만 출력 : 허용태그
$htmlXssChars = new \Flex\Html\HtmlXssChars( $model->contents );
Log::d('XSS : 이벤트 허용 태그');
$htmlXssChars->setAllowTags('<a>');
$htmlXssChars->setAllowTags('<frameset>');
Log::d( $htmlXssChars->getContext('XSS') );
?>