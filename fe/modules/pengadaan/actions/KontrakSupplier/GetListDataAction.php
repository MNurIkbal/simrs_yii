<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetListDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($filter['advanced-filter']['tgl_berlaku'])) {
            $tgl_pr = explode(' - ', $filter['advanced-filter']['tgl_berlaku']);
            $tgl_awal = $tgl_pr[0];
            $tgl_akhir = $tgl_pr[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tgl_berlaku_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tgl_berlaku_akhir'] = $tgl_akhir_format;
            unset($filter['advanced-filter']['tgl_berlaku']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $listKontrakSupplier = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
                'url' => 'kontrak-supplier/get-list-data',
                'method' => 'GET',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            
            $no = $request->get('start',1);
            foreach ($listKontrakSupplier['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kontraksupplier_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_berlaku'] = date('d-M-Y', strtotime($value['tgl_berlaku']));
                $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $listKontrakSupplier['_meta']['totalCount'];
            $result['recordsFiltered'] = $listKontrakSupplier['_meta']['totalCount'];
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
