<?php

namespace app\modules\v1\models;

use Yii;


/**
 * This is the model class for table "rencanakontrol_t".
 *
 * @property int $rencanakontrol_id
 * @property int $pendaftaran_id
 * @property int $bpjs_id
 * @property string $no_spri
 * @property string $jenis_rencana
 * @property string $no_sep
 * @property string $no_kartu
 * @property string $nama
 * @property string $nosuratkontrol
 * @property string $nama_spesialis
 * @property string $dokterdpjp_kode
 * @property string $dokterdpjp_nama
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
 * @property string $tgl_rencanakontrol
 * @property string $jenis_pelayanan
 * @property string $kode_poli
 * 
 */

class RencanaKontrolT extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rencanakontrol_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['pendaftaran_id', 'bpjs_id', 'nama_spesialis'], 'required'],
            [['nama_spesialis'], 'required'],

            [['no_sep', 'no_spri', 'no_kartu', 'nosuratkontrol', 'pendaftaran_id', 'bpjs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],

            [['rencanakontrol_id', 'pendaftaran_id', 'bpjs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'konsulpoli_id'], 'integer'],

            [['rencanakontrol_id', 'pendaftaran_id', 'bpjs_id','no_spri', 'jenis_rencana', 'no_sep', 'no_kartu', 'nama', 'nosuratkontrol', 'nama_spesialis', 'dokterdpjp_kode', 'tgl_rencanakontrol', 'dokterdpjp_nama', 'jenis_pelayanan', 'last_modified_date', 'deleted_date', 'kode_poli', 'konsulpoli_id'], 'safe'],

            [['additional_data', 'no_spri', 'jenis_rencana', 'no_sep', 'no_kartu', 'nama', 'nosuratkontrol', 'nama_spesialis', 'dokterdpjp_kode', 'tgl_rencanakontrol', 'dokterdpjp_nama', 'jenis_pelayanan', 'kode_poli'], 'string'],

            [['is_deleted', 'is_active',], 'boolean'],
            // [['no_pindahkamar'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rencanakontrol_id' => 'Rencana Kontrol ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'bpjs_id' => 'BPJS ID',
            'no_spri' => 'No Spri',
            'no_kartu' => 'No Kartu',
            'no_sep' => 'No SEP',
            'nama' => 'Nama',
            'nosuratkontrol' => 'No Surat Kontrol',
            'nama_spesialis' => 'Nama Spesialis',
            'dokterdpjp_kode' => 'Dokter DPJP Kode',
            'dokterdpjp_nama' => 'Dokter DPJP Nama',
            'tgl_rencanakontrol' => 'Tgl Rencana Kontrol',
            'jenis_pelayanan' => 'Jenis Pelayanan',
            'kode_poli' => 'Kode Poli',
            'jenis_rencana' => 'Jenis Rencana',
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
    
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    public function getBpjs()
    {
        return $this->hasOne(Ruangan::className(), ['bpjs_id' => 'bpjs_id']);
    }
}