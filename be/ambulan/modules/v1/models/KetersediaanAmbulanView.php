<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "v_ab_ketersediaanambulan".
 *
 * @property string $tgl_pesanambulan
 * @property int $ambulan_id
 * @property int $barang_id
 * @property string $no_polisi
 * @property bool $is_emergency
 * @property string $jenis_ambulan
 * @property string $status_ambulan
 */
class KetersediaanAmbulanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ketersediaanambulan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_pesanambulan'], 'safe'],
            [['ambulan_id', 'barang_id'], 'default', 'value' => null],
            [['ambulan_id', 'barang_id'], 'integer'],
            [['is_emergency'], 'boolean'],
            [['jenis_ambulan', 'status_ambulan'], 'string'],
            [['no_polisi'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_pesanambulan' => 'Tgl Pesanambulan',
            'ambulan_id' => 'Ambulan ID',
            'barang_id' => 'Barang ID',
            'no_polisi' => 'No Polisi',
            'is_emergency' => 'Is Emergency',
            'jenis_ambulan' => 'Jenis Ambulan',
            'status_ambulan' => 'Status Ambulan',
        ];
    }
}
