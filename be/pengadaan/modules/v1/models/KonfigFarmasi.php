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
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglberlaku', 'created_date', 'last_modified_date', 'deleted_date', 'last_modified_by','is_verifpemesanan','is_verifpenerimaan', 'po_expired'], 'safe'],
            [['persenppn', 'persenpph', 'pembulatanharga', 'ri_persjualppn', 'rd_persjualppn', 'rj_persjualppn', 'administrasi', 'persjualbebas', 'persdiskpasien', 'persenmargin', 'nilai_vital', 'nilai_esensial', 'nilai_nonesensial', 'nilai_a_persen', 'nilai_b_persen', 'nilai_c_persen', 'po_expired'], 'number'],
            [['bayarlangsung', 'konfigfarmasi_aktif', 'hargajualglobal', 'otomatismargin', 'is_multireturresep', 'is_deleted', 'is_active'], 'boolean'],
            [['pesandistruk', 'pesandifaktur', 'pesan_etiket', 'additional_data'], 'string'],
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
            'deleted_by' => 'Deleted By',
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
