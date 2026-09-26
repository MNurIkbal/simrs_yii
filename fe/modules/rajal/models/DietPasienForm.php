<?php
/**
 * @Author: [Aris Munandar][aris.m@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\rajal\models;

use Yii;

/**
 *
 * @property int $pendaftaran_id
 * @property int $peg_pemesan_id
 * @property string $catatan_diet
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
class DietPasienForm extends \yii\base\Model
{
	/**
	 * {@inheritdoc}
	 */
	public $pendaftaran_id;
	public $peg_pemesan_id;
	public $catatan_diet;
	public $additional_data;
	public $created_date;
	public $created_by;
	public $modified_count;
	public $last_modified_by;
	public $last_modified_date;
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
			[['pendaftaran_id', 'catatan_diet'], 'required'],			
			[['pendaftaran_id', 'catatan_diet'], 'default', 'value' => null],
			[['pendaftaran_id'], 'integer'],
			[['created_date', 'last_modified_date', 'deleted_date','created_by'], 'safe'],
			[['catatan_diet', 'additional_data'], 'string'],
			[['is_deleted', 'is_active'], 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pendaftaran_id' => 'Pendaftaran ID',
			'catatan_diet' => 'Jenis Diet',
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
?>
