<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "komponentarif_m".
 *
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property string $komponentarif_namalainnya
 * @property int $komponentarif_urutan
 * @property double $persen_delegasi
 * @property double $persen_operator
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
 *
 * @property KomponenjasaM[] $komponenjasaMs
 * @property KomponentarifinstalasiMp[] $komponentarifinstalasiMps
 * @property InstalasiM[] $instalasis
 * @property ObatalkeskomponenT[] $obatalkeskomponenTs
 * @property PelayananrekeningM[] $pelayananrekeningMs
 * @property PembebasantarifT[] $pembebasantarifTs
 * @property TariftindakanM[] $tariftindakanMs
 */
class KomponenTarif extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'komponentarif_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['komponentarif_nama'], 'required'],
            [['komponentarif_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['komponentarif_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['persen_delegasi', 'persen_operator'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['komponentarif_nama', 'komponentarif_namalainnya'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Komponentarif Nama',
            'komponentarif_namalainnya' => 'Komponentarif Namalainnya',
            'komponentarif_urutan' => 'Komponentarif Urutan',
            'persen_delegasi' => 'Persen Delegasi',
            'persen_operator' => 'Persen Operator',
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
