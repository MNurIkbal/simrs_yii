<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanRekapPenerimaanObat;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\helpers\HandlingValueHelper as SetValue;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $response = Yii::$app->docoRest->pengadaan->get('lap-rekap-penerimaan-obat/get-data', [
                'query' => $filter
            ]);

            $body = json_decode($response->getBody(), True);
            $no   = $request->get('start', 1);
            $data = [];
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_penerimaan'] = !isset($value['tgl_penerimaan']) ? "-" : date('d-M-Y', strtotime($value['tgl_penerimaan']));
                $value['tgl_po'] = !isset($value['tgl_po']) ? "-" : date('d-M-Y', strtotime($value['tgl_po']));
                $value['tgl_validasi_po'] = !isset($value['tgl_validasi_po']) ? "-" : date('d-M-Y', strtotime($value['tgl_validasi_po']));
                $value['total'] = DocoHelpers::formatNumber($value['total']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['message'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }
}
