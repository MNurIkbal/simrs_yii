<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-28 17:42:45
 */

namespace app\modules\v1\controllers;

use Doco\models\PegawaiView;
use Doco\Services\Cache;
use Yii;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\JadwalBukaPoliView;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\InfoPendaftaranOnlineView;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\ReservasiPoliklinik;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\Pasien;
use app\modules\v1\payload\UploadPayload;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstansId;
use Doco\Services\InternalService;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class ReservasiPoliklinikController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\ReservasiPoliklinik';

    /**
     * @todo Verbs function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    /**
     * @todo Actions function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @todo Fungsi untuk mendapatkan bundle data reservasi poliklinik
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetBundleData()
    {
        $caraBayar = CaraBayar::find()->where([
            'is_active' => true,
            'is_deleted' => false
        ])->all();

        $konfigSystem = KonfigSystem::find()->one();
        return [
            'caraBayar' => $caraBayar,
            'konfigSystem' => $konfigSystem,
        ];
    }

    /**
     * @todo Fungsi untuk mendapatkan data pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPasien()
    {
        $request = Yii::$app->request;
        $q = $request->get('q', null);
        $id = $request->get('id', null);
        $model = PasienV::find();
        $search_type = $request->get('search_type', []);
        $is_search_by_type = !empty($search_type) ? true : false; // condition for where query if fe pass search by type. *check search by type simple, need to improve

        if ($q) {
            if (in_array('no_rekam_medik', $search_type) || !$is_search_by_type) {
                $model->orWhere(['ilike', 'no_rekam_medik', $q]);
            }
            if (in_array('nama_pasien', $search_type) || !$is_search_by_type) {
                $model->orWhere(['ilike', 'nama_pasien', $q]);
            }
            if (in_array('nopeserta_bpjs', $search_type) || !$is_search_by_type) {
                $model->orWhere(['ilike', 'nopeserta_bpjs', $q]);
            }
            if (in_array('no_telepon_pasien', $search_type) || !$is_search_by_type) {
                $model->orWhere(['ilike', 'no_telepon_pasien', $q]);
            }
            if (in_array('no_mobile_pasien', $search_type) || !$is_search_by_type) {
                $model->orWhere(['ilike', 'no_mobile_pasien', $q]);
            }
            $model->limit(50);
        }

        if ($id) {
            $model->where(['pasien_id' => $id]);

            return $model->one();
        }

        return $model->all();
    }

    /**
     * @todo Fungsi untuk mendapatkan data jadwal buka poli
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetJadwalBukaPoli()
    {
        $request = Yii::$app->request;
        $tanggal = $request->get('tanggal', null);
        $kondisiJam = false;
        $model = JadwalBukaPoliView::find();
        $ruangan = [];

        if ($tanggal) {
            $hariId = null;
            $lookup_hari = DocoConstants::$look_hari;
            $hari = DocoHelpers::getTanggalIndonesia($tanggal);

            if (isset($lookup_hari[$hari['urutan_hari']])) {
                $hariId = $lookup_hari[$hari['urutan_hari']];
            }

            if ($tanggal == date('Y-m-d', strtotime('NOW'))) {
                $kondisiJam = true;
            }

            $model->where([
                'instalasi_id' => DocoConstants::VAR_I_RJ,
                'hari' => $hariId,
                'is_active' => true
            ]);

            if ($kondisiJam) {
                $model->andWhere(['>', 'jam_tutup', date('H:i:s', strtotime('NOW'))]);
            }

            $data = $model->all();

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $ruangan[$value['ruangan_id']] = $value['ruangan_nama'];
                }
            }
        }

        return $ruangan;
    }

    /**
     * @todo Fungsi untuk mendapatkan data jadwal dokter
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetJadwalDokter()
    {
        $request = Yii::$app->request;
        $tglPendaftaran = $request->get('tgl_pendaftaran', null);
        $ruanganId = $request->get('ruangan_id', null);
        $kondisiJam = false;
        $model = InfoJadwalDokterView::find();
        $dokter = [];

        if ($ruanganId) {
            if ($tglPendaftaran == date('Y-m-d', strtotime('NOW'))) {
                $kondisiJam = true;
            }

            $model->where([
                'instalasi_id' => DocoConstants::VAR_I_RJ,
                'ruangan_id' => $ruanganId
            ]);

            if ($kondisiJam) {
                $model->andWhere(['>', 'waktu_selesai', date('H:i:s', strtotime('NOW'))]);
            }

            $data = $model->all();

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $dokter[$value['pegawai_id']] = $value['nama_pegawai'];
                }
            }
        }

        return $dokter;
    }

    /**
     * @todo Fungsi untuk mendapatkan data jam kunjungan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetJamKunjungan()
    {
        $request = Yii::$app->request;
        $tglPendaftaran = $request->get('tgl_pendaftaran', null);
        $ruanganId = $request->get('ruangan_id', null);
        $pegawaiId = $request->get('pegawai_id', null);
        $kondisiJam = false;
        $konfigSystem = KonfigSystem::find()->one();
        $jamKunjungan = [];

        if ($konfigSystem != null) {
            if ($konfigSystem['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                $model = JadwalBukaPoliView::find();
            } else {
                $model = InfoJadwalDokterView::find();
            }
        } else {
            $model = InfoJadwalDokterView::find();
        }

        $model->where(['is_active' => true]);

        if ($tglPendaftaran) {
            $hariId = null;
            $lookup_hari = DocoConstants::$look_hari;
            $hari = DocoHelpers::getTanggalIndonesia($tglPendaftaran);

            if (isset($lookup_hari[$hari['urutan_hari']])) {
                $hariId = $lookup_hari[$hari['urutan_hari']];
            }

            if ($tglPendaftaran == date('Y-m-d', strtotime('NOW'))) {
                $kondisiJam = true;
            }

            if ($hariId) {
                if ($konfigSystem['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                    $model->andWhere(['hari' => $hariId]);

                    if ($kondisiJam) {
                        $model->andWhere(['>', 'jam_tutup', date('H:i:s', strtotime('NOW'))]);
                    }
                } else {
                    $model->andWhere(['hari_jadwalbuka' => $hariId]);

                    if ($kondisiJam) {
                        $model->andWhere(['>', 'waktu_selesai', date('H:i:s', strtotime('NOW'))]);
                    }
                }
            }
        }

        if ($ruanganId) {
            $model->andWhere(['ruangan_id' => $ruanganId]);
        }

        if ($pegawaiId) {
            $model->andWhere(['pegawai_id' => $pegawaiId]);
        }

        $data = $model->all();

        if (!empty($data)) {
            if ($konfigSystem['kuota_antrian'] == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                foreach ($data as $key => $value) {
                    $waktu = date('H:i', strtotime($value['jam_mulai'])).'-'.date('H:i', strtotime($value['jam_tutup']));
                    $jamKunjungan[$waktu] = $waktu;
                }
            } else {
                foreach ($data as $key => $value) {
                    $waktu = date('H:i', strtotime($value['waktu_mulai'])).'-'.date('H:i', strtotime($value['waktu_selesai']));
                    $jamKunjungan[$waktu] = $waktu;
                }
            }
        }

        return $jamKunjungan;
    }

    /**
     * @todo Fungsi untuk mendapatkan data penjamin
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        $caraBayarId = $request->get('carabayar_id', null);
        $model = Penjamin::find();
        $penjamin = [];

        if ($caraBayarId) {
            $data = $model->where([
                'carabayar_id' => $caraBayarId,
                'is_active' => true,
                'is_deleted' => false,
            ])->all();

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $penjamin[$value['penjamin_id']] = $value['penjamin_nama'];
                }
            }
        }

        return $penjamin;
    }

    /* bg-proc */
    public function actionUnduhFile()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $tipe = isset($get['tipe']) ? $get['tipe'] : 1;
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = ArrayHelper::getValue($get, 'randString');
        $columns = ArrayHelper::getValue($get, 'columns');
        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        
        $data = $this->actionGetObjectData();

        $countData = ($tipe == 1) ? count($data) : ArrayHelper::getValue($data, 'countData', 0);
        $totalPerPage = ceil($countData/20);
        $url = Yii::$app->docoRest->getBaseUri('pendaftaran');
        $params = [
            'sendToUrl' => 'reservasi-poliklinik/drop-file',
            'getDataUrl' => 'reservasi-poliklinik/get-object-data',
            'base_uri' => $url,
        ];
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'ReservasiPoliklinikDataPasienDownload' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                    'params' => $params,
                    'columns' => $columns
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ReservasiPoliklinikDataPasienExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                    'title' => 'Laporan Reservasi Poliklinik',
                    'columns' => $columns
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ReservasiPoliklinikDataPasienUpload' => [
                'token' => $auth,
                'xOwner' => $xOwner,
                'unique_str' => $randString,
                'totalPerPage' => $totalPerPage,
                'params' => $params,
                'tipe' => $tipe
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $params = ArrayHelper::getValue($getData, 'params', []);
        $tipe = ArrayHelper::getValue($getData, 'tipe', 1);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $advancedFilters = [];
        if(isset($getData['advanced-filter'])) {
            $advancedFilters = $getData['advanced-filter'];
        }

        if(isset($params['advanced-filter'])) {
            $advancedFilters = $params['advanced-filter'];
        }

        $model = new InfoPendaftaranOnlineView;
        $query = $model::find();
      
        if(!empty($advancedFilters)) {
            if(isset($advancedFilters['tgl_kunjungan'])) {
                $tglBatal = $advancedFilters['tgl_kunjungan'];
                $explode = explode(" - ", $tglBatal);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_kunjungan']);
            }

            if(isset($advancedFilters['nama_pasien'])) {
                $namaPasien = trim($advancedFilters['nama_pasien']);
                $query->andWhere(['or', ['ilike', 'LOWER(nama_pasien)',  strtolower($namaPasien)], ['like', 'LOWER(nama_pasien_ol)', strtolower($namaPasien)]]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }

            if(isset($advancedFilters['ruangan_id']) && is_array(explode(',', $advancedFilters['ruangan_id'])) ){
                $query->andWhere(['in', 'ruangan_id', explode(',', $advancedFilters['ruangan_id'])]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }
        }

        $query->andWhere(['between', 'tgl_kunjungan', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $model = $query->asArray()->all();
        $result = $model;

        if($tipe == 2) {
            $periode = date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end));
            $attributes = [
                '#datatable#' => $this->renderPartial('index', [
                    'data' => $model,
                ]),
                '#periode#' => $periode,
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#tanggal#' => date('d F Y H:i'),
            ];
            $result = [
                'periode' => $periode,
                'attributes' => $attributes,
                'countData' => count($model),
            ];
        }
        return $result;
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                'path' => $path,
                'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $tipe = $request->get('tipe', 1);
        $ext = ($tipe == 1) ? '.xlsx' : '.pdf';
        $rootPath = './uploads';
        $files = $rootPath.'/'.$filename . $ext;
        if(file_exists($files)) {
            header('Content-Description: File Transfer');
            if($tipe == 1) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            }
            else {
                header('Content-Type: application/pdf');
            }
            header("Content-Disposition: inline; filename=$files");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($files);
            unlink($files);
            die();
        }
    }

    /* 
    * This function is used by external apps name Portal Reservasi Sirs
    */
    public function actionGetPortalBundle()
    {
        $caraBayar = Cache::getCaraBayar();

        $caraBayar = array_values(array_filter($caraBayar, function ($var) {
            return in_array($var['carabayar_id'], [DocoConstants::PENJAMIN_UMUM, DocoConstants::PENJAMIN_ASURANSI, 17]); // 17 = perusahaan
        }));

        $defaultPenjamin = DocoConstansId::actionGetAdditional('default_penjamin_by_carabayar', true);

        $penjamin = Cache::getPenjamin();
        $jenisIdentitas = Cache::getJenisIdentitasPasien();

        $asalRujukan = [
            [
                'label' => 'Faskes Tingkat 1',
                'value' => '1096',
            ],
            [
                'label' => 'Faskes Tingkat 2 (RS)',
                'value' => '1099',
            ],
        ];

        $kuotaAntrian = 598;

        $ruanganPegawai = InfoJadwalDokterView::find()->select([
            'ruangan_id',
            'ruangan_nama',
            'pegawai_id',
            'nama_pegawai',
            'is_executive'
        ])->where([
            'instalasi_id' => DocoConstants::INST_ID_RJ,
        ])->orderBy([
            'ruangan_nama' => SORT_ASC,
            'nama_pegawai' => SORT_ASC,
        ])->groupBy([
            'ruangan_id',
            'ruangan_nama',
            'pegawai_id',
            'nama_pegawai',
            'is_executive'
        ])->all();

        return [
            'message' => 'Success Retrieve Portal Bundle Data',
            'data' => compact('caraBayar', 'penjamin', 'asalRujukan', 'kuotaAntrian','ruanganPegawai', 'defaultPenjamin', 'jenisIdentitas'),
        ];
    }

    /*
    * Validate pasien by no rm /pasien id dan tgl lahirnya
    * digunakan untuk memastikan pasien tersebut benar ketika akan emndaftarkan pasien baru di reservasi react js
    * [GET], int $pasien_id (required), string tgl_lahir (required)
    */
    public function actionValidatePasienExist()
    {
        $request = Yii::$app->request;
        if (!empty($request->get('no_identitas_pasien', null)) && !empty($request->get('tgl_lahir', null))) {
            $subQuery =
                (new \yii\db\Expression("(SELECT DISTINCT ON (a.pasien_id) a.pasien_id, arr.object->>'no_identitas_pasien' as no_induk_kependudukan
                FROM pasien_m a, json_array_elements(a.additional_pasien::json) with ordinality arr(object, position)
                WHERE a.additional_pasien <> '' AND object->>'jenisidentitas' = '94'
                GROUP BY a.pasien_id, no_induk_kependudukan)"))
            ;
            $pasien = Pasien::find()->select(['pasien_m.pasien_id', 'pasien_m.nama_pasien', 'pasien_m.no_rekam_medik', 'pasien_m.tanggal_lahir', 'pasien_m.alamat_pasien', 'lookup_m.lookup_value as jenis_kelamin', 'no_telepon_pasien'])
                      ->leftJoin('lookup_m', 'lookup_m.lookup_id = pasien_m.jeniskelamin::integer')
                      ->leftJoin(['data_nik' => $subQuery], 'data_nik.pasien_id = pasien_m.pasien_id')
                      ->andWhere(['and',
                            ['or',
                                ['pasien_m.no_rekam_medik' => $request->get('no_identitas_pasien', null)],
                                ['pasien_m.no_identitas_pasien' => $request->get('no_identitas_pasien', null)],
                                ['data_nik.no_induk_kependudukan' => $request->get('no_identitas_pasien', null)]
                            ],
                          ['pasien_m.tanggal_lahir' => date('Y-m-d', strtotime($request->get('tgl_lahir')))]
                        ]);

            $pasien = $pasien->asArray()->one();
            
            if (!empty($pasien)) {
                return [
                  'status' => 200,
                  'message' => 'Data Pasien Ditemukan!!',
                  'data' => $pasien
                ];
            }        
            return [
              'status' => 404,
              'message' => 'Data Pasien Tidak Ditemukan'
            ];
        }
        return [
          'status' => 422,
          'message' => 'No Rekam Medik Atau Tanggal Lahir Tidak Boleh Kosong!!'
        ];
    }

}