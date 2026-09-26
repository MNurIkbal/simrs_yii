<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;


class ObatAlkesPasien extends \Integrasi\Components\ActiveRepositories
{
    public $stok_obat;
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
            [['sumberdana_id', 'racikan_id', 'returresepdetail_id', 'tipepaket_id', 'ruangan_id', 'carabayar_id', 'pegawai_id', 'daftartindakan_id', 'tindakanpelayanan_id', 'satuankecil_id', 'shift_id', 'pendaftaran_id', 'obatalkes_id', 'pasien_id', 'penjamin_id', 'kelaspelayanan_id', 'pasienanastesi_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'obatsudahbayar_id', 'penjualanresep_id', 'rke', 'permintaan_oa', 'jmlkemasan_oa', 'kekuatan_oa', 'verifikasitagihan_id', 'jurnalrekening_id', 'permohonanoadetail_id', 'persenppnjual', 'resepturdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'perawat1_id', 'perawat2_id'], 'default', 'value' => null],
            [['qty_oa'],'checkStok'],
            [['sumberdana_id', 'racikan_id', 'returresepdetail_id', 'tipepaket_id', 'ruangan_id', 'carabayar_id', 'pegawai_id', 'daftartindakan_id', 'tindakanpelayanan_id', 'satuankecil_id', 'shift_id', 'pendaftaran_id', 'obatalkes_id', 'pasien_id', 'penjamin_id', 'kelaspelayanan_id', 'pasienanastesi_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'obatsudahbayar_id', 'penjualanresep_id', 'rke', 'permintaan_oa', 'jmlkemasan_oa', 'kekuatan_oa', 'verifikasitagihan_id', 'jurnalrekening_id', 'permohonanoadetail_id', 'persenppnjual', 'resepturdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'perawat1_id', 'perawat2_id'], 'integer'],
            [['tglpelayanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['satuankekuatan_oa', 'etiket', 'kontrasrad', 'additional_data'], 'string'],
            [['qty_oa', 'hargasatuan_oa', 'harganetto_oa', 'hargajual_oa', 'jmlexposerad', 'biayaservice', 'biayakonseling', 'jasadokterresep', 'biayakemasan', 'biayaadministrasi', 'tarifcyto', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'pembulatan', 'nilaippnjual'], 'number'],
            [['is_deleted', 'is_active', 'is_jurnal'], 'boolean'],
            [['r', 'oa'], 'string', 'max' => 1],
            [['signa_oa'], 'string', 'max' => 53],
        ];
    }

    public function checkStok($attributes, $params)
    {
        if ($this->stok_obat < $this->qty_oa) {
            $this->addError("qty","Stok Tidak Mencukupi");
            return false;
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios["jurnal"] = ['is_jurnal'];

        return $scenarios;
    }
}
