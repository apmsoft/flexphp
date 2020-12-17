--
-- 적립금
--
CREATE TABLE `fu2_accumulated` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `muid` int(11) unsigned NOT NULL,
  `money` int(10) NOT NULL,
  `total` int(10) unsigned NOT NULL COMMENT '총금액 ',
  `signdate` int(10) NOT NULL,
  `memo` varchar(255) NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='적림금 ';


--
-- 상품
--
CREATE TABLE `fu2_item` (
  `id` int(11) unsigned NOT NULL,
  `gid` varchar(52) NOT NULL COMMENT '그룹',
  `signdate` int(10) unsigned NOT NULL COMMENT '등록일 ',
  `title` varchar(100) NOT NULL COMMENT '상품명 ',
  `price` int(10) unsigned NOT NULL COMMENT '상품가 ',
  `sale_price` int(10) NOT NULL DEFAULT '0' COMMENT '할인가격',
  `accumulated_money` int(10) unsigned NOT NULL DEFAULT '0',
  `sold_out` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '품절 ',
  `option1` varchar(60) NOT NULL COMMENT '옵션(필수)1 제목',
  `option2` varchar(60) NOT NULL COMMENT '옵션2 제목',
  `delivery_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '배송료',
  `is_after_delivery` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '착불 ',
  `individual_delivery` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '개별배송비',
  `description` text NOT NULL COMMENT '상세설명',
  `extract_id` varchar(40) NOT NULL DEFAULT '' COMMENT '첨부파일 연결 키',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='상품 ';

--
-- 장바구니
--
CREATE TABLE `fu2_item_cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cartid` varchar(45) NOT NULL COMMENT '카트아이디 ',
  `cart_mode` enum('c','d') NOT NULL DEFAULT 'c' COMMENT '카트물건모드(d:바로구매,c:장바구니)',
  `muid` int(11) unsigned NOT NULL COMMENT '회원번호 ',
  `item_id` int(11) unsigned NOT NULL COMMENT '상품id',
  `option1_id` int(11) unsigned NOT NULL,
  `option1_title` varchar(100) NOT NULL COMMENT '옵션1제목',
  `option1_price` int(10) unsigned NOT NULL,
  `option2_id` int(11) unsigned NOT NULL DEFAULT '0',
  `option2_title` varchar(100) DEFAULT NULL COMMENT '옵션2제목',
  `option2_price` int(10) unsigned NOT NULL DEFAULT '0',
  `option_count` smallint(5) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL COMMENT '총합(1개가격)',
  `signdate` int(10) unsigned NOT NULL COMMENT '등록일',
  PRIMARY KEY (`id`),
  KEY `cartid` (`cartid`),
  KEY `muid` (`muid`),
  KEY `item_id` (`item_id`),
  KEY `cart_mode` (`cart_mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='장바구니';

--
-- 상품그룹
--
CREATE TABLE `fu2_item_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` decimal(30,20) NOT NULL DEFAULT '0.00000000000000000000' COMMENT '그룹키',
  `group_title` varchar(60) NOT NULL DEFAULT '' COMMENT '그룹명',
  `reply_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '자식 그룹 카운터',
  `item_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록된 상품갯수',
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='상품그룹';

--
-- 나의 배송지주소목록
--
CREATE TABLE `fu2_item_my_postcode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `muid` int(11) unsigned NOT NULL,
  `postcode` varchar(6) NOT NULL,
  `address1` varchar(160) NOT NULL,
  `address2` varchar(160) NOT NULL,
  `signdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `muid` (`muid`),
  KEY `postcode` (`postcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='나의 배송지주소목록';

--
-- 상품옵션1
--
CREATE TABLE `fu2_item_option` (
  `op_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `option_id` varchar(30) NOT NULL COMMENT '상품ID',
  `option_token` varchar(60) NOT NULL,
  `option_title` varchar(100) NOT NULL,
  `option_price` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`op_id`),
  KEY `token` (`option_token`,`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='상품옵션1';

--
-- 주문정보
--
CREATE TABLE `fu2_item_order_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orderid` varchar(40) NOT NULL COMMENT '주문번호',
  `order_mode` char(1) NOT NULL DEFAULT 'c' COMMENT '구매형태(d:바로구매,c:장바구니)',
  `muid` int(11) unsigned NOT NULL DEFAULT '0',
  `passwd` varchar(100) NOT NULL COMMENT '주문비밀번호',
  `o_name` varchar(45) NOT NULL COMMENT '주문자성명',
  `o_email` varchar(60) NOT NULL COMMENT '주문자이메일',
  `o_hp` varchar(45) NOT NULL COMMENT '주문자휴대폰',
  `r_name` varchar(45) NOT NULL COMMENT '배송지성명',
  `r_hp` varchar(45) NOT NULL COMMENT '배송지휴대폰',
  `r_tel` varchar(45) NOT NULL COMMENT '배송지연락처',
  `r_postcode` varchar(6) NOT NULL COMMENT '배송지우편번호',
  `r_address` varchar(255) NOT NULL COMMENT '배송지주소|상세주소',
  `r_memo` varchar(255) DEFAULT NULL COMMENT '배송메모',
  `delivery_way` varchar(10) NOT NULL COMMENT '배송방법',
  `order_date` datetime NOT NULL COMMENT '주문일',
  `total_item_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '총상품가격 ',
  `use_accumulated_money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '적립금사용 ',
  `total_sale_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '세일금액 ',
  `total_delivery_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '총배송료',
  `total_accumulated_money` int(10) NOT NULL COMMENT '총예정적립금액(구매확정시자동 등록)',
  `total` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '총구매금액',
  `payment_way` varchar(4) NOT NULL COMMENT '결제방법',
  `payment_status` varchar(4) NOT NULL COMMENT '결재상태',
  `payment_bank` varchar(4) NOT NULL COMMENT '입금은행(키)',
  `shipper_code` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '배송업체코드 ',
  `delivery_code` varchar(60) DEFAULT NULL COMMENT '배송장번호',
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`),
  KEY `payment_status` (`payment_status`),
  KEY `order_date` (`order_date`),
  KEY `payment_way` (`payment_way`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='주문정보';


--
-- 주문상품
--
CREATE TABLE `fu2_item_order_item` (
  `oitid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orderid` varchar(18) NOT NULL COMMENT '주문번호',
  `item_id` int(11) unsigned NOT NULL COMMENT '상품id',
  `item_title` varchar(100) NOT NULL COMMENT '상품명',
  `item_gid` varchar(52) NOT NULL COMMENT '그룹',
  `individual_delivery` enum('n','y') NOT NULL COMMENT '개별배송 ',
  `is_after_delivery` enum('n','y') NOT NULL COMMENT '착불 ',
  `option1` varchar(60) NOT NULL COMMENT '옵션1제목 ',
  `option1_title` varchar(100) NOT NULL COMMENT '옵션1제목',
  `option1_price` int(10) unsigned NOT NULL,
  `option2` varchar(60) DEFAULT NULL COMMENT '옵션2제목 ',
  `option2_title` varchar(100) DEFAULT NULL COMMENT '옵션2제목',
  `option2_price` int(10) unsigned NOT NULL,
  `option_count` smallint(5) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL COMMENT '총합(상품가격*갯수)',
  `option_total_accmoney` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '예상적립금액 ',
  `extract_id` varchar(40) NOT NULL,
  PRIMARY KEY (`oitid`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='주문상품';

--
-- 배송업체
--
CREATE TABLE `fu2_item_shipper` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_print` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '출력',
  `name` varchar(60) NOT NULL COMMENT '배송업체명 ',
  `tel` varchar(16) DEFAULT NULL COMMENT '배송업체연락처',
  `url` varchar(255) NOT NULL COMMENT '배송추적경로',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='배송업체';

--
-- 파일업로드테이블목록
--
CREATE TABLE `fu2_item_uploadfiles` (
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







