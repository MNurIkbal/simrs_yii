<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class KonfigWorklistController extends DocoController
{
    protected $_title = "Master Konfig Worklist";
    protected $_module = '/master/konfig-worklist/';
    protected $_restMaster;
    protected $_workspace;
    protected $_ruangan_id;
    protected $_ruangan_nama;
    protected $_request;
    protected $_session;
    protected $_instalasi;
    protected $_backUrl;
    protected $_uid;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $this->_backUrl = 'konfig-worklist/';
        $this->_uid = Yii::$app->docoVars->user('uid');
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
        try {
            $status_arr = ['0' => Yii::t('fe', 'Aktif'), '1' => Yii::t('fe', 'Tidak aktif')];
            return $this->render('index', get_defined_vars());
        } catch (\Exception $e) {
            $status_arr = [];
            return $status_arr;
        }
    }

    public function actionGetKonfigTable()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        try {
            $response = $this->_restMaster->get('konfig-worklist/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kode_transaksi']);
                $value['primary'] = $primaryKey;
                unset($value['kode_transaksi']);
                $status = false;
                if ($value['additional_value'] == 'true') {
                    $status = true;
                }
                $value['aktif'] = DocoHelpers::switchStatus($status, $primaryKey);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->put('konfig-worklist/change-status?id='.$id, [
                'form_params' => ['additional_value' => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message,  
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

}
