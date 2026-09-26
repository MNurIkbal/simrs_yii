<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;

/**
 * This is the model class for table "cetakpemesanankamar_v".
 *
 * @property int $bookingkamar_id
 * @property string $Nomor Pemesanan
 * @property string $Tanggal Pemesanan
 * @property string $Tanggal Rawat Inap
 * @property string $Jenis Kasus Penyakit
 * @property string $Kelas Pelayanan
 * @property string $Ruangan
 * @property string $Kamar
 * @property string $Bed
 * @property string $Keterangan Pendaftaran
 * @property string $No. Rekam Medik
 * @property string $Nama Pasien
 * @property string $Tanggl Lahir
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property int $kamarruangan_id
 * @property string $kamarruangan_nokamar
 * @property int $kamartempattidur_id
 * @property string $Jenis Kelamin
 * @property string $Nomor Telepon
 * @property string $Pemesan
 * @property string $Petugas Pendaftaran
 */
class CetakPemesananKamar extends \Doco\components\DocoActiveRecord
{
    public $additional = [  
        'no_rekam_medik'=>'',
        'nama_pasien'=>'',
        'tanggal_lahir'=>'',
        'jeniskelamin'=>'',
        'no_telepon_pasien'=>'',
        'no_mobile_pasien'=>'',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cetakpemesanankamar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bookingkamar_id', 'kamarruangan_id', 'kamartempattidur_id'], 'default', 'value' => null],
            [['bookingkamar_id', 'kamarruangan_id', 'kamartempattidur_id'], 'integer'],
            [['Tanggal Pemesanan', 'Tanggal Rawat Inap', 'Tanggl Lahir'], 'safe'],
            [['Keterangan Pendaftaran'], 'string'],
            [['Nomor Pemesanan', 'no_mobile_pasien', 'Nomor Telepon'], 'string', 'max' => 20],
            [['Jenis Kasus Penyakit', 'Pemesan'], 'string', 'max' => 100],
            [['Kelas Pelayanan', 'Ruangan', 'Nama Pasien', 'Petugas Pendaftaran'], 'string', 'max' => 50],
            [['Kamar', 'kamarruangan_nokamar'], 'string', 'max' => 25],
            [['Bed'], 'string', 'max' => 255],
            [['No. Rekam Medik'], 'string', 'max' => 10],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['Jenis Kelamin'], 'string', 'max' => 200],
        ];
    }

    public function getLookupJenisKelamin()
    {
        $additional = json_decode($this->additional_data, true);
        $jk_id = $additional && isset($additional['jeniskelamin']) ? $additional['jeniskelamin'] : '';
        if ($jk_id) {
            return Lookup::findOne($jk_id)->lookup_name;
        }
        return $this->jenis_kelamin;
    }

}
