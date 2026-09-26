<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ketersediaankamar_r".
 *
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
 * @property int $kamarruangan_id
 * @property int $kamartempattidur_id
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
class KetersediaanKamar extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'pendaftaran_id', 
        'pasien_id',
        'ruangan_id',
        'kelaspelayanan_id',
        'kamarruangan_id',
        'kamartempattidur_id',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ketersediaankamar_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'pendaftaran_id', 
                'pasien_id', 
                'ruangan_id', 
                'kelaspelayanan_id', 
                'kamarruangan_id',
                'kamartempattidur_id',
            ], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'ruangan_id' => 'Ruangan',
            'kelaspelayanan_id' => 'Kelas Pelayanan',
            'kamarruangan_id' => 'Kamar Ruangan',
            'kamartempattidur_id' => 'Kamar Tempat Tidur',
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
