<?php 

namespace app\modules\laboratorium\components\traits;

use Yii;

use app\components\DocoConstants;
use app\components\DocoHelpers;
use app\components\models\OrderObatAlkesForm;
use app\components\DocoDatatableHelper;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\Services\FarmasiService;
use yii\helpers\ArrayHelper;

trait OrderObatTrait
{
	// public function actionOrderObat()
	// {
	// 	$request = Yii::$app->request;
	// 	$id = $request->get('id', null);
	// 	$pelayananId = $request->get('pelayananId', null);
	// 	$tindakanId = $request->get('tindakanId', null);
	// 	$pendaftaran_id = $request->get('pendaftaran_id', null);

	// 	if($id && !is_numeric($id)) {
	// 		$id = DocoHelpers::decrypt($id);
	// 	}
	// 	if($pelayananId && !is_numeric($pelayananId)) {
	// 		$pelayananId = DocoHelpers::decrypt($pelayananId);
	// 	}
	// 	if($tindakanId && !is_numeric($tindakanId)) {
	// 		$tindakanId = DocoHelpers::decrypt($tindakanId);
	// 	}
	// 	if($pendaftaran_id && !is_numeric($pendaftaran_id)) {
	// 		$pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
	// 	}
	// 	$bundleData = $this->getDataAttributes($id, $pelayananId, $tindakanId);
	// 	$model = new OrderObatAlkesForm;
	// 	$model->scenario = 'tindakan';
	// 	$model->pemeriksaan_id = $pelayananId;
	// 	$model->qty_tindakan = 1;
	// 	$endPoint = '/laboratorium';
	// 	$urlHasil = '/hasil-lab';
	// 	return $this->renderAjax('components/order-obat/index', [
	// 		'id' => $id,
	// 		'pelayananId' => $pelayananId,
	// 		'tindakanId' => $tindakanId,
	// 		'model' => $model,
	// 		'listInfo' => $bundleData['listInfo'],
	// 		'list_pemeriksaan' => $bundleData['list_pemeriksaan'],
	// 		'list_pegawai' => $bundleData['list_pegawai'],
	// 		'status_periksa' => $bundleData['status_periksa'],
	// 		'pendaftaran_id' => $pendaftaran_id,
	// 		'endPoint' => $endPoint,
	// 		'urlHasil' => $urlHasil,
	// 	]);
	// }

	public function actionTambahObat($id, $pelayananId = null, $tindakanId = null, $pendaftaran_id = null)
	{
		$model = new OrderObatAlkesForm;
		$model->scenario = 'obat';
		$id = DocoHelpers::decrypt($id);
		$pelayananId = DocoHelpers::decrypt($pelayananId);
		$tindakanId = DocoHelpers::decrypt($tindakanId);
		$pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
		$bundleData = $this->getDataAttributes($id, $pelayananId, $tindakanId);
		$model->pemeriksaan_id = $pelayananId;
		$model->qty = 1;
		$model->is_tagihkan = 1;
		$endPoint = '/laboratorium';
		
		return $this->renderAjax('components/order-obat/tindakan_obat', [
			'id' => $id,
			'pelayananId' => $pelayananId,
			'tindakanId' => $tindakanId,
			'model' => $model,
			'listInfo' => $bundleData['listInfo'],
			'list_pemeriksaan' => $bundleData['list_pemeriksaan'],
			'list_pegawai' => $bundleData['list_pegawai'],
			'status_periksa' => $bundleData['status_periksa'],
			'pendaftaran_id' => $pendaftaran_id,
			'endPoint' => $endPoint,
		]);
	}

