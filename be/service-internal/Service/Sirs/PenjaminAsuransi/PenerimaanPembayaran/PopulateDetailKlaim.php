<?php

namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\TerimaBayarKlaim;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;

class PopulateDetailKlaim extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $cacheFiles = Yii::$app->cacheFiles;
      $cacheFiles->set('data-penerimaan-bpjs-'.$this->randString, $this->data);

      return json_encode([
         'service' => 'Sirs-PopulateDetailKlaim',
         'payload' => $this->randString,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }
}