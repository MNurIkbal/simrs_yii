<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klaiminacbg_t".
 *
 * @property int $klaiminacbg_id
 * @property int $klaimgroup_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $instalasi_id
 * @property int $pasien_id
 * @property string $tgl_masuk
 * @property string $tgl_keluar
 * @property int $los
 * @property string $adl_subacute
 * @property string $adl_cronic
 * @property int $dokterdpjp_id
 * @property double $total_tarifrs
 * @property string $no_sep
 * @property string $jenis_kelasrawat
 * @property string $umur
 * @property double $berat_lahir
 * @property string $carapulang_id
 * @property string $tarif
 * @property double $prosedur_bedah
 * @property double $prosedur_nonbedah
 * @property double $konsultasi
 * @property double $tenaga_ahli
 * @property double $keperawatan
 * @property double $penunjang
 * @property double $radiologi
 * @property double $laboratorium
 * @property double $pelayanan_darah
 * @property double $rehabilitasi
 * @property double $kamar_akomodasi
 * @property double $rawat_intensif
 * @property double $obat
 * @property double $alkes
 * @property double $bmhp
 * @property double $sewa_alat
 * @property string $diagnosa_primer
 * @property string $diagnosa_sekunder
 * @property string $no_rekam_medik
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $nama_pasien
 * @property int $nama_dokter
 * @property int $obat_kronis
 * @property int $nama_kemoterapi
 * @property int $deleted_by
 * @property int $klaim_penjamin
 * @property string $status_covid
 * @property bool $is_komplikasi
 * @property bool $is_pemulasaranjenazah;
 * @property bool $is_kantongjenazah;
 * @property bool $is_petijenazah;
 * @property bool $is_plastikerat;
 * @property bool $is_desinfektanjenazah;
 * @property bool $is_transport;
 * @property bool $is_desinfektanmobil;
 * @property int $total_episodedijamin
 */
class KlaimInacbg extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'klaiminacbg_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['klaimgroup_id', 'pendaftaran_id', 'pasienadmisi_id', 'instalasi_id', 'pasien_id', 'los', 'dokterdpjp_id', 'berat_lahir', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['klaimgroup_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'los', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'klaim_penjamin'], 'integer'],
            [['tgl_masuk', 'tgl_keluar', 'created_date', 'last_modified_date', 'deleted_date', 'is_naikkelas','ventilator','naik_kelas','klaiminacbg_id', 'tarif_polieksekutif', 'is_rawatintensif', 'instalasi_id', 'dokterdpjp_id', 'no_rekam_medik', 'berat_lahir', 'klaim_penjamin', 'status_covid', 'is_pemulasaranjenazah', 'is_kantongjenazah', 'is_petijenazah', 'is_plastikerat', 'is_desinfektanjenazah', 'is_transport', 'is_desinfektanmobil', 'total_episodedijamin'], 'safe'],
            [['total_tarifrs', 'prosedur_bedah', 'prosedur_nonbedah', 'konsultasi', 'tenaga_ahli', 'keperawatan', 'penunjang', 'radiologi', 'laboratorium', 'pelayanan_darah', 'rehabilitasi', 'kamar_akomodasi', 'rawat_intensif', 'obat', 'alkes', 'bmhp', 'sewa_alat', 'obat_kronis', 'obat_kemoterapi'], 'number'],
            [['diagnosa_primer', 'diagnosa_sekunder', 'additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_terkirim', 'is_komplikasi', 'is_pemulasaranjenazah', 'is_kantongjenazah', 'is_petijenazah', 'is_plastikerat', 'is_desinfektanjenazah', 'is_transport', 'is_desinfektanmobil'], 'boolean'],
            [['adl_subacute', 'adl_cronic', 'jenis_kelasrawat', 'umur', 'carapulang_id', 'tarif', 'status_covid'], 'string', 'max' => 100],
            [['no_sep', 'nama_pasien', 'nama_dokter'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'klaimgroup_id' => 'Klaimgroup ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'instalasi_id' => 'Instalasi ID',
            'pasien_id' => 'Pasien ID',
            'tgl_masuk' => 'Tgl Masuk',
            'tgl_keluar' => 'Tgl Keluar',
            'los' => 'Los',
            'adl_subacute' => 'Adl Subacute',
            'adl_cronic' => 'Adl Cronic',
            'dokterdpjp_id' => 'Dokterdpjp ID',
            'total_tarifrs' => 'Total Tarifrs',
            'no_sep' => 'No Sep',
            'jenis_kelasrawat' => 'Jenis Kelasrawat',
            'umur' => 'Umur',
            'berat_lahir' => 'Berat Lahir',
            'carapulang_id' => 'Carapulang ID',
            'tarif' => 'Tarif',
            'prosedur_bedah' => 'Prosedur Bedah',
            'prosedur_nonbedah' => 'Prosedur Nonbedah',
            'konsultasi' => 'Konsultasi',
            'tenaga_ahli' => 'Tenaga Ahli',
            'keperawatan' => 'Keperawatan',
            'penunjang' => 'Penunjang',
            'radiologi' => 'Radiologi',
            'laboratorium' => 'Laboratorium',
            'pelayanan_darah' => 'Pelayanan Darah',
            'rehabilitasi' => 'Rehabilitasi',
            'kamar_akomodasi' => 'Kamar Akomodasi',
            'rawat_intensif' => 'Rawat Intensif',
            'obat' => 'Obat',
            'alkes' => 'Alkes',
            'bmhp' => 'Bmhp',
            'sewa_alat' => 'Sewa Alat',
            'diagnosa_primer' => 'Diagnosa Primer',
            'diagnosa_sekunder' => 'Diagnosa Sekunder',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'nama_pasien' => 'Nama Pasien',
            'nama_dokter' => 'Nama Dokter',
            'obat_kronis' => 'Obat Kronis',
            'obat_kemoterapi' => 'Obat Kemoterapi',
            'klaim_penjamin' => 'Penjamin / Cara Bayar',
            'status_covid' => 'Status Pasien COVID-19',
            'is_komplikasi' => 'Komorbid / Komplikasi',
            'is_pemulasaranjenazah' => 'Pemulasaran Jenazah',
            'is_kantongjenazah' => 'Kantong Jenazah',
            'is_petijenazah' => 'Peti Jenazah',
            'is_plastikerat' => 'Pelastik Erat',
            'is_desinfektanjenazah' => 'Desinfektan Jenazah',
            'is_transport' => 'Transport',
            'is_desinfektanmobil' => 'Desinfektan Mobil'
        ];
    }
}
