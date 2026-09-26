<?php 

namespace app\modules\v1\payload;
use Yii;
use yii\base\Model;

class UploadPayload extends Model 
{
   public $file;
   public function rules()
   {
      return [
         [['file'], 'file', 
            'skipOnEmpty' => false, 
            'extensions' => ['pdf'],
            'wrongExtension' => 'Hanya file PDF yang diperbolehkan untuk {attribute} ini.',
            'wrongMimeType' => 'Hanya file PDF yang diperbolehkan untuk {attribute} ini.',
            'mimeTypes' => ['application/pdf']
         ]
      ];
   }
}