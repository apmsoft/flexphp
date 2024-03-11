<?php
namespace Flex\Annona\Calendars;

use \DateTime;

# Parent Class : DateTime::';
class Calendars extends DateTime
{
	public const __version = '2.5';
	# 년
	private $year = 0;

	# 이달
	# @ monthname : August
	# @ shortmonthname : Aug
	# @ lastdaydow : 그달마지막날이 무슨요일(3) 값
	# @ lastday : 이달의 마지막 날짜
	# @ firstdaydow : 이달의 첫째날이 속한 요일(3)
	private $month = 0;
	private $lastday, $lastdaydow;
	private $firstdaydow;
	private $monthname, $shortmonthname;

	# 오늘(일)
	# @ dow : (일=0,월=1,화=2,수=3,목=4,금=5,토=6)
	# @ dayname : Saturday
	# @ shortdayname : Sat
	# @ cur_week : 오늘 날짜가 속한주(2)째주
	private $day = 0;
	private $daydow, $dayname, $shortdayname;
	private $cur_week;

	# 이전 년월
	private $pre_year;
	private $pre_month;
	private $pre_week;

	# 다음 년월
	private $next_year;
	private $next_month;
	private $next_week;

	# 총 달력 데이타
	/**
	days_of_month[0] =array(1,2,3,4,5,6,7);
	days_of_month[1] =array(8,9,10,11,12,13,14);
	*/
	private $days_of_month = [];

	# 기념일 및 휴일설정
	private $memorial_arg = [];

