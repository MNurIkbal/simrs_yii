<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "satuanlab_m".
 *
 * @property int $satuanlab_id
 * @property string $satuanlab_kode
 * @property string $satuanlab_nama
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
class SatuanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'satuanlab_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['satuanlab_kode','satuanlab_nama'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['satuanlab_kode', 'satuanlab_nama'], 'string', 'max' => 255],
            [['satuanlab_kode'], 'chkKode'],
            [['satuanlab_nama'], 'chkNama'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'satuanlab_id' => 'Satuanlab ID',
            'satuanlab_kode' => 'Satuanlab Kode',
            'satuanlab_nama' => 'Satuanlab Nama',
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
        $satuanlab_kode = $this->satuanlab_kode;
        $model = self::find()->where([
            'LOWER (satuanlab_kode)' => strtolower($this->satuanlab_kode),
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->satuanlab_id != $this->satuanlab_id)){
            $this->addError("satuanlab_kode","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }

    public function chkNama()
    {
        $model = self::find()->where([
            'LOWER (satuanlab_nama)' => strtolower($this->satuanlab_nama), 
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->satuanlab_id != $this->satuanlab_id)) {
            $this->addError("satuanlab_nama", "Nama Sudah Dipakai");
            return false;
        }
        return true;
    }
}
