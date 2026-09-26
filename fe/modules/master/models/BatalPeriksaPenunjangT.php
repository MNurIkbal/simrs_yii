<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "batalperiksapenunjang_t".
 *
 * @property int $batalperiksapenunjang_id
 * @property int $pasienmasukpenunjang_id
 * @property string $no_batalperiksa
 * @property string $tgl_batalperiksa
 * @property int $peg_menyetujui_id
 * @property string $alasan
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
class BatalPeriksaPenunjangT extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batalperiksapenunjang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batalperiksapenunjang_id', 'pasienmasukpenunjang_id'], 'required'],
            [['batalperiksapenunjang_id', 'pasienmasukpenunjang_id', 'peg_menyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['batalperiksapenunjang_id', 'pasienmasukpenunjang_id', 'peg_menyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_batalperiksa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alasan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_batalperiksa'], 'string', 'max' => 255],
            [['batalperiksapenunjang_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'batalperiksapenunjang_id' => 'Batalperiksapenunjang ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'no_batalperiksa' => 'No Batalperiksa',
            'tgl_batalperiksa' => 'Tgl Batalperiksa',
            'peg_menyetujui_id' => 'Peg Menyetujui ID',
            'alasan' => 'Alasan',
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
