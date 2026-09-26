<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tarifambulan_v".
 *
 * @property int $ambulan_id
 * @property string $no_polisi
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property bool $is_default
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 */
class TarifAmbulanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tarifambulan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ambulan_id', 'daftartindakan_id', 'kelaspelayanan_id', 'penjamin_id', 'komponentarif_id'], 'default', 'value' => null],
            [['ambulan_id', 'daftartindakan_id', 'kelaspelayanan_id', 'penjamin_id', 'komponentarif_id'], 'integer'],
            [['is_default'], 'boolean'],
            [['harga_tariftindakan'], 'number'],
            [['no_polisi'], 'string', 'max' => 20],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['kelaspelayanan_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['komponentarif_nama'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambulan_id' => 'Ambulan ID',
            'no_polisi' => 'No Polisi',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'is_default' => 'Is Default',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Komponentarif Nama',
            'harga_tariftindakan' => 'Harga Tariftindakan',
        ];
    }
}
