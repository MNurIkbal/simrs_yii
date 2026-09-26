<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-05 10:55:56
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-10 13:47:15
 * @Description: 
 */

namespace app\modules\rajal\models;
use app\components\DocoBaseModel;

use Yii;

class KonsulpoliForm extends DocoBaseModel
{
    protected $xssProtected = [
        'catatan_dokter_konsul',
        'jawaban_konsul'
    ];

    public $konsulpoli_id;
    public $ruangan_id;
    public $daftartindakan_id;
    public $pegawai_id;
    public $tindakanpelayanan_id;
    public $pendaftaran_id;
    public $pasien_id;
    public $tgl_konsulpoli;
    public $asalpoliklinikkonsul_id;
    public $status_periksa;
    public $catatan_dokter_konsul;
    public $no_antriankonsul;
    public $tglberlakukonsul_sd;
    public $tgl_masukperiksa;
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
    public $jadwaldokter_id;
    public $jawaban_konsul;
    public $jadwal_id;

    // constants
    const SCENARIO_UPDATE = 'update'; //scenario add session

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'jadwaldokter_id',
                    'pegawai_id',
                    'pendaftaran_id',
                    'pasien_id',
                    'tgl_konsulpoli',
                    'asalpoliklinikkonsul_id',
                    'jadwal_id'
                ],
                'required'
            ],
            [['ruangan_id', 'daftartindakan_id', 'tindakanpelayanan_id', 'pendaftaran_id', 'pasien_id', 'asalpoliklinikkonsul_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'daftartindakan_id', 'pegawai_id', 'tindakanpelayanan_id', 'pendaftaran_id', 'pasien_id', 'asalpoliklinikkonsul_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_konsulpoli', 'tglberlakukonsul_sd', 'created_date', 'last_modified_date', 'deleted_date', 'jadwaldokter_id', 'jawaban_konsul', 'pegawai_id','jadwal'], 'safe'],
            [['catatan_dokter_konsul', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['status_periksa'], 'string', 'max' => 50],
            [['no_antriankonsul'], 'string', 'max' => 6],

            // required in "update" scenario
            // [['ruangan_id', 'pegawai_id'], 'required', 'on' => self::SCENARIO_UPDATE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'konsulpoli_id' => 'Konsulpoli ID',
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'daftartindakan_id' => 'Daftartindakan ID',
            'pegawai_id' => Yii::t('fe', 'Dokter'),
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'tgl_konsulpoli' => 'Tanggal Konsul Poli',
            'asalpoliklinikkonsul_id' => 'Asalpoliklinikkonsul ID',
            'status_periksa' => 'Status Periksa',
            'catatan_dokter_konsul' => 'Catatan',
            'no_antriankonsul' => 'No Antriankonsul',
            'tglberlakukonsul_sd' => 'Tglberlakukonsul Sd',
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
            'jadwaldokter_id' => 'Ruangan',
            'jadwal_id' => 'Jadwal',
        ];
    }
}
