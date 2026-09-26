<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemakaianbarang_t".
 *
 * @property int $pemakaianbarang_id
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $tgl_pemakaianbarang
 * @property string $no_pemakaianbarang
 * @property string $untuk_keperluan
 * @property string $keteranganpakai
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
class PemakaianBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemakaianbarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'pegawai_id', 'tgl_pemakaianbarang', 'untuk_keperluan'], 'required'],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pemakaianbarang', 'created_date', 'last_modified_date', 'deleted_date','is_deleted', 'is_active'], 'safe'],
            [['keteranganpakai', 'additional_data'], 'string'],
            [['no_pemakaianbarang'], 'string', 'max' => 20],
            [['untuk_keperluan'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemakaianbarang_id' => 'Pemakaianbarang ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'tgl_pemakaianbarang' => 'Tgl Pemakaianbarang',
            'no_pemakaianbarang' => 'No Pemakaianbarang',
            'untuk_keperluan' => 'Untuk Keperluan',
            'keteranganpakai' => 'Keteranganpakai',
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

    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(),[
            'pegawai_id' => 'pegawai_id'
        ]);
    }
}
