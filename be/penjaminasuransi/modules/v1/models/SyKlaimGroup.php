<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klaimgroup_t".
 *
 * @property int $sy_klaimgroup_id
 * @property int $sy_klaiminacbg_id
 * @property string $group_nama
 * @property int $group_tarif
 * @property string $spesial_procedure
 * @property string $spesial_prosthesis
 * @property string $spesial_investigation
 * @property string $spesial_drug
 * @property double $total
 * @property double $tambahan_biaya
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
 * @property string $cbg
 * @property string $special_group
 * @property string $sp_procedure_kode
 * @property string $sp_prosthesis_kode
 * @property string $sp_investigation_kode
 * @property string $sp_drug_kode
 * @property string $sp_procedure_nama
 * @property string $sp_prosthesis_nama
 * @property string $sp_investigation_nama
 * @property string $sp_drug_nama
 * @property string $add_episode
 * @property string $add_jenazah
 */
class SyKlaimGroup extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_klaimgroup_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sy_klaiminacbg_id'], 'required'],
            [['sy_klaiminacbg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['sy_klaiminacbg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['total', 'tambahan_biaya', 'group_tarif'], 'number'],
            [['additional_data', 'add_episode', 'add_jenazah'], 'string'],
            [['add_jenazah','created_date', 'last_modified_date', 'deleted_date', 'persen_tambahan', 'total_naikkelas','total_kelaspelayanan'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['group_nama', 'spesial_procedure', 'spesial_prosthesis', 'spesial_investigation', 'spesial_drug', 'cbg', 'special_group', 'sp_procedure_kode', 'sp_prosthesis_kode', 'sp_investigation_kode', 'sp_drug_kode', 'sp_procedure_nama', 'sp_prosthesis_nama', 'sp_investigation_nama', 'sp_drug_nama'], 'string', 'max' => 255],
            [['sy_klaimgroup_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sy_klaimgroup_id' => 'Klaimgroup ID',
            'sy_klaiminacbg_id' => 'Klaiminacbg ID',
            'group_nama' => 'Group Nama',
            'spesial_procedure' => 'Spesial Procedure',
            'spesial_prosthesis' => 'Spesial Prosthesis',
            'spesial_investigation' => 'Spesial Investigation',
            'spesial_drug' => 'Spesial Drug',
            'total' => 'Total',
            'tambahan_biaya' => 'Tambahan Biaya',
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
            'add_episode' => 'Additional Episode',
            'add_jenazah' => 'Additional Jenazah',
        ];
    }
}
