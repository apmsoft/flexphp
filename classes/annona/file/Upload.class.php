<?php
namespace Flex\Annona\File;

use Flex\Annona\Dir\DirInfo;
use Flex\Annona\Image\ImageExif;
use Flex\Annona\Cipher\CipherGeneric;
use Flex\Annona\Cipher\HashEncoder;
use Flex\Annona\Log;
use \Exception;

class Upload extends DirInfo
{
    public const __version = '2.2';
    public string $file_extension = '';
	public string $mimeType;
	public string $basename;
    public $process;
    public string $savefilename = '';

    private array $error_msg = [
        UPLOAD_ERR_INI_SIZE    => 'e_upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE   => 'e_upload_max_filesize',
        UPLOAD_ERR_PARTIAL     => 'e_partially_uploaded',
        UPLOAD_ERR_NO_FILE     => 'e_no_was_uploaded',
        UPLOAD_ERR_NO_TMP_DIR  => 'e_miss_temp_folder',
        UPLOAD_ERR_CANT_WRITE  => 'e_failed_write_disk',
        UPLOAD_ERR_EXTENSION   => 'e_upload_stopped',
    ];

    # 1
    public function __construct(string $directory)
	{
        parent::__construct($directory);
	}

    # 2 첨부파일
    public function process(string $process_id, array $_files) : Upload
    {
        # 값이 정상적인지 체크
        if (!isset($_files[$process_id])) {
            self::exceptionsErrorHandler(UPLOAD_ERR_NO_FILE);
        }

        $this->process = $_files[$process_id];
        $filename = method_exists($this->process, 'getClientFilename') ? $this->process->getClientFilename() : $this->process['name'];
        $mimeType = method_exists($this->process, 'getClientMediaType') ? $this->process->getClientMediaType() : $this->process['type'];
        $size = method_exists($this->process, 'getSize') ? $this->process->getSize() : $this->process['size'];
        $error = method_exists($this->process, 'getError') ? $this->process->getError() : $this->process['error'];

        Log::d('-----------< Upload >-------------------');
        Log::d('filename', $filename);
        Log::d('mimeType', $mimeType);
        Log::d('size', $size);
        Log::d('error', $error);
        Log::d('--------------------------------------');

        # 기초에러
        if ($error !== UPLOAD_ERR_OK) {
            self::exceptionsErrorHandler($error);
        }

    return $this;
    }

    # 3 업로드 허용된 파일 인치 체크
    public function filterExtension(array $allowe_extension=['jpg','jpeg','png','gif']) : Upload
	{
        self::getExtName();
        if (!in_array($this->file_extension, $allowe_extension)) {
			self::exceptionsErrorHandler(UPLOAD_ERR_EXTENSION);
        }
    return $this;
    }

    # 4 파일크기 체크 8(M),12(M),100(M)
    public function filterSize(int $size) : Upload 
    {
        $maxsize = (int)(1024 * 1024 * $size);
        if ($this->process->getSize() >= $maxsize) {
            self::exceptionsErrorHandler(UPLOAD_ERR_INI_SIZE);
        }
    return $this;
    }

    # 5 업로드할 디렉토리 체크 및 만들기
    public function makeDirs() : Upload
    {
        try {
            parent::makesDir();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    return $this;
    }

    # 6 업로드 파일 복사하기
	public function save(): Upload
    {
        #저장할파일명
		$tempfilename  = str_replace(['.',' '],['_','_'], microtime());
		$this->savefilename = sprintf("%s.%s", (new CipherGeneric(new HashEncoder($tempfilename)))->hash(), $this->file_extension);
        $fullname = sprintf("%s/%s", $this->directory, $this->savefilename);

        # 파일 저장
        try {
            if ($this->process->getStream() !== null) {
                # BufferedBody 객체에서 내용을 가져옴
                $bodyContent = (string)$this->process->getStream();
                if (file_put_contents($fullname, $bodyContent) === false) {
                    throw new Exception('Failed to write file to disk.');
                }
            } else {
                if (!move_uploaded_file($this->process['tmp_name'], $fullname)) {
                    self::exceptionsErrorHandler(UPLOAD_ERR_CANT_WRITE);
                }
            }
        } catch (Exception $e) {
            Log::e(__LINE__, $e->getMessage());
            self::exceptionsErrorHandler(UPLOAD_ERR_CANT_WRITE);
        }

    return $this;
	}

    # 7 orientation
    public function filterOrientation() : Upload
    {
        # jpeg, jpg 인지 체크
		if (preg_match('/(jpeg|jpg)/', $this->file_extension)) {
            $fullname = sprintf("%s/%s", $this->directory, $this->savefilename);
			$ifdo = (new ImageExif($fullname))->getIfdo();
			if (isset($ifdo['Orientation']) && !empty($ifdo['Orientation'])) {
				$im = imagecreatefromjpeg($fullname);
				switch ($ifdo['Orientation']) {
                    case 8:
                        $rotate = imagerotate($im, 90, 0);
                        imagejpeg($rotate, $fullname);
                        break;
                    case 3:
                        $rotate = imagerotate($im, 180, 0);
                        imagejpeg($rotate, $fullname);
                        break;
                    case 6:
                        $rotate = imagerotate($im, -90, 0);
                        imagejpeg($rotate, $fullname);
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
            'filesize'  => $this->process->getSize() ?? $this->process['size'],
            'mimeType'  => $this->mimeType,
            'ofilename' => self::cleansEtcWords(),
            'sfilename' => $this->savefilename
        ];
    }

    # 파일 확장자 추출
	private function getExtName() : void
    {
		$count    = strrpos($this->process->getClientFilename(), '.');
		$this->file_extension = strtolower(substr($this->process->getClientFilename(), $count+1));
        $this->mimeType = (preg_match('/(gif|jpeg|jpg|png)/', $this->file_extension)) ? 'image/'.$this->file_extension : 'application/'.$this->file_extension;
	}

    # 업로드된 파일인지 체크
	private function is_upload_files(): bool {
        if (!isset($this->process['tmp_name']) || !is_uploaded_file($this->process['tmp_name'])) {
            return false;
        }
	return true;
	}

    # 첨부 실파일명 특수문자 제거
	private function cleansEtcWords() : string {
		$ofilename = preg_replace("/[ #\&\+\-%@=\/\\\:;,\'\"\^`~\|\!\?\*$#<>()\[\]\{\}]/i", '_', $this->process->getClientFilename() ?? $this->process['name']); 
		$ofilename = preg_replace('/\s\s+/', '_', $ofilename); // 연속된 공백을 하나의 문자로 변경
	return $ofilename;
	}

    # 에러 발생시키기
    private function exceptionsErrorHandler(int $error_no) : void {
        if (array_key_exists($error_no, $this->error_msg)) {
            throw new Exception($this->error_msg[$error_no]);
        }
    }
}
?>
