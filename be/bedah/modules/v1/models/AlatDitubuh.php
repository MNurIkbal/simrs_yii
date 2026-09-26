<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "alatditubuh_t".
 *
 * @property int $alatditubuh_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property int $jenis_alat obatalkes_m.obatalkes_id where jenisobatalkes_id='ALKES'
 * @property int $jumlah
 * @property string $lokasi
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
class AlatDitubuh extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alatditubuh_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'jenis_alat', 'jumlah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'jenis_alat', 'jumlah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['lokasi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'alatditubuh_id' => 'Alatditubuh ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'jenis_alat' => 'Jenis Alat',
            'jumlah' => 'Jumlah',
            'lokasi' => 'Lokasi',
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
