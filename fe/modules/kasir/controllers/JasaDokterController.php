<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\kasir\models\JasaDokter;

class JasaDokterController extends DocoController
{
    protected $_title = "Master Transaksi Jasa Dokter";
    protected $_module = 'kasir/jasa-dokter/';
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
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
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
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

        $response = $this->_restKasir->get('master-tra-jasa-dokter', [
            'query' => $yiiRestfulParams
        ]);

        $body = json_decode($response->getBody(), true);
        $no = $request->get('start',1);
        foreach ($body['response']['data'] as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['jasadokter_id']);
            unset($value['jasadokter_id']);
            $value['rowNum'] = $no; 
            $value['primary'] = $primaryKey;
            $value['status'] = DocoHelpers::isActive($value['is_active']);
            $data[] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }

    public function actionCreate($id = null)
    {
        $model = new JasaDokter;
        $request = Yii::$app->request;
        if ($post = $request->post()) {
            $model->load($post);
            $action = 'create';
            $idDecrypt = null;
            if ($model->validate()) {
                if ($id) {
                    $idDecrypt = DocoHelpers::decrypt($id);
                    $action = 'update';
                } 
                $response = $this->_restKasir->post("master-tra-jasa-dokter/{$action}", [
                    'query' => [
                        'id' => $idDecrypt
                    ],
                    'form_params' => $model->attributes
                ]);
                $body = json_decode($response->getBody(), true);
            } else {
                return DocoHelpers::response($model->errors, 422, 'JasaDokter');
            }
            return DocoHelpers::response($body, false, 'JasaDokter');
        } else {
            if ($id) {
                $idDecrypt = DocoHelpers::decrypt($id);
                $response = $this->_restKasir->get('master-tra-jasa-dokter/view', [
                    'query' => [
                        'id' => $idDecrypt
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                $model->attributes = isset($body['response']) ? $body['response'] : [];
                $model->is_active = $model->is_active ? 1 : 0;
                $title = Yii::t('fe', 'Ubah') .' '. $this->_title;
            } else {
                $title = Yii::t('fe', 'Tambah') .' '. $this->_title;
            }
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restKasir->delete('master-tra-jasa-dokter/delete?id='.$id);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::response($body);
    }
}
