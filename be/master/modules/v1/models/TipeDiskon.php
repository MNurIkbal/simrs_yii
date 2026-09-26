<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tipediskon_m".
 *
 * @property int $tipediskon_nama
 * @property bool $is_active
 */
class TipeDiskon extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipediskon_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipediskon_nama'], 'required'],
            [['tipediskon_nama'], 'validateUniqueName'],
            [['tipediskon_nama'], 'string'],
            [['is_active'], 'safe'],
        ];
    }

    public function validateUniqueName(){
        if ($this->isNewRecord){
            $model = self::find()->where([
                'tipediskon_nama' => $this->tipediskon_nama
                ])->count();
            if($model!=0){
                $this->addError("tipediskon_nama","Nama tipe diskon sudah digunakan.");
                return false;
            }
         
        }else{
            $model = self::find()->where(['tipediskon_nama' => $this->tipediskon_nama])
                                    ->andWhere(['<>', 'tipediskon_id', $this->tipediskon_id])
                                    ->count();
            if($model != 0){
                $this->addError("tipediskon_nama","Nama tipe diskon sudah digunakan.");
                return false;
            }
        }
    
        return true;
    }

    
}
