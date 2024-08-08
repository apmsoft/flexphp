<?php
namespace Flex\Annona\Image;

# purpose : 이미지 효과주기
class ImageGDS
{
	public const __version = '1.3';
	public $filename;

	public $im;
	private $quality = 100;
	private $bgcolor = 0x7fffffff;
	private $fontsrc, $fontangle=0, $color = [0,0,0], $fontsize = 20, $x=5, $y=5;

	# 시작
	public function __construct(string|null $filename=null){
		if($filename && !file_exists($filename)) {
			throw new \Exception(__METHOD__.' '.$filename,__LINE__);
		}

		if(!is_null($filename)){
			$this->filename = $filename;
		}
	}

	# void 퀄리티 설정
	public function setCompressionQuality(int $quality) : void
	{
		$this->quality = $quality;
	}

	# 칼라 채우기
	public function setFilledrectangle(mixed $image,int $x1, int $y1, int $x2, int $y2, string $color) : mixed
	{
		if(false === ($im = imagefilledrectangle($image,$x1,$y1,$x2,$y2,$color))) return false;
	return $im;
	}

	# 칼라 채우기 RGB
	public function setColorallocate(mixed $image, int $r, int $g, int $b) : mixed
	{
		if(0 > ($im = imagecolorallocate($image,$r,$g,$b))) return false;
	return $im;
	}

	# alpha
	public function setAlphablending(mixed $image,bool $boolean=false) : void
	{
		imagealphablending($image, $boolean);
	}

	# alpha
	public function setSavealpha(mixed $image,bool $boolean=false) : void
	{
		imagesavealpha($image, $boolean);
	}

	public function setFttext(mixed $image, int $fontcolor, string $text){
		imagefttext($image,$this->fontsize,$this->fontangle,$this->x,$this->y,$fontcolor,$this->fontsrc,$text);
	}

	# 폰트 파일 경로 지정
	public function setFont(string $fontsrc) : void { $this->fontsrc = $fontsrc; }

	# 칼라 지정
	public function setFontColor(array $color) : void { $this->color = $color; }

	# 폰트 사이즈
	public function setFontSize(string $pixel): void { $this->fontsize = $pixel; }

	# 배경칼라
	public function setBgColor(string $bgcolor) : void { $this->bgcolor = $bgcolor; }

	# 폰트 앵글
	public function setFontAngle(int $angle) : void { $this->fontangle = $angle; }

	# x:y 축
	public function setXY(int $x, int $y) : void { $this->x = $x; $this->y = $y; }

	# 텍스트 이미지 만들기
	public function writeTextImage(int $width, int $height, string $text) : void{
		$this->im = $this->createTrueImage($width,$height);
		$this->setAlphablending($this->im);
		$this->setFilledrectangle($this->im,0,0,$width,$height,$this->bgcolor);

		$fontcolor = $this->setColorallocate($this->im,$this->color[0],$this->color[1],$this->color[2]);
		$this->setFttext($this->im,$fontcolor,$text);
		$this->setSavealpha($this->im,true);
	}

	public function setAntialias(mixed $image,bool $boolean=false): void {
		imageantialias($image,$boolean);
	}

	public function setTTFText(mixed $image,float $size,int $x, int $y,int $color,string $text){
		imagettftext($image,$size,$this->fontangle,$x,$y,$color,$this->fontsrc,$text);
	}

	# 그림자 입체 텍스트 쓰기
	public function writeShadowText(int $width, int $height,string $text,array $bgRGB=[255,255,255], array $mdRGB=[128,128,128], array $frontRGB=[0,0,0]) :void
	{
		$this->im = $this->createTrueImage($width,$height);

		$bg     = $this->setColorallocate($this->im,$bgRGB[0],$bgRGB[1],$bgRGB[2]);
		$middle = $this->setColorallocate($this->im, $mdRGB[0],$mdRGB[1],$mdRGB[2]);
		$front  = $this->setColorallocate($this->im, $frontRGB[0],$frontRGB[1],$frontRGB[2]);
		$this->setFilledrectangle($this->im,0,0,$width-1,$height-1,$bg);

		// Add some shadow to the text
		$this->setTTFText($this->im,$this->fontsize,$this->x,$this->y,$middle,$text);

		// Add the text
		$this->setTTFText($this->im,$this->fontsize, ($this->x - 1), ($this->y - 1),$front,$text);
	}

