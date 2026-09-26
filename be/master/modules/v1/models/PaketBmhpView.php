<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "paketbmhp_v".
 *
 * @property int $paketbmhp_id
 * @property int $obatalkes_id
 * @property string $obatalkes_kode
 * @property string $obatalkes_namalain
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property string $tindakanmedis_nama
 * @property int $satuankecil_id
 * @property string $satuanunit_nama
 * @property double $qty_pemakaian
 */
class PaketBmhpView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paketbmhp_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['paketbmhp_id', 'obatalkes_id', 'daftartindakan_id', 'satuankecil_id'], 'default', 'value' => null],
            [['paketbmhp_id', 'obatalkes_id', 'daftartindakan_id', 'satuankecil_id'], 'integer'],
            [['obatalkes_kode', 'obatalkes_namalain', 'satuanunit_nama'], 'string'],
            [['qty_pemakaian'], 'number'],
            [['daftartindakan_nama', 'tindakanmedis_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'paketbmhp_id' => 'Paketbmhp ID',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_kode' => 'Obatalkes Kode',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tindakanmedis_nama' => 'Tindakanmedis Nama',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanunit_nama' => 'Satuanunit Nama',
            'qty_pemakaian' => 'Qty Pemakaian',
        ];
    }
}
