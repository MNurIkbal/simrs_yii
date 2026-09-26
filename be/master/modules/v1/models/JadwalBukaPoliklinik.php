<?php
/* Ardi Pratama */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jadwalbukapoli_m".
 *
 * @property integer $jadwalbukapoli_id
 * @property integer $ruangan_id
 * @property string $hari
 * @property string $waktu_pelayanan
 * @property string $jam_mulai
 * @property string $jam_tutup
 * @property integer $maxantiran_poli
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 *
 * @property RuanganM $ruangan
 */
class JadwalBukaPoliklinik extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jadwalbukapoli_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'hari', 'waktu_pelayanan', 'jam_mulai', 'jam_tutup'], 'required'],
            [['ruangan_id', 'maxantiran_poli', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jam_mulai', 'jam_tutup', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['hari'], 'string', 'max' => 20],
            [['waktu_pelayanan'], 'string', 'max' => 50],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwalbukapoli_id' => 'Jadwalbukapoli ID',
            'ruangan_id' => 'Ruangan ID',
            'hari' => 'Hari',
            'waktu_pelayanan' => 'Waktu Pelayanan',
            'jam_mulai' => 'Jam Mulai',
            'jam_tutup' => 'Jam Tutup',
            'maxantiran_poli' => 'Maxantiran Poli',
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
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function extraFields()
    {
        return [
            'ruangan_m' => function($item){
                return $item->ruangan;
            }
        ];
    }
}
