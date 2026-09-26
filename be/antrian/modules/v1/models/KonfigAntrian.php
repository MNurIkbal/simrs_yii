<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigantrian_m".
 *
 * @property int $konfigantrian_id
 * @property int $layarantrian_id
 * @property int $fungsiantrian_id
 * @property string $kode_antrian
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
 * @property bool $is_default
 * @property int $carabayar_id
 * @property int $jenisantrian_id
 * @property int $klasifikasipasien_id
 * @property int $instalasi_id
 * @property int $penomoran_id
 * @property int $groupcarabayar_id
 * @property int $ruangan_id
 */
class KonfigAntrian extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigantrian_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['layarantrian_id', 'fungsiantrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'carabayar_id', 'jenisantrian_id', 'klasifikasipasien_id', 'penomoran_id', 'groupcarabayar_id'], 'default', 'value' => null],
            [['layarantrian_id', 'fungsiantrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'carabayar_id', 'jenisantrian_id', 'klasifikasipasien_id', 'penomoran_id', 'groupcarabayar_id'], 'integer'],
            // [['fungsiantrian_id'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','instalasi_id','ruangan_id'], 'safe'],
            [['is_deleted', 'is_active', 'is_default'], 'boolean'],
            [['kode_antrian'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigantrian_id' => 'Konfigantrian ID',
            'layarantrian_id' => 'Layarantrian ID',
            'fungsiantrian_id' => 'Fungsiantrian ID',
            'kode_antrian' => 'Kode Antrian',
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
            'is_default' => 'Is Default',
            'carabayar_id' => 'Carabayar ID',
            'jenisantrian_id' => 'Jenisantrian ID',
            'klasifikasipasien_id' => 'Klasifikasipasien ID',
            'instalasi_id' => 'Instalasi ID',
            'penomoran_id' => 'Penomoran ID',
            'groupcarabayar_id' => 'Groupcarabayar ID',
            'ruangan_id' => 'Ruangan ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLayarantrianMs()
    {
        return $this->hasOne(Layarantrian::className(), ['konfigantrian_id' => 'konfigantrian_id']);
    }
}
