<?php


/**
 * @Author: Naufal
 * @Date:   2018-01-31 13:28:38
 * @Last Modified by:   Naufal
 * @Last Modified time: 
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;



/**
 * This is the model class for table "buatjanjipoli_t".
 *
 * @property int $buatjanjipoli_id
 * @property string $tgl_buatjanji
 * @property string $antrian_id
 * @property string $no_antrian
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $hari
 * @property string $tgl_jadwal
 */

class TraRencanaKontrol extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'buatjanjipoli_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['ruangan_id', 'pasien_id', 'tgl_buatjanji', 'hari_jadwal', 'tgl_jadwal'], 'required'],
            [['status_janjipoli','tgl_buatjanji', 'tgl_jadwal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['by_phone', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_buatjanji', 'additional_data'], 'string'],
            [['hari_jadwal'], 'string', 'max' => 20],
            /*[['no_antrianjanji'], 'string', 'max' => 6],*/
            [['no_buatjanji'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}
