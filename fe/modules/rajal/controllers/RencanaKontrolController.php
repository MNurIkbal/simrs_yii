<?php

/**
 * @Author: afil
 * @Date:   2018-01-17 14:24:34
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-25 18:29:49
 * @Description: 
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rajal\models\InformasiForm;
use app\modules\rajal\models\BuatJanjiPoliForm;

class RencanaKontrolController extends DocoController
{
    protected $_title = "Rencana kontrol";
    protected $_module = '/rajal/rencana-kontrol';
    protected $_page;
    protected $_restRajal;
    protected $_id_ruangan;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_page = Yii::t('fe', 'Rencana kontrol');
    }

    public function actionIndex()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $status = $this->_status; 
        $options = $this->_options;

        // data select

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

        try {
            $response = $this->_restRajal->get('inf-rencana-kontrol/index?ruangan_id='.$this->_id_ruangan.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);

            $result = [];
            $body = json_decode($response->getBody(),TRUE);

            $no = $request->post('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $value['rowNum'] = $no;
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['buatjanjipoli_id']);

                $value['aksi'] = $this->getAksi($value);
                unset($value['buatjanjipoli_id']);

                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionUpdate($id = null)
    {
        // Init
        $status = $this->_status; $options = $this->_options;

        $request = Yii::$app->request;
        $title = \Yii::t('fe', $this->_title);
        $action = \Yii::t('fe', 'Ubah');
        $model = new BuatJanjiPoliForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            try {
                $response = $this->_restMaster->put('inf-rencana-kontrol/update?id='.$id, [
                    'form_params' => $model->attributes
                ]);

                return DocoHelpers::responseJsonString($response->getBody(), $formName);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        } else {
            $response = $this->_restRajal->get('inf-rencana-kontrol/view?ruangan_id='.$this->_id_ruangan.'&id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response']["data"];
            $model->attributes = $attributes;

            return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRajal->delete('rencana-kontrol/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", [
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function getAksi($data)
    {
        $primaryKey = $data['buatjanjipoli_id'];
        $primaryKey = DocoHelpers::encrypt($primaryKey);

        $return  = '<table class="no-border"><tr>';
        $return .= '<td>&nbsp;'.Html::button(
            '<i class="fa fa-pencil" aria-hidden="true"></i>', [
                'class' => 'btn btn-dark-turquise btn-xs data-update',
                'action' => Url::to([$this->_module .'/update', 'id' => $primaryKey]),
                'data-toggle' => 'modal',
                'data-target' => '#modal_backdrop',
                'data-popup' => "tooltip",
                'data-placement' => 'bottom',
                'data-original-title' => \Yii::t('fe', 'Ubah'),
            ]
        ).'&nbsp;</td>';
        $return .= '<td>&nbsp;'.Html::a(
            '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                'class' => 'btn btn-danger btn-xs data-delete',
                'action' => Url::to([$this->_module .'/delete', 'id' => $primaryKey]),
                'data-popup' => "tooltip",
                'data-placement' => 'bottom',
                'title' => \Yii::t('fe', 'Hapus'),
            ]
        ).'&nbsp;</td>';
        $return .= '</tr></table>';

        return $return;
    }
}