<?php

namespace Doco\models\Bedah;

use Yii;

/**
 * This is the model class for table "konsultasitindakan_t".
 *
 * @property int $konsultasitindakan_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property string $bagian_tubuh
 * @property int $dokter_id
 * @property string $alasan
 * @property int $daftartindakan_id
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
class KonsultasiTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konsultasitindakan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'dokter_id', 'daftartindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'dokter_id', 'daftartindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bagian_tubuh', 'alasan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konsultasitindakan_id' => 'Konsultasitindakan ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'bagian_tubuh' => 'Bagian Tubuh',
            'dokter_id' => 'Dokter ID',
            'alasan' => 'Alasan',
            'daftartindakan_id' => 'Daftartindakan ID',
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
