<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanpenjualanresep_v".
 *
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property int $penjualanresep_id
 * @property string $jenispenjualan
 * @property string $tglresep
 * @property string $noresep
 * @property double $totharganetto
 * @property double $totalhargajual
 * @property double $totaltarifservice
 * @property double $biayaadministrasi
 * @property double $biayakonseling
 * @property double $pembulatanharga
 * @property double $jasadokterresep
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property string $gelardepan
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $tglpenjualan
 * @property double $discount
 * @property double $subsidiasuransi
 * @property double $subsidipemerintah
 * @property double $subsidirs
 * @property double $iurbiaya
 * @property int $lamapelayanan
 * @property int $pasienadmisi_id
 * @property int $reseptur_id
 * @property int $pendaftaran_id
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $golongandarah
 * @property string $rhesus
 * @property int $anakke
 * @property int $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property string $photopasien
 * @property string $alamatemail
 * @property int $antrianfarmasi_id
 * @property string $no_antrian
 * @property bool $panggil_antrian
 * @property bool $antrian_lewat
 * @property string $tglambil_antrian
 * @property int $racikanantrian_id
 * @property string $racikanantrian_nama
 * @property string $racikanantrian_singkatan
 * @property double $racikanantrian_tarifservice
 * @property double $racikanantrian_persenservice
 * @property double $racikanantrian_biayakemasan
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property string $nomorindukpasien
 */
class LaporanPenjualanResepView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanpenjualanresep_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'penjualanresep_id', 'pegawai_id', 'carabayar_id', 'penjamin_id', 'lamapelayanan', 'pasienadmisi_id', 'reseptur_id', 'pendaftaran_id', 'anakke', 'jumlah_bersaudara', 'antrianfarmasi_id', 'racikanantrian_id', 'instalasi_id', 'ruangan_id'], 'default', 'value' => null],
            [['pasien_id', 'rt', 'rw', 'penjualanresep_id', 'pegawai_id', 'carabayar_id', 'penjamin_id', 'lamapelayanan', 'pasienadmisi_id', 'reseptur_id', 'pendaftaran_id', 'anakke', 'jumlah_bersaudara', 'antrianfarmasi_id', 'racikanantrian_id', 'instalasi_id', 'ruangan_id'], 'integer'],
            [['tanggal_lahir', 'tglresep', 'tglpenjualan', 'tglambil_antrian', 'tgl_pendaftaran'], 'safe'],
            [['alamat_pasien', 'jenispenjualan', 'noresep'], 'string'],
            [['totharganetto', 'totalhargajual', 'totaltarifservice', 'biayaadministrasi', 'biayakonseling', 'pembulatanharga', 'jasadokterresep', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'racikanantrian_tarifservice', 'racikanantrian_persenservice', 'racikanantrian_biayakemasan'], 'number'],
            [['panggil_antrian', 'antrian_lewat'], 'boolean'],
            [['no_rekam_medik', 'gelardepan'], 'string', 'max' => 10],
            [['namadepan', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien', 'racikanantrian_singkatan', 'no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien', 'nama_pegawai', 'carabayar_nama', 'penjamin_nama', 'racikanantrian_nama', 'instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
            [['nama_bin', 'nomorindukpasien'], 'string', 'max' => 30],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
            [['no_antrian'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'namadepan' => 'Namadepan',
            'nama_pasien' => 'Nama Pasien',
            'nama_bin' => 'Nama Bin',
            'jeniskelamin' => 'Jeniskelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'penjualanresep_id' => 'Penjualanresep ID',
            'jenispenjualan' => 'Jenispenjualan',
            'tglresep' => 'Tglresep',
            'noresep' => 'Noresep',
            'totharganetto' => 'Totharganetto',
            'totalhargajual' => 'Totalhargajual',
            'totaltarifservice' => 'Totaltarifservice',
            'biayaadministrasi' => 'Biayaadministrasi',
            'biayakonseling' => 'Biayakonseling',
            'pembulatanharga' => 'Pembulatanharga',
            'jasadokterresep' => 'Jasadokterresep',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'gelardepan' => 'Gelardepan',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'tglpenjualan' => 'Tglpenjualan',
            'discount' => 'Discount',
            'subsidiasuransi' => 'Subsidiasuransi',
            'subsidipemerintah' => 'Subsidipemerintah',
            'subsidirs' => 'Subsidirs',
            'iurbiaya' => 'Iurbiaya',
            'lamapelayanan' => 'Lamapelayanan',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'reseptur_id' => 'Reseptur ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'statusperkawinan' => 'Statusperkawinan',
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'rhesus' => 'Rhesus',
            'anakke' => 'Anakke',
            'jumlah_bersaudara' => 'Jumlah Bersaudara',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'warga_negara' => 'Warga Negara',
            'photopasien' => 'Photopasien',
            'alamatemail' => 'Alamatemail',
            'antrianfarmasi_id' => 'Antrianfarmasi ID',
            'no_antrian' => 'No Antrian',
            'panggil_antrian' => 'Panggil Antrian',
            'antrian_lewat' => 'Antrian Lewat',
            'tglambil_antrian' => 'Tglambil Antrian',
            'racikanantrian_id' => 'Racikanantrian ID',
            'racikanantrian_nama' => 'Racikanantrian Nama',
            'racikanantrian_singkatan' => 'Racikanantrian Singkatan',
            'racikanantrian_tarifservice' => 'Racikanantrian Tarifservice',
            'racikanantrian_persenservice' => 'Racikanantrian Persenservice',
            'racikanantrian_biayakemasan' => 'Racikanantrian Biayakemasan',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'nomorindukpasien' => 'Nomorindukpasien',
        ];
    }
}
