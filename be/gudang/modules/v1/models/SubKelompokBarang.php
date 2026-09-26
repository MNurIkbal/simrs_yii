<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "subkelompokbarang_m".
 *
 * @property int $subkelompokbarang_id
 * @property int $kelompokbarang_id
 * @property string $subkelompok_kode
 * @property string $subkelompok_nama
 * @property string $subkelompok_namalain
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
class SubKelompokBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'subkelompokbarang_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelompokbarang_id', 'subkelompok_kode', 'subkelompok_nama'], 'required'],
            [['kelompokbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelompokbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['subkelompok_kode'], 'string', 'max' => 12],
            [['subkelompok_nama'], 'string', 'max' => 30],
            [['subkelompok_namalain'], 'string', 'max' => 30],
            [['subkelompok_kode'], 'chkKode'],
            [['subkelompok_nama'], 'chkNama'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $subkelompok_kode = $this->subkelompok_kode;
        $rest = substr($this->subkelompok_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("subkelompok_kode", "Kode Sub Kelompok Barang mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (subkelompok_kode))' => strtolower($this->subkelompok_kode), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->subkelompokbarang_id != $this->subkelompokbarang_id ){
                $this->addError("subkelompok_kode","Kode Sub Kelompok Barang Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    public function chkNama($params, $attributes)
    {
        $subkelompok_nama = $this->subkelompok_nama;
        $rest = substr($this->subkelompok_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("subkelompok_nama", "Nama Sub Kelompok Barang mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (subkelompok_nama))' => strtolower($this->subkelompok_nama), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->subkelompokbarang_id != $this->subkelompokbarang_id ){
                $this->addError("subkelompok_nama","Nama Sub Kelompok Barang Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subkelompokbarang_id' => 'Subkelompokbarang ID',
            'kelompokbarang_id' => 'Kelompokbarang ID',
            'subkelompok_kode' => 'Subkelompok Kode',
            'subkelompok_nama' => 'Subkelompok Nama',
            'subkelompok_namalain' => 'Subkelompok Namalain',
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
