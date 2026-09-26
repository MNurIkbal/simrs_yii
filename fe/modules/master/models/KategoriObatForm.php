<?php

namespace app\modules\master\models;

use Yii;

class KategoriObatForm extends \yii\base\Model
{
  const SCENARIO_CREATE = 'create';
  const SCENARIO_UPDATE = 'update';

  public $instalasi_id;
  public $carabayar_id;
  public $penjamin_id;
  public $detail_obat;
  public $kategori;
  public $obatalkes_id;
  public $restriction_obat_id;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['kategori'], 'string'],
      [['kategori'], 'default', 'value' => null],
      [
        [
          'detail_obat',
        ],
        'safe'
      ],
      [[
        'kategori',
      ], 'required',
      'message'=>'{attribute} tidak boleh kosong'],
      [[
        'instalasi_id',
        'penjamin_id',
      ], 'required',
      'message'=>'Pilih setidaknya satu {attribute}'],
      // Validasi untuk memastikan instalasi_id dan penjamin_id adalah array
      [['instalasi_id', 'penjamin_id'], 'validateArray'],
      // Validasi untuk memastikan array tidak kosong setelah filtering
      [['instalasi_id', 'penjamin_id'], 'validateNotEmptyArray'],
      [['restriction_obat_id'], 'safe'],
      [['restriction_obat_id'], 'required', 'on' => self::SCENARIO_UPDATE, 'message' => 'ID kategori obat tidak boleh kosong'],
    ];
  }

  /**
   * Custom validator untuk memastikan field adalah array
   */
  public function validateArray($attribute, $params)
  {
    if (!is_array($this->$attribute)) {
      $this->addError($attribute, $attribute . ' harus berupa array');
    }
  }

  /**
   * Custom validator untuk memastikan array tidak kosong setelah filtering
   */
  public function validateNotEmptyArray($attribute, $params)
  {
    if (is_array($this->$attribute)) {
      // Filter array untuk menghapus nilai kosong
      $filteredArray = array_filter($this->$attribute, function ($value) {
        return !empty($value);
      });
      
      if (empty($filteredArray)) {
        $this->addError($attribute, 'Pilih setidaknya satu ' . $this->getAttributeLabel($attribute));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'instalasi_id' => 'Instalasi',
      'carabayar_id' => 'Cara Bayar',
      'penjamin_id' => 'Cara Bayar/Penjamin',
      'kategori' => 'Nama Kategori',
      'restriction_obat_id' => 'ID Restriction Obat'
    ];
  }

  /**
   * Helper method untuk memformat data instalasi yang diagregasi
   */
  public function formatInstalasiDisplay($instalasiData)
  {
    return $instalasiData;
  }

  /**
   * Helper method untuk memformat data penjamin yang diagregasi
   */
  public function formatPenjaminDisplay($penjaminData)
  {
    return $penjaminData;
  }

  /**
   * Helper method untuk memisahkan string yang diagregasi menjadi array
   */
  public function parseAggregatedString($aggregatedString)
  {
    if (is_string($aggregatedString) && strpos($aggregatedString, ',') !== false) {
      return array_map('trim', explode(',', $aggregatedString));
    }
    
    return [$aggregatedString];
  }
}
