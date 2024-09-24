<?php
namespace Flex\Annona\File;

use Flex\Annona\Dir\DirInfo;
use Flex\Annona\Cipher\Encrypt;
use Flex\Annona\Image\ImageExif;
use \Exception;
use Flex\Annona\Cipher\CipherGeneric;
use Flex\Annona\Cipher\HashEncoder;

class Upload extends DirInfo
{
    public const __version = '2.1';
    public string $file_extension = '';
	public string $mimeType;
	public string $basename;
    private array $process = [];
    public string $savefilename = '';

    private array $error_msg = [
        'E2' => 'e_upload_max_filesize',
        'E3' => 'e_partially_uploaded',
        'E4' => 'e_no_was_uploaded',
        'E5' => 'e_extension_not_allowed',
        'E6' => 'e_miss_temp_folder',
        'E7' => 'e_failed_write_disk',
        'E8' => 'e_upload_stopped',
        'E9' => 'e_not_uploaded_file',
    ];

    # 1
    public function __construct(string $directory)
	{
        parent::__construct($directory);
	}

    # 2 첨부파일
    public function process(string $process_id) : Upload
    {
        # 값이 정상적인지 체크
        if(!isset($_FILES[$process_id])){
            $this->exceptionsErrorHandler(4);
        }

        $this->process = $_FILES[$process_id];
        // Flex\Annona\Log::d('tmp_name' ,$this->process['tmp_name']);
        // Flex\Annona\Log::d('filename' ,$this->process['name']);
        // Flex\Annona\Log::d('mimeType',$this->process['type']);
        // Flex\Annona\Log::d('size', $this->process['size']);
        // Flex\Annona\Log::d('error', $this->process['error']);

        # 기초에러
        if($this->process['error'] > 0){
            $this->exceptionsErrorHandler($this->process['error']);
        }

        # 업로드 된 파일인지 체크
        if(!$this->is_upload_files()){
            $this->exceptionsErrorHandler(9);
        }

    return $this;
    }

    # 3 업로드 허용된 파일 인치 체크
    public function filterExtension(array $allowe_extension=['jpg','jpeg','png','gif']) : Upload
	{
        $this->getExtName();
        if(!in_array($this->file_extension,$allowe_extension)){
			$this->exceptionsErrorHandler(5);
        }
    return $this;
    }

    # 4 파일크기 체크 8(M),12(M),100(M)
    public function filterSize(int $size) : Upload 
    {
        $maxsize = (int)(1024 * 1024 * $size);
        if($this->process['size'] >= $maxsize){
            $this->exceptionsErrorHandler(2);
        }
    return $this;
    }

    # 5 업로드할 디렉토리 체크 및 만들기
    public function makeDirs() : Upload
    {
        try{
            parent::makesDir();
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    return $this;
    }

    # 6 업로드 파일 복사하기
	public function save(): Upload
    {
        #저장할파일명
		$tempfilename  = str_replace(['.',' '],['_','_'],microtime());
		$this->savefilename = sprintf("%s.%s", (new CipherGeneric(new HashEncoder($tempfilename)))->hash(), $this->file_extension);
        $fullname = sprintf("%s/%s", $this->directory, $this->savefilename);

		if(!move_uploaded_file($this->process['tmp_name'], $fullname )){
            $this->exceptionsErrorHandler(7);
        }
    return $this;
	}

    # 7 orientation
    public function filterOrientation() : Upload
    {
        # jpeg, jpg 인지 체크
		if( preg_match('/(jpeg|jpg)/',$this->file_extension) )
		{
            $fullname = sprintf("%s/%s", $this->directory, $this->savefilename);
			$ifdo = (new ImageExif( $fullname ))->getIfdo();
			if(isset($ifdo['Orientation']) && !empty($ifdo['Orientation']))
            {
                // Flex\Annona\Log::d('filterOrientation >>>>',$ifdo);
				$im = imagecreatefromjpeg( $fullname );
				switch($ifdo['Orientation']) {
                    case 8:
                        $rotate = imagerotate($im,90,0);
                        imagejpeg($rotate, $fullname );
                        break;
                    case 3:
                        $rotate = imagerotate($im,180,0);
                        imagejpeg($rotate, $fullname);
                        break;
                    case 6:
                        $rotate = imagerotate($im,-90,0);
                        imagejpeg($rotate,$fullname);
                        break;
                    }
			}
		}
    return $this;
	}

    # end fetch
    public function fetch() : array 
    {
        return [
            'filesize'  => $this->process['size'],
            'mimeType'  => $this->mimeType,
            'ofilename' => $this->cleansEtcWords(),
            'sfilename' => $this->savefilename,
        ];
    }

    # 파일 확장자 추출
	private function getExtName() : void
    {
		$count    = strrpos($this->process['name'],'.');
		$this->file_extension = strtolower(substr($this->process['name'], $count+1));
        $this->mimeType = (preg_match('/(gif|jpeg|jpg|png)/',$this->file_extension)) ? 'image/'.$this->file_extension : 'application/'.$this->file_extension;
	}

    # 업로드된 파일인지 체크
	private function is_upload_files(): bool{
		if(!is_uploaded_file( $this->process['tmp_name'] )) return false;
	return true;
	}

    # 첨부 실파일명 특수문자 제거
	private function cleansEtcWords() : string{
		$ofilename = preg_replace("/[ #\&\+\-%@=\/\\\:;,\'\"\^`~\|\!\?\*$#<>()\[\]\{\}]/i",'_',$this->process['name']); 
		$ofilename = preg_replace('/\s\s+/', '_', $ofilename); // 연속된 공백을 하나의 문자로 변경
	return $ofilename;
	}

    # 에러 발생시키기
    private function exceptionsErrorHandler(int $error_no) : void
    {
        if($error_no >= 2 && $error_no <= 9) {
            $error_code = sprintf("E%d",$error_no);
            throw new Exception($this->error_msg[$error_code]);
        }
    }
}
?>