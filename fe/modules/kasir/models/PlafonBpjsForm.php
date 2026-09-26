<?php

namespace app\modules\kasir\models;

use Yii;

class PlafonBpjsForm extends \yii\base\Model
{
  public $instalasi_id;
  public $kelaspelayanan_id;
  public $plafon;
  public $pendaftaran_id;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [
        [
          'instalasi_id',
          'kelaspelayanan_id',
          'plafon',
          'pendaftaran_id'
        ],
        'safe'
      ],
      [[
        'plafon'
      ], 'required'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'instalasi_id' => 'Instalasi',
      'kelaspelayanan_id' => 'Kelas Pelayanan',
    ];
  }
}
