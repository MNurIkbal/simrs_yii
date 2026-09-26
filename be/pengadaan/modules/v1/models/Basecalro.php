<?php

/**
 * @author : Iqbal Ramadhani (iqbal.ramdhani@sirs.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "basecalro_r".
 *
 * @property int $basecalro_id 
 * @property string $tanggal
 * @property string $count
 * @property int $move_category
 * @property string $min
 * @property int $max
 * @property int $avg
 * @property int $min_resep
 */
class Basecalro extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'basecalro_r';
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
            'basecalro_id' => 'Basecalro ID',
            'tanggal' => 'Tanggal',
            'count' => 'Count',
            'move_category' => 'Move Category Kriteria',
            'min' => 'Nilai Min',
            'max' => 'Nilai Max'

            
        ];
    }
}