	# 음력 관련
	private $sunargs= array(20000101,20000107,20000201,20000205,20000301,20000306,20000401,20000405,
		20000501,20000504,20000601,20000602,20000701,20000702,20000731,20000801,20000829,20000901,
		20000928,20001001,20001027,20001101,20001126,20001201,20001226,20010101,20010124,20010201,
		20010223,20010301,20010325,20010401,20010424,20010501,20010523,20010601,20010621,20010701,
		20010721,20010801,20010819,20010901,20010917,20011001,20011017,20011101,20011115,20011201,
		20011215,20020101,20020113,20020201,20020212,20020301,20020314,20020401,20020413,20020501,
		20020512,20020601,20020611,20020701,20020710,20020801,20020809,20020901,20020907,20021001,
		20021006,20021101,20021105,20021201,20021204,20030101,20030103,20030201,20030301,20030303,
		20030401,20030402,20030501,20030531,20030601,20030630,20030701,20030729,20030801,20030828,
		20030901,20030926,20031001,20031025,20031101,20031124,20031201,20031223,20040101,20040122,
		20040201,20040220,20040301,20040321,20040401,20040419,20040501,20040519,20040601,20040618,
		20040701,20040717,20040801,20040816,20040901,20040914,20041001,20041014,20041101,20041112,
		20041201,20041212,20050101,20050110,20050201,20050209,20050301,20050310,20050401,20050409,
		20050501,20050508,20050601,20050607,20050701,20050706,20050801,20050805,20050901,20050904,
		20051001,20051003,20051101,20051102,20051201,20051202,20051231,20060101,20060129,20060201,
		20060228,20060301,20060329,20060401,20060428,20060501,20060527,20060601,20060626,20060701,
		20060725,20060801,20060824,20060901,20060922,20061001,20061022,20061101,20061121,20061201,
		20061220,20070101,20070119,20070201,20070218,20070301,20070319,20070401,20070417,20070501,
		20070517,20070601,20070615,20070701,20070714,20070801,20070813,20070901,20070911,20071001,
		20071011,20071101,20071110,20071201,20071210,20080101,20080108,20080201,20080207,20080301,
		20080308,20080401,20080406,20080501,20080505,20080601,20080604,20080701,20080703,20080801,
		20080831,20080901,20080929,20081001,20081029,20081101,20081128,20081201,20081227,20090101,
		20090126,20090201,20090225,20090301,20090327,20090401,20090425,20090501,20090524,20090601,
		20090623,20090701,20090722,20090801,20090820,20090901,20090919,20091001,20091018,20091101,
		20091117,20091201,20091216,20100101,20100115,20100201,20100214,20100301,20100316,20100401,
		20100414,20100501,20100514,20100601,20100612,20100701,20100712,20100801,20100810,20100901,
		20100908,20101001,20101008,20101101,20101106,20101201,20101206,20110101,20110104,20110201,
		20110203,20110301,20110305,20110401,20110403,20110501,20110503,20110601,20110602,20110701,
		20110731,20110801,20110829,20110901,20110927,20111001,20111027,20111101,20111125,20111201,
		20111225,20120101,20120123,20120201,20120222,20120301,20120322,20120401,20120421,20120501,
		20120521,20120601,20120620,20120701,20120719,20120801,20120818,20120901,20120916,20121001,
		20121015,20121101,20121114,20121201,20121213,20130101,20130112,20130201,20130210,20130301,
		20130312,20130401,20130410,20130501,20130510,20130601,20130609,20130701,20130708,20130801,
		20130807,20130901,20130905,20131001,20131005,20131101,20131103,20131201,20131203,20140101,
		20140131,20140201,20140301,20140331,20140401,20140429,20140501,20140529,20140601,20140627,
		20140701,20140727,20140801,20140825,20140901,20140924,20141001,20141024,20141101,20141122,
		20141201,20141222,20150101,20150120,20150201,20150219,20150301,20150320,20150401,20150419,
		20150501,20150518,20150601,20150616,20150701,20150716,20150801,20150814,20150901,20150913,
		20151001,20151013,20151101,20151112,20151201,20151211,20160101,20160110,20160201,20160209,
		20160301,20160309,20160401,20160407,20160501,20160507,20160601,20160605,20160701,20160704,
		20160801,20160803,20160901,20161001,20161031,20161101,20161129,20161201,20161229,20170101,
		20170128,20170201,20170227,20170301,20170328,20170401,20170426,20170501,20170526,20170601,
		20170624,20170701,20170723,20170801,20170822,20170901,20170920,20171001,20171020,20171101,
		20171118,20171201,20171218,20180101,20180117,20180201,20180216,20180301,20180317,20180401,
		20180416,20180501,20180515,20180601,20180614,20180701,20180713,20180801,20180811,20180901,
		20180910,20181001,20181009,20181101,20181108,20181201,20181207,20190101,20190106,20190201,
		20190205,20190301,20190307,20190401,20190405,20190501,20190505,20190601,20190603,20190701,
		20190703,20190801,20190830,20190901,20190929,20191001,20191028,20191101,20191127,20191201,
		20191226,20200101,20200125,20200201,20200224,20200301,20200324,20200401,20200423,20200501,
		20200523,20200601,20200621,20200701,20200721,20200801,20200819,20200901,20200917,20201001,
		20201017,20201101,20201115,20201201,20201215,20210101,20210113,20210201,20210212,20210301,
		20210313,20210401,20210412,20210501,20210512,20210601,20210610,20210701,20210710,20210801,
		20210808,20210901,20210907,20211001,20211006,20211101,20211105,20211201,20211204,20220101,
		20220103,20220201,20220301,20220303,20220401,20220501,20220530,20220601,20220629,20220701,
		20220729,20220801,20220827,20220901,20220926,20221001,20221025,20221101,20221124,20221201,
		20221223,20230101,20230122,20230201,20230220,20230301,20230322,20230401,20230420,20230501,
		20230520,20230601,20230618,20230701,20230718,20230801,20230816,20230901,20230915,20231001,
		20231015,20231101,20231113,20231201,20231213,20240101,20240111,20240201,20240210,20240301,
		20240310,20240401,20240409,20240501,20240508,20240601,20240606,20240701,20240706,20240801,
		20240804,20240901,20240903,20241001,20241003,20241101,20241201,20241231,20250101,20250129,
		20250201,20250228,20250301,20250329,20250401,20250428,20250501,20250527,20250601,20250625,
		20250701,20250725,20250801,20250823,20250901,20250922,20251001,20251021,20251101,20251120,
		20251201,20251220,20260101,20260119,20260201,20260217,20260301,20260319,20260401,20260417,
		20260501,20260517,20260601,20260615,20260701,20260714,20260801,20260813,20260901,20260911,
		20261001,20261011,20261101,20261109,20261201,20261209,20270101,20270108,20270201,20270207,
		20270301,20270308,20270401,20270407,20270501,20270506,20270601,20270605,20270701,20270704,
		20270801,20270802,20270901,20270930,20271001,20271029,20271101,20271128,20271201,20271228,
		20280101,20280127,20280201,20280225,20280301,20280326,20280401,20280425,20280501,20280524,
		20280601,20280623,20280701,20280722,20280801,20280820,20280901,20280919,20281001,20281018,
		20281101,20281116,20281201,20281216,20290101,20290115,20290201,20290213,20290301,20290315,
		20290401,20290414,20290501,20290513,20290601,20290612,20290701,20290712,20290801,20290810,
		20290901,20290908,20291001,20291008,20291101,20291106,20291201,20291205,20300101,20300104,
		20300201,20300203,20300301,20300304,20300401,20300403,20300501,20300502,20300601,20300701,
		20300730,20300801,20300829,20300901,20300927,20301001,20301027,20301101,20301125,20301201,
		20301225,20310101,20310123,20310201,20310222,20310301,20310323,20310401,20310422,20310501,
		20310521,20310601,20310620,20310701,20310719,20310801,20310818,20310901,20310917,20311001,
		20311016,20311101,20311115,20311201,20311214,20320101,20320113,20320201,20320211,20320301,
		20320312,20320401,20320410,20320501,20320509,20320601,20320608,20320701,20320707,20320801,
		20320806,20320901,20320905,20321001,20321004,20321101,20321103,20321201,20321203,20330101,
		20330131,20330201,20330301,20330331,20330401,20330429,20330501,20330528,20330601,20330627,
		20330701,20330726,20330801,20330825,20330901,20330923,20331001,20331023,20331101,20331122,
		20331201,20331222,20340101,20340120,20340201,20340219,20340301,20340320,20340401,20340419,
		20340501,20340518,20340601,20340616,20340701,20340716,20340801,20340814,20340901,20340913,
		20341001,20341012,20341101,20341111,20341201,20341211,20350101,20350110,20350201,20350208,
		20350301,20350310,20350401,20350408,20350501,20350508,20350601,20350606,20350701,20350705,
		20350801,20350804,20350901,20350902,20351001,20351031,20351101,20351130,20351201,20351229,
		20360101,20360128,20360201,20360227,20360301,20360328,20360401,20360426,20360501,20360526,
		20360601,20360624,20360701,20360723,20360801,20360822,20360901,20360920,20361001,20361019,
		20361101,20361118,20361201,20361217,20370101,20370116,20370201,20370215,20370301,20370317,
		20370401,20370416,20370501,20370515,20370601,20370614,20370701,20370713,20370801,20370811,
		20370901,20370910,20371001,20371009,20371101,20371107,20371201,20371207,20380101,20380105,
		20380201,20380204,20380301,20380306,20380401,20380405,20380501,20380504,20380601,20380603,
		20380701,20380702,20380801,20380830,20380901,20380929,20381001,20381028,20381101,20381126,
		20381201,20381226,20390101,20390124,20390201,20390223,20390301,20390325,20390401,20390423,
		20390501,20390523,20390601,20390622,20390701,20390721,20390801,20390820,20390901,20390918,
		20391001,20391018,20391101,20391116,20391201,20391216,20400101,20400114,20400201,20400212,
		20400301,20400313,20400401,20400411,20400501,20400511,20400601,20400610,20400701,20400709,
		20400801,20400808,20400901,20400907,20401001,20401006,20401101,20401105,20401201,20401204,
		20410101,20410103,20410201,20410301,20410303,20410401,20410430,20410501,20410530,20410601,
		20410628,20410701,20410728,20410801,20410827,20410901,20410925,20411001,20411025,20411101,
		20411124,20411201,20411223,20420101,20420122,20420201,20420220,20420301,20420322,20420401,
		20420420,20420501,20420519,20420601,20420618,20420701,20420717,20420801,20420816,20420901,
		20420914,20421001,20421014,20421101,20421113,20421201,20421213,20430101,20430111,20430201,
		20430210,20430301,20430311,20430401,20430410,20430501,20430509,20430601,20430607,20430701,
		20430707,20430801,20430805,20430901,20430903,20431001,20431003,20431101,20431102,20431201,
		20431231,20440101
		);

