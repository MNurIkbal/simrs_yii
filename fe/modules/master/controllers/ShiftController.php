<?php 

/**
 * @author Randy Vianda Putra
 * @todo Master Shift
 * @copyright 6 September 2018 aweutist
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\master\models\ShiftForm;
use yii\helpers\ArrayHelper;

class ShiftController extends DocoController
{

    protected $_title = "Shift";
    protected $_module = '/master/shift';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $status[0] = Yii::t('fe', 'Tidak Aktif');
        $status[1] = Yii::t('fe', 'Aktif');

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'shift/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['shift_id']);
                $value['primary'] = $primaryKey;
                $value['shift_jamawal'] = date('H:i', strtotime($value['shift_jamawal']));
                $value['shift_jamakhir'] = date('H:i', strtotime($value['shift_jamakhir']));
                if ($value['is_active']) {
                    $value['is_active'] = "Aktif";
                }else{
                    $value['is_active'] = "Tidak Aktif";
                }
                unset($value['shift_id']);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        $title = Yii::t('fe', 'Tambah');
        $model = new ShiftForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        
        if ($post) {
            if ($model->validate()) {
                $response = $this->_restMaster->request('POST', 'shift/save',[
                    'form_params' => $post
                ]);
                $response = json_decode($response->getBody(), true);

                return DocoHelpers::response($response,false,'ShiftForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'ShiftForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        }

        return $this->render('form', get_defined_vars());
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->request('DELETE', 'shift/delete',[
                'query' => ['id' => $id ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id)
    {
        $title = Yii::t('fe', 'Ubah');
        $id = DocoHelpers::decrypt($id);
        $model = new ShiftForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        if ($post) {
            if ($model->validate()) {
                $model->is_active = $post['ShiftForm']['is_active'];
                $response = $this->_restMaster->request('POST', 'shift/update?id=' . $id, [
                    'form_params' => $model
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response,false,'ShiftForm');
            } else {
                $errors = DocoHelpers::parseError($model->errors,'ShiftForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        } else {
            $response = $this->_restMaster->get('shift/view-data?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $model->attributes = $body['response'];
            $model->shift_namalainnya = $body['response']['shift_namalainnya'];
            $model->is_active = $body['response']['is_active'];
            $model->is_active = ($model->is_active) ? '1' : '0' ;
            $model->shift_jamawal = date('H:i', strtotime($model->shift_jamawal));
            $model->shift_jamakhir = date('H:i', strtotime($model->shift_jamakhir));
        }

        return $this->render('form', get_defined_vars());
    }

    /**
     * @todo Method untuk export excel shift
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $response = $this->_restMaster->get('shift/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($url);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
