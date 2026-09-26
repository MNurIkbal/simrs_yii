<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatalkespasien_t".
 *
 * @property int $obatalkespasien_id
 * @property int $sumberdana_id
 * @property int $racikan_id
 * @property int $returresepdetail_id
 * @property int $tipepaket_id
 * @property int $ruangan_id
 * @property int $carabayar_id
 * @property int $pegawai_id
 * @property int $daftartindakan_id
 * @property int $tindakanpelayanan_id
 * @property int $satuankecil_id
 * @property int $shift_id
 * @property int $pendaftaran_id
 * @property int $obatalkes_id
 * @property int $pasien_id
 * @property int $penjamin_id
 * @property int $kelaspelayanan_id
 * @property int $pasienanastesi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienadmisi_id
 * @property int $obatsudahbayar_id
 * @property int $penjualanresep_id
 * @property string $tglpelayanan
 * @property string $r
 * @property int $rke
 * @property int $permintaan_oa
 * @property int $jmlkemasan_oa
 * @property int $kekuatan_oa
 * @property string $satuankekuatan_oa
 * @property double $qty_oa
 * @property double $hargasatuan_oa
 * @property string $signa_oa
 * @property double $harganetto_oa
 * @property double $hargajual_oa
 * @property string $etiket
 * @property double $jmlexposerad
 * @property string $kontrasrad
 * @property double $biayaservice
 * @property double $biayakonseling
 * @property double $jasadokterresep
 * @property double $biayakemasan
 * @property double $biayaadministrasi
 * @property double $tarifcyto
 * @property double $discount
 * @property double $subsidiasuransi
 * @property double $subsidipemerintah
 * @property double $subsidirs
 * @property double $iurbiaya
 * @property string $oa
 * @property double $pembulatan
 * @property int $verifikasitagihan_id
 * @property int $jurnalrekening_id
 * @property int $permohonanoadetail_id
 * @property int $persenppnjual
 * @property int $resepturdetail_id
 * @property double $nilaippnjual
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
 * @property int $pemakaianambulan_id
 *
 * @property ObatalkeskomponenT[] $obatalkeskomponenTs
 */
class ObatAlkesPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'obatalkespasien_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sumberdana_id', 'racikan_id', 'returresepdetail_id', 'tipepaket_id', 'ruangan_id', 'carabayar_id', 'pegawai_id', 'daftartindakan_id', 'tindakanpelayanan_id', 'satuankecil_id', 'shift_id', 'pendaftaran_id', 'obatalkes_id', 'pasien_id', 'penjamin_id', 'kelaspelayanan_id', 'pasienanastesi_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'obatsudahbayar_id', 'penjualanresep_id', 'rke', 'permintaan_oa', 'jmlkemasan_oa', 'kekuatan_oa', 'verifikasitagihan_id', 'jurnalrekening_id', 'permohonanoadetail_id', 'persenppnjual', 'resepturdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pemakaianambulan_id'], 'default', 'value' => null],
            [['sumberdana_id', 'racikan_id', 'returresepdetail_id', 'tipepaket_id', 'ruangan_id', 'carabayar_id', 'pegawai_id', 'daftartindakan_id', 'tindakanpelayanan_id', 'satuankecil_id', 'shift_id', 'pendaftaran_id', 'obatalkes_id', 'pasien_id', 'penjamin_id', 'kelaspelayanan_id', 'pasienanastesi_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'obatsudahbayar_id', 'penjualanresep_id', 'rke', 'permintaan_oa', 'jmlkemasan_oa', 'kekuatan_oa', 'verifikasitagihan_id', 'jurnalrekening_id', 'permohonanoadetail_id', 'persenppnjual', 'resepturdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglpelayanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['satuankekuatan_oa', 'etiket', 'kontrasrad', 'additional_data'], 'string'],
            [['qty_oa', 'hargasatuan_oa', 'harganetto_oa', 'hargajual_oa', 'jmlexposerad', 'biayaservice', 'biayakonseling', 'jasadokterresep', 'biayakemasan', 'biayaadministrasi', 'tarifcyto', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'pembulatan', 'nilaippnjual'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['r', 'oa'], 'string', 'max' => 1],
            [['signa_oa'], 'string', 'max' => 53],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'sumberdana_id' => 'Sumberdana ID',
            'racikan_id' => 'Racikan ID',
            'returresepdetail_id' => 'Returresepdetail ID',
            'tipepaket_id' => 'Tipepaket ID',
            'ruangan_id' => 'Ruangan ID',
            'carabayar_id' => 'Carabayar ID',
            'pegawai_id' => 'Pegawai ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'satuankecil_id' => 'Satuankecil ID',
            'shift_id' => 'Shift ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'obatalkes_id' => 'Obatalkes ID',
            'pasien_id' => 'Pasien ID',
            'penjamin_id' => 'Penjamin ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'pasienanastesi_id' => 'Pasienanastesi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'obatsudahbayar_id' => 'Obatsudahbayar ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'tglpelayanan' => 'Tglpelayanan',
            'r' => 'R',
            'rke' => 'Rke',
            'permintaan_oa' => 'Permintaan Oa',
            'jmlkemasan_oa' => 'Jmlkemasan Oa',
            'kekuatan_oa' => 'Kekuatan Oa',
            'satuankekuatan_oa' => 'Satuankekuatan Oa',
            'qty_oa' => 'Qty Oa',
            'hargasatuan_oa' => 'Hargasatuan Oa',
            'signa_oa' => 'Signa Oa',
            'harganetto_oa' => 'Harganetto Oa',
            'hargajual_oa' => 'Hargajual Oa',
            'etiket' => 'Etiket',
            'jmlexposerad' => 'Jmlexposerad',
            'kontrasrad' => 'Kontrasrad',
            'biayaservice' => 'Biayaservice',
            'biayakonseling' => 'Biayakonseling',
            'jasadokterresep' => 'Jasadokterresep',
            'biayakemasan' => 'Biayakemasan',
            'biayaadministrasi' => 'Biayaadministrasi',
            'tarifcyto' => 'Tarifcyto',
            'discount' => 'Discount',
            'subsidiasuransi' => 'Subsidiasuransi',
            'subsidipemerintah' => 'Subsidipemerintah',
            'subsidirs' => 'Subsidirs',
            'iurbiaya' => 'Iurbiaya',
            'oa' => 'Oa',
            'pembulatan' => 'Pembulatan',
            'verifikasitagihan_id' => 'Verifikasitagihan ID',
            'jurnalrekening_id' => 'Jurnalrekening ID',
            'permohonanoadetail_id' => 'Permohonanoadetail ID',
            'persenppnjual' => 'Persenppnjual',
            'resepturdetail_id' => 'Resepturdetail ID',
            'nilaippnjual' => 'Nilaippnjual',
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
            'pemakaianambulan_id' => 'Pemakaian Ambulan ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkeskomponenTs()
    {
        return $this->hasMany(ObatalkeskomponenT::className(), ['obatalkespasien_id' => 'obatalkespasien_id']);
    }
}
