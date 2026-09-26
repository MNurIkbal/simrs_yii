<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokbarang_m".
 *
 * @property int $kelompokbarang_id
 * @property string $kelompokbarang_kode
 * @property string $kelompokbarang_nama
 * @property string $kelompokbarang_namalain
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
class KelompokBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'kelompokbarang_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokbarang_kode'], 'string', 'max' => 12],
            [['kelompokbarang_nama'], 'string', 'max' => 30],
            [['kelompokbarang_namalain'], 'string', 'max' => 30],
            [['kelompokbarang_kode'], 'chkKode'],
            [['kelompokbarang_nama'], 'chkNama'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $kelompokbarang_kode = $this->kelompokbarang_kode;
        $rest = substr($this->kelompokbarang_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("kelompokbarang_kode", "Kode Kelompok Barang mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (kelompokbarang_kode))' => strtolower($this->kelompokbarang_kode), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->kelompokbarang_id != $this->kelompokbarang_id ){
                $this->addError("kelompokbarang_kode","Kode Kelompok Barang Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    public function chkNama($params, $attributes)
    {
        $kelompokbarang_nama = $this->kelompokbarang_nama;
        $rest = substr($this->kelompokbarang_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("kelompokbarang_nama", "Nama Kelompok Barang mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (kelompokbarang_nama))' => strtolower($this->kelompokbarang_nama), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->kelompokbarang_id != $this->kelompokbarang_id ){
                $this->addError("kelompokbarang_nama","Nama Kelompok Barang Sudah Dipakai");
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
            'kelompokbarang_id' => 'Kelompokbarang ID',
            'kelompokbarang_kode' => 'Kelompokbarang Kode',
            'kelompokbarang_nama' => 'Kelompokbarang Nama',
            'kelompokbarang_namalain' => 'Kelompokbarang Namalain',
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
