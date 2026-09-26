<?php

namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "infoterimamutasiobat_v".
 *
 * @property int $terimamutasiobat_id
 * @property string $tglterima
 * @property string $noterimamutasi
 * @property int $ruanganasal_id
 * @property string $ruangan_pengirim
 * @property int $instalasi_id
 * @property string $instalasi_pengirim
 */
class InfoTerimaMutasiObat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infoterimamutasiobat_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['terimamutasiobat_id', 'ruanganasal_id', 'instalasi_id'], 'default', 'value' => null],
            [['terimamutasiobat_id', 'ruanganasal_id', 'instalasi_id'], 'integer'],
            [['tglterima'], 'safe'],
            [['noterimamutasi'], 'string', 'max' => 20],
            [['ruangan_pengirim', 'instalasi_pengirim'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'terimamutasiobat_id' => 'Terimamutasiobat ID',
            'tglterima' => 'Tglterima',
            'noterimamutasi' => 'Noterimamutasi',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruangan_pengirim' => 'Ruangan Pengirim',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_pengirim' => 'Instalasi Pengirim',
        ];
    }
}
