<?php 

namespace app\components\Traits;

use Yii;

use app\components\DocoConstants;
use app\components\DocoHelpers;
use app\components\models\TindakanForm;
use app\components\models\ObatForm;
use app\components\DocoDatatableHelper;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\Services\FarmasiService;
use yii\helpers\ArrayHelper;
use app\components\models\RujukanPenunjangForm;

trait TindakanPenunjangTrait
{
	/**
	* Render form order obat
	* 
	* @return Html
	* @author : Budi (budi@sirs.co.id)
	* Powered by Sirs
	*/

	public function actionOrderObat()
	{
		$request = Yii::$app->request;
		$instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
		$ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
		$id = DocoHelpers::decrypt($request->get('id', null));
		$pelayananId = DocoHelpers::decrypt($request->get('pelayananId', null));
		$tindakanId = DocoHelpers::decrypt($request->get('tindakanId', null));
		$pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id', null));
		$bundleData = $this->getDataAttributes($id, $pelayananId, $tindakanId);
		$model = new TindakanForm;
		$model->pemeriksaan_id = $pelayananId;
		$model->tanggal_tindakan = date('d/M/Y');
		$model->qty = 1;
		$modelObat = new ObatForm;
		$modelObat->qty = 1;
		$modelObat->depo_id = $ruangan_id;
		$endPoint = ($instalasi_id == DocoConstants::INSTALASI_ID_RAD) ? '/radiologi' : '/laboratorium';
		$urlHasil = ($instalasi_id == DocoConstants::INSTALASI_ID_RAD) ? '/hasil-rad' : '/hasil-lab';
		$path = '//penunjang/order-obat/__index';

		$arrayTindakan = $optionTindakanParent = $tmpOptionTindakanParent = [];
		$list_pemeriksaan = !empty($bundleData['list_pemeriksaan']) ? $bundleData['list_pemeriksaan'] : [];
		if(!empty($list_pemeriksaan)){
			foreach($list_pemeriksaan as $val){
				$tindakanpelayanan_id = !empty($val['tindakanpelayanan_id']) ? $val['tindakanpelayanan_id'] : null;
				$daftartindakan_id = !empty($val['daftartindakan_id']) ? $val['daftartindakan_id'] : null;
				$daftartindakan_nama = !empty($val['daftartindakan_nama']) ? $val['daftartindakan_nama'] : null;
				if($tindakanpelayanan_id == $pelayananId){
					$arrayTindakan[$tindakanId] = $val;
				}
				if(empty($tindakanId)){
					$arrayTindakan[$daftartindakan_id] = $val;
				}
			}
		}

		if(!empty($tindakanId)){
			$daftarTindakanNama = !empty($arrayTindakan[$tindakanId]['daftartindakan_nama']) ? $arrayTindakan[$tindakanId]['daftartindakan_nama'] : '';
			$optionTindakanParent = [
				"" . $tindakanId . "" => $daftarTindakanNama
			];
		}else{
			$optionTindakanParent = array_column($list_pemeriksaan, 'daftartindakan_nama', 'daftartindakan_id');
		}

        $instalasiPenunjang = [DocoConstants::INSTALASI_FISIOTERAPI, DocoConstants::INSTALASI_ID_LAB, DocoConstants::INSTALASI_ID_RAD];

		$params = [
			'id' => $id,
			'pelayananId' => $pelayananId,
			'tindakanId' => $tindakanId,
			'model' => $model,
			'modelObat' => $modelObat,
			'listInfo' => $bundleData['listInfo'],
			'list_pemeriksaan' => $bundleData['list_pemeriksaan'],
			'list_pegawai' => $bundleData['list_pegawai'],
			'status_periksa' => $bundleData['status_periksa'],
			'pendaftaran_id' => $pendaftaran_id,
			'endPoint' => $endPoint,
			'urlHasil' => $urlHasil,
			'bundleData' => $bundleData,
			'optionTindakanParent' => $optionTindakanParent,
			'isLab' => ($instalasi_id == DocoConstants::INSTALASI_ID_RAD) ? false : true,
			'arrayTindakan' => $arrayTindakan,
			'ruangan_id' => $ruangan_id,
			'instalasiPenunjang' => $instalasiPenunjang
		];
		$render = $this->renderAjax($path, $params);
		if($instalasi_id == DocoConstants::INSTALASI_ID_RAD) {
			$render = $this->render($path, $params);
		}
		return $render;
	}

