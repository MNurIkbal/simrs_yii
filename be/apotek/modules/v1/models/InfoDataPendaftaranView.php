<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomutasiobatalkes_v".
 *
 * @property int $pendaftaran_id
 * @property int $ins_id
 * @property int $rua_id
 * @property int $pasien_id
 * @property int $pen_id
 * @property int $car_id
 * @property int $kelaspelayanan_id
 * @property int $pasienpulang_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $no_mobile_pasien
 * @property string $ins_nama
 * @property string $rua_nama
 * @property string $car
 * @property string $pen
 * @property string $kelaspelayanan_nama
 * @property double $jumlah_uangmuka
 * @property int $pasienpulangri_id
 * @property int $pasienadmisi_id
 * @property string $status_pasien
 * @property int $pasienmasukpenunjang_id
 * @property string $tglpasienpulang
 * @property string $nama_dok_rj_rd
 * @property string $nama_dok_ri
 * @property string $jeniskasuspenyakit_nama
 * @property string $umur
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property int $status_bayar
 * @property int $jeniskasuspenyakit_id
 * @property string $tanggal_lahir
 * @property int $penjualanresep_id
 * @property double $jasa
 * @property double $administrasi
 * @property double $obat
 * @property double $totalharga_jual
 * @property int $hak_kelas
 * @property string $no_kartu
 * @property int $group_carabayar
 * @property double $total_piutang
 */
class InfoDataPendaftaranView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infodatapendaftaran_v';
    }

    public static function primaryKey()
    {
        return ["pendaftaran_id"];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id',
                'ins_id',
                'rua_id',
                'pasien_id',
                'pen_id',
                'car_id',
                'kelaspelayanan_id',
                'pasienpulang_id',
                'pasienpulangri_id',
                'pasienadmisi_id',
                'pasienmasukpenunjang_id',
                'carabayar_id',
                'penjamin_id',
                'instalasi_id',
                'ruangan_id',
                'status_bayar',
                'jeniskasuspenyakit_id',
                'penjualanresep_id',
                'hak_kelas',
                'group_carabayar'], 'integer', 'max' => 10],
            [['tgl_pendaftaran',
                'tglpasienpulang',
                'tanggal_lahir',
                'no_pendaftaran',
                'no_rekam_medik',
                'nama_pasien',
                'no_mobile_pasien',
                'ins_nama',
                'rua_nama',
                'car',
                'pen',
                'kelaspelayanan_nama',
                'status_pasien',
                'nama_dok_rj_rd',
                'nama_dok_ri',
                'jeniskasuspenyakit_nama',
                'umur',
                'carabayar_nama',
                'penjamin_nama',
                'instalasi_nama',
                'ruangan_nama',
                'no_kartu'], 'string'],
            [['jumlah_uangmuka',
                'jasa',
                'administrasi',
                'obat',
                'totalharga_jual',
                'total_piutang'], 'number'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['no_mobile_pasien'], 'string', 'max' => 20],
            [['ins_nama',
                'rua_nama',
                'car',
                'pen',
                'nama_dok_rj_rd',
                'nama_dok_ri',
                'carabayar_nama',
                'penjamin_nama',
                'instalasi_nama',
                'ruangan_nama'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatruangan_id' => 'Mutasiobatruangan ID',

            'pendaftaran_id' => 'ID Pendaftaran',
            'ins_id' => 'ID Instalasi',
            'rua_id' => 'ID Ruangan',
            'pasien_id' => 'ID Pasien',
            'pen_id' => 'ID Penjamin',
            'car_id' => 'ID Cara Bayar',
            'kelaspelayanan_id' => 'ID Kelas Pelayanan',
            'pasienpulang_id' => 'ID Pasien Pulang',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tanggal Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'ins_nama' => 'Nama Instalasi',
            'rua_nama' => 'Nama Ruangan',
            'car' => 'Cara Bayar',
            'pen' => 'Penjamin',
            'kelaspelayanan_nama' => 'Nama Kelas Pelayanan',
            'jumlah_uangmuka' => 'Jumlah Uang Muka',
            'pasienpulangri_id' => 'ID Pasien Pulang Rawat Inap',
            'pasienadmisi_id' => 'ID Pasien Admisi',
            'status_pasien' => 'Status Pasien',
            'pasienmasukpenunjang_id' => 'ID Pasien Masuk Penunjang',
            'tglpasienpulang' => 'Tanggal Pasien Pulang',
            'nama_dok_rj_rd' => 'Nama Dokter RJ RD',
            'nama_dok_ri' => 'Nama Dokter RI',
            'jeniskasuspenyakit_nama' => 'Nama Jenis Kasus Penyakit',
            'umur' => 'Umur',
            'carabayar_id' => 'ID Cara Bayar',
            'penjamin_id' => 'ID Penjamin',
            'carabayar_nama' => 'Cara Bayar',
            'penjamin_nama' => 'Nama Penjamin',
            'instalasi_id' => 'ID Instalasi',
            'ruangan_id' => 'ID Ruangan',
            'instalasi_nama' => 'Nama Instalasi',
            'ruangan_nama' => 'Nama Ruangan',
            'status_bayar' => 'Status Bayar',
            'jeniskasuspenyakit_id' => 'ID Jenis Kasus Penyakit',
            'tanggal_lahir' => 'Tanggal Lahir',
            'penjualanresep_id' => 'ID Penjualan Resep',
            'jasa' => 'Jasa',
            'administrasi' => 'Administrasi',
            'obat' => 'Obat',
            'totalharga_jual' => 'Total Harga Jual',
            'hak_kelas' => 'Hak Kelas',
            'no_kartu' => 'No Kartu',
            'group_carabayar' => 'Group Cara Bayar',
            'total_piutang' => 'Total Piutang'
        ];
    }
}
