<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompoktindakan_m".
 *
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property string $kelompoktindakan_namalainnya
 * @property double $kelompoktindakan_persencyto
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
 * @property double $kelompoktindakan_persendiskon
 * @property string $kelompoktindakan_kode
 * @property string $catatan
 *
 * @property DaftartindakanM[] $daftartindakanMs
 * @property KomponenjasaM[] $komponenjasaMs
 */
class KelompokTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompoktindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompoktindakan_nama', 'kelompoktindakan_kode'], 'required'],
            [['kelompoktindakan_persencyto', 'kelompoktindakan_persendiskon'], 'number'],
            [['additional_data', 'catatan'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompoktindakan_nama', 'kelompoktindakan_namalainnya'], 'string', 'max' => 50],
            [['kelompoktindakan_kode'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'kelompoktindakan_namalainnya' => 'Kelompoktindakan Namalainnya',
            'kelompoktindakan_persencyto' => 'Kelompoktindakan Persencyto',
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
            'kelompoktindakan_persendiskon' => 'Kelompoktindakan Persendiskon',
            'kelompoktindakan_kode' => 'Kelompoktindakan Kode',
            'catatan' => 'Catatan',
        ];
    }
}
