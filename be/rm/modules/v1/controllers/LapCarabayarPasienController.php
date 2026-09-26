<?php 

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPersentaseCarabayarV;
use app\modules\v1\models\CaraBayar;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapCarabayarPasienController extends DocoActiveController
{
    public $modelClass = '';
    const RJ = 'RJ';
    const RD = 'RD';
    const RI = 'RI';
    const MCU = 'MCU';
    const PENUNJANG = 'PENUNJANG';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        // unset($actions['view']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $getRequest = Yii::$app->request->get();

        return $this->generateData(self::RJ, $getRequest);
    }

    public function actionGetDataIgd()
    {
        $getRequest = Yii::$app->request->get();

        return $this->generateData(self::RD, $getRequest);
    }

    public function actionGetDataRanap()
    {
        $getRequest = Yii::$app->request->get();

        return $this->generateData(self::RI, $getRequest);
    }

    public function actionGetDataMcu()
    {
        $getRequest = Yii::$app->request->get();

        return $this->generateData(self::MCU, $getRequest);
    }

    public function actionGetDataPenunjang()
    {
        $getRequest = Yii::$app->request->get();

        return $this->generateData(self::PENUNJANG, $getRequest);
    }

    public function actionGenerateApi()
    {
        $listCaraBayar = $this->getListCarabayar();

        return [
            'carabayar' => $listCaraBayar,
        ];
    }

    private function generateData($instalasi, $filter)
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
        }
        $listCaraBayar = $this->getListCarabayar();
        $data = [];
        try {
            $query_total = (new \yii\db\Query())
                ->select([
                    'SUM(total_carabayar) AS total_carabayar',
                    'MAX(tgl_pendaftaran) AS tgl_pendaftaran'
                ])
                ->from('laporanpersentasecarabayar_v')
                ->where(['between', 'tgl_pendaftaran', $start, $end])
                ->andWhere(['jenis' => $instalasi]);
            $model_total = $query_total->one();

            $jumlah = isset($model_total['total_carabayar']) ? (int)$model_total['total_carabayar'] : 0;
            $tgl_pendaftaran = isset($model_total['tgl_pendaftaran']) ? $model_total['tgl_pendaftaran'] : null;
            $data[0]['jumlah'] = $jumlah;
            $data[0]['tgl_pendaftaran'] = $tgl_pendaftaran;
            $data[1]['jumlah'] = ($jumlah != 0) ? 1 : 0;
            $data[1]['tgl_pendaftaran'] = $tgl_pendaftaran;
            
            foreach($listCaraBayar as $key => $value) {
                $query = (new \yii\db\Query())
                    ->select([
                        'SUM(total_carabayar) AS total_carabayar',
                    ])
                    ->from('laporanpersentasecarabayar_v')
                    ->where(['between', 'tgl_pendaftaran', $start, $end])
                    ->andWhere(['jenis' => $instalasi])
                    ->andWhere(['carabayar_id' => $value['carabayar_id']]);
                $model = $query->one();
                $total_carabayar = isset($model['total_carabayar']) ? (int)$model['total_carabayar'] : 0;
                $data[0][$value['carabayar_nama']] =  $total_carabayar;
                $data[1][$value['carabayar_nama']] = ($jumlah != 0) ? (float)$total_carabayar / $jumlah : 0;
            }

            return ['data' => $data];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionExportExcel()
    {
        // try {
            $request = Yii::$app->request->get();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if (isset($request['advanced-filter'])) {
                if(isset($request['advanced-filter']['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                }
            }
            
            $result = [];
            $tmp = [];
            $subTitle = [];
            $SheetName = [];
            $data = [];

            $data[self::RJ] = $this->generateData(self::RJ, $request);
            $data[self::RD] = $this->generateData(self::RD, $request);
            $data[self::RI] = $this->generateData(self::RI, $request);
            $data[self::MCU] = $this->generateData(self::MCU, $request);
            $data[self::PENUNJANG] = $this->generateData(self::PENUNJANG, $request);

            $listCaraBayar = $this->getListCarabayar();
            $no = 0;
            foreach ($data as $key => $value) {
                foreach ($value['data'] as $detail) {     
                    foreach($listCaraBayar as $carabayar) {
                        $tmp[$carabayar['carabayar_nama']] = $detail[$carabayar['carabayar_nama']];
                    }
                    $tmp['Jumlah']  = $detail['jumlah'];
                    $row[] = $tmp;
                }
                $result[$no] = $row;

                switch ($key) {
                    case self::RJ:
                        $subTitle[$no] = 'PASIEN RAWAT JALAN';
                        $SheetName[$no] = 'RAWAT JALAN';
                        break;
                    case self::RD:
                        $subTitle[$no] = 'PASIEN RAWAT DARURAT';
                        $SheetName[$no] = 'RAWAT DARURAT';
                        break;
                    case self::RI:
                        $subTitle[$no] = 'PASIEN RAWAT INAP';
                        $SheetName[$no] = 'RAWAT INAP';
                        break;
                    case self::MCU:
                        $subTitle[$no] = 'PASIEN MCU';
                        $SheetName[$no] = 'MCU';
                        break;
                    case self::PENUNJANG:
                        $subTitle[$no] = 'PASIEN PENUNJANG';
                        $SheetName[$no] = 'PENUNJANG';
                        break;
                    default:
                        $result[$no] = '';
                        break;
                }
                $tmp = [];
                $row = [];
                $no++;
            }

            $title   = Yii::t('app', 'LAPORAN CARA BAYAR PASIEN RUMAH SAKIT');
            $header = [
                'Periode' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            ];
            $filePath = DocoHelpers::exportExcelMultiSheet($title, $result, $header, array(
                "skipIncrement" => true, 
                "subHeader" => $subTitle
            ),[],[],true, $SheetName);
            // $filePath = DocoHelpers::exportExcel(self::TITLE, $tmp['dataKeluar'], $header,  array("uploadPath" => "./uploads", "skipIncrement" => false),[],[],true);
            // return $filePath;

            $filePath->save('php://output');
            die;

        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return ['message' => $e->getMessage()];
        // }
    }

    private function getListCarabayar()
    {
        // cara bayar
        $modelCaraBayar = CaraBayar::find();
        $modelCaraBayar->where(['is_active' => true]);

        return $modelCaraBayar->all();
    }
}