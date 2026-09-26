<?php

/**
 * @author : zen
 * Powered by Sirs
 */

namespace app\modules\bedah\processes;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\base\DynamicModel;

use Doco\bedah\models\IntraOperasiForm;
use Doco\bedah\models\IntraPegawaiOperasiForm;
use Doco\bedah\models\IntraItemOperasiForm;
use Doco\bedah\models\IntraPenggunaanCairanForm;
use Doco\bedah\models\IntraAlatDitubuhForm;
use Doco\bedah\models\IntraPemeriksaanPelengkapForm;
use Doco\bedah\models\IntraKonsulTindakanForm;
use Doco\bedah\models\IntraPenggunaanBmhpForm;
use Doco\bedah\models\IntraTindakanLuarBedahForm;
use app\modules\bedah\components\traits\ApiTrait;

class EditTindakanOperasiProcess extends \app\components\DocoBaseProcessExtension
{   
    use ApiTrait;
    protected $_title = "Edit List Tindakan Operasi";
    protected function processFlow($controller, $key = null, $unique = null)
    {
		$request = Yii::$app->request;
		$model = new IntraItemOperasiForm;
        $cacheUnique = $request->get('cacheName', null);
		$title = $this->_title;
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		$title = $this->_title;
		$options['jenisluka'] = [];
		$options['jenisanastesi'] = [];
		$opsi['daftartindakan'] = '';
		$opsi['golonganoperasi'] = [];
		$cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheUnique);
        $xx = $cache->get('pegawaioperasi-MzI4-Nzc3OA');
        $cacheDataPegawai = $cache->get('pegawaioperasi-'.$cacheUnique);
		$ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
		if ($request->post()) {
			$post = $request->post();
            $ruangan_id = $request->post('ruangan_id', $ruangan_id);
			$model->load($post);
			
			$inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
			$pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);

			$unique = DocoHelpers::encrypt($model->inpostoperasi_id) . '-' . DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
			if (empty($model->daftartindakan_id)) {
				$opsi_operasi = json_decode($post['opsi_operasi'], true);
				if (isset($opsi_operasi['id']) && !empty($opsi_operasi['id'])) {
					$model->daftartindakan_id = $opsi_operasi['id'];
				}
				if (isset($opsi_operasi['name']) && !empty($opsi_operasi['name'])) {
					$model->daftartindakan_nama = $opsi_operasi['name'];
				}
			}

			/** 
			 * updated 30 September 2021
			 * Ambil id pegawai yang posisinya dokter operator di pegawai operasi tindakan ini
			 * Ini Untuk handle replace ketika tambah tindakan , user memilih tindakan yang sama, sementara dokter operator tidak dipilih lagi karena sudah ada, 
			 * sehingga memungkinkan form pegawai id untuk form ini null (karena triggernya terisi ketika on simpan form pegawai operasi), padahal dokter id nya sudah pernah diinputkan dan sudah ada di cache .
			 */
			$model->pegawai_id = $this->checkPegawai($model->daftartindakan_id, $inpostid, $pasienpenunjangid);

