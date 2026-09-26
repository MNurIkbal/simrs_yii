<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\Html;

class GetListDataBarangAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        // echo json_encode(["req" => $request]); exit();
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(Yii::$app->docoVars->workspace('ruangan_id') != DocoConstants::RUANGAN_PENGADAAN)
            $filter['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        if (isset($filter['advanced-filter']['tgl_pr'])) {
            $tgl_pr = explode(' - ', $filter['advanced-filter']['tgl_pr']);
            $tgl_awal = $tgl_pr[0];
            $tgl_akhir = $tgl_pr[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tgl_pr_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tgl_pr_akhir'] = $tgl_akhir_format;
            unset($filter['advanced-filter']['tgl_pr']);
        }

        if (isset($request->get()['is_prcyto'])) {
            $filter['advanced-filter']['is_prcyto'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $filter['advanced-filter']['is_admin'] = $request->get('is_admin', null);
        }

        $filter['type'] = "barang";

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'purchase-requisition/get-list-data',
            'method' => 'GET',
            'payload' => [
                'query' => $filter
            ]
        ]);

        $no = $request->get('start',1);
        foreach ($response['data'] as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['purchasereqbrg_id']);
            unset($value['purchasereqbrg_id']);
            $value['primary'] = $primaryKey;
            $value['rowNum'] = $no;
            $value['tgl_pr'] = date('d M Y', strtotime($value['tgl_pr']));
            $value['no_pr'] = isset($value['no_pr']) ? $value['no_pr'] : '-';
            $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success',
                    'data-source'=>"/pengadaan/info-purchase-requisition/expand?type=barang&id=" . $primaryKey,
                    'onclick'=> 'docoHelper.detail(this)'
            ]);
            $value['pegawai_approve'] = $value['pegawai_approve'] != null ? $value['pegawai_approve'] : '-';
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $response['_meta']['totalCount'];
        $result['recordsFiltered'] = $response['_meta']['totalCount'];
        return $result;
    }
}
