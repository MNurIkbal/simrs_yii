<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetListProduksiAction extends Action {
    public function run() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'inf-produksi-obat/get-data-produksi',
                'payload' => [
                    'query' => $yiiRestfulParams
                ],
            ]);

            $no = $request->get('start',1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['produksiobatalkes_id']);
                $pemsananProduksiId = DocoHelpers::encrypt($value['pemesananproduksiobat_id']);
                $value['nopemesanan'] = is_null($value['nopemesanan']) ? "-" : $value['nopemesanan'];
                $value['pegawai_pemesanan'] = is_null($value['pegawai_pemesanan']) ? "-" : $value['pegawai_pemesanan'];
                $value['status_produksi'] = is_null($value['status_produksi']) ? "-" : $value['status_produksi'];
                $value['noproduksiobat'] = is_null($value['noproduksiobat']) ? "-" : $value['noproduksiobat'];
                $value['tglproduksiobat'] = is_null($value['tglproduksiobat']) ? "-" : date("j M Y H:i:s", strtotime($value['tglproduksiobat']));
                $value['tglpemesanan'] = is_null($value['tglpemesanan']) ? "-" : date("j M Y H:i:s", strtotime($value['tglpemesanan']));
                $value['pegawai_approve'] = is_null($value['pegawai_approve']) ? "-" : $value['pegawai_approve'];
                $value['pemesananproduksiobat_id'] = $pemsananProduksiId;
                
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
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
