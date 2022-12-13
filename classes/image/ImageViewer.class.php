<?php
namespace Flex\Image;

use Flex\Image\ImageGDS;
use Flex\Log\Log;

# 이미지 뷰어
final class ImageViewer extends ImageGDS
{
	# 이미지 경로
	private $upload_dir;

	final public function __construct(string $dir){
		$this->upload_dir = $dir;
	}

	#@ return String
	# 파일 확장자 추출
	public function getExtName(string $filename) : string{
		$count = strrpos($filename,'.');
		$file_extension = strtolower(substr($filename, $count+1));
	return $file_extension;
	} 

	/**
	 * @ filename : 파일명
	 * @ compression : 압축률
	 * @ size : 이미지 사이즈
	 * @ file_extension : ['jpg','jpeg','png'] 허용파일 확장자
	 */
	public function doView (string $filename, int $compression, string $size, array $file_extension) : array
	{
		$fullname = $this->upload_dir.'/'.$filename;

		$file_type = 'application';
		$exe       = $this->getExtName($filename);
		$ext_args  = $file_extension;
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
					parent::__construct( $this->upload_dir.'/'.$filename );
					parent::setCompressionQuality($compression);

					# resize
					if(strpos($size,'x') !==false)
					{
						$sizes = explode('x', $size);
						if(isset($sizes[0]) && isset($sizes[1])){
							parent::thumbnailImage($sizes[0], $sizes[1]);
							parent::write($this->upload_dir.'/'.$thumb_filename);
							$fullname = $this->upload_dir.'/'.$thumb_filename;
						}
					}

					# 압축
					parent::write($this->upload_dir.'/'.$thumb_filename);
					$fullname = $this->upload_dir.'/'.$thumb_filename;
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