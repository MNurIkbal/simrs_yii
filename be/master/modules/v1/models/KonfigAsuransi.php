<?php

namespace app\modules\v1\models;

use Yii;

class KonfigAsuransi extends \Doco\components\DocoActiveRecord
{
  /**
   * @inheritdoc
   */
  public static function tableName()
  {
    return 'konfigasuransi_k';
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['provider_id', 'base_url', 'auth', 'url_cek_eligibilitas', 'url_referensi_benefit', 'url_pengesahan', 'url_pendaftaran', 'provider_code'], 'required'],
      [['base_url', 'auth', 'url_cek_eligibilitas', 'url_autentikasi', 'url_get_referensi', 'url_referensi_benefit', 'url_pengesahan', 'url_pendaftaran', 'url_jaminan', 'url_kunjungan', 'provider_code', 'additional_data'], 'string'],
      [['created_date', 'last_modified_date', 'deleted_date', 'session_expired', 'url_cek_eligibilitas', 'is_active', 'provider_id'], 'safe'],
      [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
      [['is_deleted', 'is_active'], 'boolean'],
      [['provider_code'], 'checkExist'],
    ];
  }

  public function checkExist($attributes, $params)
  {
    $providerCode = $this->provider_code;
    $model = KonfigAsuransi::find()->where([
      'provider_code' => $providerCode,
      'is_deleted' => false,
      'is_active' => true
    ])->one();

    if (!empty($model) && $model->konfigasuransi_id != $this->konfigasuransi_id) {
      $this->addError("provider_id", "Kode Provider Sudah Dipakai");
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
      'provider_id' => 'ID',
      'provider_code' => 'Provider Code',
      'base_url' => 'Base Url',
      'url_get_referensi' => 'Get Referensi',
      'url_referensi_benefit' => 'Referensi Benefit',
      'auth' => 'Auth',
      'url_pengesahan' => 'Discharge / Pengesahan',
      'url_pendaftaran' => 'Cetak Pendaftaran',
      'url_kunjungan' => 'Cetak Kunjungan',
      'url_jaminan' => 'Cetak Jaminan',
      'url_cek_eligibilitas' => 'Cek Eligibilitas',
      'session_expired' => 'Session Expired (dalam bentuk detik)',
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
