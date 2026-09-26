<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klasifikasidiagnosa_m".
 *
 * @property int $klasifikasidiagnosa_id
 * @property string $klasifikasidiagnosa_kode
 * @property string $klasifikasidiagnosa_nama
 * @property string $klasifikasidiagnosa_namalain
 * @property string $klasifikasidiagnosa_desc
 * @property int $dtd_id
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
 * @property DtdM $dtd
 */
class KlasifikasiDiagnosaM extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'klasifikasidiagnosa_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['klasifikasidiagnosa_kode', 'klasifikasidiagnosa_nama', 'dtd_id'], 'required'],
            [['klasifikasidiagnosa_namalain', 'klasifikasidiagnosa_desc', 'additional_data'], 'string'],
            [['dtd_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['dtd_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['klasifikasidiagnosa_kode'], 'string', 'max' => 10],
            [['klasifikasidiagnosa_nama'], 'string', 'max' => 500],
            [['dtd_id'], 'exist', 'skipOnError' => true, 'targetClass' => DtdM::className(), 'targetAttribute' => ['dtd_id' => 'dtd_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'klasifikasidiagnosa_id' => 'Klasifikasidiagnosa ID',
            'klasifikasidiagnosa_kode' => 'Klasifikasidiagnosa Kode',
            'klasifikasidiagnosa_nama' => 'Klasifikasidiagnosa Nama',
            'klasifikasidiagnosa_namalain' => 'Klasifikasidiagnosa Namalain',
            'klasifikasidiagnosa_desc' => 'Klasifikasidiagnosa Desc',
            'dtd_id' => 'Dtd ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDtd()
    {
        return $this->hasOne(DtdM::className(), ['dtd_id' => 'dtd_id']);
    }
}
