<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-10 15:00
 * @Last Modified by:   Naufal
 * @Description: Model untuk master Perujuk Pasien (Pendaftaran) 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "perujuk_m".
 *
 * @property integer $perujuk_id
 * @property integer $asalrujukan_id
 * @property string $namaperujuk
 * @property string $spesialis
 * @property string $alamatlengkap
 * @property string $notelp
 * @property string $kodeppk
 * @property string $perujuk_kode
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
 * @property AsalrujukanM $asalrujukan
 */
class Perujuk extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'perujuk_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['asalrujukan_id','namaperujuk'], 'required'],
            [['asalrujukan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alamatlengkap', 'additional_data', 'sync_id'], 'string'],
            [['perujuk_id','created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['namaperujuk', 'notelp'], 'string', 'max' => 100],
            [['spesialis'], 'string', 'max' => 50],
            [['kodeppk'], 'string', 'max' => 20],
            [['perujuk_kode'], 'string', 'max' => 50],
            [['asalrujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsalRujukan::className(), 'targetAttribute' => ['asalrujukan_id' => 'asalrujukan_id']],
            [[/*'namaperujuk',*/'spesialis','alamatlengkap','notelp'], 'validateDuplicate'],
            [['namaperujuk'], 'unique', 'targetAttribute' => ['namaperujukLowercase' => 'lower(namaperujuk)']],
            [['sync_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'perujuk_id' => 'Perujuk ID',
            'asalrujukan_id' => 'Asal Perujuk',
            'namaperujuk' => 'Namaperujuk',
            'spesialis' => 'Spesialis',
            'alamatlengkap' => 'Alamatlengkap',
            'notelp' => 'Notelp',
            'kodeppk' => 'Kodeppk',
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

    public function getNamaperujukLowercase()
    {
        return strtolower($this->namaperujuk);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsalRujukan()
    {
        return $this->hasOne(AsalRujukan::className(), ['asalrujukan_id' => 'asalrujukan_id']);
    }

    public function extraFields()
    {
        return [
            'asalrujukan_m' => function($item){
                return $item->asalRujukan;
            }
        ];
    }
    public function validateDuplicate($attributes, $params){    
        $where = [
            'asalrujukan_id'=>$this->asalrujukan_id,
            'namaperujuk'=>$this->namaperujuk,
            'spesialis'=>$this->spesialis,
            'alamatlengkap'=>$this->alamatlengkap,
            'notelp'=>$this->notelp,
            'is_deleted'=>'false',
        ];        
        $model = Perujuk::find()->where($where)->one();
        if(count($model) > 0){
            $msg = "Data sudah pernah diinputkan";
            if(!empty($this->perujuk_id)){
                if($model->perujuk_id != $this->perujuk_id){
                    $this->addError('namaperujuk', $msg);
                    $this->addError('spesialis', $msg);
                    $this->addError('alamatlengkap', $msg);
                    $this->addError('notelp', $msg);    
                }
            }else{                
                $this->addError('namaperujuk', $msg);
                $this->addError('spesialis', $msg);
                $this->addError('alamatlengkap', $msg);
                $this->addError('notelp', $msg);    
            }
        }
    }
}
