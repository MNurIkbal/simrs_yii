<?php

namespace app\modules\ranap\models;

use Yii;

class PelayananJenazahForm extends \yii\base\Model
{
	public $kondisi_pasien;
	public $nama_pj;
	public $jenis_kelamin;
	public $umur;
	public $no_telp;
	public $hub_keluarga;
	public $alamat;
	public $tindakan;
	public $qty_tindakan;
	public $obatalkes;
	public $qty_obatalkes;
	public $satuan_obatalkes;
	public $linen;
	public $qty_linen;
	public $alat_terpasang;
	public $qty_alat_terpasang;

	/**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        	[['nama_pj','jenis_kelamin','umur','no_telp','hub_keluarga'],'required'],
        	[['kondisi_pasien','nama_pj','jenis_kelamin','umur','no_telp','hub_keluarga','alamat','tindakan','qty_tindakan','obatalkes','qty_obatalkes','satuan_obatalkes','linen','qty_linen','alat_terpasang','qty_alat_terpasang'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        	'kondisi_pasien' => 'Kondisi Pasien',
            'nama_pj' => 'Nama Penanggung Jawab',
            'jenis_kelamin' => 'Jenis Kelamin',
            'umur' => 'Umur',
            'no_telp' => 'No Telp/HP',
            'hub_keluarga' => 'Hubungan Keluarga'
        ];
    }
}