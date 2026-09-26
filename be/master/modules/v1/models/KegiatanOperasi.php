<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kegiatanoperasi_m".
 *
 * @property int $kegiatanoperasi_id
 * @property string $kegiatanoperasi_kode
 * @property string $kegiatanoperasi_nama
 * @property string $kegiatanoperasi_namalainnya
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
class KegiatanOperasi extends \Doco\components\DocoActiveRecord
{
    
    public static function tableName()
    {
        return 'kegiatanoperasi_m';
    }

    
    public function rules()
    {
        return [
            [['kegiatanoperasi_kode', 'kegiatanoperasi_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kegiatanoperasi_nama', 'kegiatanoperasi_kode'], 'string', 'max' => 50],
            [['kegiatanoperasi_kode'], 'chkKode'],
            ['kegiatanoperasi_nama', 'chkNama'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $kegiatanoperasi_kode = $this->kegiatanoperasi_kode;
        $rest = substr($this->kegiatanoperasi_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("kegiatanoperasi_kode", "Kode Kegiatan mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (kegiatanoperasi_kode)' => strtolower($this->kegiatanoperasi_kode), 'is_deleted' => false])->one();
            if(!empty($model) && $model->kegiatanoperasi_id != $this->kegiatanoperasi_id ){
                $this->addError("kegiatanoperasi_kode","Kode Kegiatan Sudah Dipakai");
                return false;
            }
        }
        
        return true;
    }

    public function chkNama()
    {
        $rest = substr($this->kegiatanoperasi_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("kegiatanoperasi_nama", "Kegiatan operasi mengandung spasi di awal kata");
            return false;
        }else{
            $model = self::find()->where(['LOWER (kegiatanoperasi_nama)' => strtolower($this->kegiatanoperasi_nama), 'is_deleted' => false])->one();
            if (!empty($model) && $model->kegiatanoperasi_id != $this->kegiatanoperasi_id) {
                $this->addError("kegiatanoperasi_nama", "Nama Kegiatan Sudah Dipakai");
                return false;
            }
        }
       
        return true;
    }

    public function attributeLabels()
    {
        return [
            'kegiatanoperasi_id' => 'Kegiatan Operasi ID',
            'kegiatanoperasi_nama' => 'Nama Kegiatan Operasi',
            'kegiatanoperasi_kode' => 'Kode Kegiatan Operasi',
            'kegiatanoperasi_namalainnya' => 'Nama Kegiatan Operasi Lainnya',
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
