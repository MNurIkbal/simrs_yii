<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "carakeluar_m".
 *
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $carakeluar_namalain
 * @property string $carakeluar_kode
 * @property int $carakeluar_urutan
 * @property string $catatan
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
 * @property KondisikeluarM[] $kondisikeluarMs
 * @property PasienpulangT[] $pasienpulangTs
 */
class CaraKeluar extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carakeluar_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_nama', 'carakeluar_namalain'], 'required'],
            [['carakeluar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carakeluar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['carakeluar_nama', 'carakeluar_namalain', 'carakeluar_kode'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Carakeluar Nama',
            'carakeluar_namalain' => 'Carakeluar Namalain',
            'carakeluar_kode' => 'Carakeluar Kode',
            'carakeluar_urutan' => 'Carakeluar Urutan',
            'catatan' => 'Catatan',
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
    public function getKondisikeluarMs()
    {
        return $this->hasMany(KondisikeluarM::className(), ['carakeluar_id' => 'carakeluar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienpulangTs()
    {
        return $this->hasMany(PasienpulangT::className(), ['carakeluar_id' => 'carakeluar_id']);
    }

    /**
    * @author sunarko
    * @since 2018-05-22 09:54:19
    * @param
    * @return array list of jenis cara keluar
    * @desc
    */

    public static function getCaraKeluar() {
        $sql = "
        SELECT
            carakeluar_id,
            carakeluar_nama
        FROM carakeluar_m
        WHERE is_deleted = false AND is_active = true
        ORDER BY carakeluar_urutan, carakeluar_id
        ";
        $list = Yii::$app->db->createCommand($sql)->queryAll();
        return $list;
    }
}
