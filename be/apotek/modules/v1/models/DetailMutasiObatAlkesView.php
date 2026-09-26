<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "detailmutasiobatalkes_v".
 *
 * @property int $mutasiobatdetail_id
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
 * @property double $jumlah_mutasi
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 */
class DetailMutasiObatAlkesView extends  \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detailmutasiobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasiobatdetail_id', 'mutasiobatruangan_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'obatalkes_id'], 'default', 'value' => null],
            [['mutasiobatdetail_id', 'mutasiobatruangan_id', 'instalasi_tujuan_id', 'ruangan_tujuan_id', 'instalasi_asal_id', 'ruangan_asal_id', 'obatalkes_id'], 'integer'],
            [['nomutasioa', 'obatalkes_namalain'], 'string'],
            [['tglmutasioa'], 'safe'],
            [['jumlah_mutasi'], 'number'],
            [['instalasi_nama', 'ruangan_nama', 'instalasi_asal', 'ruangan_asal'], 'string', 'max' => 50],
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
        ];
    }
}


