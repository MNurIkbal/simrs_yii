<?php

/**
 * @Author: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Date:   2021-01-22 10:35:38
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "manufaktur_m".
 *
 * @property int $manufaktur_id
 * @property string $kode
 * @property string $nama
 * @property string $alamat
 * @property string $kontak
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

class Manufaktur extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'manufaktur_m';
    }

    public function rules()
    {
        return [
            [
                ['kode', 'nama', 'alamat', 'kontak', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
                'default', 
                'value' => null
            ],
            [
                ['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 
                'integer'
            ],
            [
                ['kode', 'nama', 'alamat', 'kontak', 'additional_data'], 
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
            [['kode'], 'string', 'max' => 100],
            [['nama'], 'string', 'max' => 255],
            [
                ['kode', 'nama', 'alamat', 'kontak'], 
                'required'
            ],
        ];
    }
}
?>
