<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class GetDataPaketAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $response = Yii::$app->docoRest->master->get('paket-fisio/get', [
            'query' => $yiiRestfulParams,
            'form_params' => [],
        ]);
        $responseApi = json_decode($response->getBody(), true);
        $body = ArrayHelper::getValue($responseApi, 'response.data');
        $no = $request->get('start', 1);
        $datas = [];
        foreach ($body as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $isActive = ArrayHelper::getValue($value, 'is_active');
            $isActiveLabel = 'Tidak Aktif';
            if ($isActive == 1) $isActiveLabel = 'Aktif';
            $primaryKeyId = ArrayHelper::getValue($value, 'daftarpaketfisio_id');
            $primaryKey = DocoHelpers::encrypt($primaryKeyId);
            $value['primary'] = $primaryKey;
            $value['status_label'] = $isActiveLabel;
            $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                'class' => 'btn btn-sm btn-success',
                'data-source' => "/master/tindakan/paket-fisio-sub?id=$primaryKey",
                'onclick' => 'docoHelper.detail(this)'
            ]);
            $datas[$key] = $value;
        }
        $totalCount = ArrayHelper::getValue($responseApi, 'response._meta.totalCount');
        $result['data'] = $datas;
        $result['recordsTotal'] = $totalCount;
        $result['recordsFiltered'] = $totalCount;
        return $result;
    }
}
