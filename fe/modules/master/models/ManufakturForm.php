<?php

/**
 * @Author: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Date:   2021-01-22 11:07:10
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "manufaktur_m".
 *
 * @property int $manufaktur_id
 * @property string $kode
 * @property string $nama
 * @property string $alamat
 * @property string $kontak
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

class ManufakturForm extends \yii\base\Model
{
	// Public property
	public $manufaktur_id;
	public $kode;
	public $nama;
	public $alamat;
	public $kontak;
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
                ['kode', 'nama', 'alamat', 'kontak', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
                'default', 
                'value' => null
            ],
            [
                ['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 
                'integer'
            ],
            [
                ['kode', 'nama', 'alamat', 'kontak', 'additional_data'], 
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
            [['kode'], 'string', 'max' => 100],
            [['nama'], 'string', 'max' => 255],
            [
                ['kode', 'nama', 'alamat', 'kontak'], 
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
			'manufaktur_id' => Yii::t('fe', 'Manufaktur ID'),
			'kode' => Yii::t('fe', 'Kode Manufaktur'),
			'nama' => Yii::t('fe', 'Nama Manufaktur'),
			'alamat' => Yii::t('fe', 'Alamat'),
			'kontak' => Yii::t('fe', 'Kontak'),
			'additional_data' => 'Additional Data',
			'created_date' => 'Created Date',
			'created_by' => 'Created By',
			'modified_count' => 'Modified Count',
			'last_modified_date' => 'Last Modified Date',
			'last_modified_by' => 'Last Modified By',
			'is_deleted' => 'Is Deleted',
			'is_active' => 'Is Active',
			'deleted_date' => 'Deleted Date',
			'deleted_by' => 'Deleted By'
		];
	}
}
?>
