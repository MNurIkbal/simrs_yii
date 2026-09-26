<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "satuankonversi_m".
 *
 * @property int $satuankonversi_id
 * @property int $satuanbesar_id
 * @property int $satuankecil_id
 * @property double $nilai_konversi
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
 * @property bool $is_generik
 */
class SatuanKonversi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'satuankonversi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['satuankonversi_id', 'satuanbesar_id', 'satuankecil_id', 'nilai_konversi'], 'required'],
            [['satuankonversi_id', 'satuanbesar_id', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['satuankonversi_id', 'satuanbesar_id', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nilai_konversi'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active', 'is_generik'], 'boolean'],
            [['satuankonversi_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuankonversi_id' => 'Satuankonversi ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuankecil_id' => 'Satuankecil ID',
            'nilai_konversi' => 'Nilai Konversi',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'is_generik' => 'Is Generik',
        ];
    }
}
