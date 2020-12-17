<?php
/* ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://apmsoft.tistory.com
| @Editor   : Sublime Text 3
| @UPDATE   : 0.1.1
----------------------------------------------------------*/
namespace Flex\Util;

use Flex\Dir\DirInfo;
use Flex\Image\ImageGDS;
use Flex\Out\Out;

# 이미지 썸네일 이미지 만들기
class UtilMakeThumbnail
{
    #@ void
    # 썸네일 만들기
    public function makeThumbnail($file_info=array(), $thumbnail_size=array(), $middle_size=array())
    {
        $img_root_dir = _ROOT_PATH_.$file_info['directory'];

        if(!isset($thumbnail_size['width'])) return;

        # 이미지
        if(substr($file_info['file_type'],0,5)=='image')
        {
            # 경로폴더 만들기 s
            $thumbnail_dir = $img_root_dir.'/s';
            $dirObj = new DirInfo($thumbnail_dir);
            $dirObj->makesDir();

            # 경로폴더 만들기 m
            $middle_dir = $img_root_dir.'/m';
            $dirObj = new DirInfo($middle_dir);
            $dirObj->makesDir();

            try{
                # 이미지 자르고 만들기 thumbnail/-------------------
                $gd =new ImageGDS($img_root_dir.DIRECTORY_SEPARATOR.$file_info['sfilename']);
                if($gd->thumbnailImage($thumbnail_size['width'],$thumbnail_size['height'])){
                    $gd->write($thumbnail_dir.DIRECTORY_SEPARATOR.$file_info['sfilename']);

                    # middle
                    if(isset($middle_size['width'])){
                        $gd =new ImageGDS($img_root_dir.DIRECTORY_SEPARATOR.$file_info['sfilename']);
                        if($gd->thumbnailImage($middle_size['width'],$middle_size['height'])){
                            $gd->write($middle_dir.DIRECTORY_SEPARATOR.$file_info['sfilename']);
                        }
                    }
                }
            }catch(Exception $e){
                Out::prints_json(array('result'=>'false','msg_code'=>'gd_make_thumbnail','msg'=>$e->getMessage()));
            }
        }
    }

    #@ void
    # 이미 서버에 등록된 파일을 삭제
    public function removeFile($file_info=array())
    {
        $img_root_dir = _ROOT_PATH_.$file_info['directory'];

        # unlink
        @unlink($img_root_dir.'/s/'.$file_info['sfilename']);
        @unlink($img_root_dir.'/m/'.$file_info['sfilename']);
        @unlink($img_root_dir.DIRECTORY_SEPARATOR.$file_info['sfilename']);
    }
}
?>
