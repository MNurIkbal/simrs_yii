<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenistarif_m".
 *
 * @property int $jenistarif_id
 * @property string $jenistarif_nama
 * @property string $jenistarif_namalainnya
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
 * @property string $jenistarif_kode
 * @property string $catatan
 *
 * @property KomponenJasa $komponenjasa
 * @property TarifTindakan $tariftindakan
 */
class JenisTarif extends \Doco\components\DocoActiveRecord
{

  public $data_mapping;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenistarif_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenistarif_nama'], 'required'],
            [['additional_data', 'catatan'], 'string'],
            [['created_date','data_mapping' ,'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenistarif_nama', 'jenistarif_namalainnya'], 'string', 'max' => 25],
            [['jenistarif_kode'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenistarif_id' => 'Jenistarif ID',
            'jenistarif_nama' => 'Jenistarif Nama',
            'jenistarif_namalainnya' => 'Jenistarif Namalainnya',
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
            'jenistarif_kode' => 'Jenistarif Kode',
            'catatan' => 'Catatan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKomponenjasa()
    {
        return $this->hasMany(KomponenJasa::className(), ['jenistarif_id' => 'jenistarif_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariftindakan()
    {
        return $this->hasMany(TarifTindakan::className(), ['jenistarif_id' => 'jenistarif_id']);
    }
}