	private $moonargs = array (
		19991125,19991201,19991226,20000101,20000126,20000201,20000227,20000301,20000327,20000401,20000429,
		20000501,20000530,20000601,20000701,20000702,20000801,20000804,20000901,20000904,20001001,20001006,
		20001101,20001106,20001201,20001207,20010101,20010109,20010201,20010207,20010301,20010308,20010401,
		20010408,20010401,20010410,20010501,20010511,20010601,20010612,20010701,20010714,20010801,20010815,
		20010901,20010916,20011001,20011017,20011101,20011118,20011201,20011220,20020101,20020118,20020201,
		20020219,20020301,20020319,20020401,20020421,20020501,20020521,20020601,20020623,20020701,20020724,
		20020801,20020825,20020901,20020927,20021001,20021027,20021101,20021129,20021201,20030101,20030129,
		20030201,20030230,20030301,20030401,20030501,20030502,20030601,20030602,20030701,20030704,20030801,
		20030805,20030901,20030906,20031001,20031008,20031101,20031108,20031201,20031210,20040101,20040111,
		20040201,20040211,20040201,20040212,20040301,20040313,20040401,20040414,20040501,20040514,20040601,
		20040616,20040701,20040717,20040801,20040818,20040901,20040919,20041001,20041020,20041101,20041121,
		20041201,20041223,20050101,20050121,20050201,20050223,20050301,20050323,20050401,20050425,20050501,
		20050525,20050601,20050627,20050701,20050728,20050801,20050828,20050901,20050930,20051001,20051030,
		20051101,20051201,20051202,20060101,20060104,20060201,20060202,20060301,20060304,20060401,20060404,
		20060501,20060506,20060601,20060606,20060701,20060708,20060701,20060709,20060801,20060810,20060901,
		20060911,20061001,20061011,20061101,20061113,20061201,20061214,20070101,20070112,20070201,20070214,
		20070301,20070315,20070401,20070416,20070501,20070517,20070601,20070619,20070701,20070720,20070801,
		20070821,20070901,20070922,20071001,20071022,20071101,20071123,20071201,20071225,20080101,20080124,
		20080201,20080225,20080301,20080326,20080401,20080428,20080501,20080528,20080601,20080701,20080801,
		20080802,20080901,20080903,20081001,20081004,20081101,20081104,20081201,20081206,20090101,20090107,
		20090201,20090205,20090301,20090306,20090401,20090407,20090501,20090509,20090501,20090509,20090601,
		20090611,20090701,20090713,20090801,20090813,20090901,20090915,20091001,20091015,20091101,20091117,
		20091201,20091218,20100101,20100116,20100201,20100217,20100301,20100318,20100401,20100419,20100501,
		20100520,20100601,20100621,20100701,20100723,20100801,20100824,20100901,20100925,20101001,20101026,
		20101101,20101127,20101201,20101229,20110101,20110127,20110201,20110228,20110301,20110329,20110401,
		20110430,20110501,20110601,20110701,20110702,20110801,20110804,20110901,20110905,20111001,20111006,
		20111101,20111107,20111201,20111208,20120101,20120110,20120201,20120209,20120301,20120311,20120301,
		20120311,20120401,20120412,20120501,20120512,20120601,20120614,20120701,20120715,20120801,20120816,
		20120901,20120918,20121001,20121018,20121101,20121120,20121201,20121221,20130101,20130120,20130201,
		20130221,20130301,20130322,20130401,20130423,20130501,20130523,20130601,20130625,20130701,20130726,
		20130801,20130827,20130901,20130928,20131001,20131029,20131101,20131201,20140101,20140102,20140201,
		20140301,20140302,20140401,20140403,20140501,20140504,20140601,20140605,20140701,20140706,20140801,
		20140808,20140901,20140908,20140901,20140909,20141001,20141010,20141101,20141111,20141201,20141213,
		20150101,20150111,20150201,20150213,20150301,20150313,20150401,20150415,20150501,20150516,20150601,
		20150617,20150701,20150719,20150801,20150819,20150901,20150920,20151001,20151020,20151101,20151122,
		20151201,20151223,20160101,20160122,20160201,20160224,20160301,20160325,20160401,20160426,20160501,
		20160527,20160601,20160629,20160701,20160801,20160901,20161001,20161002,20161101,20161103,20161201,
		20161204,20170101,20170105,20170201,20170203,20170301,20170305,20170401,20170406,20170501,20170507,
		20170501,20170508,20170601,20170610,20170701,20170711,20170801,20170812,20170901,20170913,20171001,
		20171014,20171101,20171115,20171201,20171216,20180101,20180114,20180201,20180216,20180301,20180316,
		20180401,20180418,20180501,20180518,20180601,20180620,20180701,20180722,20180801,20180822,20180901,
		20180924,20181001,20181024,20181101,20181126,20181201,20181227,20190101,20190125,20190201,20190226,
		20190301,20190327,20190401,20190428,20190501,20190529,20190601,20190701,20190801,20190803,20190901,
		20190903,20191001,20191005,20191101,20191105,20191201,20191207,20200101,20200108,20200201,20200207,
		20200301,20200309,20200401,20200409,20200401,20200410,20200501,20200511,20200601,20200612,20200701,
		20200714,20200801,20200815,20200901,20200916,20201001,20201017,20201101,20201118,20201201,20201220,
		20210101,20210118,20210201,20210220,20210301,20210320,20210401,20210421,20210501,20210522,20210601,
		20210623,20210701,20210725,20210801,20210825,20210901,20210927,20211001,20211027,20211101,20211129,
		20211201,20220101,20220129,20220201,20220301,20220401,20220501,20220503,20220601,20220603,20220701,
		20220704,20220801,20220806,20220901,20220906,20221001,20221008,20221101,20221108,20221201,20221210,
		20230101,20230111,20230201,20230210,20230201,20230211,20230301,20230312,20230401,20230413,20230501,
		20230514,20230601,20230615,20230701,20230717,20230801,20230817,20230901,20230918,20231001,20231019,
		20231101,20231120,20231201,20231222,20240101,20240121,20240201,20240223,20240301,20240323,20240401,
		20240425,20240501,20240526,20240601,20240627,20240701,20240729,20240801,20240829,20240901,20241001,
		20241101,20241201,20241202,20250101,20250104,20250201,20250202,20250301,20250304,20250401,20250404,
		20250501,20250506,20250601,20250607,20250601,20250608,20250701,20250710,20250801,20250810,20250901,
		20250912,20251001,20251012,20251101,20251113,20251201,20251214,20260101,20260113,20260201,20260214,
		20260301,20260315,20260401,20260416,20260501,20260517,20260601,20260619,20260701,20260720,20260801,
		20260821,20260901,20260922,20261001,20261023,20261101,20261124,20261201,20261225,20270101,20270123,
		20270201,20270225,20270301,20270325,20270401,20270427,20270501,20270527,20270601,20270629,20270701,
		20270801,20270901,20270902,20271001,20271004,20271101,20271104,20271201,20271205,20280101,20280106,
		20280201,20280206,20280301,20280307,20280401,20280407,20280501,20280509,20280501,20280509,20280601,
		20280611,20280701,20280713,20280801,20280813,20280901,20280915,20281001,20281016,20281101,20281117,
		20281201,20281218,20290101,20290117,20290201,20290218,20290301,20290318,20290401,20290420,20290501,
		20290520,20290601,20290621,20290701,20290723,20290801,20290824,20290901,20290925,20291001,20291026,
		20291101,20291128,20291201,20291229,20300101,20300127,20300201,20300229,20300301,20300329,20300401,
		20300501,20300601,20300701,20300703,20300801,20300804,20300901,20300905,20301001,20301006,20301101,
		20301107,20301201,20301208,20310101,20310110,20310201,20310208,20310301,20310310,20310301,20310310,
		20310401,20310412,20310501,20310512,20310601,20310614,20310701,20310715,20310801,20310815,20310901,
		20310917,20311001,20311017,20311101,20311119,20311201,20311220,20320101,20320120,20320201,20320221,
		20320301,20320322,20320401,20320424,20320501,20320524,20320601,20320626,20320701,20320727,20320801,
		20320827,20320901,20320929,20321001,20321029,20321101,20321201,20330101,20330102,20330201,20330301,
		20330302,20330401,20330403,20330501,20330505,20330601,20330605,20330701,20330707,20330701,20330708,
		20330801,20330809,20330901,20330910,20331001,20331010,20331101,20331111,20331201,20331213,20340101,
		20340111,20340201,20340213,20340301,20340313,20340401,20340415,20340501,20340516,20340601,20340617,
		20340701,20340719,20340801,20340819,20340901,20340921,20341001,20341021,20341101,20341122,20341201,
		20341223,20350101,20350122,20350201,20350223,20350301,20350324,20350401,20350425,20350501,20350526,
		20350601,20350628,20350701,20350729,20350801,20350901,20351001,20351002,20351101,20351102,20351201,
		20351204,20360101,20360105,20360201,20360204,20360301,20360305,20360401,20360406,20360501,20360507,
		20360601,20360608,20360601,20360610,20360701,20360711,20360801,20360812,20360901,20360914,20361001,
		20361014,20361101,20361116,20361201,20361217,20370101,20370115,20370201,20370216,20370301,20370316,
		20370401,20370418,20370501,20370518,20370601,20370620,20370701,20370722,20370801,20370822,20370901,
		20370924,20371001,20371025,20371101,20371126,20371201,20371228,20380101,20380126,20380201,20380227,
		20380301,20380327,20380401,20380429,20380501,20380529,20380601,20380701,20380801,20380803,20380901,
		20380903,20381001,20381005,20381101,20381106,20381201,20381207,20390101,20390109,20390201,20390207,
		20390301,20390308,20390401,20390409,20390501,20390510,20390501,20390510,20390601,20390612,20390701,
		20390713,20390801,20390814,20390901,20390915,20391001,20391016,20391101,20391117,20391201,20391219,
		20400101,20400119,20400201,20400220,20400301,20400321,20400401,20400422,20400501,20400522,20400601,
		20400624,20400701,20400725,20400801,20400825,20400901,20400927,20401001,20401027,20401101,20401129,
		20401201,20410101,20410129,20410201,20410301,20410401,20410402,20410501,20410503,20410601,20410604,
		20410701,20410705,20410801,20410806,20410901,20410907,20411001,20411008,20411101,20411108,20411201,
		20411210,20420101,20420111,20420201,20420210,20420201,20420211,20420301,20420312,20420401,20420414,
		20420501,20420514,20420601,20420616,20420701,20420717,20420801,20420818,20420901,20420919,20421001,
		20421019,20421101,20421120,20421201,20421222,20430101,20430120,20430201,20430222,20430301,20430322,
		20430401,20430424,20430501,20430525,20430601,20430626,20430701,20430728,20430801,20430829,20430901,
		20430930,20431001,20431101,20431201,20431202);