	public function actionSimpanOrderObat($id, $pelayananId)
	{
		$model = new OrderObatAlkesForm;
		$request = Yii::$app->request;
		if(!is_numeric($id)) {
			$id = DocoHelpers::decrypt($id);
		}
		$pelayananId = DocoHelpers::decrypt($pelayananId);
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		if ($request->post()) {
			$post = $request->post('OrderObatAlkesForm');
			$model->scenario = $post['jenis'];
			$model->load($request->post());
			if(empty($pelayananId)) {
				$pelayananId = $model->pemeriksaan_id;
			}
			if ($model->validate()) {
				try {
					$response = $this->_restLab->post('inf-pasien-rujukan-lab/simpan', [
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
	
	// private function getDataAttributes($id, $pelayananId, $tindakanId)
	// {
	// 	$payload = [
   //       'pasienmasukpenunjang_id' => $id,
	// 		'pelayanan_id' => $pelayananId,
	// 		'tindakan_id' => $tindakanId,
   //    	];
	// 	return $this->guzzleExec($this->_restLab, [
	// 		'url' => 'inf-pasien-rujukan-lab/bundle-data-attributes',
	// 		'payload' => [
	// 			'query' => $payload
	// 		]
	// 	]);
	// }

	// public function actionGetDataTindakanObat()
   // {
	// 	Yii::$app->response->format = Response::FORMAT_JSON;
   //    	$request = Yii::$app->request;
	// 	$payload = DocoDatatableHelper::advancedFilterParam();
	// 	$pendaftaran_id = $request->get('pendaftaran_id');
	// 	$pasienmasukpenunjang_id = $request->get('pasienmasukpenunjang_id');
	// 	$payload['pendaftaran_id'] = $pendaftaran_id;
	// 	$payload['pasienmasukpenunjang_id'] = $pasienmasukpenunjang_id;
	// 	$response = $this->guzzleExec($this->_restLab, [
	// 		'url' => "inf-pasien-rujukan-lab/get-data-tindakan-obat",
	// 		'payload' => [
	// 				'query' => $payload,
	// 		]
	// 	]);
	// 	foreach ($response['data'] as $key => $value) {
	// 		$primary = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
	// 		$pendaftaran_id = DocoHelpers::encrypt($value['pendaftaran_id']);
	// 		$obatalkespasien_id = $value['obatalkespasien_id'];
   //       	$response['data'][$key]['primary'] = $primary;
	// 		$response['data'][$key]['is_ditagihkan'] = ($value['is_ditagihkan']) ? '✓' : null;
	// 		$response['data'][$key]['action'] = '<i class="fa fa-lock"></i>';
	// 			if (!$value['is_sudahbayar']) {
	// 				$params = 'obatalkespasien_id='.$obatalkespasien_id.'&tindakanpelayanan_id='.$value['tindakanpelayanan_id'];
	// 				$jenis = !empty($value['tindakanpelayanan_id']) ? 1 : 2;
	// 				$id = !empty($value['obatalkespasien_id']) ? $value['obatalkespasien_id'] : $value['tindakanpelayanan_id'];
	// 				$response['data'][$key]['action'] = Html::button(
	// 					"<i class='fa fa-trash'></i>",[
	// 						'class' => 'btn btn-danger btn-sm',
	// 						'onclick' => 'hapusTindakanObat('.$jenis.', '.$id.')',
	// 						'action' => '/laboratorium/inf-pasien-rujukan-lab/delete-tindakan-obat?'.$params,
	// 						'id' => 'delete-item-'.$id
	// 					]
	// 				);
	// 			}
   //      }
   //      $response['recordsTotal'] = $response['_meta']['totalCount'];
   //      $response['recordsFiltered'] = $response['_meta']['totalCount'];
   //      return $response;
   // }

	// public function actionFilters()
	// {
	// 	$request = Yii::$app->request;
	// 	$term = $request->get('term', null);
	// 	$type = $request->get('type', null);
	// 	$page = $request->get('page', 1);
	// 	$additionalPayload = $request->get('additionalPayload', []);
	// 	$response = $this->guzzleExec($this->_restLab, [
	// 		'url' => 'inf-pasien-rujukan-lab/filters',
	// 		'payload' => [
	// 			'query' => [
	// 				'term' => $term,
	// 				'type' => $type,
	// 				'page' => $page,
	// 				'additionalPayload' => $additionalPayload,
	// 			]
	// 		],
	// 	]);
   //    return $this->responseJson(200, 'Data berhasil diambil!', $response);
	// }

	

	// public function actionDeleteTindakanObat($obatalkespasien_id, $tindakanpelayanan_id)
	// {
	// 	try {
	// 		$response = $this->_restLab->delete('inf-pasien-rujukan-lab/delete-tindakan-obat',
	// 			[
	// 				'query' => [
	// 					'tindakanpelayanan_id' => $tindakanpelayanan_id,
	// 					'obatalkespasien_id' => $obatalkespasien_id
	// 				]
	// 			]);
	// 		$response = json_decode($response->getBody(),422);
	// 		return DocoHelpers::response($response,false);
	// 	} catch (RequestException $e) {
	// 		return DocoHelpers::response([
	// 				'text' => $e->getMessage()
	// 		],422);
	// 	} catch (\Exception $e) {
	// 		return DocoHelpers::response([
	// 				'text' => $e->getMessage()
	// 		],422);
	// 	}
	// }

	// public function actionListTindakan()
	// {
	// 	$request = Yii::$app->request;
	// 	$get = $request->get();
	// 	$api = 'inf-pasien-rujukan-lab/list-tindakan';
	// 	$data_id = 'daftartindakan_id';
	// 	$data_name = [                    
	// 		'daftartindakan_nama'
	// 	];
	// 	$getRest = $this->_restLab;
	// 	$helpers = new DocoHelpers;
	// 	return $helpers->paginationSelec2($api, $data_id, $data_name, $getRest, $get);
	// }

	// public function actionListObat()
	// {
	// 	$request = Yii::$app->request;
	// 	$ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
	// 	$kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
	// 	$penjamin_id = $request->get('penjamin_id', null);
	// 	$keyword = $request->get('term', null);
	// 	$page = $request->get('page', 1);
	// 	$instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
	// 	$params = [
	// 		'instalasi_id' => $instalasi_id,
	// 		'ruangan_id' => $ruangan_id,
	// 		'penjamin_id' => $penjamin_id,
	// 		'kelaspelayanan_id' => $kelaspelayanan_id,
	// 		'page' => $page,
	// 		'keyword' => $keyword,
	//   	];
	// 	return (new FarmasiService)->getListObat($params);
	// }

	// public function actionFormModalTindakan()
   // {
	// 	$title = Yii::t('fe', 'Tambah Pemeriksaan');
	// 	$result = [];
	// 	$request = Yii::$app->request;
	// 	$get = $request->get();
	// 	$docoVars = Yii::$app->docoVars;
	// 	$ruangan_id = $docoVars->workspace('ruangan_id') ? $docoVars->workspace('ruangan_id') : 1;
	// 	$instalasi_id = $docoVars->workspace('instalasi_id') ? $docoVars->workspace('instalasi_id') : null;
	// 	$get['instalasi_id'] = $instalasi_id;
	// 	$pasienkirimkeunitlain_id = $get['pasienkirimkeunitlain_id'];
	// 	$penjamin_id = isset($get['penjamin_id']) ? DocoHelpers::decrypt($get['penjamin_id']) : null;
	// 	$kelaspelayanan_id = isset($get['kelaspelayanan_id']) ? DocoHelpers::decrypt($get['kelaspelayanan_id']) : null;
	// 	$module = ($instalasi_id == DocoConstants::INSTALASI_ID_RAD) ? 'radiologi' : 'laboratorium';
	// 	$endPoint = 'inf-pasien-rujukan-lab';
	// 	$dataTindakan = $this->getListDataPemeriksaan($pasienkirimkeunitlain_id);
	// 	$params = [
	// 		'ruangan_id'        => $ruangan_id,
	// 		'penjamin_id'       => isset($get['penjamin_id']) ? DocoHelpers::decrypt($get['penjamin_id']) : '',
	// 		'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? DocoHelpers::decrypt($get['kelaspelayanan_id']) : '',
	// 		'instalasi_id'      => $instalasi_id,
	// 	];
	// 	$response = $this->guzzleExec($this->_restLab, [
	// 		'url' => 'inf-pasien-rujukan-lab/get-tarif-tindakan',
	// 		'payload' => [
	// 			'query' => $params
	// 		]
	// 	]);
	// 	$dataJenis = [];
	// 	$countData = 0;
	// 	if(!empty($response['data'])) {
	// 		$countData = count($response['data']);
	// 		foreach ($response['data'] as $key => $value) {
	// 			$result[$value['jenispemeriksaanlab_nama']][] = $value;
	// 			$dataJenis[] = [
	// 				'id' => $value['jenispemeriksaanlab_id'],
	// 				'text' => $value['jenispemeriksaanlab_nama'],
	// 			];
	// 		}
	// 		if(!empty($dataJenis)) {
	// 			$dataJenis = ArrayHelper::map($dataJenis, 'id', 'text');
	// 		}
	// 	}
	// 	return $this->renderAjax('components/order-obat/modal', get_defined_vars());
   // }

	// public function actionSimpanTindakan()
	// {
	// 	$request = Yii::$app->request;
	// 	$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
	// 	if($request->post()) {
	// 		$postData = $request->post();
	// 		if(!empty($pasienkirimkeunitlain_id) && !empty($request->post())) {
	// 			$response = $this->_restLab->get('inf-pasien-rujukan-lab/simpan-tindakan', [
	// 				'query' => [
	// 					'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
	// 				],
	// 				'form_params' => $postData,
	// 			]);
	// 			$body = json_decode($response->getBody(), true);
	// 			return DocoHelpers::response($body);
	// 		}
	// 	}
	// }

	// public function actionGetDataOrderPemeriksaan()
	// {
	// 	Yii::$app->response->format = Response::FORMAT_JSON;
	// 	$request = Yii::$app->request;
	// 	$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
	// 	$yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
	// 	$yiiRestfulParams['pasienkirimkeunitlain_id'] = $pasienkirimkeunitlain_id;
	// 	$draw = $request->get('draw', 1);
	// 	$data = $listDokter = [];
	// 	$dokterList = $this->getListDokter();
	// 	if(!empty($dokterList['data'])) {
	// 		$listDokter = ArrayHelper::map($dokterList['data'], 'id','text');
	// 	}
	// 	try {
	// 		$response = $this->_restLab->get('inf-pasien-rujukan-lab/get-data-pemeriksaan', [
	// 				'query' => $yiiRestfulParams
	// 		]);
	// 		$body = json_decode($response->getBody(), True);
	// 		$no = $request->get('start', 1);
	// 		foreach ($body['response']['data'] as $key => $value) {
	// 				$no++;
	// 				$primaryKey = DocoHelpers::encrypt($value['permintaankepenunjang_id']);
	// 				$value['primary'] = $primaryKey;
	// 				$value['rowNum'] = $no;
	// 				$value['is_cyto'] = ($value['is_cyto']) ? '&#10004;' : '';
	// 				$value['dokter'] = Html::dropDownList('dokter', null, $listDokter, [
	// 					'prompt' => Yii::t('fe', 'Pilih Dokter'),
	// 					'class' => 'select2 form-control input-xs'
	// 				]);
	// 				$value['approve'] = Html::checkbox('approve', true, [
	// 					'label' => false,
	// 					'class' => 'approveCheck',
	// 					'data-value' => $value['daftartindakan_id'],
	// 					'data-idpenunjang' => $value['permintaankepenunjang_id'],
	// 					'data-idunitlain' => $value['pasienkirimkeunitlain_id'],
	// 					'data-namatindakan' => $value['daftartindakan_nama']
	// 				]);
	// 				$data[$key] = $value;
	// 		}
	// 		$return = [
	// 				'data' => $data,
	// 				'draw' => $request->get('draw'),
	// 				'recordsTotal' => isset($body['response']['_meta']) ? $body['response']['_meta']['totalCount'] : count($body['response']['data']),
	// 				'recordsFiltered' => isset($body['response']['_meta']) ? $body['response']['_meta']['totalCount'] : count($body['response']['data']),
	// 		];
	// 		return DocoHelpers::response($return);
	// 	} catch (RequestException $e) {
	// 		$result['error'] = $e->getMessage();
	// 		return $result;
	// 	} catch (\Exception $e) {
	// 		$result['error'] = $e->getMessage();
	// 		return $result;
	// 	}
	// }

	// private function getListDokter()
	// {
	// 	return $this->helper->guzzleExec($this->_restLab, [
	// 		'url' => 'inf-pasien-rujukan-lab/list-dokter',
	// 		'returnResponse' => true,
	// 		'payload' => [
	// 			'query' => Yii::$app->request->get('payload', [])
	// 		],
	// 	]);
	// }

	// public function actionSearch()
	// {
	// 	$result = [];
	// 	$request = Yii::$app->request;
		
	// 	$ruanganId = $request->get('ruangan_id', null);
	// 	$penjaminId = $request->get('penjamin_id', null);
	// 	$kelasId = $request->get('kelaspelayanan_id', null);
	// 	$instalasiId = $request->get('instalasi_id', null);
	// 	$term = $request->get('daftartindakan_nama', null);
	// 	$jenisPemeriksaanId = $request->get('jenispemeriksaanlab_id', null);
	// 	try {
	// 		if($instalasiId == DocoConstants::INSTALASI_ID_LAB || $instalasiId == DocoConstants::INSTALASI_ID_RAD || $instalasiId == DocoConstants::INSTALASI_ID_BEDAH){
	// 			$params = [
	// 				'ruangan_id'        => $ruanganId,
	// 				'penjamin_id'       => $penjaminId,
	// 				'kelaspelayanan_id' => $kelasId,
	// 				'instalasi_id'      => $instalasiId,
	// 				'daftartindakan_nama' => $term,
	// 				'jenispemeriksaanlab_id'      => $jenisPemeriksaanId,
	// 			];
				
	// 			$url = 'inf-pasien-rujukan-lab/get-tarif-tindakan';
	// 			$response = $this->_restLab->get($url, ['query' => $params]);
	// 			$body = json_decode($response->getBody(), true);
	// 			$body = isset($body['response']) ? $body['response'] : [];
	// 			foreach ($body['data'] as $key => $value) {
	// 				$result[$value['jenispemeriksaanlab_nama']][] = $value;
	// 			}
	// 		}
	// 		return json_encode($result);
	// 	} catch (RequestException $e) {
	// 		return DocoHelpers::response(['message' => $e->getMessage()],500);
	// 	} catch (\Exception $e) {
	// 		return DocoHelpers::response(['message' => $e->getMessage()],500);
	// 	}
	// }

	// private function getListDataPemeriksaan($pasienkirimkeunitlain_id)
	// {
	// 	$result = [];
	// 	$response = $this->guzzleExec($this->_restLab, [
	// 		'url' => 'inf-pasien-rujukan-lab/get-list-data-pemeriksaan',
	// 		'payload' => [
	// 			'query' => [
	// 				'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
	// 			]
	// 		]
	// 	]);
	// 	if(!empty($response)) {
	// 		if(is_array($response)) {
	// 			foreach ($response as $key => $value) {
	// 				$daftartindakanId = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
	// 				if(!empty($daftartindakanId)) {
	// 					$result[$daftartindakanId] = $daftartindakanId;
	// 				}
	// 			}
	// 		}
	// 	}
	// 	return $result;
	// }

	// public function actionDeletePemeriksaan()
	// {
	// 	$request = Yii::$app->request;
	// 	$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
	// 	if($request->post()) {
	// 		$postData = $request->post();
	// 		if(!empty($pasienkirimkeunitlain_id) && !empty($request->post())) {
	// 			$response = $this->_restLab->get('inf-pasien-rujukan-lab/delete-pemeriksaan', [
	// 				'query' => [
	// 					'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
	// 				],
	// 				'form_params' => $postData,
	// 			]);
	// 			$body = json_decode($response->getBody(), true);
	// 			return DocoHelpers::response($body);
	// 		}
	// 	}
	// }
}
