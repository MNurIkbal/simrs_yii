<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanformulirstokopname_v".
 *
 * @property int $formulirstokopname_id
 * @property string $tglformulir
 * @property string $noformulir
 * @property int $ruangan_id
 * @property double $harganetto_sistem
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 */
class LaporanFormulirStokOpnameView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanformulirstokopname_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['formulirstokopname_id', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['formulirstokopname_id', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['tglformulir'], 'safe'],
            [['noformulir'], 'string'],
            [['harganetto_sistem'], 'number'],
            [['ruangan_nama', 'instalasi_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'tglformulir' => 'Tglformulir',
            'noformulir' => 'Noformulir',
            'ruangan_id' => 'Ruangan ID',
            'harganetto_sistem' => 'Harganetto Sistem',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'tglperiodestok_awal' => 'Tanggal Periode Stok Awal',
            'tglperiodestok_akhir' => 'Tanggal Periode Stok Akhir',
        ];
    }
}
