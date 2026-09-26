<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfiggudang_k".
 *
 * @property int $konfiggudang_id
 * @property string $tglberlaku
 * @property double $persenppn
 * @property double $persenpph
 * @property bool $bayarlangsung
 * @property string $pesandistruk
 * @property string $pesandifaktur
 * @property string $hargaygdigunakan
 * @property double $pembulatanharga
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
 */
class KonfigGudang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfiggudang_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['konfiggudang_id', 'tglberlaku'], 'required'],
            [['konfiggudang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['konfiggudang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglberlaku', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['persenppn', 'persenpph', 'pembulatanharga', 'administrasi', 'persjualbebas', 'persdiskpasien', 'persenmargin', 'persenppnjual', 'nilai_vital', 'nilai_esensial', 'nilai_nonesensial', 'nilai_a_persen', 'nilai_b_persen', 'nilai_c_persen'], 'number'],
            [['bayarlangsung', 'hargajualglobal', 'otomatismargin', 'is_deleted', 'is_active'], 'boolean'],
            [['pesandistruk', 'pesandifaktur', 'additional_data'], 'string'],
            [['hargaygdigunakan'], 'string', 'max' => 50],
            [['metodeantrian'], 'string', 'max' => 200],
            [['konfiggudang_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfiggudang_id' => 'Konfiggudang ID',
            'tglberlaku' => 'Tglberlaku',
            'persenppn' => 'Persenppn',
            'persenpph' => 'Persenpph',
            'bayarlangsung' => 'Bayarlangsung',
            'pesandistruk' => 'Pesandistruk',
            'pesandifaktur' => 'Pesandifaktur',
            'hargaygdigunakan' => 'Hargaygdigunakan',
            'pembulatanharga' => 'Pembulatanharga',
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
        ];
    }
}
