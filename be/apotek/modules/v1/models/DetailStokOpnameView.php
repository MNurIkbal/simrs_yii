<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "detailstokopname_v".
 *
 * @property int $stokopnamedetail_id
 * @property int $formstokopname_id
 * @property int $formulirstokopname_id
 * @property double $stok_sistem
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 */
class DetailStokOpnameView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detailstokopname_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['formstokopname_id', 'formulirstokopname_id', 'obatalkes_id'], 'default', 'value' => null],
            [['stokopnamedetail_id', 'formstokopname_id', 'formulirstokopname_id', 'obatalkes_id'], 'integer'],
            [['stok_sistem'], 'number'],
            [['obatalkes_namalain'], 'string'],
            [['tglkadaluarsa', 'stokobatalkes_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'formstokopname_id' => 'Formstokopname ID',
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'stok_sistem' => 'Stok Sistem',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_namalain' => 'Obatalkes Namalain'
        ];
    }
}
