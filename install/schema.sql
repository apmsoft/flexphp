--
-- 관리자정보
--
CREATE TABLE `fu3_adm` (
  `id` int(11) unsigned NOT NULL,
  `signdate` int(10) unsigned NOT NULL DEFAULT '0',
  `recently_connect_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '최근접속일',
  `logout_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '마지막로그아웃시간',
  `level` smallint(6) unsigned NOT NULL DEFAULT '0',
  `userid` varchar(35) NOT NULL COMMENT '회원아이디',
  `passwd` varchar(60) NOT NULL COMMENT '회원비밀번호',
  `name` varchar(24) NOT NULL COMMENT '이름',
  `signout_date` int(10) unsigned DEFAULT '0' COMMENT '탈퇴일자',
  `email` varchar(60) DEFAULT NULL,
  `allow_ipall` enum('n','y') NOT NULL DEFAULT 'y' COMMENT '모든IP에서접속허용',
  `allow_ip1` varchar(20) DEFAULT NULL COMMENT '접속허용ip1',
  `allow_ip2` varchar(20) DEFAULT NULL COMMENT '접속허용ip2',
  `allow_ip3` varchar(20) DEFAULT NULL COMMENT '접속허용ip3',
  `allow_mobile` enum('n','y') NOT NULL DEFAULT 'y' COMMENT '모바일접속허용',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='관리자정보';

--
-- 관리자 로그
--
CREATE TABLE `fu3_adm_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `muid` int(10) unsigned NOT NULL COMMENT '관리자회원번호',
  `description` varchar(255) NOT NULL COMMENT '행위',
  `signdate` int(10) NOT NULL COMMENT '시간',
  `ip` varchar(20) DEFAULT NULL COMMENT '접속 IP등록',
  PRIMARY KEY (`id`),
  KEY `muid` (`muid`,`signdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='관리자로그';

--
-- 기업/단체
--
CREATE TABLE `fu3_company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(45) NOT NULL DEFAULT '' COMMENT '업체/단체명',
  `number` varchar(45) NOT NULL DEFAULT '' COMMENT '사업자등록번호',
  `biz_type` varchar(40) NOT NULL DEFAULT '' COMMENT '사업구분',
  `ceo` varchar(45) NOT NULL DEFAULT '' COMMENT '대표',
  `open_date` varchar(10) DEFAULT NULL,
  `address1` varchar(200) NOT NULL DEFAULT '' COMMENT '사업장주소',
  `address2` varchar(200) DEFAULT NULL,
  `business` varchar(160) DEFAULT NULL,
  `event` varchar(160) DEFAULT NULL,
  `signdate` int(10) unsigned NOT NULL,
  `extract_id` varchar(60) NOT NULL,
  `tel` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `tax_email` varchar(60) DEFAULT NULL COMMENT '세금계산서전용이메일',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='거래처정보 ';

--
-- 기업/단체 첨부파일
--
CREATE TABLE `fu3_company_upfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `extract_id` varchar(60) NOT NULL COMMENT '구분',
  `is_regi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '최종등록처리완료 ',
  `regi_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
  `file_type` varchar(30) NOT NULL COMMENT '이미지및파일구분',
  `sfilename` varchar(50) NOT NULL COMMENT '저장파일명',
  `ofilename` varchar(100) NOT NULL COMMENT '실제파일명',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '파일크기바이트',
  `directory` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file_type` (`file_type`),
  KEY `extract_id` (`extract_id`),
  KEY `is_regi` (`is_regi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='파일업로드테이블목록';

--
-- 회원
--
CREATE TABLE `fu3_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `signdate` int(10) unsigned NOT NULL COMMENT '입사일 ',
  `up_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '정보수정일 ',
  `recently_connect_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '마지막접속일 ',
  `logout_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '마지막로그아웃시간',
  `alarm_readdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '새소식 최근 확인시간 ',
  `userid` varchar(50) DEFAULT NULL COMMENT '이메일 및 아이디',
  `passwd` varchar(100) NOT NULL COMMENT '비밀번호 ',
  `level` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '등급',
  `cellphone` varchar(20) NOT NULL COMMENT '휴대전화 ',
  `name` varchar(60) NOT NULL COMMENT '이름 ',
  `extract_id` varchar(60) NOT NULL COMMENT '사진 ',
  `introduce` varchar(250) DEFAULT NULL COMMENT '자기소개 ',
  `authemailkey` varchar(100) DEFAULT NULL COMMENT '이메일인증키',
  `is_push` enum('n','y') NOT NULL DEFAULT 'y',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '회사/소속 ',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원';

--
-- 회원 첨부파일
--
CREATE TABLE `fu3_member_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `extract_id` varchar(60) NOT NULL COMMENT '구분',
  `is_regi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '최종등록처리완료 ',
  `regi_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
  `file_type` varchar(30) NOT NULL COMMENT '이미지및파일구분',
  `sfilename` varchar(50) NOT NULL COMMENT '저장파일명',
  `ofilename` varchar(100) NOT NULL COMMENT '실제파일명',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '파일크기바이트',
  `directory` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file_type` (`file_type`),
  KEY `extract_id` (`extract_id`),
  KEY `is_regi` (`is_regi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='파일업로드테이블목록';

--
-- 팝업
--
CREATE TABLE `fu3_popup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `view_count` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(60) NOT NULL,
  `extract_id` varchar(60) NOT NULL DEFAULT '',
  `link` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `start_date` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='팝업'

--
-- 팝업 첨부파일
--
CREATE TABLE `fu3_popupfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `extract_id` varchar(60) NOT NULL COMMENT '구분',
  `is_regi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '최종등록처리완료 ',
  `regi_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
  `file_type` varchar(30) NOT NULL COMMENT '이미지및파일구분',
  `sfilename` varchar(50) NOT NULL COMMENT '저장파일명',
  `ofilename` varchar(100) NOT NULL COMMENT '실제파일명',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '파일크기바이트',
  `directory` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file_type` (`file_type`),
  KEY `extract_id` (`extract_id`),
  KEY `is_regi` (`is_regi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='팝업업로드파일'

--
-- 기본 첨부파일
--
CREATE TABLE `fu3_uploadfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `extract_id` varchar(60) NOT NULL COMMENT '구분',
  `is_regi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '최종등록처리완료 ',
  `regi_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
  `file_type` varchar(30) NOT NULL COMMENT '이미지및파일구분',
  `sfilename` varchar(50) NOT NULL COMMENT '저장파일명',
  `ofilename` varchar(100) NOT NULL COMMENT '실제파일명',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '파일크기바이트',
  `directory` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file_type` (`file_type`),
  KEY `extract_id` (`extract_id`),
  KEY `is_regi` (`is_regi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='파일업로드테이블목록'

--
-- 알람
--
CREATE TABLE `fu3_alarm` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(60) NOT NULL DEFAULT '',
  `msg` varchar(200) NOT NULL DEFAULT '',
  `param` varchar(250) DEFAULT '' COMMENT '파라메터',
  `signdate` int(10) unsigned NOT NULL DEFAULT '0',
  `isread` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `isread` (`isread`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='알림(새소식)'

--
-- 공지사항
--
CREATE TABLE `fu3_bbs_notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `signdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
  `muid` int(11) unsigned DEFAULT NULL,
  `headline` enum('n','y') DEFAULT NULL COMMENT '공지',
  `title` varchar(60) NOT NULL COMMENT '제목',
  `description` text COMMENT '내용',
  `extract_id` varchar(60) NOT NULL COMMENT '첨부파일토큰',
  `category` varchar(2) NOT NULL DEFAULT 'z' COMMENT '카테고리',
  PRIMARY KEY (`id`),
  KEY `headline` (`headline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='공지사항'