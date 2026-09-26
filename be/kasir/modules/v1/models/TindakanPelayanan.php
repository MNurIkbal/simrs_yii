<?php

namespace app\modules\v1\models;
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
 * @property double $tarifpenyulit_tindakan
 * @property string $satuan_tindakan
 * @property int $qty_tindakan
 * @property bool $cyto_tindakan
 * @property bool $penyulit_tindakan
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
 * @property int $implementasi_id
 * @property int $harga_origin
 * @property int $parent_id
 */
class TindakanPelayanan extends \Doco\components\DocoActiveRecord
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
            [['shift_id', 'kelaspelayanan_id', 'kelastanggungan_id', 'pasien_id', 'rencanaoperasi_id', 'instalasi_id', 'daftartindakan_id', 'alatmedis_id', 'tipepaket_id', 'tindakansudahbayar_id', 'carabayar_id', 'pendaftaran_id', 'hasilpemeriksaanrad_id', 'jeniskasuspenyakit_id', 'hasilpemeriksaanrm_id', 'ruangan_id', 'konsulpoli_id', 'pasienmasukpenunjang_id', 'hasilpemeriksaanlabdetail_id', 'penjamin_id', 'pasienadmisi_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'instruksitindakan_id', 'qty_tindakan', 'dokterpenanggungjawab_id', 'dokterpelaksana_id', 'dokteranastesi_id', 'dokterdelegasi_id', 'bidan1_id', 'bidan2_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'implementasi_id', 'parent_id'], 'default', 'value' => null],
            [['shift_id', 'kelaspelayanan_id', 'kelastanggungan_id', 'pasien_id', 'rencanaoperasi_id', 'instalasi_id', 'daftartindakan_id', 'alatmedis_id', 'tipepaket_id', 'tindakansudahbayar_id', 'carabayar_id', 'pendaftaran_id', 'hasilpemeriksaanrad_id', 'jeniskasuspenyakit_id', 'hasilpemeriksaanrm_id', 'ruangan_id', 'konsulpoli_id', 'pasienmasukpenunjang_id', 'hasilpemeriksaanlabdetail_id', 'penjamin_id', 'pasienadmisi_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'instruksitindakan_id', 'qty_tindakan', 'dokterpenanggungjawab_id', 'dokterpelaksana_id', 'dokteranastesi_id', 'dokterdelegasi_id', 'bidan1_id', 'bidan2_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'implementasi_id', 'tindakanpelayananasal_id', 'parent_id'], 'integer'],
            [['kelaspelayanan_id', 'pasien_id', 'instalasi_id', 'carabayar_id', 'pendaftaran_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'penjamin_id', 'tgl_tindakan'], 'required'],
            [[
                'tgl_tindakan', 
                'created_date', 
                'last_modified_date', 
                'deleted_date', 
                'additional_data', 
                'is_penatajasa', 
                'is_overwrite', 
                'implementasi_id', 
                'instruksitindakan_id', 
                'pasienmasukpenunjang_id', 
                'penyulit_tindakan', 
                'tarifpenyulit_tindakan', 
                'kamarruangan_id', 
                'kamartempattidur_id',
                'perawat1_id',
                'perawat2_id',
                'tindakanpelayananasal_id',
                'programterapi_id',
                'programterapidetail_id'
            ], 'safe'],
            [['tarif_rsakomodasi', 'tarif_medis', 'tarif_paramedis', 'tarif_bhp', 'tarif_satuan', 'tarif_tindakan', 'tarifcyto_tindakan', 'tarifpenyulit_tindakan', 'discount_tindakan', 'pembebasan_tindakan', 'subsidiasuransi_tindakan', 'subsidipemerintah_tindakan', 'subsisidirumahsakit_tindakan', 'uangditerima_tindakan', 'pembulatan', 'harga_origin'], 'number'],
            [['cyto_tindakan', 'penyulit_tindakan', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangantindakan'], 'string'],
            [['satuan_tindakan'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'shift_id' => 'Shift ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelastanggungan_id' => 'Kelastanggungan ID',
            'pasien_id' => 'Pasien ID',
            'rencanaoperasi_id' => 'Rencanaoperasi ID',
            'instalasi_id' => 'Instalasi ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'alatmedis_id' => 'Alatmedis ID',
            'tipepaket_id' => 'Tipepaket ID',
            'tindakansudahbayar_id' => 'Tindakansudahbayar ID',
            'carabayar_id' => 'Carabayar ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'hasilpemeriksaanrad_id' => 'Hasilpemeriksaanrad ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'hasilpemeriksaanrm_id' => 'Hasilpemeriksaanrm ID',
            'ruangan_id' => 'Ruangan ID',
            'konsulpoli_id' => 'Konsulpoli ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'hasilpemeriksaanlabdetail_id' => 'Hasilpemeriksaanlabdetail ID',
            'penjamin_id' => 'Penjamin ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'verifikasitagihan_id' => 'Verifikasitagihan ID',
            'jurnalrekening_id' => 'Jurnalrekening ID',
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'tgl_tindakan' => 'Tgl Tindakan',
            'tarif_rsakomodasi' => 'Tarif Rsakomodasi',
            'tarif_medis' => 'Tarif Medis',
            'tarif_paramedis' => 'Tarif Paramedis',
            'tarif_bhp' => 'Tarif Bhp',
            'tarif_satuan' => 'Tarif Satuan',
            'tarif_tindakan' => 'Tarif Tindakan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'tarifpenyulit_tindakan' => 'Tarif Penyulit Tindakan',
            'satuan_tindakan' => 'Satuan Tindakan',
            'qty_tindakan' => 'Qty Tindakan',
            'cyto_tindakan' => 'Cyto Tindakan',
            'penyulit_tindakan' => 'Penyulit Tindakan',
            'dokterpenanggungjawab_id' => 'Dokterpenanggungjawab ID',
            'dokterpelaksana_id' => 'Dokterpelaksana ID',
            'dokteranastesi_id' => 'Dokteranastesi ID',
            'dokterdelegasi_id' => 'Dokterdelegasi ID',
            'bidan1_id' => 'Bidan1 ID',
            'bidan2_id' => 'Bidan2 ID',
            'perawat1_id' => 'Perawat1 ID',
            'perawat2_id' => 'Perawat2 ID',
            'discount_tindakan' => 'Discount Tindakan',
            'pembebasan_tindakan' => 'Pembebasan Tindakan',
            'subsidiasuransi_tindakan' => 'Subsidiasuransi Tindakan',
            'subsidipemerintah_tindakan' => 'Subsidipemerintah Tindakan',
            'subsisidirumahsakit_tindakan' => 'Subsisidirumahsakit Tindakan',
            'uangditerima_tindakan' => 'Uangditerima Tindakan',
            'keterangantindakan' => 'Keterangantindakan',
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
            'pembulatan' => 'Pembulatan',
            'implementasi_id' => 'Implementasi ID',
            'is_overwrite' => 'Is Overwrite',
        ];
    }
}
