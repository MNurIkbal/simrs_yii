<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\TerimaBayarKlaim;

/**
 * This is the model class for table "terimabayarklaimdetail_t".
 *
 * @property int $terimabayarklaimdetail_id
 * @property int $terimabayarklaim_id
 * @property int $pengajuanklaim_id
 * @property string $no_pengajuanklaim
 * @property double $total_pengajuan
 * @property double $total_terbayar
 * @property double $pembayaran
 * @property double $total_sisapiutang
 * @property bool $is_alokasi
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
class TerimaBayarKlaimDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terimabayarklaimdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['terimabayarklaim_id', 'pengajuanklaim_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['terimabayarklaim_id', 'pengajuanklaim_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['total_pengajuan', 'total_terbayar', 'pembayaran', 'total_sisapiutang'], 'number'],
            [['is_alokasi', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','terimabayarklaimdetail_id', 'is_alokasi'], 'safe'],
            [['no_pengajuanklaim'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terimabayarklaimdetail_id' => 'Terimabayarklaimdetail ID',
            'terimabayarklaim_id' => 'Terimabayarklaim ID',
            'pengajuanklaim_id' => 'Pengajuanklaim ID',
            'no_pengajuanklaim' => 'No Pengajuanklaim',
            'total_pengajuan' => 'Total Pengajuan',
            'total_terbayar' => 'Total Terbayar',
            'pembayaran' => 'Pembayaran',
            'total_sisapiutang' => 'Total Sisapiutang',
            'is_alokasi' => 'Is Alokasi',
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

    public function getParent()
    {
        return $this->hasOne(TerimaBayarKlaim::className(),['terimabayarklaim_id' => 'terimabayarklaim_id']);
    }
}
