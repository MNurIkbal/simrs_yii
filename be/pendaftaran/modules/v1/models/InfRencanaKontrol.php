<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 11:13
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infojanjipoli_v".
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

class InfRencanaKontrol extends \Doco\components\DocoActiveRecord
{
    public $isToday;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infojanjipoli_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['buatjanjipoli_id', 'pegawai_id', 'ruangan_id', 'pasien_id'], 'default', 'value' => null],
            [['buatjanjipoli_id', 'pegawai_id', 'ruangan_id', 'pasien_id'], 'integer'],
            [['tgl_buatjanji', 'tgl_jadwal', 'tgl_pendaftaran', 'hari' , 'status_janji'], 'safe'],
            [['antrian_id'], 'string', 'max' => 32],
            [['no_antrian'], 'string', 'max' => 6],
            [['nama_pegawai', 'ruangan_nama', 'nama_pasien'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['hari'], 'string', 'max' => 200],
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        $tglJanji = date("y-m-d", strtotime($this->tgl_buatjanji));
        $tglDaftar = date("y-m-d", strtotime($this->tgl_pendaftaran));
        if($tglJanji > $tglDaftar){
            /** tgl  janji tidak sama dengan tgl pendaftaran */
            $this->status_janji = false;
        } else {
            $this->status_janji = true;
        }
        return $this->status_janji;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buatjanjipoli_id' => 'Buatjanjipoli ID',
            'tgl_buatjanji' => 'Tgl Buatjanji',
            'antrian_id' => 'Antrian ID',
            'no_antrian' => 'No Antrian',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'hari' => 'Hari',
            'tgl_jadwal' => 'Tgl Jadwal',
        ];
    }

    /**
     * This function to get record patient konsul / rencana kontrol
     *
     * @param Integer $buatjanjipoli_id
     * @return Array
     **/
    public function janjiDaftar($janjiId)
    {
        $sql = "
        SELECT
            buatjanjipoli_id,
            pasien_id,
            no_pendaftaran,
            carabayar_id,
            nama_pasien,
            pegawai_id,
            no_rekam_medik,
            penjamin_id,
            ruangan_id ,
            tgl_jadwal,
            tgl_pendaftaran,
            tanggal_lahir,
            tgl_buatjanji,
            status_janji
        FROM infojanjipoli_v
        WHERE buatjanjipoli_id = :buatjanjipoli_id";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':buatjanjipoli_id', $janjiId)
            ->queryOne();
    }
}
