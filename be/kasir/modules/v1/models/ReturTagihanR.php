<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "returtagihan_r".
 *
 * @property int $returtagihan_id
 * @property int $pembayaran_id
 * @property string $no_tagihan
 * @property int $pegawaipembayaran_id
 * @property int $pegawairetur_id
 * @property string $tgl_returtagihan
 * @property int $total_returtagihan
 * @property string $keterangan
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
class ReturTagihanR extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'returtagihan_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaran_id', 'no_tagihan', 'pegawairetur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['returtagihan_id', 'pembayaran_id', 'pegawaipembayaran_id', 'pegawairetur_id', 'total_returtagihan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data', 'tgl_returtagihan'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'keterangan', 'no_tagihan', 'tgl_returtagihan', 'pegawaipembayaran_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }
}
