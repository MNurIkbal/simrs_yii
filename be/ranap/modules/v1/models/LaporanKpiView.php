<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanpasienri_v".
 * @property string $patien_type
 * @property string $no_pendaftaran
 * @property string $tgl_admisi
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tgl_lahir
 * @property string $kamar_terakhir
 * @property string $jenis_kamar
 * @property string $ruangan_terakhir
 * @property string $nama_dpjp
 * @property string $tglpasienpulang
 * @property string $kondisi_pulang
 * @property string $cara_keluar
 * @property string $penjamin_nama
 * @property string $tgl_pendaftaran
 * @property string $tgl_masukkamar
 * @property string $tgl_rencanapulang 
 * @property string $nomor_tagihan 
 * @property string $tgl_tagihan
 * @property string $tgl_bayar
 * @property string $tgl_dibersihkan
 * @property int $jam_ranap
 * @property int $jam_tunggu
 */
class LaporanKpiView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporankpiranap_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jam_ranap', 'jam_tunggu'], 'integer'],
            [['patien_type','no_pendaftaran','tgl_admisi','no_rekam_medik','nama_pasien','tgl_lahir','kamar_terakhir','jenis_kamar','ruangan_terakhir','nama_dpjp','tglpasienpulang','kondisi_pulang','cara_keluar','penjamin_nama','tgl_pendaftaran','tgl_masukkamar','tgl_rencanapulang','nomor_tagihan','tgl_tagihan','tgl_bayar','tgl_dibersihkan'],'string'],
        ];
    }

}
