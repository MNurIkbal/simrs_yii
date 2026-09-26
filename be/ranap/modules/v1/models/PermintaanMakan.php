<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "permintaanmakan_t".
 *
 * @property int $permintaaanmakan_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_permintaanmakan
 * @property string $tgl_permintaanmakan
 * @property int $peg_pemesan_id
 * @property int $status 0=Batal, 1=Proses
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
class PermintaanMakan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permintaanmakan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'peg_pemesan_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'peg_pemesan_id', 'status', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'peg_pemesan_id', 'status', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_permintaanmakan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_permintaanmakan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaaanmakan_id' => 'Permintaaanmakan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_permintaanmakan' => 'No Permintaanmakan',
            'tgl_permintaanmakan' => 'Tgl Permintaanmakan',
            'peg_pemesan_id' => 'Peg Pemesan ID',
            'status' => 'Status',
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
