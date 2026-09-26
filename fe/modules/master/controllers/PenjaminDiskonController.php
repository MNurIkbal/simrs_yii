<?php
/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\master\models\PenjaminDiskonForm;
use GuzzleHttp\Exception\RequestException;

class PenjaminDiskonController extends DocoController
{
    protected $_title = "Otoritas Penjamin";
    protected $_module = 'master/penjamin-diskon/';
    protected $_restMaster;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new PenjaminDiskonForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        try {
            if ($request->post()) {
                $post = $request->post();
                $penjaminDiskonForm = $post['PenjaminDiskonForm'];
                $model->load($request->post());
                if ($model->validate()) {
                    return $this->guzzleExec($this->_restMaster, [
                        'url' => 'penjamin-diskon/create',
                        'method' => 'post',
                        'payload' => [
                            'form_params' => $post,
                        ],
                        'returnResponse' => true
                    ]);      
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            }

            $caraBayarResponse = $this->_restMaster->get('cara-bayar/get-list-cara-bayar');
            $body = json_decode($caraBayarResponse->getBody(), TRUE);
            $caraBayarList = $body['response'];
    
            return $this->renderAjax('form', get_defined_vars());
    
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new PenjaminDiskonForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        try {
            if ($request->post()) {
                $post = $request->post('PenjaminDiskonForm');
                Yii::error($post);
                $post['id_before_update'] = DocoHelpers::decrypt($post['id_before_update']);
                $model->attributes = $post;
                if ($model->validate()) {
                    return $this->guzzleExec($this->_restMaster, [
                        'url' => 'penjamin-diskon/update',
                        'method' => 'post',
                        'payload' => [
                            'query' => [
                                'id' => $id
                            ],
                            'form_params' => $post
                        ],
                        'returnResponse' => true
                    ]);  
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            }
        
            $caraBayarResponse = $this->_restMaster->get('cara-bayar/get-list-cara-bayar');
            $body = json_decode($caraBayarResponse->getBody(), TRUE);
            $caraBayarList = $body['response'];
    
            $penjaminResponse = $this->_restMaster->get('penjamin/list-penjamin');
            $body = json_decode($penjaminResponse->getBody(), TRUE);
            $penjaminList = $body['response'];
    
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'penjamin-diskon/view',
                'payload' => [
                    'query' => [
                        'id' => $id
                    ]
                ]
            ]);
            
            $model->attributes = $response;
            $model->is_active = ($model->is_active) ? '1' : '0';

            $id_before_update = DocoHelpers::encrypt($id);
    
            return $this->renderAjax('form', get_defined_vars());

        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        $response = $this->guzzleExec($this->_restMaster, [
            'method' => 'delete',
            'url' => 'penjamin-diskon/delete',
            'payload' => [
                'query' => [
                    'id' => $id
                ]
            ]
        ]);
        return DocoHelpers::response($response);
    }
}
