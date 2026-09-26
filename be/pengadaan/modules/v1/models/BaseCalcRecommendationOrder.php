<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "basecalro_r".
 *
 * @property int $basecalcro_id
 * @property int $obatalkes_id
 * @property int $jenisobatalkes_id
 * @property string $tanggal
 * @property double $count
 * @property string $move_category
 * @property double $min
 * @property double $max
 * @property double $avg
 * @property double $min_resep
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property double $last_7
 * @property double $last_14
 * @property double $last_30
 */
class BaseCalcRecommendationOrder extends \Doco\components\DocoActiveRecord
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
        return [
            [['tanggal', 'count', 'move_category', 'min', 'max', 'avg', 'min_resep', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['obatalkes_id', 'jenisobatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['count', 'min', 'max', 'avg', 'min_resep', 'last_7', 'last_14', 'last_30'], 'double'],
            [['created_date', 'last_modified_date', 'deleted_date', 'additional_data'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}
