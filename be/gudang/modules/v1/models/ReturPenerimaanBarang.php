<?php
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "returpenerimaanbarang_t".
 *
 * @property int $returpenerimaanbarang_id
 * @property int $pegawairetur_id
 * @property int $ruanganretur_id
 * @property string $no_returpenerimaanbarang
 * @property string $tgl_retur
 * @property string $alasan_retur
 * @property string $keterangan_retur
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
class ReturPenerimaanBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'returpenerimaanbarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pegawairetur_id', 'ruanganretur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['returpenerimaanbarang_id', 'pegawairetur_id', 'ruanganretur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_retur', 'created_date', 'last_modified_date', 'deleted_date','penerimaansupp_id'], 'safe'],
            [['keterangan_retur', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_returpenerimaanbarang'], 'string', 'max' => 50],
            [['alasan_retur'], 'string', 'max' => 100],
            [['no_returpenerimaanbarang'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'returpenerimaanbarang_id' => 'Returpenerimaanbarang ID',
            'pegawairetur_id' => 'Pegawairetur ID',
            'ruanganretur_id' => 'Ruanganretur ID',
            'no_returpenerimaanbarang' => 'No Returpenerimaanbarang',
            'tgl_retur' => 'Tgl Retur',
            'alasan_retur' => 'Alasan Retur',
            'keterangan_retur' => 'Keterangan Retur',
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
            'penerimaansupp_id' => 'Penerimaan Supp Id'
        ];
    }
}