	# 이미지 위에 텍스트 쓰기
	public function combineImageText(int $width, int $height, string $text, string|null $filename=null) : void{
		$this->im = $this->createTrueImage($width,$height);
		$this->setAntialias($this->im,true);
		$fontcolor = $this->setColorallocate($this->im,$this->color[0],$this->color[1],$this->color[2]);

		$filename = ($filename) ? $filename : $this->filename;
		if(!$filename) throw new \Exception(__CLASS__,':'.__METHOD__.':'.__LINE__);
		
		$image = $this->readImage($filename);
		$this->copy($this->im,$image,0,0,0,0,$width,$height);
		$this->setTTFText($this->im,$this->fontsize,$this->x,$this->y,$fontcolor,$text);
	}

	# margin_x : 가로 여백, margin_y : 세로 여백
	# RB : 오른쪽 아래 기분, LB : 왼쪽 아래 기분, LT : 왼쪽 위 기준, RT : 오른쪽 위 기준
	public function filterWatermarks(string $marksfilename,int $margin_x=10,int $margin_y=10, string $position='RB'): void
	{
		if(!file_exists($marksfilename))
			throw new \Exception(__CLASS__.':'.__METHOD__.':'.$marksfilename);

		$this->im = $this->readImage($this->filename);
		$this->setAntialias($this->im,true);
		$image = $this->readImage($marksfilename);

		$width  = imagesx($image);
		$height = imagesy($image);

		# switch
		$im_x   = $margin_x;
		$im_y   = $margin_y;
		switch ($position){
			case 'RB' :
				$im_x   = imagesx($this->im) - $width - $margin_x;
				$im_y   = imagesy($this->im) - $height - $margin_y;
				break;
			case 'LB' :
				$im_x   = $margin_x;
				$im_y   = imagesy($this->im) - $height - $margin_y;
				break;
			case 'LT' :
				$im_x   = $margin_x;
				$im_y   = $margin_y;
				break;
			case 'RT' :
				$im_x   = imagesx($this->im) - $width - $margin_x;
				$im_y   = $margin_y;
				break;
		}

		$this->copy($this->im,$image,$im_x,$im_y,0,0,$width,$height);
	}

	# void 이미지 자르기 int width,height,x,y
	public function cropImage(int $width,int $height, int $x, int $y) : void{
		$this->im = $this->createTrueImage($width,$height);
		$image = $this->readImage($this->filename);
		if($this->copy($this->im,$image,0,0,$x,$y,$width,$height) === false)
			throw new \Exception(__METHOD__,__LINE__);
	}

	# void 이미지 자르기 (center) int width,height
	public function cropThumbnailImage(int $width, int $height) : void
	{
		$imgsize = $this->getImageSize($this->filename);

		# 조정
		$im_x    = 0;
		$im_y    = 0;
		$image_x = 0;
		$image_y = 0;

		$wm       = $imgsize->width/$width;
		$hm       = $imgsize->height/$height;
		$h_height = $height/2;
		$w_height = $width/2;

		if($imgsize->width > $imgsize->height){
			$width      = $imgsize->width / $hm;
			$half_width = $width / 2;
			$im_x       = -($half_width - $w_height);
		}else if(($imgsize->width < $imgsize->height) || ($imgsize->wdith == $imgsize->height)){
			$height      = $imgsize->height / $wm;
			$half_height = $height / 2;
			$im_y        = $half_height - $h_height;
		}

		$this->im = $this->createTrueImage($width,$height);
		$image = $this->readImage($this->filename);
		if($this->copyResampled($this->im,$image,$im_x,$im_y,$image_x,$image_y,$width,$height,$imgsize->width,$imgsize->height) === false)
			throw new \Exception(__METHOD__,__LINE__);
	}

	# 썸네일 이미지 만들기 int width, height
	public function thumbnailImage(int $width, int $height) : void
	{
		$imgsize = $this->getImageSize($this->filename);

		# 썸네일 사진 사이즈 설정
		if($imgsize->width>$imgsize->height){
			$height= ceil(($imgsize->height*$width)/$imgsize->width);
		}
		else if($imgsize->width<$imgsize->height || $imgsize->width == $imgsize->height){
			$width= ceil(($imgsize->width*$height)/$imgsize->height);
		}

		$this->im = $this->createTrueImage($width,$height);
		$image = $this->readImage($this->filename);
		if($this->copyResampled($this->im, $image, 0,0,0,0,$width,$height,$imgsize->width,$imgsize->height) ===false)
			throw new \Exception(__METHOD__,__LINE__);
	}

