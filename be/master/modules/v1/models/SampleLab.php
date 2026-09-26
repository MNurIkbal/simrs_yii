<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "samplelab_m".
 *
 * @property int $samplelab_id
 * @property string $kode_sample
 * @property string $nama_sample
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
class SampleLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'samplelab_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_sample', 'nama_sample'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kode_sample'], 'string', 'max' => 50],
            [['nama_sample'], 'string', 'max' => 100],
            [['kode_sample'], 'chkKode'],
            [['nama_sample'], 'chkNama'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'samplelab_id' => 'Samplelab ID',
            'kode_sample' => 'Kode Sample',
            'nama_sample' => 'Nama Sample',
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

    public function chkKode()
    {
        $kode_sample = $this->kode_sample;
        $model = self::find()->where([
            'LOWER (kode_sample)' => strtolower($this->kode_sample),
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->samplelab_id != $this->samplelab_id)){
            $this->addError("kode_sample","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNama()
    {
        $model = self::find()->where([
            'LOWER (nama_sample)' => strtolower($this->nama_sample), 
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->samplelab_id != $this->samplelab_id)) {
            $this->addError("nama_sample", "Nama Sudah Dipakai");
            return false;
        }
        return true;
    }
}
