<?php

namespace app\modules\v1\models;

use Yii;

class LogHistoryPlafonBpjs extends \Doco\components\DocoActiveRecord
{
   /**
    * @inheritdoc
    */

   public static function tableName()
   {
      return 'histori_plafon_bpjs_pasien_r';
   }

   /**
    * @inheritdoc
    */
   public function rules()
   {
      return [
         [['pendaftaran_id', 'pasienadmisi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
         [['pendaftaran_id', 'pasienadmisi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
         [['pendaftaran_id'], 'required'],
         [['is_deleted', 'is_active'], 'boolean'],
         [['additional_data'], 'string'],
         [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
      ];
   }

   /**
    * @inheritdoc
    */
   public function attributeLabels()
   {
      return [
         'pendaftaran_id' => 'Pendaftaran ID',
         'pasienadmisi_id' => 'Pasien Admisi ID',
         'additional_data' => 'Additional Data',
         'created_date' => 'Created Date',
         'created_by' => 'Created By',
         'modified_count' => 'Modified Count',
         'last_modified_date' => 'Last Modified Date',
         'last_modified_by' => 'Last Modified By',
         'is_deleted' => 'Is Deleted',
         'is_active' => 'Is Active',
         'deleted_date' => 'Deleted Date',
         'deleted_by' => 'Deleted By',
      ];
   }
}