<?php
namespace Flex\Components\Data\Mgmt;

# javascript editjs 이미지 내용 찾아 압축하기
# @ FidProviderInterface : requied
# @ ImageCompressorBase64Trait : requied
trait ImageComporessorEditjsTrait
{
    public function compressDescriptionBase64Image(array $descriptions, int $width, int $height) : array 
    {
        if(is_array($descriptions) && isset($descriptions['blocks'])){
            foreach($descriptions['blocks'] as $idx => $content)
            {
                if($content['type'] == 'image')
                {
                    $base64_image = $content['data']['url'];
                    $resize_base64_image = $this->resizeBase64Image($base64_image, $width, $height);
                    $descriptions['blocks'][$idx]['data']['url'] = $resize_base64_image;
                }
            }
        }

        return $descriptions;
    }

    # @ ImageCompressorBase64Trait : requied
    abstract protected function resizeBase64Image(string $base64_image, int $width, int $height) : string;
}

?>