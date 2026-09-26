<?php

namespace Integrasi\Service\Fisioterapi\Models;

use Yii;

/**
 * This is the model class for table "tindakanpelayanan_t".
 *
 * @property int $tindakanpelayanan_id
 * @property int $shift_id
 * @property int $kelaspelayanan_id
 * @property int $kelastanggungan_id
 * @property int $pasien_id
 * @property int $rencanaoperasi_id
 * @property int $instalasi_id
 * @property int $daftartindakan_id
 * @property int $alatmedis_id
 * @property int $tipepaket_id
 * @property int $tindakansudahbayar_id
 * @property int $carabayar_id
 * @property int $pendaftaran_id
 * @property int $hasilpemeriksaanrad_id
 * @property int $jeniskasuspenyakit_id
 * @property int $hasilpemeriksaanrm_id
 * @property int $ruangan_id
 * @property int $konsulpoli_id
 * @property int $pasienmasukpenunjang_id
 * @property int $hasilpemeriksaanlabdetail_id
 * @property int $penjamin_id
 * @property int $pasienadmisi_id
 * @property int $verifikasitagihan_id
 * @property int $jurnalrekening_id
 * @property int $instruksitindakan_id
 * @property string $tgl_tindakan
 * @property double $tarif_rsakomodasi
 * @property double $tarif_medis
 * @property double $tarif_paramedis
 * @property double $tarif_bhp
 * @property double $tarif_satuan
 * @property double $tarif_tindakan
 * @property double $tarifcyto_tindakan
 * @property string $satuan_tindakan
 * @property int $qty_tindakan
 * @property bool $cyto_tindakan
 * @property int $dokterpenanggungjawab_id
 * @property int $dokterpelaksana_id
 * @property int $dokteranastesi_id
 * @property int $dokterdelegasi_id
 * @property int $bidan1_id
 * @property int $bidan2_id
 * @property int $perawat1_id
 * @property int $perawat2_id
 * @property double $discount_tindakan
 * @property double $pembebasan_tindakan
 * @property double $subsidiasuransi_tindakan
 * @property double $subsidipemerintah_tindakan
 * @property double $subsisidirumahsakit_tindakan
 * @property double $uangditerima_tindakan
 * @property string $keterangantindakan
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
 * @property double $pembulatan
 *
 */
class TindakanPelayanan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakanpelayanan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shift_id', 'kelaspelayanan_id', 'kelastanggungan_id', 'pasien_id', 'rencanaoperasi_id', 'instalasi_id', 'daftartindakan_id', 'alatmedis_id', 'tipepaket_id', 'tindakansudahbayar_id', 'carabayar_id', 'pendaftaran_id', 'hasilpemeriksaanrad_id', 'jeniskasuspenyakit_id', 'hasilpemeriksaanrm_id', 'ruangan_id', 'konsulpoli_id', 'pasienmasukpenunjang_id', 'hasilpemeriksaanlabdetail_id', 'penjamin_id', 'pasienadmisi_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'instruksitindakan_id', 'qty_tindakan', 'dokterpenanggungjawab_id', 'dokterpelaksana_id', 'dokteranastesi_id', 'dokterdelegasi_id', 'bidan1_id', 'bidan2_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['shift_id', 'kelaspelayanan_id', 'kelastanggungan_id', 'pasien_id', 'rencanaoperasi_id', 'instalasi_id', 'daftartindakan_id', 'alatmedis_id', 'tipepaket_id', 'tindakansudahbayar_id', 'carabayar_id', 'pendaftaran_id', 'hasilpemeriksaanrad_id', 'jeniskasuspenyakit_id', 'hasilpemeriksaanrm_id', 'ruangan_id', 'konsulpoli_id', 'pasienmasukpenunjang_id', 'hasilpemeriksaanlabdetail_id', 'penjamin_id', 'pasienadmisi_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'instruksitindakan_id', 'qty_tindakan', 'dokterpenanggungjawab_id', 'dokterpelaksana_id', 'dokteranastesi_id', 'dokterdelegasi_id', 'bidan1_id', 'bidan2_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[
                'daftartindakan_id',
                'dokterpenanggungjawab_id', 
            ], 'required'],
            [['programterapi_id','programterapidetail_id','tgl_tindakan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['tarif_rsakomodasi', 'tarif_medis', 'tarif_paramedis', 'tarif_bhp', 'tarif_satuan', 'tarif_tindakan', 'tarifcyto_tindakan', 'discount_tindakan', 'pembebasan_tindakan', 'subsidiasuransi_tindakan', 'subsidipemerintah_tindakan', 'subsisidirumahsakit_tindakan', 'uangditerima_tindakan', 'pembulatan'], 'number'],
            [['cyto_tindakan', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangantindakan', 'additional_data'], 'string'],
            [['satuan_tindakan'], 'string', 'max' => 10],
        ];
    }


}
