<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "golonganpegawai_m".
 *
 * @property integer $golonganpegawai_id
 * @property string $golonganpegawai_nama
 * @property string $golonganpegawai_namalainnya
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
 */
class GolonganOperasi extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\GolonganOperasiRepositories';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'golonganoperasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['golonganoperasi_kode', 'golonganoperasi_nama'], 'required'],
            [['golonganoperasi_kode'],'checkDuplicate'],
            [['golonganoperasi_nama'],'checkDuplicateName'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['golonganoperasi_nama', 'golonganoperasi_kode'], 'string', 'max' => 50],
        ];
    }

    public function checkDuplicate()
    {
        $golonganoperasi_kode = $this->golonganoperasi_kode;
        $model = self::find()->where([
            'LOWER (golonganoperasi_kode)' => strtolower($this->golonganoperasi_kode),
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->golonganoperasi_id != $this->golonganoperasi_id)){
            $this->addError("golonganoperasi_kode","Kode golongan sudah di gunakan");
            return false;
        }
    
        return true;
    }

    public function checkDuplicateName()
    {
        $golonganoperasi_nama = $this->golonganoperasi_nama;
        $model = self::find()->where([
            'LOWER (golonganoperasi_nama)' => strtolower($this->golonganoperasi_nama),
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->golonganoperasi_id != $this->golonganoperasi_id)){
            $this->addError("golonganoperasi_nama","Nama Golongan sudah digunakan");
            return false;
        }
    
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'golonganoperasi_id' => 'Golongan Operasi ID',
            'golonganoperasi_nama' => 'Nama Golongan Operasi',
            'golonganoperasi_kode' => 'Kode Golongan Operasi',
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
