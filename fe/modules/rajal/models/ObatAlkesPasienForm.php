<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 16:03:50
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-13 16:16:52
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class ObatAlkesPasienForm extends \yii\base\Model
{
    public $obatalkespasien_id;
    public $sumberdana_id;
    public $racikan_id;
    public $returresepdet_id;
    public $tipepaket_id;
    public $ruangan_id;
    public $carabayar_id;
    public $pegawai_id;
    public $daftartindakan_id;
    public $tindakanpelayanan_id;
    public $satuankecil_id;
    public $shift_id;
    public $pendaftaran_id;
    public $obatalkes_id;
    public $pasien_id;
    public $penjamin_id;
    public $kelaspelayanan_id;
    public $pasienanastesi_id;
    public $pasienmasukpenunjang_id;
    public $pasienadmisi_id;
    public $oasudahbayar_id;
    public $penjualanresep_id;
    public $tglpelayanan;
    public $r;
    public $rke;
    public $permintaan_oa;
    public $jmlkemasan_oa;
    public $kekuatan_oa;
    public $satuankekuatan_oa;
    public $qty_oa;
    public $hargasatuan_oa;
    public $signa_oa;
    public $harganetto_oa;
    public $hargajual_oa;
    public $etiket;
    public $jmlexposerad;
    public $kontrasrad;
    public $biayaservice;
    public $biayakonseling;
    public $jasadokterresep;
    public $biayakemasan;
    public $biayaadministrasi;
    public $tarifcyto;
    public $discount;
    public $subsidiasuransi;
    public $subsidipemerintah;
    public $subsidirs;
    public $iurbiaya;
    public $oa;
    public $pembulatan;
    public $verifikasitagihan_id;
    public $jurnalrekening_id;
    public $permohonanoadetail_id;
    public $persenppnjual;
    public $resepturdetail_id;
    public $nilaippnjual;
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
    public $obat;
    public $alkes;
    public $jumlah_tarif;
    public $perawat1_id;
    public $perawat2_id;

    // additional attributes
    public $obatalkes_nama;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'obatalkespasien_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkespasien_id', 'sumberdana_id', 'racikan_id', 'returresepdet_id', 'tipepaket_id', 'ruangan_id', 'carabayar_id', 'pegawai_id', 'daftartindakan_id', 'tindakanpelayanan_id', 'satuankecil_id', 'shift_id', 'pendaftaran_id', 'obatalkes_id', 'pasien_id', 'penjamin_id', 'kelaspelayanan_id', 'pasienanastesi_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'oasudahbayar_id', 'penjualanresep_id', 'rke', 'permintaan_oa', 'jmlkemasan_oa', 'kekuatan_oa', 'verifikasitagihan_id', 'jurnalrekening_id', 'permohonanoadetail_id', 'persenppnjual', 'resepturdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['obatalkespasien_id', 'sumberdana_id', 'racikan_id', 'returresepdet_id', 'tipepaket_id', 'ruangan_id', 'carabayar_id', 'pegawai_id', 'tindakanpelayanan_id', 'satuankecil_id', 'shift_id', 'pendaftaran_id', 'obatalkes_id', 'pasien_id', 'penjamin_id', 'kelaspelayanan_id', 'pasienanastesi_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'oasudahbayar_id', 'penjualanresep_id', 'rke', 'permintaan_oa', 'jmlkemasan_oa', 'kekuatan_oa', 'verifikasitagihan_id', 'jurnalrekening_id', 'permohonanoadetail_id', 'persenppnjual', 'resepturdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'obat', 'perawat1_id'], 'integer'],
            [['tglpelayanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['satuankekuatan_oa', 'signa_oa', 'etiket', 'kontrasrad', 'additional_data'], 'string'],
            [['qty_oa', 'harganetto_oa', 'hargajual_oa', 'jmlexposerad', 'biayaservice', 'biayakonseling', 'jasadokterresep', 'biayakemasan', 'biayaadministrasi', 'tarifcyto', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'pembulatan', 'nilaippnjual'], 'number'],
            [['obatalkes_id', 'obat', 'qty_oa', 'perawat1_id'], 'required'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['daftartindakan_id'], 'safe'],
            [['r', 'oa'], 'string', 'max' => 1],
            [['obatalkespasien_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'obatalkespasien_id' => Yii::t('fe', 'obatalkespasien_id'),
            'sumberdana_id' => Yii::t('fe', 'Sumberdana ID'),
            'racikan_id' => Yii::t('fe', 'Racikan ID'),
            'returresepdet_id' => Yii::t('fe', 'Returresepdet ID'),
            'tipepaket_id' => Yii::t('fe', 'Tipepaket ID'),
            'ruangan_id' => Yii::t('fe', 'Ruangan ID'),
            'carabayar_id' => Yii::t('fe', 'Carabayar ID'),
            'pegawai_id' => Yii::t('fe', 'Pegawai ID'),
            'daftartindakan_id' => Yii::t('fe', 'Nama tindakan'),
            'tindakanpelayanan_id' => Yii::t('fe', 'Tindakanpelayanan ID'),
            'satuankecil_id' => Yii::t('fe', 'Satuankecil ID'),
            'shift_id' => Yii::t('fe', 'Shift ID'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran ID'),
            'obatalkes_id' => Yii::t('fe', 'Pemakaian obat/alkes'),
            'pasien_id' => Yii::t('fe', 'Pasien ID'),
            'penjamin_id' => Yii::t('fe', 'Penjamin ID'),
            'kelaspelayanan_id' => Yii::t('fe', 'Kelaspelayanan ID'),
            'pasienanastesi_id' => Yii::t('fe', 'Pasienanastesi ID'),
            'pasienmasukpenunjang_id' => Yii::t('fe', 'Pasienmasukpenunjang ID'),
            'pasienadmisi_id' => Yii::t('fe', 'Pasienadmisi ID'),
            'oasudahbayar_id' => Yii::t('fe', 'Oasudahbayar ID'),
            'penjualanresep_id' => Yii::t('fe', 'Penjualanresep ID'),
            'tglpelayanan' => Yii::t('fe', 'tglpelayanan'),
            'r' => 'R',
            'rke' => Yii::t('fe', 'R -'),
            'permintaan_oa' => Yii::t('fe', 'Permintaan'),
            'jmlkemasan_oa' => Yii::t('fe', 'Jumlah kemasan'),
            'kekuatan_oa' => Yii::t('fe', 'Kekuatan'),
            'satuankekuatan_oa' => Yii::t('fe', 'Satuan kekuatan'),
            'qty_oa' => Yii::t('fe', 'Kuantitas'),
            'hargasatuan_oa' => Yii::t('fe', 'Harga satuan'),
            'signa_oa' => Yii::t('fe', 'Signa'),
            'harganetto_oa' => Yii::t('fe', 'Harga neto'),
            'hargajual_oa' => Yii::t('fe', 'Harga jual'),
            'etiket' => Yii::t('fe', 'Etiket'),
            'jmlexposerad' => 'Jmlexposerad',
            'kontrasrad' => 'Kontrasrad',
            'biayaservice' => Yii::t('fe', 'Biaya pelayanan'),
            'biayakonseling' => Yii::t('fe', 'Biaya konseling'),
            'jasadokterresep' => Yii::t('fe', 'Jasa dokter resep'),
            'biayakemasan' => Yii::t('fe', 'Biaya kemasan'),
            'biayaadministrasi' => Yii::t('fe', 'Biaya administrasi'),
            'tarifcyto' => Yii::t('fe', 'Tarif cyto'),
            'discount' => Yii::t('fe', 'Diskon'),
            'subsidiasuransi' => Yii::t('fe', 'Subsidi asuransi'),
            'subsidipemerintah' => Yii::t('fe', 'Subsidi pemerintah'),
            'subsidirs' => Yii::t('fe', 'Subsidi rumah sakit'),
            'iurbiaya' => 'Iurbiaya',
            'oa' => Yii::t('fe', 'Obat alkes'),
            'pembulatan' => Yii::t('fe', 'Pembulatan'),
            'verifikasitagihan_id' => 'Verifikasitagihan ID',
            'jurnalrekening_id' => 'Jurnalrekening ID',
            'permohonanoadetail_id' => 'Permohonanoadetail ID',
            'persenppnjual' => 'Persenppnjual',
            'resepturdetail_id' => 'Resepturdetail ID',
            'nilaippnjual' => 'Nilaippnjual',
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
            'obat' => Yii::t('fe', 'Jenis Pemakaian'),
            'alkes' => Yii::t('fe', 'Alkes'),
            'jumlah_tarif' => Yii::t('fe', 'Jumlah tarif'),
            'perawat1_id' => Yii::t('fe', 'Perawat 1'),
            'perawat2_id' => Yii::t('fe', 'Perawat 2'),
        ];
    }
}