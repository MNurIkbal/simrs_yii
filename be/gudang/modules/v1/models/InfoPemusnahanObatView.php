<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemusnahanobat_v".
 *
 * @property int $pemusnahanobat_id
 * @property string $tglpemusnahan
 * @property string $nopemusnahan
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $pegawai_mengetahui
 * @property string $pegawai_menyetujui
 * @property double $total_harganetto
 */
class InfoPemusnahanObatView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopemusnahanobat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemusnahanobat_id', 'ruangan_id'], 'default', 'value' => null],
            [['pemusnahanobat_id', 'ruangan_id'], 'integer'],
            [['tglpemusnahan'], 'safe'],
            [['total_harganetto'], 'number'],
            [['nopemusnahan'], 'string', 'max' => 200],
            [['instalasi_nama', 'ruangan_nama', 'pegawai_mengetahui', 'pegawai_menyetujui'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemusnahanobat_id' => 'Pemusnahanobat ID',
            'tglpemusnahan' => 'Tglpemusnahan',
            'nopemusnahan' => 'Nopemusnahan',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawai_mengetahui' => 'Pegawai Mengetahui',
            'pegawai_menyetujui' => 'Pegawai Menyetujui',
            'total_harganetto' => 'Total Harganetto',
        ];
    }
}
