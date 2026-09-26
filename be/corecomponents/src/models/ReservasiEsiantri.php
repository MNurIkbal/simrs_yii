<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\models;

use Yii;

class ReservasiEsiantri extends \Doco\components\DocoActiveRecord {
    public static $defaultSchema = 'public';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'reservasi_esiantri_t';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alamat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['id', 'kodepoli', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['id', 'nama_pasien'], 'required'],
            [['no_mr', 'nama_pasien', 'no_identitas', 'jenis_kelamin', 'alamat', 'no_hp', 'cara_bayar', 'no_rujukan', 'no_bpjs', 'kode_booking', 'nomor_antrian', 'jenis_pasien', 'loket', 'additional_data'], 'string'],
            [['tgl_lahir', 'tgl_kunjungan', 'additional_data', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_mr' => 'Nomor Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_identitas' => 'Nomor Identitas',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tgl_lahir' => 'Tanggal Lahir',
            'alamat' => 'Alamat',
            'no_hp' => 'Nomor Handphone',
            'tgl_kunjungan' => 'Tanggal Kunjungan',
            'cara_bayar' => 'Cara Bayar',
            'no_rujukan' => 'Nomor Rujukan',
            'no_bpjs' => 'Nomor BPJS',
            'kode_booking' => 'Kode Booking',
            'nomor_antrian' => 'Nomor Antrian',
            'jenis_pasien' => 'Jenis Pasien',
            'loket' => 'Loket',
            'kodepoli' => 'ID Poli Esiantri',
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