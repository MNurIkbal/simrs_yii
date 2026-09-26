<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Sunarko
 * @Date:   2018-06-05 16:21:21
 * @Last Modified by:
 * @Last Modified time:
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoTarifRs;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class InfTarifRanapController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienRanap';
    protected $_title = 'Informasi Pasien';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        //$verbs["update"] = ["POST", "GET"];
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

        $model = new InfoTarifRs;
        $query = $model::find();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $ruanganId = $_GET['ruangan_id'];
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_admisi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_admisi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_admisi']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['ruangan_id' => $ruanganId]);
        $query->andWhere(['not in', 'status_ranap', ['453']]);
        $query->andWhere(['between', 'tgl_admisi', $start, $end]);
        
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        echo "<pre>"; var_dump($query);die();

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $idRuangan = $post['idR']; 
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select pendaftaran_id, no_pendaftaran from infopasienri_v where ruangan_id={$idRuangan} and no_pendaftaran LIKE '%{$term}%'
            and tgl_admisi BETWEEN '{$start}' AND'{$end}'
            group by pendaftaran_id, no_pendaftaran
            order by no_pendaftaran asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataRekamMedik()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $idRuangan = $post['idR'];
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select no_rekam_medik from infopasienri_v where ruangan_id={$idRuangan} and no_rekam_medik LIKE '%{$term}%'
            and tgl_admisi BETWEEN '{$start}' AND'{$end}'
            group by no_rekam_medik
            order by no_rekam_medik asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNamaPasien()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataNamaDokter()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('dokter_admisi')
        ->groupBy(["dokter_admisi"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataKasusPenyakit()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('jeniskasuspenyakit_nama')
        ->groupBy(["jeniskasuspenyakit_nama"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataHakKelas()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('hak_kelas')
        ->groupBy(["hak_kelas"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataKelasSaatIni()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('kelas_pelayanan')
        ->groupBy(["kelas_pelayanan"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataNamaRuangan()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('ruangan_nama')
        ->groupBy(["ruangan_nama"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_pasien# => Untuk Menampilkan Tabel pasien ranap
    * @attribute #periode# => untuk menampilkan periode data
    * @attribute #tanggal# => untuk menampilkan tanggal sekarang
    * @attribute #tanggal_cetak# => untuk menampilkan tanggal cetak
    * @attribute #jenis# => untuk menampilkan title
    * @attribute #cetak_oleh# => untuk menampilkan pencetak
    * @attribute #kepala# => untuk menampilkan nama kepala ruangan
    * @attribute #kepalanip# => untuk menampilkan nip kepala ruangan
    **/

    public function actionExportPdf($jenis,$ruangan){
        $data = [];
        $model = new InfoPasienRanap;
        $jenis_title = 'Rawat Inap';

        $query = $model::find(
            'tgl_admisi','no_rekam_medik',
            'no_pendaftaran','nama_pasien',
            'jenis_kelamin','dokter_admisi',
            'carabayar_nama','penjamin_nama',
            'jeniskasuspenyakit_nama','kelas_pelayanan',
            'hak_kelas','ruangan_nama','kamarruangan_nokamar','no_tempattidur',
            'tgl_pindahkamar','rencana_pulang'
        )->where(['ruangan_id' => $ruangan]);
        $query->andWhere(['not in', 'status_ranap', ['453']]);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // modify advanced filters
        $request = Yii::$app->request;
        $tgl_awal = '';
        $tgl_akhir = '';
        $dataKepala = (PegawaiView::find()->where(['ruangan_id'=>$ruangan, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id'=>$ruangan, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one() : '';
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_admisi_awal']) && isset($advancedFilters['tgl_admisi_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_admisi_awal'];
            $tgl_akhir = $advancedFilters['tgl_admisi_akhir'];
            $query->andWhere(['between', 'tgl_admisi', $tgl_awal, $tgl_akhir]);
        }
        $data = $query->asArray()->all();
        $print = new DocoPrint();

        $print->attributes = [
            '#table_pasien#' => $this->renderPartial('index',[
                'detail' => $data
            ]),
            '#periode#' => $tgl_awal.'-'.$tgl_akhir,
            '#tanggal#' => date('d F Y'),
            '#tanggal_cetak#' => date('d F Y H:i:s'),
            '#jenis#'=>$jenis_title,
            '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
            '#kepala#'=> ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
            '#kepalanip#'=> ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',
        ];

        $print->Output();
    }

    public function actionExportExcel($jenis,$ruangan)
    {
        $data = [];
        $model = new InfoPasienRanap;

        $query = $model::find(
            'tgl_admisi','no_rekam_medik',
            'no_pendaftaran','nama_pasien',
            'jenis_kelamin','dokter_admisi',
            'carabayar_nama','penjamin_nama',
            'jeniskasuspenyakit_nama','kelas_pelayanan',
            'hak_kelas','ruangan_nama','kamarruangan_nokamar','no_tempattidur',
            'tgl_pindahkamar','rencana_pulang'
        )->where(['ruangan_id' => $ruangan]);
        $query->andWhere(['not in', 'status_ranap', ['453']]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // modify advanced filters
        $request = Yii::$app->request;
        $tgl_awal = '';
        $tgl_akhir = '';

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_admisi_awal']) && isset($advancedFilters['tgl_admisi_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_admisi_awal'];
            $tgl_akhir = $advancedFilters['tgl_admisi_akhir'];
            $query->andWhere(['between', 'tgl_admisi', $tgl_awal, $tgl_akhir]);
        }
        $data = $query->asArray()->all();
        $counter = 0;
        foreach ($data as $key => $value) {
            $date1=date_create($value['tgl_admisi']);
            $date2=date_create();
            $diff=date_diff($date1,$date2);
            $dataHariRawat = $diff->d;
            $data_baru[$counter]['Tanggal Admisi'] = $value['tgl_admisi'];
            $data_baru[$counter]['No.RM / No Pendaftaran'] = $value['no_rekam_medik']." / ".$value['no_pendaftaran'];
            $data_baru[$counter]['Nama Pasien'] = $value['nama_pasien'];
            $data_baru[$counter]['Jenis Kelamin'] = $value['jenis_kelamin'];
            $data_baru[$counter]['Dokter DPJP'] = $value['dokter_admisi'];
            $data_baru[$counter]['Cara Bayar / Penjamin'] = $value['carabayar_nama'].' / '.$value['penjamin_nama'];
            $data_baru[$counter]['Hak Kelas / Kelas Saat Ini'] = $value['hak_kelas']." / ".$value['kelas_pelayanan'];
            $data_baru[$counter]['Kasus Penyakit'] = $value['tgl_admisi'];
            $data_baru[$counter]['Nama Ruangan / No.Kamar-No.Bed'] = $value['ruangan_nama']." / ".$value['kamarruangan_nokamar']." - ".$value['no_tempattidur'];
            $data_baru[$counter]['Hari Rawat'] = $dataHariRawat;
            $data_baru[$counter]['Tanggal Pindah'] = $value['tgl_pindahkamar'];
            $data_baru[$counter]['Rencana Pulang'] = $value['rencana_pulang'];
            $counter++;
        }
        $header = ['Periode'=> $tgl_awal . ' - '.$tgl_akhir];
        $filePath = DocoHelpers::exportExcel("Info Pasien Rawat Inap", $data_baru, $header, array(
            "uploadPath" => "./uploads",
        ));

        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    public function actionViewData($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = "")
    {
        $pasienranap = InfoPasienRanap::find();
        if ($id) {
            $pasienranap->where(['pendaftaran_id' => $id]);
        }

        return $pasienranap;
    }

    public function actionGetBundleData($id="")
    {

        $dataranap = $this->getData($id)->asArray()->one();
        return [
            'list-ruangan' => $this->getListRuangan($dataranap['jeniskasuspenyakit_id']),
        ];
    }

    private function getListRuangan($jeniskasuspenyakit_id)
    {
        $data = KasusPenyakitRuangan::find()
        ->where(['jeniskasuspenyakit_id'=>$jeniskasuspenyakit_id])
        ->orderBy('jeniskasuspenyakit_id');
        $items = ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama');

        return $items;
    }

    public function actionUpdateData()
    {
        try {
            $request = Yii::$app->request;

            if ($request->post()) {
                $modelBatalPeriksa = new PasienBatalPeriksa;
                $modelBatalPeriksa->attributes = $request->post();
                $id = $request->post('pasienadmisi_id');

                if ($modelBatalPeriksa->save()){
                    // $modelPendaftaran = Pendaftaran::findOne($id);
                    // $modelPendaftaran['status_periksa'] = 402;
                    // $modelPendaftaran['pasienbatalperiksa_id'] = $modelBatalPeriksa->pasienbatalperiksa_id;
                    $modelPendaftaran = PasienAdmisi::findOne($id);
                    $modelPendaftaran['status_ranap'] = 453;
                    $modelPendaftaran['pasienbatalperiksa_id'] = $modelBatalPeriksa->pasienbatalperiksa_id;
                    
                    //get kamar tempat tidur
                    $kamartempattidur_id = $modelPendaftaran['kamartempattidur_id'];
                    $datakamar = KamarTempatTidur::findOne($kamartempattidur_id);
                    //get kamar ruangan
                    $kamarruangan_id = $datakamar['kamarruangan_id'];
                    $dataruangan = KamarRuangan::findOne($kamarruangan_id);
                    //get kamar ruangan jenis
                    $ruanganjenis = $dataruangan['kamarruangan_jenis'];
                    //$datalookup = Lookup::findOne($ruanganjenis);
                    //set data update ruangan kosong
                    $datakamar['status_isi'] = false;
                    //$datakamar['kettempattidur_id'] = $datalookup['lookup_urutan'];
                    switch ($ruanganjenis) {
                        case '340':
                            $datakamarlama['kettempattidur_id'] = 2;//jenis kamar laki-laki
                            break;
                        case '341':
                            $datakamarlama['kettempattidur_id'] = 1;//jenis kamar perempuan
                            break;
                        default:
                            $datakamarlama['kettempattidur_id'] = 7;//jenis kamar campur
                            break;
                    }
                    //update ruangan kosong 
                    $datakamar->save();
                    //
                    if ($modelPendaftaran->save()) {
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($modelPasienPulang->errors,'PasienBatalPeriksa');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    };
                }else{
                    $errors = DocoHelpers::parseError($modelPasienPulang->errors, 'PasienBatalPeriksa');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDataKamar()
    {
        $request = Yii::$app->request;
        
        $model = new KamarRuanganView;
        $query = $model::find();
        if($request->get('jeniskasuspenyakit_id')){
            $query->andWhere(['jeniskasuspenyakit_id'=>$request->get('jeniskasuspenyakit_id')]);
        }
        if($request->get('kelaspelayanan_id')){
            $query->andWhere(['kelaspelayanan_id'=>$request->get('kelaspelayanan_id')]);
        }
        if($request->get('ruangan_id')){
            $query->andWhere(['ruangan_id'=>$request->get('ruangan_id')]);
        }
        return [
            'data'=>$query->asArray()->all(),
            'list-ruangan' => $query->select(['ruangan_id','ruangan_nama','kamarruangan_nokamar'])->distinct()->orderBy(['ruangan_nama'=>SORT_ASC,'kamarruangan_nokamar'=>SORT_ASC])->all()
        ];
    }

    public function actionProsesPindahKamar()
    { 
        try {
            $request = Yii::$app->request;
            $usernya = Yii::$app->jwt;
            $pegawai_idnya = $usernya->user->pegawai_id;

            if ($request->post()) {
                $dataPost = $request->post();
                $tgl_pindah = date('Y-m-d',strtotime($dataPost['tgl_pindahkamar']));
                $jam_pindah = date('H:i:s',strtotime($dataPost['tgl_pindahkamar']));

                $idAdmisi = $dataPost['pasienadmisi_id'];
                $idPendaftaran = $dataPost['pendaftaran_id'];

                $modelAdmisi = PasienAdmisi::findOne($idAdmisi);
                $kamartempattidur_id_lama = $modelAdmisi['kamartempattidur_id'];
                $kamartempattidur_id_baru = $dataPost['no_tempattidur'];

                $modelAdmisi['kamarruangan_id'] = $dataPost['kamarruangan_nokamar'];
                $modelAdmisi['ruangan_id'] = $dataPost['ruangan_id'];
                $modelAdmisi['kamartempattidur_id'] = $dataPost['no_tempattidur'];
                $modelAdmisi['tgl_pindahkamar'] = $dataPost['tgl_pindahkamar'];
                
                if ($modelAdmisi->save()){
                    //set data dan update ruangan lama kosong
                    $datakamarlama = KamarTempatTidur::findOne($kamartempattidur_id_lama);
                    $kamarruangan_id = $datakamarlama['kamarruangan_id'];
                    $dataruangan = KamarRuangan::findOne($kamarruangan_id);
                    $ruanganjenis = $dataruangan['kamarruangan_jenis'];
                    $datakamarlama['status_isi'] = false;
                    switch ($ruanganjenis) {
                        case '340':
                            $datakamarlama['kettempattidur_id'] = 2;//jenis kamar laki-laki
                            break;
                        case '341':
                            $datakamarlama['kettempattidur_id'] = 1;//jenis kamar perempuan
                            break;
                        default:
                            $datakamarlama['kettempattidur_id'] = 7;//jenis kamar campur
                            break;
                    }
                    if (!$datakamarlama->save()) {
                        $transaction->rollBack();
                        $errors = DocoHelpers::parseError($datakamarlama->errors,'KamarTempatTidur');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                    //
                    //set data dan update ruangan baru terisi
                    $datakamarbaru = KamarTempatTidur::findOne($kamartempattidur_id_baru);
                    $datakamarbaru['status_isi'] = true;
                    $dataJK = strtolower($dataPost['jenis_kelamin']);
                    if ($dataJK=="perempuan") {
                        $datakamarbaru['kettempattidur_id'] = 3;//isi perempuan
                    }else{
                        $datakamarbaru['kettempattidur_id'] = 4;//isi laki-laki
                    }
                    if (!$datakamarbaru->save()) {
                        $transaction->rollBack();
                        $errors = DocoHelpers::parseError($datakamarbaru->errors,'KamarTempatTidur');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                    //
                    //data insert pindahkamar_t
                    $modelPindah = new PindahKamar;
                    $modelPindah->attributes = $request->post();
                    $modelPindah['kamartempattidur_id'] = $dataPost['no_tempattidur'];
                    $modelPindah['kamarruangan_id'] = $dataPost['kamarruangan_nokamar'];
                    $modelPindah['pasien_id'] = $modelAdmisi['pasien_id'];
                    $modelPindah['pegawai_id'] = $pegawai_idnya;
                    $modelPindah['jam_pindahkamar'] = $jam_pindah;
                    
                    if (!$modelPindah->save()) {
                        $transaction->rollBack();
                        $errors = DocoHelpers::parseError($modelPindah->errors,'PindahKamar');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                    //
                    $modelMKupdate = MasukKamar::find()->where(['pasienadmisi_id'=>$idAdmisi])->one();
                    $modelMKupdate['pindahkamar_id'] = $modelPindah['pindahkamar_id'];
                    //update masukkamar_t
                    if (!$modelMKupdate->save()) {
                        $transaction->rollBack();
                        $errors = DocoHelpers::parseError($modelMKupdate->errors,'MasukKamar');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                    //
                    //insert masukkamar_t
                    $modelMKinsert = new MasukKamar;
                    $modelMKinsert->attributes = $request->post();

                    $modelMKinsert['kamartempattidur_id'] = $dataPost['no_tempattidur'];
                    $modelMKinsert['kamarruangan_id'] = $dataPost['kamarruangan_nokamar'];
                    $modelMKinsert['tgl_masukkamar'] = $dataPost['tgl_pindahkamar'];
                    $modelMKinsert['jam_masukkamar'] = $jam_pindah;
                    $modelMKinsert['pegawai_id'] = $pegawai_idnya;

                    if ($modelMKinsert->save()) {
                        $transaction->commit();
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($modelMKinsert->errors,'MasukKamar');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    };
                    //
                }else{
                    $errors = DocoHelpers::parseError($modelAdmisi->errors, 'PasienAdmisi');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {die;
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {die;
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateStatusPeriksa($id)
    {
        $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
        $modelAdmisi = PasienAdmisi::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
        $modelAdmisi['status_ranap'] = 441;
        $modelAdmisi->save();
    }
    
    public function actionCekKamarFleksibel($kamarruangan_id)
    {
        $db = Yii::$app->db;
        $jenis_fleksibel = DocoConstants::VAR_JKF;
        $status_approve = DocoConstants::VAR_STJ;
        $status_dipesan = DocoConstants::VAR_BK;
        $sql = "SELECT jeniskelamin
            FROM infopemesanankamar_v
            WHERE 
                kamarruangan_id = {$kamarruangan_id}
            AND kamarruangan_jenis = {$jenis_fleksibel}
            AND (
                statusbooking = '{$status_approve}' OR statusbooking = '{$status_dipesan}'
            )
        ";
        $data = $db->createCommand($sql)->queryOne();

        return !empty($data) ? $data : false;
    }

}
