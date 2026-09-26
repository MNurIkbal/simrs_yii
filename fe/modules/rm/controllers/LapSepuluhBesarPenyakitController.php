<?php
// Author : Ramdhan Nurrachman

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LapSepuluhBesarPenyakitController extends DocoController
{
    protected $_title = "Laporan 10 Besar Penyakit";
    protected $_module = 'rm/lap-sepuluh-besar-penyakit/';
    protected $_restRm; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm; $this->_restMaster = Yii::$app->docoRest->master;
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
        $response = $this->_restRm->get('allow/get-instalasi-pelayanan');
        $body = json_decode($response->getBody(), TRUE);
        $instalasi = $body['response'];
        $workspace = Yii::$app->docoVars->workspace('instalasi_name');
    
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
            $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['diagnosa_id']);
                unset($value['diagnosa_id']);

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['diagnosa_kode'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['tglmorbiditas'] = date("j M Y", strtotime($value['tglmorbiditas']));

                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    // Beware Section Data Selection
    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        $url = "";
        try {
            $path = Yii::getAlias("@download") . "/Laporan Sepuluh Besar Penyakit.xlsx";

            $response = $this->_restRm->get('lap-sepuluh-besar-penyakit/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), True);
            return DocoHelpers::downloadFile($path,true);              
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }
    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-ruangan?advanced-filter[instalasi_id]='.$parent_label);
            $body = json_decode($response->getBody(), True);            
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @todo Fungsi untuk export pdf
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Laporan 10 Besar Penyakit.pdf";

            $restRm = $this->_restRm->get('lap-sepuluh-besar-penyakit/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);

            // print_r("<pre>");
            // print_r(json_decode($restRm->getBody(), true));
            // print_r("</pre>");
            // die();

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }
}