<?php

namespace app\modules\v1\models;

use Yii;

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
 * @property bool $is_multireturresep
 */
class KonfigFarmasi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'konfigfarmasi_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['konfigfarmasi_id', 'tglberlaku'], 'required'],
            [['konfigfarmasi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['konfigfarmasi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglberlaku', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['persenppn', 'persenpph', 'pembulatanharga', 'ri_persjualppn', 'rd_persjualppn', 'rj_persjualppn', 'administrasi', 'persjualbebas', 'persdiskpasien', 'persenmargin', 'persenppnjual', 'nilai_vital', 'nilai_esensial', 'nilai_nonesensial', 'nilai_a_persen', 'nilai_b_persen', 'nilai_c_persen'], 'number'],
            [['bayarlangsung', 'konfigfarmasi_aktif', 'hargajualglobal', 'otomatismargin', 'is_deleted', 'is_active', 'is_multireturresep'], 'boolean'],
            [['pesandistruk', 'pesandifaktur', 'additional_data'], 'string'],
            [['formulajasadokter', 'formulajasaparamedis'], 'string', 'max' => 100],
            [['hargaygdigunakan'], 'string', 'max' => 50],
            [['metodeantrian'], 'string', 'max' => 200],
            [['konfigfarmasi_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
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
            'is_multireturresep' => 'Is Multireturresep',
        ];
    }
}
