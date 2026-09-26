<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-02 11:28:49
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-02 16:39:46
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
use app\modules\rajal\models\AnamnesaForm;
use app\modules\rajal\models\BuatJanjiPoliForm;
use app\modules\rajal\models\PendaftaranForm;

class LaporanController extends DocoController
{
    protected $_title = "Rajal :: Laporan pasien rawat jalan";
    protected $_module = '/rajal';
    protected $_controller = '/rajal/laporan';
    protected $_page;
    protected $_restRajal;
    protected $_id_ruangan;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';
        $this->_page = Yii::t('fe', 'Laporan pasien rawat jalan');
    }

    public function actionDaftarPasien()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $status = $this->_status;
        $page = $this->_page;

        // data select
        $list_data = $this->getListData();
        $data_pegawai = $list_data["data_pegawai"];
        $data_penjamin = $list_data["data_penjamin"];
        $data_statusperiksa = $list_data["data_statusperiksa"];
        $data_carabayar = $list_data["data_carabayar"];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetDataDaftarPasien()
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
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restRajal->get('lap-kunjungan-pasien/index?ruangan_id='.$this->_id_ruangan.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

   	// export excel laporan pasien rajal
    public function actionExportExcelKunjunganRajal()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruangan_name = DocoHelpers::encrypt($this->_nama_ruangan);

        $result = [];
        $url = "";
        try {
            $response = $this->_restRajal->get('lap-kunjungan-pasien/export-excel-kunjungan-rajal?ruangan_name='.$ruangan_name.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            return $this->downloadFile($body["response"]);
            return file_get_contents($body["response"]);
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }

    /**
     *
     * private function
     *
     */
    private function getListData()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('lap-kunjungan-pasien/get-list-data?id_ruangan='.$this->_id_ruangan);
            $body = json_decode($response->getBody(),TRUE);

            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $data_carabayar = empty($body['response']['data-carabayar']) ? [] : $body['response']['data-carabayar'];

            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
                'data_carabayar' => $data_carabayar,
            ];

            return $result;
        } catch (RequestException $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
                'data_carabayar' => [],
            ];
        } catch (\Exception $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
                'data_carabayar' => [],
            ];
        }
    }

    private function downloadFile($filename)
    {
        $file = basename($filename);
        $fp = fopen($file, 'w');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}