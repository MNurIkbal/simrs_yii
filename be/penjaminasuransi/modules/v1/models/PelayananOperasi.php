<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pelayananoperasi_t".
 *
 * @property int $pelayananoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property int $operasi_id
 * @property int $daftartindakan_id
 * @property bool $is_cyto
 * @property int $jenis_luka lookup_type='jenis_luka'
 * @property int $golonganoperasi_id
 * @property int $jenisanastesi_id
 * @property double $tarif_satuan
 * @property double $tarif_tindakan
 * @property double $tarif_cyto
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
class PelayananOperasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pelayananoperasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'operasi_id', 'daftartindakan_id', 'jenis_luka', 'golonganoperasi_id', 'jenisanastesi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id', 'operasi_id', 'daftartindakan_id', 'jenis_luka', 'golonganoperasi_id', 'jenisanastesi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_cyto', 'is_deleted', 'is_active'], 'boolean'],
            [['tarif_satuan', 'tarif_tindakan', 'tarif_cyto'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pelayananoperasi_id' => 'Pelayananoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'operasi_id' => 'Operasi ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'is_cyto' => 'Is Cyto',
            'jenis_luka' => 'Jenis Luka',
            'golonganoperasi_id' => 'Golonganoperasi ID',
            'jenisanastesi_id' => 'Jenisanastesi ID',
            'tarif_satuan' => 'Tarif Satuan',
            'tarif_tindakan' => 'Tarif Tindakan',
            'tarif_cyto' => 'Tarif Cyto',
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
