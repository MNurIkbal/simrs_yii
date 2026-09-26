<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "suratketlahir_t".
 *
 * @property int $suratketlahir_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $dokterdpjp_id
 * @property string $ibu_nama
 * @property string $ibu_ktp
 * @property string $ibu_alamat
 * @property string $ibu_pekerjaan
 * @property string $ibu_golongandarah
 * @property string $ayah_nama
 * @property string $ayah_ktp
 * @property string $ayah_alamat
 * @property int $ayah_pekerjaan_id
 * @property int $ayah_golongandarah_id
 * @property int $hari_lahir
 * @property string $tgl_lahir
 * @property string $jam_lahir
 * @property double $bb_lahir
 * @property double $panjang_lahir
 * @property string $kelahiran
 * @property int $golongan_darah
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
 * @property int $anakke
 */
class SuratKetLahir extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suratketlahir_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ayah_nama',
                'ayah_ktp',
                'ayah_alamat',
                'ayah_pekerjaan_id',
                        //'hari_lahir',
                'tgl_lahir',
                'jam_lahir',
                'bb_lahir',
                'panjang_lahir',
                'kelahiran',
                'anakke'], 'required'],

            [['pendaftaran_id', 
                'pasienadmisi_id', 
                'pasien_id', 
                'dokterdpjp_id', 
                'ayah_pekerjaan_id', 
                'ayah_golongandarah_id', 
                    //'hari_lahir', 
                'golongan_darah',
                'created_by',
                'modified_count',
                'last_modified_by',
                'deleted_by', 
                'anakke'], 'default', 'value' => null],

            [['pendaftaran_id', 
                'pasienadmisi_id',
                'pasien_id',
                'ayah_pekerjaan_id',
                'ayah_golongandarah_id',
                //'hari_lahir',
                'golongan_darah',
                'created_by',
                'modified_count',
                'last_modified_by',
                'deleted_by',
                'anakke'], 'integer'],

            [['ibu_alamat', 
                'ayah_alamat',
                'additional_data'], 'string'],

            [['tgl_lahir', 
                'jam_lahir', 
                'created_date',
                'last_modified_date',
                'deleted_date'], 'safe'],

            [['bb_lahir',
                'panjang_lahir'], 'number'],

            [['is_deleted',
                'is_active'], 'boolean'],

            [['ibu_nama',
                'ibu_ktp',
                'ibu_pekerjaan',
                'ayah_nama',
                'ayah_ktp'], 'string', 'max' => 100],

            [['ibu_golongandarah'], 'string', 'max' => 10],

            [['kelahiran'], 'string', 'max' => 255],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'suratketlahir_id'      => 'Suratketlahir ID',
            'pendaftaran_id'        => 'Pendaftaran ID',
            'pasienadmisi_id'       => 'Pasienadmisi ID',
            'pasien_id'             => 'Pasien ID',
            'dokterdpjp_id'         => 'Dokterdpjp ID',
            'ibu_nama'              => 'Ibu Nama',
            'ibu_ktp'               => 'Ibu Ktp',
            'ibu_alamat'            => 'Ibu Alamat',
            'ibu_pekerjaan'         => 'Ibu Pekerjaan',
            'ibu_golongandarah'     => 'Ibu Golongandarah',
            'ayah_nama'             => 'Ayah Nama',
            'ayah_ktp'              => 'Ayah Ktp',
            'ayah_alamat'           => 'Ayah Alamat',
            'ayah_pekerjaan_id'     => 'Ayah Pekerjaan ID',
            'ayah_golongandarah_id' => 'Ayah Golongandarah ID',
            //'hari_lahir'            => 'Hari Lahir',
            'tgl_lahir'             => 'Tgl Lahir',
            'jam_lahir'             => 'Jam Lahir',
            'bb_lahir'              => 'Bb Lahir',
            'panjang_lahir'         => 'Panjang Lahir',
            'kelahiran'             => 'Kelahiran',
            'golongan_darah'        => 'Golongan Darah',
            'additional_data'       => 'Additional Data',
            'created_date'          => 'Created Date',
            'created_by'            => 'Created By',
            'modified_count'        => 'Modified Count',
            'last_modified_date'    => 'Last Modified Date',
            'last_modified_by'      => 'Last Modified By',
            'is_deleted'            => 'Is Deleted',
            'is_active'             => 'Is Active',
            'deleted_date'          => 'Deleted Date',
            'deleted_by'            => 'Deleted By',
            'anakke'                => 'Anakke',
        ];
    }
}
