<?php

namespace Integrasi\Service\Sirs\Models\PenjaminAsuransi;

use Yii;

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
class TerimaBayarKlaim extends \Integrasi\Components\ActiveRepositories
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
            [['carabayar_id', 'penjamin_id', 'pegawaipenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carabayar_id', 'penjamin_id', 'pegawaipenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_terimabayarklaim', 'created_date', 'last_modified_date', 'deleted_date','bank', 'no_rekening'], 'safe'],
            [['total_terimabayar'], 'number'],
            [['catatan', 'additional_data'], 'string'],
            [['is_nontunai', 'is_deleted', 'is_active'], 'boolean'],
            [['no_terimabayarklaim', 'pemilik_rekening'], 'string', 'max' => 255],
            [['no_rekening'], 'string', 'max' => 20],
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
