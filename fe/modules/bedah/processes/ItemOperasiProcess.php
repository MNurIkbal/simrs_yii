<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\bedah\processes;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\bedah\models\IntraItemOperasiForm;
use Doco\bedah\models\IntraPegawaiOperasiForm;
use app\modules\bedah\components\traits\ApiTrait;

class ItemOperasiProcess extends \app\components\DocoBaseProcessExtension
{
	use ApiTrait;
	
	protected $_title = "Tambah List Tindakan Operasi";
	protected function processFlow($controller, $key = null, $unique = null)
	{
		$request = Yii::$app->request;
		$model = new IntraItemOperasiForm;
		$title = $this->_title;
		$formName = substr(strrchr(get_class($model), "\\"), 1);
		$title = $this->_title;
		$options['jenisluka'] = [];
		$options['jenisanastesi'] = [];
		$opsi['daftartindakan'] = '';
		$opsi['golonganoperasi'] = [];
		$cache = Yii::$app->session;
		$ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

		/**Kondisi update */
		$is_edit = false;
		$key = $request->get('key', null);
		$changeTindakan = $request->get('changeTindakan', false); //kondisi jika edit dan tindakan diganti
		/** */
		
		$pegawai_input = Yii::$app->session->get('user_identity');
		$pegawai_input = !empty($pegawai_input['nama_pegawai']) ? $pegawai_input['nama_pegawai'] : '';

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
				$cacheNameBmhp = 'penggunaanbmhp-' . $inpostid . '-' . $pasienpenunjangid;
				$cacheBmhp = $cache->get($cacheNameBmhp);

				if($changeTindakan){
					/**
					 * Hapus bmhp berdasarkan tindakan yang diganti, lalu masukkan lagi bmhp sesuai mappingan tindakan baru
					*/
					$old_daftartindakan_id = isset($cacheData[$key]['daftartindakan_id']) ? $cacheData[$key]['daftartindakan_id'] : null;
					if(!empty($old_daftartindakan_id)){
						$this->unsetCacheBmhp($old_daftartindakan_id, $cacheNameBmhp);
						$cacheBmhp = $cache->get($cacheNameBmhp);
					}
				}

				try {
					$restBedah = Yii::$app->docoRest->bedahsentral;
					$response = $restBedah->get('allow/compare-bmhp-ruangan', ['form_params' => ['daftartindakan_id' => $model->daftartindakan_id, 'ruangan_id' => $ruangan_id]]);
					$body = json_decode($response->getBody(), true);
					foreach ($body['response'] as $k => $value) {
						$obatalkes_id = !empty($value['obatalkes_id']) ? $value['obatalkes_id'] : 0;
						$qty_terpakai = !empty($value['qty_terpakai']) ? $value['qty_terpakai'] : 0;
						$persediaan = !empty($value['persediaan']) ? $value['persediaan'] : 0;
						$qty_tersedia = !empty($value['qty_tersedia']) ? $value['qty_tersedia'] : 0;
						$newData = [];
						$newData['pasienmasukpenunjang_id'] = $model->pasienmasukpenunjang_id;
						$newData['inpostoperasi_id'] = $model->inpostoperasi_id;
						$newData['obatalkes_id'] = $obatalkes_id;
						$newData['daftartindakan_id'] = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : 0;
						$newData['obatalkes_nama'] = !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '';
						$newData['persediaan'] = $qty_tersedia;
						$newData['tambahan'] = 0;
						$newData['terpakai'] = $qty_terpakai;
						$newData['sisa'] = $persediaan;
						$newData['ditagihkan'] = '';
						$newData['is_ditagihkan'] = '0';
						$newData['is_available'] = ($value['is_available']) ? 1 : 0;
						$newData['msg'] = !empty($value['msg']) ? $value['msg'] : '';
						$newData['operasi_key'] = count($cacheData);
						$newData['pegawai_input'] = $pegawai_input;

						// override sisa stok jika sudah ada obat sebelumnya
						$sisa_stok =  $this->getSisaStokTerakhir($obatalkes_id, $cacheBmhp);
						if(!empty($sisa_stok)){
							if(max($sisa_stok) != $qty_tersedia){
								$sisa_stok = $this->resetSisaStokBmhp($obatalkes_id, $cacheNameBmhp, $qty_tersedia);
							}
							$sisa_stok = !empty($sisa_stok) ? min($sisa_stok) : 0; //handling jika ada beberapa row bmhp
							$newData['persediaan'] = $sisa_stok;
							$newData['sisa'] = $sisa_stok-$qty_terpakai;
						}

						$bmhp[] = $newData;
					}
				} catch (RequestException $e) {
					$bmhp = [];
				}
				$data = $model->attributes;
				$data['pegawai_input'] = $pegawai_input;

				/** cek kemungkinan input tindakan yang sudah diinput, agar tidak double di cache, jika ada maka replace */
				if($key == null){
					$key = $this->getKeyTimOperasi($model->daftartindakan_id, $cacheName);
				}

				if ($key >= 0 && !is_null($key)) {
					if($changeTindakan){
						$this->saveToCache($cacheNameBmhp, $bmhp);
					}
					return DocoHelpers::response($this->updateCache($cacheName, $data, $key));
				}
				if (count($bmhp) > 0) {
					$this->saveToCache($cacheNameBmhp, $bmhp);
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

		/** handling ketika edit */
		$cacheName = $request->get('cacheName',null);
		$key = $request->get('id',null);
		$key = DocoHelpers::decrypt($key);
		$cacheNamePegawai = str_replace("itemoperasi","pegawaioperasi",$cacheName);
		$dataPegawai = $cache->get($cacheNamePegawai);
		$data = $cache->get($cacheName);

		$dataTimOperasi = $this->getDataTimOperasi();
		$timOperasi = json_encode($dataTimOperasi);
		
		if(!empty($cacheName) && !empty($data[$key])){
			$title = "Edit Tindakan Operasi";
			$is_edit = true;
			$model->attributes = $data[$key];
		}
		
		$show_kegiatan_golongan_operasi = $controller->getShowKegiatanGolonganOperasi();
		return $controller->renderAjax('@app/modules/bedah/processes/itemoperasi/form.php', get_defined_vars());
	}

	private function checkPegawai($daftartindakan_id, $inpostoperasi_id, $pasienmasukpenunjang_id){
        $getCache = Yii::$app->session;
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
        $getCache = Yii::$app->session;
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

    private function getDataTimOperasi()
    {
		$restBedah = Yii::$app->docoRest->bedahsentral;
        $response = $restBedah->get('allow/get-tim-operasi');
        $body = json_decode($response->getBody(), true);

        return $body['response'];
    }

	private function getSisaStokTerakhir($obatalkes_id, $cacheBmhp){
		$sisa = [];
		if(!empty($obatalkes_id) && !empty($cacheBmhp)){
			foreach ($cacheBmhp as $val){
				$tmp_obatalkes_id = !empty($val['obatalkes_id']) ? $val['obatalkes_id'] : null;
				$tmp_daftartindakan_id = !empty($val['daftartindakan_id']) ? $val['daftartindakan_id'] : null;
				$tmp_sisa = !empty($val['sisa']) ? $val['sisa'] : null;
				if($obatalkes_id == $tmp_obatalkes_id){
					$sisa[] = $tmp_sisa;
				}
			}
		}
		return $sisa;
	}

}
