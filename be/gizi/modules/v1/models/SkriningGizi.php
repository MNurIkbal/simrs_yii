<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "skrininggizi_t".
 *
 * @property int $skrininggizi_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $bb_ygdirencanakan 0=tidak terjadi, 1=tidak yakin, 2=Ya,Turun
 * @property int $bb_turun lookup_type='bb_turun'
 * @property int $porsi_makan lookup_type='sgizi_bb_turun'
 * @property int $sakit_berat lookup_type='sgizi_sakitberat'
 * @property int $skor
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
class SkriningGizi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skrininggizi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'bb_ygdirencanakan', 'bb_turun', 'porsi_makan', 'sakit_berat', 'skor', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'bb_ygdirencanakan', 'bb_turun', 'porsi_makan', 'sakit_berat', 'skor', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'skrininggizi_id' => 'Skrininggizi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'bb_ygdirencanakan' => 'Bb Ygdirencanakan',
            'bb_turun' => 'Bb Turun',
            'porsi_makan' => 'Porsi Makan',
            'sakit_berat' => 'Sakit Berat',
            'skor' => 'Skor',
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
