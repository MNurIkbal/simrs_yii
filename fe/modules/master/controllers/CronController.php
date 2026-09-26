<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-19 13:31:14
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CronController extends DocoController
{
    protected $_restMaster;

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

    /**
     * @todo Method untuk menampilkan halaman awal master list cron
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk mendapatkan data cron
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataCron()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            $restMaster = $this->_restMaster->get('cron/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restMaster->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                	$no++;
                    $primaryKey = DocoHelpers::encrypt($value['cron_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['cron_nama'] = $value['cron_nama'];
                    $value['cron_tgl_mulai'] = isset($value['cron_tgl_mulai']) ? date('d-m-Y h:i:s', strtotime($value['cron_tgl_mulai'])) : '';
                    $value['last_modified_date'] = isset($value['last_modified_date']) ? date('d-m-Y h:i:s', strtotime($value['last_modified_date'])) : '';
                    $value['created_by'] = isset($value['created_by']) ? $alue['created_by'] : '';
                    unset($value['cron_id']);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }


    /**
     * @todo Method untuk syncronasi manual
     * @author ali.padilah@docotel.com
     */
    public function actionSync($id = null)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->get('cron/sync?id='.$id);
            $response = json_decode($response->getBody(),true);

            return DocoHelpers::response($response,false);                

        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        } catch (\Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }
    }
}