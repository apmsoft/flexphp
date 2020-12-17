<?php
$strings_filters = array(
    "id"
);

$column_data_types = array(
    array(
        'label' => '숫자',
        'type' => array('INT','MEDIUMINT','SMALLINT','TINYINT','DOUBLE','BIGINT','DECIMAL')
    ),
    array(
        'label' => '문자',
        'type' => array('CHAR','VARCHAR','VARBINARY','TEXT','MEDIUMTEXT','TINYTEXT','LONGTEXT','JSON','BLOB')
    ),
    array(
        'label' => '날짜',
        'type' => array('DATE','TIME','DATETIME','YEAR','TIMESTAMP')
    ),
    array(
        'label' => 'ENUM',
        'type' => array('ENUM')
    )
);

$column_data_null = array(
    'NOT NULL' => 'NOT NULL',
    'NULL' => 'NUll'
);
?>