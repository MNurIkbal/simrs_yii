<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 16:02:11
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-16 17:30:51
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class TindakanPelayananForm extends \yii\base\Model
{
    public $tindakanpelayanan_id;
    public $detailhasilpemeriksaanlab_id;
    public $shift_id;
    public $kelaspelayanan_id;
    public $kelastanggungan_id;
    public $pasien_id;
    public $rencanaoperasi_id;
    public $instalasi_id;
    public $daftartindakan_id;
    public $alatmedis_id;
    public $tipepaket_id;
    public $tindakansudahbayar_id;
    public $karcis_id;
    public $carabayar_id;
    public $pendaftaran_id;
    public $hasilpemeriksaanrad_id;
    public $jeniskasuspenyakit_id;
    public $hasilpemeriksaanrm_id;
    public $ruangan_id;
    public $konsulpoli_id;
    public $pasienmasukpenunjang_id;
    public $hasilpemeriksaanpa_id;
    public $penjamin_id;
    public $pasienadmisi_id;
    public $verifikasitagihan_id;
    public $jurnalrekening_id;
    public $rencanatindakan_id;
    public $tgl_tindakan;
    public $tarif_rsakomodasi;
    public $tarif_medis;
    public $tarif_paramedis;
    public $tarif_bhp;
    public $tarif_satuan;
    public $tarif_tindakan;
    public $tarifcyto_tindakan;
    public $satuan_tindakan;
    public $qty_tindakan;
    public $cyto_tindakan;
    public $dokterpenanggungjawab_id;
    public $dokterpelaksana_id;
    public $dokteranastesi_id;
    public $dokterdelegasi_id;
    public $bidan1_id;
    public $bidan2_id;
    public $perawat1_id;
    public $perawat2_id;
    public $discount_tindakan;
    public $pembebasan_tindakan;
    public $subsidiasuransi_tindakan;
    public $subsidipemerintah_tindakan;
    public $subsisidirumahsakit_tindakan;
    public $uangditerima_tindakan;
    public $keterangantindakan;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $pembulatan;
    public $text_cyto_tindakan;
    
    // additional attributes
    public $daftartindakan_nama;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tindakanpelayanan_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['detailhasilpemeriksaanlab_id', 'shift_id', 'kelaspelayanan_id', 'kelastanggungan_id', 'pasien_id', 'rencanaoperasi_id', 'instalasi_id', 'daftartindakan_id', 'alatmedis_id', 'tipepaket_id', 'tindakansudahbayar_id', 'karcis_id', 'carabayar_id', 'pendaftaran_id', 'hasilpemeriksaanrad_id', 'jeniskasuspenyakit_id', 'hasilpemeriksaanrm_id', 'ruangan_id', 'konsulpoli_id', 'pasienmasukpenunjang_id', 'hasilpemeriksaanpa_id', 'penjamin_id', 'pasienadmisi_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'rencanatindakan_id', 'qty_tindakan', 'dokterpenanggungjawab_id', 'dokterpelaksana_id', 'dokteranastesi_id', 'dokterdelegasi_id', 'bidan1_id', 'bidan2_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelaspelayanan_id', 'pasien_id', 'instalasi_id', 'daftartindakan_id', 'carabayar_id', 'pendaftaran_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'penjamin_id', 'tgl_tindakan', 'qty_tindakan', 'dokterpenanggungjawab_id'], 'required'],
            [['tgl_tindakan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['cyto_tindakan', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['satuan_tindakan'], 'string', 'max' => 10],
            [['keterangantindakan'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => Yii::t('fe', 'tindakanpelayanan_id'),
            'detailhasilpemeriksaanlab_id' => Yii::t('fe', 'detailhasilpemeriksaanlab_id'),
            'shift_id' => Yii::t('fe', 'shift_id'),
            'kelaspelayanan_id' => Yii::t('fe', 'kelaspelayanan_id'),
            'kelastanggungan_id' => Yii::t('fe', 'kelastanggungan_id'),
            'pasien_id' => Yii::t('fe', 'pasien_id'),
            'rencanaoperasi_id' => Yii::t('fe', 'rencanaoperasi_id'),
            'instalasi_id' => Yii::t('fe', 'instalasi_id'),
            'daftartindakan_id' => Yii::t('fe', 'Daftar Tindakan'),
            'alatmedis_id' => Yii::t('fe', 'alatmedis_id'),
            'tipepaket_id' => Yii::t('fe', 'tipepaket_id'),
            'tindakansudahbayar_id' => Yii::t('fe', 'tindakansudahbayar_id'),
            'karcis_id' => Yii::t('fe', 'karcis_id'),
            'carabayar_id' => Yii::t('fe', 'carabayar_id'),
            'pendaftaran_id' => Yii::t('fe', 'pendaftaran_id'),
            'hasilpemeriksaanrad_id' => Yii::t('fe', 'hasilpemeriksaanrad_id'),
            'jeniskasuspenyakit_id' => Yii::t('fe', 'jeniskasuspenyakit_id'),
            'hasilpemeriksaanrm_id' => Yii::t('fe', 'hasilpemeriksaanrm_id'),
            'ruangan_id' => Yii::t('fe', 'ruangan_id'),
            'konsulpoli_id' => Yii::t('fe', 'konsulpoli_id'),
            'pasienmasukpenunjang_id' => Yii::t('fe', 'pasienmasukpenunjang_id'),
            'hasilpemeriksaanpa_id' => Yii::t('fe', 'hasilpemeriksaanpa_id'),
            'penjamin_id' => Yii::t('fe', 'penjamin_id'),
            'pasienadmisi_id' => Yii::t('fe', 'pasienadmisi_id'),
            'verifikasitagihan_id' => Yii::t('fe', 'verifikasitagihan_id'),
            'jurnalrekening_id' => Yii::t('fe', 'jurnalrekening_id'),
            'rencanatindakan_id' => Yii::t('fe', 'rencanatindakan_id'),
            'tgl_tindakan' => Yii::t('fe', 'Tanggal Tindakan'),
            'tarif_rsakomodasi' => Yii::t('fe', 'Tarif Rsakomodasi'),
            'tarif_medis' => Yii::t('fe', 'Tarif Medis'),
            'tarif_paramedis' => Yii::t('fe', 'Tarif Paramedis'),
            'tarif_bhp' => Yii::t('fe', 'Tarif Bhp'),
            'tarif_satuan' => Yii::t('fe', 'Tarif Satuan'),
            'tarif_tindakan' => Yii::t('fe', 'Jumlah Tarif'),
            'tarifcyto_tindakan' => Yii::t('fe', 'tarifcyto_tindakan'),
            'satuan_tindakan' => Yii::t('fe', 'satuan_tindakan'),
            'qty_tindakan' => Yii::t('fe', 'Jumlah Tindakan'),
            'cyto_tindakan' => Yii::t('fe', 'Cyto'),
            'dokterpenanggungjawab_id' => Yii::t('fe', 'Dokter Pemeriksa'),
            'dokterpelaksana_id' => Yii::t('fe', 'dokterpelaksana_id'),
            'dokteranastesi_id' => Yii::t('fe', 'dokteranastesi_id'),
            'dokterdelegasi_id' => Yii::t('fe', 'Dokter Delegasi'),
            'bidan1_id' => Yii::t('fe', 'Bidan1 ID'),
            'bidan2_id' => Yii::t('fe', 'Bidan2 ID'),
            'perawat1_id' => Yii::t('fe', 'Perawat 1'),
            'perawat2_id' => Yii::t('fe', 'Perawat 2'),
            'discount_tindakan' => Yii::t('fe', 'discount_tindakan'),
            'pembebasan_tindakan' => Yii::t('fe', 'Pembebasan Tindakan'),
            'subsidiasuransi_tindakan' => Yii::t('fe', 'Subsidiasuransi Tindakan'),
            'subsidipemerintah_tindakan' => Yii::t('fe', 'Subsidipemerintah Tindakan'),
            'subsisidirumahsakit_tindakan' => Yii::t('fe', 'Subsisidirumahsakit Tindakan'),
            'uangditerima_tindakan' => Yii::t('fe', 'Uangditerima Tindakan'),
            'keterangantindakan' => Yii::t('fe', 'Keterangantindakan'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
            'pembulatan' => Yii::t('fe', 'deleted_by'),
            'daftartindakan_nama' => Yii::t('fe', 'daftartindakan_nama'),
            'text_cyto_tindakan' => Yii::t('fe', 'Cyto'),
        ];
    }
}