<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;

/**
 * This is the model class for table "terimabayarklaim_t".
 *
 * @property int $terimabayarklaim_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property string $tgl_terimabayarklaim
 * @property string $no_terimabayarklaim
 * @property double $total_terimabayar
 * @property int $pegawaipenerima_id
 * @property string $catatan
 * @property bool $is_nontunai
 * @property string $pemilik_rekening
 * @property int $bank
 * @property int $no_rekening
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
class TerimaBayarKlaim extends DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terimabayarklaim_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carabayar_id', 'penjamin_id','no_terimabayarklaim'], 'required'],
            [['carabayar_id', 'penjamin_id', 'pegawaipenerima_id', 'no_rekening', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carabayar_id', 'penjamin_id', 'pegawaipenerima_id', 'no_rekening', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_terimabayarklaim', 'created_date', 'last_modified_date', 'deleted_date','bank', 'is_active'], 'safe'],
            [['total_terimabayar'], 'number'],
            [['catatan', 'additional_data'], 'string'],
            [['is_nontunai', 'is_deleted', 'is_active'], 'boolean'],
            [['no_terimabayarklaim', 'pemilik_rekening'], 'string', 'max' => 255],
            [['no_terimabayarklaim'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terimabayarklaim_id' => 'Terimabayarklaim ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'tgl_terimabayarklaim' => 'Tgl Terimabayarklaim',
            'no_terimabayarklaim' => 'No Pembayaran',
            'total_terimabayar' => 'Total Terimabayar',
            'pegawaipenerima_id' => 'Pegawaipenerima ID',
            'catatan' => 'Catatan',
            'is_nontunai' => 'Is Nontunai',
            'pemilik_rekening' => 'Pemilik Rekening',
            'bank' => 'Bank ID',
            'no_rekening' => 'No Rekening',
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
