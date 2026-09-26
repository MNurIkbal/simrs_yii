<?php

namespace app\modules\v1\models;


/**
 * This is the model class for table "zataktifobat_mp".
 *
 * @property int $obatalkes_id
 * @property int $zataktif_id
 * @property bool $is_primary
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 
 */

class ZatAktifObat extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'zataktifobat_mp';
    }

    public static function primaryKey()
    {
        return ['obatalkes_id', 'zataktif_id'];
    }

    public function rules()
    {
        return [
            [
                ['is_primary', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
                'default', 
                'value' => null
            ],
            [
                ['obatalkes_id', 'zataktif_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 
                'integer'
            ],
            [
                ['additional_data'], 
                'string'
            ],
            [
                ['created_date', 'last_modified_date', 'deleted_date'], 
                'safe'
            ],
            [
                ['is_deleted', 'is_active'], 
                'boolean'
            ]
        ];
    }

}

?>
