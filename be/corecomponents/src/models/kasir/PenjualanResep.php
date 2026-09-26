<?php

namespace Doco\models\kasir;

use Yii;

class PenjualanResep extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjualanresep_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['karyawan_id', 'pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'returresep_id', 'kelaspelayanan_id', 'penjamin_id', 'pasien_id', 'carabayar_id', 'ruangan_id', 'reseptur_id', 'shift_id', 'lamapelayanan', 'penjpasienpegawai_id', 'penjpasienruangan_id', 'antrianfarmasi_id', 'permohonanoa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglpenjualan', 'tglresep', 'iter','created_date', 'last_modified_date', 'penjualanresep_id','catatan', 'deleted_date','pembatalanresep_id'], 'safe'],
            [['jenispenjualan', 'noresep', 'additional_data'], 'string'],
            [['totharganetto', 'totalhargajual', 'totaltarifservice', 'biayaadministrasi', 'biayakonseling', 'pembulatanharga', 'jasadokterresep', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'takaranresep'], 'number'],
        ];
    }
}
