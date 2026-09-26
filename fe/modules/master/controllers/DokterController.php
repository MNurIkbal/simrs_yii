<?php

/**
 * @author Randy Vianda Putra
 * @todo master dokter 
 * @copyright 9 November 2018 aweutist
 */

 namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use yii\helpers\Url;
use app\modules\master\models\DokterForm;
use yii\web\UploadedFile;


class DokterController extends DocoController
{
    protected $_title = "Dokter";
    protected $_module = '/master/dokter';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restMaster->request('get', 'dokter/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
            $path = \Yii::getAlias('@webroot');            
            $rootPath = Url::base(true);
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);
                $value['primary'] = $primaryKey;
                $preview_file_gambar = $rootPath . '/media/img/foto-dokter/' .$value['photopegawai'];
                $cek_gambar = $path . '/media/img/foto-dokter/' . $value['photopegawai'];
                $value['foto'] = '';
                if (file_exists($cek_gambar) && !empty($value['photopegawai'])) {
                    $value['foto'] = '<img src="' . $preview_file_gambar . '" width="150px" height = "150px" />';
                } else {
                    $value['foto'] = '';
                }
                unset($value['pegawai_id']);
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            // dump($row);
            // die();
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

    public function actionUpdate($id)
    {
        $title = Yii::t('fe', 'Ubah');
        $id = DocoHelpers::decrypt($id);
        $id_encrypt = DocoHelpers::encrypt($id);
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $model = new DokterForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        $path = \Yii::getAlias('@webroot');
        $image = UploadedFile::getInstance($model, 'photopegawai');
        if ($post) {
            if (isset($image->name)) {
                $model->photopegawai = $image->name;
                $cek_dir = $path . '/media/img/foto-dokter';
                if (!is_dir($cek_dir)){
                    mkdir($cek_dir, 0777, true);
                }
                if ($image->saveAs($path . '/media/img/foto-dokter/' . $model->photopegawai)) {
                    $response = $this->_restMaster->post('dokter/update?id='.$id, [
                        'form_params' => $model->attributes,
                    ]);
                }
    
            } else {
                $model->photopegawai = null;
                $response = $this->_restMaster->post('dokter/update?id='.$id, [
                    'form_params' => $model->attributes,
                ]);
            }
            return DocoHelpers::responseJsonString($response->getBody(), $formName);
        } else {
            $response = $this->_restMaster->get('dokter/detail?id=' . $id);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response']['data'];
            $model->attributes = $attributes;
            $rootPath = Url::base(true);
            $preview_file_gambar = !empty($model->photopegawai) ? [$rootPath .'/media/img/foto-dokter/' . @$model->photopegawai] : false;
        }

        return $this->render('form', get_defined_vars());
    }

    private function getData()
    {
        try {
            $response = $this->_restMaster->request('GET', 'dokter/generate-api');
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_ruangan' => $body['response']['data-ruangan'],
                'data_dokter' => $body['response']['data-dokter'],
            ];

            return $return;
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}