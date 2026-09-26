<?php

namespace app\modules\master\models;

use Yii;

class KonfigAsuransiForm extends \yii\base\Model
{
  const SCENARIO_CEK_KONEKSI = 'cek-koneksi';
  const SCENARIO_DEFAULT = 'default';

  public $konfigasuransi_id;
  public $provider_id;
  public $provider_code;
  public $base_url;
  public $url_referensi_benefit;
  public $auth;
  public $url_cek_eligibilitas;
  public $url_pengesahan;
  public $url_pendaftaran;
  public $url_kunjungan;
  public $url_jaminan;
  public $session_expired;
  public $is_active;

  public function scenarios()
  {
    $scenarios = parent::scenarios();
    $scenarios[self::SCENARIO_CEK_KONEKSI] = [
      'provider_id',
      'provider_code',
      'base_url',
      'auth',
    ];

    $scenarios[self::SCENARIO_DEFAULT] = [
      'konfigasuransi_id',
      'provider_id',
      'provider_code',
      'base_url',
      'auth',
      'url_referensi_benefit',
      'url_cek_eligibilitas',
      'url_pengesahan',
      'url_pendaftaran',
      'url_kunjungan',
      'url_jaminan',
      'session_expired',
      'is_active',
    ];

    return $scenarios;
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [
        [
          'provider_id',
          'provider_code',
          'base_url',
          'url_referensi_benefit',
          'auth',
          'url_cek_eligibilitas',
          'url_pengesahan',
          'url_pendaftaran',
          'url_kunjungan',
          'url_jaminan',
          'session_expired',
          'is_active',
          'konfigasuransi_id'
        ],
        'safe'
      ],
      [[
        'provider_id',
        'base_url',
        'auth'
      ], 'required'],
      [
        [
          'url_referensi_benefit',
          'url_cek_eligibilitas',
          'url_pengesahan',
          'url_pendaftaran',
          'session_expired',
          'is_active',
        ],
        'required',
        'on' => self::SCENARIO_DEFAULT
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'provider_id' => 'Nama Provider',
      'base_url' => 'Base Url',
      'url_get_referensi' => 'Get Referensi',
      'url_referensi_benefit' => 'Referensi Benefit',
      'auth' => 'Auth',
      'url_pengesahan' => 'Discharge / Pengesahan',
      'url_pendaftaran' => 'Cetak Struk Pendaftaran',
      'url_kunjungan' => 'Cetak Kunjungan',
      'url_jaminan' => 'Cetak Jaminan',
      'url_cek_eligibilitas' => 'Cek Eligibilitas',
      'session_expired' => 'Session Expired (dalam bentuk detik)',
      'is_active' => 'Status',
    ];
  }
}
