<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "nilairujukan_m".
 *
 * @property int $nilairujukan_id
 * @property int $pemeriksaanlab_id
 * @property string $nilairujukan_nama
 * @property int $jenis_kelamin lookup_type='jenis_kelamin'
 * @property int $golonganumur_id
 * @property double $nilai_min
 * @property double $nilai_max
 * @property int $satuan_hasillab lookup_type='satuan_hasillab'
 * @property string $text_value
 * @property int $positif_negatif 0=negatif, 1=positif
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
class NilaiRujukan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nilairujukan_m';
    }

    // validasi XSS Form
    protected $xssProtected = [
        'nilairujukan_nama',
        'nilai_min',
        'nilai_max',
        'satuan_hasillab',
        'text_value',
        'keterangan',
        'nilaikritis_min',
        'additional_data',
        'nama_rujukan',
        'nilaikritis_max',
        'kertas_nama'
    ];

    /**
     * {@inheritdoc}
     */
    
    public function rules()
    {
        return [
            // [['jenis_kelamin', 'golonganumur_id'], 'required'],
            [['pemeriksaanlab_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pemeriksaanlab_id', 'jenis_kelamin', 'golonganumur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'nilai_rujukan'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_rujukan'], 'trim'],
            [['nama_rujukan', 'nilai_min', 'nilai_max', 'nilaikritis_min', 'nilaikritis_max', 'keterangan'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nilairujukan_id' => 'Nilairujukan ID',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'nama_rujukan' => 'Nama Rujukan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'golonganumur_id' => 'Golonganumur ID',
            'nilai_min' => 'Nilai Min',
            'nilai_max' => 'Nilai Max',
            'nilai_rujukan' => 'Nilai Rujukan',
            'satuan_hasillab' => 'Satuan Hasillab',
            'nilaikritis_min' => 'Nilaikritis Min',
            'nilaikritis_max' => 'Nilaikritis Max',
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

    public function getJenisKelamin()
    {
        return $this->hasOne(Lookup::className(), ['lookup_id' => 'jenis_kelamin']);
    }

    public function getGolonganUmurLab()
    {
        return $this->hasOne(GolonganUmurLab::className(), ['golonganumurlab_id' => 'golonganumur_id']);
    }
}
