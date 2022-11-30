<?php
namespace Flex\Image;

use Flex\R\R;
use Flex\Log\Log;

# purpose : 이미지를(비율에 맞춰) 출력하기 위해
final class ImageViewer
{
	private $upload_dir;

	final public function __construct(string $extract_id){
		$this->upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/'.$extract_id;

	}

	#@ return String
	# 파일 확장자 추출
	public function getExtName(string $filename) : string{
		$count = strrpos($filename,'.');
		$file_extension = strtolower(substr($filename, $count+1));
	return $file_extension;
	} 

	public function doView (string $filename, int $compression, string $size) : array
	{
		$fullname = $this->upload_dir.'/'.$filename;

		$file_type = 'application';
		$exe       = $this->getExtName($filename);
		$ext_args  = explode(',',R::$config['file_extension']);
		if(in_array($exe,$ext_args)){
			$file_type = 'image';
		}

		# 이미지일 경우 이미지 사이즈 구하기
		$imagecontents = '';
		if(!strcmp($file_type,'image'))
		{
			$thumb_filename = 'thumb'.$compression.$size.'_'.$filename;
			if(file_exists($this->upload_dir.'/'.$thumb_filename)){
				$fullname = $this->upload_dir.'/'.$thumb_filename;
			}else{
				try{
					// $image_size = @getimagesize($fullname);
					$imageGDS = new \Flex\Image\ImageGDS( $this->upload_dir.'/'.$filename );
					$imageGDS->setCompressionQuality($compression);

					# resize
					if(strpos($size,'x') !==false)
					{
						$sizes = explode('x', $size);
						if(isset($sizes[0]) && isset($sizes[1])){
							if($imageGDS->thumbnailImage($sizes[0], $sizes[1])){
								if($imageGDS->write($this->upload_dir.'/'.$thumb_filename)){
									$fullname = $this->upload_dir.'/'.$thumb_filename;
								}
							}
						}
					}

					# 압축
					if($imageGDS->write($this->upload_dir.'/'.$thumb_filename)){
						$fullname = $this->upload_dir.'/'.$thumb_filename;
					}
				}catch(\Exception $e){
                    Log::e($e->getMessage());
				}
			}

			# base64 image data
			$imagecontents = file_get_contents($fullname);
		}

		// model
		return [
			'mimeType' => $file_type.'/'.$exe,
			'contents' => $imagecontents
		];
    }
}
?>