<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemusnahanobatdetail_v".
 *
 * @property int $pemusnahanobat_id
 * @property string $nopemusnahan
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $pegawai_mengetahui
 * @property string $pegawai_menyetujui
 * @property string $obatalkes_nama
 * @property string $tglkadaluarsa
 * @property double $jumlah
 * @property string $satuan_kecil
 * @property double $harganetto
 */
class InfoPemusnahanObatDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopemusnahanobatdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemusnahanobat_id'], 'default', 'value' => null],
            [['pemusnahanobat_id'], 'integer'],
            [['tglkadaluarsa'], 'safe'],
            [['jumlah', 'harganetto'], 'number'],
            [['satuan_kecil'], 'string'],
            [['nopemusnahan'], 'string', 'max' => 200],
            [['instalasi_nama', 'ruangan_nama', 'pegawai_mengetahui', 'pegawai_menyetujui'], 'string', 'max' => 50],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemusnahanobat_id' => 'Pemusnahanobat ID',
            'nopemusnahan' => 'Nopemusnahan',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawai_mengetahui' => 'Pegawai Mengetahui',
            'pegawai_menyetujui' => 'Pegawai Menyetujui',
            'obatalkes_nama' => 'Obatalkes Nama',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'jumlah' => 'Jumlah',
            'satuan_kecil' => 'Satuan Kecil',
            'harganetto' => 'Harganetto',
        ];
    }
}
