<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sensuspasienranap_r".
 *
 * @property int $id
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
 * @property string $tgl_sensus
 * @property int $pasien_awal
 * @property int $pasien_masuk
 * @property int $pasien_pindahan
 * @property int $pasien_keluarhidup
 * @property int $pasien_keluardipindahkan
 * @property int $pasien_keluarmeninggalkur48
 * @property int $pasien_keluarmeninggalleb48
 * @property int $pasien_akhir
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
class SensuspasienranapR extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sensuspasienranap_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'kelaspelayanan_id', 'pasien_awal', 'pasien_masuk', 'pasien_pindahan', 'pasien_keluarhidup', 'pasien_keluardipindahkan', 'pasien_keluarmeninggalkur48', 'pasien_keluarmeninggalleb48', 'pasien_akhir', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'pasien_awal', 'pasien_masuk', 'pasien_pindahan', 'pasien_keluarhidup', 'pasien_keluardipindahkan', 'pasien_keluarmeninggalkur48', 'pasien_keluarmeninggalleb48', 'pasien_akhir', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_sensus', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ruangan_id' => 'Ruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'tgl_sensus' => 'Tgl Sensus',
            'pasien_awal' => 'Pasien Awal',
            'pasien_masuk' => 'Pasien Masuk',
            'pasien_pindahan' => 'Pasien Pindahan',
            'pasien_keluarhidup' => 'Pasien Keluarhidup',
            'pasien_keluardipindahkan' => 'Pasien Keluardipindahkan',
            'pasien_keluarmeninggalkur48' => 'Pasien Keluarmeninggalkur48',
            'pasien_keluarmeninggalleb48' => 'Pasien Keluarmeninggalleb48',
            'pasien_akhir' => 'Pasien Akhir',
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