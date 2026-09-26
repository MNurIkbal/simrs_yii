<?php

/**
 * @Author: Budi
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;
use app\modules\kasir\models\InvoiceGabungForm;
use app\modules\kasir\models\InvoiceGabungDetailForm;
use app\modules\kasir\models\KwitansiGabungForm;
use app\components\DocoConstants;

class InfGabungInvoiceController extends DocoController
{
	protected $_title = "Informasi Gabung Nomor Invoice";
	protected $_module = 'kasir/inf-gabung-invoice/';
	protected $_restKasir;
	protected $allowAction = [
		'*',
	];

	public function init()
	{
		parent::init();
		$this->_restKasir = Yii::$app->docoRest->kasir;
	}

	public function actionIndex()
	{
		$title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : $this->_title;
		$btnDetailInvoiceGabung = Yii::$app->docoPlugin->execute($this, 'detail_invoice_gabung');
        $btnInvoiceGabung = Yii::$app->docoPlugin->execute($this, 'invoice_gabung');
		return $this->render('index', compact('title', 'filters', 'btnInvoiceGabung', 'btnDetailInvoiceGabung'));
	}

	public function actionGetData()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$payload = DocoDatatableHelper::advancedFilterParam();
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'inf-gabung-invoice/index',
			'method' => 'get',
			'payload' => [
					'query' => $payload,
			]
		]);
		foreach ($response['data'] as $key => $value) {
			$response['data'][$key]['primary'] = DocoHelpers::encrypt($value['invoicegabung_id']);
			$response['data'][$key]['total_invoicegabung'] = DocoHelpers::formatNumber($value['total_invoicegabung']);
			$ref = '';
			if(!empty($value['ref_invoice'])) {
				$invoice_ref = !empty($value['ref_invoice']) ? $value['ref_invoice'] : [];
				$arrInvoice = explode(",",$invoice_ref);
				foreach ($arrInvoice as $val) {
					$ref .= '<p>'.isset($val) ? $val.'</p>' : '';
				}
			}
			$response['data'][$key]['ref_invoice'] = $ref;

			$no_pendaftaran_ref = '';
			if(!empty($value['no_pendaftaran_ref'])) {
				$no_pendaftaran = !empty($value['no_pendaftaran_ref']) ? $value['no_pendaftaran_ref'] : [];
				$arrPendaftaran = explode(",",$no_pendaftaran);
				foreach ($arrPendaftaran as $val) {
					$no_pendaftaran_ref .= '<p>'.isset($val) ? $val.'</p>' : '';
				}
			}
			$response['data'][$key]['no_pendaftaran_ref'] = $no_pendaftaran_ref;

			$penjamin_nama_ref = '';
			if(!empty($value['penjamin_nama_ref'])) {
				$penjamin_nama = !empty($value['penjamin_nama_ref']) ? $value['penjamin_nama_ref'] : [];
				$arrPenjamin = explode(",",$penjamin_nama);
					foreach ($arrPenjamin as $val) {
						$penjamin_nama_ref .= '<p>'.isset($val) ? $val.'</p>' : '';
					}
			}
			$response['data'][$key]['penjamin_nama_ref'] = $penjamin_nama_ref;

		}
		$response['recordsTotal'] = $response['_meta']['totalCount'];
		$response['recordsFiltered'] = $response['_meta']['totalCount'];
		return $response;
	}

	public function actionFilters($type = null, $pendaftaran_id = [])
	{
		$request = Yii::$app->request;
		$payload = $request->get('payload', []);
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'inf-gabung-invoice/filters',
			'payload' => [
					'query' => [
						'type' => $type,
						'term' => isset($payload['term']) ? $payload['term'] : '',
						'limit' => isset($payload['limit']) ? $payload['limit'] : '',
						'pasien_id' => isset($payload['pasien_id']) ? $payload['pasien_id'] : '',
						'pendaftaran_id' => isset($payload['pendaftaran_id']) ? $payload['pendaftaran_id'] : $pendaftaran_id,
						'page' => Yii::$app->request->get('page', 1),
					]
			],
		]);
		return $this->responseJson(200, 'Data berhasil diambil!', $response);
	}

	public function actionTambahInvoice()
	{
		$request = Yii::$app->request;
		$title = 'Gabung Nomor Invoice';
		$model = new InvoiceGabungDetailForm;
		$modelHeader = new InvoiceGabungForm;
		$modelHeader->tgl_invoicegabung = date('d-M-Y');
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		if($request->post()) {
			$post = $request->post('InvoiceGabungDetailForm');
			$model->attributes = $post;
			if($model->validate()) {
					$dataPembayaran = $this->actionFilters('no_invoice', $model->pendaftaran_id);
					$dataPembayaran = isset($dataPembayaran['data']) ? $dataPembayaran['data'] : [];
					$newData = [];
					$this->actionDeleteCache(null);
					if(!empty($dataPembayaran)) {
						foreach ($dataPembayaran as $key => $value) {
							$newData[] = [
									'pasien_id' => $model->pasien_id,
									'pendaftaran_id' => !empty($value['pendaftaran_id']) ? $value['pendaftaran_id'] : null,
									'no_rekam_medik' => $model->no_rekam_medik,
									'no_pendaftaran' => !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : null,
									'nama_pasien' => $model->nama_pasien,
									'pembayaran_id' => $value['id'],
									'no_invoice' => $value['no_pembayaran'],
									'tgl_invoicegabung' => $value['tgl_pembayaran'],
									'total_invoice' => $value['tagihan'],
									'penjamin_nama' => !empty($value['penjamin_nama']) ?  $value['penjamin_nama'] : '',
							];
						}
						return $this->saveToCache($newData);
					}
			}
			else {
					$errors = DocoHelpers::parseError($model->errors, $formName);
					return DocoHelpers::responseTemplate(422, 'Error', $errors);
			}
		}
		$cache = Yii::$app->cache;
		$cacheName = $this->getPrefixCache();
		$cache->set($cacheName, []);
		return $this->renderAjax('_modal', get_defined_vars());
	}

	private function saveToCache($data)
	{
		$cache = Yii::$app->cache;
		$cacheName = $this->getPrefixCache();
		$cacheData = $cache->get($cacheName);
		if (!$cacheData) {
			$cache->set($cacheName, $data);
		} else {
			$arr = $cacheData;
			if (isset($data[0])) {
					$arr = array_merge($arr, $data);
			} else {
					array_push($arr, $data);
			}
			$cache->set($cacheName, $arr);
		}
		return true;
	}

	public function actionGetDataInvoice()
	{
		$cacheName = $this->getPrefixCache();
		$cache = Yii::$app->cache;
		$cacheData = $cache->get($cacheName);
		$request = Yii::$app->request;
		$result = [];
		$data = [];
		$draw = $request->get('draw', 1);
		$result['data'] = $data;
		$result['draw'] = $draw;
		$result['recordsTotal'] = 0;
		$result['recordsTotal'] = 0;
		$no = 0;
		if(!empty($cacheData)) {
			foreach ($cacheData as $key => $value) {
				$totalInvoice = isset($value['total_invoice']) ? $value['total_invoice'] : 0;
				$tglInvoice = isset($value['tgl_invoicegabung']) ? $value['tgl_invoicegabung'] : '';
				$nama_pasien = isset($value['nama_pasien']) ? $value['nama_pasien'] : '';
				$no_rekam_medik = isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
				$value['no_pendaftaran'] = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
				$value['no_invoice'] = isset($value['no_invoice']) ? $value['no_invoice'] : '';
				$value['nama_pasien'] = $nama_pasien.' / '.$no_rekam_medik;
				$value['total_invoice'] = DocoHelpers::formatNumber($totalInvoice);
				$value['tgl_invoicegabung'] = !empty($tglInvoice) ? date('d M Y H:i:s', strtotime($tglInvoice)) : '-';
				$value['aksi'] = Html::checkbox("check", false, [
					'value' => $value['pembayaran_id'],
					'class' => 'btn btn-danger btn-sm check-invoice',
					'data-key' => $key,
					'data-value' => $value['pembayaran_id'],
					'data-harga' => $totalInvoice,
				]);
				$value['aksi'] .= "<input type='checkbox' 
									class='check-invoice-fake'
									style='display:none'
								></input>";
				$data[$key] = $value;
			}
		}
		$result['data'] = $data;
		$result['draw'] = $draw;
		$result['recordsTotal'] = count($data);
		$result['recordsTotal'] = count($data);
		return DocoHelpers::response($result);
	}

	public function actionDeleteCache($key = null)
	{
		$cacheName = $this->getPrefixCache();
		$cache = Yii::$app->cache;
		$cacheData = $cache->get($cacheName);
		if ($cacheData) {
			if(is_null($key)){
				$cache->delete($cacheName);
			}else{
				$arr = $cacheData;
				unset($arr[$key]);
				$newArr = [];
				foreach ($arr as $key => $value) {
						$newArr[] = $value;
				}
				$cache->set($cacheName, $newArr);
			}
		}
		return DocoHelpers::response(['response' => ['title' => 'Proses berhasil!', 'text' => 'Data berhasil dihapus']]);
	}

	public function actionSimpanInvoice()
	{
		$request = Yii::$app->request;
		$model = new InvoiceGabungForm;
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		$post = $request->post('InvoiceGabungForm');
		$detail = $request->post('detail');
		$detail = explode(',', $detail);
		if(count($detail) == 1) {
			$response['response']['message'] = 'Gabung Invoice harus lebih dari satu Invoice';
            return DocoHelpers::response($response, 500);
		}
		$model->attributes = $post;
		$model->tgl_invoicegabung = !empty($post['tgl_invoicegabung']) ? date('Y-m-d', strtotime($post['tgl_invoicegabung'])) : '';
		$cacheName = $this->getPrefixCache();
		$cache = Yii::$app->cache;
		$cacheData = $cache->get($cacheName);
		$model->cache_data = $cacheData;
		if($model->validate()) {
			try {
					$response = $this->_restKasir->post('inf-gabung-invoice/simpan-invoice',[
						'form_params' => [
							'data' => $model->attributes,
							'detail' => $detail,
						]
					]);
					$response = json_decode($response->getBody(),true);
					return DocoHelpers::response($response);
			} catch (RequestException $e) {
					return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
			} catch (\Exception $e) {
					return DocoHelpers::responseTemplate(500, $e->getMessage());
			}
		}
		else {
			$errors = DocoHelpers::parseError($model->errors, $formName);
			return DocoHelpers::responseTemplate(422, 'Error', $errors);
		}
	}

	public function actionCetak()
	{
		$request = Yii::$app->request;
		$id = $request->get('invoicegabung_id', null);
		if(!is_numeric($id)) {
			$id = DocoHelpers::decrypt($id);
		}
		$path = Yii::getAlias("@download") . "/cetak-invoice-gabung.pdf";
		$userIdentity = Yii::$app->session->get('user_identity');
		$request = Yii::$app->request;
		$url = "inf-gabung-invoice/cetak";
		if(!is_numeric($id)) {
			$id = DocoHelpers::decrypt($id);
		}
		try {
			$response = $this->_restKasir->get($url, [
				'query' => [
					'invoicegabung_id' => $id,
					'nama_pegawai' => $userIdentity['nama_pegawai'],
				],
				'save_to' => $path
			]);
			$response = json_decode($response->getBody(), true);
			return DocoHelpers::previewPdf($path);
		} catch (RequestException $e) {
			throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
		} catch (\Exception $e) {
			throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
		}
	}

	public function actionShowPopup()
	{
		$title = 'Cetak Detail Invoice';
		$request = Yii::$app->request;
		$randString = DocoHelpers::generateRandomString();
		$userIdentity = Yii::$app->session->get('user_identity');
		$nama_pegawai = isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : null;
		$_GET['nama_pegawai'] = $nama_pegawai;
		$_GET['randString'] = $randString;
		$_GET['id'] = !empty($_GET['id'] && !is_numeric($_GET['id'])) ? DocoHelpers::decrypt($_GET['id']) : $_GET['id'];
		$get = $request->get();
		Yii::$app->session->setFlash($randString, $get);
		return $this->renderAjax('_modalDetail', get_defined_vars());
	}

	public function actionProcessSync($randString)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$userIdentity = Yii::$app->session->get('user_identity');
		return $this->guzzleExec($this->_restKasir, [
			'url' => "inf-gabung-invoice/cetak-detail",
			'payload' => [
				'nama_pegawai' => isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : '',
				'query' => Yii::$app->session->getFlash($randString)
			],
		]);
	}

	public function actionDownloadInvoice()
	{
		$request = Yii::$app->request;
		$fileName = $request->get('fileName', null);
		$path = Yii::getAlias("@download").'/'.$fileName;
		$response = $this->_restKasir->get('inf-gabung-invoice/download-invoice',
		[
			'query' => [
				'fileName' => $fileName,
			],
			'save_to' => $path,
		]);
		$response = json_decode($response->getBody(), true);
		return DocoHelpers::previewPdf($path);
	}

	public function actionCetakKwitansi($id)
    {
        $title = 'Cetak Kwitansi';
        $request = Yii::$app->request;
        $model = new KwitansiGabungForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $jenis_kwitansi = [1 => 'Kwitansi Lengkap', 2 => 'Kwitansi Pasien', 3 => 'Kwitansi Penjamin'];
		if(!is_numeric($id)) {
			$id = DocoHelpers::decrypt($id);
		}
        if($request->post()) {
            $model->load($request->post());
            $model->invoicegabung_id = $id;
			$model->invoiceGabungIdEncrypt = DocoHelpers::encrypt($id);
            if(!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
            return DocoHelpers::response(['data' => $model->attributes]);
        }
        else {
            return $this->renderAjax('_cetak_kwitansi_gabung', get_defined_vars());
        }
    }

	public function actionGenerateKwitansi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $diterima_dari = $request->get('diterima_dari', null);
        $jenis_kwitansi = $request->get('jenis_kwitansi', null);
        $keterangan = $request->get('keterangan', null);
        $params = [
            'id' => DocoHelpers::decrypt($id),
            'diterima_dari' => $diterima_dari,
            'jenis_kwitansi' => $jenis_kwitansi,
            'keterangan' => $keterangan,
        ];
		
        $path = Yii::getAlias("@download") . "/kwitansi-sudah-bayar.pdf";
        $response = $this->_restKasir->get('inf-gabung-invoice/cetak-kwitansi', [
            'query' => $params,
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

	private function listNoPembayaran()
	{
		$cache = Yii::$app->cache;
		$cacheName = $this->getPrefixCache();
		$cacheData = $cache->get($cacheName);
		$list_no_invoice = [];
		if(!empty($cacheData)){
			foreach($cacheData as $key => $value){
				$no_invoice = !empty($value['no_invoice']) ? $value['no_invoice'] : null;
				$list_no_invoice[] = $no_invoice;
			}
		}
		return $list_no_invoice;
	}

	private function getPrefixCache(){
		$userIdentity = Yii::$app->session->get('user_identity');
		$pegawai_id = $userIdentity['id_pegawai'];
		$cacheName = 'invoice-gabung-'.$pegawai_id;
		return $cacheName;
	}

	public function actionClearInvoiceCache(){
		$this->actionDeleteCache(null);
		return DocoHelpers::responseTemplate(200, '', []);
	}

	public function actionFiltersDropdown() 
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => 'allow/filters',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

	public function actionBatalInvoice()
	{
		$request = Yii::$app->request;
		$response = $this->_restKasir->post('inf-gabung-invoice/batal-invoice',[
			'form_params' => [
				'invoicegabung_id' => $request->post('invoicegabung_id'),
				'is_batal' => $request->post('is_batal'),
			]
		]);
		$response = json_decode($response->getBody(),true);
		return DocoHelpers::response($response);
	}

	public function actionDetailInvoice()
	{
		$title = 'Detail Nomor Invoice Gabungan';
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		$response = $this->_restKasir->post('inf-gabung-invoice/detail-invoice',[
			'form_params' => [
				'invoicegabung_id' => !empty($id && !is_numeric($id)) ? DocoHelpers::decrypt($id) : $id,
			]
		]);
		$response = json_decode($response->getBody(), true);
		$total = isset($response['response']) ? $response['response'] : 'Rp. 0';

		return $this->renderAjax('_modalDetailInvoice', get_defined_vars());
	}

	public function actionDetailInvoiceGetData()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		$response = $this->_restKasir->post('inf-gabung-invoice/detail-invoice-get-data',[
			'form_params' => [
				'invoicegabung_id' => !empty($id && !is_numeric($id)) ? DocoHelpers::decrypt($id) : $id,
			]
		]);

		$response = json_decode($response->getBody(), true);
		if (isset($response['response'])) {
			foreach ($response['response']['data'] as $key => $value) {
				$pasien = '';
				if (isset($value['nama_pasien']) && isset($value['no_rekam_medik'])) {
					$pasien = $value['nama_pasien'].' / '.$value['no_rekam_medik'];
				}

				$nilai_invoice = '';
				if (isset($value['total_invoice']) ) {
					$nilai_invoice = DocoHelpers::rupiahDisplay($value['total_invoice'], false);
				}

				$tanggal_invoice = '';
				if (isset($value['tgl_invoicegabung']) ) {
					$tanggal_invoice = DocoHelpers::convDateTime($value['tgl_invoicegabung'], false, false);
				}
				
				
				$value['pasien'] = $pasien; 
				$value['nilai_invoice'] = $nilai_invoice; 
				$value['tanggal_invoice'] = $tanggal_invoice; 

				$response['data'][$key] = $value; 
			}
			$response['recordsTotal'] = isset($response['response']['_meta']['totalCount']) ? $response['response']['_meta']['totalCount'] : 0;
			$response['recordsFiltered'] = isset($response['response']['_meta']['totalCount']) ? $response['response']['_meta']['totalCount'] : 0;

			return json_encode($response);
		} else {
			return json_encode([
				"data" => [],
				'recordsTotal' => 0,
				'recordsFiltered' => 0
			]);
		}
	}

	public function actionShowPopupCetakan()
    {
        $title = 'Jenis Detail Invoice';
		$request = Yii::$app->request;
		$get = $request->get();
		$model = new \yii\base\DynamicModel(['jenis_invoice', 'id', 'invoice_id', 'penjamin_id', 'nama_pegawai', 'pembayaranpelayanan_id']);
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		$model
			->addRule(['jenis_invoice', 'id', 'invoice_id', 'penjamin_id', 'pembayaranpelayanan_id'], 'integer')
			->addRule(['jenis_invoice'], 'required');
		
		$model->attributes = $get;
		$model->jenis_invoice = 1;
		$jenis_invoice = [1 => 'Lengkap', 2 => 'Pasien', 3 => 'Penjamin'];
		$groupUmum = DocoConstants::GROUP_UMUM;
		$pembayaran_id = $request->get('ref_pembayaran_id', null);
		$pendaftaran_id = $request->get('id', null);
		$penjamin_id = $request->get('penjamin_id', null);
		$groupcarabayar_id = $request->get('groupcarabayar_id', null);
        $invoicegabung_id = $request->get('invoicegabung_id', null);
		$pathJs = 'js/invoice-designer.js';
		if(!empty($invoicegabung_id) && !is_numeric($invoicegabung_id)) {
			$invoicegabung_id = DocoHelpers::decrypt($invoicegabung_id);
		}
		if(!empty($pendaftaran_id) && !is_numeric($pendaftaran_id)) {
			$pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
		}
		if(!empty($penjamin_id) && !is_numeric($penjamin_id)) {
			$penjamin_id = DocoHelpers::decrypt($penjamin_id);
		}
		if(!empty($groupcarabayar_id) && !is_numeric($groupcarabayar_id)) {
			$groupcarabayar_id = DocoHelpers::decrypt($groupcarabayar_id);
		}
        $listPenjamin = [];
		$listPenjamin = $this->guzzleExec(Yii::$app->docoRest->kasir, [
            'url' => 'inf-gabung-invoice/get-penjamin',
            'payload' => [
                'query' => [
                    'pembayaran_id' => $pembayaran_id
                ]
            ]
        ]);
		
		$listPenjamin = !empty($listPenjamin) ? $listPenjamin : [];
		if(empty($groupcarabayar_id)) {
			$groupcarabayar_id = isset($listPenjamin[0]['groupcarabayar_id']) ? $listPenjamin[0]['groupcarabayar_id'] : null;
		}
		if(!empty($listPenjamin)) {
			$listPenjamin = ArrayHelper::map($listPenjamin, 'penjamin_id', 'penjamin_nama');
		}
		$is_bgprocess = $request->get('is_bgprocess', true);
		$is_invoice = $request->get('is_invoice', false);
		if($is_invoice){
			$title = 'Jenis Invoice';
		}

		$userIdentity = Yii::$app->session->get('user_identity');
		$pegawaiId = ArrayHelper::getValue($userIdentity, 'id_pegawai');
		return $this->renderAjax('_form_detail_invoice', get_defined_vars());
    }

	public function actionCetakInvoice()
    {
		$request = Yii::$app->request;
        $id = $request->get('id');
        $invoiceId = $request->get('invoice_id');
        $invoiceGabungId = $request->get('invoicegabung_id');
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        if(!is_numeric($invoiceGabungId)) {
            $invoiceGabungId = DocoHelpers::decrypt($invoiceGabungId);
        }

        $userIdentity = Yii::$app->session->get('user_identity');
        $uid = !empty($userIdentity['id_pegawai']) ?  '&uid=' . $userIdentity['id_pegawai'] : ''; 
        $params = 'invoice_id=' . $invoiceId . '&invoicegabung_id=' . $invoiceGabungId . $uid;
        return Yii::$app->report->exec('invoice-gabung-summary?'.$params);
    }
}
