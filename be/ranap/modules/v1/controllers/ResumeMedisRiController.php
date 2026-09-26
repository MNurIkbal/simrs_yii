<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;

use app\modules\v1\models\AsesmenMedis; //
use app\modules\v1\models\Cppt; //diagnosa table
use app\modules\v1\models\RiwayatInstruksiTindakanView; //tindakan dan bmhp
use app\modules\v1\models\InfoResepturDetailView; //obat
use app\modules\v1\models\InfoOrderBedahDetailView; //bedah
use app\modules\v1\models\HasilPemeriksaanLabRoche; //bedah
use app\modules\v1\models\HasilPemeriksaanLabWynacom; 

use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\InfoPasienLabDetailView;
use app\modules\v1\models\InfoPasienRadDetailView;
use app\modules\v1\models\InfoPasienOperasiDetailView;
use app\modules\v1\models\InfoMorbiditasView;
use app\modules\v1\models\InfoKunjunganRi;

use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\HasilPemeriksaanLab;
use app\modules\v1\models\Infokonsulpoli;
use app\modules\v1\models\ResumeMedisRIT;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\models\Pegawai;
use Doco\models\radiologi\HasilPemeriksaanRadView;
use Doco\models\radiologi\PemeriksaanPasienRadiologiView;
use Doco\Services\Cache;
use Doco\models\PasienMasukPenunjang;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoPasienPulangRI;
use yii\db\Expression;

class ResumeMedisRiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ResumeMedisRi';

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

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasienadmisi_id = $request->get('pasienadmisi_id');
        try {
            return ResumeMedisRIT::resumeByRegistrationId($pendaftaran_id, $pasienadmisi_id);
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }
    }

    public function actionGetTerapi()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new RiwayatInstruksiTindakanView();
            $query = $model::find()
            ->select([
                'tindakan',
                'tindakan_paket_obat',
                'qty'
            ]);
            if(isset($get['type'])){
                if($get['type'] == 'BMHP'){
                    $query->andWhere(['tipe' => $get['type']]);
                }else{
                    $query->andWhere(['IN','tipe', ['TINDAKAN', 'PAKET']]);
                }
            }
            if(isset($get['pendaftaran_id']))
            {
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            // $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                    'query' => $query->asArray(),
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
            $query = $model::find()
            ->select([
                'racikan_nama',
                'rke',
                'obatalkes_nama',
                'satuan_kecil',
                'signa_nama',
                'qty_reseptur'
            ]);
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
            $model = new Cppt();
            $query = $model::find()
            ->select([
                'cppt_id',
                'a_diag_utama',
                'a_diag_penyerta'
            ]);
            if(isset($get['pendaftaran_id'])){
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            // $query = DocoRestActiveFilter::advancedFilter($model, $query);
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
            $model = new InfoOrderBedahDetailView();
            $query = $model::find()
            ->select([
                'infoorderanbedahdetail_v.tgl_periksa',
                'infoorderanbedahdetail_v.tipepaket_id',
                'infoorderanbedahdetail_v.daftartindakan_nama'
            ])
            ->leftJoin('infoorderanbedah_v','infoorderanbedahdetail_v.pasienkirimkeunitlain_id = infoorderanbedah_v.pasienkirimkeunitlain_id')
            ->andWhere(['infoorderanbedah_v.status_periksa' => '483']);
            if(isset($get['pendaftaran_id'])){
                $query->andWhere(['pendaftaran_id' => $get['pendaftaran_id']]);
            }
            if(isset($get['pasien_id'])){
                $query->andWhere(['pasien_id' => $get['pasien_id']]);
            }
            // $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return ([
                    'query' => $query->asArray()->all(),
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
        $registrationId = Yii::$app->request->get('pendaftaran_id');
        $resumeMedisRecord = ResumeMedisRIT::resumeByRegistrationId($registrationId);

        // get signature path by dpjp_id
        if (!empty($resumeMedisRecord['patientRecord'])) {
            $signaturePath = Pegawai::signatureEmployee($resumeMedisRecord['patientRecord']['dokter_dpjp_id']);
        } else {
            $signaturePath = '';
        }

        $print = new DocoPrint();
        $printAttributes = [];
        foreach ($resumeMedisRecord['patientRecord'] as $keyPatient => $value) {
            $printAttributes['#' . $keyPatient . '#'] = $value;
        }

        $pegawailogin_id   = Yii::$app->jwt->user->pegawai_id;
        $pegawailogin      = Pegawai::findOne($pegawailogin_id);
        $pegawailogin_nama = $pegawailogin->nama_pegawai;
        $waktu_cetak       = date('d-m-Y H:i:s');

        $print->attributes = array_merge($printAttributes, [
            // '#rujukan#' => ($askepRecord['rujukan'] ? ($askepRecord['tujuan_rujukan'] == 'rs' ? $askepRecord['rujukan_rs'] : ucwords($askepRecord['tujuan_rujukan'])) : '-'),
            '#rujukan#' => '-',
            '#dokter_rawat_bersama#' => $resumeMedisRecord['doctorInpatientConsule'],
            '#diagnosa_awal#' => isset($resumeMedisRecord['diagAwal']) && isset($resumeMedisRecord['diagAwal']['text']) ? $resumeMedisRecord['diagAwal']['text'] : '-',
            '#riwayat_penyakit_dahulu#' => $resumeMedisRecord['patientDiseaseHistory'],
            '#diagnosa_utama#' => isset($resumeMedisRecord['cpptRecord']['text']) ? $resumeMedisRecord['cpptRecord']['text'] : (!empty($resumeMedisRecord['cpptRecord']['a_diag_utama']) ? json_decode($resumeMedisRecord['cpptRecord']['a_diag_utama'])->text : '-'),
            '#diagnosa_sekunder#' => $resumeMedisRecord['additionalDiagnose'],
            '#reaksi_alergi_obat#' => isset($resumeMedisRecord['askepRecord']['reaksi_alergi_obat']) && !empty($resumeMedisRecord['askepRecord']['reaksi_alergi_obat']) ? $resumeMedisRecord['askepRecord']['reaksi_alergi_obat'] : '-',
            '#carapulang_pasien#' => isset($resumeMedisRecord['patientRecord']['carakeluar_nama']) ? $resumeMedisRecord['patientRecord']['carakeluar_nama'] : '-',
            '#indikasi_pasien_dirawat#' => isset($resumeMedisRecord['indication']) ? $resumeMedisRecord['indication'] : '-',
            '#laboratorium#' => isset($resumeMedisRecord['labOrder']['text']) ? $resumeMedisRecord['labOrder']['text'] : $this->renderPartial('__laboratorium', [
                'data' => $resumeMedisRecord['labOrder']
            ]),
            '#radiologi#' => isset($resumeMedisRecord['radOrder']['text']) ? $resumeMedisRecord['radOrder']['text'] : $this->renderPartial('__laboratorium', [
                'data' => $resumeMedisRecord['radOrder']
            ]),
            '#anamnesa#' => $this->renderPartial('__anamnesa', [
                'data' => $resumeMedisRecord['askepRecord']
            ]),
            '#pemeriksaan_fisik#' => $this->renderPartial('__pemeriksaan_fisik', [
                'data' => $resumeMedisRecord['askepRecord']
            ]),
            '#tindakan#' => $this->renderPartial('__tindakan', [
                'data' => $resumeMedisRecord['actRecord']
            ]),
            '#konsul_poli#' => $this->renderPartial('__konsul', [
                'data' => $resumeMedisRecord['consuleRecord']
            ]),
            '#reseptur#' => $this->renderPartial('__reseptur', [
                'data' => $resumeMedisRecord['medicineRecord']
            ]),
            '#obat_dibawa_pulang#' => $this->renderPartial('__reseptur', [
                'data' => isset($resumeMedisRecord['medicineOnReturn']) ? $resumeMedisRecord['medicineOnReturn'] : []
            ]),
            '#intruksi#' => isset($resumeMedisRecord['instruction']) ? $resumeMedisRecord['instruction'] : '-',
            '#nama_dokter#' => $resumeMedisRecord['patientRecord']['dokter_dpjp'],
            '#hari_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s"), 'w'),
            '#tanggal_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s")),
            '#ttd_dokter#' => $signaturePath,
            '#nama_pegawai#' => $pegawailogin_nama,
            '#timestamps#' => $waktu_cetak,
        ]);
        $print->Output();
        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     $this->logError($e);
        //     return ['message' => $e->getMessage()];
        // }
    }

    /**
     * This function will save data resume medis
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveResumeMedis($pendaftaran_id)
    {
        $registrationRecord = InfoKunjunganRi::find()
            ->select(['pendaftaran_id'])
            ->andWhere(compact('pendaftaran_id'))
            ->asArray()
            ->one();

        if (empty($registrationRecord)) {
            return $this->responseJson(400, 'Pendaftaran tidak ditemukan');
        }
        
        // $encodedFields = ResumeMedisRIT::$encodedFields;
        $payload = Yii::$app->request->post();
        $model = ResumeMedisRIT::find()->select(['resumemedisri_id', 'pendaftaran_id'])->andWhere(['pendaftaran_id' => $pendaftaran_id])->one();
        
        if (empty($model)) {
            $model = new ResumeMedisRIT;
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
        $model->attributes = $payload;
        $model->indikasi_pasien_dirawat = ArrayHelper::getValue($payload, 'indikasi_pasien_dirawat', '');
        $model->prosedur = ArrayHelper::getValue($payload, 'prosedur', '');
        $model->pendaftaran_id = $pendaftaran_id;

        if (!$model->save()) {
            Yii::error([
                "Message" => "Proses simpan Resume medis gagal",
                "Bucket" => $model->errors
            ]);
            return $this->responseJson(400, 'Terjadi kesalahan pada proses penyimpanan data.');
        }
        return $this->responseJson(200, 'Berhasil menyimpan data resume medis');
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
        $registrationRecord = InfoKunjunganRi::find()->select(['pendaftaran_id', 'prev_pendaftaran_id'])->andWhere(compact('pendaftaran_id'))->asArray()->one();
        if (empty($registrationRecord)) {
            return [
                'message' => 'Data pendaftaran tidak ditemukan',
                'status' => 400
            ];
        }
        $lookPrevRegId = ArrayHelper::getValue($registrationRecord, 'prev_pendaftaran_id');
        $listRegId[] = $pendaftaran_id;
        if(!empty($lookPrevRegId)) {
            array_push($listRegId, $lookPrevRegId);
        }

        // first get order by pendaftaran id
        $orders = PemeriksaanPasienRadiologiView::find()
            ->select(['pasienmasukpenunjang_id'])
            // ->andWhere(compact('pendaftaran_id'))
            ->andWhere(['IN', 'pendaftaran_id', $listRegId])
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
     * @controller actionCetakResumeRi
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
     * @attribute #lain-lain# => Menampilkan lain-lain
     * @attribute #kamar# => Menampilkan kamar
     * @attribute #perujuk# => Menampilkan perujuk
     * @attribute #no_identitas# => Menampilkan No Identitas
     * @attribute #tempat_lahir# => Menampilkan Tempat Lahir
     */
    public function actionCetakResumeRi()
    {
        $registrationId = Yii::$app->request->get('pendaftaran_id');
        $resumeMedisRecord = ResumeMedisRIT::resumeByRegistrationId($registrationId);
        $pfisik = $anamnesa = $riwayatPenyakit = $indikasiPasienDirawat = $dll = $tindakanRs = $konsul = $alergiObat = $obatRs = $obatHome = $kondisiPulang = $instruksi = '';

        // get signature path by dpjp_id
        if (!empty($resumeMedisRecord['patientRecord'])) {
            $signaturePath = Pegawai::signatureEmployee($resumeMedisRecord['patientRecord']['dokter_dpjp_id']);
            // DATE FORMATTING
            $resumeMedisRecord['patientRecord']['tgl_pendaftaran'] = date('d F Y', strtotime($resumeMedisRecord['patientRecord']['tgl_pendaftaran']));
            $resumeMedisRecord['patientRecord']['tglpasienpulang'] = !empty($resumeMedisRecord['patientRecord']['tglpasienpulang']) ? date('d F Y', strtotime($resumeMedisRecord['patientRecord']['tglpasienpulang'])) : '';
        } else {
            $signaturePath = '';
        }
        
        $dll = isset($resumeMedisRecord['lain_lainnya']) && !empty($resumeMedisRecord['lain_lainnya']) ? $resumeMedisRecord['lain_lainnya'] : '-';
        $suhu = isset($resumeMedisRecord['suhu']) && !empty($resumeMedisRecord['suhu']) ? $resumeMedisRecord['suhu']. '&deg;C' : '-';
        $nadi = isset($resumeMedisRecord['nadi']) && !empty($resumeMedisRecord['nadi']) ? $resumeMedisRecord['nadi']. ' x/Menit' : '-';
        $td = isset($resumeMedisRecord['td']) && !empty($resumeMedisRecord['td']) ? $resumeMedisRecord['td']. ' mmHg' : '-';
        $tglMasuk = !empty($resumeMedisRecord['tgl_masuk']) ? $this->helper->convertDate($resumeMedisRecord['tgl_masuk'], 'd m Y') : '-';
        $tglKeluar = !empty($resumeMedisRecord['tgl_keluar']) ? $this->helper->convertDate($resumeMedisRecord['tgl_keluar'], 'd m Y') : '-';
        $keluhanUtama = isset($resumeMedisRecord['askepRecord']['keluhan_utama']) && !empty($resumeMedisRecord['askepRecord']['keluhan_utama']) ? $this->generateTextNewLine($resumeMedisRecord['askepRecord']['keluhan_utama'], "\n") : '-';
        $pfisik = isset($resumeMedisRecord['pemeriksaan_fisik']) && !empty($resumeMedisRecord['pemeriksaan_fisik']) ? $resumeMedisRecord['pemeriksaan_fisik'] : '-';
        
        // ?? comment sementara moga bisa sedikit mempercepat proses cetak 
        // $anamnesa = isset($resumeMedisRecord['anamnesa']) && !empty($resumeMedisRecord['anamnesa']) ? $this->generateTextNewLine($resumeMedisRecord['anamnesa']) : '';
        // $indikasiPasienDirawat = isset($resumeMedisRecord['indication']) && !empty($resumeMedisRecord['indication']) ? $this->generateTextNewLine($resumeMedisRecord['indication'], "\n") : '';
        // $konsul = isset($resumeMedisRecord['konsultasi']) && !empty($resumeMedisRecord['konsultasi']) ? $this->generateTextNewLine($resumeMedisRecord['konsultasi'], "\n") : '';
        // $alergiObat = isset($resumeMedisRecord['askepRecord']['reaksi_alergi_obat']) && !empty($resumeMedisRecord['askepRecord']['reaksi_alergi_obat']) ? $this->generateTextNewLine($resumeMedisRecord['askepRecord']['reaksi_alergi_obat']) : '';
        // $obatRs = isset($resumeMedisRecord['obat_rs']) && !empty($resumeMedisRecord['obat_rs']) ? $this->generateTextNewLine($resumeMedisRecord['obat_rs'], "\n") : '';
        $kondisiPulang = isset($resumeMedisRecord['patientRecord']['carakeluar_nama']) && !empty($resumeMedisRecord['patientRecord']['carakeluar_nama']) ?$this->generateTextNewLine($resumeMedisRecord['patientRecord']['carakeluar_nama']) : '-';
        // $instruksi = isset($resumeMedisRecord['instruction']) && !empty($resumeMedisRecord['instruction']) ? $this->generateTextNewLine($resumeMedisRecord['instruction'], "\n") : '';
        
        $riwayatPenyakit = isset($resumeMedisRecord['patientDiseaseHistory']) && !empty($resumeMedisRecord['patientDiseaseHistory']) ? $resumeMedisRecord['patientDiseaseHistory'] : '-';
        $tindakanRs = isset($resumeMedisRecord['prosedur']) && !empty($resumeMedisRecord['prosedur']) ? $this->generateTextNewLine($resumeMedisRecord['prosedur'], "\n") : '';
         if(empty($tindakanRs)) {
            $tindakanRs = '';
            $actRecord = isset($resumeMedisRecord['actRecord']) ? $resumeMedisRecord['actRecord'] : [];
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
        $obatHome = isset($resumeMedisRecord['obat_dibawa_pulang']) && !empty($resumeMedisRecord['obat_dibawa_pulang']) ? 
            (is_array($resumeMedisRecord['obat_dibawa_pulang']) ? $resumeMedisRecord['obat_dibawa_pulang'] : $this->generateTextNewLine($resumeMedisRecord['obat_dibawa_pulang'], "\n")) : '';
        if(empty($obatHome)) {
            $obatHome = $resumeMedisRecord['medicineRecord'];
        }
        $tgl_pendaftaran = isset($resumeMedisRecord['patientRecord']['tgl_pendaftaran']) ? $this->helper->convertDate($resumeMedisRecord['patientRecord']['tgl_pendaftaran'], 'd m Y') : "-";
        $tglpasienpulang = isset($resumeMedisRecord['patientRecord']['tglpasienpulang']) ? $this->helper->convertDate($resumeMedisRecord['patientRecord']['tglpasienpulang'], 'd m Y') : "-";
        $diet = isset($resumeMedisRecord['catatan_diet']) && !empty($resumeMedisRecord['catatan_diet']) ? $this->generateTextNewLine($resumeMedisRecord['catatan_diet'], "\n") : '-';
        
        $print = new DocoPrint('resume-medis-ri-bg');
        $printAttributes = [];
        foreach ($resumeMedisRecord['patientRecord'] as $keyPatient => $value) {
            if($keyPatient == 'tanggal_lahir') {
                $value = !empty($value) ? date('d M Y', strtotime($value)) : '';
            }
            $printAttributes['#' . $keyPatient . '#'] = $value;
        }

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
        
        // ?? penyesuaian form resume medis baru
        $is_igd = $is_alergi = false;
        $kontak_darurat = $edukasi_rencana = $keadaan_umum = $kesadaran = $frekuensi_nafas = $tindakanProsedur = $cara_keluar_nama = $alergi = 
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
            $frekuensi_nafas = isset($additionalData['frekuensi_nafas']) && !empty($additionalData['frekuensi_nafas']) ? $additionalData['frekuensi_nafas'] : '-';
            $tindakanProsedur = isset($additionalData['instruksi_tindakanbmhp']) && !empty($additionalData['instruksi_tindakanbmhp']) ? $this->generateTextNewLine($additionalData['instruksi_tindakanbmhp'], "\n") : '-';
            $optionsCaraKeluar = isset($resumeMedisRecord['caraKeluar']) ? $resumeMedisRecord['caraKeluar'] : [];
            
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
        $print->attributes = array_merge($printAttributes, [
            // '#rujukan#' => isset($resumeMedisRecord['patientRecord']['perujuk']) && !empty($resumeMedisRecord['patientRecord']['perujuk']) ? $resumeMedisRecord['patientRecord']['perujuk'] : '-',
            // '#dokter_rawat_bersama#' => $resumeMedisRecord['doctorInpatientConsule'],
            // '#reaksi_alergi_obat#' => $alergiObat,
            // '#carapulang_pasien#' => $kondisiPulang,
            // '#indikasi_pasien_dirawat#' => $indikasiPasienDirawat,
            // '#anamnesa#' => $anamnesa,
            // '#tindakan#' => $tindakanRs,
            // '#konsul_poli#' => $konsul,
            // '#reseptur#' => $obatRs,
            // '#intruksi#' => $instruksi,
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
            '#tgl_masuk#' => $tglMasuk,
            '#tgl_keluar#' => $tglKeluar,
            '#diagnosa_masuk#' => $diagnosa_masuk,
            '#diagnosa_utama#' => $this->generateTextNewLine($diagnosa_utama, "\n"),
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
            '#instruksi_kontrol#' => $instruksi_kontrol,
            '#instruksi_tanggal#' => $instruksi_tanggal,
            '#keadaan_darurat#' => $keadaan_darurat,
            '#edukasi_rencana#' => $edukasi_rencana,
            '#obat_dibawa_pulang#' => is_array($obatHome) ? Yii::$app->controller->renderPartial('resume-medis-ri', [
                'obat_dibawa_pulang' => $obatHome
            ]) : $obatHome,
            '#konsultasi#' => ArrayHelper::getValue($resumeMedisRecord, 'konsultasi'),
            '#alamat#' => ArrayHelper::getValue($resumeMedisRecord, 'patientRecord.alamat_pasien'),
            '#umur#' => ArrayHelper::getValue($resumeMedisRecord, 'patientRecord.umur'),
    
        ]);
        return $print->OutputHtml();
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
