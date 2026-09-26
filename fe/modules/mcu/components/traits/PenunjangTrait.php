<?php

/**
 * @Author: rizal@docotel.com
 */

namespace app\modules\mcu\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

// use app\modules\mcu\models\InstruksiPenunjangForm;

trait PenunjangTrait 
{
    public function actionPenunjang()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id = $request->get('pasien_id', null);
        $title = Yii::t('fe', 'Riwayat');

        return $this->renderAjax('__penunjang', [
            'pendaftaran_id' => $pendaftaran_id,
            'pasien_id' => $pasien_id,
            'title' => $title,
        ]);
    }

    public function actionGetDataRiwayat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $yiiRestfulParams['pendaftaran_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restMcu->get('pemeriksaan/get-riwayat-penunjang', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            $data = !empty($body['response']['data'] ) ? $body['response']['data']  : [];
            foreach ($data as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = !empty($value['tgl_pendaftaran']) ? date('d M Y H:i:s', strtotime($value['tgl_pendaftaran'])) : null;
                $id = isset($value['pasienmasukpenunjang_id']) ? $value['pasienmasukpenunjang_id'] : null;
                $pemeriksaanspesialismcu_id = isset($value['pemeriksaanspesialismcu_id']) ? $value['pemeriksaanspesialismcu_id'] : null;
                $daftartindakanid = isset($value['detail_3id']) ? $value['detail_3id'] : null;
                $idTindakan = isset($value['tindakanpelayanan_id']) ? $value['tindakanpelayanan_id'] : null;
                $penunjang = isset($value['penunjang']) ? $value['penunjang'] : '';
                $status = isset($value['status']) ? $value['status'] : '';
                $instalasi_id = isset($value['instalasi_id']) ? $value['instalasi_id'] : '';
                $status_id = isset($value['status_id']) ? $value['status_id'] : '';

                $buttonHasil = '';
                $urlTarget = '';
                $style = 'display:none;';
                if ($instalasi_id == DocoConstants::INSTALASI_ID_RAD && $status_id == DocoConstants::ST_SELESAI_PNNJG) {
                    $urlTarget = '/radiologi/hasil-rad/cetak-hasil?id='.DocoHelpers::encrypt($daftartindakanid).'&tindakan_id='.DocoHelpers::encrypt($idTindakan).'&penunjang_id='.DocoHelpers::encrypt($id);
                    $style = '';
                }else if ($instalasi_id == DocoConstants::INSTALASI_ID_LAB && $status_id == DocoConstants::ST_SELESAI_PNNJG) {
                    if($this->checkIntegrasi($id)){
                        $urlTarget = '/laboratorium/integrasi-lis-hasil/cetak?id='.DocoHelpers::encrypt($id);
                    }else{
                        $urlTarget = '/laboratorium/hasil-lab/cetak?id='.DocoHelpers::encrypt($id);
                    }
                    $style = '';
                }else if ($instalasi_id == DocoConstants::INSTALASI_ID_RJ && $status_id != null) {
                    $urlTarget = '/mcu/pemeriksaan/cetak-pemeriksaan-mcu?id='.DocoHelpers::encrypt($pemeriksaanspesialismcu_id);
                    $style = '';
                }
                $buttonHasil = '<button type="button" class="btn btn-info btn-labeled btn-xs btn-toolbar" data-options="link" target="_blank" data-target="'.$urlTarget.'" style ="' . $style . '"><b><i class="fa fa-print"></i></b>Hasil</button>';
                $value['instalasi_nama'] = $value['instalasi_nama'].' - '.$value['ruangan_nama'].'</br>'.$buttonHasil;
                $value['nama_pemeriksaan'] = !empty($value['detail_2']) ? $value['detail_2'] : '';

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

    private function checkIntegrasi($pasienmasukpenunjang_id)
    {
        $response = $this->_restMcu->get('pemeriksaan/check-integrasi-lis', [
            'query' => [
                'id' => $pasienmasukpenunjang_id
            ]
        ]);

        $is_integrasi = json_decode($response->getBody(), True);

        return $is_integrasi;
    }
    
}