<?php
//author: Ardi Pratama

namespace app\modules\igd\models;

use Yii;

class InstruksiTindakanForm extends \yii\base\Model
{
    public $instruksi_id;
    public $tgl_tindakan;
    public $daftartindakan_id;
    public $perawat1_id;
    public $tarif_satuan;
    public $perawat2_id;
    public $tarif_cyto;
    public $qty;
    public $tarif_tindakan;
    public $pendaftaran_id;
    public $dokterdpjp_id;
    public $harga_jumlah;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'qty'], 'required'],
            [[
                'tgl_tindakan',
                'daftartindakan_id',
                'perawat1_id',
                'tarif_satuan',
                'perawat2_id',
                'tarif_cyto',
                'qty',
                'tarif_tindakan',
                'pendaftaran_id',
                'dokterdpjp_id',
                'harga_jumlah',
                'instruksi_id',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'instruksi_id' => 'Instruksi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'tgl_tindakan' => 'Tanggal Tindakan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'tipepaket_id' => 'Tipepaket ID',
            'qty' => 'Jumlah Tindakan',
            'is_cyto' => 'Is Cyto',
            'tarif_satuan' => 'Tarif Satuan',
            'tarif_cyto' => 'Tarif Cyto',
            'tarif_tindakan' => 'Jumlah Tarif',
            'jumlah_tarif' => 'Jumlah Tarif',
            'dokterdpjp_id' => 'Dokter Pemeriksa',
            'dokterdelegasi_id' => 'Dokterdelegasi ID',
            'perawat1_id' => 'Perawat 1',
            'perawat2_id' => 'Perawat 2',
            'status_implementasi' => 'Status Implementasi',
            'catatan' => 'Catatan',
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
