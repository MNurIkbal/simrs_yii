<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "batalorderpenunjang_t".
 *
 * @property int $batalorderpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $no_batalorder
 * @property string $tgl_batalorder
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
class BatalOrderPenunjangForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batalorderpenunjang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienkirimkeunitlain_id'], 'required'],
            [['pasienkirimkeunitlain_id', 'peg_menyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienkirimkeunitlain_id', 'peg_menyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_batalorder', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alasan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_batalorder'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'batalorderpenunjang_id' => 'Batalorderpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'no_batalorder' => 'No Batalorder',
            'tgl_batalorder' => 'Tgl Batalorder',
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
