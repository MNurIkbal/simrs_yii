<?php

/*Author: Ardi Pratama*/

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;
/**
 * This is the model class for table "jadwalbukapoli_m".
 *
 * @property integer $jadwalbukapoli_id
 * @property integer $ruangan_id
 * @property integer $shift_id
 * @property string $hari
 * @property string $waktu_pelayanan
 * @property string $jam_mulai
 * @property string $jam_tutup
 * @property integer $maxantrian_poli
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
class JadwalPoliklinik extends \Doco\components\DocoActiveRecord
{
    public function scenarios()
    {
        return [
            'create' => ['ruangan_id','waktu_pelayanan','jam_mulai','jam_tutup','maxantrian_poli','hari','additional_data','created_date','created_by','modified_count','last_modified_date','last_modified_by','is_deleted','is_active','deleted_date','deleted_by','shift_id','kuota_online'],
            'update' => ['jadwalbukapoli_id','ruangan_id','waktu_pelayanan','jam_mulai','jam_tutup','maxantrian_poli','hari','additional_data','created_date','created_by','modified_count','last_modified_date','last_modified_by','is_deleted','is_active','deleted_date','deleted_by','shift_id','kuota_online'],
        ];
    }
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
            [['ruangan_id', 'hari', 'waktu_pelayanan'], 'required'],
            [['shift_id', 'ruangan_id', 'maxantrian_poli', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kuota_online'], 'integer'],
            [['jam_mulai', 'jam_tutup', 'created_date', 'last_modified_date', 'deleted_date', 'jadwalbukapoli_id', 'shift_id', 'maxantrian_poli', 'kuota_online'], 'safe'],
            [['additional_data'], 'string'],
            [['jam_mulai','jam_tutup'],'cekJadwalPoliAktif'],
            // [['ruangan_id'], 'checkPoli'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['hari'], 'string', 'max' => 20],
            [['waktu_pelayanan'], 'string', 'max' => 50],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    public function checkPoli($attribute, $params)
    {
        $request = Yii::$app->request;
        $hari = $this->hari;
        $ruangan_id = $this->ruangan_id;
        $query = Yii::$app->db->createCommand("
            SELECT jadwalbukapoli_id FROM jadwalbukapoli_m 
                WHERE ruangan_id = $ruangan_id 
                AND hari = $hari
                AND is_deleted = false
        ")->queryOne();
        if (!empty($query)) {
            if ($this->jadwalbukapoli_id != $query['jadwalbukapoli_id']) {
                $this->addError('ruangan_id',Yii::t('app','Ruangan sudah ada di hari yang sama'));
            }
        }
    }

    public function cekJadwalPoliAktif($attribute,$params)
    {
        $findJadwal = $this::find()
                        ->where(['hari'=>$this->hari,'ruangan_id'=>$this->ruangan_id,'is_active'=>TRUE,'is_deleted'=>FALSE]);
        if($this->scenario == 'update'){
            $findJadwal->andWhere("jadwalbukapoli_id <> '{$this->jadwalbukapoli_id}'");
        }
        if($findJadwal->count() > 0){
            $jadwal_poli = $findJadwal
                            ->select(['jadwalbukapoli_id','jam_mulai','jam_tutup'])
                            ->asArray()->all();
            $jadwalpoli_mulai = strtotime($this->jam_mulai);
            $jadwalpoli_tutup = strtotime($this->jam_tutup);
            foreach ($jadwal_poli as $k_jadwal_poli => $v_jadwal_poli) {
                $jam_mulai = strtotime($v_jadwal_poli['jam_mulai']);
                $jam_tutup = strtotime($v_jadwal_poli['jam_tutup']);
                if( 
                    (($jadwalpoli_mulai >= $jam_mulai) && ($jadwalpoli_mulai <= $jam_tutup)) || 
                    ($jadwalpoli_tutup == $jam_tutup) || 
                    (($jadwalpoli_mulai <= $jam_mulai) && ($jadwalpoli_tutup >= $jam_tutup)) || 
                    (($jadwalpoli_tutup > $jam_mulai) && ($jadwalpoli_tutup <= $jam_tutup))
                ){
                    $this->addError($attribute,'Telah Ada Jadwal Pada Rentang Jam Ini');
                }
            }
        }
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
            'maxantrian_poli' => 'Maxantiran Poli',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'shift_id' => 'Shift',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'kuota_online' => 'Kuota Online',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function getInstalasi()
    {
        return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'instalasi_id'])
            ->via('ruangan');
    }

    public function getShift()
    {
        return $this->hasOne(Shift::className(), ['shift_id' => 'shift_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHari()
    {
        return $this->hasOne(Lookup::className(), ['lookup_id' => 'hari']);
    }

    public function extraFields()
    {
        return [
            'ruangan_m' => function($item){
                return $item->ruangan;
            },
            'lookup_m' => function ($item) {
                return $item->hari;
            }
        ];
    }
}