	#@ void
	# Y-m-d H:i:s
	public function __construct($times){
		parent::__construct($times);
		self::resetTodayDate();
	}

	#@ void
	#날짜 리셋
	public function resetTodayDate() : void{
		$ymd_args    = explode('-',$this->format('Y-m-d'));
		$this->year  = $ymd_args[0];
		$this->month = $ymd_args[1];
		$this->day   = $ymd_args[2];

		self::fromJd();
		self::set_pre_next_date();
	}

	#@ void
	# 오늘날짜에 속한 정보들을 얻는다
	public function fromJd() :void{
		if(function_exists('unixtojd'))
		{
			$today_mktime =unixtojd(mktime(0,0,0,$this->month,$this->day, $this->year));
			$today_args =cal_from_jd($today_mktime, CAL_GREGORIAN);
			if(is_array($today_args))
			{
				#month
				$this->monthname      = $today_args['monthname'];
				$this->shortmonthname = (isset($today_args['abbrevmonthname'])) ? $today_args['abbrevmonthname'] : substr($today_args['monthname'],0,3);

				#day
				$this->daydow       = $today_args['dow'];
				$this->dayname      = $today_args['dayname'];
				$this->shortdayname = $today_args['abbrevdayname'];
				$this->firstdaydow  = date('w',mktime(0,0,0,$this->month,1,$this->year));
			}
			$this->lastday    = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
			$this->lastdaydow = date('w',mktime(0,0,0,$this->month,$this->lastday,$this->year));
		}else{
			$this->daydow		 = date('w',mktime(0,0,0,$this->month,$this->day,$this->year));
			$this->firstdaydow   = date('w',mktime(0,0,0,$this->month,1,$this->year));
			$this->lastday		 = date("t",mktime(0,0,1,$this->month,1,$this->year));
			$this->lastdaydow	 = date('w',mktime(0,0,0,$this->month,$this->lastday,$this->year));
		}
	}

