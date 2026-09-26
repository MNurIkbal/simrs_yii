<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasanginfus_t".
 *
 * @property int $pasanginfus_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property int $jeniscairan_id obatalkes_m.jenisobatalkes='alkes'
 * @property string $tgl_pemasangan
 * @property int $jumlah_tetes
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
class PasangInfus extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasanginfus_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'jeniscairan_id', 'jumlah_tetes', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'jeniscairan_id', 'jumlah_tetes', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pemasangan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasanginfus_id' => 'Pasanginfus ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'jeniscairan_id' => 'Jeniscairan ID',
            'tgl_pemasangan' => 'Tgl Pemasangan',
            'jumlah_tetes' => 'Jumlah Tetes',
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
