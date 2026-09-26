<?php

/**
 * @author Rizal
 * @description backend transaksi pasien inap
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\LapKunjunganRawatInap;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\AsuransiPasien;
use app\modules\v1\models\Rujukan;
use app\modules\v1\models\InfoKunjunganRiView;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\LoginPemakai;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\TindakanPelayanan;
use yii\helpers\ArrayHelper;
use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyncEditsantoyusupR;

class TraPasienRawatInapController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    const PENDAFTARAN_ID = 'pendaftaran_id';
    const RUANGAN_ID = 'ruangan_id';
    const PENJAMIN_ID = 'penjamin_id';
    const NO_PENDAFTARAN = 'no_pendaftaran';
    const ERR_GAGAL_BATAL = 'Pendaftaran tidak bisa dibatalkan.';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET", 'PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }
    
    public function actionView($id) {
        // $data = Pendaftaran::findOne($id);
        $data = InfoKunjunganRiView::find()
        ->where([self::PENDAFTARAN_ID =>$id])
        ->asArray()
        ->one();

        return $data;
    }

    public function actionBatal()
    {
        return Yii::$app->docoPlugin->execute('batal_pendaftaran_ranap');
    }

    public function actionUpdate($id) 
    {
        try {
            $request = Yii::$app->request;
            return $request->post();
            $model = Pendaftaran::findOne($id);
            $modelPasienAdmisi = $this->findAdmisi()
                ->where([self::PENDAFTARAN_ID =>$model->pendaftaran_id])
                ->one();
            $modelRujukan = Rujukan::findOne($model->rujukan_id);

            if ($request->post() && !empty($model)) {
                $post = $request->post();
                if(isset($post['PendaftaranForm'])){
                    $modelRujukan->asalrujukan_id = $post['PendaftaranForm']['asalrujukan_id'];
                    $model->attributes = $post['PendaftaranForm'];
                    $modelPasienAdmisi->attributes = $post['PendaftaranForm'];
                }
                if(isset($post['PasienAdmisiForm'])){
                    $model->attributes = $post['PasienAdmisiForm'];
                    $modelPasienAdmisi->attributes = $post['PasienAdmisiForm'];
                }

                $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                $petugas_id = $jwt->loginpemakai_id;
                $petugas_tgl_pembuat = date('Y-m-d H:i:s');

                $model->petugas_id = $petugas_id;
                $model->petugas_tgl_pembuat = $petugas_tgl_pembuat;

                if ($model->update() 
                    && $modelPasienAdmisi->update() 
                    && $modelRujukan->update()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendaftaranForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new \yii\base\ErrorException("Data Tidak Di Temukan", 500);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetBundleData($id = null)
    {
        return [
            'list-dokter-rajal' => $this->getDokterRajal(),
            'list-penyakit' => $this->getListPenyakit(),
            'list-cara-bayar' => $this->getListCaraBayar(),
            'list-penjamin' => $this->getListPenjamin(),
            'list-asalrujukan' => $this->getListAsalRujukan(),
            'view-asuransi' => $this->getDataAsuransi($id),
            'view-rujukan' => $this->getDataRujukan($id)
        ];
    }

    private function getDokterRajal()
    {
        $data = Pegawai::find()
        ->where(['is_active' => 't'])
        ->orderBy('nama_pegawai');
        $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

        return $items;
    } 

    private function getListPenyakit() 
    {
        $data = JenisKasusPenyakit::find()
        ->where(['is_active' => 't'])
        ->orderBy('jeniskasuspenyakit_nama');
        $items = ArrayHelper::map($data->all(), 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');

        return $items;
    }

    private function getListCaraBayar($default='1') {
        $data = CaraBayar::find()->where(['is_active' => 't'])->orderBy('carabayar_nama');
        if ($default=="1") {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'carabayar_nama');
        } else {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'namaAndSingkatan');
        }

        return $items;
    }

    private function getListPenjamin($carabayar_id=null) {
        $data = Penjamin::find();
        if ($carabayar_id) {
            $data->where(
                [
                    'is_active' => 't',
                    'carabayar_id' => $carabayar_id
                ]
            );
        }
        $data->orderBy('penjamin_nama');

        $items = ArrayHelper::map($data->all(), self::PENJAMIN_ID, 'penjamin_nama');
        return $items;
    }

    private function getListAsalRujukan() 
    {
        $data = AsalRujukan::find()
        ->where(['is_active' => 't'])
        ->orderBy('asalrujukan_nama');
        $items = ArrayHelper::map($data->all(), 'asalrujukan_id', 'asalrujukan_nama');

        return $items;
    }

    private function getDataAsuransi($id)
    {
        $mAsuransi = AsuransiPasien::find()
                    ->where([self::PENDAFTARAN_ID =>$id])
                    ->asArray()
                    ->one();
        return $mAsuransi;
    }
    private function getDataRujukan($id)
    {
        $mPendaftaran = Pendaftaran::findOne($id);
        if(!$mPendaftaran){
            return null;
        }
        $mRujukan = Rujukan::findOne($mPendaftaran->rujukan_id);
        return $mRujukan;
    }

    public function actionGetSelectRujukanDari($id)
    {
        $mPendaftaran = Pendaftaran::findOne($id);
        if(!$mPendaftaran){
            return '';
        }
        $mRujukan = Rujukan::findOne($mPendaftaran->rujukan_id);
        return $mRujukan->rujukandari_id;
    }

    public function actionSaveAsuransi()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $data = $request->post('AsuransiForm');
            $data['diagnosa_id'] = '';

            $model = new AsuransiPasien();
            $model->attributes = $data;
            if(!$model->validate()){
                throw new \yii\db\Exception(json_encode($model->getErrors()),1);
            }
            if(!$model->save()){
                throw new \yii\db\Exception('Terjadi Kesalahan',1);
            }

            $mRujukan = new Rujukan();
            $mRujukan->attributes = $data;
            $mRujukan->asalrujukan_id = isset($data['asalrujukan_id']) ? $data['asalrujukan_id'] : null;
            $mRujukan->rujukandari_id = isset($data['rujukandari_id']) ? $data['rujukandari_id'] : 2;
            $mRujukan->diagnosa_id = isset($data['diagnosa_id']) ? $data['diagnosa_id'] : null;
            $mRujukan->nama_perujuk = isset($data['nama_perujuk']) ? $data['nama_perujuk'] : null;
            $mRujukan->no_rujukan = isset($data['no_rujukan']) ? $data['no_rujukan'] : null;
            $mRujukan->tanggal_rujukan = isset($data['tanggal_rujukan']) ? $data['tanggal_rujukan'] : null;

            if(!$mRujukan->validate()){
                throw new \yii\base\ErrorException("Gagal Validasi Rujukan", 500);      
            }
            if(!$mRujukan->save()){
                throw new \yii\base\ErrorException("Gagal Simpan Rujukan", 500);      
            }

            $mPendaftaran = Pendaftaran::findOne($model->pendaftaran_id);
            if(!$mPendaftaran){
                throw new \yii\base\ErrorException("Gagal get data pendaftaran", 500);      
            }
            $mPendaftaran->rujukan_id = $mRujukan->getPrimaryKey();
            if(!$mPendaftaran->update()){
                throw new \yii\base\ErrorException(json_encode($mPendaftaran->getErrors()), 500);      
            }

            $transaction->commit();
            return [
                    'rujukan_id'=>$mRujukan->getPrimaryKey(),
                    'asuransipasienid'=>$model->getPrimaryKey()];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];

        }
    }

    public function actionSaveRujukan($params){
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if($params == 'rujukan'){
                $post = $request->post();
                $post = $post['RujukanForm'];
                // $save = $this->saveRujukan($post);
                $data = $post;
                $model = new Rujukan();
                $model->attributes = $data;
                // $model->asalrujukan_id = isset($data['asalrujukan_id']) ? $data['asalrujukan_id'] : null;
                // $model->rujukandari_id = isset($data['rujukandari_id']) ? $data['rujukandari_id'] : 2;
                // $model->diagnosa_id = isset($data['diagnosa_id']) ? (int)$data['diagnosa_id'] : null;
                // $model->nama_perujuk = isset($data['nama_perujuk']) ? $data['nama_perujuk'] : null;
                // $model->no_rujukan = isset($data['no_rujukan']) ? $data['no_rujukan'] : null;
                // $model->tanggal_rujukan = isset($data['tanggal_rujukan']) ? $data['tanggal_rujukan'] : null;
                $pendaftaran_id = $data[self::PENDAFTARAN_ID];
                if(!$model->validate()){
                    throw new \yii\db\Exception("Gagal Validasi Rujukan",$model->getErrors(), 500);
                }
                if(!$model->save()){
                    throw new \yii\base\ErrorException("Gagal Simpan Rujukan", 500);
                }

                $mPendaftaran = Pendaftaran::findOne($pendaftaran_id);
                if(!$mPendaftaran){
                    throw new \yii\base\ErrorException("Gagal get data pendaftaran", 500);      
                }
                $mPendaftaran->rujukan_id = $model->getPrimaryKey();
                if(!$mPendaftaran->update()){
                    throw new \yii\base\ErrorException(json_encode($mPendaftaran->getErrors()), 500);      
                }

                $transaction->commit();
                return [
                        'rujukan_id'=>$model->getPrimaryKey()
                    ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakTracerRanap
    * @attribute #norekammedik# => data nomor rekam medik
    * @attribute #noidentitaspasien# => data no_identitas_pasien
    * @attribute #namapasien# => data nama_pasien
    * @attribute #namapanggilan# => data nama_bin
    * @attribute #tempat_lahir# => data tempat_lahir
    * @attribute #tanggal_lahir# => data tanggal_lahir
    * @attribute #umurpasien# => data umur
    * @attribute #jeniskelamin# => data jenis_kelamin
    * @attribute #statusperkawinan# => data status_perkawinan
    * @attribute #namaibu# => data nama_ibu
    * @attribute #alamatpasien# => data alamat_pasien
    * @attribute #rt# => data rt
    * @attribute #rw# => data rw
    * @attribute #propinsi# => data propinsi_nama
    * @attribute #kabupaten# => data kabupaten_nama
    * @attribute #kecamatan# => data kecamatan_nama
    * @attribute #kelurahan# => data kelurahan_nama
    * @attribute #no_mobile_pasien# => data no_mobile_pasien
    * @attribute #no_telepon_pasien# => data no_telepon_pasien
    * @attribute #pekerjaan# => data pekerjaan_nama
    * @attribute #warganegara# => data warganegara
    * @attribute #agama# => data agama_pasien
    * @attribute #tglmasuk_kunjungan# => data tgl_pendaftaran
    * @attribute #kamar_ruangan_kunjungan# => data kamarruangan_nokamar
    * @attribute #notempattidur_kunjungan# => data no_tempattidur
    * @attribute #jeniskasus_kunjungan# => data jeniskasuspenyakit_nama
    * @attribute #kelas_kunjungan# => data kelaspelayanan_nama
    * @attribute #dokter_kunjungan# => data nama_pegawai
    * @attribute #carabayar_kunjungan# => data carabayar_nama
    * @attribute #penjamin_kunjungan# => data penjamin_nama
    * @attribute #keteranganpendaftaran_kunjungan# => data keterangan_pendaftaran
    * @attribute #photopasien# => data photopasien
    **/
    public function actionCetakTracerRanap()
    {
        $request = Yii::$app->request;
        try {
            $id = $request->get('id');
            $modelKunjungan = LapKunjunganRawatInap::find()
                ->where([self::PENDAFTARAN_ID =>$id])
                ->one();
            $modelPasienAdmisi = $this->findAdmisi()
                ->where([self::PENDAFTARAN_ID =>$id])
                ->one();
            $modelPasien = PasienV::find()
                ->where(['pasien_id'=>$modelPasienAdmisi->pasien_id])
                ->one();
            
            $result1 = array_merge($modelPasienAdmisi->attributes,$modelPasien->attributes);
            $result = array_merge($result1,$modelKunjungan->attributes);

            $print = new DocoPrint();
            $print->attributes = [
                '#norekammedik#' => @$result['no_rekam_medik'],
                '#noidentitaspasien#' => @$result['no_identitas_pasien'],
                '#namapasien#' => @$result['nama_pasien'],
                '#namapanggilan#' => @$result['nama_bin'],
                '#tempat_lahir#' => @$result['tempat_lahir'],
                '#tanggal_lahir#' => @$result['tanggal_lahir'],
                '#umurpasien#' => @$result['umur'],
                '#jeniskelamin#' => @$result['jenis_kelamin'],
                '#statusperkawinan#' => @$result['status_perkawinan'],
                '#namaibu#' => @$result['nama_ibu'],
                '#alamatpasien#' => @$result['alamat_pasien'],
                '#rt#' => @$result['rt'],
                '#rw#' => @$result['rw'],
                '#propinsi#' => @$result['propinsi_nama'],
                '#kabupaten#' => @$result['kabupaten_nama'],
                '#kecamatan#' => @$result['kecamatan_nama'],
                '#kelurahan#' => @$result['kelurahan_nama'],
                '#no_mobile_pasien#' => @$result['no_mobile_pasien'],
                '#no_telepon_pasien#' => @$result['no_telepon_pasien'],
                '#pekerjaan#' => @$result['pekerjaan_nama'],
                '#warganegara#' => @$result['warganegara'],
                '#agama#' => @$result['agama_pasien'],
                '#tglmasuk_kunjungan#' => @$result['tgl_pendaftaran'],
                '#kamar_ruangan_kunjungan#' => @$result['kamarruangan_nokamar'],
                '#notempattidur_kunjungan#' => @$result['no_tempattidur'],
                '#jeniskasus_kunjungan#' => @$result['jeniskasuspenyakit_nama'],
                '#kelas_kunjungan#' => @$result['kelaspelayanan_nama'],
                '#dokter_kunjungan#' => @$result['nama_pegawai'],
                '#carabayar_kunjungan#' => @$result['carabayar_nama'],
                '#penjamin_kunjungan#' => @$result['penjamin_nama'],
                '#keteranganpendaftaran_kunjungan#' => @$result['keterangan_pendaftaran'],
                '#photopasien#' => @$result['photopasien']
            ];
            $ret = $print->OutputHtml();

            return $ret;
        } catch(\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function deleteSep($bpjs_id)
    {
        if (!$bpjs_id) return '';

        $model = Bpjs::findOne($bpjs_id);
        $sep = $model->nosep;
        $result = $model->deleteSep($sep);

        return $result;
    }

    private function deleteKarcis($pendaftaran_id)
    {
        if (!$pendaftaran_id) return '';

        $query = "
            UPDATE tindakankomponen_t tk
            SET is_deleted = true, is_active = false
            FROM tindakanpelayanan_t AS tp
            WHERE tk.tindakanpelayanan_id = tp.tindakanpelayanan_id
            AND tp.pendaftaran_id= '$pendaftaran_id'
            ";
        $query_result = Yii::$app->db->createCommand($query)->execute();

        $query = "
            UPDATE tindakanpelayanan_t
            SET is_deleted = true, is_active = false
            WHERE pendaftaran_id = '$pendaftaran_id'
            ";
        $query_result = Yii::$app->db->createCommand($query)->execute();

        return;
    }

    private function findAdmisi()
    {
        return PasienAdmisi::find();
    }

    private function findPasienKeUnitLain()
    {
        return PasienKirimUnitLain::find();
    }

    public function actionUpdateStatus()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $statPeriksa = $post['status_periksa'];
        $jenis = $post['jenis'];

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

        $check = $jwt->katakunci_pemakai;
        $valid = Yii::$app->security->validatePassword($post['password'], $check);
        if (!$valid) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Password salah.'
            ]);
        }

        $model = Pendaftaran::find()->andWhere([
            self::NO_PENDAFTARAN => $post['no_pendaftaran']
        ])->one();
        
        $model_admisi = PasienAdmisi::find()->andWhere([
            'pendaftaran_id' => $model->pendaftaran_id
        ])->one();

        $statPeriksaold =  $model->status_periksa;
        if (!empty($model)) {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $petugas_id = $jwt->loginpemakai_id;
                $petugas_tgl_pembuat = date('Y-m-d H:i:s');

                $model->status_periksa = $statPeriksa;
                $model->petugas_id = $petugas_id;
                $model->petugas_tgl_pembuat = $petugas_tgl_pembuat;

                $model_admisi->status_ranap = $statPeriksa;
                if ($statPeriksa == DocoConstants::STATUS_RANAP_PULANG) {
                    $model_admisi->tgl_pulang = $petugas_tgl_pembuat;
                }
                    
                if ($model->save() && $model_admisi->save()) {
                    $transaction->commit();

                    // switch($jenis){
                    //     case 'ranap':
                    //         $route = 'app/update-status-kunjungan-ranap';
                    //         break;
                    // }

                    // $idPendaftaran = $model->pendaftaran_id;
                    // $params['route'] = $route;
                    // $params['data'] = $this->syncPendaftaran($idPendaftaran);
                    // // return $params['data'];
                    // (new PendaftaranService)->syncPendaftaranSty($params);

                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                        'text' => 'Status Kunjungan berhasil diubah.'
                    ]);
                } else {
                    $transaction->rollBack();
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
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
        throw new \Exception("Data Tidak Di Temukan");
    }

    private function syncPendaftaran($idPendaftaran)
    {
        $result = $asuransi = $keluarga = $penanggung = $pasien = $penjamin = $penanggungJawab = [];
        $pdftrn = SyPendaftaranView::find()
        ->where(['pendaftaran_id' => $idPendaftaran])
        ->asArray()
        ->one();

        if(!empty($pdftrn)) {
            $add = json_decode($pdftrn['additional_data'], true);

            $pasien = SyPasienView::find()
            ->where(['pasien_id' => $pdftrn['pasien_id']])
            ->asArray()
            ->one();

            if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                $umurExp = explode(" ",$pdftrn['umur']);
                $pdftrn['umur_hari'] = $umurExp[4];
                $pdftrn['umur_bulan'] = $umurExp[2];
                $pdftrn['umur_tahun'] = $umurExp[0];
            }

            if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                $additionalPasien = json_decode($pasien['additional_pasien']);
                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                    if(isset($ktp)) {
                        $pasien['nik'] = $ktp;
                    }
                }
            }
        }

        $result = [
            'pendaftaran' => $pdftrn,
            'pasien' => $pasien,
        ];

        $modelSync = new SyncEditsantoyusupR;
        $modelSync->pendaftaran_id = $pdftrn['pendaftaran_id'];
        $modelSync->pasien_id = $pdftrn['pasien_id'];
        $modelSync->created_date = date("Y-m-d H:i:s");
        $modelSync->count_sync = 0;
        $modelSync->payload = json_encode($result);
        $modelSync->state = 'update status';
        $modelSync->save(false);

        return $result;
    }

}