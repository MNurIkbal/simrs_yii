<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "infopasienkarcis_v".
 *
 * @property integer $pasien_id
 * @property string $jenisidentitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property integer $rt
 * @property integer $rw
 * @property string $agama
 * @property string $golongandarah
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $statusrekammedis
 * @property string $statusperkawinan
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property integer $propinsi_id
 * @property string $propinsi_nama
 * @property integer $kabupaten_id
 * @property string $kabupaten_nama
 * @property integer $kelurahan_id
 * @property string $kelurahan_nama
 * @property integer $kecamatan_id
 * @property string $kecamatan_nama
 * @property integer $pekerjaan_id
 * @property string $pekerjaan_nama
 * @property integer $suku_id
 * @property string $suku_nama
 * @property integer $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_urutantri
 * @property string $transportasi
 * @property string $keadaan_masuk
 * @property string $status_periksa
 * @property string $status_pasien
 * @property string $kunjungan
 * @property boolean $alih_status
 * @property boolean $by_phone
 * @property boolean $kunjungan_rumah
 * @property string $status_masuk
 * @property string $umur
 * @property integer $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property integer $penjamin_id
 * @property string $penjamin_nama
 * @property integer $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property integer $tindakanpelayanan_id
 * @property integer $shift_id
 * @property string $shift_nama
 * @property integer $tipepaket_id
 * @property string $tipepaket_nama
 * @property integer $daftartindakan_id
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property integer $karcis_id
 * @property string $karcis_nama
 * @property integer $pasienmasukpenunjang_id
 * @property string $no_masukpenunjang
 * @property string $tglmasukpenunjang
 * @property integer $pasienadmisi_id
 * @property string $tgl_admisi
 * @property integer $caramasuk_id
 * @property string $caramasuk_nama
 * @property string $tgl_tindakan
 * @property double $tarif_rsakomodasi
 * @property double $tarif_medis
 * @property double $tarif_paramedis
 * @property double $tarif_bhp
 * @property double $tarif_satuan
 * @property double $tarif_tindakan
 * @property string $satuan_tindakan
 * @property integer $qty_tindakan
 * @property boolean $cyto_tindakan
 * @property double $tarifcyto_tindakan
 * @property integer $kelastanggungan_id
 * @property string $kelastanggungan_nama
 * @property double $pembebasan_tindakan
 * @property double $subsidiasuransi_tindakan
 * @property double $subsidipemerintah_tindakan
 * @property double $subsisidirumahsakit_tindakan
 * @property double $uangditerima_tindakan
 * @property integer $verifikasitagihan_id
 * @property integer $jurnalrekening_id
 * @property string $keterangantindakan
 * @property integer $tindakansudahbayar_id
 */
