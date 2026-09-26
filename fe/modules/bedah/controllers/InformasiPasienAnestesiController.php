<?php

namespace Doco\bedah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;
use app\modules\bedah\components\traits\PostOperativeAnestesiTrait;
use app\modules\bedah\components\traits\IntraOperativeAnestesiTrait;
use app\modules\bedah\components\traits\AnestesiTrait;
use app\modules\bedah\components\traits\BeforeLeavingTrait;
use app\modules\bedah\components\traits\PreAnestheticTrait;

class InformasiPasienAnestesiController extends DocoController
{

    use PostOperativeAnestesiTrait;
    use IntraOperativeAnestesiTrait;
    use AnestesiTrait;
    use PreAnestheticTrait;
    use BeforeLeavingTrait;

    protected $_title = "Informasi Pasien Anestesi";
    protected $_restBedah;

    public function init()
    {
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $list_sts_op = [];

        $pack_data = $this->helper->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-anestesi/get-pack-data',
            'method' => 'GET',
            'payload' => []
        ]);

        foreach ($pack_data['list_sts_op'] as $key => $value) {
            $list_sts_op[] = [
                'id' => $value['lookup_id'],
                'text' => $value['lookup_name']
            ];
        }
        $status_anestesi = [
            ["id" => "Sudah Proses", "text" => "Sudah Proses"],
            ["id" => "Belum Proses", "text" => "Belum Proses"],
        ];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        $draw = $request->get('draw', 1);
        $data = [];

        try {
            $body = $this->helper->guzzleExec($this->_restBedah, [
                'url' => 'inf-pasien-anestesi/index',
                'method' => 'GET',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);
            
            $no = $request->get('start', 1);
            foreach ($body['data'] as $key => $value) {
                $pemeriksaan = [];
                $no++;
                $value['primary'] = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $value['tgl_operasi'] =  DocoHelpers::convDateTime($value['tgl_operasi'], true, false);
                if (!empty($value['pemeriksaan'])) {
                    foreach ($value['pemeriksaan'] as $k => $v) {
                        if (!in_array($v['tindakan'], $pemeriksaan)) {
                            $pemeriksaan[] = $v['tindakan'];
                        }
                    }
                }
                $value['list_pemeriksaan'] = implode(" <br/> ", $pemeriksaan);
                $value['rowNum'] = $no;
                
                $data[$key] = $value;
            }
            
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionProses()
    {
        $title = $this->_title;
        // passing id from action button on "list" pasien anestesi
        $id = Yii::$app->request->get('id');
        return $this->render('proses', get_defined_vars());
    }
}
