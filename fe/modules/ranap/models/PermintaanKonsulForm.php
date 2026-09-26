<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property int $pasienbatalperiksa_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $tgl_batal
 * @property string $keterangan_batal
 * @property string $alasan_batal
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
 *
 */

class PermintaanKonsulForm extends \yii\base\Model
{
	public $pendaftaran_id;
	public $pasienadmisi_id;
	public $dokter_id;
	public $jenis_konsul;
	public $ket_konsul;
	public $status_konsul;
	public $waktu_permintaan;
	public $jawaban_konsul;
	public $additional_data;
	public $created_by;
	public $modified_count;
	public $last_modified_by;
	public $deleted_by;
	public $is_deleted;
    public $is_active;
    public $permintaankonsul_id;
	public $waktu_persetujuan;
	public $dokter_nama;
	public $jenis_konsul_nama;
    public $cppt_id;

    /**
     * {@inheritdoc}
     */
    /*public static function tableName()
    {
        return 'permintaankonsul_t';
    }*/

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id', 'pasienadmisi_id', 'dokter_id', 
                    'jenis_konsul', 'ket_konsul',
                    'dokter_nama', 'jenis_konsul_nama'
                ], 
                'required',
                'on' => 'form'
            ],
            [['status_konsul', 'permintaankonsul_id'], 'required','on' => 'persetujuan'],
            [['jawaban_konsul','permintaankonsul_id'], 'required','on' => 'jawaban'],
            [['pendaftaran_id', 'jawaban_konsul', 'pasienadmisi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'dokter_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['cppt_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['jawaban_konsul','ket_konsul'], 'string'],
            [['ket_konsul', 'jawaban_konsul', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['form'] = ['pendaftaran_id','pasienadmisi_id', 'dokter_id', 'jenis_konsul', 'ket_konsul'];
        $scenarios['persetujuan'] = ['status_konsul', 'permintaankonsul_id'];
        $scenarios['jawaban'] = ['jawaban_konsul', 'permintaankonsul_id'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaankonsul_id' => 'Permintaan Konsul ID',
            'dokter_id' => 'Dokter yang di konsul',
            'dokter_nama' => 'Dokter yang di konsul',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'waktu_persetujuan' => 'Waktu Persetujuan',
            'waktu_permintaan' => 'Waktu Permintaan',
            'jenis_konsul' => 'Jenis Konsul',
            'jenis_konsul_nama' => 'Jenis Konsul',
            'ket_konsul' => 'Permintaan Konsul',
            'status_konsul' => 'Status Konsul',
            'jawaban_konsul' => 'Jawaban Konsul',
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
            'cppt_id' => 'Cppt ID'
        ];
    }

}
