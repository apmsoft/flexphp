<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @UPDATE	: 2010-02-16
----------------------------------------------------------*/
namespace Fus3\Image;

# purpose : 이미지 효과주기
class ImageDrawCircle extends ImageGDS
{
	private $allocate = array();
	
	public function filledarc($image,$cx,$cy,$width,$height,$start,$end,$color,$style){
		return imagefilledarc($image,$cx,$cy,$width,$height,$start,$end,$color,$style);
	}
	
	public function addAllocate(){
	}
	
	public function drowPie($width,$height){
		$this->im = parent::createTrueImage($width,$height);
		
		// allocate some solors
		$white    = imagecolorallocate($this->im, 0xFF, 0xFF, 0xFF);
		$gray     = imagecolorallocate($this->im, 0xC0, 0xC0, 0xC0);
		$darkgray = imagecolorallocate($this->im, 0x90, 0x90, 0x90);
		$navy     = imagecolorallocate($this->im, 0x00, 0x00, 0x80);
		$darknavy = imagecolorallocate($this->im, 0x00, 0x00, 0x50);
		$red      = imagecolorallocate($this->im, 0xFF, 0x00, 0x00);
		$darkred  = imagecolorallocate($this->im, 0x90, 0x00, 0x00);
		
		// make the 3D effect
		for ($i = 60; $i > 50; $i--){
		   imagefilledarc($this->im, 50, $i, 100, 50, 0, 45, $darknavy, IMG_ARC_PIE);
		   imagefilledarc($this->im, 50, $i, 100, 50, 45, 75 , $darkgray, IMG_ARC_PIE);
		   imagefilledarc($this->im, 50, $i, 100, 50, 75, 360 , $darkred, IMG_ARC_PIE);
		}
		
		imagefilledarc($this->im, 50, 50, 100, 50, 0, 45, $navy, IMG_ARC_PIE);
		imagefilledarc($this->im, 50, 50, 100, 50, 45, 75 , $gray, IMG_ARC_PIE);
		imagefilledarc($this->im, 50, 50, 100, 50, 75, 360 , $red, IMG_ARC_PIE);

		imagepng($this->im);
		imagedestroy($this->im);		
	}
}
?>