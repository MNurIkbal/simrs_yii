<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Iqbal
 * @Date:   2018-07-23 16:21:21
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 11:30:00
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\PermintaanKonsul;
use app\modules\v1\models\PermintaanKonsulView;
use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\InfoPermintaanKonsul;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\ResumeMedisRIT;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class InfPasienKonsulController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPermintaanKonsul';
    protected $_title = 'Informasi Pasien';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["komponen-tarif"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $ruanganId = $request->get('ruangan_id');
            $dokterId = $request->get('dokter_id');
            $model = new PermintaanKonsulView;
            $query = $model::find();
            $query->orderBy(['waktu_permintaan' => SORT_DESC])
                    ->andWhere(['!=','status_konsul',DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL]);

            if(isset($_GET['advanced-filter'])){            
                $filter = $_GET['advanced-filter'];
                $between = false;
                $start = date('Y-m-d 00:00:00');
                $end = date('Y-m-d 23:59:00');
                if(isset($filter['waktu_permintaan'])){             
                    $explode = explode(" - ", $filter['waktu_permintaan']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($filter['waktu_permintaan']); // Unset Advanced Filter  date range
                    $between = true;
                    $query->andWhere(['between', 'waktu_permintaan', $start, $end]);
                }
               
                if(isset($filter['nama_pasien'])){             
                   $query->andWhere(['nama_pasien' => $filter['nama_pasien']]);
                }

                if(isset($filter['no_rekam_medik'])){
                    $query->andWhere(['no_rekam_medik' => $filter['no_rekam_medik'] ]);
                }
                if(isset($filter['no_pendaftaran'])){
                    $query->andWhere(['no_pendaftaran' => $filter['no_pendaftaran'] ]);
                }

                if(isset($filter['caraBayarPenjamin'])){
                    $explode = explode(" / ", $filter['caraBayarPenjamin']);
                    $query->andWhere(['carabayar_nama' => $explode[0] ]);
                    $query->andWhere(['penjamin_nama' => $explode[1] ]);
                }
                
                if(isset($filter['kelompokpegawai_id'])){
                    if($filter['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_DOKTER){
                        $query->andWhere(['dokter_id' => Yii::$app->jwt->user->pegawai_id]);
                    }
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
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

    public function actionViewDataPasienKonsul($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    public function actionDataPasienKonsul()
    {
        try {
            $request = Yii::$app->request;
            $permintaankonsul_id = $request->get('permintaankonsul_id');
            $model = new PermintaanKonsulView;
            $query = $model::find();
            $result = $query->select(['*'])
                            ->where(['permintaankonsul_id'=>$permintaankonsul_id ])->asArray()->one();
            
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

    public function actionSetujuiPermintaanKonsul()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = PermintaanKonsul::findOne($request->post('permintaankonsul_id'));
            $data =['status_konsul'=> $request->post('status_konsul')];
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            if ($request->post() && !empty($model)) {
                $model->status_konsul = $request->post('status_konsul');
                $model->waktu_persetujuan = $request->post('waktu_persetujuan');
                $model->disetujui_oleh = isset($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : 0;
                if (!$model->save()) {
                    $errors = DocoHelpers::parseError($model->errors,'SetujuiPermintaanKonsul');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }

                if($model->jenis_konsul == DocoConstants::JNS_KNSL_AR || $model->jenis_konsul == DocoConstants::JNS_KNSL_AR_RB){
                    if($model->status_konsul == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU){
                        $mAdmisi = PasienAdmisi::findOne($model->pasienadmisi_id);
                        $mAdmisi->pegawai_id = $model->dokter_id;
                        if(!$mAdmisi->update()){
                            $transaction->rollBack();
                            throw new \Yii\db\Exception("Ubah DPJP tidak berhasil",$mAdmisi->getErrors(),500);
                        }
                    }
                }
                // Clear cache
                Yii::$app->cache->delete('gizi-pendaftaran-id-'. DocoHelpers::encrypt($model->pendaftaran_id));

                $transaction->commit();

                return [
                    'message' => 'Data Berhasil di simpan',
                ];
            }
            throw new \Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    
    public function actionJawabanPermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $model = PermintaanKonsul::findOne($request->post('permintaankonsul_id'));
            $data = ['jawaban_konsul'=> $request->post('jawaban_konsul')];
            if ($request->post() && !empty($model)) {
                $model->jawaban_konsul = $request->post('jawaban_konsul');
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JawabanPermintaanKonsul');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Data Informasi Pasien Konsul';
            $get = $request->get();
            
            $model = new PermintaanKonsulView;
            $query = $model::find();
            $query->orderby('waktu_permintaan DESC')
                    ->andWhere(['ruangan_id'=> $_GET['advanced-filter']['ruangan_id'] ])
                    ->andWhere(['dokter_id'=> $_GET['advanced-filter']['dokter_id'] ])
                    ->andWhere(['!=','status_konsul',DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL]);

            if(isset($_GET['advanced-filter'])){            
                $filter = $_GET['advanced-filter'];
                $between = false;
                $start = date('Y-m-d 00:00:00');
                $end = date('Y-m-d 23:59:00');
                if(isset($filter['waktu_permintaan'])){             
                    $explode = explode(" - ", $filter['waktu_permintaan']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($filter['waktu_permintaan']); // Unset Advanced Filter  date range
                    $between = true;
                    $query->andWhere(['between', 'waktu_permintaan', $start, $end]);
                }
               
                if(isset($filter['nama_pasien'])){             
                   $query->andWhere(['nama_pasien' => $filter['nama_pasien']]);
                }

                if(isset($filter['no_rekam_medik'])){
                    $query->andWhere(['no_rekam_medik' => $filter['no_rekam_medik'] ]);
                }
                if(isset($filter['no_pendaftaran'])){
                    $query->andWhere(['no_pendaftaran' => $filter['no_pendaftaran'] ]);
                }

                if(isset($filter['caraBayarPenjamin'])){
                    $explode = explode(" / ", $filter['caraBayarPenjamin']);
                    $query->andWhere(['carabayar_nama' => $explode[0] ]);
                    $query->andWhere(['penjamin_nama' => $explode[1] ]);
                }

                if(isset($filter['noRuangan'])){
                    $explodeSpace = explode("@#", $filter['noRuangan']);
                    $query->andWhere(['ruangan_nama' => $explodeSpace[0] ]);
                    $query->andWhere(['kamarruangan_nokamar' => $explodeSpace[1] ]);
                    $query->andWhere(['no_tempattidur' => $explodeSpace[2] ]);
                }
            }

            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $tgl_admisi = date('Y-m-d', strtotime($value['tgl_admisi'])); //date from database 
                $now = date('Y-m-d', strtotime('NOW'));
                $date1 = new \DateTime($tgl_admisi);
                $date2 = new \DateTime($now);
                $diff = $date1->diff($date2);
                $value['hakKelas'] = $value['kls_hak'].'/'.$value['kls_rawat'].'/'.isset($value['kelas_ditagihkan_nama'])?$value['kelas_ditagihkan_nama']:'-';
                $value['caraBayarPenjamin'] = $value['carabayar_nama'].'/'.$value['penjamin_nama'];
                $value['noRuangan'] = $value['ruangan_nama'].' '.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'];
                $value['hariRawat'] = $diff->days;
                $value['gab_noRmPdft'] = $value['no_rekam_medik']." / ".$value['no_pendaftaran'];
                $value['waktu_permintaan'] = date('d F Y H:i:s', strtotime($value['waktu_permintaan']));
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
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $title = 'Data Informasi Pasien Konsul';
        $result = [];
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if ($request->get()) {
            $get = $request->get('advanced-filter');
            $model = new PermintaanKonsulView;
            $query = $model::find();
            $query->select([
                'tgl_admisi',
                'kls_hak',
                'kls_rawat',
                'carabayar_nama',
                'penjamin_nama',
                'ruangan_nama',
                'kamarruangan_nokamar',
                'no_tempattidur',
                'no_rekam_medik',
                'no_pendaftaran',
                'waktu_permintaan',
                'nama_pasien',
                'jenis_kelamin',
                'dok_dpjp',
                'jenis_konsul_nama',
                'dok_konsul'
            ]);
            $query->orderby('waktu_permintaan DESC')
                    ->andWhere(['ruangan_id'=> $get['ruangan_id'] ])
                    ->andWhere(['dokter_id'=> $get['dokter_id'] ])
                    ->andWhere(['!=','status_konsul',DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL]);

            if(isset($_GET['advanced-filter'])){            
                $filter = $_GET['advanced-filter'];
                $between = false;
                if(isset($filter['waktu_permintaan'])){             
                    $explode = explode(" - ", $filter['waktu_permintaan']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($filter['waktu_permintaan']); // Unset Advanced Filter  date range
                    $between = true;
                    $query->andWhere(['between', 'waktu_permintaan', $start, $end]);
                }
               
                if(isset($filter['nama_pasien'])){             
                   $query->andWhere(['nama_pasien' => $filter['nama_pasien']]);
                }

                if(isset($filter['no_rekam_medik'])){
                    $query->andWhere(['no_rekam_medik' => $filter['no_rekam_medik'] ]);
                }
                if(isset($filter['no_pendaftaran'])){
                    $query->andWhere(['no_pendaftaran' => $filter['no_pendaftaran'] ]);
                }

                if(isset($filter['caraBayarPenjamin'])){
                    $explode = explode(" / ", $filter['caraBayarPenjamin']);
                    $query->andWhere(['carabayar_nama' => $explode[0] ]);
                    $query->andWhere(['penjamin_nama' => $explode[1] ]);
                }

                if(isset($filter['noRuangan'])){
                    $explodeSpace = explode("@#", $filter['noRuangan']);
                    $query->andWhere(['ruangan_nama' => $explodeSpace[0] ]);
                    $query->andWhere(['kamarruangan_nokamar' => $explodeSpace[1] ]);
                    $query->andWhere(['no_tempattidur' => $explodeSpace[2] ]);
                }
                if(isset($filter['jenis_konsul_nama'])){
                    $query->andWhere(['like', 'jenis_konsul_nama', $filter['jenis_konsul_nama']]);
                }
            }

            
            foreach ($query->asArray()->all() as $key => $value) {
                $value['Tanggal_Permintaan_Konsul'] = date('d F Y H:i:s', strtotime($value['waktu_permintaan']));
                // $value['no_rekam_medik/No_pendaftaran'] = $value['no_rekam_medik']." / ".$value['no_pendaftaran'];
                $value['No._RM'] = $value['no_rekam_medik'];
                $value['No_Pendaftaran'] = $value['no_pendaftaran'];
                $value['nama_Pasien'] = $value['nama_pasien'];
                $value['jenis_Kelamin'] = $value['jenis_kelamin'];
                $value['dokter_DPJP'] = $value['dok_dpjp'];
                $value['cara_Bayar_Penjamin'] = $value['carabayar_nama'].'/'.$value['penjamin_nama'];
                $value['hak_Kelas/Kelas_saat_ini/Kelas_tagihan'] = isset($value['kls_hak'])?$value['kls_hak']:'-'.'/'.$value['kls_rawat'].'/'.isset($value['kelas_ditagihkan_nama'])?$value['kelas_ditagihkan_nama']:'-';
                $value['nama_Ruangan_No.Kamar-No.Bed'] = $value['ruangan_nama'].' '.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'];
                $value['jenis_Konsul'] = $value['jenis_konsul_nama'];
                $tgl_admisi = date('Y-m-d', strtotime($value['tgl_admisi'])); //date from database 
                $now = date('Y-m-d', strtotime('NOW'));
                $date1 = new \DateTime($tgl_admisi);
                $date2 = new \DateTime($now);
                $diff = $date1->diff($date2);
                $value['hari_Rawat'] = $diff->days;
                $value['dokter_Konsul'] = $value['dok_konsul'];
                unset($value['no_rekam_medik']);
                unset($value['no_pendaftaran']);
                unset($value['dok_konsul']);
                unset($value['jenis_konsul_nama']);
                unset($value['dok_dpjp']);
                unset($value['jenis_kelamin']);
                unset($value['nama_pasien']);
                unset($value['kls_hak']);
                unset($value['kls_rawat']);
                unset($value['penjamin_nama']);
                unset($value['carabayar_nama']);
                unset($value['ruangan_nama']);
                unset($value['kamarruangan_nokamar']);
                unset($value['no_tempattidur']);
                unset($value['tgl_admisi']);
                unset($value['waktu_permintaan']);
                $result[] = $value;
            }
        }

        $header = ['Periode'=> date('d F Y H:i:s', strtotime($start)) . ' - '.date('d F Y H:i:s', strtotime($end))];

        $filePath = DocoHelpers::exportExcel("Info Pasien Konsul", $result, $header,  array(
            "uploadPath" => "./uploads",
        ),[],[],true);

        $filePath->save('php://output');
        die;
    }
}   