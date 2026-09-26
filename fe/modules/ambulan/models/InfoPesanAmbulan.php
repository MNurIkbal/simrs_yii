<?php

namespace app\modules\ambulan\models;

use Yii;

class InfoPesanAmbulan extends \yii\base\Model
{

     public $pesanambulan_id;
     public $no_pesanambulan;
     public $tgl_pesanambulan;
     public $pendaftaran_id;
     public $pasien_id;
     public $ambulan_id;
     public $pemakaianambulan_id;
     public $pemesan;
     public $jenis_kelamin;
     public $tempat_lahir;
     public $tgl_lahir;
     public $umur;
     public $asal_pasien;
     public $keluhan;
     public $is_sadar;
     public $is_nafas;
     public $is_nadi;
     public $nama_pj;
     public $kontak_pj;
     public $tujuan_pasien;
     public $kesadaran;
     public $tanda_vital;
     public $td_systolic;
     public $td_diastolic;
     public $detaknadi;
     public $respirasi;
     public $saturasi;
     public $estimasi_biaya;
     public $keterangan;
     public $ruangan_id;
     public $status_ambulan;
     public $additional_data;
     public $created_date;
     public $created_by;
     public $modified_count;
     public $is_emergency;
     public $qty;
     public $instalasi_asal;
     public $ruangan_asal;
     public $diagnosa_pasien;
     public $jenis;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesanambulandetail_id' => 'Pesanambulandetail ID',
            'pesanambulan_id' => 'Pesanambulan ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'is_default' => 'Is Default',
            'qty_tindakan' => 'Qty Tindakan',
            'tarif_satuan' => 'Tarif Satuan',
            'jumlah_tarif' => 'Jumlah Tarif',
            'obatalkes_id' => 'Obatalkes ID',
            'qty_obat' => 'Qty Obat',
            'satuankonversi_id' => 'Satuankonversi ID',
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
