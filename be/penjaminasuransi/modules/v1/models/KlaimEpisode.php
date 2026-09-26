<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klaimepisode_t".
 *
 * @property int $klaimepisode_id
 * @property int $klaiminacbg_id
 * @property string $episode
 * @property int $jumlah
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
class KlaimEpisode extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'klaimepisode_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['klaiminacbg_id'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['klaiminacbg_id', 'jumlah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['klaiminacbg_id', 'episode', 'jumlah', 'created_date'], 'safe'],
            [['episode', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'klaiminacbg_id' => 'Klaiminacbg ID',
            'episode' => 'Episode',
            'jumlah' => 'Hari',
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