	private function getDataAttributes($id, $pelayananId, $tindakanId)
	{
		$payload = [
         'pasienmasukpenunjang_id' => $id,
			'pelayanan_id' => $pelayananId,
			'tindakan_id' => $tindakanId,
      ];
		return $this->guzzleExec($this->serviceRest, [
			'url' => $this->backendUrl . '/bundle-data-attributes',
			'payload' => [
				'query' => $payload
			]
		]);
	}

	public function actionGetDataTindakanObat()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$payload = DocoDatatableHelper::advancedFilterParam();
		$pendaftaran_id = $request->get('pendaftaran_id');
		$jenis = $request->get('jenis');
		$pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id');
		$payload['pendaftaran_id'] = $pendaftaran_id;
		$payload['jenis'] = $jenis;
		$payload['pasienmasukpenunjang_id'] = $pasienmasukpenunjang_id;
		$response = $this->guzzleExec($this->serviceRest, [
			'url' => $this->backendUrl . "/get-data-tindakan-obat",
			'payload' => [
				'query' => $payload,
			]
		]);
		foreach ($response['data'] as $key => $value) {
			$primary = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
			$pendaftaran_id = DocoHelpers::encrypt($value['pendaftaran_id']);
			$obatalkespasien_id = DocoHelpers::encrypt($value['obatalkespasien_id']);
			$nama_pemeriksaan = isset($value['nama_pemeriksaan']) ? $value['nama_pemeriksaan'] : '';
			$nama_tindakan = isset($value['nama_tindakan']) ? $value['nama_tindakan'] : '';
			$tindakanNama = !empty($nama_tindakan) ? $nama_tindakan : $nama_pemeriksaan;
			$response['data'][$key]['primary'] = $primary;
			$response['data'][$key]['is_ditagihkan'] = ($value['is_ditagihkan']) ? '✓' : null;
			$response['data'][$key]['action'] = '<i class="fa fa-lock"></i>';
			$response['data'][$key]['nama_tindakan'] = ($value['jenis'] == 'tindakan') ? $nama_tindakan : $tindakanNama;
			$response['data'][$key]['harga_tindakan'] = isset($value['harga_tindakan']) ? DocoHelpers::formatNumber($value['harga_tindakan']) : null;
			if (!$value['is_sudahbayar']) {
				$params = 'tindakanpelayanan_id='.$primary;
				$class = 'delete';
				if($value['jenis'] == 'obat') {
					$params = 'obatalkespasien_id='.$obatalkespasien_id;
					$class = 'delete-obat';
				}
				$response['data'][$key]['action'] = Html::button(
					"<i class='fa fa-trash'></i>",[
						'class' => 'btn btn-danger btn-sm '.$class,
						'action' => Url::to([
							'/radiologi/order-obat-alkes/delete-tindakan-obat?'.$params
						]),
					]
				);
			}
		}
		$response['recordsTotal'] = $response['_meta']['totalCount'];
		$response['recordsFiltered'] = $response['_meta']['totalCount'];
		return $response;
	}

	public function actionFilters()
	{
		return $this->guzzleExec($this->serviceRest, [
			'url' => $this->backendUrl . '/filters',
			'payload' => [
				'query' => Yii::$app->request->get()
			],
			'returnResponse' => true
	  	]);
	}

	public function actionSimpan($id, $pelayananId = null)
	{
		$request = Yii::$app->request;
		$pelayananId = DocoHelpers::decrypt($pelayananId);
		if ($request->post()) {
			$jenis = $request->post('jenis', 'tindakan');
			$formName = ($jenis == 'tindakan') ? 'TindakanForm' : 'ObatForm';
			$model = ($jenis == 'tindakan') ? new TindakanForm : new ObatForm; 
			$model->load($request->post());
			if(!is_numeric($pelayananId)) {
				$pelayananId = DocoHelpers::decrypt($pelayananId);
			}
			if(!is_numeric($id)) {
				$id = DocoHelpers::decrypt($id);
			}
			if($jenis == 'obat') {
				$post = $request->post('ObatForm');
				$pelayananId = isset($post['tindakanpelayanan_id']) ? $post['tindakanpelayanan_id'] : null;
			}
			if(empty($pelayananId) && !empty($model->pemeriksaan_id)) {
				$pelayananId = $model->pemeriksaan_id;
			}
			$model->pemeriksaan_id = $pelayananId;
			$model->jenis = $jenis;
			if ($model->validate()) {
				try {
					$response = $this->serviceRest->post($this->backendUrl . '/simpan', [
						'form_params' => $model->attributes,
						'query' => [
							'id' => $id,
						]
					]);
               $response = json_decode($response->getBody(),true);
               return DocoHelpers::response($response, false, $formName);
				} catch (RequestException $e) {
					return DocoHelpers::response($e->getMessage(), 422);
				} catch (\Exception $e) {
					return DocoHelpers::response($e->getMessage(), 422);
				}
			} else {
				return DocoHelpers::response($model->errors, 422,$formName);
			}
		}
	}

	public function actionDeleteTindakanObat($tindakanpelayanan_id = null, $obatalkespasien_id = null)
	{
		$tindakanpelayanan_id = !empty($tindakanpelayanan_id) ? DocoHelpers::decrypt($tindakanpelayanan_id) : null;
		$obatalkespasien_id = !empty($obatalkespasien_id) ? DocoHelpers::decrypt($obatalkespasien_id) : null;
		try {
			$response = $this->serviceRest->delete($this->backendUrl . '/delete-tindakan-obat',
				[
					'query' => [
						'tindakanpelayanan_id' => $tindakanpelayanan_id,
						'obatalkespasien_id' => $obatalkespasien_id
					]
				]);
			$response = json_decode($response->getBody(),422);
			return DocoHelpers::response($response,false);
		} catch (RequestException $e) {
			return DocoHelpers::response([
					'text' => $e->getMessage()
			],422);
		} catch (\Exception $e) {
			return DocoHelpers::response([
					'text' => $e->getMessage()
			],422);
		}
	}

	public function actionListTindakan()
	{
		$request = Yii::$app->request;
		$get = $request->get();
		$api = $this->backendUrl . '/list-tindakan';
		$data_id = 'daftartindakan_id';
		$data_name = [                    
			'daftartindakan_nama'
		];
		$getRest = $this->serviceRest;
		$helpers = new DocoHelpers;
		return $helpers->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
	}

	public function actionListObat()
	{
		$request = Yii::$app->request;
		$ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
		$kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
		$depo_id = $request->get('depo_id', null);
		if(!$depo_id) {
			$depo_id = $ruangan_id;
		}
		$penjamin_id = $request->get('penjamin_id', null);
		$keyword = $request->get('term', null);
		$page = $request->get('page', 1);
		$instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
		$params = [
			// 'instalasi_id' => $instalasi_id,
            'instalasi_id' => $request->get('instalasi_id', null),
			'ruangan_id' => $depo_id,
			'penjamin_id' => $penjamin_id,
			'kelaspelayanan_id' => $kelaspelayanan_id,
			'page' => $page,
			'keyword' => $keyword,
	  	];
		return (new FarmasiService)->getListObat($params);
	}

	public function actionDataTindakanRuangan()
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $api = $this->backendUrl . '/tindakan-ruangan'; 
      $data_id = 'daftartindakan_id'; 
      $data_name = [                    
         'daftartindakan_kode',
         'daftartindakan_nama'
      ];
      $getRest = $this->serviceRest;
      return $this->helper->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }

	public function actionGetTarif()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$get = $request->get();
		if(!$get['daftartindakan_id']){
			return null;
		}
		return $this->guzzleExec($this->serviceRest, [
			'url' => $this->backendUrl . '/get-tarif',
			'payload' => [
				'query' => $get,
			]
		]);
   }

	public function actionListDepo()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$api = $this->backendUrl . '/get-depo'; 
		$data_id = 'ruangan_id'; 
		$data_name = [                    
			'ruangan_nama'
		];
		$getRest = $this->serviceRest;
		$get = $request->get();
		$instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
		$get['instalasi_id'] = $instalasi_id;
		return $this->helper->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
	}

	public function actionDataPemakaianTindakan()
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $api = $this->backendUrl . '/pemakaian-tindakan'; 
      $data_id = 'daftartindakan_id'; 
      $data_name = [                    
         'daftartindakan_nama'
      ];
      $getRest = $this->serviceRest;
      return $this->helper->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
   }
	public function actionFormModalTindakan()
   {
		$title = Yii::t('fe', 'Tambah Pemeriksaan');
		$result = [];
		$request = Yii::$app->request;
		$get = $request->get();
		$docoVars = Yii::$app->docoVars;
		$ruangan_id = $docoVars->workspace('ruangan_id') ? $docoVars->workspace('ruangan_id') : 1;
		$instalasi_id = $docoVars->workspace('instalasi_id') ? $docoVars->workspace('instalasi_id') : null;
		$get['instalasi_id'] = $instalasi_id;
		$pasienkirimkeunitlain_id = ArrayHelper::getValue($get, 'pasienkirimkeunitlain_id');
		$penjaminId = ArrayHelper::getValue($get, 'penjamin_id');
		if(!empty($penjaminId) && !is_numeric($penjaminId)) {
			$penjaminId = DocoHelpers::decrypt($penjaminId);
		}
		$kelasPelayananId = ArrayHelper::getValue($get, 'kelaspelayanan_id');
		if(!empty($kelasPelayananId) && !is_numeric($kelasPelayananId)) {
			$kelasPelayananId = DocoHelpers::decrypt($kelasPelayananId);
		}
		$module = ($instalasi_id == DocoConstants::INSTALASI_ID_RAD) ? 'radiologi' : 'laboratorium';
		$dataTindakan = $this->getListDataPemeriksaan($pasienkirimkeunitlain_id);
		$params = [
			'ruangan_id'        => $ruangan_id,
			'penjamin_id'       => $penjaminId,
			'kelaspelayanan_id' => $kelasPelayananId,
			'instalasi_id'      => $instalasi_id,
		];
		$endPoint = $this->backendUrl;
		$response = $this->guzzleExec($this->serviceRest, [
			'url' => $endPoint.'/get-tarif-tindakan',
			'payload' => [
				'query' => $params
			]
		]);
		$dataJenis = [];
		$countData = 0;
		$responseData = ArrayHelper::getValue($response, 'data', []);
		if(!empty($responseData)) {
			$countData = count($responseData);
			foreach ($responseData as $key => $value) {
				$jenisPemeriksaanLabId = ArrayHelper::getValue($value, 'jenispemeriksaanlab_id');
				$jenisPemeriksaanLabNama = ArrayHelper::getValue($value, 'jenispemeriksaanlab_nama');
				if(!empty($jenisPemeriksaanLabNama)) {
					$result[$jenisPemeriksaanLabNama][] = $value;
					$dataJenis[] = [
						'id' => $jenisPemeriksaanLabId,
						'text' => $jenisPemeriksaanLabNama,
					];
				}
			}
			if(!empty($dataJenis)) {
				$dataJenis = ArrayHelper::map($dataJenis, 'id', 'text');
			}
		}
		return $this->renderAjax('//penunjang/order-tindakan/__modal_order_tindakan', get_defined_vars());
   }

	public function actionSimpanTindakan()
	{
		$request = Yii::$app->request;
		$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
		if($request->post()) {
			$postData = $request->post();
			if(!empty($pasienkirimkeunitlain_id) && !empty($request->post())) {
				$response = $this->serviceRest->get($this->backendUrl.'/simpan-tindakan', [
					'query' => [
						'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
					],
					'form_params' => $postData,
				]);
				$body = json_decode($response->getBody(), true);
				return DocoHelpers::response($body);
			}
		}
	}

	public function actionGetDataOrderPemeriksaan()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
		$yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
		$yiiRestfulParams['pasienkirimkeunitlain_id'] = $pasienkirimkeunitlain_id;
		$data = $listDokter = [];
		$dokterList = $this->getListDokter();
		if(!empty($dokterList['data'])) {
			$listDokter = ArrayHelper::map($dokterList['data'], 'id','text');
		}
		try {
			$response = $this->serviceRest->get($this->backendUrl.'/get-data-pemeriksaan', [
				'query' => $yiiRestfulParams
			]);
			$body = json_decode($response->getBody(), True);
			$no = $request->get('start', 1);
			foreach ($body['response']['data'] as $key => $value) {
				$no++;
				$permintaanKePenunjangId = ArrayHelper::getValue($value, 'permintaankepenunjang_id');
				$daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
				$daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
				$pasienKirimKeUnitLainId = ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id');
				$isCito = ArrayHelper::getValue($value, 'is_cyto', false);

				$primaryKey = DocoHelpers::encrypt($permintaanKePenunjangId);
				$value['primary'] = $primaryKey;
				$value['rowNum'] = $no;
				$value['is_cyto'] = ($isCito) ? '&#10004;' : '';
				$value['dokter'] = Html::dropDownList('dokter', null, $listDokter, [
					'prompt' => Yii::t('fe', 'Pilih Dokter'),
					'class' => 'select2 form-control input-xs'
				]);
				$value['approve'] = Html::checkbox('approve', true, [
					'label' => false,
					'class' => 'approveCheck',
					'data-value' => $daftarTindakanId,
					'data-idpenunjang' => $permintaanKePenunjangId,
					'data-idunitlain' => $pasienKirimKeUnitLainId,
					'data-namatindakan' => $daftarTindakanNama
				]);
				$value['diagnosa'] = Html::dropDownList('diagnosa', null, [], [
					'prompt' => Yii::t('fe', 'Pilih Diagnosa'),
					'class' => 'select2 form-control input-xs diagnosa'
				]);
				$value['dirujuk'] = Html::checkbox('dirujuk', true, [
					'label' => false,
					'class' => 'rujukCheck',
					'data-value' => $daftarTindakanId,
					'data-idpenunjang' => $permintaanKePenunjangId,
					'data-idunitlain' => $pasienKirimKeUnitLainId,
					'data-namatindakan' => $daftarTindakanNama
				]);
				$data[$key] = $value;
			}
			$return = [
				'data' => $data,
				'draw' => $request->get('draw'),
				'recordsTotal' => isset($body['response']['_meta']) ? $body['response']['_meta']['totalCount'] : count($body['response']['data']),
				'recordsFiltered' => isset($body['response']['_meta']) ? $body['response']['_meta']['totalCount'] : count($body['response']['data']),
			];
			return DocoHelpers::response($return);
		} catch (RequestException $e) {
			$result['error'] = $e->getMessage();
			return $result;
		} catch (\Exception $e) {
			$result['error'] = $e->getMessage();
			return $result;
		}
	}

	private function getListDokter()
	{
		return $this->helper->guzzleExec($this->serviceRest, [
			'url' => $this->backendUrl.'/list-dokter',
			'returnResponse' => true,
			'payload' => [
				'query' => Yii::$app->request->get('payload', [])
			],
		]);
	}

	public function actionSearch()
	{
		$result = [];
		$request = Yii::$app->request;
		
		$ruanganId = $request->get('ruangan_id', null);
		$penjaminId = $request->get('penjamin_id', null);
		$kelasId = $request->get('kelaspelayanan_id', null);
		$instalasiId = $request->get('instalasi_id', null);
		$term = $request->get('daftartindakan_nama', null);
		$jenisPemeriksaanId = $request->get('jenispemeriksaanlab_id', null);
		try {
			if($instalasiId == DocoConstants::INSTALASI_ID_LAB || $instalasiId == DocoConstants::INSTALASI_ID_RAD || $instalasiId == DocoConstants::INSTALASI_ID_BEDAH){
				$params = [
					'ruangan_id'        => $ruanganId,
					'penjamin_id'       => $penjaminId,
					'kelaspelayanan_id' => $kelasId,
					'instalasi_id'      => $instalasiId,
					'daftartindakan_nama' => $term,
					'jenispemeriksaanlab_id'      => $jenisPemeriksaanId,
				];
				
				$url = $this->backendUrl.'/get-tarif-tindakan';
				$response = $this->serviceRest->get($url, ['query' => $params]);
				$body = json_decode($response->getBody(), true);
				$body = isset($body['response']) ? $body['response'] : [];
				foreach ($body['data'] as $key => $value) {
					$result[$value['jenispemeriksaanlab_nama']][] = $value;
				}
			}
			return json_encode($result);
		} catch (RequestException $e) {
			return DocoHelpers::response(['message' => $e->getMessage()],500);
		} catch (\Exception $e) {
			return DocoHelpers::response(['message' => $e->getMessage()],500);
		}
	}

	private function getListDataPemeriksaan($pasienkirimkeunitlain_id)
	{
		$result = [];
		$response = $this->guzzleExec($this->serviceRest, [
			'url' => $this->backendUrl.'/get-list-data-pemeriksaan',
			'payload' => [
				'query' => [
					'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
				]
			]
		]);
		if(!empty($response)) {
			if(is_array($response)) {
				foreach ($response as $key => $value) {
					$daftartindakanId = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
					if(!empty($daftartindakanId)) {
						$result[$daftartindakanId] = $daftartindakanId;
					}
				}
			}
		}
		return $result;
	}

	public function actionDeletePemeriksaan()
	{
		$request = Yii::$app->request;
		$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
		if($request->post()) {
			$postData = $request->post();
			if(!empty($pasienkirimkeunitlain_id) && !empty($request->post())) {
				$response = $this->serviceRest->get($this->backendUrl.'/delete-pemeriksaan', [
					'query' => [
						'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
					],
					'form_params' => $postData,
				]);
				$body = json_decode($response->getBody(), true);
				return DocoHelpers::response($body);
			}
		}
	}

	public function actionRujuk($id)
	{
		$request = Yii::$app->request;
		$instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
		$instalasiNama = ($instalasi_id == DocoConstants::INSTALASI_ID_RAD) ? 'Radiologi' : 'Laboratorium';
		$instalasiId = DocoHelpers::encrypt($instalasi_id);
		$instalasi = $instalasiNama;
		$path = ($instalasi_id == DocoConstants::INSTALASI_ID_RAD) ? 'form_rujuk' : 'form_rujuk_lab';
		$url = $this->backendUrl.'/generate-api';
		$urlSimpan = $this->backendUrl.'/proses-rujuk';
		$title = 'Rujukan Pemeriksaan '.$instalasi;
		if(!is_numeric($id)) {
			$id = DocoHelpers::decrypt($id);
		}
		
      $model = new RujukanPenunjangForm;
		$response = $this->guzzleExec($this->serviceRest, [
			'url' => $url,
			'payload' => [
				'query' => [
					'id' => $id,
				]
			]
		]);
		$detail = ArrayHelper::getValue($response, 'labDetail', []);
		$pemeriksaan = ArrayHelper::getValue($response, 'listPemeriksaan', []);
		$diagnosaUtama = ArrayHelper::getValue($response, 'diagnosa_utama', []);
		$dokterRujukId = ArrayHelper::getValue($detail, 'pegawai_id');
		$dokterPerujuk = ArrayHelper::getValue($detail, 'dokter_perujuk');
		$diagnosaUtamaId = ArrayHelper::getValue($diagnosaUtama, 'diagnosa_utama_id');
		$diagnosaUtamaNama = ArrayHelper::getValue($diagnosaUtama, 'diagnosa_utama_nama');

		if($request->post()) {
			$model->load($request->post());
			$detail = $request->post('detail', []);
			$model->detail_tindakan = $detail;
			$model->pasienkirimkeunitlain_id = DocoHelpers::decrypt($request->post('pasienkirimkeunitlain_id'));
			if(!$model->validate()) {
				return DocoHelpers::response($model->errors, 422, 'RujukanPenunjangForm');
			}
			$response = $this->serviceRest->post($urlSimpan, [
				'form_params' => $model->attributes
		  	]);
		  	$response = json_decode($response->getBody(),true);
			return DocoHelpers::response($response);
		}
		else {
			return $this->render('//penunjang/'.$path, get_defined_vars());
		}
	}

	public function actionCetakRujukan()
	{
		$request = Yii::$app->request;
		$id = $request->get('id');
		$penunjang_id = DocoHelpers::decrypt($request->get('penunjang_id'));
		$instalasi_id = $request->get('instalasi_id');
		$rujukankeluar_id = $request->get('rujukankeluar_id');
		$id = !is_numeric($id) ? DocoHelpers::decrypt($id) : $id;
		$instalasi_id = !is_numeric($instalasi_id) ? DocoHelpers::decrypt($instalasi_id) : $instalasi_id;
		$url = $this->backendUrl.'/cetak-rujukan';
		$path = Yii::getAlias("@download")."/cetak-rujukan.pdf";
		$userIdentity = Yii::$app->session->get('user_identity');
		$post = ['id' => $id, 'rujukankeluar_id' => DocoHelpers::decrypt($rujukankeluar_id), 'userIdentity' => $userIdentity, 'penunjang_id' => $penunjang_id];
		if(Yii::$app->report->enabled){
			$urlReport = 'surat-jaminan-pelayanan';

			return Yii::$app->report->exec($urlReport.'?'.http_build_query($post),[
				'queryParameter' => $post,
				'manualRender'=>function() use($post,$path){
					$response = $this->serviceRest->post($this->backendUrl.'/cetak-rujukan', [
						'form_params' => $post,
						'save_to' => $path
					]);

					return DocoHelpers::previewPdf($path);
				}
			]);
		}
		$response = $this->serviceRest->get($url, [
			'query' => [
				'id' => $id,
				'rujukankeluar_id' => DocoHelpers::decrypt($rujukankeluar_id),
				'userIdentity' => $userIdentity
			],
			'save_to' => $path
		]);
		$body = json_decode($response->getBody(), true);
		return DocoHelpers::previewPdf($path);
	}
}
