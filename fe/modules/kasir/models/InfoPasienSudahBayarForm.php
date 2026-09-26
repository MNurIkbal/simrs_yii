<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "infopasiensudahbayar_v".
 *
 * @property integer $pembayaranpelayanan_id
 * @property integer $pendaftaran_id
 * @property integer $pasienadmisi_id
 * @property string $tglinvoice
 * @property string $tglbuktibayar
 * @property string $nobuktibayar
 * @property string $instalasi
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $nama_bin
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property double $total_biayapelayanan
 * @property double $totalsubsidiasuransi
 * @property double $total_subsidipemerintah
 * @property double $total_subsidirs
 * @property double $total_iurbiaya
 * @property double $total_discount
 * @property double $total_pembebasan
 * @property double $total_bayartindakan
 * @property integer $tandabuktibayar_id
 * @property integer $returbayarpelayanan_id
 * @property integer $closingkasir_id
 * @property string $ruangan_nama
 * @property integer $ruangan_id
 * @property integer $ruangankasir_id
 * @property string $ruangankasir_nama
 * @property string $tgl_selesaiperiksa
 * @property string $jenisidentitas
 * @property string $no_identitas_pasien
 * @property string $jeniskelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $alamat_pasien
 * @property integer $rt
 * @property integer $rw
 * @property integer $kelurahan_id
 * @property string $kelurahan_nama
 * @property integer $kecamatan_id
 * @property string $kecamatan_nama
 * @property integer $kabupaten_id
 * @property string $kabupaten_nama
 * @property integer $propinsi_id
 * @property string $propinsi_nama
 * @property string $statusperkawinan
 * @property string $agama
 * @property string $golongandarah
 * @property string $rhesus
 * @property integer $anakke
 * @property integer $jumlah_bersaudara
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $warga_negara
 * @property string $photopasien
 * @property string $alamatemail
 * @property string $statusrekammedis
 * @property string $tgl_meninggal
 * @property string $nama_ibu
 * @property string $nama_ayah
 * @property integer $petugasadministrasi_id
 * @property string $petugasadministrasi_gelardepan
 * @property string $petugasadministrasi_nama
 * @property string $petugasadministrasi_gelarbelakang
 * @property integer $dokterpendaftaran_id
 * @property string $dokterpendaftaran_gelardepan
 * @property string $dokterpendaftaran_nama
 * @property string $dokterpendaftaran_gelarbelakang
 * @property integer $dokteradmisi_id
 * @property string $dokteradmisi_gelardepan
 * @property string $dokteradmisi_nama
 * @property string $dokteradmisi_gelarbelakang
 * @property integer $instalasi_id
 * @property string $tgl_pendaftaran
 * @property string $tgl_admisi
 * @property integer $pembklaimdetail_id
 * @property integer $piutangasuransi_id
 * @property integer $pendaftaran_t
 */
class InfoPasienSudahBayarForm extends \yii\base\Model
{
    
    public $pembayaranpelayanan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $tglinvoice;
    public $tglbuktibayar;
    public $nobuktibayar;
    public $instalasi;
    public $no_pendaftaran;
    public $no_rekam_medik;
    public $nama_pasien;
    public $nama_bin;
    public $carabayar_nama;
    public $penjamin_nama;
    public $total_biayapelayanan;
    public $totalsubsidiasuransi;
    public $total_subsidipemerintah;
    public $total_subsidirs;
    public $total_iurbiaya;
    public $total_discount;
    public $total_pembebasan;
    public $total_bayartindakan;
    public $tandabuktibayar_id;
    public $returbayarpelayanan_id;
    public $closingkasir_id;
    public $ruangan_nama;
    public $ruangan_id;
    public $ruangankasir_id;
    public $ruangankasir_nama;
    public $tgl_selesaiperiksa;
    public $jenisidentitas;
    public $no_identitas_pasien;
    public $jeniskelamin;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $alamat_pasien;
    public $rt;
    public $rw;
    public $kelurahan_id;
    public $kelurahan_nama;
    public $kecamatan_id;
    public $kecamatan_nama;
    public $kabupaten_id;
    public $kabupaten_nama;
    public $propinsi_id;
    public $propinsi_nama;
    public $statusperkawinan;
    public $agama;
    public $golongandarah;
    public $rhesus;
    public $anakke;
    public $jumlah_bersaudara;
    public $no_telepon_pasien;
    public $no_mobile_pasien;
    public $warga_negara;
    public $photopasien;
    public $alamatemail;
    public $statusrekammedis;
    public $tgl_meninggal;
    public $nama_ibu;
    public $nama_ayah;
    public $petugasadministrasi_id;
    public $petugasadministrasi_gelardepan;
    public $petugasadministrasi_nama;
    public $petugasadministrasi_gelarbelakang;
    public $dokterpendaftaran_id;
    public $dokterpendaftaran_gelardepan;
    public $dokterpendaftaran_nama;
    public $dokterpendaftaran_gelarbelakang;
    public $dokteradmisi_id;
    public $dokteradmisi_gelardepan;
    public $dokteradmisi_nama;
    public $dokteradmisi_gelarbelakang;
    public $instalasi_id;
    public $tgl_pendaftaran;
    public $tgl_admisi;
    public $pembklaimdetail_id;
    public $piutangasuransi_id;
    public $kelaspelayanan_nama;

