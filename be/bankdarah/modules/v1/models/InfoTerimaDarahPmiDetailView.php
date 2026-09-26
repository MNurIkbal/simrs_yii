<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoterimadarahpmidetail_v".
 *
 * @property int $terimadarahpmidetail_id
 * @property int $terimadarahpmi_id
 * @property string $no_terimadarahpmi
 * @property int $supplier_id
 * @property string $nama_pmi
 * @property string $alamat
 * @property string $no_tlp
 * @property int $pesandarahpmidetail_id
 * @property int $jenisdarah_id
 * @property string $jenisdarah_nama
 * @property int $golongandarah_id
 * @property string $golongandarah_nama
 * @property string $no_kantongdarah
 * @property string $tgl_pengambilan
 * @property double $harga
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property bool $is_deleted
 * @property bool $is_active
 */
class InfoTerimaDarahPmiDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoterimadarahpmidetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['terimadarahpmidetail_id', 'terimadarahpmi_id', 'supplier_id', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'created_by'], 'default', 'value' => null],
            [['terimadarahpmidetail_id', 'terimadarahpmi_id', 'supplier_id', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'created_by'], 'integer'],
            [['alamat', 'golongandarah_nama', 'additional_data'], 'string'],
            [['tgl_pengambilan', 'created_date'], 'safe'],
            [['harga'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_terimadarahpmi', 'jenisdarah_nama', 'no_kantongdarah'], 'string', 'max' => 255],
            [['nama_pmi'], 'string', 'max' => 100],
            [['no_tlp'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terimadarahpmidetail_id' => 'Terimadarahpmidetail ID',
            'terimadarahpmi_id' => 'Terimadarahpmi ID',
            'no_terimadarahpmi' => 'No Terimadarahpmi',
            'supplier_id' => 'Supplier ID',
            'nama_pmi' => 'Nama Pmi',
            'alamat' => 'Alamat',
            'no_tlp' => 'No Tlp',
            'pesandarahpmidetail_id' => 'Pesandarahpmidetail ID',
            'jenisdarah_id' => 'Jenisdarah ID',
            'jenisdarah_nama' => 'Jenisdarah Nama',
            'golongandarah_id' => 'Golongandarah ID',
            'golongandarah_nama' => 'Golongandarah Nama',
            'no_kantongdarah' => 'No Kantongdarah',
            'tgl_pengambilan' => 'Tgl Pengambilan',
            'harga' => 'Harga',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }
}
