<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanpemesananobatalkes_v".
 *
 * @property int $pesanobatalkes_id
 * @property string $tglpemesanan
 * @property int $ruangan_id
 * @property string $ruangan_tujuan
 * @property int $instalasi_id
 * @property string $instalasi_tujuan
 * @property string $nopemesanan
 * @property int $ruanganpemesan_id
 * @property int $ruangan_pemesan_id
 * @property string $ruangan_pemesan
 * @property int $instalasi_pemesan_id
 * @property string $instalasi_pemesan
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property double $jumlah_pesan
 * @property string $tglmintadikirim
 * @property int $mutasiobatdetail_id
 * @property int $satuankecil_id
 * @property int $pesanobatdetail_id
 * @property int $satuanbesar_id
 * @property string $satuan_besar
 * @property string $satuan_kecil
 * @property double $qty_besar
 */
class LaporanPemesananObatalkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanpemesananobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id', 'obatalkes_id', 'mutasiobatdetail_id', 'satuankecil_id', 'pesanobatdetail_id', 'satuanbesar_id'], 'default', 'value' => null],
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id', 'obatalkes_id', 'mutasiobatdetail_id', 'satuankecil_id', 'pesanobatdetail_id', 'satuanbesar_id'], 'integer'],
            [['tglpemesanan', 'tglmintadikirim'], 'safe'],
            [['nopemesanan', 'obatalkes_namalain', 'satuan_besar', 'satuan_kecil'], 'string'],
            [['jumlah_pesan', 'qty_besar'], 'number'],
            [['ruangan_tujuan', 'instalasi_tujuan', 'ruangan_pemesan', 'instalasi_pemesan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatalkes_id' => 'Pesanobatalkes ID',
            'tglpemesanan' => 'Tglpemesanan',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_tujuan' => 'Instalasi Tujuan',
            'nopemesanan' => 'Nopemesanan',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'ruangan_pemesan_id' => 'Ruangan Pemesan ID',
            'ruangan_pemesan' => 'Ruangan Pemesan',
            'instalasi_pemesan_id' => 'Instalasi Pemesan ID',
            'instalasi_pemesan' => 'Instalasi Pemesan',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'jumlah_pesan' => 'Jumlah Pesan',
            'tglmintadikirim' => 'Tglmintadikirim',
            'mutasiobatdetail_id' => 'Mutasiobatdetail ID',
            'satuankecil_id' => 'Satuankecil ID',
            'pesanobatdetail_id' => 'Pesanobatdetail ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuan_besar' => 'Satuan Besar',
            'satuan_kecil' => 'Satuan Kecil',
            'qty_besar' => 'Qty Besar',
        ];
    }
}
