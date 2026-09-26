<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_tujuan_id'] = $ruangan = Yii::$app->docoVars->workspace("ruangan_id");

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = Yii::$app->docoRest->apotek->get('informasi-permintaan-bmhp/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $list_data = $body['response']['data'];

            $no = $request->get('start',1);
            foreach ($list_data as $key => $value) {
                $no++;
                $primary = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['rowNum'] = $no;
                $value['primary'] = $primary;
                $value['tgl_permintaan'] = is_null($value['tgl_permintaan']) ?
                    "-" : date('d M Y', strtotime($value['tgl_permintaan']));
                $value['status_bmhp'] = is_null($value['status_bmhp']) ?
                    "-" : $value['status_bmhp'];
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