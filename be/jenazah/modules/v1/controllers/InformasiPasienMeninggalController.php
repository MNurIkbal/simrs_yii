<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPasienMeninggal;
use app\modules\v1\models\InfoPasienMeninggalDetail;
use app\modules\v1\models\InfoPasienMeninggalDetail2;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\HistoriJenazah;
use app\modules\v1\models\AmbilJenazah;
use app\modules\v1\models\PersetujuanJenazah;
use app\modules\v1\models\AmbilJenazahView;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\ObatAlkesPasien;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class InformasiPasienMeninggalController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienMeninggal';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['save']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $model = new InfoPasienMeninggal;
        $query = $model::find();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $startPulang = date('Y-m-d 00:00:00');
        $endPulang = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_meninggal_awal']) && isset($_GET['advanced-filter']['tgl_meninggal_akhir'])) {
                $start = $_GET['advanced-filter']['tgl_meninggal_awal'];
                $end = $_GET['advanced-filter']['tgl_meninggal_akhir'];
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruanganakhir_id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere(['ruanganakhir_id' => $ruanganakhir_id]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            if(isset($_GET['advanced-filter']['status_periksa_nama'])) {
                $status_periksa = $_GET['advanced-filter']['status_periksa_nama'];
                $query->andWhere(['status_periksa' => $status_periksa]);
                unset($_GET['advanced-filter']['status_periksa_nama']);
            }

            if(isset($_GET['advanced-filter']['jenis_kelamin'])) {
                $jeniskelamin = $_GET['advanced-filter']['jenis_kelamin'];
                $query->andWhere(['jeniskelamin' => $jeniskelamin]);
                unset($_GET['advanced-filter']['jenis_kelamin']);
            }

            if(isset($_GET['advanced-filter']['penanggungjawab_nama'])) {
                $penanggungjawab_nama = $_GET['advanced-filter']['penanggungjawab_nama'];
                $query->andWhere(['ILIKE', 'penanggungjawab_nama', $penanggungjawab_nama]);
            }

            if(isset($_GET['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
            }

            if(isset($_GET['advanced-filter']['no_rekam_medik'])) {
                $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                $query->andWhere(['ILIKE', 'no_rekam_medik', $no_rekam_medik]);
            }      
        }
        
        $query->andWhere(['between', 'tgl_meninggal', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetTindakanObat()
    {
        $request = Yii::$app->request;
        $model = new InfoPasienMeninggalDetail2;
        $query = $model::find()->where(['pendaftaran_id' => $request->get('pendaftaran_id')]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetObat()
    {
        $request = Yii::$app->request;
        $model = new InfoPasienMeninggalDetail2;
        $query = $model::find()
            ->where(['pendaftaran_id' => $request->get('pendaftaran_id'), 'jenis' => 'obat']);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
    {
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()->where(['is_active' => true]);
        $queryRuangan = $queryRuangan->asArray()->all();

        $modelStatus = new Lookup;
        $queryStatus = $modelStatus::find()->where(['is_active' => true])->andWhere(['ILIKE', 'lookup_kode', 'JNZ'])
            ->asArray()->all();

        $modelGender = new Lookup;
        $queryGender = $modelGender::find()->where(['is_active' => true])->andWhere(['ILIKE', 'lookup_type', 'jenis_kelamin'])
            ->asArray()->all();

        $modelHubunganKeluarga = new Lookup;
        $queryHubunganKeluarga = $modelHubunganKeluarga::find()
            ->where(['is_active' => true, 'lookup_type' => 'hubungan_keluarga'])
            ->orderBy('lookup_name')
            ->asArray()->all();

        $modelJenisIdentitas = new Lookup;
        $queryJenisIdentitas = $modelJenisIdentitas::find()
            ->where(['is_active' => true, 'lookup_type' => 'jenis_identitas'])
            ->orderBy('lookup_name')
            ->asArray()->all();

        $modelPegawai = new PegawaiView;
        $queryPegawai = $modelPegawai::find()->where(['ruangan_id' => 38]);
        $queryPegawai = $queryPegawai->asArray()->all();

        return [
            'ruangan' => $queryRuangan,
            'status_jenazah' => $queryStatus,
            'jenis_kelamin' => $queryGender,
            'hubungan_keluarga' => $queryHubunganKeluarga,
            'jenis_identitas' => $queryJenisIdentitas,
            'pegawai' => $queryPegawai,
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoPasienMeninggal;
        $query = $model::find();
        
        $title = 'Pasien Meninggal';

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $arrNamaJenazah = [];
        $arrNoRM = [];
        $arrRuangan = [];
        $arrStatus = [];
        $arrJk = [];
        $arrNamaPj = [];

        $ruangan_nama = '';
        $status = '';
        $jenis_kelamin = '';

        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tgl_meninggal'])) {
                $exp = explode(' - ', $advancedFilters['tgl_meninggal']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $advancedFilters['tgl_meninggal_awal'] = $tgl_awal_format;
                $advancedFilters['tgl_meninggal_akhir'] = $tgl_akhir_format;
                $tgl_awal = $advancedFilters['tgl_meninggal_awal'];
                $tgl_akhir = $advancedFilters['tgl_meninggal_akhir'];
            }
            
            if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruanganakhir_id = $_GET['advanced-filter']['ruangan_nama'];
                $ruangan = Ruangan::findOne($ruanganakhir_id);
                $ruangan_nama = $ruangan->ruangan_nama;
                $arrRuangan = ['Ruangan' => $ruangan_nama];
                $query->andWhere(['ruanganakhir_id' => $ruanganakhir_id]);
            }

            if(isset($_GET['advanced-filter']['status_periksa_nama'])) {
                $status_periksa = $_GET['advanced-filter']['status_periksa_nama'];
                $modelStatus = Lookup::findOne($status_periksa);
                $status = $modelStatus->lookup_name;
                $arrStatus = ['Status' => $status];
                $query->andWhere(['status_periksa' => $status_periksa]);
            }

            if(isset($_GET['advanced-filter']['jenis_kelamin'])) {
                $jeniskelamin = $_GET['advanced-filter']['jenis_kelamin'];
                $modelGender = Lookup::findOne($jeniskelamin);
                $jenis_kelamin = $modelGender->lookup_name;
                $arrJk = ['Jenis Kelamin' => $jenis_kelamin];
                $query->andWhere(['jeniskelamin' => $jeniskelamin]);
            }

            if(isset($_GET['advanced-filter']['penanggungjawab_nama'])) {
                $penanggungjawab_nama = $_GET['advanced-filter']['penanggungjawab_nama'];
                $query->andWhere(['ILIKE', 'penanggungjawab_nama', $penanggungjawab_nama]);
                $arrNamaPj = ['Penanggung Jawab' => $penanggungjawab_nama];
            }

            if(isset($_GET['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
                $arrNamaJenazah = ['Nama Jenazah' => $nama_pasien];
            }

            if(isset($_GET['advanced-filter']['no_rekam_medik'])) {
                $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                $query->andWhere(['ILIKE', 'no_rekam_medik', $no_rekam_medik]);
                $arrNoRM = ['Nomor Rekam Medik' => $no_rekam_medik];
            }
        }

        $periode = [ Yii::t('app', "Periode Tanggal Meninggal") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];

        $additional = array_merge($arrRuangan, $arrStatus, $arrJk, $arrNamaPj, $arrNamaJenazah, $arrNoRM);
        $header = array_merge($periode, $additional);
        $query->andWhere(['between', 'tgl_meninggal', $tgl_awal, $tgl_akhir]);
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Meninggal')] = date('d M Y', strtotime($value['tgl_meninggal']));
            $newValue[\Yii::t('app', 'Nama Jenazah')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'No Rekam Medik')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Jenis Kelamin')] = $value['jenis_kelamin'];
            $newValue[\Yii::t('app', 'Ruangan Asal')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Penyebab Meninggal Dunia')] = $value['diagnosa_nama'];
            $newValue[\Yii::t('app', 'Nama Penanggungjawab Jawab')] = $value['penanggungjawab_nama'];
            $newValue[\Yii::t('app', 'Status')] = $value['status_periksa_nama'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ),[],[],true);

        $filePath->save('php://output');
        die;
    }

    public function actionGetDataJenazah($id)
    {
        $query =  InfoPasienMeninggal::find()->where(['pendaftaran_id' => $id])->one();

        return $query;
    }

    public function actionGetDataMasukPenunjang($id)
    {
        $query =  PasienMasukPenunjang::find()->where(['pendaftaran_id' => $id])->one();

        return $query;
    }

    public function actionSave()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $model = PasienMasukPenunjang::find()
            ->where(['pendaftaran_id' => $post['pendaftaran_id']])
            ->one();

        if($model) {
            try {
                $model->attributes = $post;
                $tglmasukpenunjang = date('Y-m-d',strtotime($post['tglserah_terima']));
                $model->tglmasukpenunjang = $tglmasukpenunjang;
                $model->status_periksa = 577;
                $model->ruangan_id = 38;

                if($model->validate() && $model->save()) {
                    $histori = new HistoriJenazah;
                    $histori->pasien_id = $post['pasien_id'];
                    $histori->pendaftaran_id = $post['pendaftaran_id'];
                    $histori->pasienmasukpenunjang_id = $model->pasienmasukpenunjang_id;
                    $histori->tgl_pelayanan = $model->tglmasukpenunjang;
                    $histori->pegawai_id = $model->pegawai_id;
                    $histori->status = DocoConstants::STATUS_DITERIMA_JNZ;
                    $histori->save(false);
                    
                    $no_masukpenunjang = $model->no_masukpenunjang;
                    $transaction->commit();
                    $response = [
                        'text' => 'Serah Terima Pasien Meninggal Dunia berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'no_masukpenunjang' => $no_masukpenunjang
                    ];
                    
                    return $response;
                }
                else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            } 
            catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }
        }
    }

    public function actionSaveKeluarga()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $model = new AmbilJenazah;
        $model->attributes = $post;
        $tgl_lahir = date('Y-m-d',strtotime($post['tgl_lahir']));
        $model->tgl_lahir = $tgl_lahir;
        $model->ruangan_id = 38;
        try {
            if($model->validate() && $model->save()) {
                $masukpenunjang = PasienMasukPenunjang::find()->where(['pendaftaran_id' => $model->pendaftaran_id])->one();
                if($masukpenunjang) {
                    $masukpenunjang->status_periksa = 579;
                    $masukpenunjang->save(false);
                }
                $pendaftaran_id = $model->pendaftaran_id;
                $transaction->commit();
                $response = [
                    'text' => 'Simpan Telah Berhasil, Apakah Akan Print Serah Terima Jenazah ke Keluarga ?',
                    'title' => 'Proses berhasil !',
                    'pendaftaran_id' => $pendaftaran_id
                ];
                
                return $response;
            }
            else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } 
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSaveProses()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $postPenunjang = $post['PasienMasukPenunjangForm'];
        $pendaftaran_id = $post['pendaftaran_id'];
        $model = PasienMasukPenunjang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $model->attributes = $postPenunjang;
        $model->status_periksa = 578;
        
        try {
            if($model->validate() && $model->save()) {
                if(isset($post['Tindakan'])) {
                    foreach ($post['Tindakan'] as $key => $value) {
                        $cekIsTindakan = InfoPasienMeninggalDetail2::find()
                            ->where(['pendaftaran_id' => $pendaftaran_id, 'tindakan_obat_id' => $key]) 
                            ->one();

                        if($cekIsTindakan->jenis == 'tindakan') {
                            $value['tgl_tindakan'] = date('Y-m-d', strtotime($value['tgl_tindakan']));
                            $tindakanPelayanan = TindakanPelayanan::find()
                                ->where(['pendaftaran_id' => $pendaftaran_id, 'daftartindakan_id' => $key])
                                ->one();

                            $is_dilakukan = isset($value['dilakukan']) ? true : false;
                            $tindakanPelayanan->is_dilakukan = $is_dilakukan;
                            $tindakanPelayanan->tgl_tindakan = isset($value['tgl_tindakan']) ? $value['tgl_tindakan'] : null;
                            $tindakanPelayanan->save(false);
                        }
                        else {
                            $obatAlkesPasien = ObatAlkesPasien::find()
                            ->where(['pendaftaran_id' => $pendaftaran_id, 'obatalkes_id' => $key])
                            ->one();

                            $is_dilakukan = isset($value['dilakukan']) ? true : false;
                            $obatAlkesPasien->is_dilakukan = $is_dilakukan;
                            $obatAlkesPasien->tglpelayanan = date('Y-m-d H:i:s', strtotime($value['tgl_tindakan']));
                            $obatAlkesPasien->save(false);
                        }
                    }
                }

                $transaction->commit();
                $response = [
                    'text' => 'Simpan Telah Berhasil, Apakah Akan Print Pemrosesan Pasien Meninggal?',
                    'title' => 'Proses berhasil !',
                    'pendaftaran_id' => $pendaftaran_id
                ];
                
                return $response;
            }
            else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } 
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }


    /**
    * @controller actionPrintBelumDiterima 
    * @attribute #table# => table
    * @attribute #title# => title
    * @attribute #nama_pasien# => nama pasien
    * @attribute #alamat_pasien# => alamat
    * @attribute #tanggal_lahir# => tgl lahir
    * @attribute #pegawai_jenazah# => pegawai jenazah
    * @attribute #jabatan_pegjenazah# => jabatan_pegjenazah
    * @attribute #pegawai_ruangan# => pegawai_ruangan
    * @attribute #jabatan_nama# => jabatan_nama
    * @attribute #catatan# => catatan
    * @attribute #tanggal# => tanggal
    * @attribute #hari_meninggal# => hari_meninggal
    **/
    public function actionPrintBelumDiterima()
    {
        $title = 'Surat Persetujuan Pelayanan Jenazah';
        $pendaftaran_id = isset($_GET['pendaftaran_id']) ? $_GET['pendaftaran_id'] : '';
        $query =  InfoPasienMeninggal::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $tindakan =  InfoPasienMeninggalDetail2::find()
            ->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'tindakan'])->all();

        $obat =  InfoPasienMeninggalDetail2::find()->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'obat'])->all();
        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,

            // data penanggung jawab
            '#nama_pj#' => $query->penanggungjawab_nama,
            '#umur_jk#' => $query->umur_pj.' Thn / '.$query->jenis_kelamin_pj,
            '#alamat_pj#' => $query->alamat,
            '#no_telp#' => $query->no_kontak,
            '#hubungan_keluarga#' => $query->hubungan_kel,

            '#nama_pasien#' => $query->nama_pasien,
            '#tanggal_lahir#' => date('d-M-Y', strtotime($query->tanggal_lahir)).' / '.$query->umur,
            '#alamat_pasien#' => $query->alamat_pasien,
            '#no_rekam_medik#' => $query->no_rekam_medik,
            '#ruangan_asal#' => $query->ruangan_nama,
            '#penyebab_meninggal#' => $query->diagnosa_nama,
            '#kondisi#' => $query->kondisi,
            '#pegawai_ruangan#' => $query->pegawai_ruangan,
            '#jabatan_nama#' => $query->jabatan_nama,
            '#tanggal#' => date('d M Y'),
            '#table#' => $this->renderPartial('_belum_diterima',
                [
                    'tindakan' => $tindakan,
                    'obat' => $obat,
                ]
        )];
        $print->Output();
    }

    /**
    * @controller actionPrintSerahTerima 
    * @attribute #table# => table
    * @attribute #title# => title
    * @attribute #nama_pasien# => nama pasien
    * @attribute #alamat_pasien# => alamat
    * @attribute #tanggal_lahir# => tgl lahir
    * @attribute #pegawai_jenazah# => pegawai jenazah
    * @attribute #jabatan_pegjenazah# => jabatan_pegjenazah
    * @attribute #pegawai_ruangan# => pegawai_ruangan
    * @attribute #jabatan_nama# => jabatan_nama
    * @attribute #catatan# => catatan
    * @attribute #tanggal# => tanggal
    * @attribute #hari_meninggal# => hari_meninggal
    **/
    public function actionPrintSerahTerima()
    {
        $title = 'Serah Terima Jenazah';
        $pendaftaran_id = isset($_GET['pendaftaran_id']) ? $_GET['pendaftaran_id'] : '';
        $masukpenunjang = PasienMasukPenunjang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $query =  InfoPasienMeninggal::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        
        $alat =  InfoPasienMeninggalDetail::find()
            ->where(['pendaftaran_id' => $pendaftaran_id, 'alat_id' => 'ALAT'])
            ->andWhere(['IS NOT', 'nama_alat', null])
            ->all();
        
        $linen =  InfoPasienMeninggalDetail::find()->where(['pendaftaran_id' => $pendaftaran_id, 'alat_id' => 'LINEN'])->all();
        $tindakan =  InfoPasienMeninggalDetail2::find()
            ->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'tindakan'])->all();

        $obat =  InfoPasienMeninggalDetail2::find()->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'obat'])->all();
        $histori = HistoriJenazah::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $date = date('Y-m-d', strtotime($histori->tgl_pelayanan));
        $nameOfDay = date('D', strtotime($date)); 
        $listHari = DocoHelpers::$_hari_indo;

        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#nama_pasien#' => $query->nama_pasien,
            '#tanggal_lahir#' => date('d-M-Y', strtotime($query->tanggal_lahir)).' / '.$query->umur,
            '#alamat_pasien#' => $query->alamat_pasien,
            '#pegawai_jenazah#' => $query->pegawai_jenazah,
            '#jabatan_pegjenazah#' => $query->jabatan_pegjenazah,
            '#pegawai_ruangan#' => $query->pegawai_ruangan,
            '#jabatan_nama#' => $query->jabatan_nama,
            '#catatan#' => $masukpenunjang->catatan_proses,
            '#tanggal#' => date('d M Y'),
            '#hari_meninggal#' => $listHari[$nameOfDay].', '.date('d M Y', strtotime($histori->tgl_pelayanan)).' Jam : '.date('H:i:s', strtotime($histori->tgl_pelayanan)),
            '#table#' => $this->renderPartial('_serah_terima_ruangan',
                [
                    'linen' => $linen,
                    'alat' => $alat,
                    'tindakan' => $tindakan,
                    'obat' => $obat,
                ]
        )];
        $print->Output();
    }

    /**
    * @controller actionPrintSerahTerimaKeluarga 
    * @attribute #table# => table
    * @attribute #title# => title
    * @attribute #nama_pasien# => nama pasien
    * @attribute #tanggal_lahir# => tgl lahir
    * @attribute #alamat_pasien# => alamat
    * @attribute #nama_pj# => nama penanggung jawab
    * @attribute #umur_jk# => umur penanggung jawab
    * @attribute #alamat_pj# => alamat penanggung jawab
    * @attribute #no_telp# => no_telp penanggung jawab
    * @attribute #hubungan_keluarga# => hubungan_keluarga
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #ruangan_asal# => ruangan_asal
    * @attribute #penyebab_meninggal# => penyebab_meninggal
    * @attribute #kondisi# => kondisi
    * @attribute #tanggal# => tanggal sekarang
    **/
    public function actionPrintSerahTerimaKeluarga()
    {
        $title = 'Pernyataan Serah Terima Jenazah';
        $pendaftaran_id = isset($_GET['pendaftaran_id']) ? $_GET['pendaftaran_id'] : '';
        $ambilJenazah = AmbilJenazah::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $masukPenunjang = PasienMasukPenunjang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $query =  InfoPasienMeninggal::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $histori = HistoriJenazah::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $dataJenazah = AmbilJenazahView::find()->where(['ambiljenazah_id' => $ambilJenazah->ambiljenazah_id])->one();
        $tindakan =  InfoPasienMeninggalDetail2::find()
                ->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'tindakan'])->all();

        $obat =  InfoPasienMeninggalDetail2::find()
            ->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'obat'])->all();

        $listHari = DocoHelpers::$_hari_indo;
        $dateAmbil = date('Y-m-d', strtotime($dataJenazah->tgl_ambil));
        $nameOfDayAmbil = date('D', strtotime($dateAmbil)); 
        $date = date('Y-m-d', strtotime($histori->tgl_pelayanan));
        $nameOfDay = date('D', strtotime($date)); 
        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            // data penanggung jawab
            '#nama_pj#' => $query->penanggungjawab_nama,
            '#umur_jk#' => $query->tempat_lahir.' / '.date('d M Y', strtotime($query->tanggal_lahir)),
            '#alamat_pj#' => $query->alamat,
            '#no_telp#' => $query->no_kontak,
            '#hubungan_keluarga#' => $query->hubungan_kel,
            '#pekerjaan#' => $dataJenazah->pekerjaan,
            '#alamat#' => $dataJenazah->alamat,
            '#no_identitas#' => $dataJenazah->no_identitas,
            '#nama_login#' => $dataJenazah->nama_pegawai,

            // data jenazah
            '#nama_pasien#' => $query->nama_pasien,
            '#no_rekam_medik#' => $query->no_rekam_medik,
            '#ruangan_asal#' => $query->ruangan_nama,
            '#tanggal_lahir#' => $query->tanggal_lahir .' / '.$query->umur,
            '#alamat_pasien#' => $query->alamat_pasien,
            '#penyebab_meninggal#' => $query->diagnosa_nama,
            '#nama_rumahsakit#' => $dataJenazah->nama_rumahsakit,
            '#kondisi#' => $query->kondisi,
            '#tanggal#' => date('d M Y'),
            '#pegawai_ruangan#' => $query->pegawai_ruangan,
            '#hari#' => $listHari[$nameOfDay].', '.date('d M Y', strtotime($histori->tgl_pelayanan)).' Jam : '.date('H:i:s', strtotime($histori->tgl_pelayanan)),
            '#hari_ambil#' => $listHari[$nameOfDayAmbil].', '.date('d M Y', strtotime($dataJenazah->tgl_ambil)).' Jam : '.date('H:i:s', strtotime($dataJenazah->tgl_ambil)),
            '#table#' => $this->renderPartial('_serah_terima',
                [
                    'tindakan' => $tindakan,
                    'obat' => $obat,
                ]
        )];
        $print->Output();
    }

    /**
    * @controller actionPrintProses 
    * @attribute #table# => table
    * @attribute #title# => title
    * @attribute #nama_pasien# => nama pasien
    * @attribute #tanggal_lahir# => tgl lahir
    * @attribute #alamat_pasien# => alamat
    * @attribute #pegawai_jenazah# => pegawai jenazah
    * @attribute #jabatan_pegjenazah# => jabatan pegawai jenazah
    * @attribute #catatan# => catatan
    * @attribute #tanggal# => tanggal
    * @attribute #hari_meninggal# => hari_meninggal
    **/
    public function actionPrintProses()
    {
        $title = 'Bukti Pelayanan Jenazah';
        $pendaftaran_id = isset($_GET['pendaftaran_id']) ? $_GET['pendaftaran_id'] : '';
        $query =  InfoPasienMeninggal::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $masukPenunjang = PasienMasukPenunjang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $histori = HistoriJenazah::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $date = date('Y-m-d', strtotime($histori->tgl_pelayanan));
        $nameOfDay = date('D', strtotime($date));
        
        $tindakan =  InfoPasienMeninggalDetail2::find()
            ->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'tindakan'])->all();

        $obat =  InfoPasienMeninggalDetail2::find()->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'obat'])->all();

        $listHari = DocoHelpers::$_hari_indo;
        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#nama_pasien#' => $query->nama_pasien,
            '#tanggal_lahir#' => date('d M Y', strtotime($query->tanggal_lahir)) .' / '.$query->umur,
            '#alamat_pasien#' => $query->alamat_pasien,
            '#pegawai_jenazah#' => $query->pegawai_jenazah,
            '#jabatan_pegjenazah#' => $query->jabatan_pegjenazah,
            '#catatan#' => $masukPenunjang->catatan_proses,
            '#tanggal#' => date('d M Y'),
            '#hari_meninggal#' => $listHari[$nameOfDay].', '.date('d M Y', strtotime($histori->tgl_pelayanan)).' Jam : '.date('H:i:s', strtotime($histori->tgl_pelayanan)),
            '#table#' => $this->renderPartial('_proses',
                [
                    'tindakan' => $tindakan,
                    'obat' => $obat,
                ]
        ),
        ];
        $print->Output();

        
    }
}