<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "diagnosa_m".
 *
 * @property int $diagnosa_id
 * @property int $klasifikasidiagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property string $diagnosa_katakunci
 * @property int $diagnosa_nourut
 * @property bool $diagnosa_imunisasi
 * @property string $diagnosa_cat_weight
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
class Diagnosa extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'diagnosa_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['klasifikasidiagnosa_id', 'diagnosa_nourut', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['klasifikasidiagnosa_id', 'diagnosa_nourut', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['diagnosa_kode', 'diagnosa_nama'], 'required'],
            [['diagnosa_imunisasi', 'is_deleted', 'is_active'], 'boolean'],
            [['diagnosa_cat_weight'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['diagnosa_kode'], 'string', 'max' => 10],
            [['diagnosa_nama', 'diagnosa_namalainnya'], 'string', 'max' => 200],
            [['diagnosa_katakunci'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'diagnosa_id' => Yii::t('app', 'Diagnosa'),
            'klasifikasidiagnosa_id' => Yii::t('app', 'Klasifikasi diagnosa'),
            'diagnosa_kode' => Yii::t('app', 'Kode diagnosa'),
            'diagnosa_nama' => Yii::t('app', 'Nama diagnosa'),
            'diagnosa_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'diagnosa_katakunci' => Yii::t('app', 'Keyword diagnosa'),
            'diagnosa_nourut' => Yii::t('app', 'No urut diagnosa'),
            'diagnosa_imunisasi' => Yii::t('app', 'Imunisasi diagnosa'),
            'diagnosa_cat_weight' => Yii::t('app', 'Diagnosa cat weight'),
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
