<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-18 11:08:18
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-15 11:15:49
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\Infokonsulpoli;
use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use app\modules\v1\models\HasilPemeriksaanLabRoche; //bedah
use app\modules\v1\models\HasilPemeriksaanLabWynacom; 
use app\modules\v1\models\PasienMorbiditas;
use app\modules\v1\models\ResumeMedis;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\InfoPasienLabDetailView;
use app\modules\v1\models\InfoPasienRadDetailView;
use app\modules\v1\models\InfoPasienOperasiDetailView;
use app\modules\v1\models\InfoMorbiditasView;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\SoapRj;
use Doco\models\Pegawai;
use Doco\models\PasienMasukPenunjang;
use app\modules\v1\models\HasilPemeriksaanLab;
use Doco\models\radiologi\HasilPemeriksaanRadView;
use Doco\models\radiologi\PemeriksaanPasienRadiologiView;
use app\modules\v1\models\ResumeMedisRi;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\Pendaftaran;
use Doco\Services\Cache;
use yii\db\Expression;

class ResumeMedisController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ResumeMedis';

    public $messageBroker = [
        'save-resume-odc' => [
            'services' => [
                'SatuSehat' => [
                    'EncounterResume' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                        'taskid' => '4',
                        'update_from' => 'save_resume_medis'
                    ],
                ],
            ]
        ]
    ];

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

    public function actionGetDataResumeMedis()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        try {
            return ResumeMedisRi::resumeByRegistrationId($pendaftaran_id);
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        try {
            $getPemeriksaanFisik = PemeriksaanFisik::find()->with(['anamnesa' => function($query){
                $query->select(['is_resikojatuh', 'is_nyeri', 'skala_nyeri']);
            }])->where(['pemeriksaanfisik_t.pendaftaran_id' => $pendaftaran_id, 'pemeriksaanfisik_t.pasien_id' => $pasien_id])->asArray()->one();
            return [
                'pemeriksaanfisik' => $getPemeriksaanFisik,
            ];
        } catch (\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        }
    }
    public function actionGetTerapi()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new RiwayatTindakanView();
            $query = $model::find();
            if(isset($get['type'])){
                if($get['type'] == 'BMHP'){
                    $query->andWhere(['tipe_pelayanan' => $get['type']]);
                }else{
                    $query->andWhere(['IN','tipe_pelayanan', ['TINDAKAN', 'PAKET']]);
                }
            }
            if(isset($get['pendaftaran_id']))
            {
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                    'query' => $query,
                ]);
        } catch (\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        }
    }
    public function actionGetReseptur()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new InfoResepturDetailView();
            $query = $model::find();
            if(isset($get['pendaftaran_id'])){
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            if(isset($get['pasien_id'])){
                $query->andWhere(['pasien_id' => $get['pasien_id']]);
            }
            // $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                    'query' => $query,
                ]);
        } catch (\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        }
    }
    public function actionGetDiagnosa()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new InfoMorbiditasView();
            $query = $model::find();
            if(isset($get['pendaftaran_id'])){
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                    'query' => $query->asArray(),
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
    public function actionGetDetailLab()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new InfoPasienLabDetailView();
            $query = $model::find();
            if(isset($get['pendaftaran_id'])){
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            if(isset($get['pasien_id'])){
                $query->andWhere(['pasien_id' => $get['pasien_id']]);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                    'query' => $query,
                ]);
        } catch (\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        }
    }
    public function actionGetDetailRad()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new InfoPasienRadDetailView();
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            if(isset($get['pendaftaran_id'])){
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            if(isset($get['pasien_id'])){
                $query->andWhere(['pasien_id' => $get['pasien_id']]);
            }
            return new ActiveDataProvider([
                    'query' => $query,
                ]);
        } catch (\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        }
    }
    public function actionGetDetailBedah()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new InfoPasienOperasiDetailView();
            $query = $model::find();
            if(isset($get['pendaftaran_id'])){
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            if(isset($get['pasien_id'])){
                $query->andWhere(['pasien_id' => $get['pasien_id']]);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                    'query' => $query,
                ]);
        } catch (\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'pemeriksaanfisik' => []
            ];
        }
    }
    /**
    * @controller actionCetakResume
    * @attribute #no_pendaftaran# => Menamplkan nomor pendaftaran
    * @attribute #nama_pasien# => Menamplkan nama pasien
    * @attribute #no_rekam_medik# => Menamplkan nomor rekam medis
    * @attribute #alamat_pasien# => Menamplkan alamat
    * @attribute #ruangan_nama# => Menamplkan Nama ruangan dan lantai
    * @attribute #umur# => Menamplkan umur
    * @attribute #tgl_pendaftaran# => Menamplkan tanggal masuk / tanggal pendaftaran
    * @attribute #umur# => Menamplkan umur
    * @attribute #jenis_kelamin# => Menamplkan jenis kelamin
    * @attribute #tglpasienpulang# => Menamplkan tanggal keluar
    * @attribute #agama# => Menamplkan Agama
    * @attribute #datatable# => Untuk menampilkan data table
    * @attribute #rujukan# => Menampilkan rujukan
    * @attribute #diagnosa_awal# => Menampilkan diagnosa awal
    * @attribute #riwayat_penyakit_dahulu# => Menampilkan riwayat penyakit
    * @attribute #diagnosa_utama# => Menampilkan diagnosa utama
    * @attribute #diagnosa_sekunder# => Menampilkan diagnosa sekunder
    * @attribute #reaksi_alergi_obat# => Menampilkan reaksi alergi obat
    * @attribute #carapulang_pasien# => Menampilkan cara pulang
    * @attribute #laboratorium# => Menampilkan tabel list pemeriksaan laboratorium
    * @attribute #radiologi# => Menampilkan tabel list pemeriksaan radiologi
    * @attribute #anamnesa# => Menampilkan seluruh data anamnesa
    * @attribute #pemeriksaan_fisik# => Menampilkan seluruh data pemeriksaan fisik
    * @attribute #tindakan# => Menampilkan tabel list tindakan
    * @attribute #konsul_poli# => Menampilkan tabel list konsul poli
    * @attribute #reseptur# => Menampilkan tabel list reseptur
    */
    public function actionCetakResume()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $pendaftaran_id = $get['pendaftaran_id'];
            $pasien_id = $get['pasien_id'];

            // Pasien
            $patientRecord = InfoKunjunganRajal::find()
                ->select(['nama_pasien', 'no_pendaftaran', 'pasien_id', 'pendaftaran_id', 'no_rekam_medik', 'alamat_pasien', 'ruangan_nama', 'umur', 'tgl_pendaftaran', 'umur', 'jenis_kelamin', 'tglpasienpulang', 'agama', 'nama_pegawai as dokter_dpjp', 'carakeluar_nama', 'pegawai_id as dpjp_id'])
                ->andWhere(compact('pasien_id', 'pendaftaran_id'))
                ->asArray()
                ->one();
            // get signature path by dpjp_id
            if (!empty($patientRecord)) {
                $signaturePath = Pegawai::signatureEmployee($patientRecord['dpjp_id']);
            } else {
                $signaturePath = '';
            }

            $askepRecord = Anamnesa::find()
                ->select([
                    'rujukan',
                    'rujukan_rs',
                    'tujuan_rujukan',
                    'diagnosa_rujukan',
                    'keluhan_utama',
                    'reaksi_alergi',
                    'alergi_obat',
                    'alergi_makanan',
                    'alergi_lainnya',
                    'obat_dikonsumsi_nama',
                    'pasien_id',
                    'pendaftaran_id',
                    'skala_nyeri',
                    'skor_nyeri',
                    'riwayat_penyakit_nama',
                    'berat_badan',
                    'nadi',
                    'tinggi_badan',
                    'rr',
                    'td',
                    'suhu'
                ])
                ->orderBy(['created_date' => SORT_DESC])
                ->andWhere(compact('pasien_id', 'pendaftaran_id'))
                ->asArray()
                ->one();
            if( empty($askepRecord) ){
                $askepRecord = [
                    'rujukan' => '',
                    'rujukan_rs' => '',
                    'tujuan_rujukan' => '',
                    'diagnosa_rujukan' => '',
                    'keluhan_utama' => '',
                    'reaksi_alergi' => '',
                    'alergi_obat' => '',
                    'alergi_makanan' => '',
                    'alergi_lainnya' => '',
                    'obat_dikonsumsi_nama' => '',
                    'pasien_id' => '',
                    'pendaftaran_id' => '',
                    'skala_nyeri' => '',
                    'skor_nyeri' => '',
                    'riwayat_penyakit_nama' => '',
                    'berat_badan' => '',
                    'nadi' => '',
                    'tinggi_badan' => '',
                    'rr' => '',
                    'td' => '',
                    'suhu' => ''
                ];
            }
            
            // SOAP RJ
            $soapRecord = SoapRj::find()
                ->select([
                    'a_diag_utama',
                    'a_diag_penyerta',
                    'pasien_id',
                    'pendaftaran_id',
                ])
                ->andWhere(compact('pasien_id', 'pendaftaran_id'))
                ->orderBy(['tgl_soaprj' => SORT_DESC])
                ->asArray()
                ->one();

            // tindakan
            $actRecord = $this->getTerapi($pendaftaran_id);

            // laboratorium
            $labOrder = InfoPasienLabDetailView::find()
                ->select([
                    'pasien_id',
                    'pendaftaran_id',
                    'tgl_tindakan',
                    'jenis',
                    'daftartindakan_nama'
                ])
                ->where(compact('pendaftaran_id', 'pasien_id'))
                ->orderBy(['tgl_tindakan' => SORT_ASC])
                ->asArray()
                ->all();

            // radiologi
            $radOrder = InfoPasienRadDetailView::find()
                ->select([
                    'pasien_id',
                    'pendaftaran_id',
                    'tgl_tindakan',
                    'jenis',
                    'daftartindakan_nama'
                ])
                ->where(compact('pendaftaran_id', 'pasien_id'))
                ->orderBy(['tgl_tindakan' => SORT_ASC])
                ->asArray()
                ->all();

            // Konsul poli
            $consuleRecord = Infokonsulpoli::find()
                ->select([
                    'pendaftaran_id',
                    'dok_mengkonsul',
                    'catatan_dokter_konsul',
                    'jawaban_konsul',
                    'tgl_konsulpoli',
                    'tgl_selesaikonsul',
                ])
                ->andWhere(compact('pendaftaran_id'))
                ->asArray()
                ->all();

            // reseptur
            $medicineRecord = InfoResepturDetailView::find()
                ->select([
                    'racikan_nama',
                    'rke',
                    'obatalkes_nama',
                    'satuan_kecil',
                    'signa_nama',
                    'qty_reseptur',
                ])
                ->where(['pendaftaran_id' => $pendaftaran_id, 'pasien_id' => $pasien_id])
                ->orderBy(['racikan_nama' => SORT_ASC])
                ->asArray()
                ->all();

            $pegawailogin_id   = Yii::$app->jwt->user->pegawai_id;
            $pegawailogin      = Pegawai::findOne($pegawailogin_id);
            $pegawailogin_nama = $pegawailogin->nama_pegawai;
            $waktu_cetak       = date('d-m-Y H:i:s');

            $additionalDiagnose = '<ul>';
            if (!empty($soapRecord['a_diag_penyerta'])) {
                foreach (json_decode($soapRecord['a_diag_penyerta']) as $additional) {
                    $additionalDiagnose .= '<li>' . $additional->text . '</li>';
                }
            }
            $additionalDiagnose .= '</ul>';
            $print = new DocoPrint();
            $printAttributes = [];
            foreach ($patientRecord as $keyPatient => $value) {
                $printAttributes['#' . $keyPatient . '#'] = $value;
            }
            $todayDate = $this->helper->convertDate(date("Y-m-d H:i:s"));
            $nowDay = $this->helper->convertDate(date("Y-m-d H:i:s"), 'w');
            $print->attributes = array_merge($printAttributes, [
                '#rujukan#' => ($askepRecord['rujukan'] ? ($askepRecord['tujuan_rujukan'] == 'rs' ? $askepRecord['rujukan_rs'] : ucwords($askepRecord['tujuan_rujukan'])) : '-'),
                '#diagnosa_awal#' => $askepRecord['diagnosa_rujukan'],
                '#riwayat_penyakit_dahulu#' => $askepRecord['riwayat_penyakit_nama'],
                '#diagnosa_utama#' => empty($soapRecord['a_diag_utama']) ? '' : json_decode($soapRecord['a_diag_utama'])->text,
                '#diagnosa_sekunder#' => $additionalDiagnose,
                '#reaksi_alergi_obat#' => !empty($askepRecord['alergi_obat']) ? $askepRecord['reaksi_alergi'] : '-',
                '#carapulang_pasien#' => $patientRecord['carakeluar_nama'],
                '#laboratorium#' => $this->renderPartial('__laboratorium', [
                    'data' => $labOrder
                ]),
                '#radiologi#' => $this->renderPartial('__laboratorium', [
                    'data' => $radOrder
                ]),
                '#anamnesa#' => $this->renderPartial('__anamnesa', [
                    'data' => $askepRecord
                ]),
                '#pemeriksaan_fisik#' => $this->renderPartial('__pemeriksaan_fisik', [
                    'data' => $askepRecord
                ]),
                '#tindakan#' => $this->renderPartial('__tindakan', [
                    'data' => $actRecord
                ]),
                '#konsul_poli#' => $this->renderPartial('__konsul', [
                    'data' => $consuleRecord
                ]),
                '#reseptur#' => $this->renderPartial('__reseptur', [
                    'data' => $medicineRecord
                ]),
                '#nama_dokter#' => $patientRecord['dokter_dpjp'],
                '#hari_skr#' => $nowDay,
                '#tanggal_skr#' => $todayDate,
                '#ttd_dokter#' => $signaturePath,
                '#nama_pegawai#' => $pegawailogin_nama,
                '#timestamps#' => $waktu_cetak,
            ]);
            $print->Output();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        }
    }
    public function getTerapi($pendaftaran_id)
    {
        $databmhp = $datatindakan = [];
        try {
            // while print is only require tindakan so bmhp / obat / medicine will not include to data
            $getData = RiwayatTindakanView::find()
                ->select([
                    'daftartindakan_namapaket',
                    'tindakan_obat',
                    'qty',
                    'pendaftaran_id',
                    'tipe_pelayanan'
                ])
                ->andWhere(['pendaftaran_id'=>$pendaftaran_id])
                ->andWhere(['!=', 'tipe_pelayanan', 'BMHP'])
                ->orderBy(['tindakan_obat' => SORT_ASC])
                ->asArray()
                ->all();
            if(count($getData) < 1){
                return [
                    'bmhp' => $databmhp,
                    'tindakan' => $datatindakan
                ];
            }
            foreach ($getData as $key => $value) {
                if($value['tipe_pelayanan'] == 'BMHP'){
                    $databmhp[] = $value;
                }else{
                    $datatindakan[] = $value;
                }
            }
            return [
                'bmhp' => $databmhp,
                'tindakan' => $datatindakan
            ];
        } catch (\Exception $e) {
            return [
                'bmhp' => [],
                'tindakan' => []
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'bmhp' => [],
                'tindakan' => []
            ];
        }
    }

    public function actionGetResumeOdc()
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        if( is_null($pendaftaran_id) ){
            return $this->responseJson(422, 'pendaftaran_id Tidak Boleh Kosong!');
        }
        $resume = (new ResumeMedisRi)->getResumeData($pendaftaran_id);
        $caraKeluar = [
            'carakeluar' => ArrayHelper::map(CaraKeluar::getCaraKeluar(), 'carakeluar_id', 'carakeluar_nama')
        ];
        return array_merge($resume, $caraKeluar);
    }
    /**
     * Return data lab
     * 
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionLabResult($pendaftaran_id)
    {
        // $page = Yii::$app->request->get('page');
        if (\Yii::$app->request->get('load') == 'external') {
            return $this->getLabExternalResult($pendaftaran_id);
        } else {
            $paginationOption = Yii::$app->request->get('paginationOption', []);
            $record = HasilPemeriksaanLab::resultByRegistration($pendaftaran_id, $paginationOption);
            if ($record['status'] == 200) {
                return $record;
            } else {
                return $this->responseJson(isset($record['status']) ? $record['status'] : 500, isset($record['message']) ? $record['message'] : 'Terjadi kesalahan pada server');
            }
        }
    }

    public function getLabExternalResult($pendaftaran_id)
    {
        $orders = PasienMasukPenunjang::find()
        ->leftJoin('ruangan_m','ruangan_m.ruangan_id = pasienmasukpenunjang_t.ruangan_id')
        ->leftJoin('pasien_m','pasien_m.pasien_id = pasienmasukpenunjang_t.pasien_id')
        ->leftJoin('pendaftaran_t','pendaftaran_t.pasien_id = pasien_m.pasien_id')
        ->andWhere(['pendaftaran_t.pendaftaran_id'=>$pendaftaran_id])
        ->andWhere(['IS NOT','pasienmasukpenunjang_t.list_result',null])
        ->andWhere(['ruangan_m.instalasi_id'=>DocoConstansId::actionGetId('LAB')])
        ->asArray()
        ->all();
        
        $result = [];
        $listOrder = [];
        $listResultWynacom = [];
        $listResultRoche = [];
        if ($orders) {
            foreach($orders as $_order){
                $listOrder[] = $_order['no_masukpenunjang'];

            }
            if(count($listOrder)>0 ){
                $listResultWynacom = HasilPemeriksaanLabWynacom::find()
                ->select([
                    'hasilpemeriksaanlab_wynacom_t.*',
                    new Expression('NULL as daftartindakan_id'),
                    new Expression('NULL as daftartindakan_nama')
                ])->andWhere(['his_reg_no' => $listOrder])->asArray()->all();
                $listResultRoche = HasilPemeriksaanLabRoche::find()->andWhere(['order_no' => $listOrder])->asArray()->all();
            }

            //cari hasil lab wynacom berdasarkan no order
            foreach($orders as $order){
                $list_result = json_decode($order['list_result'], true) ?: [];
                if(count($list_result)<1){
                    if(count($listResultRoche)>0){
                            $tmp = [];
                            $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $order['tanggal_verifikasi']);
                            $tmp['tgl_hasilpemeriksaanlab'] = $order['tanggal_verifikasi'];
                            $tmp['results'] = [];
                            
                            foreach ($listResultRoche as $keyListResultRoche => $listRoche) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $listRoche['obv_id'],
                                    'daftartindakan_nama' => $listRoche['obv_name'],
                                    'nama_rujukan' => $listRoche['obv_name'],
                                    'hasil' => $listRoche['value']
                                ];
                            }
                            $result[] = $tmp;
                        
                    }else if(count($listResultWynacom)>0){
                        $tmp = [];
                        $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $order['tanggal_verifikasi']);
                        $tmp['tgl_hasilpemeriksaanlab'] = $order['tanggal_verifikasi'];
                        $tmp['results'] = [];
                        
                        foreach ($listResultWynacom as $keyListResultWynacom => $listWynacom) {
                            if ($order['no_masukpenunjang'] == $listWynacom['his_reg_no']) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $listWynacom['lis_test_id'],
                                    'daftartindakan_nama' => $listWynacom['test_name'],
                                    'nama_rujukan' => $listWynacom['test_name'],
                                    'hasil' => $listWynacom['result']
                                ];
                            }
                        }
                        $result[] = $tmp;

                    }

                }else{
                    if(count($listResultRoche)>0){
                            
                        foreach ($list_result as $index => $list) {
                            $tmp = [];
                            $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $list['dateTime']);
                            $tmp['tgl_hasilpemeriksaanlab'] = $list['dateTime'];
                            $tmp['results'] = [];
                            
                            foreach ($list['list'] as $item) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $item['obv_id'],
                                    'daftartindakan_nama' => $item['obv_name'],
                                    'nama_rujukan' => $item['obv_name'],
                                    'hasil' => $item['value']
                                ];
                            }
                            $result[] = $tmp;
                        }
                    }else if(count($listResultWynacom)>0){
                        foreach ($list_result as $index => $list) {
                            $tmp = [];
                            $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $list['dateTime']);
                            $tmp['tgl_hasilpemeriksaanlab'] = $list['dateTime'];
                            $tmp['results'] = [];
                            
                            foreach ($list['list'] as $item) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $item['daftartindakan_id'],
                                    'daftartindakan_nama' => $item['test_name'],
                                    'nama_rujukan' => $item['test_name'],
                                    'hasil' => $item['result']
                                ];
                            }
                            $result[] = $tmp;
                        }

                    }
                }
            }
        }
        return [
            'status' => 200,
            'data' => $result
        ];
    }

    /**
     * return result radiologi which already verified
     * 
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionRadResult($pendaftaran_id)
    {
        // first get order by pendaftaran id
        $orders = PemeriksaanPasienRadiologiView::find()
            ->select(['pasienmasukpenunjang_id'])
            ->andWhere(compact('pendaftaran_id'))
            ->asArray()
            ->all();
        $result = [];
        $orderIds = [];
        for ($i=0; $i < count($orders); $i++) {
            if (!in_array($orders[$i]['pasienmasukpenunjang_id'], $orderIds)) {
                $orderIds[] = $orders[$i]['pasienmasukpenunjang_id'];
            }
        }
        $query = HasilPemeriksaanRadView::find()
            ->select(['no_hasilrad', 'tgl_hasilrad', 'daftartindakan_nama', 'kesan as deskripsi', 'kesimpulan as kesan'])
            ->andWhere(['in', 'pasienmasukpenunjang_id', $orderIds])
            ->andWhere(['IS NOT', 'tgl_verifikasi', null]);
        $paginationOption = Yii::$app->request->get('paginationOption', []);
        $additionalResponse = [];
        if (isset($paginationOption['page']) && isset($paginationOption['limit'])) {
            $additionalResponse['total'] = $query->count();
            $query = $query->offset(($paginationOption['page'] - 1) * $paginationOption['limit'])->limit($paginationOption['limit']);
        }
        $result['data'] = $query->asArray()
            ->all();
        return array_merge($result, $additionalResponse);
    }

    public function actionSaveResumeOdc($pendaftaran_id)
    {
        $registrationRecord = Pendaftaran::find()
            ->select(['pendaftaran_id'])
            ->andWhere(compact('pendaftaran_id'))
            ->asArray()
            ->one();

        if (empty($registrationRecord)) {
            return $this->responseJson(400, 'Pendaftaran tidak ditemukan');
        }
        // $encodedFields = ResumeMedisRIT::$encodedFields;
        $payload = Yii::$app->request->post();

        // $asdasd = $asdas;
        $model = ResumeMedisRi::find()->select(['resumemedisri_id', 'pendaftaran_id'])->andWhere(['pendaftaran_id' => $pendaftaran_id])->one();
        if (empty($model)) {
            $model = new ResumeMedisRi;
        }
        if(isset($payload['order_laboratorium']) && is_string($payload['order_laboratorium'])) {
            $payload['order_laboratorium'] = [
                'text' => $payload['order_laboratorium']
            ];
        }
        if(isset($payload['order_radiologi']) && is_string($payload['order_radiologi'])) {
            $payload['order_radiologi'] = [
                'text' => $payload['order_radiologi']
            ];
        }

        $payload['tgl_masuk'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $payload['tgl_masuk'])));
        $payload['tgl_keluar'] = isset($payload['tgl_keluar']) && $payload['tgl_keluar'] != '' ?date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $payload['tgl_keluar']))) : '';
        $model->attributes = $payload;
        $model->pendaftaran_id = $pendaftaran_id;
        if (!$model->save()) {
            Yii::error([
                "Message" => "Proses simpan Resume medis gagal",
                "Bucket" => $model->errors
            ]);
            return $this->responseJson(400, 'Terjadi kesalahan pada proses penyimpanan data.');
        }
        return $this->responseJson(200, 'Berhasil menyimpan data resume medis', [
            'id' => $pendaftaran_id,
            'resumemedisri_id' => $model->resumemedisri_id,
            'pendaftaran_id' => $pendaftaran_id, // kebutuhan untuk service internal
        ]);

    }

        /**
     * @controller actionCetakResumeOdc
     * @attribute #no_pendaftaran# => Menamplkan nomor pendaftaran
     * @attribute #nama_pasien# => Menamplkan nama pasien
     * @attribute #no_rekam_medik# => Menamplkan nomor rekam medis
     * @attribute #alamat_pasien# => Menamplkan alamat
     * @attribute #ruangan_nama# => Menamplkan Nama ruangan dan lantai
     * @attribute #umur# => Menamplkan umur
     * @attribute #tgl_pendaftaran# => Menamplkan tanggal masuk / tanggal pendaftaran
     * @attribute #umur# => Menamplkan umur
     * @attribute #jenis_kelamin# => Menamplkan jenis kelamin
     * @attribute #tglpasienpulang# => Menamplkan tanggal keluar
     * @attribute #agama# => Menamplkan Agama
     * @attribute #datatable# => Untuk menampilkan data table
     * @attribute #rujukan# => Menampilkan rujukan
     * @attribute #diagnosa_awal# => Menampilkan diagnosa awal
     * @attribute #riwayat_penyakit_dahulu# => Menampilkan riwayat penyakit
     * @attribute #diagnosa_utama# => Menampilkan diagnosa utama
     * @attribute #diagnosa_sekunder# => Menampilkan diagnosa sekunder
     * @attribute #reaksi_alergi_obat# => Menampilkan reaksi alergi obat
     * @attribute #carapulang_pasien# => Menampilkan cara pulang
     * @attribute #laboratorium# => Menampilkan tabel list pemeriksaan laboratorium
     * @attribute #radiologi# => Menampilkan tabel list pemeriksaan radiologi
     * @attribute #anamnesa# => Menampilkan seluruh data anamnesa
     * @attribute #pemeriksaan_fisik# => Menampilkan seluruh data pemeriksaan fisik
     * @attribute #tindakan# => Menampilkan tabel list tindakan
     * @attribute #konsul_poli# => Menampilkan tabel list konsul poli
     * @attribute #reseptur# => Menampilkan tabel list reseptur
     * @attribute #no_identitas# => Menampilkan No Identitas
     * @attribute #tempat_lahir# => Menampilkan Tempat Lahir
     */


    public function actionCetakResumeOdc()
    {
        $registrationId = Yii::$app->request->get('pendaftaran_id');
        $resumeMedisRecord = ResumeMedisRi::resumeByRegistrationId($registrationId);
        $pfisik = $anamnesa = $riwayatPenyakit = $indikasiPasienDirawat = $dll = $tindakanRs = $konsul = $alergiObat = $obatRs = $obatHome = $kondisiPulang = $instruksi = '';
        // get signature path by dpjp_id
        if (!empty($resumeMedisRecord['patientRecord'])) {
            $signaturePath = Pegawai::signatureEmployee($resumeMedisRecord['patientRecord']['dokter_dpjp_id']);
            // DATE FORMATTING
            //$resumeMedisRecord['patientRecord']['tgl_pendaftaran'] = date('d F Y', strtotime($resumeMedisRecord['patientRecord']['tgl_pendaftaran']));
            //$resumeMedisRecord['patientRecord']['tglpasienpulang'] = !empty($resumeMedisRecord['patientRecord']['tglpasienpulang']) ? date('d F Y', strtotime($resumeMedisRecord['patientRecord']['tglpasienpulang'])) : '';
        } else {
            $signaturePath = '';
        }
        $askepRecord = $resumeMedisRecord['askepRecord'];
        $asmedRecord = $resumeMedisRecord['asmedRecord'];
        $dll = isset($resumeMedisRecord['lain_lainnya']) && !empty($resumeMedisRecord['lain_lainnya']) ? $resumeMedisRecord['lain_lainnya'] : '-';
        $suhu = isset($asmedRecord['suhu']) && !empty($asmedRecord['suhu']) ? $asmedRecord['suhu']. ' &deg;C' : '-';
        $nadi = isset($asmedRecord['nadi']) && !empty($asmedRecord['nadi']) ? $asmedRecord['nadi']. ' x/Menit' : '-';
        $td = isset($asmedRecord['td']) && !empty($asmedRecord['td']) ? $asmedRecord['td']. ' mmHg' : '-';
        $frekuensi_nafas = isset($asmedRecord['rr']) && !empty($asmedRecord['rr']) ? $asmedRecord['rr'] : '-';
        $tglMasuk = !empty($resumeMedisRecord['tgl_masuk']) ? $this->helper->convertDate($resumeMedisRecord['tgl_masuk'], 'd m Y') : '-';
        $tglKeluar = !empty($resumeMedisRecord['tgl_keluar']) ? $this->helper->convertDate($resumeMedisRecord['tgl_keluar'], 'd m Y') : '-';
        $keluhanUtama = isset($resumeMedisRecord['askepRecord']['keluhan_utama']) && !empty($resumeMedisRecord['askepRecord']['keluhan_utama']) ? $this->generateTextNewLine($resumeMedisRecord['askepRecord']['keluhan_utama'], "\n") : '-';
        $pfisik = isset($resumeMedisRecord['pemeriksaan_fisik']) && !empty($resumeMedisRecord['pemeriksaan_fisik']) ? $resumeMedisRecord['pemeriksaan_fisik'] : '-';
        $kondisiPulang = isset($resumeMedisRecord['patientRecord']['carakeluar']) && !empty($resumeMedisRecord['patientRecord']['carakeluar']) ?$this->generateTextNewLine($resumeMedisRecord['patientRecord']['carakeluar']) : '-';
        $instruksi = isset($resumeMedisRecord['instruction']) && !empty($resumeMedisRecord['instruction']) ? $this->generateTextNewLine($resumeMedisRecord['instruction'], "\n") : '-';
        $riwayatPenyakit = isset($resumeMedisRecord['patientDiseaseHistory']) && !empty($resumeMedisRecord['patientDiseaseHistory']) ? $resumeMedisRecord['patientDiseaseHistory'] : '-';
        $tindakanRs = isset($resumeMedisRecord['prosedur']) && !empty($resumeMedisRecord['prosedur']) ? $this->generateTextNewLine($resumeMedisRecord['prosedur'], "\n") : '';
        $obatHome = isset($resumeMedisRecord['obat_dibawa_pulang']) && !empty($resumeMedisRecord['obat_dibawa_pulang']) ? 
        (is_array($resumeMedisRecord['obat_dibawa_pulang']) ? $resumeMedisRecord['obat_dibawa_pulang'] : $this->generateTextNewLine($resumeMedisRecord['obat_dibawa_pulang'], "\n")) : '';
        if(empty($obatHome)) {
            $obatHome = $resumeMedisRecord['medicineOnReturn'];
        }
        $actRecord = isset($resumeMedisRecord['actRecord']) ? $resumeMedisRecord['actRecord'] : [];
        if(empty($tindakanRs)) {
            $tindakanRs = '';
            for ($i=0; $i < count($actRecord); $i++) { 
                if( $actRecord[$i]['tipe'] == 'TINDAKAN'){
                     $tindakanRs .= '<b>Tindakan</b> '.$actRecord[$i]['tindakan_paket_obat'].' Jumlah ' . $actRecord[$i]['qty']  ."<br>";
                }
                if( $actRecord[$i]['tipe'] == 'BMHP'){
                    $tindakanRs .= '&emsp;- <b>Obat</b> '.$actRecord[$i]['tindakan_paket_obat'].' Jumlah ' . $actRecord[$i]['qty']  ."<br>";
               }
            }
            $tindakanRs = !empty($tindakanRs) ? $tindakanRs : '-';
        }
        $tgl_pendaftaran = isset($resumeMedisRecord['patientRecord']['tgl_pendaftaran']) ? $this->helper->convertDate($resumeMedisRecord['patientRecord']['tgl_pendaftaran'], 'd m Y H:i:s') : "-";
        $tglpasienpulang = isset($resumeMedisRecord['patientRecord']['tglpasienpulang']) ? $this->helper->convertDate($resumeMedisRecord['patientRecord']['tglpasienpulang'], 'd m Y H:i:s') : "-";
        $diet = isset($resumeMedisRecord['catatan_diet']) && !empty($resumeMedisRecord['catatan_diet']) ? $this->generateTextNewLine($resumeMedisRecord['catatan_diet'], "\n") : '-';
        
        $print = new DocoPrint();
        $printAttributes = [];
        // $tanggal_lahir = isset($resumeMedisRecord['patientRecord']['tanggal_lahir']) ?  date('d M Y', strtotime($resumeMedisRecord['patientRecord']['tanggal_lahir'])) : null;
        
        foreach ($resumeMedisRecord['patientRecord'] as $keyPatient => $value) {
            if($keyPatient == 'tanggal_lahir') {
                $value = !empty($value) ? date('d M Y', strtotime($value)) : '';
            }
            $printAttributes['#' . $keyPatient . '#'] = $value;
        }
        $ruangan_nama = isset($resumeMedisRecord['patientRecord']['kamar']) ? $resumeMedisRecord['patientRecord']['kamar'] : null;
        $pegawailogin_id   = Yii::$app->jwt->user->pegawai_id;
        $pegawailogin      = Pegawai::findOne($pegawailogin_id);
        $pegawailogin_nama = $pegawailogin->nama_pegawai;
        $tanggal_cetak     = $this->helper->convertDate(date('d M Y'), 'd m Y');
        $waktu_cetak       = date('d-m-Y H:i:s');
        
        // =========== lab =============
        $lab = $resumeMedisRecord['labOrder']['text'];
        $lab = str_replace("\n", "", $lab);
        $lab = str_replace("<p>&nbsp;</p>", "", $lab);
        $lab = str_replace("<p>", "", $lab);
        $lab = str_replace("</p>", "<br>", $lab);
        $lab = str_replace("<p style=\"margin-left:24px\">", "", $lab);
        $labExplode = explode("<br>", $lab);
        preg_match_all ( '#<strong>(.+?)</strong>#', $lab, $parts );
        $labIdx = array_unique($parts[0]);
        $labDataArray = [];
        $statusInsert = false;
        if(!empty($labIdx)) {
            foreach ($labIdx as $k => $v) {
                foreach ($labExplode as $key => $value) {
                    if (in_array($value, $labIdx)) {
                        $statusInsert = ($v == $value) ? true : false;
                    } else {
                        if ($statusInsert && !empty($value)) {
                            $labDataArray[$v][] = $value;
                        }
                    }
                }
            }

            $lab = "";
            foreach ($labDataArray as $key => $value) {
                $lab .= '<span style="font-size:12px;font-family:Arial,Helvetica,sans-serif;">'.$key."</span><br>";
                foreach ($value as $k => $v) {
                    $lab .= '<span style="font-size:12px;font-family:Arial,Helvetica,sans-serif">'.$v."</span><br>";
                }
            }
            $lab = str_replace("<strong>", "", $lab);
            $lab = str_replace("</strong>", "", $lab);
        }
        $lab = !empty($lab) ? $lab : '-';
        
        // =========== rad =============
        $rad = $resumeMedisRecord['radOrder']['text'];
        $rad = str_replace("\n", "", $rad);
        $rad = str_replace(array("<p>","</p>"), array("<span><div style='height:0.5px; font-size:0.5px'>&nbsp;</div>","</span>"), $rad);
        $rad = str_replace("<strong>", "", $rad);
        $rad = str_replace("</strong>", "", $rad);
        $rad = !empty($rad) ? $rad : '-';
        
        // penyesuaian form resume medis baru
        $is_igd = $is_alergi = false;
        $kontak_darurat = $edukasi_rencana = $keadaan_umum = $kesadaran = $tindakanProsedur = $cara_keluar_nama = $alergi = 
        $keadaan_darurat = $instruksi_kontrol = $instruksi_tanggal = '-';
        $cara_keluar_nama = '';
        $additionalData = !empty($resumeMedisRecord['additional_data']) ? $resumeMedisRecord['additional_data'] : [];
        if(!empty($additionalData)) {
            $additionalData = json_decode($additionalData, true);
            $instruksi_kontrol = isset($additionalData['instruksi_kontrol']) && !empty($additionalData['instruksi_kontrol']) ? $additionalData['instruksi_kontrol'] : '-';
            $instruksi_tanggal = isset($additionalData['instruksi_tanggal']) && !empty($additionalData['instruksi_tanggal']) ? $this->helper->convertDate($additionalData['instruksi_tanggal'], 'd m Y') : '-';
            $is_igd = isset($additionalData['is_igd']) && !empty($additionalData['is_igd']) ? $additionalData['is_igd'] : '';
            if($is_igd == 1) {
                $is_igd = 'IGD, ';
            }
            $kontak_darurat = isset($additionalData['kontak_darurat']) && !empty($additionalData['kontak_darurat']) ? $additionalData['kontak_darurat'] : '-';
            $edukasi_rencana = isset($additionalData['edukasi_rencana']) && !empty($additionalData['edukasi_rencana']) ? $additionalData['edukasi_rencana'] : '-';
            $keadaan_darurat = $is_igd.' Telepon : '.$kontak_darurat;
            $cara_keluar = isset($additionalData['cara_keluar']) && !empty($additionalData['cara_keluar']) ? $additionalData['cara_keluar'] : null;
            $keadaan_umum = isset($additionalData['keadaan_umum']) && !empty($additionalData['keadaan_umum']) ? $additionalData['keadaan_umum'] : '-';
            $kesadaran = isset($additionalData['kesadaran']) && !empty($additionalData['kesadaran']) ? $additionalData['kesadaran'] : '-';
            $is_alergi = isset($additionalData['is_alergi']) && !empty($additionalData['is_alergi']) ? $additionalData['is_alergi'] : '-';
            $nama_alergi = isset($additionalData['nama_alergi']) && !empty($additionalData['nama_alergi']) ? $additionalData['nama_alergi'] : '-';
            $alergi = ($is_alergi == 1) ? 'Ya, ' . $nama_alergi : 'Tidak';
            $tindakanProsedur = isset($additionalData['instruksi_tindakanbmhp']) && !empty($additionalData['instruksi_tindakanbmhp']) ? $this->generateTextNewLine($additionalData['instruksi_tindakanbmhp'], "\n") : '-';
            $optionsCaraKeluar = isset($resumeMedisRecord['list_caraKeluar']) ? $resumeMedisRecord['list_caraKeluar'] : [];
            
            if(!empty($optionsCaraKeluar)) {
                foreach ($optionsCaraKeluar as $key => $value) {
                    if($cara_keluar == $value['carakeluar_id']) {
                        $cara_keluar_nama = $value['carakeluar_namalain'];
                    }
                }
            }
        }

        $cara_keluar_nama = empty($cara_keluar_nama) ? $kondisiPulang : $cara_keluar_nama;

        $diagnosa_masuk = isset($resumeMedisRecord['diagAwal']) && isset($resumeMedisRecord['diagAwal']['text']) ? $resumeMedisRecord['diagAwal']['text'] : '-';
        $diagnosa_utama = isset($resumeMedisRecord['cpptRecord']['text']) ? $resumeMedisRecord['cpptRecord']['text'] : (!empty($resumeMedisRecord['cpptRecord']['a_diag_utama']) ? (is_array($resumeMedisRecord['cpptRecord']['a_diag_utama']) && (isset($resumeMedisRecord['cpptRecord']['a_diag_utama']['text']) && !empty($resumeMedisRecord['cpptRecord']['a_diag_utama']['text'])) ? $resumeMedisRecord['cpptRecord']['a_diag_utama']['text'] : json_decode($resumeMedisRecord['cpptRecord']['a_diag_utama'], true)['text']) : '-');
        $diagnosa_sekunder = !empty(strip_tags($resumeMedisRecord['additionalDiagnose'])) ? $resumeMedisRecord['additionalDiagnose'] : '-';
        $lokasi_rs = Cache::getProfileRs();
        $no_identitas_pasien = isset($resumeMedisRecord['patientRecord']['no_identitas_pasien']) && !empty($resumeMedisRecord['patientRecord']['no_identitas_pasien']) ? $resumeMedisRecord['patientRecord']['no_identitas_pasien'] : '-';
        $additionalPasien = !empty($resumeMedisRecord['patientRecord']['additional_pasien']) ? $resumeMedisRecord['patientRecord']['additional_pasien'] : [];
        if(!empty($additionalPasien)){
            $additionalPasien = json_decode($additionalPasien, true);
            foreach ($additionalPasien as $value) {
                $no_identitas_pasien = isset($value['no_identitas_pasien']) && !empty($value['no_identitas_pasien']) ? $value['no_identitas_pasien'] : '-';
            }
        }
        $tempat_lahir = isset($resumeMedisRecord['patientRecord']['tempat_lahir']) && !empty($resumeMedisRecord['patientRecord']['tempat_lahir']) ? $resumeMedisRecord['patientRecord']['tempat_lahir'] : '-';
        // Yii::error($print);
        // $asdas = $asda;
        $print->attributes = array_merge($printAttributes, [
            '#ruangan_nama#' => $ruangan_nama,
            '#nama_dokter#' => $resumeMedisRecord['patientRecord']['dokter_dpjp'],
            '#hari_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s"), 'w'),
            '#tanggal_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s")),
            '#ttd_dokter#' => $signaturePath,
            '#nama_pegawai#' => $pegawailogin_nama,
            '#timestamps#' => $waktu_cetak,
            '#tgl_pendaftaran#' => $tgl_pendaftaran,
            '#tglpasienpulang#' => $tglpasienpulang,
            '#lokasi#' => ucwords(strtolower($lokasi_rs[0]['kota'])).', '.$tanggal_cetak,
            '#no_identitas#' => $no_identitas_pasien,
            '#tempat_lahir#' => $tempat_lahir,
            '#tgl_masuk#' => $tgl_pendaftaran,
            '#tgl_keluar#' => $tglpasienpulang,
            '#diagnosa_masuk#' => $diagnosa_masuk,
            '#diagnosa_utama#' => $diagnosa_utama,
            '#diagnosa_penyerta#' => $diagnosa_sekunder,
            '#keluhan_utama#' => $keluhanUtama,
            '#riwayat_penyakit_dahulu#' => $riwayatPenyakit,
            '#pemeriksaan_fisik#' => $pfisik,
            '#lab#' => $lab,
            '#rad#' => $rad,
            '#dll#' => $dll,
            '#tindakan#' => $tindakanRs,
            '#tindakan_prosedur#' => $tindakanProsedur,
            '#diet#' => $diet,
            '#alergi#' => $alergi,
            '#keadaan_umum#' => $keadaan_umum,
            '#kesadaran#' => $kesadaran,
            '#td#' => $td,
            '#suhu#' => $suhu,
            '#nadi#' => $nadi,
            '#nafas#' => $frekuensi_nafas,
            '#cara_keluar#' => $cara_keluar_nama,
            '#instruksi_kontrol#' => $instruksi,
            '#instruksi_tanggal#' => $instruksi_tanggal,
            '#keadaan_darurat#' => $keadaan_darurat,
            '#edukasi_rencana#' => $edukasi_rencana,
            '#obat_dibawa_pulang#' => Yii::$app->controller->renderPartial('obat-dibawa-pulang', [
                'obat_dibawa_pulang' => $obatHome
            ]),
            
        ]);
        return $print->Output();
    }

    private function generateTextNewLine($string, $delimiter = null)
    {
        $newString = '';
        $delimiter = is_null($delimiter) ? "\r" : $delimiter;
        $decodeText = explode($delimiter, $string);
        for ($i=0; $i < count($decodeText); $i++) { 
            $string = trim(preg_replace('/\s\s+/', ' ', $decodeText[$i]));
            $tmp[$i] = "<p>". $string ."</p>\n";
        }
        $newString = implode($tmp);
        return $newString;
    }
}