<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-08-15 08:01:21
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 11:34:22
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;


use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoPasienPulangRI;
use app\modules\v1\models\PasienBatalPulang;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\Pasien;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\models\KonfirmasiUnit;
use Doco\models\bpjs\BpjsAplicare;

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
        return InfoPasienPulangRI::find();   
    }

    public function actionIndex()
    {
       try {
            $request = Yii::$app->request;
            $model = new InfoPasienPulangRI;
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
                if (isset($advancedFilters['tgl_admisi'])) {
                    if($advancedFilters['tgl_admisi'] != ' - '){
                        $explode = explode(' - ', $advancedFilters['tgl_admisi']);
                        $tgl_awalAdmisi = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_akhirAdmisi = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_admisi']);
                        $query->andWhere(['between', 'tgl_admisi', $tgl_awalAdmisi, $tgl_akhirAdmisi]);
                    }
                }

                if (isset($advancedFilters['tglpasienpulang'])) {
                    $explode = explode(' - ', $advancedFilters['tglpasienpulang']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($advancedFilters['tglpasienpulang']);
                    $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
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

            if ( empty( $request->get('advanced-filter', [])['tgl_admisi'] ) && empty( $request->get('advanced-filter', [])['tglpasienpulang'] )  ){ 
                $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            }
            
            // $query->AndWhere([
            //     'ruanganakhir_id' => $request->get('ruangan_id', null),
            // ]);            
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
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $pasienPulangId = $post['pasienpulang_id'];

            $modelBatalPulang = new PasienBatalPulang;
            $modelBatalPulang->attributes =  $post;
            if (!$modelBatalPulang->validate()) {
                return ['data' => $modelBatalPulang->errors,'status' => 422];
            } else {
                $getPasienPulang = PasienPulang::findOne($pasienPulangId);
                if(!$getPasienPulang){
                    $errors = DocoHelpers::parseError($getPasienPulang->errors,'PasienPulang');
                    return ['data' => $errors,'status' => 422];
                }else{
                    $getPasienAdmisi = PasienAdmisi::findOne($getPasienPulang->pasienadmisi_id);
                    $dataPendaftaran = Pendaftaran::findOne($getPasienAdmisi->pendaftaran_id);
                    $getData = $this->getData($getPasienAdmisi->pendaftaran_id);
                    $result = $getData->asArray()->one();
                    $ruangan_id = !empty($post['ruangan_id']) || $post['ruangan_id'] != "" ? $post['ruangan_id'] : $result['ruanganakhir_id'];
                    $kamarruangan_id = !empty($post['kamarruangan_id']) || $post['kamarruangan_id'] != ""  ? $post['kamarruangan_id'] : $result['kamarruangan_id'];
                    $kamartempattidur_id = !empty($post['kamartempattidur_id']) || $post['kamartempattidur_id'] != ""  ? $post['kamartempattidur_id'] : $result['kamartempattidur_id'];
                    if (!$getPasienAdmisi) {
                        $errors = DocoHelpers::parseError($getPasienAdmisi->errors,'PasienAdmisi');
                        return ['data' => $errors,'status' => 422];
                    }else{
                        if(!empty($kamarruangan_id) && !empty($kamartempattidur_id)){
                            $dataPasien = Pasien::find()->select(['pasien_id','jeniskelamin'])->where(['pasien_id' => $getPasienAdmisi->pendaftaran_id])->asArray()->one();
                            $getKamarTempatTidur = KamarTempatTidur::findOne($kamartempattidur_id);
                            $getKamarTempatTidur->status_isi = true;
                            $getKamarTempatTidur->kettempattidur_id = $dataPasien['jeniskelamin'] == DocoConstants::VAR_LK ? DocoConstants::KET_TT_ISI_L : DocoConstants::KET_TT_ISI_P;
                            $getKamarTempatTidur->save();
                            
                            $getPasienAdmisi->ruangan_id = $ruangan_id;
                            $getPasienAdmisi->kamarruangan_id = $kamarruangan_id;
                            $getPasienAdmisi->kamartempattidur_id = $kamartempattidur_id;
                        }
                        $getPasienAdmisi->pasienpulang_id = null;
                        $getPasienAdmisi->tgl_pulang = null;
                        $getPasienAdmisi->status_ranap = DocoConstants::STATUS_RANAP_PERIKSA;
                        if ($dataPendaftaran) {
                            if($dataPendaftaran->instalasi_id != DocoConstants::INST_ID_RD){

                                $dataPendaftaran->ruangan_id = $ruangan_id;
                            }
                            $dataPendaftaran->is_stopakomodasi = false;
                            $dataPendaftaran->alasan_batalstop = isset($post['alasan_pembatalan']) ? $post['alasan_pembatalan'] : '';
                            $dataPendaftaran->tgl_stopakomodasi = null;
                            $dataPendaftaran->save();
                        }
                        if (!$getPasienAdmisi->save(false)) {
                            $errors = DocoHelpers::parseError($getPasienAdmisi->errors,'PasienAdmisi');
                            return ['data' => $errors,'status' => 422];
                        }else{
                            if(!$modelBatalPulang->save(false)){
                                $errors = DocoHelpers::parseError($modelBatalPulang->errors,'BatalPulang');
                                return ['data' => $errors,'status' => 422];
                            }else{
                                $getPasienPulang->pasienbatalpulang_id = $modelBatalPulang->pasienbatalpulang_id;
                                if(!$getPasienPulang->save(false)){
                                    $errors = DocoHelpers::parseError($getPasienPulang->errors,'PasienPulang');
                                    return ['data' => $errors,'status' => 422];
                                }else{
                                    $dataMasukKamar = MasukKamar::find()->where(['pasienadmisi_id' => $getPasienAdmisi->pasienadmisi_id])->orderby(['masukkamar_id' => SORT_DESC])->one();
                                    $addMasukKamar = new MasukKamar;
                                    $addMasukKamar->ruangan_id = $getPasienAdmisi->ruangan_id;
                                    $addMasukKamar->carabayar_id = $getPasienAdmisi->carabayar_id;
                                    $addMasukKamar->pasienadmisi_id = $getPasienAdmisi->pasienadmisi_id;
                                    $addMasukKamar->penjamin_id = $getPasienAdmisi->penjamin_id;
                                    $addMasukKamar->pegawai_id = Yii::$app->jwt->user->pegawai_id;
                                    $addMasukKamar->kelaspelayanan_id = $getPasienAdmisi->kelaspelayanan_id;
                                    $addMasukKamar->kamartempattidur_id = $kamartempattidur_id;
                                    $addMasukKamar->kamarruangan_id = $kamarruangan_id;
                                    $addMasukKamar->tgl_masukkamar = $dataMasukKamar->tgl_keluarkamar;
                                    $addMasukKamar->jam_masukkamar = !empty($dataMasukKamar->jam_keluarkamar) ? $dataMasukKamar->jam_keluarkamar : date('H:i:s',strtotime($dataMasukKamar->tgl_keluarkamar));
                                    if($addMasukKamar->save(false)){
                                        Yii::error([
                                            'ruangan' => $ruangan_id,
                                            'admisi' => $getPasienAdmisi->pasienadmisi_id
                                        ]);
                                    }
                                    (new BpjsAplicare())->createOrUpdateAplicare($kamarruangan_id, DocoConstants::TYPE_UPDATE_APLICARE);
                                    Yii::$app->cache->delete('gizi-pendaftaran-id-'. DocoHelpers::encrypt($getPasienAdmisi->pendaftaran_id));
                                    return [
                                        'message' => 'Data Berhasil di simpan',
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            Yii::warning($e->getMessage());
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            Yii::warning($e->getMessage());
            Yii::warning($e->getFile());
            Yii::warning($e->getLine());

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
            $title = 'Informasi Pasien Pulang Rawat Inap Rumah Sakit';
            $get = $request->get();

            $model = new InfoPasienPulangRI;
            $query = $this->model();
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['tgl_admisi'])) {
                    if($advancedFilters['tgl_admisi'] != ' - '){
                        $explode = explode(' - ', $advancedFilters['tgl_admisi']);
                        $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_admisi']);
                        $query->andWhere(['between', 'tgl_admisi', $tgl_awal, $tgl_akhir]);
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
                if (isset($advancedFilters['kelaspelayanan_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(kelaspelayanan_nama)', strtolower($advancedFilters['kelaspelayanan_nama']) ]);
                }
                if (isset($advancedFilters['ruangan_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(ruangan_nama)', strtolower($advancedFilters['ruangan_nama']) ]);
                }
                if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($advancedFilters['jeniskasuspenyakit_nama']) ]);
                }
                if (isset($advancedFilters['dokter']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(dokter)', strtolower($advancedFilters['dokter']) ]);
                }
                if (isset($advancedFilters['carabayar_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($advancedFilters['carabayar_nama']) ]);
                }
                if (isset($advancedFilters['penjamin_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_nama)', strtolower($advancedFilters['penjamin_nama']) ]);
                }
                if (isset($advancedFilters['carakeluar_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(carakeluar_nama)', strtolower($advancedFilters['carakeluar_nama']) ]);
                }
                if (isset($advancedFilters['kondisikeluar_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(kondisikeluar_nama)', strtolower($advancedFilters['kondisikeluar_nama']) ]);
                }
            }
            $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            $query->orderby(['tglpasienpulang' => SORT_DESC]);        
            
            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $lamaRawat = isset($value['lama_rawat']) ? $value['lama_rawat'] : '';
                if(empty($lamaRawat)) {
                    $lamaRawat = $this->getLamaRawat($value['tgl_admisi'], $value['tglpasienpulang']);
                }
                $value['lama_rawat'] = $lamaRawat .' Hari';
                $value['no_telpon'] = isset($value['no_telepon_pasien']) ? $value['no_telepon_pasien'] : ' - '; 
                $value['tgl_admisi'] = date('d M Y H:i:s', strtotime($value['tgl_admisi']));
                $value['tglpasienpulang'] = !empty($value['tglpasienpulang']) ? date('d M Y H:i:s', strtotime($value['tglpasienpulang'])) : null;
                $value['tgl_admisi'] = 'Masuk : '.$value['tgl_admisi'].'<br>Pulang : '.$value['tglpasienpulang'];
                $value['no_rekam_medik'] = strtoupper($value['nama_pasien']).' ('.substr($value['jns_kelamin'], 0, 1).')<br>No. Registrasi : '.$value['no_pendaftaran'].'<br>No. RM : '.$value['no_rekam_medik'];
                $tempKelas = $value['kelaspelayanan_nama'];
                $value['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : -';
                $value['ruangan_nama'] = 'Kasus Penyakit : '.$value['jeniskasuspenyakit_nama'].'<br>Ruangan : '.$value['ruangan_nama'].'<br>Lama Rawat : '.$value['lama_rawat'];


                if (!array_key_exists('pk_kelas_ditagihkan_nama', $value)) {
                    $value['pk_kelas_ditagihkan_nama'] = '-';
                }

                if (!array_key_exists('kelas_ditagihkan_nama', $value)) {
                    $value['kelas_ditagihkan_nama'] = '-';
                }

                if ($value['pindahkamar_id']) {
                    if ($value['pk_is_stoptitipan'] == false) {
                        if ($value['pk_is_pasientitipan']) {
                            $value['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : '.$value['pk_kelas_ditagihkan_nama'];
                        }
                    }
                } else {
                    if ($value['is_stoptitipan'] == false) {
                        if ($value['is_pasientitipan']) {
                            $value['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : '.$value['kelas_ditagihkan_nama'];
                        }
                    }
                }

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
        try {
            $request = Yii::$app->request;
            $title = 'Informasi Pasien Pulang Rumah Sakit';
            $result = [];
            $get = $request->get();
            $model = new InfoPasienPulangRI;
            $query = $this->model();
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            $advancedFilters = $request->get('advanced-filter', []);

            if (isset($advancedFilters)) {
                if (isset($advancedFilters['tgl_admisi'])) {
                    if($advancedFilters['tgl_admisi'] != ' - '){
                        $explode = explode(' - ', $advancedFilters['tgl_admisi']);
                        $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_admisi']);
                        $query->andWhere(['between', 'tgl_admisi', $tgl_awal, $tgl_akhir]);
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

                if (isset($advancedFilters['kelaspelayanan_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(kelaspelayanan_nama)', strtolower($advancedFilters['kelaspelayanan_nama']) ]);
                }

                if (isset($advancedFilters['ruangan_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(ruangan_nama)', strtolower($advancedFilters['ruangan_nama']) ]);
                }

                if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($advancedFilters['jeniskasuspenyakit_nama']) ]);
                }

                if (isset($advancedFilters['dokter']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(dokter)', strtolower($advancedFilters['dokter']) ]);
                }

                if (isset($advancedFilters['carabayar_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($advancedFilters['carabayar_nama']) ]);
                }

                if (isset($advancedFilters['penjamin_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(penjamin_nama)', strtolower($advancedFilters['penjamin_nama']) ]);
                }

                if (isset($advancedFilters['carakeluar_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(carakeluar_nama)', strtolower($advancedFilters['carakeluar_nama']) ]);
                }

                if (isset($advancedFilters['kondisikeluar_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(kondisikeluar_nama)', strtolower($advancedFilters['kondisikeluar_nama']) ]);
                }
            }
            $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            $query->orderby(['tglpasienpulang' => SORT_DESC]);
            $no = 0;
            $resultData = $query->asArray()->all();
            foreach ($resultData as $key => $value) {
                $no ++;
                $nama_pasien = isset($value['nama_pasien']) ?  $value['nama_pasien'] : ' - ';
                $no_pendaftaran = isset($value['no_pendaftaran']) ?  $value['no_pendaftaran'] : ' - ';
                $no_rekam_medik = isset($value['no_rekam_medik']) ?  $value['no_rekam_medik'] : ' - ';
                $no_telpon = isset($value['no_telepon_pasien']) ?  $value['no_telepon_pasien'] : ' - ';
                $ruangan= isset($value['ruangan_nama']) ?  $value['ruangan_nama'] : ' - ';
                $kasus_penyakit = isset($value['jeniskasuspenyakit_nama']) ?  $value['jeniskasuspenyakit_nama'] : ' - ';
                $carabayar_nama = isset($value['carabayar_nama']) ?  $value['carabayar_nama'] : ' - ';
                $penjamin_nama = isset($value['penjamin_nama']) ?  $value['penjamin_nama'] : ' - ';
                $cara_kondisipulang = (is_null($value['pasienpulang_id']) && $value['is_stopakomodasi'] == true) ? 'STOP AKOMODASI' : $value['carakeluar_nama'] .' - '. $value['kondisikeluar_nama'];
                $petugas_pemulang_nama = isset($value['petugas_pemulang_nama']) ?  $value['petugas_pemulang_nama'] : ' - ';
                $kelaspelayanan_nama = isset($value['kelaspelayanan_nama']) ?  $value['kelaspelayanan_nama'] : ' - ';
                $kelasditagihkan_nama = isset($value['kelasditagihkan_nama']) ?  $value['kelasditagihkan_nama'] : ' - ';
                $tempKelas = $value['kelaspelayanan_nama'];
                $lamaRawat = isset($value['lama_rawat']) ? $value['lama_rawat'] : '';
                if(empty($lamaRawat)) {
                    $lamaRawat = $this->getLamaRawat($value['tgl_admisi'], $value['tglpasienpulang']);
                }
                $value['lama_rawat'] = $lamaRawat .' Hari';

                $data['Tanggal'] = 'Masuk :'.date('d M Y H:i:s', strtotime($value['tgl_admisi'])).' / Pulang :'.date('d M Y H:i:s', strtotime($value['tglpasienpulang']));
                $data['Pasien'] = 'Nama Pasien : '.$nama_pasien.' / No Pendaftaran : '.$no_pendaftaran.' / No Rekam Medik : '.$no_rekam_medik;
                $data['No Telepon'] = $no_telpon;
                $data['Kelas'] = 'Kelas Layanan : '.$kelaspelayanan_nama.' / Kelas Tagihan : '.$kelasditagihkan_nama;

                if (!array_key_exists('pk_kelas_ditagihkan_nama', $value)) {
                    $value['pk_kelas_ditagihkan_nama'] = '-';
                }

                if (!array_key_exists('kelas_ditagihkan_nama', $value)) {
                    $value['kelas_ditagihkan_nama'] = '-';
                }

                if ($value['pindahkamar_id']) {
                    if ($value['pk_is_stoptitipan'] == false) {
                        if ($value['pk_is_pasientitipan']) {
                            $data['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : '.$value['pk_kelas_ditagihkan_nama'];
                        }
                    }
                } else {
                    if ($value['is_stoptitipan'] == false) {
                        if ($value['is_pasientitipan']) {
                            $data['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : '.$value['kelas_ditagihkan_nama'];
                        }
                    }
                }

                
                $data['Ruangan'] = 'Kasus Penyakit : '.$kasus_penyakit.' / Ruangan : '.$ruangan.' / Lama Rawat : '.$value['lama_rawat'];
                $data['Dokter Penanggung Jawab'] = isset($value['dokter']) ?  $value['dokter'] : ' - ';
                $data['Cara Bayar'] = $carabayar_nama .' / '. $penjamin_nama;
                $data['Status'] = $cara_kondisipulang;
                $data['Dipulangkan Oleh'] = $petugas_pemulang_nama;
                $result[] = $data;
            }


            $header = [
                'Periode' => date('d F Y H:i:s', strtotime($tgl_awal)) . ' - '.date('d F Y H:i:s', strtotime($tgl_akhir))
            ];
            $options = [
                "skipIncrement" => true,
                "customFormatCode" => [
                    [
                        'startRow' => 'B13',
                        'endRow' => 'AG13'
                    ],
                    [
                        'startRow' => 'B15',
                        'endRow' => 'AG15'
                    ],
                    [
                        'startRow' => 'B19',
                        'endRow' => 'AG19'
                    ],
                    [
                        'startRow' => 'F5',
                        'endRow' => 'F'. (count($resultData) + 5), // +5 karena awalnya dari F5 bukan dari F1
                        'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT
                    ],
                ],
            ];

            $filePath = DocoHelpers::exportExcel("Informasi Pasien Pulang Rawat Inap", $result, $header, $options, [], [], true);
            $filePath->save('php://output');

            die;

            $filePath = DocoHelpers::exportExcel("Informasi Pasien Pulang Rumah Sakit", $result, $header,  array("uploadPath" => "./uploads"),[],[],true);

            $filePath->save('php://output');
   
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionKonfirmasi()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new KonfirmasiUnit;
            $model->attributes = $post;
            $model->is_konfirmasi = true;
            $check = KonfirmasiUnit::find()->where([
                'pendaftaran_id' => $model->pendaftaran_id,
                'instalasi_id' => $model->instalasi_id,
            ])->one();

            if(!empty($check)) {
                $response = [
                    'status' => 422,
                    'text' => 'Data Sudah di Konfirmasi!',
                ];
            }
            else {
                if($model->validate() && $model->save()) {
                    $response = [
                        'text' => 'Data Berhasil di Konfirmasi',
                    ];
                }
                else {
                    $response = [
                        'status' => 422,
                        'text' => 'Data Gagal di Konfirmasi',
                    ];
                }
            }
            return $response;
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

    private function getLamaRawat($startDate, $endDate) {
        $earlier = new \DateTime($startDate);
        $later = new \DateTime($endDate);
        $abs_diff = $later->diff($earlier)->format("%a");
        return ($abs_diff == 0) ? 1 : $abs_diff;
    }

}
