<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-22 16:27:36
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-22 16:29:30
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

class AsesmenAwalGiziNrsDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'asesmenawalgizinrsdetail_t';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['asesmenawalgizinrs_id','skriningnrs_id'], 'required'],
            [['skor','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }
}