	#@ void
	# 음력, 양력 기념일 데이타를 양력으로 통일 시킴
	/**
	= 입력 데이타 형식
	[0] =array(
		'date'         =>[0000-01-01|2012-01-01],	#날짜
		'smtype'       =>[s|m],						#양력|음력
		'repeat'       =>[y|m|n],					#반복(년,월,없음)
		'holiday'      =>[0|1],						#휴일여부,
		'holiday_plus' =>[0|1],						#설날,추석같이 특이한 휴일설정일경우(추가휴일여부)
		'title'        =>신정							#제목
	)
	*/
	public function set_memorials(Array $memorials=array()) : void
	{
		$count=count($memorials);
		for($i=0; $i<$count; $i++)
		{
			if(isset($memorials[$i]))
			{
				$m             =&$memorials[$i];
				$date_args     = explode('-',$m['date']);
				$this_int_date = $this->year.$date_args[1].$date_args[2];
				$int_date      = $date_args[0].$date_args[1].$date_args[2];
				$holiday_plus  = $m['holiday_plus'];

				# 반복아닌기념일
				# y : 년, m : 월, n : 반복없음
				if($m['smtype']=='m'){ # 음력
					switch($m['repeat']){
						case 'y' :
							self::set_memorials_holiday(self::get_moon2sun($this_int_date), $m['holiday'], $m['title'], $holiday_plus);
							break;
						case 'm':
							for($si=1; $si<13; $si++)
								self::set_memorials_holiday(self::get_moon2sun($date_args[0].sprintf("%02d",$si).$date_args[2]), $m['holiday'], $m['title'],$holiday_plus);
							break;
						default :
							self::set_memorials_holiday(self::get_moon2sun($int_date), $m['holiday'], $m['title'],$holiday_plus);
					}
				}else{ # 양력
					switch($m['repeat']){
						case 'y' :
							self::set_memorials_holiday($this_int_date, $m['holiday'], $m['title'], $holiday_plus);
							break;
						case 'm':
							for($si=1; $si<13; $si++)
								self::set_memorials_holiday($this->year.sprintf("%02d",$si).$date_args[2], $m['holiday'], $m['title'],$holiday_plus);
							break;
						default:
							self::set_memorials_holiday($this_int_date, $m['holiday'], $m['title'],$holiday_plus);
					}
				}
			}
		}
	}

