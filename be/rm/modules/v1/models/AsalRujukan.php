<?php
/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-10 15:00
 * @Last Modified by:   Naufal
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
            'asalrujukan_id' => 'Asalrujukan ID',
            'asalrujukan_nama' => 'Asalrujukan Nama',
            'asalrujukan_institusi' => 'Asalrujukan Institusi',
            'asalrujukan_namalainnya' => 'Asalrujukan Namalainnya',
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
