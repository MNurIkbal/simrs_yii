<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanPemakaianBmhpPasien;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\helpers\HandlingValueHelper;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace('ruangan_id');
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = Yii::$app->docoRest->gudang->get('lap-pemakaian-bmhp-pasien/index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_transaksi'] = date('d M Y', strtotime($value['tgl_transaksi']));
                $value['harga_netto'] = DocoHelpers::formatNumber($value['harga_netto']);
                $value['total'] = DocoHelpers::formatNumber($value['total']);
                $value['is_ditagihkan'] = $value['is_ditagihkan'] ? "Ya" : "Tidak";
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
