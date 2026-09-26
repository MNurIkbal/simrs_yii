<?php

/**
 * @Author: Aris
 * @Date:   2021-01-18 14:20:59
 */

namespace Doco\models;

use Yii;

/**
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $penjamin_id
 * @property int $dokter_jaga_id
 * @property int $dokter_dpjp_id
 * @property string $nama_pasien
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property string $no_rekam_medik
 * @property string $jenis_kelamin
 * @property string $penjamin_nama
 * @property string $dokter_jaga
 * @property string $dokter_dpjp
 * @property string $status_periksa
 * @property string $status_periksa_id
 * @property date $tgl_pendaftaran
 */
class InfoPasienRdView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienrd_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'tgl_pendaftaran', 'no_pendaftaran', 'pasien_id', 'nama_pasien', 'no_rekam_medik', 'jenis_kelamin', 'penjamin_id', 'dokter_jaga_id', 'dokter_jaga', 'dokter_dpjp_id', 'dokter_dpjp', 'status_periksa_id', 'status_periksa'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'penjamin_id', 'dokter_jaga_id', 'dokter_dpjp_id'], 'integer'],
            [['nama_pasien', 'diagnosa_namalainnya'], 'string', 'max' => 200],
            [['no_rekam_medik', 'jenis_kelamin', 'penjamin_nama', 'dokter_dpjp'], 'string', 'max' => 100],
            [['status_periksa_id', 'status_periksa'], 'string', 'max' => 255],
        ];
    }

    public static function primaryKey()
    {
        return ['pendaftaran_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'penjamin_id' => 'Penjamin Id',
            'dokter_jaga_id' => 'Dokter Jaga Id',
            'dokter_dpjp_id' => 'Dokter Dpjp Id',
            'nama_pasien' => 'Nama Pasien',
            'diagnosa_nama' => 'Nama Diagnosa',
            'diagnosa_namalainnya' => 'Nama Diagnosa Lainnya',
            'no_rekam_medik' => 'No Rekam Medik',
            'jenis_kelamin' => 'Jenis Kelamin',
            'penjamin_nama' => 'Nama Penjamin',
            'dokter_jaga' => 'Dokter Jaga',
            'dokter_dpjp' => 'Dokter Dpjp',
            'status_periksa' => 'Status Periksa',
            'status_periksa_id' => 'Status Periksa ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
        ];
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGantiDokterPj()
    {
        return $this->hasMany(GantiDokterPj::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
}
