<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomutasibarang_v".
 *
 * @property int $mutasibarang_id
 * @property string $nomutasi_barang
 * @property string $tgl_mutasibarang
 * @property int $instalasi_tujuan_id
 * @property string $instalasi_nama
 * @property int $ruangan_tujuan_id
 * @property string $ruangan_nama
 * @property int $instalasi_asal_id
 * @property string $instalasi_asal
 * @property int $ruangan_asal_id
 * @property string $ruangan_asal
 * @property int $status_mutasi
 * @property string $statusmutasi
 */
class InfoMutasiBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infomutasibarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'status_mutasi'], 'default', 'value' => null],
            [['mutasibarang_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'status_mutasi'], 'integer'],
            [['tgl_mutasibarang'], 'safe'],
            [['nomutasi_barang', 'instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal'], 'string', 'max' => 50],
            [['statusmutasi'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarang_id' => 'Mutasibarang ID',
            'nomutasi_barang' => 'Nomutasi Barang',
            'tgl_mutasibarang' => 'Tgl Mutasibarang',
            'instalasi_tujuan_id' => 'Instalasi Tujuan ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_tujuan_id' => 'Ruangan Tujuan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_asal_id' => 'Instalasi Asal ID',
            'instalasi_asal' => 'Instalasi Asal',
            'ruangan_asal_id' => 'Ruangan Asal ID',
            'ruangan_asal' => 'Ruangan Asal',
            'status_mutasi' => 'Status Mutasi',
            'statusmutasi' => 'Statusmutasi',
            'no_pemesanan' => 'No Pemesanan',
        ];
    }
}
