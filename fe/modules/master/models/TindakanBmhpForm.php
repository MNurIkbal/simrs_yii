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
 * @property int $tipepaket_id
 * @property int $obatalkes_id
 * @property int $satuaninput_id
 * @property int $satuanunit_id
 * @property int $nilai_konversi
 * @property int $qty_input
 * @property int $qty_konversi
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

class TindakanBmhpForm extends \app\components\DocoBaseModel
{
    // public variable
    public $daftartindakan_id;
    public $tipepaket_id;
    public $obatalkes_id;
    public $satuanunit_id;
    public $satuaninput_id;
    public $nilai_konversi;
    public $qty_input;
    public $qty_konversi;
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
    public $group;

    public static function primaryKey()
	{
		return ['daftartindakan_id'];
    }

    /**
     * {@inheritdoc}
     */
    protected $xssProtected = [
        'qty_input',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'data'], 'required'],
            [['daftartindakan_id'], 'checkTindakan', 'on' => 'create'],
            [['daftartindakan_id'], 'safe', 'on' => 'update'],
			[['tipepaket_id', 'qty_input', 'qty_konversi', 'obatalkes_id', 'satuaninput_id', 'satuanunit_id', 'nilai_konversi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['daftartindakan_id', 'tipepaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['additional_data'], 'string'],
			[['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_id', 'group'], 'safe'],
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
			'tipepaket_id' => Yii::t('app', 'Tipe Paket ID'),
			'obatalkes_id' => Yii::t('app', 'Obat Alkes ID'),
			'satuaninput_id' => Yii::t('app', 'Satuan Input ID'),
			'satuanunit_id' => Yii::t('app', 'Satuan Unit ID'),
			'nilai_konversi' => Yii::t('app', 'Nilai Konversi'),
			'qty_input' => Yii::t('app', 'QTY'),
			'qty_konversi' => Yii::t('app', 'QTY Konversi'),
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

    public function checkTindakan(){
        $daftartindakan_id = $this->daftartindakan_id;

        $tindakan = (new Query)
                ->select(['daftartindakan_id', 'is_deleted'])
                ->from('tindakanbmhp_mp')
                ->where([
                    'daftartindakan_id' => $daftartindakan_id,
                    'is_deleted' => false
                ])
                ->all();
        if ($tindakan) {
            $this->addError('daftartindakan_id', 'Tindakan Sudah Digunakan');
        }

	}
}