<?php

/**
 * @Author: Anggoro <tri.anggoro@docotel.com>
 * @Date:   2019-04-25
 */

namespace Doco\rm\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class RekapTempatTidurController extends DocoController
{
	/**
     * @todo Protected vars
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    protected $_restRm;
    protected $allowAction = ['*'];
    protected $_months = [];

    /**
     * @todo Init function
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
        for ($i=1; $i <= 12; $i++) {
            $this->_months[$i] = date('M', mktime(0, 0, 0, $i, 10));
        }
    }

    /**
     * @todo Behaviors function
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    /**
     * @todo Menampilkan Rekap  Penggunaan Tempat Tidur dengan default tahun sekarang
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $months = $this->_months;

            // Year
            $curDate = (int) date('Y');
            for ($i=1; $i <= 5; $i++) {
                $years[$curDate] = $curDate;
                $curDate--;
            }

            return $this->render('index', get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo API Get Data
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function actionGetData($tahun = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        try {
            $tahun = is_null($tahun) ? date('Y') : $tahun;

            $respond = $this->_restRm->get('rekap-tempat-tidur/', [
                'query' => $filter
            ]);
            $body = json_decode($respond->getBody(), true);
            $data = $body["response"]["result"];
            $tahun = $body["response"]["tahun"];

            $count = 1;
            foreach ($data as $key => $value) {
                $row["rowNum"] = $count;
                $row["tahun"] = $tahun;
                $row["ruangan_nama"] = $value["ruangan_nama"];
                $row["ruangan_id"] = $value["ruangan_id"];
                for ($i=1; $i <= 12; $i++) {
                    $key = $i < 10 ? "0".$i : $i;
                    $monthName = date('M', mktime(0, 0, 0, (int)$key, 10));
                    $row[$monthName] = $value[$key];
                    unset($value[$key]);
                }
                $list_data[] = $row;
                $count++;
            }

            $result['data'] = $list_data;
            $result['recordsTotal'] = $count;
            $result['recordsFiltered'] = $count;
            $result['tahun'] = $tahun;
            return DocoHelpers::response($result);

        } catch (\Exception $e) {
            return DocoHelpers::response($e->getMessage());
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage());
        }
    }

    /**
     * @todo Action untuk melakukan proses export pdf
     * @author Anggoro <tri.anggoro@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            $path = Yii::getAlias("@download") . "/Rekap-Laporan-Tempat-Tidur.pdf";
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

            $response = $this->_restRm->get('rekap-tempat-tidur/export-pdf', [
                'query' => $filter,
                'save_to' => $path
            ]);

            // dump(json_decode($response->getBody(), true)); die();

            return DocoHelpers::previewPdf($path);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Anggoro <tro.anggoro@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Rekap Laporan Tempat Tidur.xlsx";

            $response = $this->_restRm->get('rekap-tempat-tidur/export-excel', [
                'query' => $filter,
                'save_to' => $path
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}