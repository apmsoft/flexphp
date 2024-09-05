<?php

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Log;
use Flex\Annona\R;
use Flex\Annona\Image\ImageGDS;

use Flex\Components\Data\Mgmt\ImageCompressorInterface;
use Flex\Components\Data\Mgmt\ImageCompressorBase64Trait;
use Flex\Components\Data\Mgmt\ImageComporessorEditjsTrait;

# config
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init(Log::MESSAGE_ECHO);

# resource
R::tables();
$db = new DbMySqli();

# example class
class ImageCompressor implements ImageCompressorInterface
{
    use ImageCompressorBase64Trait;
    use ImageComporessorEditjsTrait;

    private $imageGDS;

    public function __construct()
    {
        $this->imageGDS = new ImageGDS();
    }

    public function getImageGDS(): ImageGDS
    {
        return $this->imageGDS;
    }
}

$processor = new ImageCompressor();

// 단일 이미지 처리
$base64Image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==";
$resizedImage = $processor->resizeBase64Image($base64Image, 100, 100);
Log::d( "Resized Image: " . substr($resizedImage, 0, 50) );


// EditorJS 형식의 설명 처리
$description = [
    'blocks' => [
        [
            'type' => 'image',
            'data' => [
                'url' => $base64Image
            ]
        ],
        [
            'type' => 'text',
            'data' => [
                'text' => 'This is a sample text.'
            ]
        ]
    ]
];

$processedDescription = $processor->compressDescriptionBase64Image($description, 200, 200);
Log::d( "Processed Description: " , json_encode($processedDescription, JSON_PRETTY_PRINT) );
?>