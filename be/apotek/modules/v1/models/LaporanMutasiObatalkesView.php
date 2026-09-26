<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanmutasiobatalkes_v".
 *
 * @property int $mutasiobatdetail_id
 * @property int $mutasiobatruangan_id
 * @property string $nomutasioa
 * @property string $tglmutasioa
 * @property int $pesanobatalkes_id
 * @property string $nopemesanan
 * @property int $instalasi_tujuan_id
 * @property string $instalasi_nama
 * @property int $ruangan_tujuan_id
 * @property string $ruangan_nama
 * @property int $instalasi_asal_id
 * @property string $instalasi_asal
 * @property int $ruangan_asal_id
 * @property string $ruangan_asal
 * @property double $jumlah_mutasi
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property int $satuankecil_id
 * @property string $satuankecil_nama
 * @property double $harga_netto
 * @property double $harga_jualsatuan
 * @property string $pegawai_mutasi
 * @property string $pegawai_mengetahui
 */
class LaporanMutasiObatalkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanmutasiobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasiobatdetail_id', 'mutasiobatruangan_id', 'pesanobatalkes_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'obatalkes_id', 'satuankecil_id'], 'default', 'value' => null],
            [['mutasiobatdetail_id', 'mutasiobatruangan_id', 'pesanobatalkes_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'obatalkes_id', 'satuankecil_id'], 'integer'],
            [['nomutasioa', 'nopemesanan', 'obatalkes_namalain', 'satuankecil_nama'], 'string'],
            [['tglmutasioa'], 'safe'],
            [['jumlah_mutasi', 'harga_netto', 'harga_jualsatuan'], 'number'],
            [['instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal', 'pegawai_mutasi', 'pegawai_mengetahui'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatdetail_id' => 'Mutasiobatdetail ID',
            'mutasiobatruangan_id' => 'Mutasiobatruangan ID',
            'nomutasioa' => 'Nomutasioa',
            'tglmutasioa' => 'Tglmutasioa',
            'pesanobatalkes_id' => 'Pesanobatalkes ID',
            'nopemesanan' => 'Nopemesanan',
            'instalasi_tujuan_id' => 'Instalasi Tujuan ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_tujuan_id' => 'Ruangan Tujuan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_asal_id' => 'Instalasi Asal ID',
            'instalasi_asal' => 'Instalasi Asal',
            'ruangan_asal_id' => 'Ruangan Asal ID',
            'ruangan_asal' => 'Ruangan Asal',
            'jumlah_mutasi' => 'Jumlah Mutasi',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'satuankecil_id' => 'Satuankecil ID',
            'satuankecil_nama' => 'Satuankecil Nama',
            'harga_netto' => 'Harga Netto',
            'harga_jualsatuan' => 'Harga Jualsatuan',
            'pegawai_mutasi' => 'Pegawai Mutasi',
            'pegawai_mengetahui' => 'Pegawai Mengetahui',
        ];
    }
}