	#@ void
	# 기념일 데이타를 입력
	private function set_memorials_holiday($int_date, $holiday, $holiday_title,$holiday_plus) : void{
		$this->memorial_arg[$int_date] = ['holiday'=>$holiday,'title'=>$holiday_title];
		if($holiday_plus==1){
			$this->memorial_arg[$int_date-1] = ['holiday'=>$holiday,'title'=>''];
			$this->memorial_arg[$int_date+1] = ['holiday'=>$holiday,'title'=>''];
		}
	}

	#현재달력 구하기
	public function set_days_of_month() : void
	{
		$x=0;

		# 이전달
		if(function_exists('cal_days_in_month')){
			$pre_lastday = cal_days_in_month(CAL_GREGORIAN, $this->pre_month, $this->pre_year);
		}else{
			$pre_lastday = date("t",mktime(0,0,1,$this->pre_month,1,$this->pre_year));
		}

		if($this->firstdaydow==0) $s_pre_day=$pre_lastday-6;
		else $s_pre_day=$pre_lastday-($this->firstdaydow-1);

		for($i=$s_pre_day; $i<=$pre_lastday; $i++)
		{
			$tmp_date = sprintf("%04d-%02d-%02d", $this->pre_year,$this->pre_month,$i);
			$int_date = intval(str_replace('-','',$tmp_date));
			$this->days_of_month[$x][] =array(
				'date'        => $tmp_date, 
				'day'         => $i, 
				'moon'        => '',
				'holiday'     => '',
				'event_title' => '',
				'this_month'  => ''
			);
			$num=date('w',mktime(0,0,0,$this->pre_month,$i,$this->pre_year));
			if($num== 6) $x++;
		}

		# 현재달
		for($j=1; $j<=$this->lastday; $j++)
		{
			$tmp_date = sprintf("%04d-%02d-%02d", $this->year,$this->month,$j);
			$int_date = intval(str_replace('-','',$tmp_date));
			$int_day  = intval($this->day);

			#10일에 한번씩 [음력날짜] 계산 및 표기
			$moon_date='';
			if($j%10==0){
				$moon_date = self::get_sun2moon($int_date);
				$moon_date = substr($moon_date,4,2).'.'.substr($moon_date,-2);
			}

			# 기념일 및 휴일
			$holiday='';
			$event_title='';
			if(isset($this->memorial_arg[$int_date])){
				if(isset($this->memorial_arg[$int_date]['holiday']) && $this->memorial_arg[$int_date]['holiday']==1) $holiday = 1;
				$event_title = (isset($this->memorial_arg[$int_date]['title'])) ? $this->memorial_arg[$int_date]['title'] : '';
			}

			# 달력
			$this->days_of_month[$x][] =[
				'date'        => $tmp_date, 
				'day'         => $j, 
				'moon'        => $moon_date,
				'holiday'     => $holiday,
				'event_title' => $event_title,
				'this_month'  => 1
			];
			$num=date('w',mktime(0,0,0,$this->month,$j,$this->year));
			if($j==$int_day){
				$this->pre_week  = $x-1;
				$this->cur_week  = $x;
				$this->next_week = $x+1;
			}
			if($num== 6) $x++;
		}

		# 다음달
		$nk=1;
		$snxt=($this->lastdaydow==6) ? 0 : $this->lastdaydow+1;
		for($k=$snxt;$k<7; $k++)
		{
			$tmp_date=sprintf("%04d-%02d-%02d", $this->next_year,$this->next_month,$nk);
			$int_date =intval(str_replace('-','',$tmp_date));
			$this->days_of_month[$x][] =[
				'date'        => $tmp_date, 
				'day'         => $nk, 
				'moon'        => '','holiday'=>'',
				'event_title' => '',
				'this_month'  => ''
			];
			$num=date('w',mktime(0,0,0,$this->next_month,$nk,$this->next_year));
			$nk++;
			if($num== 6) $x++;
		}
	}

