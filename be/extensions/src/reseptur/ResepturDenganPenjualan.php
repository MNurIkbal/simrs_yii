<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\reseptur;

use Yii;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;

class ResepturDenganPenjualan extends \Doco\processes\ResepturProcess
{
	protected function createResepPenjualan()
	{
        $request = Yii::$app->docoRest->apotek->post('reseptur/penjualan-resep', [
            'json'=> Yii::$app->request->post()
        ]);
        $response = json_decode($request->getBody(),true);
        return (new DocoHelpers)->response($response);
        // if(!isset($response['response']['nomor'])) throw new \Exception("Error Processing Request", 1);
        // return ['message' => 'Data Berhasil Disimpan', $response];
        // return $response['response'];
	}

    public function execute()
    {
		try{
			return $this->createResepPenjualan();
        } catch (RequestException $e) {
			Yii::error(
                'Message : ' . $e->getMessage() . '--||--Line : ' . $e->getLine() . '--||--File : ' . $e->getFile() . '--||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : POST --||--Payload : ' . json_encode(['postdata' => Yii::$app->request->post(), 'response-api' => json_decode($e->getResponse()->getBody(),true) ]),
                'server-error'
            );
            $response = json_decode($e->getResponse()->getBody(), true);
            return (new DocoHelpers)->response($response, $e->getCode() ?: 500);
		}catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            Yii::error(
                'Message : ' . $e->getMessage() . '--||--Line : ' . $e->getLine() . '--||--File : ' . $e->getFile() . '--||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : POST --||--Payload : ' . json_encode(Yii::$app->request->post()),
                'server-error'
            );
            return [
                'message' => 'Terjadi Kesalahan, Silahkan ulangi beberapa saat lagi.',
                'title' => 'Proses Gagal'
            ];
		}
    }

    public function processFlow()
    {
        try{
            return $this->createResepPenjualan();
        }catch(RequestException $e){
            \Yii::$app->response->statusCode = 500;
            $response = json_decode($e->getResponse()->getBody(),true);
            return $response['response'];
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            $response = json_decode($e->getResponse()->getBody(),true);
            return $response['response'];
        }
    }
}