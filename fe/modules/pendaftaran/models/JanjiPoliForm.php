<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-17 13:22
*/

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * This is the model class for table "buatjanjipoli_t".
 *
 * @property integer $buatjanjipoli_id
 * @property integer $pendaftaran_id
 * @property integer $pegawai_id
 * @property integer $ruangan_id
 * @property integer $pasien_id
 * @property string $tgl_buatjanji
 * @property string $hari_jadwal
 * @property string $tgl_jadwal
 * @property boolean $by_phone
 * @property string $keterangan_buatjanji
 * @property string $no_antrianjanji
 * @property string $no_buatjanji
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
 *
 * @property PasienM $pasien
 * @property PegawaiM $pegawai
 * @property PendaftaranT $pendaftaran
 * @property RuanganM $ruangan
 */

class JanjiPoliForm extends \yii\base\Model
{
    
    public $buatjanjipoli_id;
    public $pendaftaran_id;
    public $pegawai_id;
    public $ruangan_id;
    public $pasien_id;
    public $tgl_buatjanji;
    public $hari_jadwal;
    public $tgl_jadwal;
    public $by_phone;
    public $keterangan_buatjanji;
    public $no_antrianjanji;
    public $no_buatjanji;
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
            [['pendaftaran_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[/*'ruangan_id',*/ 'pasien_id', 'tgl_buatjanji', 'hari_jadwal', 'tgl_jadwal', 'no_antrianjanji'], 'required'],
            [['tgl_buatjanji', 'tgl_jadwal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['by_phone', 'is_deleted', 'is_active'], 'boolean'],
            [['keterangan_buatjanji', 'additional_data'], 'string'],
            [['hari_jadwal'], 'string', 'max' => 20],
            [['no_antrianjanji'], 'string', 'max' => 6],
            [['no_buatjanji'], 'string', 'max' => 100],
           /* [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buatjanjipoli_id' => 'Buatjanjipoli ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pegawai_id' => 'Dokter',
            'ruangan_id' => 'Poliklinik',
            'pasien_id' => 'Nama Pasien',
            'tgl_buatjanji' => 'Tgl Buatjanji',
            'hari_jadwal' => 'Hari Jadwal',
            'tgl_jadwal' => 'Tgl Jadwal',
            'by_phone' => 'By Phone',
            'keterangan_buatjanji' => 'Keterangan Buatjanji',
            'no_antrianjanji' => 'No Antrianjanji',
            'no_buatjanji' => 'No Buatjanji',
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
