<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoformsobarang_v".
 *
 * @property int $formsobarang_id
 * @property int $stokopnamebarang_id
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property string $tglformulir
 * @property string $noformulir
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property double $total_harganetto
 */
class InfoFormSoBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoformsobarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formsobarang_id', 'stokopnamebarang_id', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['formsobarang_id', 'stokopnamebarang_id', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['tglformulir'], 'safe'],
            [['noformulir'], 'string'],
            [['total_harganetto'], 'number'],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formsobarang_id' => 'Formsobarang ID',
            'stokopnamebarang_id' => 'Stokopnamebarang ID',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'tglformulir' => 'Tglformulir',
            'noformulir' => 'Noformulir',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'total_harganetto' => 'Total Harganetto',
        ];
    }
}
