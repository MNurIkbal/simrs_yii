<?php

namespace app\modules\v1\models;

class Rak extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'rakobat_m';
    }

    public function rules(){

        return [
            [
                ['rakobat_nama', 'ruangan_id', 'parentrakobat_id', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
                'default',
                'value' => null
            ],
            [
                ['created_by', 'modified_count', 'last_modified_by', 'deleted_by'],
                'integer'
            ],
            [
                ['created_date', 'last_modified_date', 'deleted_date'],
                'safe'
            ],
            [
                ['is_deleted', 'is_active'],
                'boolean'
            ],
            [
                ['rakobat_nama'], 'string', 'max' => 255
            ],
            [
                ['rakobat_nama'],
                'required'
            ]
        ];
    }
}

?>
