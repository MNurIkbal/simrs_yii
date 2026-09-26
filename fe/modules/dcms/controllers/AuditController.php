<?php
// Author : Ramdhan Nurrachman

namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\dcms\models\LoginpemakaiForm;
use GuzzleHttp\Exception\RequestException;

class AuditController extends DocoController
{
    protected $_title = "Audit";
    protected $_module = '/dcms/audit/';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionLoad()
    {
        $view = Yii::$app->request->get('view');
        if ($view === 'useract') {
            $title = 'User Action';
        } else {
            $title = 'Audit Trail';
        }
        return $this->renderAjax($view, get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $view = $request->get('view');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $pk = $view === 'log' ? 'event_id' : 'audit_trail_id';
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $target = "audit/{$view}?".http_build_query($yiiRestfulParams);

        try {
            $response = $this->_restDcms->get($target, [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value[$pk]);
                $value['primary'] = $primaryKey;
                unset($value[$pk]);
                $value['rowNum'] = $no;
                $value['button'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success',
                    'data-source' => "/dcms/audit/detail?view={$view}&id={$primaryKey}",
                    'onclick' => 'docoHelper.detail(this)'
                ]);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $view = $request->get('view');
        $title = 'Lihat Data';
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restDcms->get("audit/detail-{$view}?id={$id}");
        $body = json_decode($response->getBody(), TRUE);
        $response = json_encode($body['response'], JSON_PRETTY_PRINT);
        return $this->renderAjax('_detail', get_defined_vars());
    }
}
