<?php

namespace app\modules\master\models;

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
class OperasiForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    
    public $operasi_id;
    public $operasi_kode;
    public $operasi_nama;
    public $operasi_namalainnya;
    public $daftartindakan_id;
    public $kegiatanoperasi_id;
    public $golonganoperasi_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['operasi_kode', 'daftartindakan_id', 'kegiatanoperasi_id', 'golonganoperasi_id'], 'required'],
            [['additional_data'], 'string'],
            [['operasi_kode'], 'trimWhitespace'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['operasi_kode'], 'string', 'max' => 50],
        ];
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
            'daftartindakan_id' => 'Nama Daftar Tindakan',
            'kegiatanoperasi_id' => 'Kegiatan Operasi',
            'golonganoperasi_id' => 'Golongan Operasi',
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
    public function trimWhitespace(){
        $operasi_kode = $this->operasi_kode;
        if (strpos(substr($operasi_kode, 0, 1), ' ') !== FALSE) {
            $this->addError('operasi_kode', 'Kode operasi mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}
