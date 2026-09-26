<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopenjualanresepdetail_v".
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
 * @property int $obatalkes_id
 * @property int $jenisobatalkes_id
 * @property string $jenisobatalkes_nama
 * @property string $obatalkes_kode
 * @property string $obatalkes_namalain
 * @property string $obatalkes_golongan
 * @property string $obatalkes_kategori
 * @property string $obatalkes_kadarobat
 * @property int $kekuatan
 * @property int $racikan_id
 * @property int $shift_id
 * @property string $tglpelayanan
 * @property string $r
 * @property int $rke
 * @property double $qty_oa
 * @property double $qty_medis
 * @property double $det_medis
 * @property double $hargasatuan_oa
 * @property string $signa_oa
 * @property double $harganetto_oa
 * @property double $hargajual_oa
 * @property string $etiket
 * @property double $biayaservice
 * @property double $biayakemasan
 * @property string $oa
 * @property int $sumberdana_id
 * @property string $sumberdana_nama
 * @property int $satuankecil_id
 * @property string $satuankecil_nama
 * @property int $tipepaket_id
 * @property int $obatsudahbayar_id
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
 * @property int $pembayaranpelayanan_id
 * @property string $tgl_pembayaran
 * @property string $no_pembayaran
 * @property int $tandabuktibayar_id
 * @property string $tglbuktibayar
 * @property string $nobuktibayar
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property string $nomorindukpasien
 */
class InfoPenjualanResepDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopenjualanresepdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'penjualanresep_id', 'pegawai_id', 'carabayar_id', 'penjamin_id', 'lamapelayanan', 'pasienadmisi_id', 'reseptur_id', 'pendaftaran_id', 'obatalkes_id', 'jenisobatalkes_id', 'kekuatan', 'racikan_id', 'shift_id', 'rke', 'sumberdana_id', 'satuankecil_id', 'tipepaket_id', 'obatsudahbayar_id', 'anakke', 'jumlah_bersaudara', 'antrianfarmasi_id', 'racikanantrian_id', 'instalasi_id', 'ruangan_id', 'pembayaranpelayanan_id', 'tandabuktibayar_id','additional_data'], 'default', 'value' => null],
            [['pasien_id', 'rt', 'rw', 'penjualanresep_id', 'pegawai_id', 'carabayar_id', 'penjamin_id', 'lamapelayanan', 'pasienadmisi_id', 'reseptur_id', 'pendaftaran_id', 'obatalkes_id', 'jenisobatalkes_id', 'kekuatan', 'racikan_id', 'shift_id', 'rke', 'sumberdana_id', 'satuankecil_id', 'tipepaket_id', 'obatsudahbayar_id', 'anakke', 'jumlah_bersaudara', 'antrianfarmasi_id', 'racikanantrian_id', 'instalasi_id', 'ruangan_id', 'pembayaranpelayanan_id', 'tandabuktibayar_id'], 'integer'],
            [['tanggal_lahir', 'tglresep', 'tglpenjualan', 'tglpelayanan', 'tglambil_antrian', 'tgl_pembayaran', 'tglbuktibayar', 'tgl_pendaftaran'], 'safe'],
            [['alamat_pasien', 'jenispenjualan', 'noresep', 'jenisobatalkes_nama', 'obatalkes_kode', 'obatalkes_namalain', 'obatalkes_golongan', 'obatalkes_kategori', 'obatalkes_kadarobat', 'etiket', 'sumberdana_nama', 'satuankecil_nama'], 'string'],
            [['totharganetto', 'totalhargajual', 'totaltarifservice', 'biayaadministrasi', 'biayakonseling', 'pembulatanharga', 'jasadokterresep', 'discount', 'subsidiasuransi', 'subsidipemerintah', 'subsidirs', 'iurbiaya', 'qty_oa', 'hargasatuan_oa', 'harganetto_oa', 'hargajual_oa', 'biayaservice', 'biayakemasan', 'racikanantrian_tarifservice', 'racikanantrian_persenservice', 'racikanantrian_biayakemasan'], 'number'],
            [['panggil_antrian', 'antrian_lewat'], 'boolean'],
            [['no_rekam_medik', 'gelardepan'], 'string', 'max' => 10],
            [['namadepan', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien', 'racikanantrian_singkatan', 'no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien', 'nama_pegawai', 'carabayar_nama', 'penjamin_nama', 'racikanantrian_nama', 'instalasi_nama', 'ruangan_nama', 'no_pembayaran', 'nobuktibayar'], 'string', 'max' => 50],
            [['nama_bin', 'nomorindukpasien'], 'string', 'max' => 30],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['r', 'oa'], 'string', 'max' => 1],
            [['signa_oa'], 'string', 'max' => 53],
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
            'obatalkes_id' => 'Obatalkes ID',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'obatalkes_kode' => 'Obatalkes Kode',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'obatalkes_golongan' => 'Obatalkes Golongan',
            'obatalkes_kategori' => 'Obatalkes Kategori',
            'obatalkes_kadarobat' => 'Obatalkes Kadarobat',
            'kekuatan' => 'Kekuatan',
            'racikan_id' => 'Racikan ID',
            'shift_id' => 'Shift ID',
            'tglpelayanan' => 'Tglpelayanan',
            'r' => 'R',
            'rke' => 'Rke',
            'qty_oa' => 'Qty Oa',
            'hargasatuan_oa' => 'Hargasatuan Oa',
            'signa_oa' => 'Signa Oa',
            'harganetto_oa' => 'Harganetto Oa',
            'hargajual_oa' => 'Hargajual Oa',
            'etiket' => 'Etiket',
            'biayaservice' => 'Biayaservice',
            'biayakemasan' => 'Biayakemasan',
            'oa' => 'Oa',
            'sumberdana_id' => 'Sumberdana ID',
            'sumberdana_nama' => 'Sumberdana Nama',
            'satuankecil_id' => 'Satuankecil ID',
            'satuankecil_nama' => 'Satuankecil Nama',
            'tipepaket_id' => 'Tipepaket ID',
            'obatsudahbayar_id' => 'Obatsudahbayar ID',
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
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'tgl_pembayaran' => 'Tgl Pembayaran',
            'no_pembayaran' => 'No Pembayaran',
            'tandabuktibayar_id' => 'Tandabuktibayar ID',
            'tglbuktibayar' => 'Tglbuktibayar',
            'nobuktibayar' => 'Nobuktibayar',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'nomorindukpasien' => 'Nomorindukpasien',
        ];
    }
}
