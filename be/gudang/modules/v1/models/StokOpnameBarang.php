<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokopnamebarang_t".
 *
 * @property int $stokopnamebarang_id
 * @property int $ruangan_id
 * @property int $formsobarang_id
 * @property string $tglstokopname
 * @property string $nostokopname
 * @property bool $is_stokawal
 * @property string $jenisstokopname lookup_type='jenis_stokopname'
 * @property string $keterangan_opname
 * @property double $totalharga_fisik
 * @property double $totalharga_sistem
 * @property int $pegmengetahui_id
 * @property int $petugas_id
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
class StokOpnameBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokopnamebarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id'], 'required'],
            [['ruangan_id', 'formsobarang_id', 'pegmengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['stokopnamebarang_id', 'ruangan_id', 'formsobarang_id', 'pegmengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglstokopname', 'created_date', 'last_modified_date', 'deleted_date','ruangan_id', 'tgl_implementasi'], 'safe'],
            [['nostokopname', 'jenisstokopname', 'keterangan_opname', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['totalharga_fisik', 'totalharga_sistem'], 'number'],
            [['nostokopname'], 'unique'],
            [['stokopnamebarang_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokopnamebarang_id' => 'Stokopnamebarang ID',
            'ruangan_id' => 'Ruangan ID',
            'formsobarang_id' => 'Formsobarang ID',
            'tglstokopname' => 'Tglstokopname',
            'nostokopname' => 'Nostokopname',
            'jenisstokopname' => 'Jenisstokopname',
            'keterangan_opname' => 'Keterangan Opname',
            'totalharga_fisik' => 'Totalharga Fisik',
            'totalharga_sistem' => 'Totalharga Sistem',
            'pegmengetahui_id' => 'Pegmengetahui ID',
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
