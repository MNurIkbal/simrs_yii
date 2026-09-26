<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-05 10:56:50
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-10 16:17:07
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konsulpoli_t".
 *
 * @property int $konsulpoli_id
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property int $tindakanpelayanan_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property string $tgl_konsulpoli
 * @property string $asalpoliklinikkonsul_id
 * @property string $status_periksa
 * @property string $catatan_dokter_konsul
 * @property string $no_antriankonsul
 * @property string $tglberlakukonsul_sd
 * @property string $additional_data
 * @property string $tgl_masukperiksa
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
class Konsulpoli extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    
    protected $xssProtected = [
        'jawaban_konsul'
    ];

    public static function tableName()
    {
        return 'konsulpoli_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'pendaftaran_id', 'pasien_id', 'tgl_konsulpoli', 'asalpoliklinikkonsul_id', 'status_periksa'], 'required'],
            [['ruangan_id', 'pegawai_id', 'tindakanpelayanan_id', 'pendaftaran_id', 'pasien_id', 'asalpoliklinikkonsul_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'tindakanpelayanan_id', 'pendaftaran_id', 'pasien_id', 'asalpoliklinikkonsul_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['antrian_id', 'tgl_konsulpoli', 'tglberlakukonsul_sd', 'created_date', 'last_modified_date', 'deleted_date', 'tgl_setujui', 'tgl_masukperiksa','disetujui_oleh'], 'safe'],
            [['catatan_dokter_konsul', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['status_periksa'], 'string', 'max' => 50],
            [['no_antriankonsul'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'konsulpoli_id' => 'Konsulpoli ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'tgl_konsulpoli' => 'Tgl Konsulpoli',
            'asalpoliklinikkonsul_id' => 'Asalpoliklinikkonsul ID',
            'status_periksa' => 'Status Periksa',
            'catatan_dokter_konsul' => 'Catatan Dokter Konsul',
            'no_antriankonsul' => 'No Antriankonsul',
            'tglberlakukonsul_sd' => 'Tglberlakukonsul Sd',
            'tgl_masukperiksa' => 'Tgl Masuk Periksa',
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
