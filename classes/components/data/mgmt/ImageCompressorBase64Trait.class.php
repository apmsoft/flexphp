<?php
namespace Flex\Components\Data\Mgmt;

use Flex\Annona\Log;

# Base64 Iamge 리사이즈
# @ ImageCompressorProviderInterface : requied
# 이 클래스를 사용하려면 반드시 이 이클래스 사용하는 클래스에서 구현하세요
trait ImageCompressorBase64Trait
{
    public function resizeBase64Image(string $base64_image, int $width, int $height) : string
    {
        $result = '';
        try{
            # ImageGDS class
            $imageGDS = $this->getImageGDS();
            $result = $imageGDS->resizeBase64Image($base64_image, $width, $height);
        }catch(\Exception $e){
            Log::e($e->getMessage());
        }

        return $result;
    }

    #@ ImageCompressorProviderInterface : requied
    abstract protected function getImageGDS();
}
?>