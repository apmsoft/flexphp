--
-- 쿠폰
--
CREATE TABLE `fu3_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(20) NOT NULL COMMENT '쿠폰번호',
  `title` varchar(60) NOT NULL COMMENT '제목',
  `start_date` date NOT NULL COMMENT '시작일',
  `end_date` date NOT NULL COMMENT '종료일',
  `summary` tinytext NOT NULL COMMENT '요약설명',
  `ea` int(10) unsigned NOT NULL DEFAULT '0',
  `down_count` int(10) NOT NULL DEFAULT '0',
  `extract_id` varchar(60) NOT NULL COMMENT '첨부파일키',
  `signdate` int(10) NOT NULL COMMENT '생성일',
  `muid` int(11) NOT NULL DEFAULT '0' COMMENT '생성회원번호',
  PRIMARY KEY (`id`),
  KEY `number` (`number`),
  KEY `sedate` (`start_date`,`end_date`),
  KEY `numsedate` (`number`,`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


