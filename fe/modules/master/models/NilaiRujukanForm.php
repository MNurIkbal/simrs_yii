<?php

namespace app\modules\master\models;

use Yii;
use app\components\DocoBaseModel;

/**
 * This is the model class for table "nilairujukan_m".
 *
 * @property int $nilairujukan_id
 * @property int $pemeriksaanlab_id
 * @property string $nama_rujukan
 * @property int $jenis_kelamin lookup_type='jenis_kelamin'
 * @property int $golonganumur_id
 * @property string $nilai_min
 * @property string $nilai_max
 * @property string $nilai_rujukan
 * @property int $satuan_hasillab lookup_type='satuan_hasillab'
 * @property string $nilaikritis_min
 * @property string $nilaikritis_max
 * @property string $keterangan
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
// class NilaiRujukanForm extends \yii\base\Model
class NilaiRujukanForm extends DocoBaseModel
{
    /**
     * @inheritdoc
     */
    
    public $parent;
    public $nilairujukan_id;
    public $pemeriksaanlab_id;
    public $nama_rujukan;
    public $jenis_kelamin;
    public $golonganumur_id;
    public $nilai_min;
    public $nilai_max;
    public $nilai_rujukan;
    public $satuan_hasillab;
    public $nilaikritis_min;
    public $nilaikritis_max;
    public $keterangan;
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

    // public static function tableName()
    // {
    //     return 'nilairujukan_m';
    // }

    /**
     * @inheritdoc
     */

    // Validasi XSS form
    protected $xssProtected = [
        'nama_rujukan',
        'nilai_min',
        'nilai_max',
        'nilai_rujukan',
        'satuan_hasillab',
        'nilaikritis_min',
        'nilaikritis_max',
        'keterangan'
    ];

    public function rules()
    {
        return [
            [['nama_rujukan'], 'required'],
            [['nama_rujukan'], 'checkUnique'],
            [['pemeriksaanlab_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            // [['pemeriksaanlab_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'nilai_rujukan', 'nama_rujukan'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['nama_rujukan', 'nilai_min', 'nilai_max', 'nilai_rujukan', 'nilaikritis_min', 'nilaikritis_max', 'keterangan'], 'string', 'max' => 255],
        ];
    }

    public function checkUnique() {
        $nama_rujukan = $this->nama_rujukan;
        if (strpos(substr($nama_rujukan, 0, 1), ' ') !== FALSE) {
            $this->addError('nama_rujukan', 'Nama Hasil mengandung spasi di awal kata');
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
            'nilairujukan_id' => 'Nilairujukan ID',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'nama_rujukan' => 'Nama Rujukan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'golonganumur_id' => 'Golonganumur ID',
            'nilai_min' => 'Nilai Min',
            'nilai_max' => 'Nilai Max',
            'nilai_rujukan' => 'Nilai Rujukan',
            'satuan_hasillab' => 'Satuan Hasillab',
            'nilaikritis_min' => 'Nilaikritis Min',
            'nilaikritis_max' => 'Nilaikritis Max',
            'keterangan' => 'Keterangan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Aktif',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
