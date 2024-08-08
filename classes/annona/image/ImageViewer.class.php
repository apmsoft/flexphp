<?php
namespace Flex\Annona\Image;

use Flex\Annona\Image\ImageGDS;
use Flex\Annona;
use \Exception;

# 이미지 뷰어
final class ImageViewer extends ImageGDS
{
	public const __version = '2.0';

	# 이미지 경로
	public string $file_extension = '';
	public string $mimeType;
	public string $basename;
	public string $directory;
	public int $compression;
	public array $viewseize = [];

	final public function __construct(string $filenamez){
		$this->filename = $filenamez;
		$this->getExtName();
	}

	# 파일 확장자 추출
	private function getExtName() : void{
		$this->basename       = basename($this->filename);
		$count                = strrpos($this->basename,'.');
		$this->file_extension = strtolower(substr($this->basename, $count+1));
		$this->directory      = str_replace('/'.$this->basename,'',$this->filename);
	}

	public function setFilter(int $compression, string $size, array $allowe_extension=['jpg','jpeg','png','gif']) : ImageViewer
	{
		# mimeType
		$file_type = 'application';
		if(in_array($this->file_extension,$allowe_extension)){
			$file_type = 'image';
		}else throw new \Exception('e_extension_not_allowed');

		$this->mimeType    = $file_type.'/'.$this->file_extension;
		$this->compression = $compression;
		$this->viewsize    = (strpos($size,'x') !==false) ? explode('x', $size) : [];

	return $this;
	}

	/**
	 * @ filename : 파일명
	 * @ compression : 압축률
	 * @ size : 이미지 사이즈
	 * @ file_extension : ['jpg','jpeg','png'] 허용파일 확장자
	 */
	public function getContents () : string
	{
		$imagecontents = '';
		if(strpos($this->mimeType,'image/') !== false)
		{
			$fullname = '';
			$thumb_filename = 'thumb'.$this->compression.implode('x',$this->viewsize).'_'.$this->basename;
			if(file_exists($this->directory.'/'.$thumb_filename)){
				$fullname = $this->directory.'/'.$thumb_filename;
			}else{
				try{
					// $image_size = @getimagesize($fullname);
					parent::__construct( $this->filename );
					parent::setCompressionQuality($this->compression);

					# resize
					if(isset($this->viewsize[0])){
						parent::thumbnailImage($this->viewsize[0], $this->viewsize[1]);
						parent::write($this->directory.'/'.$thumb_filename);
						$fullname = $this->directory.'/'.$thumb_filename;
					}

					# 압축
					parent::write($this->directory.'/'.$thumb_filename);
					$fullname = $this->directory.'/'.$thumb_filename;
				}catch(\Exception $e){
					throw new \Exception($e->getMessage());
				}
			}

			# base64 image data
			$imagecontents = file_get_contents($fullname);
		}

		return $imagecontents;
    }

	public function fetch() : array {
		$result = [
			'filename'  => $this->basename,
			'mimeType'  => $this->mimeType,
			'extension' => $this->file_extension,
			'contents'  => $this->getContents()
		];

	return $result;
	}
}
?>