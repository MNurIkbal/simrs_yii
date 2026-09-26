<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use yii\helpers\ArrayHelper;

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
 * @property integer $no_pembayaran
 */
class InfoPasienSudahBayarView extends \Integrasi\Components\ActiveRepositories
{
    const DATERANGE = 'daterange';
    const NUMBER = 'number';
    const STRING_TYPE = 'string';
    const DATE_FORMAT = 'd M Y';

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
            [['tglinvoice', 'tglbuktibayar', 'tgl_selesaiperiksa', 'tanggal_lahir', 'tgl_meninggal', 'tgl_pendaftaran', 'tgl_admisi', 'no_pembayaran'], 'safe'],
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
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            [['pembayaranpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayaranPelayanan::className(), 'targetAttribute' => ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']],
            [['tandabuktibayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiBayar::className(), 'targetAttribute' => ['tandabuktibayar_id' => 'tandabuktibayar_id']],
            [['returbayarpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturBayarPelayanan::className(), 'targetAttribute' => ['returbayarpelayanan_id' => 'returbayarpelayanan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembayaranpelayanan_id' => Yii::t('app', 'Pembayaran pelayanan'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran'),
            'pasienadmisi_id' => Yii::t('app', 'Pasien admisi'),
            'tglinvoice' => Yii::t('app', 'Tanggal invoice'),
            'tglbuktibayar' => Yii::t('app', 'Tanggal bukti bayar'),
            'nobuktibayar' => Yii::t('app', 'No bukti bayar'),
            'instalasi' => Yii::t('app', 'Nama instalasi'),
            'no_pendaftaran' => Yii::t('app', 'No pendaftaran'),
            'no_rekam_medik' => Yii::t('app', 'No rekam medik'),
            'nama_pasien' => Yii::t('app', 'Nama pasien'),
            'nama_bin' => Yii::t('app', 'Nama bin'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'penjamin_nama' => Yii::t('app', 'Nama penjamin'),
            'total_biayapelayanan' => Yii::t('app', 'Total biaya pelayanan'),
            'totalsubsidiasuransi' => Yii::t('app', 'Total subsidi asuransi'),
            'total_subsidipemerintah' => Yii::t('app', 'Total subsidi pemerintah'),
            'total_subsidirs' => Yii::t('app', 'Total subsidi rs'),
            'total_iurbiaya' => Yii::t('app', 'Total iur biaya'),
            'total_discount' => Yii::t('app', 'Total discount'),
            'total_pembebasan' => Yii::t('app', 'Total pembebasan'),
            'total_bayartindakan' => Yii::t('app', 'Total bayar tindakan'),
            'tandabuktibayar_id' => Yii::t('app', 'Tanda bukti bayar'),
            'returbayarpelayanan_id' => Yii::t('app', 'Retur bayar pelayanan'),
            'closingkasir_id' => Yii::t('app', 'Closing kasir'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangankasir_id' => Yii::t('app', 'Ruangan kasir'),
            'ruangankasir_nama' => Yii::t('app', 'Nama ruangan kasir'),
            'tgl_selesaiperiksa' => Yii::t('app', 'Tanggal selesai periksa'),
            'jenisidentitas' => Yii::t('app', 'Jenis identitas'),
            'no_identitas_pasien' => Yii::t('app', 'No identitas pasien'),
            'jeniskelamin' => Yii::t('app', 'Jenis kelamin'),
            'tempat_lahir' => Yii::t('app', 'Tempat lahir'),
            'tanggal_lahir' => Yii::t('app', 'Tanggal lahir'),
            'alamat_pasien' => Yii::t('app', 'Alamat pasien'),
            'rt' => Yii::t('app', 'Rt'),
            'rw' => Yii::t('app', 'Rw'),
            'kelurahan_id' => Yii::t('app', 'Kelurahan'),
            'kelurahan_nama' => Yii::t('app', 'Nama kelurahan'),
            'kecamatan_id' => Yii::t('app', 'Kecamatan'),
            'kecamatan_nama' => Yii::t('app', 'Nama kecamatan'),
            'kabupaten_id' => Yii::t('app', 'Kabupaten'),
            'kabupaten_nama' => Yii::t('app', 'Nama kabupaten'),
            'propinsi_id' => Yii::t('app', 'Propinsi'),
            'propinsi_nama' => Yii::t('app', 'Nama propinsi'),
            'statusperkawinan' => Yii::t('app', 'Status perkawinan'),
            'agama' => Yii::t('app', 'Agama'),
            'golongandarah' => Yii::t('app', 'Golongan darah'),
            'rhesus' => Yii::t('app', 'Rhesus'),
            'anakke' => Yii::t('app', 'Anakke'),
            'jumlah_bersaudara' => Yii::t('app', 'Jumlah bersaudara'),
            'no_telepon_pasien' => Yii::t('app', 'No telepon pasien'),
            'no_mobile_pasien' => Yii::t('app', 'No mobile pasien'),
            'warga_negara' => Yii::t('app', 'Warga negara'),
            'photopasien' => Yii::t('app', 'Photo pasien'),
            'alamatemail' => Yii::t('app', 'Alamat email'),
            'statusrekammedis' => Yii::t('app', 'Status rekam medis'),
            'tgl_meninggal' => Yii::t('app', 'Tanggal meninggal'),
            'nama_ibu' => Yii::t('app', 'Nama ibu'),
            'nama_ayah' => Yii::t('app', 'Nama ayah'),
            'petugasadministrasi_id' => Yii::t('app', 'Petugas administrasi'),
            'petugasadministrasi_gelardepan' => Yii::t('app', 'Gelar depan petugas administrasi'),
            'petugasadministrasi_nama' => Yii::t('app', 'Nama petugas administrasi'),
            'petugasadministrasi_gelarbelakang' => Yii::t('app', 'Gelar belakang petugas administrasi'),
            'dokterpendaftaran_id' => Yii::t('app', 'Dokter pendaftaran'),
            'dokterpendaftaran_gelardepan' => Yii::t('app', 'Gelardepan dokter pendaftaran'),
            'dokterpendaftaran_nama' => Yii::t('app', 'Nama dokter pendaftaran'),
            'dokterpendaftaran_gelarbelakang' => Yii::t('app', 'Gelar belakang dokter pendaftaran'),
            'dokteradmisi_id' => Yii::t('app', 'Dokter admisi'),
            'dokteradmisi_gelardepan' => Yii::t('app', 'Gelar depan dokter admisi'),
            'dokteradmisi_nama' => Yii::t('app', 'Nama dokter admisi'),
            'dokteradmisi_gelarbelakang' => Yii::t('app', 'Gelar belakang dokter admisi'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'tgl_pendaftaran' => Yii::t('app', 'Tanggal pendaftaran'),
            'tgl_admisi' => Yii::t('app', 'Tanggal admisi'),
            'pembklaimdetail_id' => Yii::t('app', 'Pemb klaim detail'),
            'piutangasuransi_id' => Yii::t('app', 'Piutang asuransi'),
            'kelaspelayanan_nama' => Yii::t('app', 'Nama kelas pelayanan'),
            'no_pembayaran' => Yii::t('app', 'No pembayaran'),
        ];
    }

    public static function primaryKey()
    {
        return ['pembayaranpelayanan_id'];
    }
}
