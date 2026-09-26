<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "payterm_m".
 *
 * @property int $payterm_id
 * @property string $payterm_kode
 * @property string $payterm_nama
 * @property int $jumlah_hari
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
class AkuntingPayTerm extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payterm_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jumlah_hari', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['payterm_id', 'jumlah_hari', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['payterm_id', 'created_date', 'last_modified_date', 'deleted_date', 'is_active'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['payterm_kode'], 'string', 'max' => 20],
            [['payterm_nama'], 'string', 'max' => 255],
            [['payterm_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payterm_id' => 'Payterm ID',
            'payterm_kode' => 'Payterm Kode',
            'payterm_nama' => 'Payterm Nama',
            'jumlah_hari' => 'Jumlah Hari',
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
        ];
    }
}
