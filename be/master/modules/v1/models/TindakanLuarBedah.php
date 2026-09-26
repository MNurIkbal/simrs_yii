<?php

/**
 * @Author: Wahyu Saepuloh
 * @Date:   5 November 2019
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tindakanluarbedah_mp".
 *
 * @property int $daftartindakan_id
 * @property int $tindakanluarbedah_id
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
 *
 */
class TindakanLuarBedah extends \Doco\components\DocoActiveRecord
{
	public static function primaryKey()
	{
		return ['daftartindakan_id'];
    }

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'tindakanluarbedah_mp';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
            [['daftartindakan_id'], 'required'],
			[['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['daftartindakan_id', 'tindakanluarbedah_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['additional_data'], 'string'],
			[['daftartindakan_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'daftartindakan_id' => Yii::t('app', 'Daftar Tindakan ID'),
			'tindakanluarbedah_id' => Yii::t('app', 'Tindakan Di Luar Bedah ID'),
			'additional_data' => Yii::t('app', 'Additional Data'),
			'created_date' => Yii::t('app', 'Created Date'),
			'created_by' => Yii::t('app', 'Created By'),
			'modified_count' => Yii::t('app', 'Modified Count'),
			'last_modified_date' => Yii::t('app', 'Last Modified Date'),
			'last_modified_by' => Yii::t('app', 'Last Modified By'),
			'is_deleted' => Yii::t('app', 'Is Deleted'),
			'is_active' => Yii::t('app', 'Is Active'),
			'deleted_date' => Yii::t('app', 'Deleted Date'),
			'deleted_by' => Yii::t('app', 'Deleted By'),
		];
	}

	
}
?>