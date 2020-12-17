--
-- 시나리오
--
CREATE TABLE `fu3_chat_scenario` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `title` varchar(70) NOT NULL COMMENT '시나리오제목',
  `signdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='챗 시나리오'

--
-- 시나리오 학습노트
--
CREATE TABLE `fu3_chat_scenario_note` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `scenario_id` int(11) unsigned DEFAULT NULL COMMENT '시나리오id',
  `msg` varchar(255) NOT NULL COMMENT '예상 질문 및 태그',
  PRIMARY KEY (`id`),
  KEY `scenario_id` (`scenario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='챗 시나리오 학습노트'

--
-- 시나리오 메세지
--
CREATE TABLE `fu3_chat_scenario_msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',
  `scenario_id` int(11) unsigned DEFAULT NULL COMMENT '시나리오id',
  `fid` decimal(30,20) NOT NULL DEFAULT '0.00000000000000000000' COMMENT '패밀리키',
  `question` varchar(255) NOT NULL COMMENT '질문',
  `extra_service` varchar(40) NOT NULL COMMENT '부가서비스',
  `extract_id` varchar(60) NOT NULL COMMENT '첨부파일키',
  `signdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '등록일',
  PRIMARY KEY (`id`),
  KEY `sfid` (`scenario_id`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='챗 시나리오 메세지'

--
-- 시나리오 태그
--
CREATE TABLE `fu3_chat_scenario_tags` (
  `tag` varchar(80) NOT NULL,
  `scenario_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '사나리오id',
  KEY `tag` (`tag`),
  KEY `scenario_id` (`scenario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='챗 시나리오 태그'