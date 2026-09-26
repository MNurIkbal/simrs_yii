<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisobatalkes_m".
 *
 * @property integer $jenisobatalkes_id
 * @property string $jenisobatalkes_kode
 * @property string $jenisobatalkes_nama
 * @property string $jenisobatalkes_namalain
 * @property boolean $jenisobatalkes_farmasi
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
 * @property integer $group_jenisobat
 */
class JenisObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jenisobatalkes_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_kode', 'jenisobatalkes_nama', 'jenisobatalkes_namalain', 'group_jenisobat'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'group_jenisobat'], 'integer'],
            [['jenisobatalkes_kode', 'jenisobatalkes_nama', 'jenisobatalkes_namalain', 'additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_sync'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['jenisobatalkes_kode'], 'chkKode'],
            [['jenisobatalkes_nama'], 'chkNama'],
            [['jenisobatalkes_namalain'], 'chkNamaLainnya'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $jenisobatalkes_kode = $this->jenisobatalkes_kode;
        $rest = substr($this->jenisobatalkes_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("jenisobatalkes_kode", "Kode Jenis Obat Alkes mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (jenisobatalkes_kode))' => strtolower($this->jenisobatalkes_kode), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->jenisobatalkes_id != $this->jenisobatalkes_id ){
                $this->addError("jenisobatalkes_kode","Kode Jenis Obat Alkes Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    public function chkNama($params, $attributes)
    {
        $jenisobatalkes_nama = $this->jenisobatalkes_nama;
        $rest = substr($this->jenisobatalkes_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("jenisobatalkes_nama", "Nama Jenis Obat Alkes mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (jenisobatalkes_nama))' => strtolower($this->jenisobatalkes_nama), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->jenisobatalkes_id != $this->jenisobatalkes_id ){
                $this->addError("jenisobatalkes_nama","Nama Jenis Obat Alkes Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    public function chkNamaLainnya($params, $attributes)
    {
        $rest = substr($this->jenisobatalkes_namalain, 0, 1);
        if ($rest == " ") {
            $this->addError("jenisobatalkes_namalain", "Nama Lain Jenis Obat Alkes mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (jenisobatalkes_namalain))' => strtolower($this->jenisobatalkes_namalain), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->jenisobatalkes_id != $this->jenisobatalkes_id ){
                $this->addError("jenisobatalkes_namalain","Nama Lain Jenis Obat Alkes Sudah Dipakai");
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_kode' => 'Jenisobatalkes Kode',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'jenisobatalkes_namalain' => 'Jenisobatalkes Namalain',
            // 'jenisobatalkes_farmasi' => 'Jenisobatalkes Farmasi',
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
