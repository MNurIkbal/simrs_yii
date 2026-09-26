<?php
// Author : Budi

namespace Doco\kasir\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;
use app\modules\kasir\models\GabungBillingForm;
use app\modules\kasir\models\CetakKwitansiForm;
use app\modules\kasir\models\BatalPembayaranForm;

class InfGabungBillingController extends DocoController
{
	protected $_title = "Informasi Gabung Tagihan";
	protected $_module = 'kasir/inf-gabung-billing/';
	protected $_restKasir;

	public function init()
	{
		parent::init();
		$this->_restKasir = Yii::$app->docoRest->kasir;
	}

	public function behaviors()
	{
		$behaviors = parent::behaviors();
		unset($behaviors['access']);
		unset($behaviors['verbs']);
		return $behaviors;
	}

	public function actionIndex()
	{
		$title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : $this->_title;
		$btnInvoice = Yii::$app->docoPlugin->execute($this, 'invoice');
		$btnDetailRincian = Yii::$app->docoPlugin->execute($this, 'button_detail_rincian');
		$btnDetailInvoice = Yii::$app->docoPlugin->execute($this, 'detail_invoice');
		return $this->render('index', get_defined_vars());
	}

	public function actionGetData()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$payload = DocoDatatableHelper::advancedFilterParam();
		$newResponseData = $data = $result = $arrTujuan = [];
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'inf-gabung-billing/index',
			'method' => 'get',
			'payload' => [
				'query' => $payload,
			]
		]);
		$responseData = isset($response['data']) ? $response['data'] : [];
		if(!empty($responseData)) {
			foreach ($responseData as $key => $val) {
				$pendaftaran_id = ArrayHelper::getValue($val, 'pendaftaran_id');
				$ref_pendaftaran_id = ArrayHelper::getValue($val, 'ref_pendaftaran_id');
				$ref_no_pendaftaran = ArrayHelper::getValue($val, 'ref_no_pendaftaran');
				$arrTujuan[$pendaftaran_id] = [
					'pendaftaran_id' => ArrayHelper::getValue($val, 'pendaftaran_id'),
					'no_pendaftaran' => ArrayHelper::getValue($val, 'no_pendaftaran'),
					'penjamin' => ArrayHelper::getValue($val, 'penjamin'),
					'ref_pendaftaran_id' => $ref_pendaftaran_id,
					'ref_no_pendaftaran' => $ref_no_pendaftaran,
					'ref_penjamin' => ArrayHelper::getValue($val, 'ref_penjamin'),
				];
				$newResponseData[$pendaftaran_id] = $val;
			}
			
			foreach ($newResponseData as $pendaftaranId => $value) {
				$pembayaran_id = ArrayHelper::getValue($value, 'pembayaran_id');
				$instalasi_id = ArrayHelper::getValue($value, 'instalasi_id');
				$strNoPendaftaran = $strPenjamin = '';
				if(isset($arrTujuan[$pendaftaranId]['no_pendaftaran'])) {	
					$strNoPendaftaran = $arrTujuan[$pendaftaranId]['no_pendaftaran'];
				}
				if(isset($arrTujuan[$pendaftaranId]['penjamin'])) {	
					$strPenjamin = $arrTujuan[$pendaftaranId]['penjamin'] ;
				}
				
				$primaryKey = DocoHelpers::encrypt($value['ref_pendaftaran_id']);
				$value['primary'] = $primaryKey;
				$value['ref_no_pendaftaran'] = $strNoPendaftaran;
				$value['no_pendaftaran'] = $ref_no_pendaftaran;
				$value['ref_penjamin'] = $strPenjamin;
				$value['pembayaran_id'] = $pembayaran_id;
				$value['penjamin'] = isset($arrTujuan[$pendaftaranId]['ref_penjamin']) ? $arrTujuan[$pendaftaranId]['ref_penjamin'] : null;
				$value['pendaftaran_id'] = isset($arrTujuan[$pendaftaranId]['pendaftaran_id']) ? $arrTujuan[$pendaftaranId]['pendaftaran_id'] : null;
				$value['no_pendaftaran'] = isset($arrTujuan[$pendaftaranId]['ref_no_pendaftaran']) ? $arrTujuan[$pendaftaranId]['ref_no_pendaftaran'] : null;
				$value['ref_pendaftaran_id'] = isset($arrTujuan[$pendaftaranId]['ref_pendaftaran_id']) ? $arrTujuan[$pendaftaranId]['ref_pendaftaran_id'] : null;
            $value['ref_pendaftaran_id_encrypt'] = DocoHelpers::encrypt($ref_pendaftaran_id);
				$value['pembayaran_id_encrypt'] = DocoHelpers::encrypt($pembayaran_id);
				$value['instalasi_id_encrypt'] = DocoHelpers::encrypt($instalasi_id);
				$data[] = $value;
			}
		}
		$result['data'] = $data;
		$result['recordsTotal'] = count($data);
		$result['recordsFiltered'] = count($data);
		return $result;
	}

	public function actionTambah()
	{
		$request = Yii::$app->request;
		$title = 'Gabung Tagihan';
		$model = new GabungBillingForm;
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		$cacheName = $this->getCacheName();
		$cache = Yii::$app->cache;
		$cache->set($cacheName, []);
		$roleBtnSimpan = DHtml::cekHakAkses('simpan');
      $roleBtnSimpan = ($roleBtnSimpan) ? '' : 'display:none';
		return $this->renderAjax('_modal', get_defined_vars());
	}

	public function actionFilters()
	{
		return $this->guzzleExec($this->_restKasir, [
			'url' => 'inf-gabung-billing/filters',
			'payload' => [
				'query' => Yii::$app->request->get()
			],
			'returnResponse' => true
	  	]);
	}

	public function actionDetailTagihan()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$no_pendaftaran = $request->get('no_pendaftaran', null);
		$no_pendaftaran_tujuan = $request->get('no_pendaftaran_tujuan', null);
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'inf-gabung-billing/detail-tagihan',
			'method' => 'get',
			'payload' => [
				'query' => [
					'no_pendaftaran' => $no_pendaftaran,
					'no_pendaftaran_tujuan' => $no_pendaftaran_tujuan,
				],
			]
		]);
		$cacheName = $this->getCacheName();
		$cache = Yii::$app->cache;
		$cache->set($cacheName, $response);
		$total_dijamin = isset($response['total_dijamin']) ? $response['total_dijamin'] : 0;
		$total_dibayar = isset($response['total_dibayar']) ? $response['total_dibayar'] : 0;
		$total_tagihan = isset($response['total_tagihan']) ? $response['total_tagihan'] : 0;
		$result = [
			'total_dijamin' => $total_dijamin,
			'total_dibayar' => $total_dibayar,
			'total_tagihan' => $total_tagihan,
		];
		return DocoHelpers::response($result);
	}

	public function actionDetailPendaftaran()
	{
		$request = Yii::$app->request;
		$tipe = $request->get('tipe', 1);
		$cacheName = $this->getCacheName();
		$cache = Yii::$app->cache;
		if($tipe == 3) {
			$cacheData = [];
		}
		else {
			$cacheData = $cache->get($cacheName);
		}
		$result = [];
		$data = [];
		$draw = $request->get('draw', 1);
		$result['data'] = $data;
		$result['draw'] = $draw;
		$result['recordsTotal'] = 0;
		$result['recordsTotal'] = 0;
		$no = 0;
		if(!empty($cacheData)) {
			$dataPendaftaran = isset($cacheData['data_pendaftaran']) ? $cacheData['data_pendaftaran'] : [];
			$dataPendaftaranTujuan = isset($cacheData['data_pendaftaran_tujuan']) ? $cacheData['data_pendaftaran_tujuan'] : [];
			$responseData = ($tipe == 1) ? $dataPendaftaranTujuan : $dataPendaftaran;
			foreach ($responseData as $key => $value) {
				$no++;
				$tgl_pendaftaran = isset($value['tgl_pendaftaran']) ? $value['tgl_pendaftaran'] : '';
				$no_pendaftaran = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
				$instalasi = isset($value['instalasi_ruangan']) ? $value['instalasi_ruangan'] : '';
				$tindakan = isset($value['tindakan_obat']) ? $value['tindakan_obat'] : '';
				$penjamin = isset($value['penjamin']) ? $value['penjamin'] : '';
				$sub_total = isset($value['sub_total']) ? $value['sub_total'] : 0;
				$tarif_satuan = isset($value['harga']) ? $value['harga'] : 0;
				$tarif_cyto = isset($value['cyto']) ? $value['cyto'] : 0;
				$discount = isset($value['diskon']) ? $value['diskon'] : 0;
				$dijamin = isset($value['tarif_dijamin']) ? $value['tarif_dijamin'] : 0;
				$dibayar_pasien = isset($value['tarif_dibayarkan']) ? $value['tarif_dibayarkan'] : 0;
				$value['no_pendaftaran'] = $no_pendaftaran;
				$value['tgl_pendaftaran'] = !empty($tgl_pendaftaran) ? date('d M Y', strtotime($tgl_pendaftaran)) : '-';
				$value['instalasi'] = $instalasi;
				$value['tindakan'] = $tindakan;
				$value['tarif_satuan'] = $tarif_satuan;
				$value['tarif_cyto'] = $tarif_cyto;
				$value['discount'] = $discount;
				$value['sub_total'] = $sub_total;
				$value['penjamin'] = $penjamin;
				$value['dijamin'] = $dijamin;
				$value['dibayar_pasien'] = $dibayar_pasien;
				$data[$key] = $value;
			}
		}
		$result['data'] = $data;
		$result['draw'] = $draw;
		$result['recordsTotal'] = count($data);
		$result['recordsTotal'] = count($data);
		return DocoHelpers::response($result);
	}

	private function getCacheName()
	{
		$userIdentity = Yii::$app->session->get('user_identity');
		$pegawai_id = $userIdentity['id_pegawai'];
		$cacheName = 'gabung-billing-'.$pegawai_id;
		return $cacheName;
	}

	public function actionDetailBilling()
	{
		$request = Yii::$app->request;
		$id = $request->get('id', null);
		$pembayaran_id = $request->get('pembayaran_id', null);
		if(!is_numeric($id)) {
			$id = $this->helper->decrypt($id);
		}
		if(!is_numeric($pembayaran_id)) {
			$pembayaran_id = $this->helper->decrypt($pembayaran_id);
		}
		$title = 'Detail Gabung Tagihan';
		$response = $this->guzzleExec($this->_restKasir, [
			'url' => 'inf-gabung-billing/summary-detail-gabung',
			'method' => 'get',
			'payload' => [
				'query' => [
					'pendaftaran_id' => $id,
					'pembayaran_id' => $pembayaran_id,
				],
			]
		]);
		$sub_total = isset($response['sub_total']) ? $response['sub_total'] : 0;
		$tarif_dijamin = isset($response['tarif_dijamin']) ? $response['tarif_dijamin'] : 0;
		$tarif_dibayarkan = isset($response['tarif_dibayarkan']) ? $response['tarif_dibayarkan'] : 0;
		return $this->renderAjax('_modalDetail', get_defined_vars());
	}

	public function actionDataDetailGabung()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$pendaftaran_id = $request->get('pendaftaran_id', null);
		$pembayaran_id = $request->get('pembayaran_id', null);
		if(!is_numeric($pendaftaran_id)) {
			$pendaftaran_id = $this->helper->decrypt($pendaftaran_id);
		}
		if(!is_numeric($pembayaran_id)) {
			$pembayaran_id = $this->helper->decrypt($pembayaran_id);
		}
		$yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
		$draw = $request->get('draw', 1);
		$data = [];
		$result = [];
		$result['data'] = $data;
		$result['draw'] = $draw;
		$result['recordsTotal'] = 0;
		$result['recordsTotal'] = 0;
		$yiiRestfulParams['pendaftaran_id'] = $pendaftaran_id;
		$yiiRestfulParams['pembayaran_id'] = $pembayaran_id;
		try {
			$response = $this->_restKasir->get('inf-gabung-billing/detail-gabung?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
			$body = json_decode($response->getBody(), True);
			$no = $request->get('start',1);
			foreach ($body['response']['data'] as $key => $value) {
				$value['rowNum'] = $no;
				$primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
				$value['primary'] = $primaryKey;
				$instalasi = isset($value['instalasi_ruangan']) ? $value['instalasi_ruangan'] : '';
				$tindakan = isset($value['tindakan_obat']) ? $value['tindakan_obat'] : '';
				$penjamin = isset($value['penjamin']) ? $value['penjamin'] : '';
				$sub_total = isset($value['sub_total']) ? $value['sub_total'] : 0;
				$tarif_satuan = isset($value['harga']) ? $value['harga'] : 0;
				$tarif_cyto = isset($value['cyto']) ? $value['cyto'] : 0;
				$discount = isset($value['diskon']) ? $value['diskon'] : 0;
				$dijamin = isset($value['tarif_dijamin']) ? $value['tarif_dijamin'] : 0;
				$dibayar_pasien = isset($value['tarif_dibayarkan']) ? $value['tarif_dibayarkan'] : 0;
				$tgl_pendaftaran = !empty($value['tgl_pendaftaran']) ? date('d-M-Y', strtotime($value['tgl_pendaftaran'])) : '-';
				
				$tgl_pelayanan = '-';
				if(isset($value['tgl_transaksi']) && !empty($value['tgl_transaksi'])) {
					$tgl_pelayanan = date('d-M-Y', strtotime($value['tgl_transaksi']));
				}
				if(isset($value['tgl_pelayanan']) && !empty($value['tgl_pelayanan'])) {
					$tgl_pelayanan = date('d-M-Y', strtotime($value['tgl_pelayanan']));
				}
				if(isset($value['instalasi_pelayanan'])) {
					$instalasiNama = $value['instalasi_pelayanan'];
				}
				if(isset($value['ruangan_pelayanan'])) {
					$ruanganNama = $value['ruangan_pelayanan'];
				}
				if(isset($value['tindakan_obat_nama'])) {
					$tindakan = $value['tindakan_obat_nama'];
				}
				if(isset($value['tarif_satuan'])) {
					$tarif_satuan = $value['tarif_satuan'];
				}
				if(isset($value['tarif_cyto'])) {
					$tarif_cyto = $value['tarif_cyto'];
				}
				if(isset($value['tarif_diskon'])) {
					$discount = $value['tarif_diskon'];
				}
				if(isset($value['penjamin_tinpelayanan'])) {
					$penjamin = $value['penjamin_tinpelayanan'];
				}
				if(!empty($instalasiNama) && !empty($ruanganNama)) {
					$instalasi = $instalasiNama.' - '.$ruanganNama;
				}

				$value['instalasi'] = $instalasi;
				$value['tindakan'] = $tindakan;
				$value['penjamin'] = $penjamin;
				$value['tgl_pendaftaran'] = $tgl_pendaftaran;
				$value['tgl_pelayanan'] = $tgl_pelayanan;
				$value['tarif_satuan'] = DocoHelpers::formatNumber($tarif_satuan);
				$value['tarif_cyto'] = DocoHelpers::formatNumber($tarif_cyto);
				$value['sub_total'] = DocoHelpers::formatNumber($sub_total);
				$value['dijamin'] = DocoHelpers::formatNumber($dijamin);
				$value['dibayar_pasien'] = DocoHelpers::formatNumber($dibayar_pasien);
				$value['discount'] = DocoHelpers::formatNumber($discount);
				$data[$key] = $value;
			}
			$result['data'] = $data;
			$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
			$result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
			return $result;
		} catch (RequestException $e) {
			return DocoHelpers::dataTabelsException($e->getMessage());
		} catch (\Exception $e) {
			return DocoHelpers::dataTabelsException($e->getMessage());
		}
	}

	public function actionSimpanGabungBilling()
	{
		$request = Yii::$app->request;
		$model = new GabungBillingForm;
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		if($request->post()) {
			$post = $request->post('GabungBillingForm');
			$model->attributes = $post;
			if($model->validate()) {
				try {
					$response = $this->_restKasir->post('inf-gabung-billing/simpan',[
						'form_params' => $model->attributes
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
	}

	public function actionCetakKwitansi($id, $pembayaran_id)
   {
		$title = 'Cetak Kwitansi';
		$request = Yii::$app->request;
		$model = new CetakKwitansiForm;
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		$jenis_kwitansi = [1 => 'Kwitansi Total', 2 => 'Kwitansi Penjamin', 3 => 'Kwitansi Pasien'];
		if($request->post()) {
			$model->load($request->post());
			$model->pembayaran_id = $pembayaran_id;
			if(!$model->validate()) {
					$errors = DocoHelpers::parseError($model->errors, $formName);
					return DocoHelpers::responseTemplate(422, 'Error', $errors);
			}

			return DocoHelpers::response(['data' => $model->attributes]);
		}
		else {
			return $this->renderAjax('_cetak_kwitansi', get_defined_vars());
		}
   }

	public function actionGenerateKwitansi()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$id = $request->get('id', null);
		if($id == 'null') {
			$id = '';
		}
		$pembayaran_id = $request->get('pembayaran_id', null);
		$jenis_kwitansi = $request->get('jenis_kwitansi', null);
		$diterima_dari = $request->get('diterima_dari', null);
		$keterangan = $request->get('keterangan', null);
		$userIdentity = Yii::$app->session->get('user_identity');
		$params = [
			'id' => (!is_numeric($id)) ? DocoHelpers::decrypt($id) : $id,
			'pembayaran_id' => (!is_numeric($pembayaran_id)) ? DocoHelpers::decrypt($pembayaran_id) : $pembayaran_id,
			'jenis_kwitansi' => $jenis_kwitansi,
			'diterima_dari' => $diterima_dari,
			'keterangan' => $keterangan,
			'nama_pegawai' => $userIdentity['nama_pegawai'],
		];
		$path = Yii::getAlias("@download") . "/kwitansi-sudah-bayar.pdf";
		$response = $this->_restKasir->get('lap-pasien-sudah-bayar/print-kwitansi', [
			'query' => $params,
			'save_to' => $path
		]);
		$body = json_decode($response->getBody(), true);
		return DocoHelpers::previewPdf($path);
	}

	public function actionBatal()
	{
		$request = Yii::$app->request;
		$id = $request->get('id', null);
		$gabungpelayanandetail_id = $request->get('gabungpelayanandetail_id', null);
		$title     = Yii::t('fe', 'Batal Gabung Tagihan');
		$batalForm = new BatalPembayaranForm;
		$formName = substr(strrchr(get_class($batalForm), "\\"), 1);
		$username = Yii::$app->docoVars->user('nama');
		if ($request->post()) {
			$batalForm->load($request->post());
			$batalForm->tanggal_batal = date('Y-m-d');
			if ($batalForm->validate()) {
					if(!empty($batalForm->id)) {
						$id = DocoHelpers::decrypt($batalForm->id);
						$batalForm->id = $id;
					}
					try {
						$response = $this->_restKasir->get('inf-gabung-billing/batal', [
							'query' => [
								'id' => $id,
								'gabungpelayanandetail_id' => $batalForm->gabungpelayanandetail_id,
								'alasan_batal' => $batalForm->alasan_batal,
								'password' => $batalForm->password,
							]
						]);
						$response = json_decode($response->getBody(),true);
						return DocoHelpers::response($response);
					} catch (RequestException $e) {
						$response = json_decode($e->getResponse()->getBody(),true);
						\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
						\Yii::$app->response->statusCode =500;
						return ['response'=>[
							'title'=>'Terjadi Kesalahan',
							'message'=>'gagal',
							'text'=>$response['response']
						]];
					} catch (\Exception $e) {
						return DocoHelpers::responseTemplate(422, 'Error', $e->getMessage());

					}
			}else{
					$errors = DocoHelpers::parseError($batalForm->errors, $formName);
					return DocoHelpers::responseTemplate(422, 'Error', $errors);
			}
		}else{
			return $this->renderAjax('_form_batal', get_defined_vars());
		}
	}
}