			if(empty($model->pegawai_id)){
                $customError = [
                    'posisi_tim' => [
                        'Tindakan ini belum memiliki dokter operator.',
                    ],
                ];
                $customFormName = substr(strrchr(get_class(new IntraPegawaiOperasiForm), "\\"), 1);;
                $errors = DocoHelpers::parseError($customError, $customFormName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

			if ($model->validate()) {
				$bmhp = [];
				$cacheName = 'itemoperasi-' . $inpostid . '-' . $pasienpenunjangid;
				$cacheData = $cache->get($cacheName);
				$pegawai_input = Yii::$app->session->get('user_identity');
				$pegawai_input = !empty($pegawai_input['nama_pegawai']) ? $pegawai_input['nama_pegawai'] : '';
				try {
					$restBedah = Yii::$app->docoRest->bedahsentral;
					$response = $restBedah->get('allow/compare-bmhp-ruangan', ['form_params' => ['daftartindakan_id' => $model->daftartindakan_id, 'ruangan_id' => $ruangan_id]]);
					$body = json_decode($response->getBody(), true);
					foreach ($body['response'] as $k => $value) {
						$newData = [];
						$newData['pasienmasukpenunjang_id'] = $model->pasienmasukpenunjang_id;
						$newData['inpostoperasi_id'] = $model->inpostoperasi_id;
						$newData['obatalkes_id'] = !empty($value['obatalkes_id']) ? $value['obatalkes_id'] : 0;
						$newData['daftartindakan_id'] = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : 0;
						$newData['obatalkes_nama'] = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '';
						$newData['persediaan'] = !empty($value['qty_tersedia']) ? $value['qty_tersedia'] : 0;
						$newData['tambahan'] = 0;
						$newData['terpakai'] = !empty($value['qty_terpakai']) ? $value['qty_terpakai'] : 0;
						$newData['sisa'] = !empty($value['persediaan']) ? $value['persediaan'] : 0;
						$newData['ditagihkan'] = '';
						$newData['is_ditagihkan'] = '0';
						$newData['is_available'] = ($value['is_available']) ? 1 : 0;
						$newData['msg'] = !empty($value['msg']) ? $value['msg'] : '';
						$newData['operasi_key'] = count($cacheData);
						$newData['pegawai_input'] = $pegawai_input;
						$bmhp[] = $newData;
					}
				} catch (\RequestException $e) {
					$bmhp = [];
				}
				$data = $model->attributes;
				$data['pegawai_input'] = $pegawai_input;

				/** cek kemungkinan input tindakan yang sudah diinput, agar tidak double di cache, jika ada maka replace */
				if($key == null){
					$key = $this->getKeyTimOperasi($model->daftartindakan_id, $cacheName);
				}

				if ($key >= 0 && !is_null($key)) {
					return DocoHelpers::response($this->updateCache($cacheName, $data, $key));
				}
				if (count($bmhp) > 0) {
					$this->saveToCache('penggunaanbmhp-' . $inpostid . '-' . $pasienpenunjangid, $bmhp);
				}
				return DocoHelpers::response($this->saveToCache($cacheName, $data));
			} else {
				$errors = DocoHelpers::parseError($model->errors, $formName);
				return DocoHelpers::responseTemplate(422, 'Error', $errors);
			}
		}
		$model->default = 0;
		if ($key != '') {
			$data = $cache->get($unique);
			$model->attributes = $data[$key];
			$opsi['daftartindakan'] = json_encode(['id' => $data[$key]['daftartindakan_id'], 'name' => $data[$key]['daftartindakan_nama']]);
			$opsi['golonganoperasi'] = [$data[$key]['golonganoperasi_id'] => $data[$key]['golonganoperasi_nama']];
		}
		
		return $controller->renderAjax('@app/modules/bedah/processes/itemoperasi/index', get_defined_vars());
	}

	private function checkPegawai($daftartindakan_id, $inpostoperasi_id, $pasienmasukpenunjang_id){
        $getCache = Yii::$app->cache;
        $getCache = $getCache->get('pegawaioperasi-' . $inpostoperasi_id . '-' . $pasienmasukpenunjang_id);
        $isDokterOperator = false;
		$pegawai_id = null;
        if (!empty($getCache[$daftartindakan_id])) {
            $getCache = $getCache[$daftartindakan_id];
            foreach ($getCache as $key => $value) {
				if($value["posisi_tim"] == DocoConstants::TIM_DOKTER_BEDAH){
					$isDokterOperator = true;
					$pegawai_id = !empty($value['pegawai_id']) ? $value['pegawai_id'] : null;
				}
            }
        }
        return $pegawai_id;
	}

	private function getKeyTimOperasi($daftartindakan_id, $cacheName){
        $getCache = Yii::$app->cache;
        $getCache = $getCache->get($cacheName);
        $key = null;
        if (!empty($getCache)) {
            foreach ($getCache as $tmpKey => $value) {
				if($value["daftartindakan_id"] == $daftartindakan_id){
					$key = $tmpKey;
				}
            }
        }
        return $key;
	}    
}