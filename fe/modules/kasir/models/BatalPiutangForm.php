<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property int $pendaftaran_id
 * @property string $username
 * @property string $password
 * @property string $alasan_batal
 * @property date $tgl_batal
 *
 */

class BatalPiutangForm extends \app\components\DocoBaseModel
{
   public $id;
   public $username;
   public $password;
   public $alasan_batal;
   public $tanggal_batal;
   protected $xssProtected = [
      'alasan_batal'
   ];

   /**
    * {@inheritdoc}
    */
   public function rules()
   {
      return [
         [[
            'username', 
            'password',
            'alasan_batal',
         ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
         ],
         [[
            'alasan_batal',
            'id',
            'tanggal_batal',
         ], 'safe']
      ];
   }

   /**
    * {@inheritdoc}
    */
   public function attributeLabels()
   {
      return [
         'id' => 'Pemberian Piutang ID',
         'username' => 'Username',
         'password' => 'Password',
         'alasan_batal' => 'Alasan Batal',
      ];
   }
}
