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
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class GetDetailAction extends Action {
    public function run($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = Yii::$app->docoRest->apotek->get('informasi-permintaan-bmhp/detail?'.http_build_query($yiiRestfulParams), [
                'query' => [
                    'pendaftaran_id' => DocoHelpers::decrypt($id),
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $list_data = $body['response']['data'];

            $no = $request->get('start',1);
            foreach ($list_data as $key => $value) {
                $no++;
                $primary = DocoHelpers::encrypt($no);
                $value['rowNum'] = $no;
                $value['primary'] = $primary;
                $value['tgl_permintaan'] = date('d M Y', strtotime($value['tgl_permintaan']));
                $value['status'] = "Belum Approved";
                
                if($value['status_bmhp'] == DocoConstants::BMHP_SUDAH_VERIFIKASI) {
                    $value['status'] = "Approved";
                }
                
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
