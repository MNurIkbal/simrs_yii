<?php

namespace app\modules\master\components\actions;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ViewAction;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;

class GetDataAction extends ViewAction
{
	public $serviceName = null;
	public $serviceAction = null;
	public $requestMethod = 'GET';
	public $module;
	public $keyField;
	public $parseMethod = null;

	public function run()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        // echo "<pre>";var_dump($yiiRestfulParams);die();
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            if( strtolower($this->requestMethod) !=  strtolower('GET') ){
                $response = $this->serviceName->request($this->requestMethod, $this->serviceAction,[
                            'form_params' => $request->post()
                        ]);
            }else{
                $response = $this->serviceName->get($this->serviceAction.'?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            }
            $body = json_decode($response->getBody(), true);
            if(!is_null($this->parseMethod)){
                if(method_exists($this->controller, $this->parseMethod)){
                    $data = $this->controller->{$this->parseMethod}($body['response']['data']);
                }else{
                    throw new Exception("Method {$this->parseMethod} not found in controller {$this->controller->className()}", 500);
                }
            }else{
                $data = $this->parsingResponse($body['response']['data']);
            }
            $result['data'] = $data;
            $result['recordsTotal'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
            $result['recordsFiltered'] = isset($body['response']['_meta']['totalCount']) ? $body['response']['_meta']['totalCount'] : 0;
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
	}

	public function parsingResponse($response)
	{
		$data = [];
		$request = Yii::$app->request;
		$no = $request->get('start',1);
        foreach ($response as $key => $value) {
            $no++;
            if(isset($value[$this->keyField])){
                $primaryKey = DocoHelpers::encrypt($value[$this->keyField]);
                unset($value[$this->keyField]);
            }
            if(isset($primaryKey)){
                $value['primary'] = $primaryKey;
            }
            if(isset($value['is_active'])){
                $value['is_active'] = !empty($value['is_active']) ? 'Aktif' : 'Tidak Aktif'; 
                // $value['is_active'] = DocoHelpers::isActive($value['is_active']);
            }
            if(isset($value['persen_delegasi'])){
                $value['persen_delegasi'] = !empty($value['persen_delegasi']) ? str_replace('.', ',', $value['persen_delegasi']) : 0; 
            }
            if(isset($value['perda_tgl'])){
                $value['perda_tgl'] = date('d M Y', strtotime($value['perda_tgl']));
            }

            if(isset($value['jenis_tindakan_paket'])){
                $value['jenis_nama_paket_tindakan'] = $value['jenis_tindakan_paket'].'<br>'.$value['nama_tindakan_paket'];
            }

            if(isset($value['harga_tariftindakan'])){
                $value['harga_tariftindakan'] = !empty($value['harga_tariftindakan']) ? DocoHelpers::formatNumber( $value['harga_tariftindakan'] ) : '-';
            }

            if(isset($value['tarif'])){
                $value['tarif'] = !empty($value['tarif']) ? DocoHelpers::formatNumber( $value['tarif'] ) : '-';
            }

            if(isset($value['carabayar_id']) && isset($value['penjamin_id']) ){
                $value['cara_bayar_penjamin'] = $value['carabayar_nama'].'<br>'.$value['penjamin_nama'];
            }

            if(isset($value['persencyto_tindakan'])){
                $value['persencyto_tindakan'] = str_replace('.', ',', $value['persencyto_tindakan']) ;
            }

            if(isset($value['persen_penyulit'])){
                $value['persen_penyulit'] = str_replace('.', ',', $value['persen_penyulit']) ;
            }

            if(isset($value['persendiskon_tindakan'])){
                $value['persendiskon_tindakan'] = str_replace('.', ',', $value['persendiskon_tindakan']) ;
            }

            $value['jenis_tarif'] = '';
            $value['aksi'] = '';
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
		return $data;
	}
}