class InfoPasienKarcisForm extends \yii\base\Model
{
    public $pasien_id;
    public $jenisidentitas;
    public $no_identitas_pasien;
    public $namadepan;
    public $nama_pasien;
    public $nama_bin;
    public $jeniskelamin;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $alamat_pasien;
    public $rt;
    public $rw;
    public $agama;
    public $golongandarah;
    public $photopasien;
    public $alamatemail;
    public $statusrekammedis;
    public $statusperkawinan;
    public $no_rekam_medik;
    public $tgl_rekam_medik;
    public $propinsi_id;
    public $propinsi_nama;
    public $kabupaten_id;
    public $kabupaten_nama;
    public $kelurahan_id;
    public $kelurahan_nama;
    public $kecamatan_id;
    public $kecamatan_nama;
    public $pekerjaan_id;
    public $pekerjaan_nama;
    public $suku_id;
    public $suku_nama;
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $no_urutantri;
    public $transportasi;
    public $keadaan_masuk;
    public $status_periksa;
    public $status_pasien;
    public $kunjungan;
    public $alih_status;
    public $by_phone;
    public $kunjungan_rumah;
    public $status_masuk;
    public $umur;
    public $jeniskasuspenyakit_id;
    public $jeniskasuspenyakit_nama;
    public $instalasi_id;
    public $instalasi_nama;
    public $ruangan_id;
    public $ruangan_nama;
    public $carabayar_id;
    public $carabayar_nama;
    public $penjamin_id;
    public $penjamin_nama;
    public $kelaspelayanan_id;
    public $kelaspelayanan_nama;
    public $tindakanpelayanan_id;
    public $shift_id;
    public $shift_nama;
    public $tipepaket_id;
    public $tipepaket_nama;
    public $daftartindakan_id;
    public $daftartindakan_kode;
    public $daftartindakan_nama;
    public $karcis_id;
    public $karcis_nama;
    public $pasienmasukpenunjang_id;
    public $no_masukpenunjang;
    public $tglmasukpenunjang;
    public $pasienadmisi_id;
    public $tgl_admisi;
    public $caramasuk_id;
    public $caramasuk_nama;
    public $tgl_tindakan;
    public $tarif_rsakomodasi;
    public $tarif_medis;
    public $tarif_paramedis;
    public $tarif_bhp;
    public $tarif_satuan;
    public $tarif_tindakan;
    public $satuan_tindakan;
    public $qty_tindakan;
    public $cyto_tindakan;
    public $tarifcyto_tindakan;
    public $kelastanggungan_id;
    public $kelastanggungan_nama;
    public $pembebasan_tindakan;
    public $subsidiasuransi_tindakan;
    public $subsidipemerintah_tindakan;
    public $subsisidirumahsakit_tindakan;
    public $uangditerima_tindakan;
    public $verifikasitagihan_id;
    public $jurnalrekening_id;
    public $keterangantindakan;
    public $tindakansudahbayar_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasienkarcis_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'propinsi_id', 'kabupaten_id', 'kelurahan_id', 'kecamatan_id', 'pekerjaan_id', 'suku_id', 'pendaftaran_id', 'jeniskasuspenyakit_id', 'instalasi_id', 'ruangan_id', 'carabayar_id', 'penjamin_id', 'kelaspelayanan_id', 'tindakanpelayanan_id', 'shift_id', 'tipepaket_id', 'daftartindakan_id', 'karcis_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'caramasuk_id', 'qty_tindakan', 'kelastanggungan_id', 'verifikasitagihan_id', 'jurnalrekening_id', 'tindakansudahbayar_id'], 'integer'],
            [['tanggal_lahir', 'tgl_rekam_medik', 'tgl_pendaftaran', 'tglmasukpenunjang', 'tgl_admisi', 'tgl_tindakan'], 'safe'],
            [['alamat_pasien'], 'string'],
            [['alih_status', 'by_phone', 'kunjungan_rumah', 'cyto_tindakan'], 'boolean'],
            [['tarif_rsakomodasi', 'tarif_medis', 'tarif_paramedis', 'tarif_bhp', 'tarif_satuan', 'tarif_tindakan', 'tarifcyto_tindakan', 'pembebasan_tindakan', 'subsidiasuransi_tindakan', 'subsidipemerintah_tindakan', 'subsisidirumahsakit_tindakan', 'uangditerima_tindakan'], 'number'],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'agama', 'statusperkawinan', 'no_pendaftaran', 'daftartindakan_kode', 'no_masukpenunjang'], 'string', 'max' => 20],
            [['no_identitas_pasien', 'nama_bin', 'umur'], 'string', 'max' => 30],
            [['nama_pasien', 'propinsi_nama', 'kabupaten_nama', 'kelurahan_nama', 'kecamatan_nama', 'pekerjaan_nama', 'suku_nama', 'transportasi', 'keadaan_masuk', 'status_periksa', 'status_pasien', 'kunjungan', 'status_masuk', 'instalasi_nama', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama', 'shift_nama', 'tipepaket_nama', 'caramasuk_nama', 'kelastanggungan_nama'], 'string', 'max' => 50],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['photopasien', 'daftartindakan_nama', 'keterangantindakan'], 'string', 'max' => 200],
            [['alamatemail', 'jeniskasuspenyakit_nama', 'karcis_nama'], 'string', 'max' => 100],
            [['statusrekammedis', 'no_rekam_medik', 'satuan_tindakan'], 'string', 'max' => 10],
            [['no_urutantri'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => Yii::t('fe', 'Pasien'),
            'jenisidentitas' => Yii::t('fe', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('fe', 'No identitas pasien'),
            'namadepan' => Yii::t('fe', 'Nama depan'),
            'nama_pasien' => Yii::t('fe', 'Nama Pasien'),
            'nama_bin' => Yii::t('fe', 'Nama bin'),
            'jeniskelamin' => Yii::t('fe', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('fe', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('fe', 'Tanggal lahir'),
            'alamat_pasien' => Yii::t('fe', 'Alamat pasien'),
            'rt' => Yii::t('fe', 'Rt'),
            'rw' => Yii::t('fe', 'Rw'),
            'agama' => Yii::t('fe', 'Agama'),
            'golongandarah' => Yii::t('fe', 'Golongan darah'),
            'photopasien' => Yii::t('fe', 'Photo pasien'),
            'alamatemail' => Yii::t('fe', 'Alamat email'),
            'statusrekammedis' => Yii::t('fe', 'Status rekam medis'),
            'statusperkawinan' => Yii::t('fe', 'Status perkawinan'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'tgl_rekam_medik' => Yii::t('fe', 'Tanggal rekam medik'),
            'propinsi_id' => Yii::t('fe', 'Propinsi'),
            'propinsi_nama' => Yii::t('fe', 'Nama propinsi'),
            'kabupaten_id' => Yii::t('fe', 'Kabupaten'),
            'kabupaten_nama' => Yii::t('fe', 'Nama kabupaten'),
            'kelurahan_id' => Yii::t('fe', 'Kelurahan'),
            'kelurahan_nama' => Yii::t('fe', 'Nama kelurahan'),
            'kecamatan_id' => Yii::t('fe', 'Kecamatan'),
            'kecamatan_nama' => Yii::t('fe', 'Nama kecamatan'),
            'pekerjaan_id' => Yii::t('fe', 'Pekerjaan'),
            'pekerjaan_nama' => Yii::t('fe', 'Nama pekerjaan'),
            'suku_id' => Yii::t('fe', 'Suku'),
            'suku_nama' => Yii::t('fe', 'Nama suku'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'tgl_pendaftaran' => Yii::t('fe', 'Tanggal pendaftaran'),
            'no_urutantri' => Yii::t('fe', 'No urutant rawat inap'),
            'transportasi' => Yii::t('fe', 'Transportasi'),
            'keadaan_masuk' => Yii::t('fe', 'Keadaan masuk'),
            'status_periksa' => Yii::t('fe', 'Status periksa'),
            'status_pasien' => Yii::t('fe', 'Status pasien'),
            'kunjungan' => Yii::t('fe', 'Kunjungan'),
            'alih_status' => Yii::t('fe', 'Alih status'),
            'by_phone' => Yii::t('fe', 'By phone'),
            'kunjungan_rumah' => Yii::t('fe', 'Kunjungan rumah'),
            'status_masuk' => Yii::t('fe', 'Status masuk'),
            'umur' => Yii::t('fe', 'Umur'),
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Jenis kasus penyakit'),
            'jeniskasuspenyakit_nama' => Yii::t('fe', 'Nama jenis kasus penyakit'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'instalasi_nama' => Yii::t('fe', 'Nama instalasi'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'carabayar_id' => Yii::t('fe', 'Cara bayar'),
            'carabayar_nama' => Yii::t('fe', 'Nama cara bayar'),
            'penjamin_id' => Yii::t('fe', 'Penjamin'),
            'penjamin_nama' => Yii::t('fe', 'Nama Penjamin'),
            'kelaspelayanan_id' => Yii::t('fe', 'Kelas pelayanan'),
            'kelaspelayanan_nama' => Yii::t('fe', 'Nama kelas pelayanan'),
            'tindakanpelayanan_id' => Yii::t('fe', 'Tindakan pelayanan'),
            'shift_id' => Yii::t('fe', 'Shift'),
            'shift_nama' => Yii::t('fe', 'Nama shift'),
            'tipepaket_id' => Yii::t('fe', 'Tipe paket'),
            'tipepaket_nama' => Yii::t('fe', 'Nama tipe paket'),
            'daftartindakan_id' => Yii::t('fe', 'Daftar tindakan'),
            'daftartindakan_kode' => Yii::t('fe', 'Kode daftar tindakan'),
            'daftartindakan_nama' => Yii::t('fe', 'Nama daftar tindakan'),
            'karcis_id' => Yii::t('fe', 'Karcis'),
            'karcis_nama' => Yii::t('fe', 'Nama Karcis'),
            'pasienmasukpenunjang_id' => Yii::t('fe', 'Pasien masuk penunjang'),
            'no_masukpenunjang' => Yii::t('fe', 'No masuk penunjang'),
            'tglmasukpenunjang' => Yii::t('fe', 'Tanggal masuk penunjang'),
            'pasienadmisi_id' => Yii::t('fe', 'Pasien admisi'),
            'tgl_admisi' => Yii::t('fe', 'Tanggal Admisi'),
            'caramasuk_id' => Yii::t('fe', 'Cara masuk'),
            'caramasuk_nama' => Yii::t('fe', 'Nama cara masuk'),
            'tgl_tindakan' => Yii::t('fe', 'Tanggal tindakan'),
            'tarif_rsakomodasi' => Yii::t('fe', 'Tarif rs akomodasi'),
            'tarif_medis' => Yii::t('fe', 'Tarif medis'),
            'tarif_paramedis' => Yii::t('fe', 'Tarif paramedis'),
            'tarif_bhp' => Yii::t('fe', 'Tarif bhp'),
            'tarif_satuan' => Yii::t('fe', 'Tarif satuan'),
            'tarif_tindakan' => Yii::t('fe', 'Tarif tindakan'),
            'satuan_tindakan' => Yii::t('fe', 'Satuan tindakan'),
            'qty_tindakan' => Yii::t('fe', 'Qty tindakan'),
            'cyto_tindakan' => Yii::t('fe', 'Cyto tindakan'),
            'tarifcyto_tindakan' => Yii::t('fe', 'Tarif cyto tindakan'),
            'kelastanggungan_id' => Yii::t('fe', 'Kelas tanggungan'),
            'kelastanggungan_nama' => Yii::t('fe', 'Nama kelastanggungan'),
            'pembebasan_tindakan' => Yii::t('fe', 'Pembebasan tindakan'),
            'subsidiasuransi_tindakan' => Yii::t('fe', 'Subsidi asuransi tindakan'),
            'subsidipemerintah_tindakan' => Yii::t('fe', 'Subsidi pemerintah tindakan'),
            'subsisidirumahsakit_tindakan' => Yii::t('fe', 'Subsisidi rumah sakit tindakan'),
            'uangditerima_tindakan' => Yii::t('fe', 'Uang diterima tindakan'),
            'verifikasitagihan_id' => Yii::t('fe', 'Verifikasi tagihan'),
            'jurnalrekening_id' => Yii::t('fe', 'Jurnal rekening'),
            'keterangantindakan' => Yii::t('fe', 'Keterangan tindakan'),
            'tindakansudahbayar_id' => Yii::t('fe', 'Tindakan sudah bayar'),
        ];
    }
}
