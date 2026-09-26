<?php

namespace Doco\models\kasir;

use Yii;
/**
 * This is the model class for table "pembayaran_t".
 *
 * @property int $pembayaran_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property double $total_tagihan
 * @property double $total_dibayar
 * @property double $total_dijamin
 * @property double $total_sisatagihan
 * @property double $total_administrasi
 * @property double $total_pembulatan
 * @property double $total_pembebasan
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
class PembayaranPenjamin extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pembayaranpenjamin_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pembayaranpenjamin_id', 
                'pembayaran_id',
                'created_by', 
                'modified_count', 
                'last_modified_by', 
                'deleted_by',
            ], 'integer'],
            [[
                'total_dijamin', 
            ], 'number'],
            [[
                'created_date', 
                'last_modified_date', 
                'deleted_date', 
                'pembayaran_id',
                'penjamin_nama',
                'no_kartu',
                'total_dijamin',
            ], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pembayaranpenjamin_id' => 'Pembayaran Penjamin ID',
            'pembayaran_id' => 'Pembayaran ID',
            'penjamin_nama' => 'Pembayaran Nama',
            'no_kartu' => 'No Kartu',
            'total_dijamin' => 'Total Dijamin',
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