	# 이전 년월, 다음 년월 구하기
	public function set_pre_next_date() : void
	{
		$prev_year = $this->year-1;
		$next_year = $this->year+1;
		$month     = intval($this->month);
		if($month==1){
			$this->pre_year   = $prev_year;
			$this->next_year  = $this->year;
			$this->pre_month  = 12;
			$this->next_month = sprintf("%02d",$month+1);
		}
		else if($month==12){
			$this->pre_year   = $this->year;
			$this->next_year  = $next_year;
			$this->pre_month  = sprintf("%02d",$month-1);
			$this->next_month = 1;
		}
		else if($month !=1 && $month !=12)
		{
			$this->pre_year   = $this->year;
			$this->next_year  = $this->year;
			$this->pre_month  = sprintf("%02d",$month-1);
			$this->next_month = sprintf("%02d",$month+1);
		}
	}

	# 날짜수정 DAY
	public function modifyDay(int|string $day) : void{
		$this->modify($day." day");
		self::resetTodayDate();
	}

	# 날짜수정 WEEK
	public function modifyWeek(int|string $week) : void{
		$this->modify($week." week");
		self::resetTodayDate();
	}

	# 날짜수정 MONTH
	public function modifyMonth(int|string $month) : void{
		$this->modify($month." month");
		self::resetTodayDate();
	}

