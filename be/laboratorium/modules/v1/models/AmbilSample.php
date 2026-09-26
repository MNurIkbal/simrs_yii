<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ambilsample_t".
 *
 * @property int $ambilsample_id
 * @property int $pasienmasukpenunjang_id
 * @property int $tindakanpelayanan_id
 * @property int $samplelab_id
 * @property string $tgl_ambilsample
 * @property string $jam_ambilsample
 * @property string $no_sample
 * @property int $jumlah
 * @property int $satuan_jumlah
 * @property string $keterangan
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
class AmbilSample extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ambilsample_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ambilsample_id', 'pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'samplelab_id', 'jumlah', 'satuan_jumlah', 'tindakanpaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ambilsample_id', 'pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'samplelab_id', 'jumlah', 'satuan_jumlah', 'tindakanpaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_ambilsample', 'jam_ambilsample', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_sample'], 'string', 'max' => 255],
            [['ambilsample_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambilsample_id' => 'Ambilsample ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'samplelab_id' => 'Samplelab ID',
            'tgl_ambilsample' => 'Tgl Ambilsample',
            'jam_ambilsample' => 'Jam Ambilsample',
            'no_sample' => 'No Sample',
            'jumlah' => 'Jumlah',
            'satuan_jumlah' => 'Satuan Jumlah',
            'keterangan' => 'Keterangan',
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