    // Relation
    public $pendaftaran_t;
    public $pembayaranpelayanan_t;
    public $returbayarpelayanan_t;
    public $tandabuktibayar_t;
    public $bank_m;
    public $tandabuktikeluar_t;
    public $tgl_pembayaran;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasiensudahbayar_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaranpelayanan_id', 'pendaftaran_id', 'pasienadmisi_id', 'tandabuktibayar_id', 'returbayarpelayanan_id', 'closingkasir_id', 'ruangan_id', 'ruangankasir_id', 'rt', 'rw', 'kelurahan_id', 'kecamatan_id', 'kabupaten_id', 'propinsi_id', 'anakke', 'jumlah_bersaudara', 'petugasadministrasi_id', 'dokterpendaftaran_id', 'dokteradmisi_id', 'instalasi_id', 'pembklaimdetail_id', 'piutangasuransi_id'], 'integer'],
            [['tglinvoice', 'tglbuktibayar', 'tgl_selesaiperiksa', 'tanggal_lahir', 'tgl_meninggal', 'tgl_pendaftaran', 'tgl_admisi', 'kelaspelayanan_nama', 'pendaftaran_t', 'pembayaranpelayanan_t', 'tandabuktibayar_t', 'returbayarpelayanan_t', 'bank_m', 'tandabuktikeluar_t', 'tgl_pembayaran'], 'safe'],
            [['total_biayapelayanan', 'totalsubsidiasuransi', 'total_subsidipemerintah', 'total_subsidirs', 'total_iurbiaya', 'total_discount', 'total_pembebasan', 'total_bayartindakan'], 'number'],
            [['alamat_pasien'], 'string'],
            [['nobuktibayar', 'instalasi', 'nama_pasien', 'carabayar_nama', 'penjamin_nama', 'ruangan_nama', 'ruangankasir_nama', 'kelurahan_nama', 'kecamatan_nama', 'kabupaten_nama', 'propinsi_nama', 'nama_ibu', 'nama_ayah', 'petugasadministrasi_nama', 'dokterpendaftaran_nama', 'dokteradmisi_nama'], 'string', 'max' => 50],
            [['no_pendaftaran', 'jenisidentitas', 'jeniskelamin', 'statusperkawinan', 'agama', 'rhesus', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_rekam_medik', 'statusrekammedis', 'petugasadministrasi_gelardepan', 'dokterpendaftaran_gelardepan', 'dokteradmisi_gelardepan'], 'string', 'max' => 10],
            [['nama_bin', 'no_identitas_pasien'], 'string', 'max' => 30],
            [['tempat_lahir', 'warga_negara'], 'string', 'max' => 25],
            [['golongandarah'], 'string', 'max' => 2],
            [['no_telepon_pasien', 'petugasadministrasi_gelarbelakang', 'dokterpendaftaran_gelarbelakang', 'dokteradmisi_gelarbelakang'], 'string', 'max' => 15],
            [['photopasien'], 'string', 'max' => 200],
            [['alamatemail'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembayaranpelayanan_id' => Yii::t('fe', 'Pembayaran pelayanan'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'pasienadmisi_id' => Yii::t('fe', 'Pasien admisi'),
            'tglinvoice' => Yii::t('fe', 'Tanggal invoice'),
            'tglbuktibayar' => Yii::t('fe', 'Tanggal pembayaran'),
            'nobuktibayar' => Yii::t('fe', 'No bukti bayar'),
            'instalasi' => Yii::t('fe', 'Nama instalasi'),
            'no_pendaftaran' => Yii::t('fe', 'No pendaftaran'),
            'no_rekam_medik' => Yii::t('fe', 'No rekam medik'),
            'nama_pasien' => Yii::t('fe', 'Nama pasien'),
            'nama_bin' => Yii::t('fe', 'Nama bin'),
            'carabayar_nama' => Yii::t('fe', 'Cara bayar'),
            'penjamin_nama' => Yii::t('fe', 'Penjamin'),
            'total_biayapelayanan' => Yii::t('fe', 'Total tagihan'),
            'totalsubsidiasuransi' => Yii::t('fe', 'Total subsidi asuransi'),
            'total_subsidipemerintah' => Yii::t('fe', 'Total subsidi pemerintah'),
            'total_subsidirs' => Yii::t('fe', 'Total subsidi rs'),
            'total_iurbiaya' => Yii::t('fe', 'Total iur biaya'),
            'total_discount' => Yii::t('fe', 'Total discount'),
            'total_pembebasan' => Yii::t('fe', 'Total pembebasan'),
            'total_bayartindakan' => Yii::t('fe', 'Total tagihan'),
            'tandabuktibayar_id' => Yii::t('fe', 'Tanda bukti bayar'),
            'returbayarpelayanan_id' => Yii::t('fe', 'Retur bayar pelayanan'),
            'closingkasir_id' => Yii::t('fe', 'Closing kasir'),
            'ruangan_nama' => Yii::t('fe', 'Nama ruangan'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'ruangankasir_id' => Yii::t('fe', 'Ruangan kasir'),
            'ruangankasir_nama' => Yii::t('fe', 'Nama ruangan kasir'),
            'tgl_selesaiperiksa' => Yii::t('fe', 'Tanggal selesai periksa'),
            'jenisidentitas' => Yii::t('fe', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('fe', 'No identitas pasien'),
            'jeniskelamin' => Yii::t('fe', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('fe', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('fe', 'Tanggal lahir'),
            'alamat_pasien' => Yii::t('fe', 'Alamat pasien'),
            'rt' => Yii::t('fe', 'Rt'),
            'rw' => Yii::t('fe', 'Rw'),
            'kelurahan_id' => Yii::t('fe', 'Kelurahan'),
            'kelurahan_nama' => Yii::t('fe', 'Nama kelurahan'),
            'kecamatan_id' => Yii::t('fe', 'Kecamatan'),
            'kecamatan_nama' => Yii::t('fe', 'Nama kecamatan'),
            'kabupaten_id' => Yii::t('fe', 'Kabupaten'),
            'kabupaten_nama' => Yii::t('fe', 'Nama kabupaten'),
            'propinsi_id' => Yii::t('fe', 'Propinsi'),
            'propinsi_nama' => Yii::t('fe', 'Nama propinsi'),
            'statusperkawinan' => Yii::t('fe', 'Status perkawinan'),
            'agama' => Yii::t('fe', 'Agama'),
            'golongandarah' => Yii::t('fe', 'Golongan darah'),
            'rhesus' => Yii::t('fe', 'Rhesus'),
            'anakke' => Yii::t('fe', 'Anakke'),
            'jumlah_bersaudara' => Yii::t('fe', 'Jumlah bersaudara'),
            'no_telepon_pasien' => Yii::t('fe', 'No telepon pasien'),
            'no_mobile_pasien' => Yii::t('fe', 'No mobile pasien'),
            'warga_negara' => Yii::t('fe', 'Warga negara'),
            'photopasien' => Yii::t('fe', 'Photo pasien'),
            'alamatemail' => Yii::t('fe', 'Alamat email'),
            'statusrekammedis' => Yii::t('fe', 'Status rekam medis'),
            'tgl_meninggal' => Yii::t('fe', 'Tanggal meninggal'),
            'nama_ibu' => Yii::t('fe', 'Nama ibu'),
            'nama_ayah' => Yii::t('fe', 'Nama ayah'),
            'petugasadministrasi_id' => Yii::t('fe', 'Petugas administrasi'),
            'petugasadministrasi_gelardepan' => Yii::t('fe', 'Gelar depan petugas administrasi'),
            'petugasadministrasi_nama' => Yii::t('fe', 'Nama petugas administrasi'),
            'petugasadministrasi_gelarbelakang' => Yii::t('fe', 'Gelar belakang petugas administrasi'),
            'dokterpendaftaran_id' => Yii::t('fe', 'Dokter pendaftaran'),
            'dokterpendaftaran_gelardepan' => Yii::t('fe', 'Gelardepan dokter pendaftaran'),
            'dokterpendaftaran_nama' => Yii::t('fe', 'Nama dokter pendaftaran'),
            'dokterpendaftaran_gelarbelakang' => Yii::t('fe', 'Gelar belakang dokter pendaftaran'),
            'dokteradmisi_id' => Yii::t('fe', 'Dokter admisi'),
            'dokteradmisi_gelardepan' => Yii::t('fe', 'Gelar depan dokter admisi'),
            'dokteradmisi_nama' => Yii::t('fe', 'Nama dokter admisi'),
            'dokteradmisi_gelarbelakang' => Yii::t('fe', 'Gelar belakang dokter admisi'),
            'instalasi_id' => Yii::t('fe', 'Instalasi'),
            'tgl_pendaftaran' => Yii::t('fe', 'Tanggal pendaftaran'),
            'tgl_admisi' => Yii::t('fe', 'Tanggal admisi'),
            'pembklaimdetail_id' => Yii::t('fe', 'Pemb klaim detail'),
            'piutangasuransi_id' => Yii::t('fe', 'Piutang asuransi'),
            'kelaspelayanan_nama' => Yii::t('fe', 'Kelas pelayanan'),
        ];
    }
}
