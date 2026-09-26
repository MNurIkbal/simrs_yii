<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\services;

use Yii;

class InfMutasiObatalkesService {

    protected $_restApotek;

	public function init()
    {
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

	public static function getDetail($queryParameter = [])
	{
		$response = Yii::$app->docoRest->apotek->get('inf-mutasi-obatalkes/detail',[
			'query'=>$queryParameter
		]);
		$body = json_decode($response->getBody(), true);
		return $body['response'];
	}

	public static function postCreate($nomutasioa,$post)
	{
		$response = Yii::$app->docoRest->apotek->request('POST','inf-mutasi-obatalkes/create',[
			'query' => [
				'nomutasioa' => $nomutasioa
			],
			'form_params'=>$post]);
		$body = json_decode($response->getBody(), true);
		return $body;
	}

	public static function postTerima($nomutasioa,$post)
	{
		$response = Yii::$app->docoRest->apotek->request('POST','inf-mutasi-obatalkes/terima',[
			'query' => [
				'nomutasioa' => $nomutasioa
			],
			'form_params'=>$post]);
		$body = json_decode($response->getBody(), true);
		return $body;
	}
}