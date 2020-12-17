<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @version 	: 1.0.4
----------------------------------------------------------*/
namespace Fus3\Date;

use \DateTime;
use \ErrorException;

# Parent Class : DateTime::';
# purpose : 날짜/시간에 필요한 것들을 다룬다
class DateTimes extends DateTime{
	private $datetimev;
	private $wkr_args = array('Mon'=>'월','Tue'=>'화','Wed'=>'수','Thu'=>'목','Fri'=>'금');
	private $mkr_args = array('Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,'Jul'=>7,'Aug'=>8,'Sep'=>9,'Oct'=>10,'Nov'=>11,'Dec'=>12);

	#@ void
	# times -> Y-m-d H:i:s || now || ()
	public function __construct($times=''){
		if($times =='now' || $times==''){
			parent::__construct($times);
			$this->datetimev = parent::format('Y-m-d H:i:s');
		}else{
			if(is_numeric($times)){
				$times = '@'.$times;
				$this->datetimev = parent::format('Y-m-d H:i:s');
			}
			parent::__construct($times);
			$this->datetimev = parent::format('Y-m-d H:i:s');
		}
	}

	#@ void
	# 날짜가 유효한지 체크한다
	public function checkdateEx(){
		# 날짜 입력 체크
		$ymd_args = explode('-',$this->format('Y-m-d'));
		if(is_array($ymd_args)){
			if(!checkdate($ymd_args[1],$ymd_args[2],$ymd_args[0])){
				$this->datetimev = '';
				throw new ErrorException(R::$sysmsg['e_date']);
			}
		}
	}

	#@ return boolean
	# 어떠한 특정날짜에서 일정기간(1일, 3일)기간이 지났는지를 알아보고자 할때
	# int days
	public function wasPassed($days)
	{
		$result = false;
		$timestamp = $this->format('U');
		$today_times= mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time())) - ($days*86400);
		#$today_times= time() - ($days*86400);
		if($timestamp > $today_times) $result = true;
		return $result;
	}

	#@ return date (Y-m-d)
	# 특정날짜를 기준으로 며칠전(1일전, 2일전) 날짜를  알고자 할때
	# int days
	public function dateBefore($days){
		$datetimez= $this->format('U');
		$regs= $datetimez - ($days*86400);
		$result=date('Y-m-d',$regs);

		return $result;
	}

	#@ return date (Y-m-d)
	## 어떠한 특정날짜에서로부터 며칠(3일)뒤의 날짜가 언제인지 알아낸다
	# int days
	public function dateAfter($days)
	{
		$datetimez	= $this->format('U');
		$regs= $datetimez + ($days*86400);
		$result=date('Y-m-d',$regs);

		return $result;
	}

	#@ return int
	# 오늘 날짜를 기준으로 어떠한 특정날짜에 도달하기 위해 며칠이 남았는지 리턴(1일 남았음, 2일 남았음)
	public function daysBeforeDDay()
	{
		$result = 0;
		$oneday_timenum = 86400;
		$enddate_tmpdate = $this->format('U');
		$today_times= $enddate_tmpdate - mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time()));

		$result = (int)($today_times/$oneday_timenum);
		return $result;
	}

	#@ return int
	# 오늘 날자를 기준으로 입력된 날짜가 며칠이나 지났는지 (1일지남, 2일지남)
	public function daysAfterDDay()
	{
		$result =0;
		$oneday_timenum = 86400;
		$startdate_tmpdate = $this->format('U');
		$today_times= mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time())) - $startdate_tmpdate;

		$result = (int)($today_times/$oneday_timenum);
		return $result;
	}

	#@ return String
	## 24시간 이전 값은 시간/분/초 단위로 표기
	public function timeLeft24H()
	{
		$result = '';
		$datetimez = $this->format('U');
		$timestamp = (time() - $datetimez);
		$time = intVal($timestamp / 60);

		if ($time<60){# 분
			$result = $time.'분전';
		}else{# 시간 : 분
			$hour = floor($time/60);
			if($hour>=24){
				$result = '1일전';
			}else{
				$minute = $time - (60*$hour);
				$result = $hour.'시간 '.$minute.'분전';
			}
		}
		return $result;
	}

	# 프라퍼티 값 가져오기
	public function __get($propertyname){
		return $this->{$propertyname};
	}
}
?>
