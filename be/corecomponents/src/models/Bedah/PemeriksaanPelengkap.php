<?php

namespace Doco\models\Bedah;

use Yii;

/**
 * This is the model class for table "pemeriksaanpelengkap_t".
 *
 * @property int $pemeriksaanpelengkap_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property int $daftartindakan_id
 * @property string $nama_jaringan
 * @property int $qty
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
class PemeriksaanPelengkap extends \app\components\ActiveRepositories
{
    public $_repositori = 'Doco\Repositories\PemeriksaanPelengkapRepositories';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemeriksaanpelengkap_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pasienmasukpenunjang_id', 
                'inpostoperasi_id', 
                'daftartindakan_id', 
                'qty', 
                'created_by', 
                'modified_count', 
                'last_modified_by', 
                'deleted_by',
                'ruangan_id',
            ], 'default', 'value' => null],
            [['qty'], 'number', 'message'=>'{attribute} Harus berupa angka'],
            [[
                'pasienmasukpenunjang_id', 
                'inpostoperasi_id', 
                'daftartindakan_id', 
                'created_by', 
                'modified_count', 
                'last_modified_by', 
                'deleted_by',
                'ruangan_id',
            ], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'is_cyto'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_jaringan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanpelengkap_id' => 'Pemeriksaanpelengkap ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'nama_jaringan' => 'Nama Jaringan',
            'qty' => 'Qty',
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
