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
class Operasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['operasi_kode', 'daftartindakan_id', 'kegiatanoperasi_id', 'golonganoperasi_id'], 'required'],
            [['additional_data'], 'string'],
            [['operasi_nama', 'operasi_kode', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['operasi_nama', 'operasi_kode'], 'string', 'max' => 50],
            [['operasi_kode'], 'chkKode'],
            [['daftartindakan_id'], 'chkTindakan'],
            [['operasi_kode'], 'trim'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $operasi_kode = $this->operasi_kode;
        $model = self::find()->where(['LOWER (operasi_kode)' => strtolower($this->operasi_kode), 'is_deleted' => false])->one();
        if(!empty($model) && $model->operasi_id != $this->operasi_id ){
            $this->addError("operasi_kode","Kode Sudah Dipakai");
            return false;
        }
    
        return true;
    }
    public function chkTindakan($params, $attributes)
    {
        $daftartindakan_id = $this->daftartindakan_id;
        $model = self::find()->where(['daftartindakan_id' => $this->daftartindakan_id,'kegiatanoperasi_id'=>$this->kegiatanoperasi_id, 'golonganoperasi_id'=>$this->golonganoperasi_id, 'is_deleted' => false])->one();
        if(!empty($model) && $model->operasi_id != $this->operasi_id ){
            $this->addError("daftartindakan_id","Daftar tindakan sudah pernah ditambahkan");
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
            'operasi_id' => 'Operasi ID',
            'operasi_nama' => 'Nama Operasi',
            'operasi_kode' => 'Kode Operasi',
            'daftartindakan_id' => 'Daftar Tindakan ID',
            'kegiatanoperasi_id' => 'Kegiatan Operasi ID',
            'golonganoperasi_id' => 'Golongan Operasi ID',
            'operasi_namalainnya' => 'Nama Operasi Lainnya',
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
