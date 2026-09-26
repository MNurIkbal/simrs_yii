<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\Instruksi;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\InstruksiTindakanBmhp;
use app\modules\v1\models\PermintaanKonsul;
use app\modules\v1\models\PermintaanKonsulView;
use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\InfoPasienRiView;

class PemeriksaanPermintaanKonsulController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PermintaanKonsulView';

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

    private function getData()
    {
        $result = PermintaanKonsulView::find();
        return $result;
    }
    
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $preview_only = $request->post('preview_only', false);

            $model = new PermintaanKonsulView;
            $query = $model::find()->orderBy(['waktu_permintaan'=>SORT_ASC])
                                    ->andWhere(['pendaftaran_id'=>$post['pendaftaran_id'] ]);
            if($preview_only == true) {
                $query = $query->andWhere(['NOT', ['status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL]]);
            }
            // $query = DocoRestActiveFilter::advancedFilter($model, $query);
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

    public function actionDataPasienKonsul()
    {
        try {
            $request = Yii::$app->request;
            $permintaankonsul_id = $request->get('permintaankonsul_id');
            $preview_only = $request->get('preview_only', false);

            $model = new PermintaanKonsulView;
            $query = $model::find()
                ->andWhere(['permintaankonsul_id'=>$permintaankonsul_id ]);
            if($preview_only == true) {
                $query = $query->andWhere(['NOT', ['status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL]]);
            }
            $query = $query->asArray()->one();
            
            return $query;
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

    public function actionUpdatePermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $getData = $request->post('PermintaanKonsulForm');
            $model = PermintaanKonsul::findOne($getData['permintaankonsul_id']);
            $data = $request->post('PermintaanKonsulForm');
            $model->waktu_permintaan = $data['waktu_permintaan'];
            // $model->dokter_id = $data['dokter_id'];
            // $model->jenis_konsul = $data['jenis_konsul'];
            $model->ket_konsul = $data['ket_konsul'];
            // return json_encode($model->attributes);
            if ($request->post() && !empty($model)) {
                if ($model->status_konsul == DocoConstants::STATUS_PERMINTAAN_KONSUL_DEFAULT) {
                    if ($model->save()) {
                        return [
                            'message' => 'Data Berhasil di ubah',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'UpdatePermintaanKonsul');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'UpdatePermintaanKonsul');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function getPasienId($pendaftaranId)
    {
        return ArrayHelper::getValue(Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaranId ])->one(), 'pasien_id');
    }

    private function getPegawaiId($getUser)
    {
        return ArrayHelper::getValue(PegawaiView::find()->where(['pegawai_id' => $getUser->pegawai_id ])->one(), 'pegawai_id');
    }

    private function getRuangan($pendaftaranId)
    {
        return ArrayHelper::getValue(InfoPasienRiView::find()->select([
            "CONCAT(
                CONCAT(
                    CONCAT(
                        CONCAT(
                            CONCAT(
                                ruangan_id, '@#', kamarruangan_id),
                                '@#', kamartempattidur_id),
                                '@#', kamarruangan_nokamar),
                                ' | ', kamarruangan_nokamar),
                                '-', no_tempattidur)"])
        ->where(['pendaftaran_id' => $pendaftaranId])->asArray()->one(), 'concat');
    }

    public function actionCreatePermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $model = new PermintaanKonsul;
            $post = $request->post();
            $ruangan = $this->getRuangan($post['pendaftaran_id']);
            $pasienId = $this->getPasienId($post['pendaftaran_id']);
            $getUser = Yii::$app->jwt->user;
            $pegawaiId = $this->getPegawaiId($getUser);
            if(empty($ruangan) || empty($pasienId) || empty($pegawaiId)){
                throw new Exception("Data Tidak Di Temukan");
            }
            
            // generate cppt
            if ($post['cppt_id'] == '0' || empty($post['cppt_id'])) {
                $getCppt = AllowController::actionCreateSoap($ruangan, $post['pendaftaran_id'], $pegawaiId, $pasienId, $post['pasienadmisi_id']);
                $post['cppt_id'] = $getCppt;
            }

            $model->attributes = $post;
            if(isset($model->pasienadmisi_id)){
                $mAdmisi = PasienAdmisi::findOne($model->pasienadmisi_id);
                if($mAdmisi){
                    $model->dokterdpjpasal_id = $mAdmisi->pegawai_id;
                }
            }
            if($model->validate()){
                if ($post) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'PermintaanKonsulForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }else{
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422,$response);
            }

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

    /**
    * @controller actionCetakPermintaanPdf 
    * @attribute #tgl_permintaan# => Tanggal Permintaan 
    * @attribute #nama_pegawai# => Nama Pegawai 
    * @attribute #dok_is_konsul# => Dokter Konsul 
    * @attribute #tgl_cetak# => Tanggal Cetak
    * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
    * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
    * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #inf_umur# => Informasi Pasien: Umur
    * @attribute #inf_ruangankelas# => Informasi Pasien: Ruangan Kelas
    * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter DPJP
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #permintaan_dokterdikonsul# => Permintaan: Dokter di Konsul
    * @attribute #permintaan_jeniskonsul# => Permintaan: Jenis Konsul
    * @attribute #permintaan_waktupermintaan# => Permintaan: Waktu Permintaan
    * @attribute #permintaan_ketkonsul# => Permintaan: Keterangan Konsul
    **/
    public function actionCetakPermintaanPdf()
    {

        $request = Yii::$app->request;

        $modelPermintaanKonsul = new PermintaanKonsul;
        $queryHeaderfirst = $modelPermintaanKonsul::find()
            ->Where([
                'permintaankonsul_id'=>$request->post('permintaankonsul_id'),
            ]);
        $resultPermintaanKonsul = $queryHeaderfirst->asArray()->one();

        $modelHeader = new InfoPasienRanap;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id'=>$resultPermintaanKonsul['pendaftaran_id'],
                'pasienadmisi_id'=>$resultPermintaanKonsul['pasienadmisi_id'],
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $modelFormPermintaan = new PermintaanKonsulView;
        $queryFormPermintaan = $modelFormPermintaan::find()
            ->Where([
                'permintaankonsul_id'=>$request->post('permintaankonsul_id'),
            ]);
        $resultFormPermintaan = $queryFormPermintaan->asArray()->one();

        $getUser = Yii::$app->jwt->user;
        $pegawai = PegawaiView::find()->where(['pegawai_id'=>$getUser->pegawai_id ])->one();

         // Directory Creation
        $header1 = array(
            Yii::t('app', "Nama pasien") => $resultHeader ? $resultHeader['nama_pasien'] : '',
            Yii::t('app', "No rekam medik") => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            Yii::t('app', "Tanggal lahir") => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            Yii::t('app', "Jenis kelamin") => $resultHeader ? $resultHeader['jenis_kelamin'] : ''
        );

        $header2 = array(
            Yii::t('app', "Umur") => $resultHeader ? $resultHeader['umur'] : '',
            Yii::t('app', "Ruangan / kelas") => $resultHeader ? $resultHeader['ruangan_nama'] . ' / ' . $resultHeader['kelas_pelayanan'] : '',
            Yii::t('app', "Dokter DPJP") => $resultHeader ? $resultHeader['dokter_admisi'] : '',
            Yii::t('app', "Penjamin") => $resultHeader ? $resultHeader['penjamin_nama'] : '',
        );

        $formPermintaan = array(
            Yii::t('app', "Dokter yang di konsul") => $resultFormPermintaan ? $resultFormPermintaan['dok_konsul'] : '',
            Yii::t('app', "Jenis konsul") => $resultFormPermintaan ? $resultFormPermintaan['jenis_konsul_nama'] : '',
            Yii::t('app', "Waktu permintaan") => $resultFormPermintaan ? $resultFormPermintaan['waktu_permintaan'] : '',
            Yii::t('app', "Permintaan konsultasi") => $resultFormPermintaan ? $resultFormPermintaan['ket_konsul'] : '',
        );

        $print = new DocoPrint();
        $print->attributes = [
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_ruangankelas#' => $resultHeader ? $resultHeader['ruangan_nama'] . ' / ' . $resultHeader['kelas_pelayanan'] : '',
            '#inf_dokterdpjp#' => $resultHeader ? $resultHeader['admisi_dokter'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#permintaan_dokterdikonsul#' => $resultFormPermintaan ? $resultFormPermintaan['dok_konsul'] : '',
            '#permintaan_jeniskonsul#' => $resultFormPermintaan ? $resultFormPermintaan['jenis_konsul_nama'] : '',
            '#permintaan_waktupermintaan#' => $resultFormPermintaan ? $resultFormPermintaan['waktu_permintaan'] : '',
            '#permintaan_ketkonsul#' => $resultFormPermintaan ? $resultFormPermintaan['ket_konsul'] : '',
            // '#permintaan_konsul#' => $this->renderPartial('permintaan_pdf',['header1'=>$header1, 'header2'=>$header2, 'formPermintaan'=>$formPermintaan]),
            '#tgl_permintaan#' => $resultPermintaanKonsul ? date('d F Y', strtotime($resultPermintaanKonsul['created_date'])) : '',
            '#nama_pegawai#' => $pegawai ? $pegawai->nama_pegawai : '',
            '#dok_is_konsul#' => $resultFormPermintaan ? $resultFormPermintaan['dok_dpjp'] : '',
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        $print->Output();
    }

    /**
    * @controller actionCetakJawabanPdf  
    * @attribute #tgl_permintaan# => Tanggal Permintaan
    * @attribute #nama_pegawai# => Nama Pegawai 
    * @attribute #dok_is_konsul# => Dokter Konsul 
    * @attribute #tgl_cetak# => Tanggal Cetak
    * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
    * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
    * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #inf_umur# => Informasi Pasien: Umur
    * @attribute #inf_ruangankelas# => Informasi Pasien: Ruangan Kelas
    * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter DPJP
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #jawaban_jeniskonsul# => Jawaban: Jenis Konsul
    * @attribute #jawaban_waktupermintaan# => Jawaban: Waktu Permintaan
    * @attribute #jawaban_waktupersetujuan# => Jawaban: Waktu Persetujuan
    * @attribute #jawaban_rekomendasidokterkonsulen# => Jawaban: Rekomendasi Dokter Konsulen
    **/
    public function actionCetakJawabanPdf()
    {

        $request = Yii::$app->request;

        $modelPermintaanKonsul = new PermintaanKonsul;
        $queryHeaderfirst = $modelPermintaanKonsul::find()
            ->Where([
                'permintaankonsul_id'=>$request->post('permintaankonsul_id'),
            ]);
        $resultPermintaanKonsul = $queryHeaderfirst->asArray()->one();

        $modelHeader = new InfoPasienRanap;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id'=>$resultPermintaanKonsul['pendaftaran_id'],
                'pasienadmisi_id'=>$resultPermintaanKonsul['pasienadmisi_id'],
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $modelFormPermintaan = new PermintaanKonsulView;
        $queryFormPermintaan = $modelFormPermintaan::find()
            ->Where([
                'permintaankonsul_id'=>$request->post('permintaankonsul_id'),
            ]);
        $resultFormPermintaan = $queryFormPermintaan->asArray()->one();

        $getUser = Yii::$app->jwt->user;
        $pegawai = PegawaiView::find()->where(['pegawai_id'=>$getUser->pegawai_id ])->one();

         // Directory Creation
        $header1 = array(
            Yii::t('app', "Nama pasien") => $resultHeader ? $resultHeader['nama_pasien'] : '',
            Yii::t('app', "No rekam medik") => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            Yii::t('app', "Tanggal lahir") => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            Yii::t('app', "Jenis kelamin") => $resultHeader ? $resultHeader['jenis_kelamin'] : ''
        );

        $header2 = array(
            Yii::t('app', "Umur") => $resultHeader ? $resultHeader['umur'] : '',
            Yii::t('app', "Ruangan / kelas") => $resultHeader ? $resultHeader['ruangan_nama'] . ' / ' . $resultHeader['kelas_pelayanan'] : '',
            Yii::t('app', "Dokter DPJP") => $resultHeader ? $resultHeader['dokter_admisi'] : '',
            Yii::t('app', "Penjamin") => $resultHeader ? $resultHeader['penjamin_nama'] : '',
        );

        $formJawaban = array(
            Yii::t('app', "Jenis konsul") => $resultFormPermintaan ? $resultFormPermintaan['jenis_konsul_nama'] : '',
            Yii::t('app', "Waktu permintaan") => $resultFormPermintaan ? $resultFormPermintaan['waktu_permintaan'] : '',
            Yii::t('app', "Waktu Persetujuan") => $resultPermintaanKonsul ? $resultPermintaanKonsul['waktu_persetujuan'] : '',
            Yii::t('app', "Penemuan dan rekomendasi dokter konsulen") => $resultFormPermintaan ? $resultFormPermintaan['jawaban_konsul'] : '',
        );

        $print = new DocoPrint();
        $print->attributes = [
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_ruangankelas#' => $resultHeader ? $resultHeader['ruangan_nama'] . ' / ' . $resultHeader['kelas_pelayanan'] : '',
            '#inf_dokterdpjp#' => $resultHeader ? $resultHeader['dokter_admisi'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#jawaban_jeniskonsul#' => $resultFormPermintaan ? $resultFormPermintaan['jenis_konsul_nama'] : '',
            '#jawaban_waktupermintaan#' => $resultFormPermintaan ? $resultFormPermintaan['waktu_permintaan'] : '',
            '#jawaban_waktupersetujuan#' => $resultPermintaanKonsul ? $resultPermintaanKonsul['waktu_persetujuan'] : '',
            '#jawaban_rekomendasidokterkonsulen#' => $resultFormPermintaan ? $resultFormPermintaan['jawaban_konsul'] : '',
            // '#jawaban_konsultasi#' => $this->renderPartial('jawaban_pdf',['header1'=>$header1, 'header2'=>$header2, 'formJawaban'=>$formJawaban]),
            '#tgl_permintaan#' => $resultPermintaanKonsul ? date('d F Y', strtotime($resultPermintaanKonsul['created_date'])) : '',
            '#nama_pegawai#' => $pegawai ? $pegawai->nama_pegawai : '',
            '#dok_is_konsul#' => $resultFormPermintaan ? $resultFormPermintaan['dok_konsul'] : '',
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        $print->Output();
    }

    public function actionBatalPermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $model = PermintaanKonsul::findOne($request->post('permintaankonsul_id'));
            if (!empty($model)) {
                $model->status_konsul = DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL;
                if ($model->save(false)) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'BatalPermintaanKonsul');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function getLookupKeperawatanByType($type = null)
    {
        $result = LookupKeperawatan::find();

        if ($type){
            $result->where(['lookup_type' => $type]);
        }

        return $result;
    }

    public function actionViewDiagnosisMasuk()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $result = Diagnosa::find();

        $result->select(['diagnosa_id','diagnosa_kode','CONCAT(diagnosa_kode,\' - \',diagnosa_nama) as diagnosa_nama']);
        if(!empty($post['keyword'])){
            $term = $post['keyword'];
            $result->andWhere(['like', 'LOWER(diagnosa_kode)', $term]);
            $result->orWhere(['like', 'LOWER(diagnosa_nama)', $term]);
        }
        return $result->asArray()->all();
    }

    public function actionGetlistdata()
    {
        $request = Yii::$app->request;
        $permintaankonsul_id = $request->get('permintaankonsul_id');
        $model = PermintaanKonsul::find()->where(['permintaankonsul_id'=>$permintaankonsul_id])->one();
        if(empty($model)) return null;

        return $model;
    } 

    public function getDataAsesmenAwal($pendaftaran_id, $pasienadmisi_id)
    {
        $model = AsesmenAwalT::find()->where(['pendaftaran_id'=>$pendaftaran_id,'pasienadmisi_id'=>$pasienadmisi_id])->one();
        if(empty($model)) return null;

        return $model;
    }
}
