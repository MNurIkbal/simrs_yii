<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanpenjualanresepdetail_v".
 *
 * @property string $tglpenjualan
 * @property string $noresep
 * @property string $nama_dokter
 * @property string $nama_pasien
 * @property string $nama_pembeli
 * @property string $nama_pegawai
 * @property string $no_pendaftaran
 * @property string $nama_obat
 * @property int $jumlah_obat
 * @property string $satuan
 * @property double $totalhargajual
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property int $penjualanresep_id
 * @property string $jenispenjualan
 * @property string $tglresep
 * @property string $noresep
 * @property double $totalhargajual
 * @property double $totaltarifservice
 * @property double $biayaadministrasi
 * @property double $biayakonseling
 * @property double $pembulatanharga
 * @property double $jasadokterresep
 */
class LaporanPenjualanResepDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanpenjualanresepdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}
