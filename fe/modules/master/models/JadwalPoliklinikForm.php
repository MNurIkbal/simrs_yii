<?php

namespace app\modules\master\models;

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
class JadwalPoliklinikForm extends \yii\base\Model
{
    
    public $jadwalbukapoli_id;
    public $ruangan_id;
    public $hari;
    public $waktu_pelayanan;
    public $jam_mulai;
    public $jam_tutup;
    public $maxantrian_poli;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $shift_id;
    public $kuota_online;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id','jam_mulai','jam_tutup', 'hari', 'waktu_pelayanan'], 'required'],
            [['shift_id'], 'required', 'on' => 'shift'],
            [['shift_id', 'ruangan_id', 'maxantrian_poli', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kuota_online'], 'integer'],
            [['jadwalbukapoli_id','jam_mulai', 'jam_tutup', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['hari'], 'string', 'max' => 20],
            [['waktu_pelayanan'], 'string', 'max' => 50],
          /*  [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganForm::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwalbukapoli_id' => Yii::t('fe', 'jadwalbukapoli_id'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'hari' => Yii::t('fe', 'hari'),
            'waktu_pelayanan' => Yii::t('fe', 'waktu_pelayanan'),
            'jam_mulai' => Yii::t('fe', 'jam_mulai'),
            'jam_tutup' => Yii::t('fe', 'jam_tutup'),
            'maxantrian_poli' => Yii::t('fe', 'Kuota Offline'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Aktif'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'shift_id' => Yii::t('fe','Shift'),
            'kuota_online' => Yii::t('fe','Kuota Online'),
        ];
    }

    public static function getHari()
    {
        return[
            'Senin' => Yii::t('fe', 'Senin'),
            'Selasa' => Yii::t('fe', 'Selasa'),
            'Rabu' => Yii::t('fe', 'Rabu'),
            'Kamis' => Yii::t('fe', 'Kamis'),
            'Jumat' => Yii::t('fe', 'Jumat'),
            'Sabtu' => Yii::t('fe', 'Sabtu'),
            'Minggu' => Yii::t('fe', 'Minggu'),
        ];
    }
}
