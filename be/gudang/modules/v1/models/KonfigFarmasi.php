<?php

namespace app\modules\v1\models;


use Yii;
use app\models\Loginpemakai;
use app\modules\v1\models\PenjaminView;

/**
 * This is the model class for table "konfigfarmasi_k".
 *
 * @property int $konfigfarmasi_id
 * @property string $tglberlaku
 * @property double $persenppn
 * @property double $persenpph
 * @property bool $bayarlangsung
 * @property string $pesandistruk
 * @property string $pesandifaktur
 * @property string $formulajasadokter
 * @property string $formulajasaparamedis
 * @property string $hargaygdigunakan
 * @property double $pembulatanharga
 * @property double $ri_persjualppn
 * @property double $rd_persjualppn
 * @property double $rj_persjualppn
 * @property bool $konfigfarmasi_aktif
 * @property double $administrasi
 * @property double $persjualbebas
 * @property bool $hargajualglobal
 * @property double $persdiskpasien
 * @property bool $otomatismargin
 * @property double $persenmargin
 * @property string $metodeantrian
 * @property double $persenppnjual
 * @property double $nilai_vital
 * @property double $nilai_esensial
 * @property double $nilai_nonesensial
 * @property double $nilai_a_persen
 * @property double $nilai_b_persen
 * @property double $nilai_c_persen
 * @property bool $is_multireturresep
 * @property string $pesan_etiket
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
 * @property int $max_dataso
 * @property int $harga_donasi
 * @property int $batal_pesan_by
 * @property int is_bypassworklist
 * @property int is_large_unit_pr
 * @property int is_fulfilledso
 * @property int auto_validasi_po_manual
 * @property int is_returnstock
 * @property int is_disabledfulfilled_so
 * @property int is_pesanstokobat_0
 * @property int is_others
 * @property int is_freetext
 * @property int is_tgl_implementasi_sesuai_verif
 */
class KonfigFarmasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigfarmasi_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tglberlaku'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'max_dataso'], 'integer'],
            [['tglberlaku', 'created_date', 'last_modified_date', 'deleted_date', 'last_modified_by','is_verifpemesanan','is_verifpenerimaan', 'po_expired', 'max_dataso', 'is_verifstokopname','harga_donasi', 'use_ppn', 'use_discount', 'hari_resep_kronis'], 'safe'],
            [['persenppn', 'persenpph', 'pembulatanharga', 'ri_persjualppn', 'rd_persjualppn', 'rj_persjualppn', 'administrasi', 'persjualbebas', 'persdiskpasien', 'persenmargin', 'nilai_vital', 'nilai_esensial', 'nilai_nonesensial', 'nilai_a_persen', 'nilai_b_persen', 'nilai_c_persen', 'po_expired'], 'number'],
            [['bayarlangsung', 'konfigfarmasi_aktif', 'hargajualglobal', 'otomatismargin', 'is_multireturresep', 'is_deleted', 'is_active', 'use_ppn', 'use_discount', 'enable_split_kronis'], 'boolean'],
            [['pesandistruk', 'pesandifaktur', 'pesan_etiket', 'additional_data'], 'string'],
            [['auto_validasi_po_manual','is_pesanstokobat_0','is_freetext','is_others','batal_pesan_by', 'is_large_unit_pr', 'is_fulfilledso', 'is_fulfilledso','is_disabledfulfilled_so','is_tgl_implementasi_sesuai_verif'],'string'],
            [['formulajasadokter', 'formulajasaparamedis', 'formula_penjualan'], 'string', 'max' => 100],
            [['hargaygdigunakan'], 'string', 'max' => 50],
            [['metodeantrian'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigfarmasi_id' => 'Konfigfarmasi ID',
            'tglberlaku' => 'Tglberlaku',
            'persenppn' => 'Persenppn',
            'persenpph' => 'Persenpph',
            'bayarlangsung' => 'Bayarlangsung',
            'pesandistruk' => 'Pesandistruk',
            'pesandifaktur' => 'Pesandifaktur',
            'formulajasadokter' => 'Formulajasadokter',
            'formulajasaparamedis' => 'Formulajasaparamedis',
            'hargaygdigunakan' => 'Hargaygdigunakan',
            'pembulatanharga' => 'Pembulatanharga',
            'ri_persjualppn' => 'Ri Persjualppn',
            'rd_persjualppn' => 'Rd Persjualppn',
            'rj_persjualppn' => 'Rj Persjualppn',
            'konfigfarmasi_aktif' => 'Konfigfarmasi Aktif',
            'administrasi' => 'Administrasi',
            'persjualbebas' => 'Persjualbebas',
            'hargajualglobal' => 'Hargajualglobal',
            'persdiskpasien' => 'Persdiskpasien',
            'otomatismargin' => 'Otomatismargin',
            'persenmargin' => 'Persenmargin',
            'metodeantrian' => 'Metodeantrian',
            'persenppnjual' => 'Persenppnjual',
            'nilai_vital' => 'Nilai Vital',
            'nilai_esensial' => 'Nilai Esensial',
            'nilai_nonesensial' => 'Nilai Nonesensial',
            'nilai_a_persen' => 'Nilai A Persen',
            'nilai_b_persen' => 'Nilai B Persen',
            'nilai_c_persen' => 'Nilai C Persen',
            'is_multireturresep' => 'Is Multireturresep',
            'pesan_etiket' => 'Pesan Etiket',
            'additional_data' => 'Additional Data',
            'po_expired' => 'PO Expired',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Tanggal Update',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'batal_pesan_by' => 'Batal Pesan',
            'deleted_by' => 'Deleted By',
            'max_dataso' => 'Maksimal Data SO',
            'harga_donasi' => 'Harga Item Donasi',
            'is_bypassworklist' => 'is_bypassworklist',
            'is_large_unit_pr' => 'is_large_unit_pr',
            'is_fulfilledso' => 'is_fulfilledso',
            'auto_validasi_po_manual' => 'auto_validasi_po_manual',
            'is_returnstock' => 'is_returnstock',
            'is_disabledfulfilled_so' => 'is_disabledfulfilled_so',
            'is_tgl_implementasi_sesuai_verif' => 'is_tgl_implementasi_sesuai_verif',
            'is_pesanstokobat_0' => 'is_pesanstokobat_0',
            'is_freetext' => 'is_freetext',
            'is_others' => 'is_others',
            'use_discount' => 'Discount',
            'use_ppn' => 'PPN',
            'enable_split_kronis' => 'Enable Split Resep Kronis',
            'hari_resep_kronis' => 'Jumlah Hari Resep Kronis'
        ];
    }

    public function getEditedBy()
    {
        return $this->hasOne(Loginpemakai::className(), ['loginpemakai_id' => 'last_modified_by' ]);
    }

    public function getPenjamin()
    {
        return $this->hasOne(PenjaminView::className(),['penjamin_id'=>'penjaminkaryawan_id']);
    }
}
