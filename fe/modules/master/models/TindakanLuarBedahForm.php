<?php

/**
 * @Author: Wahyu Saepuloh
 * @Date:   5 November 2019
 */

namespace app\modules\master\models;

use Yii;
use app;
use yii\db\Query;

/**
 * This is the model class for table "tindakanbmhp_mp".
 *
 * @property int $daftartindakan_id
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

class TindakanLuarBedahForm extends \app\components\DocoBaseModel
{
    // public variable
    public $daftartindakan_id;
    public $daftartindakan_nama;
    public $tindakanluarbedah_id;
    public $qty;
    public $ditagihkan;
    public $created_by;
    public $modified_count;
    public $additional_data;
    public $created_date;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $data;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'data'], 'required'],
            // [['daftartindakan_id'], 'checkTindakan', 'on' => 'create'],
            [['daftartindakan_id'], 'safe', 'on' => 'create'],
            [['daftartindakan_id'], 'safe', 'on' => 'update'],
			[['qty', 'ditagihkan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['daftartindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['additional_data'], 'string'],
			[['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'daftartindakan_id' => Yii::t('app', 'Daftar Tindakan ID'),
            'tindakanluarbedah_id' => Yii::t('app', 'Tindakan Di Luar Bedah ID'),
            'qty' => Yii::t('app', 'Qty'),
            'ditagihkan' => Yii::t('app', 'Ditagihkan'),
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