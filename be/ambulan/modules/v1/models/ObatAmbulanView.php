<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatambulan_v".
 *
 * @property int $ambulan_id
 * @property string $no_polisi
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $qty
 */
class ObatAmbulanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'obatambulan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ambulan_id', 'obatalkes_id', 'qty'], 'default', 'value' => null],
            [['ambulan_id', 'obatalkes_id', 'qty'], 'integer'],
            [['no_polisi'], 'string', 'max' => 20],
            [['obatalkes_nama'], 'string', 'max' => 255],
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
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'qty' => 'Qty',
        ];
    }
}
