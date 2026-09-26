<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sy_adjusment".
 *
 * @property int $adjusment_id
 * @property string $no_pendaftaran
 * @property string $no_buktiadjust
 * @property string $tgl_adjust
 * @property string $no_buktitrans
 * @property double $total
 * @property double $selisih
 * @property double $sisa
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
class SyKunjunganAdjusmentHeader extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_adjusment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['sisa', 'selisih', 'total'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'tgl_adjust', 'no_buktiadjust'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pendaftaran', 'no_buktiadjust', 'no_buktitrans'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusment_id' => 'Adjusment ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_buktiadjust' => 'No Bukti Adj',
            'sisa' => 'Sisa',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'selisih' => 'Selisih',
            'no_buktitrans' => 'No Bukti Transaksi',
            'tgl_adjust' => 'Tgl Adjusment',
            'total' => 'Total',
            'kode_adjust' => 'Kode Adjusment',
        ];
    }
}
