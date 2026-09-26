<?php 
/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;
use Yii;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use SirsCore\models\PasienMasukPenunjang;
use SirsCore\businessLogic\TagihanBedah;
use Doco\models\Bedah\VerifikasiBedahR;

class GetBillsProcess extends \Doco\components\DocoBaseProcessExtension
{
   protected function getStatusPeriksa()
   {
      $pasienpenunjangId = $this->_requestData->get('pasienpenunjangId', null);
      if(is_null($pasienpenunjangId)){
         throw new ValidationException(422, $this->_error, [
            'text' => 'Parameter pasienpenunjangId tidak boleh kosong!'
         ]);
      }
      // $getBills = $this->getDataBill($pasienpenunjangId);
      $cekStatusPeriksa = PasienMasukPenunjang::find()
         ->selectStatusPeriksa()
         ->findById($pasienpenunjangId)
         ->one();
      
      return ($cekStatusPeriksa) ? $cekStatusPeriksa['status_periksa'] : null;
   }

   // protected function getDataBill($pasienpenunjangId)
   // {
   //    $data = VerifikasiBedahR::find()->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])->all();
   //    $total = 0;
   //    foreach ($data as $key => $value) {
   //       $total += isset($value['total_harga']) ? $value['total_harga'] : 0;
   //    }
   //    return [
   //       'detail' => $data,
   //       'total' => $total,
   //    ];
   // }

   protected function getData()
   {
      return [
         'data_bills' => $this->getBills(),
      ];
   }

   protected function processFlow() 
   {
      return $this->getData();
   }
}
