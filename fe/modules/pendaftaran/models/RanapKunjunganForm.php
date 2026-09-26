<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * Validation model for kunjungan form.
 *
 * @property int $pasienadmisi_id
 * @property int $shift_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $pasien_id
 * @property int $caramasuk_id
 * @property int $ruangan_id
 * @property int $pasienpulang_id
 * @property int $bookingkamar_id
 * @property int $pembayaranpelayanan_id
 * @property int $pendaftaran_id
 * @property int $kamarruangan_id
 * @property int $kelaspelayanan_id
 * @property int $pegawai_id
 * @property string $tgl_admisi
 * @property string $tgl_pendaftaran
 * @property string $tgl_pulang
 * @property string $kunjungan
 * @property bool $status_keluar
 * @property bool $rawat_gabung
 * @property string $rencana_pulang
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
 * @property int $kamartempattidur_id
 *
 * @property AnamnesaT[] $anamnesaTs
 * @property AnamnesadietT[] $anamnesadietTs
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property BookingkamarT[] $bookingkamarTs
 * @property DietpasienT[] $dietpasienTs
 * @property HasilpemeriksaanlabT[] $hasilpemeriksaanlabTs
 * @property HasilpemeriksaanradT[] $hasilpemeriksaanradTs
 * @property HasilpemeriksaanrmT[] $hasilpemeriksaanrmTs
 * @property MasukkamarT[] $masukkamarTs
 * @property BookingkamarT $bookingkamar
 * @property CarabayarM $carabayar
 * @property CaramasukM $caramasuk
 * @property KamarruanganM $kamarruangan
 * @property KelaspelayananM $kelaspelayanan
 * @property PasienM $pasien
 * @property PasienpulangT $pasienpulang
 * @property PegawaiM $pegawai
 * @property PembayaranpelayananT $pembayaranpelayanan
 * @property PendaftaranT $pendaftaran
 * @property PenjaminM $penjamin
 * @property RuanganM $ruangan
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PasienpulangT[] $pasienpulangTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property RencanaoperasiT[] $rencanaoperasiTs
 * @property ReturresepT[] $returresepTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class RanapKunjunganForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $pasien_id;
    public $caramasuk_id;
    public $ruangan_id;
    public $shift_id;
    public $carabayar_id;
    public $penjamin_id;
    public $pasienpulang_id;
    public $bookingkamar_id;
    public $pembayaranpelayanan_id;
    public $kamarruangan_id;
    public $kelaspelayanan_id;
    public $pegawai_id;
    public $tgl_admisi;
    public $tgl_pendaftaran;
    public $tgl_pulang;
    public $kunjungan;
    public $status_keluar;
    public $rawat_gabung;
    public $rencana_pulang;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_active;
    public $is_deleted;
    public $deleted_date;
    public $deleted_by;
    public $kamartempattidur_id;
    public $kamartempattidur_id_label;
    public $jeniskasuspenyakit_id;
    public $asalrujukan_id;

    public $bookingkamar_no;
    public $kamarruangan_nokamar;
    public $keterangan;

    public $asuransipasien_id;
    public $bpjs_id;
    public $kelahiranbayi_id;

    // penanggungjawab
    public $pj_pengantar;
    public $pj_nama;
    public $pj_jk;
    public $pj_jenis_identitas;
    public $pj_no_identitas;
    public $pj_hubungan;
    public $pj_tempat_lahir;
    public $pj_tanggal_lahir;
    public $pj_umur;
    public $pj_alamat;
    public $pj_no_telepon;
    public $instalasi_id;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasienadmisi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 
                    'caramasuk_id', 'ruangan_id', 'pasienpulang_id', 
                    'bookingkamar_id', 'pembayaranpelayanan_id', 
                    'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 
                    'pegawai_id', 'created_by', 'modified_count', 
                    'last_modified_by', 'deleted_by', 'kamartempattidur_id'
                ], 
                'default', 
                'value' => null
            ],
            [['shift_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'caramasuk_id', /*'ruangan_id',*/ 'pasienpulang_id', 'bookingkamar_id', 'pembayaranpelayanan_id', 'pendaftaran_id', 'kamarruangan_id', 'kelaspelayanan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamartempattidur_id'], 'integer'],
            [
                [
                    'carabayar_id', 'penjamin_id', 'pasien_id','pegawai_id', 'ruangan_id', 
                    'pendaftaran_id', 'tgl_admisi', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'kamarruangan_nokamar',
                    'asalrujukan_id',
                    'pj_pengantar', 'pj_nama', 'pj_jk',
                ], 
                'required', 'on' => 'with_mandatory_all',
            ],
            [
                [
                    'carabayar_id', 'penjamin_id', 'pasien_id','pegawai_id', 'ruangan_id', 
                    'pendaftaran_id', 'tgl_admisi', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'kamarruangan_nokamar',
                    'asalrujukan_id',
                ], 
                'required', 'on' => 'with_mandatory_pjawab',
            ],
            [
                [
                    'carabayar_id', 'penjamin_id', 'pegawai_id', 'ruangan_id', 
                    'pendaftaran_id', 'tgl_admisi', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'kamarruangan_nokamar',
                    'asalrujukan_id',
                ], 
                'required', 'on' => 'with_mandatory_pjawab_bayi',
            ],
            [
                [
                    'tgl_admisi', 'tgl_pendaftaran', 'tgl_pulang', 'rencana_pulang', 'created_date', 'last_modified_date', 
                    'deleted_date','carabayar_id', 'keterangan', 'caramasuk_id',
                    'pj_jenis_identitas', 'pj_no_identitas', 'pj_hubungan', 'pj_tempat_lahir',
                    'pj_tanggal_lahir', 'pj_umur', 'pj_alamat', 'pj_no_telepon', 'kelahiranbayi_id'
                ], 
                'safe'
            ],
            [['status_keluar', 'rawat_gabung', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['kunjungan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienadmisi_id' => Yii::t('fe', 'Pasienadmisi ID'),
            'shift_id' => Yii::t('fe', 'Shift ID'),
            'carabayar_id' => Yii::t('fe', 'Cara bayar'),
            'penjamin_id' => Yii::t('fe', 'Penjamin'),
            'pasien_id' => Yii::t('fe', 'Pasien'),
            'caramasuk_id' => Yii::t('fe', 'Cara masuk'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'pasienpulang_id' => Yii::t('fe', 'Pasien pulang'),
            'bookingkamar_id' => Yii::t('fe', 'Booking kamar'),
            'pembayaranpelayanan_id' => Yii::t('fe', 'Pembayaran pelayanan'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran'),
            'kamarruangan_id' => Yii::t('fe', 'Kamar ruangan'),
            'kelaspelayanan_id' => Yii::t('fe', 'Kelas pelayanan'),
            'pegawai_id' => Yii::t('fe', 'Pegawai'),
            'tgl_admisi' => Yii::t('fe', 'Tgl admisi'),
            'tgl_pendaftaran' => Yii::t('fe', 'Tgl pendaftaran'),
            'tgl_pulang' => Yii::t('fe', 'Tgl Pulang'),
            'kunjungan' => Yii::t('fe', 'Kunjungan'),
            'status_keluar' => Yii::t('fe', 'Status Keluar'),
            'rawat_gabung' => Yii::t('fe', 'Rawat Gabung'),
            'rencana_pulang' => Yii::t('fe', 'Rencana Pulang'),
            'additional_data' => Yii::t('fe', 'Additional Data'),
            'created_date' => Yii::t('fe', 'Created Date'),
            'created_by' => Yii::t('fe', 'Created By'),
            'modified_count' => Yii::t('fe', 'Modified Count'),
            'last_modified_date' => Yii::t('fe', 'Last Modified Date'),
            'last_modified_by' => Yii::t('fe', 'Last Modified By'),
            'is_deleted' => Yii::t('fe', 'Is Deleted'),
            'is_active' => Yii::t('fe', 'Is Active'),
            'deleted_date' => Yii::t('fe', 'Deleted Date'),
            'deleted_by' => Yii::t('fe', 'Deleted By'),
            'kamartempattidur_id' => Yii::t('fe', 'Kamar tempat tidur'),
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Jenis kasus penyakit'),
            'asalrujukan_id' => Yii::t('fe', 'Asal rujukan'),
            'kamarruangan_nokamar' => Yii::t('fe', 'No tempat tidur'),
            'bookingkamar_no' => Yii::t('fe', 'No pesanan kamar'),
            'asalrujukan_id' => Yii::t('fe', 'Asal rujukan'),
            'keterangan' => Yii::t('fe', 'Keterangan pendaftaran'),

            'pj_pengantar' => \Yii::t('fe', 'Pengantar'),
            'pj_nama' => \Yii::t('fe', 'Nama'),
            'pj_jk' => \Yii::t('fe', 'Jenis kelamin'),
            'pj_jenis_identitas' => \Yii::t('fe', 'Jenis identitas'),
            'pj_no_identitas' => \Yii::t('fe', 'No identitas'),
            'pj_hubungan' => \Yii::t('fe', 'Hubungan'),
            'pj_tempat_lahir' => \Yii::t('fe', 'Tempat lahir'),
            'pj_tanggal_lahir'=> \Yii::t('fe', 'Tanggal lahir'),
            'pj_umur'=> \Yii::t('fe', 'Umur'),
            'pj_alamat'=> \Yii::t('fe', 'Alamat'),
            'pj_no_telepon'=> \Yii::t('fe', 'No telepon'),
        ];
    }

}
