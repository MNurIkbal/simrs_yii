<?php 

namespace app\components\Services;

use Yii;
use app\components\Services\BaseService;

class FarmasiService extends BaseService
{
   public function __construct()
   {
      $this->service = Yii::$app->docoRest->apotek;
   }

   public function getListObat($params)
   {
      $response = $this->guzzleExec($this->service, [
			'url' => 'allow/get-list-stok-apotek',
			'payload' => [
				 'query' => $params
			]
	  	]);
	  	return $this->responseJson(200, 'Data berhasil diambil', $response['data']);
   }
}