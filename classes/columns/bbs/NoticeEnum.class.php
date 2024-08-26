<?php
namespace Flex\Columns\Bbs;

use Flex\Columns\EntryArrayTrait;

class NoticeEnum
{
    use EntryArrayTrait;

    const ID           = 'id';
    const SIGNDATE     = 'signdate';
    const FID          = 'fid';
    const CATEGORY     = 'category';
    const MUID         = 'muid';
    const TITLE        = 'title';
    const EXTRACT_ID   = 'extract_id';
    const EXTRACT_DATA = 'extract_data';
    const DESCRIPTION  = 'description';
}
?>