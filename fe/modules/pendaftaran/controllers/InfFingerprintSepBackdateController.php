<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\web\Response;
use app\components\Services\Contracts\BpjsInterface;
use Doco\models\bpjs\Bpjs;
use app\modules\pendaftaran\models\ApprovalPengajuanForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class InfFingerprintSepBackdateController extends DocoController
{
    protected $_restPendaftaran;
    protected $bpjsService;

    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);

    }

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    public function actionIndex()
    {
        try {
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $filterParam = $request->get('advancedFilter');
            $data = $resData = $result = [];

            $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'inf-fingerprint-sep-backdate/index',
                'payload' => [
                    'query' => [
                        'advancedFilter' => $filterParam
                    ]
                ]
            ]);

            if (isset($response['metaData']['code']) && $response['metaData']['code'] == 200) {
                $resData = $response['response']['list'];
            } else {
                return DocoHelpers::response($response, 422);
            }
            
            $no = 0;
            foreach ($resData as $key => $value) {
                $no++;
                $value['no'] = $no;
                $value['primary'] = $value['noKartu'];
                $data[] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($resData);
            $result['recordsFiltered'] = count($data);

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionApproval()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new ApprovalPengajuanForm;
        try {
            $jnsPengajuan = str_replace(' ', '_', $post['status']);
            $model->attributes = $post;
            $model->tglSep = $post['tglsep'];
            $model->jnsPelayanan = DocoConstants::INS_APPR_BPJS[$post['jnspelayanan']];
            $model->jnsPengajuan = DocoConstants::JNS_APPR_BPJS[$jnsPengajuan];
            $model->keterangan = isset($post['keterangan']) ? $post['keterangan'] : '';
            $model->user = Yii::$app->docoVars->user("nama");

            if ($model->validate()) {
                $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                    'url' => 'inf-fingerprint-sep-backdate/approval',
                    'method' => 'POST',
                    'payload' => [
                        'form_params' => $model->attributes
                    ]
                ]);

                if (isset($response['metaData']['code']) && $response['metaData']['code'] != 200) {
                    return DocoHelpers::response($response, 422);
                }

                return DocoHelpers::response($response);
            } else{
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'ApprovalPengajuanForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}