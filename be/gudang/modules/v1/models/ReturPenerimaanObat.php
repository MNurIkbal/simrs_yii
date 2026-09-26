<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "returpenerimaanobat_t".
 *
 * @property int $returpenerimaanobat_id
 * @property int $panerimaanobatsupp_id
 * @property string $no_returpenerimaanobat
 * @property string $tgl_retur
 * @property int $pegawairetur_id
 * @property string $alasan_retur
 * @property int $ruanganretur_id
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
class ReturPenerimaanObat extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'returpenerimaanobat_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panerimaanobatsupp_id', 'pegawairetur_id', 'ruanganretur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['panerimaanobatsupp_id', 'pegawairetur_id', 'ruanganretur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_retur', 'created_date', 'last_modified_date', 'deleted_date','returpenerimaanobat_id'], 'safe'],
            [['alasan_retur', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_returpenerimaanobat'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'returpenerimaanobat_id' => 'Returpenerimaanobat ID',
            'panerimaanobatsupp_id' => 'Panerimaanobatsupp ID',
            'no_returpenerimaanobat' => 'No Returpenerimaanobat',
            'tgl_retur' => 'Tgl Retur',
            'pegawairetur_id' => 'Pegawairetur ID',
            'alasan_retur' => 'Alasan Retur',
            'ruanganretur_id' => 'Ruanganretur ID',
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
