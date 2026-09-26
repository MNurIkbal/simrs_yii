<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = Yii::$app->docoRest->gudang->get('informasi-adjustment-obat-alkes/index?'.http_build_query($yiiRestfulParams),
                        ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['adjusmenobat_id']);
                unset($value['adjusmenobat_id']);
                $value['primary'] = $primaryKey;
                $value['no_adjusmen'] = HandlingValueHelper::nullValue($value['no_adjusmen']);
                $value['jenis_adjusmen_nama'] = HandlingValueHelper::nullValue($value['jenis_adjusmen_nama']);
                $value['tgl_adjusmen'] = date('d M Y', strtotime($value['tgl_adjusmen']));
                $value['rowNum'] = $no;
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