<?php
namespace Flex\Util;

/*
$utilUpLoadFileSystem = new Fus3\Util\UtilUpLoadFileSystem($model->extract_id);
$utilUpLoadFileSystem->doEditUploadable (
    R::$tables['member_upfiles'],
    R::$manifest['adm_user']['uploadable']['image_size']['thumbnail'], 
    R::$manifest['adm_user']['uploadable']['image_size']['middle'],
    $db
);
*/
class UtilUpLoadFileSystem {
    private $extract_id = '';

    public function __construct($extract_id){
        $this->extract_id = $extract_id;
    }

    # 첨부파일 등록 및 변경
    public function doEditUploadable ($table, $image_thumbnail, $image_middle, &$db){
        # query
        $is_upfiles_qry = sprintf(
            "SELECT id,file_type,directory,sfilename FROM `%s` WHERE extract_id='%s' AND `is_regi`<'1'",
            $table, $this->extract_id
        );

        # 썸네일 만들기
        if($file_rlt = $db->query($is_upfiles_qry)){
            $utilMakeThumbnail = new UtilMakeThumbnail;
            while($file_row = $file_rlt->fetch_assoc()){
                $utilMakeThumbnail->makeThumbnail($file_row, $image_thumbnail,$image_middle);
            }
        }

        #files update
        $regi_upfiles_qry = sprintf(
            "UPDATE `%s` SET `is_regi`='1' WHERE `extract_id`='%s' AND `is_regi`<'1'",
            $table, $this->extract_id
        );
        $db->query($regi_upfiles_qry);
    }
}
?>