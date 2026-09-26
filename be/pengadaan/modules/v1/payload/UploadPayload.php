<?php 

namespace app\modules\v1\payload;
use Yii;
use yii\base\Model;

class UploadPayload extends Model {
   public $file;
   public function rules()
   {
      return [
         [['file'], 'file', 
            'extensions' => 'pdf,zip,xlsx,xls', 
            'mimeTypes' => 'application/pdf, application/zip, application/vnd.ms-excel',
            'checkExtensionByMimeType' => false,
            'wrongExtension' => 'Hanya file PDF, Excel atau Zip yang diperbolehkan untuk {attribute} ini.',
            'wrongMimeType' => 'Hanya file PDF, Excel atau Zip yang diperbolehkan untuk {attribute} ini.',
         ]
      ];
   }
}
