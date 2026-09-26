<?php

namespace SirsCore\models;

use Yii;

/**
 * This is the model class for table "antrian_t".
 *
 * @property int $antrian_id
 * @property int $ruangan_id
 * @property int $carabayar_id
 * @property int $pendaftaran_id
 * @property int $layarantrian_id
 * @property int $loket_id
 * @property string $tgl_antrian
 * @property string $no_antrian
 * @property string $carabayar_loket
 * @property bool $panggil_flag
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
 * @property int $pasien_id
 * @property int $penjamin_id
 * @property int $pegawai_id
 * @property double $panggilan_ke
 * @property int $status_antrian 0=belum panggil,  1=panggil, 2=pilih, 3=lewati, 4=batal
 * @property int $status_pasien lookup_type='status_pasien'
 * @property int $groupcarabayar_id
 * @property int $jenisantrian_id
 * @property int $klasifikasipasien_id
 * @property int $jadwalbukapoli_id
 * @property string $temp_urutan
 *
 * @property CarabayarM $carabayar
 * @property PendaftaranT $pendaftaran
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 */
class Antrian extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antrian_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_antrian', 'no_antrian'], 'required'],
            [['konfigantrian_id', 'ruangan_id', 'carabayar_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id', 'penjamin_id', 'pegawai_id','jadwaldokter_id', 'status_antrian', 'status_pasien', 'groupcarabayar_id', 'jenisantrian_id','fungsiantrian_id', 'klasifikasipasien_id', 'jadwalbukapoli_id', 'jenisantriandetail_id'], 'default', 'value' => null],
            [['konfigantrian_id', 'ruangan_id', 'carabayar_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id', 'penjamin_id', 'pegawai_id','jadwaldokter_id', 'status_antrian', 'status_pasien', 'groupcarabayar_id', 'jenisantrian_id','fungsiantrian_id', 'klasifikasipasien_id', 'jadwalbukapoli_id'], 'integer'],
            [['instalasi_id','tgl_antrian', 'created_date', 'last_modified_date', 'deleted_date', 'temp_urutan'], 'safe'],
            [['panggil_flag', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['panggilan_ke'], 'number'],
            [['no_antrian'], 'string', 'max' => 6],
            [['carabayar_loket'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'antrian_id' => 'Antrian ID',
            'ruangan_id' => 'Ruangan ID',
            'carabayar_id' => 'Carabayar ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'layarantrian_id' => 'Layarantrian ID',
            'loket_id' => 'Loket ID',
            'tgl_antrian' => 'Tgl Antrian',
            'no_antrian' => 'No Antrian',
            'carabayar_loket' => 'Carabayar Loket',
            'panggil_flag' => 'Panggil Flag',
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
            'pasien_id' => 'Pasien ID',
            'penjamin_id' => 'Penjamin ID',
            'pegawai_id' => 'Pegawai ID',
            'jadwaldokter_id' => 'Jadwal Dokter ID',
            'panggilan_ke' => 'Panggilan Ke',
            'status_antrian' => 'Status Antrian',
            'status_pasien' => 'Status Pasien',
            'groupcarabayar_id' => 'Groupcarabayar ID',
            'jenisantrian_id' => 'Jenisantrian ID',
            'klasifikasipasien_id' => 'Klasifikasipasien ID',
            'fungsiantrian_id' => 'Fungsi Antrian ID',
            'instalasi_id' => 'Instalasi Antrian ID',
            'jadwalbukapoli_id' => 'Jadwal Buka Poli ID',
            'temp_urutan' => "Temp Urutan"
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarabayar()
    {
        return $this->hasOne(CarabayarM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['antrian_id' => 'antrian_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['antrianfarmasi_id' => 'antrian_id']);
    }
}
