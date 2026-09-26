<?php

/**
 * @Author: Ardi Pratama Septiadi
 */

namespace app\modules\igd\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

trait RiwayatPasienTrait 
{
	public function actionRiwayatPasien($id)
	{
		try{
        	$title = 'Riwayat Pasien';
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $data_pasien = $this->_data_pasien;
            $norm = $data_pasien['no_rekam_medik'];
            return $this->renderAjax('riwayat-pasien/index', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
	}

	public function actionGetDataRiwayatPasien()
	{
        try {
            $request = Yii::$app->request;
            $norm = $request->get('norm');
            $draw = $request->get('draw', 1);
            $result = $data= [];
            $result['data'] = [];
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;


            $response = $this->_restIgd->request('get', 'riwayat-pasien/index?norm=' . $norm, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            foreach ($body['response']['data'] as $key => $value) {
                $cara_keluar = '';
            	if (!empty($value['tglpasienpulang'])) {
                    $cara_keluar .= date('d M Y H:i:s', strtotime($value['tglpasienpulang'])) . "<br>";
                    $cara_keluar .= $value['carakeluar_nama'] . "<br>";
                }
            	$data[] = [
                    'pendaftaran_id' => $value['pendaftaran_id'],
                    'tgl_pendaftaran' => date('d M Y', strtotime($value['tgl_pendaftaran'])) . ' / ' . $value['no_pendaftaran'],
                    'ruangan_pend' => $value['ruangan_pend'],
                    'dok_rjrd' => $value['dok_rjrd'],
                    'cara_keluar' => $cara_keluar,
                    'aksi_pelayanan' => $this->riwayatPelayanan($norm,$value),
                    'aksi_penunjang' => $this->riwayatPenunjang($norm,$value),
                ];
            }

			$result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
	}

	protected function riwayatPelayanan($norm,$data)
	{
		$btn_pelayanan = '';

        if ($data['r_asesmendokter'] == 1) {
            $btn_pelayanan .= Html::a('Asesmen Dokter',
                [
                    'cetak-pdf-asesmen-dokter?id='.DocoHelpers::encrypt($data['pendaftaran_id']).'&norm='.$norm
                ], [
                    'class'=>'btn btn-info btn-sm btn-riwayat',
                    'target' => 'blank'
                ]
            );
        }
        if ($data['r_cppt'] == 1) {
            $btn_pelayanan .= Html::a('Asesmen Dpjp',
                [
                    'cetak-list-dpjp?id='.DocoHelpers::encrypt($data['pendaftaran_id']).'&norm='.$norm
                ], [
                    'class'=>'btn btn-info btn-sm btn-riwayat',
                    'target' => 'blank'
                ]
            );
            $btn_pelayanan .= Html::a('Implementasi',
                [
                    'cetak-implementasi-pdf?id='.DocoHelpers::encrypt($data['pendaftaran_id']).'&norm='.$norm
                ], [
                    'class'=>'btn btn-info btn-sm btn-riwayat',
                    'target' => 'blank'
                ]
            );
        }
        if ($data['r_instruktitindakan'] == 1 || $data['r_instruktitindakanbmhp'] == 1){
            $btn_pelayanan .= Html::a('Tindakan & BMHP',
                [
                    'cetak-tindakan-bmhp?id='.DocoHelpers::encrypt($data['pendaftaran_id']).'&norm='.$norm
                ], [
                    'class'=>'btn btn-info btn-sm btn-riwayat',
                    'target' => 'blank'
                ]
            );
        }
		return $btn_pelayanan;
	}

	protected function riwayatPenunjang($norm,$data)
	{
		$btn_penunjang = '';
		if ($data['p_laboratorium'] == 1) {
            $btn_penunjang .= Html::a('Laboratorium',
                [
                    'index?norm='.$norm
                ], [
                    'class'=>'btn btn-info btn-sm btn-riwayat',
                    'target' => 'blank'
                ]
            );
        }
        if ($data['p_radiologi'] == 1) {
            $btn_penunjang .= Html::a('Radiologi',
                [
                    'index?norm='.$norm
                ], [
                    'class'=>'btn btn-info btn-sm btn-riwayat',
                    'target' => 'blank'
                ]
            );
        }
        if ($data['p_operasi'] == 1) {
            $btn_penunjang .= Html::a('Bedah Sentral',
                [
                    'index?norm='.$norm
                ], [
                    'class'=>'btn btn-info btn-sm btn-riwayat',
                    'target' => 'blank'
                ]
            );
        }
		return $btn_penunjang;
	}
}