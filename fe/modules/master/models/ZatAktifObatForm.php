<?php

namespace app\modules\master\models;

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

class ZatAktifObatForm extends \yii\base\Model
{
    public $obatalkes_id;
    public $zataktif_id;
    public $is_primary;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    public static function tableName()
    {
        return 'zataktifobat_mp';
    }

    public function rules()
    {
        return [
            [['obatalkes_id', 'zataktif_id'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
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
