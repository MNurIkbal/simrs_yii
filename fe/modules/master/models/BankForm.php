<?php

/**
 * @Author: Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * @Date:   2025-01-25 09:35:24
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "bank_m".
 *
 * @property int $bank_id
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property string $nama_bank
 * @property string $cabang
 * @property string $no_rekening
 * @property string $nama_pemilikrek
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
 */

class BankForm extends \yii\base\Model
{
	// Public property
	public $bank_id;
	public $propinsi_id;
  public $kabupaten_id;
	public $nama_bank;
	public $cabang;
	public $no_rekening;
  public $nama_pemilikrek;
	public $additional_data;
	public $created_date;
	public $created_by;
	public $modified_count;
	public $last_modified_date;
	public $last_modified_by;
	public $is_deleted;
	public $is_active;
	public $deleted_date;
	public $deleted_by;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
            [
                ['nama_bank', 'cabang', 'no_rekening', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
                'default',
                'value' => null
            ],
            [
                ['created_by', 'modified_count', 'last_modified_by', 'deleted_by'],
                'integer'
            ],
            [
                ['nama_bank', 'cabang', 'no_rekening', 'nama_pemilikrek', 'additional_data'],
                'string'
            ],
            [
                ['created_date', 'last_modified_date', 'deleted_date'],
                'safe'
            ],
            [
                ['is_deleted', 'is_active'],
                'boolean'
            ],
            [
                ['nama_bank', 'cabang', 'no_rekening', 'nama_pemilikrek', 'propinsi_id', 'kabupaten_id'],
                'required'
            ],
        ];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
            'propinsi_id' => 'Provinsi',
            'kabupaten_id' => 'Kabupaten',
            'nama_pemilikrek' => 'Nama Pemilik Rekening'
        ];
	}
}
