<?php 
/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use Doco\components\DocoMessages;
use Doco\models\RiwayatPenyakitTrans;

class GetDataMcuProcess extends \Doco\components\DocoBaseProcessExtension
{
  protected function getRiwayatPenyakit()
  {
    $result = [];
    $pendaftaran_id = $this->_requestData->get('pendaftaran_id', null);
    $model = RiwayatPenyakitTrans::find()
      ->where(['pendaftaran_id' => $pendaftaran_id])
      ->one();

    if($model) {
      $result = $model;
    }
    return $result;
  }

  protected function getData()
  {
    return [
      'data_riwayat_penyakit' => $this->getRiwayatPenyakit(),
    ];
  }

  protected function processFlow() 
  {
    $this->getData();
  }
}