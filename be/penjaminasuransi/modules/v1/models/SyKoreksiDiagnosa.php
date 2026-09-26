<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "koreksidiagnosa_t".
 *
 * @property int $koreksidiagnosa_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $dokterdpjp_id
 * @property string $tgl_koreksidiagnosa
 * @property int $kelompokdiagnosa_id
 * @property int $diagnosa_id
 * @property int $diagnosaasal_id
 * @property string $diag_asal_masuk
 * @property string $diag_asal_utama
 * @property string $diag_asal_penyerta
 * @property string $diag_asal_terapi
 * @property bool $is_inacbg update saat klaim
 * @property bool $is_icdprimer update saat klaim
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
 * @property bool $is_inagrouper
 */
class SyKoreksiDiagnosa extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_koreksidiagnosa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sy_koreksidiagnosa_id', 'kunjungan_id', 'kelompokdiagnosa_id', 'diagnosa_id', 'diagnosaasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['sy_koreksidiagnosa_id', 'kunjungan_id', 'kelompokdiagnosa_id', 'diagnosa_id', 'diagnosaasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_koreksidiagnosa', 'created_date', 'last_modified_date', 'deleted_date', 'multiplicity'], 'safe'],
            [['diag_asal_masuk', 'diag_asal_utama', 'diag_asal_penyerta', 'diag_asal_terapi', 'additional_data'], 'string'],
            [['is_inacbg', 'is_icdprimer', 'is_deleted', 'is_active', 'is_inagrouper', 'is_idrg'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sy_koreksidiagnosa_id' => 'Koreksidiagnosa ID',
            'kunjungan_id' => 'Kunjungan ID',
            'tgl_koreksidiagnosa' => 'Tgl Koreksidiagnosa',
            'kelompokdiagnosa_id' => 'Kelompokdiagnosa ID',
            'diagnosa_id' => 'Diagnosa ID',
            'diagnosaasal_id' => 'Diagnosaasal ID',
            'diag_asal_masuk' => 'Diag Asal Masuk',
            'diag_asal_utama' => 'Diag Asal Utama',
            'diag_asal_penyerta' => 'Diag Asal Penyerta',
            'diag_asal_terapi' => 'Diag Asal Terapi',
            'is_inacbg' => 'Is Inacbg',
            'is_icdprimer' => 'Is Icdprimer',
            'is_inagrouper' => 'Is Inagrouper',
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