	#이전주에 해당하는 마지막일을 가지고 온다
	public function get_pre_week_last_date() : date{
		$args = [];
		$pre_date = '';
		if(isset($this->days_of_month[$this->cur_week])){
			$args = $this->days_of_month[$this->cur_week];
		}

		if(isset($args[0]) && isset($args[0]['date'])){
			parent::__construct($args[0]['date']);
			$this->modify('-1 day');
			$pre_date = $this->format('Y-m-d');
		}
	return $pre_date;
	}

	#다음주에 해당하는 첫일을 가지고 온다
	public function get_next_week_first_date() : date{
		$args = [];
		$nxt_date = '';
		if(isset($this->days_of_month[$this->cur_week])){
			$args = $this->days_of_month[$this->cur_week];
		}

		if(isset($args[6]) && isset($args[6]['date'])){
			parent::__construct($args[6]['date']);
			$this->modify('+1 day');
			$nxt_date = $this->format('Y-m-d');
		}
	return $nxt_date;
	}

	# 해당해의 띠
	public function get_zodiac_sign() : string{
		$zodiac_sign_args = ['원숭이','닭','개','돼지','쥐','소','범','토끼','용','뱀','말','양'];
		$ddikey = intval($this->year % 12);
	return $zodiac_sign_args[$ddikey];
	}

	# 육십갑자
	public function get_sexagenary_cycle() : string{
		$tengan = ['경','신','임','계','갑','을','병','정','무','기'];
		$tenji	= ['신','유','술','해','자','축','인','묘','진','사','오','미'];

		$n1 = substr($this->year, -1);
		$n2 = intval($this->year % 12);

	return $tengan[$n1].$tenji[$n2];
	}

	#@ return
	# 양력->음력
	# intdate : 20101020
	public function get_sun2moon($intdate) : int{
		return self::date_binary_search($this->sunargs,$this->moonargs,$intdate);
	}

	# 음력->양력
	public function get_moon2sun($intdate) : int{
		return self::date_binary_search($this->moonargs,$this->sunargs,$intdate);
	}

	# 음<->양 계산메소드
	public function date_binary_search(&$haystack,&$haystack2, &$needle) : mixed
	{
		$high = count($haystack);
		$low = 0;
		if( $needle < $haystack[$low] || $needle > $haystack[$high-1] ){
			//throw new ErrorException("error function date_binary_search");
			return false;
		}

		while ($high - $low > 1){
			$mid = (int)($high + $low) / 2;
			if ($haystack[$mid] < $needle) $low = $mid;
			else $high = $mid;
		}

		if($high == count($haystack) || $haystack[$high] != $needle) {
			return $haystack2[$low] + ($needle-$haystack[$low]); // 배열에서 찾은 값이 없으므로, 날자를 계산하여 리턴해 준다.
		}else{
			return $haystack2[$high];
		}
	}

	#@ return
	# 프라퍼티 값 가져오기
	public function __get($propertyname) : mixed{ 
		return $this->{$propertyname}; 
	}
}
?>
