<?php

namespace Doco\Traits;

use Doco\models\KegiatanKeperawatan;
use Doco\models\NursingNote;
use Doco\models\JenisKasusPenyakit;
use Doco\models\KelasPelayanan;
use Doco\models\Penjamin;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\models\InfoDokterView;
use Doco\models\Pegawai;
use Doco\models\Ruangan;
use Doco\models\WorklistPasien;
use Doco\models\Lookup;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\models\AsesmenMedis;
use Doco\models\AsesmenMedisRD;
use Doco\models\Cppt;
use Doco\models\GenerateNoSuratKeteranganFn;
use Doco\models\PemeriksaanFisik;
use Doco\models\Pendaftaran;
use Doco\models\SoapRj;
use yii\data\ActiveDataProvider;
use Doco\models\SuratKeterangan;
use Doco\models\SuratKeteranganPasien;
use Doco\models\TindakanPelayanan;

/**
 * Trait of Nursing Note
 */
trait SuratKeteranganTrait
{
    public function actionGetMasterSurat() {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            
            $model = new SuratKeterangan;
            $query = $model::find();
            $query->select([
                'surat_keterangan_m.surat_keterangan_id', 
                'surat_keterangan_m.judul_surat', 
                'surat_keterangan_m.urutan',
                'skpt.pendaftaran_id', 
                'surat_keterangan_m.code_report',
                'skpt.is_eklaim AS is_eklaim_skpt',
            ]);

            $query->leftJoin("(
                select * from surat_keterangan_pasien_t 
                where pendaftaran_id = ".$pendaftaran_id."
            ) skpt", "skpt.surat_keterangan_id = surat_keterangan_m.surat_keterangan_id");
            $query->orderBy(['urutan' => SORT_ASC]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage());
        }
    }

    public function actionGetTemplateSurat() {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $surat_keterangan_id = $request->get('surat_keterangan_id');
            $suratKeteranganPasien = SuratKeteranganPasien::find()
                ->where([
                    'pendaftaran_id' => $pendaftaran_id, 
                    'surat_keterangan_id' => $surat_keterangan_id
                ])->asArray()->one();
            $content = SuratKeterangan::find()->where(['surat_keterangan_id' => $surat_keterangan_id])->asArray()->one();

            return [
                'data' => $suratKeteranganPasien,
                'content' => $content,
                'no_surat' => !empty($suratKeteranganPasien['no_surat']) ? $suratKeteranganPasien['no_surat'] : $this->generateNoSuratKeterangan($surat_keterangan_id)
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 422;
            return [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSaveSuratKeterangan() {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new SuratKeteranganPasien;
            if(!empty($post['surat_keterangan_pasien_id'])) {
                $model = $model::find()->where(['pendaftaran_id' => $post['pendaftaran_id'], 'surat_keterangan_id' => $post['surat_keterangan_id']])->one();
            }
            $model->attributes = $post;

            if(isset($post['pegawai_id']) && empty($post['pegawai_id'])) { // enable form pegawai
                $model->nama_pegawai = $post['nama_pegawai'];
                $model->nip_pegawai = $post['nip_pegawai'];
                $model->jabatan_pegawai = $post['jabatan_pegawai'];
            } else { // pegawai tanpa form
                $pegawai = Pegawai::find()
                    ->select(["pegawai_id", "nomorindukpegawai", "lm.lookup_name as gelardepan", "nama_pegawai", "gm.gelarbelakang_nama as gelarbelakang", "jm.jabatan_nama"])
                    ->leftJoin("(select gelarbelakang_id, gelarbelakang_nama from gelarbelakang_m) gm", "gm.gelarbelakang_id::INTEGER = pegawai_m.gelarbelakang::INTEGER")
                    ->leftJoin("(select lookup_id, lookup_type, lookup_name from lookup_m) lm", "lm.lookup_type = 'gelar_depan' and lm.lookup_id::INTEGER = pegawai_m.gelardepan::INTEGER")
                    ->leftJoin("(select jabatan_id, jabatan_nama from jabatan_m) jm", "jm.jabatan_id = pegawai_m.jabatan_id")
                    ->where(["pegawai_m.pegawai_id" => $post['pegawai_id']])
                    ->asArray()->one();

                $nama_pegawai = $pegawai['gelardepan'] . " " . $pegawai['nama_pegawai'] . " " . $pegawai['gelarbelakang'];
                $model->nama_pegawai = $nama_pegawai;
                $model->nip_pegawai = !is_null($pegawai['nomorindukpegawai']) ? $pegawai['nomorindukpegawai'] : "-";
                $model->jabatan_pegawai = !is_null($pegawai['jabatan_nama']) ? $pegawai['jabatan_nama'] : "-";
            }

            $model->tgl_lahir = date('Y-m-d', strtotime($post['tgl_lahir']));
            $model->additional_data = !empty($post['additional_data']) ? json_encode($post['additional_data']) : null;
            $model->no_surat = !empty($post['surat_keterangan_pasien_id']) ? $model->no_surat : $this->generateNoSuratKeterangan($post['surat_keterangan_id']);
            
            if(!$model->save()) {
                $errors = DocoHelpers::parseError($model->errors, 'SuratKeteranganPasienForm');
                \Yii::$app->response->statusCode = 500;
                return $errors;   
            }

            return $this->responseJson(200, 'Surat keterangan berhasil disimpan');
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage(), [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function actionGetDataPasien() {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $pasien = Pendaftaran::find()
                ->select([
                    'pendaftaran_id',
                    'pm.nama_pasien', 
                    'pm.tempat_lahir', 
                    'pm.tanggal_lahir as tgl_lahir', 
                    'pekerjaan.pekerjaan_nama as pekerjaan', 
                    'jk.lookup_name as jenis_kelamin', 
                    'pm.alamat_pasien as alamat'
                ])
                ->leftJoin('(
                    select pasien_id, namadepan, nama_pasien, tempat_lahir, tanggal_lahir, alamat_pasien, pekerjaan_id, jeniskelamin
                    from pasien_m
                ) pm', 'pm.pasien_id = pendaftaran_t.pasien_id')
                ->leftJoin('(
                    select pekerjaan_id, pekerjaan_nama
                    from pekerjaan_m
                ) pekerjaan', 'pekerjaan.pekerjaan_id = pm.pekerjaan_id')
                ->leftJoin('(
                    select lookup_id, lookup_name
                    from lookup_m
                ) jk', 'jk.lookup_id::INTEGER = pm.jeniskelamin::INTEGER')
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            return $pasien;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDokterById() {
        try {
            return Pegawai::find()
                ->select(["pegawai_id", "nomorindukpegawai as nip_pegawai", "lm.lookup_name as gelardepan", "nama_pegawai", "gm.gelarbelakang_nama as gelarbelakang", "jm.jabatan_nama as jabatan_pegawai"])
                ->leftJoin("(select gelarbelakang_id, gelarbelakang_nama from gelarbelakang_m) gm", "gm.gelarbelakang_id::INTEGER = pegawai_m.gelarbelakang::INTEGER")
                ->leftJoin("(select lookup_id, lookup_type, lookup_name from lookup_m) lm", "lm.lookup_type = 'gelar_depan' and lm.lookup_id::INTEGER = pegawai_m.gelardepan::INTEGER")
                ->leftJoin("(select jabatan_id, jabatan_nama from jabatan_m) jm", "jm.jabatan_id = pegawai_m.jabatan_id")
                ->where(['pegawai_id' => Yii::$app->request->get('pegawai_id')])
                ->asArray()->one();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 422;
            return [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function generateNoSuratKeterangan($surat_keterangan_id = null) {
        if(empty($surat_keterangan_id)) throw new \Exception("Surat Keterangan ID tidak boleh kosong", 1);
        $no_surat = GenerateNoSuratKeteranganFn::generate($surat_keterangan_id);
        return isset($no_surat) ? current($no_surat) : null;
    }

    public function actionGetCustomDataSuratKeterangan()
    {
        try {
            $request = Yii::$app->request;
            $session = Yii::$app->session; 
            $suratCustom = [14, 19];

            $pendaftaran_id = $request->get('pendaftaran_id');
            $surat_keterangan_id = $request->get("surat_keterangan_id");
            $modul = $request->get("modul");

            if(in_array($surat_keterangan_id, $suratCustom)) {
                if($modul == 'rajal') {
                    $asemenData = PemeriksaanFisik::find()->select([
                        'keadaanumum as keadaan_umum',
                        'tinggibadan_cm as tinggi_badan',
                        'beratbadan_kg as berat_badan',
                        'suhutubuh as suhu_tubuh',
                        'tekanandarah as tekanan_darah',
                        'detaknadi as nadi',
                        'pernapasan as pernapasan',
                        'created_date',
                        'last_modified_date'
                    ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();   
                }

                if($modul == 'ranap') {
                    $asemenData = AsesmenMedis::find()->select([
                        'keluhan_utama as keadaan_umum', 
                        'tinggi_badan',
                        'berat_badan', 
                        "CONCAT(td_systolic,'/',td_diastolic) as tekanan_darah",
                        'suhu_tubuh',
                        'detak_nadi as nadi',
                        'pernapasan as pernapasan',
                        'created_date',
                        'last_modified_date'
                    ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();    
                }

                if($modul == 'igd') {
                    $asemenData = AsesmenMedisRD::find()->select([
                        'keluhan_utama as keadaan_umum', 
                        'tinggi_badan',
                        'berat_badan', 
                        'tekanandarah as tekanan_darah',
                        'nadi',
                        'suhu as suhu_tubuh',
                        'pernapasan as pernapasan',
                        'created_date',
                        'last_modified_date'
                    ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();    
                }
                
                $dataTerapi = self::getDataTerapi($pendaftaran_id);
                $dataTindakan = self::getDataTindakan($pendaftaran_id);
                $dataCppt = self::getCppt($pendaftaran_id, $modul);

                $tmpData = array(
                    "asesmen_medis" => $asemenData,
                    "terapi" => $dataTerapi,
                    "tindakan" => $dataTindakan,
                    "diagnosa" => $dataCppt
                );

                return self::responseCustom(200, $tmpData);
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 422;
            $errorData =  [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ];

            return self::responseCustom(422, $errorData);
        }
    }

    private static function responseCustom($status = 200, $data = [])
    {
        return [
            'status' => $status,
            'data' => $data
        ];
    }

    private static function getDataTerapi($pendaftaran_id)
    {
        $str = '';
        $list = '<li><b>Terapi</b></li>';
        $pemeriksaanLab = Yii::$app->db->createCommand("
            SELECT * 
            FROM infopemeriksaanlab_v 
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->queryAll();
        
        if(! empty($pemeriksaanLab)) {
            foreach ($pemeriksaanLab as $key => $value) {
                $str .= $value['pemeriksaanlab_nama']."\n";
                $list .= '<li>'.$value['pemeriksaanlab_nama'].'</li>';
            }
        }

        $pemeriksaanRad = Yii::$app->db->createCommand("
            SELECT * 
            FROM infopemeriksaanrad_v 
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->queryAll();

        if(! empty($pemeriksaanRad)) {
            foreach ($pemeriksaanRad as $key => $value) {
                $str .= $value['pemeriksaanrad_nama']."\n";
                $list .= '<li>'.$value['pemeriksaanrad_nama'].'</li>';
            }
        }

        return [
            'jumlah_tindakan' => count($pemeriksaanLab) + count($pemeriksaanRad),
            'text' => $str,
            'listing' => $list
        ];
    }

    private static function getDataTindakan($pendaftaran_id)
    {
        $str = '';
        $list = '<li><b>Tindakan</b></li>';

        $dataTindakan = Yii::$app->db->createCommand("
            SELECT 
                daftartindakan_nama 
            FROM daftartindakan_v WHERE daftartindakan_id IN (
                SELECT 
                    daftartindakan_id 
                FROM tindakanpelayanan_t 
                WHERE pendaftaran_id = {$pendaftaran_id} 
                AND instalasi_id NOT IN (4,5)
                AND is_deleted = false
                AND is_active = true
            )
        ")->queryAll();

        if(! empty($dataTindakan)) {
            foreach ($dataTindakan as $key => $value) {
                $str .= $value['daftartindakan_nama']."\n";
                $list.= '<li>'.$value['daftartindakan_nama'].'</li>';
            }
        }

        return [
            'jumlah_tindakan' => count($dataTindakan),
            'text' => $str,
            'listing' => $list
        ];
    }

    private static function getCppt($pendaftaran_id, $modul)
    {
        $diagnosaUtama = null;
        $createdDate = null;

        if($modul == 'rajal') {
            $cppt = SoapRj::find()->select([
                'a_diag_utama',
                'created_date'
            ])->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy([
                'soaprj_id' => SORT_DESC
            ])
            ->asArray()
            ->one();
        } else {
            $cppt = Cppt::find()->select([
                'a_diag_utama',
                'created_date'
            ])->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy([
                'cppt_id' => SORT_DESC
            ])
            ->asArray()
            ->one();
        }

        if(! empty($cppt)) {
            $diagnosaUtama = json_decode($cppt['a_diag_utama']);
            $createdDate = ArrayHelper::getValue($cppt, 'created_date');
        }

        return [
            'diagnosa_utama' => $diagnosaUtama,
            'diagnosa_created_date' => $createdDate
        ];
    }

    public function actionUpdateStatusEklaim()
    {
        $request = Yii::$app->request;
        try {
            $pendaftaranId = $request->post("pendaftaran_id");
            $suratKeteranganId = $request->post("surat_keterangan_id");
            $statusEklaim = $request->post("status_eklaim");
            $model = SuratKeteranganPasien::find()
                ->where([
                    'pendaftaran_id' => $pendaftaranId,
                    'surat_keterangan_id' => $suratKeteranganId,
                ])
                ->one();

            if(! empty($model)) {
                $model->is_eklaim = $statusEklaim;
                $model->save();

                Yii::$app->response->statusCode = 200;
                return self::responseCustom(200, [
                    'message' => "Update Status Eklaim Berhasil",
                ]);
            }

            Yii::$app->response->statusCode = 422;
            return self::responseCustom(422, [
                'message' => "Data Tidak Ditemukan !",
            ]);
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 422;
            $errorData =  [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ];

            return self::responseCustom(422, $errorData);
        }
    }
}
