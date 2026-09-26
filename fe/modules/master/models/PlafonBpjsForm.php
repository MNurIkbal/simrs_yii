<?php

namespace app\modules\master\models;

use Yii;

class PlafonBpjsForm extends \yii\base\Model
{
  const SCENARIO_CREATE = 'create';

  public $plafonbpjs_id;
  public $instalasi_id;
  public $kelaspelayanan_id;
  public $plafon;
  public $is_active;
  public $ruangan_id;
  public $instalasi;
  public $kelas;
  public $list_ruangan;
  public $list_ruangan_id;
  public $is_ruangan;
  public $plafon_ruangan;

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
          'is_active',
          'plafonbpjs_id',
          'ruangan_id',
          'instalasi',
          'kelas',
          'list_ruangan',
          'list_ruangan_id',
          'is_ruangan',
          'plafon_ruangan'
        ],
        'safe'
      ],
      [[
        'plafon',
      ], 'required'],
      [['instalasi_id','kelaspelayanan_id'], 'required', 'on' => self::SCENARIO_CREATE],
      [['plafon', 'plafon_ruangan'], 'number', 'min' => 0, 'tooSmall' => '{attribute} tidak boleh kurang dari 0.'],
      [['list_ruangan_id', 'plafon_ruangan'], 'required',
            'when' => function($model) {
                return $model->is_ruangan == 1;
            },
            'whenClient' => "function (attribute, value) {
                return $('#is_ruangan').is(':checked');
            }",
            'message' => '{attribute} tidak boleh kosong.',
      ],
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
      'is_active' => 'Status',
      'ruangan_id' => 'Ruangan',
      'list_ruangan_id' => 'Ruangan'
    ];
  }
}
