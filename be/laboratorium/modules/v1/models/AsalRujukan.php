<?php
/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-10 15:00
 * @Last Modified by:   Sigit
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "asalrujukan_m".
 *
 * @property integer $asalrujukan_id
 * @property string $asalrujukan_nama
 * @property string $asalrujukan_institusi
 * @property string $asalrujukan_namalainnya
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 *
 * @property PerujukM[] $perujukMs
 * @property RujukanT[] $rujukanTs
 * @property RujukandariM[] $rujukandariMs
 * @property RujukankeluarM[] $rujukankeluarMs
 */
class AsalRujukan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'asalrujukan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['asalrujukan_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['asalrujukan_nama', 'asalrujukan_institusi', 'asalrujukan_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'asalrujukan_id' => Yii::t('app', 'Asal Rujukan ID'),
            'asalrujukan_nama' => Yii::t('app', 'Nama Asal Rujukan'),
            'asalrujukan_institusi' => Yii::t('app', 'Institusi Asal Rujukan'),
            'asalrujukan_namalainnya' => Yii::t('app', 'Nama Lain Asal Rujukan'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Status Aktif'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerujuk()
    {
        return $this->hasMany(Perujuk::className(), ['asalrujukan_id' => 'asalrujukan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRujukan()
    {
        return $this->hasMany(Rujukan::className(), ['asalrujukan_id' => 'asalrujukan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRujukandari()
    {
        return $this->hasMany(Rujukandari::className(), ['asalrujukan_id' => 'asalrujukan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRujukankeluar()
    {
        return $this->hasMany(Rujukankeluar::className(), ['asalrujukan_id' => 'asalrujukan_id']);
    }
}
