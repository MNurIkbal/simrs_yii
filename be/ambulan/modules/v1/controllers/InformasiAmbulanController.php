<?php
    /**
    * @author iqbal@docotel.com
    * @since 2019-02-27 10:11:20 
    * @desc 
    */
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\AbPgetKetersediaanAmbulanParam;
use app\modules\v1\models\InformasiAmbulanFn;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;

class InformasiAmbulanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\AbPgetKetersediaanAmbulanParam';
    protected $_dateNow;

    public function init()
    {
        parent::init();
        $this->_dateNow = date('Y-m-d H:i:s');
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    private function Model(){
        $model = new AbPgetKetersediaanAmbulanParam(['extParam' =>[$this->_dateNow ]]);
        return $model::find();
    }

    private function ModelInformasiAmbulan(){
        $model = new InformasiAmbulanFn();
        return $model::find();
    }


    public function actionGetDataAmbulanDetail()
    {
        try {
            $request = Yii::$app->request;
            $model = new InformasiAmbulanFn();

            $query = $this->modelInformasiAmbulan();
            $query->where(['ambulan_id' => $request->get('ambulan_id') ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['tgl_pemakaiandari' => SORT_DESC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new AbPgetKetersediaanAmbulanParam(['extParam' =>[$this->_dateNow]]);

            $query = $this->model();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['no_polisi' => SORT_ASC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGenerateApi()
    {
        $status_ambulan = Cache::getLookUpByKey('status_ambulan');
        $jenis_ambulan = [
            true => DocoConstants::EMERGENCY,
            false => DocoConstants::NON_EMERGENCY,
        ];

        return [
            'jenis_ambulan' => $jenis_ambulan,
            'status_ambulan' => $status_ambulan,
        ];
    }

    public function actionDetail($ambulan_id)
    {
        try {
            $query = $this->model();
            $result = $query->where(['ambulan_id' => $ambulan_id])->one();
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionExportExcel()
    {
        $title = 'Informasi Ambulan';
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $searchNoPolisi = '';
            $searchJenisAmbulan = '';
            $searchStatusAmbulan = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['no_polisi'])) {
                    $searchNoPolisi = $advancedFilters['no_polisi'];
                }

                if (!empty($advancedFilters['is_emergency'])) {
                    $searchJenisAmbulan = $advancedFilters['is_emergency'];
                    $searchJenisAmbulan = ($searchJenisAmbulan ) ? DocoConstants::EMERGENCY : DocoConstants::NON_EMERGENCY;
                }

                if (!empty($advancedFilters['status_ambulan_id'])) {
                    $searchStatusAmbulan = $advancedFilters['status_ambulan_id'];
                    $searchStatusAmbulan = Lookup::findOne($searchStatusAmbulan);
                    $searchStatusAmbulan = $searchStatusAmbulan->lookup_name;

                }
            }

            $model = new AbPgetKetersediaanAmbulanParam(['extParam' =>[$this->_dateNow]]);
            $query = $this->model();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['no_polisi' => SORT_ASC]);
            $query = $query->all();

            $data = [];
            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    $data[$counter]['Nomor Polisi'] = $value->no_polisi;
                    $data[$counter]['Jenis Ambulan'] = $value->jenis_ambulan;
                    $data[$counter]['Status Ambulan'] = $value->status_ambulan;
                    $counter++;
                }
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Nomor Polisi' => $searchNoPolisi,
                'Jenis Ambulan' => $searchJenisAmbulan,
                'Status Ambulan' => $searchStatusAmbulan,
            ];

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Diunduh Oleh' => $ruangan->ruangan_nama,
                ]
            ];
            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionExportExcelDetail()
    {
        $title = 'Detail Informasi Ambulan';
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $getDataAmbulan = $this->actionDetail($request->get('ambulan_id'));

            $model = new InformasiAmbulanFn();
            $query = $this->modelInformasiAmbulan();
            $query->where(['ambulan_id' => $request->get('ambulan_id') ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['tgl_pemakaiandari' => SORT_DESC]);
            $result = $query->all();

            $data = [];
            if (!empty($result)) {
                $counter = 0;
                foreach ($result as $index => $value) {
                    $data[$counter]['Tanggal Pemakaian'] = !empty($value->tgl_pemakaiandari) ? date('d-M-Y', strtotime($value->tgl_pemakaiandari)) : '-' ;
                    $data[$counter]['Tanggal Kembali'] = !empty($value->tgl_realisasikembali) ? date('d-M-Y', strtotime($value->tgl_realisasikembali)) : '-' ;
                    $data[$counter]['Nomor Pemesanan'] = $value->no_pesanambulan;
                    $data[$counter]['Nama Pemesanan'] = $value->nama_pemesan;
                    $data[$counter]['Supir'] = !empty($value->supir) ? $value->supir : '-';
                    $data[$counter]['Jarak Pemakaian (Km)'] = $value->jarak_pemakaian;
                    $data[$counter]['Nominal Tagihan (Rp.)'] = $value->nominal_tagihan;
                    $data[$counter]['Total (Rp.)'] = $value->biaya_tambahan + $value->nominal_tagihan;
                    $counter++;
                }
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Nomor Polisi' => !empty($getDataAmbulan->no_polisi) ? $getDataAmbulan->no_polisi : '-',
                'Jenis Ambulan' => !empty($getDataAmbulan->jenis_ambulan) ? $getDataAmbulan->jenis_ambulan : '-',
            ];

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Diunduh Oleh' => $ruangan->ruangan_nama,
                ]
            ];
            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #no_polisi# => Untuk menampilkan search No Polisi
    * @attribute #jenis_ambulan# => Untuk menampilkan search Jenis Ambulan
    * @attribute #status_ambulan# => Untuk menampilkan search Status Ambulan
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #title# => title
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::find()->where([
            'ruangan_id' => $ruangan_id
        ])->one();

        $title = 'Informasi Ambulan';
        $searchNoPolisi = '';
        $searchJenisAmbulan = '';
        $searchStatusAmbulan = '';
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (!empty($advancedFilters['no_polisi'])) {
                $searchNoPolisi = $advancedFilters['no_polisi'];
            }

            if (!empty($advancedFilters['is_emergency'])) {
                $searchJenisAmbulan = $advancedFilters['is_emergency'];
                $searchJenisAmbulan = ($searchJenisAmbulan ) ? DocoConstants::EMERGENCY : DocoConstants::NON_EMERGENCY;
            }

            if (!empty($advancedFilters['status_ambulan_id'])) {
                $searchStatusAmbulan = $advancedFilters['status_ambulan_id'];
                $searchStatusAmbulan = Lookup::findOne($searchStatusAmbulan);
                $searchStatusAmbulan = $searchStatusAmbulan->lookup_name;

            }
        }

        $model = new AbPgetKetersediaanAmbulanParam(['extParam' =>[$this->_dateNow]]);
        $query = $this->model();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderby(['no_polisi' => SORT_ASC]);
        $query = $query->asArray()->all();

        $data = [];
        if (!empty($query)) {
            foreach ($query as $index => $value) {
                $data[] = $value;
            }
        }
        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y H:i:s'),
            '#title#' => $title,
            '#no_polisi#' => $searchNoPolisi,
            '#jenis_ambulan#' => $searchJenisAmbulan,
            '#status_ambulan#' => $searchStatusAmbulan,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];
        $print->Output();
    }

    /**
    * @controller actionExportPdfDetail
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #no_polisi# => Untuk menampilkan search No Polisi
    * @attribute #jenis_ambulan# => Untuk menampilkan search Jenis Ambulan
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #title# => title
    */
    public function actionExportPdfDetail()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::find()->where([
            'ruangan_id' => $ruangan_id
        ])->one();

        $title = 'Informasi Ambulan';
        $getDataAmbulan = $this->actionDetail($request->get('ambulan_id'));

        $model = new InformasiAmbulanFn();
        $query = $this->modelInformasiAmbulan();
        $query->where(['ambulan_id' => $request->get('ambulan_id') ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderby(['tgl_pemakaiandari' => SORT_DESC]);
        $result = $query->asArray()->all();

        $data = [];
        if (!empty($result)) {
            foreach ($result as $index => $value) {
                $data[] = $value;
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y H:i:s'),
            '#title#' => $title,
            '#no_polisi#' => !empty($getDataAmbulan->no_polisi) ? $getDataAmbulan->no_polisi : '-',
            '#jenis_ambulan#' => !empty($getDataAmbulan->jenis_ambulan) ? $getDataAmbulan->jenis_ambulan : '-',
            '#datatable#' => $this->renderPartial('_cetak_detail', [
                'data' => $query->asArray()->all(),
            ]),
        ];
        $print->Output();
    }

}