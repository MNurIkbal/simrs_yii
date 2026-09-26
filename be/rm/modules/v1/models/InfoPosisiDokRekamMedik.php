<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-16 13:35:10
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-16 13:38:36
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoposisidokrm_v".
 *
 * @property int $posisidokrm_id
 * @property int $dokrekammedis_id
 * @property int $instalasi_id
 * @property int $ruanganakhir_id
 * @property int $pasien_id
 * @property int $warnadokrm_id
 * @property int $subrak_id
 * @property int $lokasirak_id
 * @property string $no_dokrm
 * @property string $no_rekam_medik
 * @property string $tglrekammedis
 * @property string $nama_pasien
 * @property string $warnadokrm_namawarna
 * @property string $subrak_nama
 * @property string $lokasirak_nama
 * @property string $instalasi_nama
 * @property string $ruangan_akhir
 * @property bool $is_pesan
 */
class InfoPosisiDokRekamMedik extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoposisidokrm_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['posisidokrm_id', 'dokrekammedis_id', 'instalasi_id', 'ruanganakhir_id', 'pasien_id', 'warnadokrm_id', 'subrak_id', 'lokasirak_id'], 'default', 'value' => null],
            [['posisidokrm_id', 'dokrekammedis_id', 'instalasi_id', 'ruanganakhir_id', 'pasien_id', 'warnadokrm_id', 'subrak_id', 'lokasirak_id'], 'integer'],
            [['tglrekammedis'], 'safe'],
            [['is_pesan'], 'boolean'],
            [['no_dokrm', 'warnadokrm_namawarna'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'instalasi_nama', 'ruangan_akhir'], 'string', 'max' => 50],
            [['subrak_nama'], 'string', 'max' => 30],
            [['lokasirak_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'posisidokrm_id' => 'Posisidokrm ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'instalasi_id' => 'Instalasi ID',
            'ruanganakhir_id' => 'Ruanganakhir ID',
            'pasien_id' => 'Pasien ID',
            'warnadokrm_id' => 'Warnadokrm ID',
            'subrak_id' => 'Subrak ID',
            'lokasirak_id' => 'Lokasirak ID',
            'no_dokrm' => 'No Dokrm',
            'no_rekam_medik' => 'No Rekam Medik',
            'tglrekammedis' => 'Tglrekammedis',
            'nama_pasien' => 'Nama Pasien',
            'warnadokrm_namawarna' => 'Warnadokrm Namawarna',
            'subrak_nama' => 'Subrak Nama',
            'lokasirak_nama' => 'Lokasirak Nama',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_akhir' => 'Ruangan Akhir',
            'is_pesan' => 'Is Pesan',
        ];
    }

    public static function primaryKey()
    {
        return ['posisidokrm_id'];
    }
}
?>