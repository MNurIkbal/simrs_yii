<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomutasiobatalkes_v".
 *
 * @property int $mutasiobatruangan_id
 * @property string $nomutasioa
 * @property string $tglmutasioa
 * @property int $instalasi_tujuan_id
 * @property string $instalasi_nama
 * @property int $ruangan_tujuan_id
 * @property string $ruangan_nama
 * @property int $instalasi_asal_id
 * @property string $instalasi_asal
 * @property int $ruangan_asal_id
 * @property string $ruangan_asal
 */
class InfoMutasiObatalkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infomutasiobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasiobatruangan_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id'], 'default', 'value' => null],
            [['mutasiobatruangan_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id'], 'integer'],
            [['nomutasioa'], 'string'],
            [['tglmutasioa'], 'safe'],
            [['instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatruangan_id' => 'Mutasiobatruangan ID',
            'nomutasioa' => 'Nomutasioa',
            'tglmutasioa' => 'Tglmutasioa',
            'instalasi_tujuan_id' => 'Instalasi Tujuan ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_tujuan_id' => 'Ruangan Tujuan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_asal_id' => 'Instalasi Asal ID',
            'instalasi_asal' => 'Instalasi Asal',
            'ruangan_asal_id' => 'Ruangan Asal ID',
            'ruangan_asal' => 'Ruangan Asal',
        ];
    }
}
