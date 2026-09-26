<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rencanapulang_t".
 *
 * @property int $rencanapulang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $info_penyakit
 * @property int $lama_perawatan
 * @property string $rencana_pulang
 * @property string $rencana_perawatan
 * @property string $rencana_transportasi
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
class RencanaPulang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rencanapulang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'lama_perawatan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'lama_perawatan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['info_penyakit', 'rencana_perawatan', 'rencana_transportasi', 'additional_data'], 'string'],
            [['rencana_pulang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rencanapulang_id' => 'Rencanapulang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'info_penyakit' => 'Info Penyakit',
            'lama_perawatan' => 'Lama Perawatan',
            'rencana_pulang' => 'Rencana Pulang',
            'rencana_perawatan' => 'Rencana Perawatan',
            'rencana_transportasi' => 'Rencana Transportasi',
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
