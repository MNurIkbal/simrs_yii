<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "gantidokterpj_t".
 *
 * @property int $gantidokterpj_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property int $dokterlama_id
 * @property int $jenis_dokter lookup_type='jenis_dokter' (SET lookup_id)
 * @property int $dokterbaru_id
 * @property string $tgl_perubahan
 * @property string $catatan
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
class GantiDokterPj extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gantidokterpj_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'dokterlama_id', 'jenis_dokter', 'dokterbaru_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'dokterlama_id', 'jenis_dokter', 'dokterbaru_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_perubahan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['catatan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gantidokterpj_id' => Yii::t('app', 'Gantidokterpj ID'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'),
            'pasienadmisi_id' => Yii::t('app', 'Pasienadmisi ID'),
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
            'dokterlama_id' => Yii::t('app', 'Dokterlama ID'),
            'jenis_dokter' => Yii::t('app', 'Jenis Dokter'),
            'dokterbaru_id' => Yii::t('app', 'Dokterbaru ID'),
            'tgl_perubahan' => Yii::t('app', 'Tgl Perubahan'),
            'catatan' => Yii::t('app', 'Catatan'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
}
