<?php

namespace Doco\models;

use Yii;

class PlafonBpjs extends \Doco\components\DocoActiveRecord
{
  /**
   * @inheritdoc
   */
  public static function tableName()
  {
    return 'plafonbpjs_m';
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['instalasi_id', 'kelaspelayanan_id', 'plafon'], 'required'],
      [['additional_data'], 'string'],
      [['created_date', 'last_modified_date', 'deleted_date', 'is_active', 'instalasi_id', 'kelaspelayanan_id', 'plafon', 'plafonbpjs_id'], 'safe'],
      [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'plafonbpjs_id'], 'integer'],
      [['is_deleted', 'is_active'], 'boolean'],
      [['instalasi_id', 'kelaspelayanan_id'], 'checkExist'],
    ];
  }

  public function checkExist($attributes, $params)
  {
    $instalasiId = $this->instalasi_id;
    $kelasPelayananId = $this->kelaspelayanan_id;
    $model = self::find()->where([
      'instalasi_id' => $instalasiId,
      'kelaspelayanan_id' => $kelasPelayananId,
      'is_deleted' => false,
      'is_active' => true
    ])->one();

    if (!empty($model) && $model->plafonbpjs_id != (int) $this->plafonbpjs_id) {
        Yii::error([$model->plafonbpjs_id,(int)$this->plafonbpjs_id]);
      $this->addError("instalasi_id", "Kombinasi Instalasi dan kelas Pelayanan yang dipilih sudah ada. Silahkan ubah inputan anda terlebih dahulu.");
      $this->addError("kelaspelayanan_id", "Kombinasi Instalasi dan kelas Pelayanan yang dipilih sudah ada. Silahkan ubah inputan anda terlebih dahulu.");
      return false;
    }

    return true;
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return [
      'instalasi_id' => 'Instalasi',
      'kelaspelayanan_id' => 'Kelas Pelayanan',
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
