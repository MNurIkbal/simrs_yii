<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoformsobarangdetail_v".
 *
 * @property int $formsobarangdetail_id
 * @property int $formsobarang_id
 * @property int $barang_id
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property string $noformulir
 * @property string $tglformulir
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $barang_nama
 * @property double $stok
 * @property string $nobatch
 */
class InfoFormSoBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoformsobarangdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formsobarangdetail_id', 'formsobarang_id', 'barang_id', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['formsobarangdetail_id', 'formsobarang_id', 'barang_id', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['noformulir'], 'string'],
            [['tglformulir','stokbarang_id'], 'safe'],
            [['stok'], 'number'],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
            [['barang_nama', 'nobatch'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formsobarangdetail_id' => 'Formsobarangdetail ID',
            'formsobarang_id' => 'Formsobarang ID',
            'barang_id' => 'Barang ID',
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'noformulir' => 'Noformulir',
            'tglformulir' => 'Tglformulir',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'barang_nama' => 'Barang Nama',
            'stok' => 'Stok',
            'nobatch' => 'Nobatch',
        ];
    }
}
