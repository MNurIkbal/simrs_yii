<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-20 16:30:52
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "racikan_m".
 *
 * @property int $racikan_id
 * @property string $racikan_nama
 * @property string $racikan_singkatan
 * @property double $tarif_service
 * @property double $persen_service
 * @property double $biaya_kemasan
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
 * @property AntrianfarmasiT[] $antrianfarmasiTs
 * @property RacikandetailM[] $racikandetailMs
 */
class Racikan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'racikan_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['racikan_nama'], 'required'],
            [['tarif_service', 'persen_service', 'biaya_kemasan'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['racikan_nama'], 'string', 'max' => 50],
            [['racikan_singkatan'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'racikan_id' => 'Racikan ID',
            'racikan_nama' => 'Racikan Nama',
            'racikan_singkatan' => 'Racikan Singkatan',
            'tarif_service' => 'Tarif Service',
            'persen_service' => 'Persen Service',
            'biaya_kemasan' => 'Biaya Kemasan',
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