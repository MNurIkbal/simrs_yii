<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pekerjaan_m".
 *
 * @property integer $pekerjaan_id
 * @property string $pekerjaan_nama
 * @property string $pekerjaan_namalainnya
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
 * @property AnamnesadietT[] $anamnesadietTs
 * @property PasienM[] $pasienMs
 */
class Pekerjaan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pekerjaan_m';
    }

    /**
     * @inheritdoc
     */

    //  Validasi XSS di Florm
    protected $xssProtected = [
        'pekerjaan_nama',
        'pekerjaan_namalainnya',
        'additional_data'
    ];

    public function rules()
    {
        return [
            [['pekerjaan_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pekerjaan_nama', 'pekerjaan_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pekerjaan_id' => 'Pekerjaan ID',
            'pekerjaan_nama' => 'Pekerjaan Nama',
            'pekerjaan_namalainnya' => 'Pekerjaan Namalainnya',
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
    public function getAnamnesadietTs()
    {
        return $this->hasMany(AnamnesadietT::className(), ['pekerjaan_id' => 'pekerjaan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienMs()
    {
        return $this->hasMany(PasienM::className(), ['pekerjaan_id' => 'pekerjaan_id']);
    }
}
