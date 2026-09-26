<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatalkes_v".
 *
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $jenisobatalkes_id
 * @property string $jenisobatalkes_nama
 * @property int $ven_id
 * @property string $ven
 */
class ObatAlkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'obatalkes_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'jenisobatalkes_id', 'ven_id'], 'default', 'value' => null],
            [['obatalkes_id', 'jenisobatalkes_id', 'ven_id'], 'integer'],
            [['jenisobatalkes_nama'], 'string'],
            [['obatalkes_nama'], 'string', 'max' => 255],
            [['ven'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'ven_id' => 'Ven ID',
            'ven' => 'Ven',
        ];
    }
}
