<?php

/**
 * @author : Iqbal Ramadhani (iqbal.ramdhani@sirs.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "movingcriteria_m".
 *
 * @property int $movingcriteria_id
 * @property string $min
 * @property string $max
 * @property int $criteria
 * @property string $factor
 * @property int $ss_min
 */
class MovingCriteria extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movingcriteria_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'movingcriteria_id' => 'Moving Criteria ID',
            'min' => 'Nilai Min',
            'min' => 'Nilai Min',
            'criteria' => 'Nilai Kriteria',
            'factor' => 'Factor Rekomendasi',
            'ss_min' => 'Nilai SS Min'

            
        ];
    }
}
