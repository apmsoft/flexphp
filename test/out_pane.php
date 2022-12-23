<?php
use Flex\Annona\App\App;
use Flex\Annona\Log\Log;
use Flex\Annona\R\R;

use Flex\Annona\Out\OutPane;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

/**
 * 자바스크립트를 활요한 웹페이지 강제 이동용
 */

# 메세지 출력 및 지정 경로로 이동
#OutPane::window_location('http://flexvue.fancyupsoft.com', '화면을 이동하시겠습니까?');

# 메세지 출력 후 뒤로 가기
#OutPane::history_go('이전 페이지로 이동합니다',-1);

# 메세지 출력 후 팝업창 자신 닫기
#OutPane::window_close('팝업창을 닫습니다');

# 팝업 창에서 본페이지 이동 시키기
#OutPane::opener_location(string $url);

# 자바스크립트 prompt 창을 통해 데이타 값 받기
#OutPane::input_prompt('이름을 입력하세요', '당신의 이름은 ');

# 자바스크립트 confirm 창을 통해 true/false 값 받기
OutPane::window_confirm('FlexVUE 페이지로 이동하시겠습니까?', $true_url = 'http://flexvue.fancyupsoft.com', $false_url='http://naver.com');
?>