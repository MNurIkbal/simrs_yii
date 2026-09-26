<?php

namespace app\modules\v1\models;

use Yii;

class LogBpjs extends \yii\db\ActiveRecord
{
   public static function tableName()
   {
      return 'logbpjs_r';
   }

   public function rules()
   {
      return [
         [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
         [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
         [['response'], 'required'],
         [['is_deleted', 'is_active'], 'boolean'],
         [['request', 'response', 'additional_data'], 'string'],
         [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
      ];
   }
}
