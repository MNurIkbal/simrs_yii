<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\Html;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params);
        $draw = Yii::$app->request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $request = Yii::$app->docoRest->master->get('margin-harga/group-margin?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);
            $no = Yii::$app->request->get('start', 1);
            if (!empty($response['response']['data'])) {
                foreach ($response['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['groupmargin_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['groupmargin_id']);
                    $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
                    $value['rowNum'] = $no;
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/margin-harga/detail-margin-harga-obat?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $response['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $response['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}