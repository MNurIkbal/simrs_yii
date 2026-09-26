<?php 

namespace Doco\radiologi\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\radiologi\models\OrderForm;

class TransaksiController extends DocoController
{   
	protected $_title = "Transaksi Radiologi";
    protected $_module = '/radiologi/transaksi';
    protected $_restRad;
	protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologiv1_1;
    }

	public function actionOrder()
	{
		$modelPenunjang = new OrderForm;
		$data_dokter = [1=>"Dr. Ardi Pratama"];
		$response = $this->_restRad->get('order/bundle-api', ['query'=>[]]);
        $body = json_decode($response->getBody(), true);
        if(isset($body['response']['listUnit'])){
        	foreach ($body['response']['listUnit'] as $ruangan) {
        		$data_instalasi[$ruangan['ruangan_id']] = 'Inst. '.@$ruangan['instalasi']['instalasi_nama'].' - '.@$ruangan['ruangan_nama'];
        	}
        }
		return $this->render('index',get_defined_vars());
	}

	public function actionCariPasien($q = null)
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results'=>[]];
	    // $out = ['results' => [['id' => '1', 'text' => 'oke'],['id'=>'2','text'=>'ce']]];
	    if (!is_null($q)) {
	    	$r = $this->_restRad->get('allow/list-pasien',['query'=>['q'=>$q]]);
	        $body = json_decode($r->getBody(),true);
	    	// var_dump($body);exit;
	    	if(isset($body['response']['data'])){
	    		foreach ($body['response']['data'] as $pasien) {
	    			$out['results'][] = [
	    				'id'=>$pasien['pendaftaran_id'],
	    				'text'=> $pasien['no_pendaftaran'].' / '.$pasien['pasien']['no_rekam_medik'].' / '.$pasien['pasien']['nama_pasien'],
	    				'no_pendaftaran' =>$pasien['no_pendaftaran']
	    			];
	    		}
	    	}
	    //     $out['results'] = array_values($data);
	    // }
	    }
	    return $out;
	}

	public function actionListDetailPemeriksaan()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $result = ['data'=>[]];
        try {
            $response = $this->_restRad->get('allow/list-pemeriksaan', ['query'=>
	            ['no_pendaftaran'=>$get['noPendaftaran']]
	        ]);
            $body = json_decode($response->getBody(), true);
            $result['data'] = $body['response'];
            return $result;
        } catch (RequestException $e) {
        	$result['errorMessage'] ='Gagal';
        	return $result;
        } catch (\Exception $e) {
        	$result['errorMessage'] ='Gagal';
        	return $result;
        }
    }

    public function actionSimpan()
    {
        $request = Yii::$app->request;
    	try {
    		$nodaftar = $request->post('nodaftar');
    		$order = $request->post('OrderForm');
    		$listorder = $request->post('listorder');
    		$order = [
    			'noDaftar' => $nodaftar,
    			'dokterPengirim' => $order['dokter_perujuk'],
    			'tglKirim' => $order['tgl_permintaan'],
    			'catatanKirim' => $order['catatan_dokter'],
    			'orderList' => json_decode($listorder)
    		];
			$response = $this->_restRad->post('order/new', ['form_params'=>$order]);
	        $body = json_decode($response->getBody(), true);
	 		
        	return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}