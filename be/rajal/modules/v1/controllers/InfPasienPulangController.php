<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-08-15 08:01:21
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 11:56:52
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\RujukBalik;
use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\InfoPasienPulangRJRD;
use app\modules\v1\models\PasienBatalPulang;
use app\modules\v1\models\PasienPulang;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\models\bpjs\Bpjs;


class InfPasienPulangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienRanap';
    protected $_title = 'Informasi Pasien Pulang';

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

    private function model(){
        return InfoPasienPulangRJRD::find();
    }

    public function actionIndex()
    {
       try {
            $request = Yii::$app->request;
            $model = new InfoPasienPulangRJRD;
            $query = $this->model();

            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            $orderby = ['tglpasienpulang'=> SORT_DESC];
            $advancedFilters = $request->get('advanced-filter', []);

            $beginOfDay = strtotime("2018-08-01 00:00:00");
            $endOfDay = "2018-08-10 23:59:59";
            // $tgl_awal = date('Y-m-d 00:00:00', strtotime($beginOfDay));
            // $tgl_akhir = date('Y-m-d 23:59:59', strtotime($endOfDay));

            if(isset($advancedFilters)){
                if (isset($advancedFilters['tgl_pendaftaran'])) {
                    if($advancedFilters['tgl_pendaftaran'] != ' - '){
                        $explode = explode(' - ', $advancedFilters['tgl_pendaftaran']);
                        $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_pendaftaran']);
                        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
                    }
                }

                if (isset($advancedFilters['tglpasienpulang'])) {
                    $explode = explode(' - ', $advancedFilters['tglpasienpulang']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($advancedFilters['tglpasienpulang']);
                }

                if (isset($advancedFilters['no_pendaftaran']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($advancedFilters['no_pendaftaran']) ]);
                }

                if (isset($advancedFilters['no_rekam_medik']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_rekam_medik)', strtolower($advancedFilters['no_rekam_medik']) ]);
                }

                if (isset($advancedFilters['nama_pasien']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($advancedFilters['nama_pasien']) ]);
                }
            }
            $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            $query->AndWhere([
                'ruanganakhir_id' => $request->get('ruangan_id', null),
            ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby($orderby);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    private function getData($id = "")
    {
        $model =  $this->model();
        if ($id) {
            $model->where(['pendaftaran_id' => $id]);
        }

        return $model;
    }

    public function actionDataInfoPasienPulang()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $getData = $this->getData($pendaftaran_id);
            $result = $getData->asArray()->one();

            return $result;
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

    public function actionBatalPulang()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $pasienPulangId = $post['pasienpulang_id'];

            $modelBatalPulang = new PasienBatalPulang;
            $modelBatalPulang->attributes =  $post;
            if ($modelBatalPulang->validate()) {
                $pasienPulang = PasienPulang::findOne($pasienPulangId);
                if ($pasienPulang) {
                    $pendaftaran = Pendaftaran::findOne($pasienPulang->pendaftaran_id);
                    if($pendaftaran){
                        $pendaftaran->pasienpulang_id = NULL;
                        $pendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_DIPERIKSA;
                        if($pendaftaran->save(false)){
                            if($modelBatalPulang->save(false)){
                                $pasienPulang->pasienbatalpulang_id = $modelBatalPulang->pasienbatalpulang_id;
                                $pasienPulang->save(false);

                                $modelRujuk = RujukBalik::find()
                                    ->andWhere(['pendaftaran_id' => $pasienPulang->pendaftaran_id])
                                    ->one();
                                if($modelRujuk != null) {
                                    $additional_data = json_decode($modelRujuk->additional_data, true);
                                    $t_prb = [
                                        "noSrb" => $modelRujuk->no_srb,
                                        "noSep" => $additional_data['t_prb']['noSep'],
                                        "user"  => $additional_data['t_prb']['user'],
                                    ];

                                    $modelBpjs = new Bpjs;
                                    $respPRB = $modelBpjs->deletePRB($t_prb);

                                    $additional_data['res_prb_deleted'] = $respPRB;
                                    $modelRujuk->additional_data = json_encode($additional_data);

                                    if(!$modelRujuk->delete()) {
                                        $transaction->rollBack();
                                        return ['message' => 'Data Bpjs di delete','status' => 422];
                                    }
                                }
                                $transaction->commit();
                                return [
                                    'message' => 'Data Berhasil di simpan',
                                ];
                            }else{
                                $transaction->rollBack();
                                $errors = DocoHelpers::parseError($modelBatalPulang->errors,'SetujuiPermintaanKonsul');
                                return ['data' => $errors,'status' => 422];
                            }
                        }else{
                            $transaction->rollBack();
                            $errors = DocoHelpers::parseError($pendaftaran->errors,'SetujuiPermintaanKonsul');
                            return ['data' => $errors,'status' => 422];
                        }
                    }else{
                        $transaction->rollBack();
                        return ['data' => $pendaftaran->errors,'status' => 422];
                    }
                }else{
                    $transaction->rollBack();
                    return ['data' => $pasienPulang->errors,'status' => 422];
                }
            } else {
                $transaction->rollBack();
                return ['data' => $modelBatalPulang->errors,'status' => 422];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Informasi Pasien Pulang Rawat Jalan Rumah Sakit';
            $get = $request->get();

            $model = new InfoPasienPulangRJRD;
            $query = $this->model();
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            $orderby = ['tglpasienpulang'=> SORT_DESC];
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['tgl_pendaftaran'])) {
                    if($advancedFilters['tgl_pendaftaran'] != ' - '){
                        $explode = explode(' - ', $advancedFilters['tgl_pendaftaran']);
                        $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_pendaftaran']);
                        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
                    }
                }

                if (isset($advancedFilters['tglpasienpulang'])) {
                    $explode = explode(' - ', $advancedFilters['tglpasienpulang']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($advancedFilters['tglpasienpulang']);
                }

                 if (isset($advancedFilters['no_pendaftaran']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($advancedFilters['no_pendaftaran']) ]);
                }

                if (isset($advancedFilters['no_rekam_medik']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_rekam_medik)', strtolower($advancedFilters['no_rekam_medik']) ]);
                }

                if (isset($advancedFilters['nama_pasien']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($advancedFilters['nama_pasien']) ]);
                }
            }
            $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            $query->AndWhere([
                'ruanganakhir_id' => $request->get('ruangan_id', null),
            ]);
            $query->orderby($orderby);
            $resQuery = DocoRestActiveFilter::advancedFilter($model, $query);
            $resultData = $resQuery->asArray()->all();

            $result = [];
            foreach ($resultData as $key => $value) {
                $result[] = $value;
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();

        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        try{
            $request = Yii::$app->request;
            $title = 'Informasi Pasien Pulang Rawat Jalan Rumah Sakit';
            $result = [];
            $get = $request->get();

            $model = new InfoPasienPulangRJRD;
            $query = $this->model();
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            $orderby = ['tglpasienpulang'=> SORT_DESC];
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['tgl_pendaftaran'])) {
                    if($advancedFilters['tgl_pendaftaran'] != ' - '){
                        $explode = explode(' - ', $advancedFilters['tgl_pendaftaran']);
                        $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_pendaftaran']);
                        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
                    }
                }

                if (isset($advancedFilters['tglpasienpulang'])) {
                    $explode = explode(' - ', $advancedFilters['tglpasienpulang']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($advancedFilters['tglpasienpulang']);
                }

                 if (isset($advancedFilters['no_pendaftaran']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($advancedFilters['no_pendaftaran']) ]);
                }

                if (isset($advancedFilters['no_rekam_medik']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_rekam_medik)', strtolower($advancedFilters['no_rekam_medik']) ]);
                }

                if (isset($advancedFilters['nama_pasien']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($advancedFilters['nama_pasien']) ]);
                }
            }
            $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            $query->AndWhere([
                'ruanganakhir_id' => $request->get('ruangan_id', null),
            ]);
            $query->orderby($orderby);
            $resQuery = DocoRestActiveFilter::advancedFilter($model, $query);
            $resultData = $resQuery->asArray()->all();

            $no = 0;
            $data = [];
            foreach ($resultData as $key => $value) {
                $no ++;
                $data['No'] = $no;
                $data['Tanggal Pendaftaran'] = date('d M Y H:i:s', strtotime($value['tgl_pendaftaran']));
                $data['Tanggal Pulang'] = date('d M Y H:i:s', strtotime($value['tglpasienpulang']));
                $data['Nomor Pendaftaran'] = $value['no_pendaftaran'];
                $data['Nomor Rekam Medik'] = $value['no_rekam_medik'];
                $data['No Telepon'] = $value['no_telepon_pasien'];
                $data['Nama Pasien'] = $value['nama_pasien'];
                $data['Ruangan'] = $value['ruangan_nama'];
                $data['Jenis Kelamin'] = $value['jenis_kelamin'];
                $data['Penjamin'] = $value['penjamin_nama'];
                $data['Dokter'] = $value['dokter'];
                $data['Cara Pulang'] = $value['carakeluar_nama'];
                $data['Dipulangkan Oleh'] = $value['petugas_pemulang_nama'];
                $result[] = $data;
            }

            $header = ['Periode'=> date('d F Y H:i:s', strtotime($tgl_awal)) . ' - '.date('d F Y H:i:s', strtotime($tgl_akhir))
                        ];

            $options = [
                "skipIncrement" => true,
                "customFormatCode" => [
                    [
                        'startRow' => 'F5',
                        'endRow' => 'F'. (count($resultData) + 5), // +5 karena awalnya dari F5 bukan dari F1
                        'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT
                    ],
                ],
            ];


            $filePath = DocoHelpers::exportExcel("Informasi Pasien Pulang Rawat Jalan Rumah Sakit", $result, $header, $options,[],[],true);

            if (ob_get_length()) {
                ob_end_clean();
            }

            $filePath->save('php://output');
            die;

        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
