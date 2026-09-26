<?php

namespace app\modules\master\models;

use Yii;

class SatusehatSinkronisasiForm extends \yii\base\Model {
    /**
     * @inheritdoc
     */
    
     public $jenis_sinkronisasi;
     public $jumlah_data;
 
     /**
      * @inheritdoc
      */
     public function rules()
     {
         return [
            [['jenis_sinkronisasi'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['jenis_sinkronisasi'], 'integer'],
            [['jumlah_data'], 'string'],
         ];
     }
 
     /**
      * @inheritdoc
      */
     public function attributeLabels()
     {
         return [
            'jenis_sinkronisasi' => 'Data Source',
         ];
     }
}