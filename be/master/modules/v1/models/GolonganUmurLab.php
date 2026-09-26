<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "golonganumurlab_m".
 *
 * @property int $golonganumurlab_id
 * @property string $gol_umurlab_nama
 * @property string $gol_umurlab_namalainnya
 * @property string $gol_umurlab_minimal
 * @property string $gol_murlab_maksimal
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
class GolonganUmurLab extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'golonganumurlab_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gol_umurlab_nama'], 'required'],
            [['gol_umurlab_minimal', 'gol_umurlab_maksimal'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['gol_umurlab_nama', 'gol_umurlab_namalainnya'], 'string', 'max' => 25],
            [['gol_umurlab_nama'], 'checkDuplicate'],
            [['gol_umurlab_minimal'], 'compare', 'compareAttribute' => 'gol_umurlab_maksimal', 'operator' => '<', 'message' => 'Golongan Umur Minimal harus lebih kecil dari Golongan Umur Maksimal.'],
            [['gol_umurlab_maksimal'], 'compare', 'compareAttribute' => 'gol_umurlab_minimal', 'operator' => '>', 'message' => 'Golongan Umur Maksimal harus lebih besar dari Golongan Umur Minimal.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'golonganumurlab_id' => 'Golonganumurlab ID',
            'gol_umurlab_nama' => 'Nama Golongan',
            'gol_umurlab_namalainnya' => 'Nama Namalainnya',
            'gol_umurlab_minimal' => 'Golongan Umur Minimal',
            'gol_umurlab_maksimal' => 'Golongan Umur Maksimal',
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
    public function checkDuplicate()
    {
        $gol_umurlab_nama = $this->gol_umurlab_nama;
        $model = self::find()->where([
            'LOWER (gol_umurlab_nama)' => strtolower($this->gol_umurlab_nama),
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->golonganumurlab_id != $this->golonganumurlab_id)){
            $this->addError("gol_umurlab_nama","Nama Golongan Sudah Digunakan");
            return false;
        }
    
        return true;
    }
}
