<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataPaketDetailAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $getAll = $request->get();
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($getAll);
        $yiiRestfulParams['daftarpaketfisio_id'] = $id;
        try {
            $response = Yii::$app->docoRest->master->get('paket-fisio/get-sub', [
                'query' => $yiiRestfulParams,
                'form_params' => [],
            ]);
            $response = json_decode($response->getBody(), true);
            $datas = ArrayHelper::getValue($response, 'response.data');
            $no = $request->get('start', 1);
            foreach ($datas as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = ArrayHelper::getValue($value, 'daftarpaketfisiodet_id');
                $value['primary'] = $primaryKey;
                $datas[$key] = $value;
            }
            $totalCount = ArrayHelper::getValue($response, 'response._meta.totalCount', 0);
            $result['data'] = $datas;
            $result['recordsTotal'] = $totalCount;
            $result['recordsFiltered'] = $totalCount;
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
