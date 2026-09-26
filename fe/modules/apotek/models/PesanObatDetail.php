<?php

namespace app\modules\apotek\models;

use Yii;

/**
 * This is the model class for table "pesanobatdetail_t".
 *
 * @property int $pesanobatdetail_id
 * @property int $sumberdana_id
 * @property int $pesanobatalkes_id
 * @property int $satuankecil_id
 * @property int $obatalkes_id
 * @property double $jumlah_pesan
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
class PesanObatDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pesanobatdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanobatdetail_id'], 'required'],
            [['pesanobatdetail_id', 'sumberdana_id', 'pesanobatalkes_id', 'satuankecil_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesanobatdetail_id', 'sumberdana_id', 'pesanobatalkes_id', 'satuankecil_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jumlah_pesan'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pesanobatdetail_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatdetail_id' => Yii::t('fe','Pesanobatdetail ID'),
            'sumberdana_id' => Yii::t('fe','Sumberdana ID'),
            'pesanobatalkes_id' => Yii::t('fe','Pesanobatalkes ID'),
            'satuankecil_id' => Yii::t('fe','Satuankecil ID'),
            'obatalkes_id' => Yii::t('fe','Obatalkes ID'),
            'jumlah_pesan' => Yii::t('fe','Jumlah Pesan'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }
}
