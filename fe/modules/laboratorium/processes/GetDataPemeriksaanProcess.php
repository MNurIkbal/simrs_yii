<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\laboratorium\processes;

use Yii;
use yii\web\Response;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetDataPemeriksaanProcess extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		$id = $request->get('id', null);
		$id = DocoHelpers::decrypt($id);
		$yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
		$yiiRestfulParams['id'] = $id;
		$data = [];
		$restLab = Yii::$app->docoRest->laboratorium;
		try {
			$response = $restLab->get('inf-pasien-lab/get-pemeriksaan-view', [
				'form_params' => [],
				'query' => $yiiRestfulParams
			]);
			$body = json_decode($response->getBody(), true);
			$no = $request->get('start', 1);
			foreach ($body['response']['data'] as $key => $value) {
				$no++;
				$primaryKey = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
				$value['primary'] = $primaryKey;
				$value['tp'] = $value['tindakanpelayanan_id'];
				unset($value['tindakanpelayanan_id']);
				$value['rowNum'] = $no;
				$warnaLegend = $this->setWarnaLegend($value);
				$value['status_periksa_btn'] = '<span class="badge" style="background: '.$warnaLegend['colorStatusPeriksa'].'; color: '.$warnaLegend['fontStatusPeriksa'].'">'.ucwords($warnaLegend['labelStatusPeriksa']).'</span>';
				$value['status_bayar_btn'] = '<span class="badge" style="background: '.$warnaLegend['colorStatusBayar'].'; color: '.$warnaLegend['fontStatusBayar'].'">'.$warnaLegend['labelStatusBayar'].'</span>';
				$data[$key] = $value;
			}
			$return = [
				'data' => $data,
				'draw' => $request->get('draw'),
				'recordsTotal' => $body['response']['_meta']['totalCount'],
				'recordsFiltered' => $body['response']['_meta']['totalCount']
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

	private function setWarnaLegend($data)
	{
		$colorStatusPeriksa = $colorStatusBayar = '#FFFfff';
		$fontStatusPeriksa = $fontStatusBayar = 'black';
		$statusLab = DocoConstants::$status_lab;
		$statusPemeriksaan = $data['status_periksa'];
		$statusBayar = $data['status_bayar'];
		$labelStatusPeriksa = !empty($data['status_pemeriksaan']) ? $data['status_pemeriksaan'] : $statusLab[DocoConstants::LAB_ST_PEN_BELUMPERIKSA];
		
		if (strtolower($statusPemeriksaan) == 'batal') {
			$colorStatusPeriksa = '#D24D57';
            $fontStatusPeriksa = 'white';
		} else if (strtolower($statusPemeriksaan) == 'selesai') {
			$colorStatusPeriksa = '#26A65B';
			$fontStatusPeriksa = 'white';
		} else if(strtolower($statusPemeriksaan) == 'periksa') {
			$colorStatusPeriksa = '#2574A9';
			$fontStatusPeriksa = 'white';
		} else if(strtolower($statusPemeriksaan) == 'ambil sampel') {
			$colorStatusPeriksa = '#F5D76E';
			$fontStatusPeriksa = 'white';
		}

		if (strtolower($statusBayar) == 'batal bayar') {
			$colorStatusBayar = '#D24D57';
			$fontStatusBayar = 'white';
		} else if (strtolower($statusBayar) == 'sudah bayar') {
			$colorStatusBayar = '#26A65B';
			$fontStatusBayar = 'white';
		}

		return [
			'colorStatusBayar' => $colorStatusBayar,
			'fontStatusBayar' => $fontStatusBayar,
			'labelStatusBayar' => $statusBayar,
			'colorStatusPeriksa' => $colorStatusPeriksa,
			'fontStatusPeriksa' => $fontStatusPeriksa,
			'labelStatusPeriksa' => $labelStatusPeriksa,
		];
	}
}