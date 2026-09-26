<?php

namespace app\modules\pengadaan\models;

use Yii;

class RecommendationOrderForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */

    public $days_of_inventory;
    public $jenisobatalkes_id;
    public $is_consignment;

    const SCENARIO_CGN = 'consignment';
    const SCENARIO_NON_CGN = 'nonconsignment';

    public function scenarios() {
        return [
            self::SCENARIO_CGN => ['jenisobatalkes_id'],
            self::SCENARIO_NON_CGN => ['jenisobatalkes_id', 'days_of_inventory']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_id', 'days_of_inventory'], 'required', 'on' => self::SCENARIO_NON_CGN],
            [['jenisobatalkes_id'], 'required', 'on' => self::SCENARIO_CGN],
            [['is_consignment'], 'safe'],
            [['is_consignment'], 'boolean'],
            [['days_of_inventory'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisobatalkes_id' => 'Jenis Obat Alkes',
            'days_of_inventory' => 'Days of Inventory',
            'is_consignment' => 'Consignment'
        ];
    }
}
