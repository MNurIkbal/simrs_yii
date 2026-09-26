<?php

namespace app\modules\v1\models;


/**
 * This is the model class for table "manufaktur_m".
 *
 * @property int $zataktif_id
 * @property string $zataktif_kode
 * @property string $zataktif_nama
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

class ZatAktif extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'zataktif_m';
    }

    public function rules()
    {
        return [
            [
                ['zataktif_kode', 'zataktif_nama', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
                'default', 
                'value' => null
            ],
            [
                ['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 
                'integer'
            ],
            [
                ['zataktif_kode', 'zataktif_nama','additional_data'], 
                'string'
            ],
            [
                ['created_date', 'last_modified_date', 'deleted_date'], 
                'safe'
            ],
            [
                ['is_deleted', 'is_active'], 
                'boolean'
            ],
            [['zataktif_kode'], 'string', 'max' => 100],
            [['zataktif_nama'], 'string', 'max' => 255],
            [
                ['zataktif_kode', 'zataktif_nama', 'alamat', 'kontak'], 
                'required'
            ],
        ];
    }

}



?>