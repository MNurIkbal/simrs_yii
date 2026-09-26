<?php

/**
 * @author Sigit Arif Munandar <sigit@docotel.com>
 * @todo Trait dashboard odoo admission
 * @copyright 26 April 2018 aweutist
 */

namespace app\modules\master\components\traits;

use Yii;
use function GuzzleHttp\json_encode;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\master\models\KategoriTindakanForm;

use GuzzleHttp\Exception\RequestException;

use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

trait DashboardOdooAdmissionTrait
{
    public function actionGetDataAdmission()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $type = $request->get('type', null);

        try {
            $yiiRestfulParams['model'] = $type;
            $response = $this->_restPendaftaran->get('api/get-data-sync-admission?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['id']);
                    $value['primary'] = $primaryKey;
                    $value['confirmation_date'] = !empty($value['confirmation_date']) ? date('Y-m-d', strtotime($value['confirmation_date'])) : '-';
                    $value['date_order'] = !empty($value['date_order']) ? date('Y-m-d', strtotime($value['date_order'])) : '-';
                    $disabled = (empty($value['id'])) ? false : true;
                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$primaryKey,
                        'class' => 'select_item',
                        'value' => $value['id'],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                        'class' => 'btn btn-sm btn-success',
                        'data-source' => "/master/dashboard-odoo/detail-response-admission?id=".$primaryKey.'&model='.$type,
                        'onclick' => 'docoHelper.detail(this)'
                    ]);
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                
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

    public function actionDetailResponseAdmission($id, $model)
    {
        $request = $this->_restPendaftaran->request('GET', 'api/get-data-sync-admission?id='.DocoHelpers::decrypt($id).'&model='.$model);
        $response = json_decode($request->getBody(), true);
        $data = $response['response'];

        if (!empty($data)) {
            $detail['PAYLOAD'] = json_decode($data['sync_payload']);
            $detail['RESPONSE'] = json_decode($data['sync_response']);

            $response = json_encode($detail, JSON_PRETTY_PRINT);
        } else {
            $response = '<center><h3><strong>Tidak Ada Response</strong><h3></center>';
        }
        
        return $this->renderAjax('_detail', get_defined_vars());
    }

    public function actionResendAdmission()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $response = [];

        try {
            $model = ($request->get('model')) ? $request->get('model') : null;
            $id = $post['id'];
            $id = !is_array($id) ? array($id) : $id;
            $result = $this->_restPendaftaran->post('api/resync-admission?model='.$model, [
                'form_params' => [
                    'id' => $id,
                ]
            ]);
            $result = json_decode($result->getBody(),true);

            return DocoHelpers::response($result['response']);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            
            return DocoHelpers::response($response, 500);
        }
    }
}
