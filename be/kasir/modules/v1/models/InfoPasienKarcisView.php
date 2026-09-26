<?php

namespace app\modules\v1\models;

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
class InfoPasienKarcisView extends \Doco\components\DocoActiveRecord
{
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
            'pasien_id' => Yii::t('app', 'Pasien'),
            'jenisidentitas' => Yii::t('app', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('app', 'No identitas pasien'),
            'namadepan' => Yii::t('app', 'Nama depan'),
            'nama_pasien' => Yii::t('app', 'Nama Pasien'),
            'nama_bin' => Yii::t('app', 'Nama bin'),
            'jeniskelamin' => Yii::t('app', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('app', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('app', 'Tanggal lahir'),
            'alamat_pasien' => Yii::t('app', 'Alamat pasien'),
            'rt' => Yii::t('app', 'Rt'),
            'rw' => Yii::t('app', 'Rw'),
            'agama' => Yii::t('app', 'Agama'),
            'golongandarah' => Yii::t('app', 'Golongan darah'),
            'photopasien' => Yii::t('app', 'Photo pasien'),
            'alamatemail' => Yii::t('app', 'Alama temail'),
            'statusrekammedis' => Yii::t('app', 'Status rekam medis'),
            'statusperkawinan' => Yii::t('app', 'Status perkawinan'),
            'no_rekam_medik' => Yii::t('app', 'No rekam medik'),
            'tgl_rekam_medik' => Yii::t('app', 'Tanggal rekam medik'),
            'propinsi_id' => Yii::t('app', 'Propinsi'),
            'propinsi_nama' => Yii::t('app', 'Nama propinsi'),
            'kabupaten_id' => Yii::t('app', 'Kabupaten'),
            'kabupaten_nama' => Yii::t('app', 'Nama kabupaten'),
            'kelurahan_id' => Yii::t('app', 'Kelurahan'),
            'kelurahan_nama' => Yii::t('app', 'Nama kelurahan'),
            'kecamatan_id' => Yii::t('app', 'Kecamatan'),
            'kecamatan_nama' => Yii::t('app', 'Nama kecamatan'),
            'pekerjaan_id' => Yii::t('app', 'Pekerjaan'),
            'pekerjaan_nama' => Yii::t('app', 'Nama pekerjaan'),
            'suku_id' => Yii::t('app', 'Suku'),
            'suku_nama' => Yii::t('app', 'Nama suku'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'no_pendaftaran' => Yii::t('app', 'No pendaftaran'),
            'tgl_pendaftaran' => Yii::t('app', 'Tanggal pendaftaran'),
            'no_urutantri' => Yii::t('app', 'No urutant rawat inap'),
            'transportasi' => Yii::t('app', 'Transportasi'),
            'keadaan_masuk' => Yii::t('app', 'Keadaan masuk'),
            'status_periksa' => Yii::t('app', 'Status periksa'),
            'status_pasien' => Yii::t('app', 'Status pasien'),
            'kunjungan' => Yii::t('app', 'Kunjungan'),
            'alih_status' => Yii::t('app', 'Alih status'),
            'by_phone' => Yii::t('app', 'By phone'),
            'kunjungan_rumah' => Yii::t('app', 'Kunjungan rumah'),
            'status_masuk' => Yii::t('app', 'Status masuk'),
            'umur' => Yii::t('app', 'Umur'),
            'jeniskasuspenyakit_id' => Yii::t('app', 'Jenis kasus penyakit'),
            'jeniskasuspenyakit_nama' => Yii::t('app', 'Nama jenis kasus penyakit'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'penjamin_nama' => Yii::t('app', 'Nama Penjamin'),
            'kelaspelayanan_id' => Yii::t('app', 'Kelas pelayanan'),
            'kelaspelayanan_nama' => Yii::t('app', 'Nama kelas pelayanan'),
            'tindakanpelayanan_id' => Yii::t('app', 'Tindakan pelayanan'),
            'shift_id' => Yii::t('app', 'Shift'),
            'shift_nama' => Yii::t('app', 'Nama shift'),
            'tipepaket_id' => Yii::t('app', 'Tipe paket'),
            'tipepaket_nama' => Yii::t('app', 'Nama tipe paket'),
            'daftartindakan_id' => Yii::t('app', 'Daftar tindakan'),
            'daftartindakan_kode' => Yii::t('app', 'Kode daftar tindakan'),
            'daftartindakan_nama' => Yii::t('app', 'Nama daftar tindakan'),
            'karcis_id' => Yii::t('app', 'Karcis'),
            'karcis_nama' => Yii::t('app', 'Nama Karcis'),
            'pasienmasukpenunjang_id' => Yii::t('app', 'Pasien masuk penunjang'),
            'no_masukpenunjang' => Yii::t('app', 'No masuk penunjang'),
            'tglmasukpenunjang' => Yii::t('app', 'Tanggal masuk penunjang'),
            'pasienadmisi_id' => Yii::t('app', 'Pasien admisi'),
            'tgl_admisi' => Yii::t('app', 'Tanggal Admisi'),
            'caramasuk_id' => Yii::t('app', 'Cara masuk'),
            'caramasuk_nama' => Yii::t('app', 'Nama cara masuk'),
            'tgl_tindakan' => Yii::t('app', 'Tanggal tindakan'),
            'tarif_rsakomodasi' => Yii::t('app', 'Tarif rs akomodasi'),
            'tarif_medis' => Yii::t('app', 'Tarif medis'),
            'tarif_paramedis' => Yii::t('app', 'Tarif paramedis'),
            'tarif_bhp' => Yii::t('app', 'Tarif bhp'),
            'tarif_satuan' => Yii::t('app', 'Tarif satuan'),
            'tarif_tindakan' => Yii::t('app', 'Tarif tindakan'),
            'satuan_tindakan' => Yii::t('app', 'Satuan tindakan'),
            'qty_tindakan' => Yii::t('app', 'Qty tindakan'),
            'cyto_tindakan' => Yii::t('app', 'Cyto tindakan'),
            'tarifcyto_tindakan' => Yii::t('app', 'Tarif cyto tindakan'),
            'kelastanggungan_id' => Yii::t('app', 'Kelas tanggungan'),
            'kelastanggungan_nama' => Yii::t('app', 'Nama kelastanggungan'),
            'pembebasan_tindakan' => Yii::t('app', 'Pembebasan tindakan'),
            'subsidiasuransi_tindakan' => Yii::t('app', 'Subsidi asuransi tindakan'),
            'subsidipemerintah_tindakan' => Yii::t('app', 'Subsidi pemerintah tindakan'),
            'subsisidirumahsakit_tindakan' => Yii::t('app', 'Subsisidi rumah sakit tindakan'),
            'uangditerima_tindakan' => Yii::t('app', 'Uang diterima tindakan'),
            'verifikasitagihan_id' => Yii::t('app', 'Verifikasi tagihan'),
            'jurnalrekening_id' => Yii::t('app', 'Jurnal rekening'),
            'keterangantindakan' => Yii::t('app', 'Keterangan tindakan'),
            'tindakansudahbayar_id' => Yii::t('app', 'Tindakan sudah bayar'),
        ];
    }
}