	# imagecopy
	public function copy(mixed $im, mixed $image,int $im_x, int $im_y, int $image_x, int $image_y, int $width, int $height) : bool
	{
		if(imagecopy($im,$image,$im_x,$im_y,$image_x,$image_y,$width,$height) === false) return false;
	return true;
	}

	# imagemerge
	public function copyMerge(mixed $im, mixed $image, int $im_x, int $im_y, int $image_x, int $image_y,int $width, int $height,$pct) : bool
	{
		if(!imagecopymerge($im,$image,$im_x,$im_y,$image_x,$image_y,$width,$height,$pct)) return false;
	return true;
	}

	# imagecopyresampled
	public function copyResampled(mixed $im, mixed $image,$im_x,$im_y,$image_x,$image_y,$width,$height,$oriwidth,$oriheight):bool
	{
		if(imagecopyresampled($im, $image,$im_x,$im_y,$image_x,$image_y,$width,$height,$oriwidth,$oriheight) ===false) return false;
	return true;
	}

	# void : createtruecolor
	public function createTrueImage(int $width, int $height){
		return $im = imagecreatetruecolor($width,$height);
	}

	# void
	public function readImage(string $filename){
		$count	= strrpos($filename,'.');
		$extention = strtolower(substr($filename, $count+1));
		try{
			switch($extention){
				case 'gif': $image = imagecreatefromgif($filename); break;
				case 'png': $image = @imagecreatefrompng($filename); break;
				case 'jpeg':
				case 'jpg':	$image = imagecreatefromjpeg($filename); break;
			}
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	return $image;
	}

	# string filename
	public function write(string $filename) : void
	{
		$count	= strrpos($filename,'.');
		$extention = strtolower(substr($filename, $count+1));
		try{
			switch($extention){
				case 'gif': imagegif($this->im,$filename); break;
				case 'png': imagepng($this->im,$filename,($this->quality/10)-1); break;
				case 'jpg':
				case 'jpeg': imagejpeg($this->im,$filename,$this->quality); break;
			}
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	# @ void : GD 버전
	public function getVersion() : float{
		if(function_exists('gd_info')){
			$info = gd_info();
			return floatval(preg_replace('/bundled \((.*) compatible\)/','\\1', $info['GD Version']));
		}
	return 0.0;
	}

	# image data base64 이미지 소스 읽기
	public function readImageFromBase64(string $base64)
    {
        $data = explode(',', $base64);
        $data = base64_decode($data[1]);
        $image = imagecreatefromstring($data);
        if (!$image) {
            throw new \Exception("Invalid base64 image data");
        }
        return $image;
    }

	# image data base64 이미지 쓰기
	public function writeImageToBase64(): string
    {
        ob_start();
        imagepng($this->im);
        $image_data = ob_get_contents();
        ob_end_clean();
        return 'data:image/png;base64,' . base64_encode($image_data);
    }

	# image data base64 이미지 크기 변경하기
	public function resizeBase64Image(string $base64, int $width, int $height): string
    {
        $this->im = $this->readImageFromBase64($base64);
        $imgsize = imagesx($this->im);
        $imgheight = imagesy($this->im);

        if ($imgsize > $imgheight) {
            $height = ceil(($imgheight * $width) / $imgsize);
        } else if ($imgsize < $imgheight || $imgsize == $imgheight) {
            $width = ceil(($imgsize * $height) / $imgheight);
        }

        $resized = $this->createTrueImage($width, $height);
        if ($this->copyResampled($resized, $this->im, 0, 0, 0, 0, $width, $height, $imgsize, $imgheight) === false)
            throw new \Exception(__METHOD__, __LINE__);

        $this->im = $resized;
        return $this->writeImageToBase64();
    }

	# 이미지 사이즈
	public function getImageSize(string|null $filename=null){
		$filename = ($filename) ? $filename : $this->filename;
		$img_info = getImageSize($filename);
	return json_decode(json_encode(array('width'=>$img_info[0],'height'=>$img_info[1],'mime'=>$img_info['mime'])));
	}

	# @ void
	public function destroy(){
		if(is_resource($this->im)) imagedestroy($this->im);
	}

	public function __destruct(){
		$this->destroy();
    }
}
?>
