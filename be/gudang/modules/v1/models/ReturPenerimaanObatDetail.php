<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "returpenerimaanobatdetail_t".
 *
 * @property int $returpenerimaanobatdetail_id
 * @property int $returpenerimaanobat_id
 * @property int $obatalkes_id
 * @property int $satuanbesar_id
 * @property string $tgl_kadaluarsa
 * @property int $qty_retur
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $penerimaanobat_id
 */
class ReturPenerimaanObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'returpenerimaanobatdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['returpenerimaanobat_id', 'obatalkes_id', 'penerimaanobat_id'], 'required'],
            [['returpenerimaanobat_id', 'obatalkes_id', 'satuanbesar_id', 'qty_retur', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['returpenerimaanobat_id', 'obatalkes_id', 'satuanbesar_id', 'qty_retur', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date','penerimaansuppdetail_id', 'penerimaanobatdetail_id', "qty_input"], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'returpenerimaanobatdetail_id' => 'Returpenerimaanobatdetail ID',
            'returpenerimaanobat_id' => 'Returpenerimaanobat ID',
            'obatalkes_id' => 'Obatalkes ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'tgl_kadaluarsa' => 'Tgl Kadaluarsa',
            'qty_retur' => 'Qty Retur',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'penerimaanobat_id' => 'Penerimaan Obat ID',
        ];
    }
}
