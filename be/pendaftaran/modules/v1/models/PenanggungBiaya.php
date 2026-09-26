<?php

namespace app\modules\v1\models;
use Yii;

/**
 * This is the model class for table "penanggungbiaya_t".
 *
 * @property int $penanggungbiaya_id
 * @property int $pasien_id
 * @property int $carabayar_id
 * @property string $penanggungbiaya_nama
 * @property string $namabagian
 * @property string $noindukkaryawan
 * @property string $jpkm
 * @property string $instansi
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
 */
class PenanggungBiaya extends \Doco\components\DocoActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penanggungbiaya_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'carabayar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'carabayar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['penanggungbiaya_nama', 'namabagian', 'noindukkaryawan','jpkm', 'instansi', 'created_date', 'last_modified_date', 'deleted_date', 'ruangcarabayar_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['penanggungbiaya_nama', 'namabagian', 'noindukkaryawan','jpkm', 'instansi',], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penanggungbiaya_id' => 'Penanggungbiaya ID',
            'pasien_id' => 'Pasien ID',
            'carabayar_id' => 'Carabayar ID',
            'penanggungbiaya_nama' => 'Nama',
            'namabagian' => 'Nama Bagian',
            'noindukkaryawan' => 'NIK',
            'jpkm' => 'JPKM',
            'instansi' => 'instansi',
            'ruangcarabayar_id' => 'Ruang Carabayar ID'
        ];
    }
}
