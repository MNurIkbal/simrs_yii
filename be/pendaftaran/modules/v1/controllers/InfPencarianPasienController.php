<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-02-14 16:53
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\web\UploadedFile;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfPencarianPasien;
use app\modules\v1\models\RiwayatkunjunganR;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\PasiencetakanV;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PasienUbahData;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PasienUbahDataView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\RingkasanMedisPasienView;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\Services\InternalService;
use yii\helpers\Url;
use app\modules\v1\models\UploadForm;

class InfPencarianPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfPencarianPasien';
    const DEFAULT_LIMIT = 100;

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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
        $request = Yii::$app->request;
        $filter = $request->get();

        $subQryUbah =  (new \yii\db\Query())->from('pasienubahdata_t pk')
        ->select(['max(pk.pasienubahdata_id) AS pasienubahdata_id', 'pk.pasien_id'])
        ->groupBy('pk.pasien_id');
        
        $subQuery = PasienUbahData::find()
        ->select(['pasienubahdata_t.pasienubahdata_id', 'pasienubahdata_t.pasien_id', 'pasienubahdata_t.alasan_ubahdata', 'pasienubahdata_t.is_active', 'pasienubahdata_t.is_deleted'])
        ->join('JOIN', ['max_pk' => $subQryUbah], "pasienubahdata_t.pasienubahdata_id = max_pk.pasienubahdata_id AND pasienubahdata_t.pasien_id = max_pk.pasien_id");
        
        
        $expression = new Expression('CASE pasien_m.jenisidentitas::text WHEN '.DocoConstants::IDENTITAS_KTP.'::text THEN no_identitas_pasien ELSE NULL END');
        $model = new Pasien;
        $query = $model::find()
                ->select([
                    'pasien_m.pasien_id',
                    'no_rekam_medik',
                    'tgl_rekam_medik',
                    'looknmdpn.lookup_value as namadepan',
                    // 'jenis_kelamin',
                    'pasien_m.jeniskelamin',
                    'nama_pasien',
                    'jenisid.lookup_value AS jenisidentitas',
                    'nik_pasien' => $expression,
                    'tanggal_lahir',
                    'alamat_pasien',
                    'pasien_m.propinsi_id',
                    'prov.propinsi_nama',
                    'kab.kabupaten_nama',
                    'pasien_m.kabupaten_id',
                    'kec.kecamatan_nama',
                    'pasien_m.kecamatan_id',
                    'pasien_m.created_date AS tgl_pembuatan',
                    'pasien_m.last_modified_date AS tgl_update_terakhir',
                    'petugas_pembuat.nama_pegawai AS pembuat_nama',
                    'pasien_m.last_modified_date AS tgl_update_terakhir',
                    'petugas_pemakai.nama_pegawai AS petugas_nama',
                    'pasienubah.alasan_ubahdata',
                    'lookjenis.lookup_value as jenis_kelamin'
                ])
                ->join('LEFT JOIN', 'lookup_m lookjenis',"lookjenis.lookup_id = pasien_m.jeniskelamin::int ")
                ->join('LEFT JOIN', 'lookup_m looknmdpn',"looknmdpn.lookup_id = pasien_m.namadepan::int ")
                ->join('LEFT JOIN', 'lookup_m jenisid',"jenisid.lookup_id = pasien_m.jenisidentitas::int ")
                ->join('LEFT JOIN', 'propinsi_m prov',"prov.propinsi_id = pasien_m.propinsi_id")
                ->join('LEFT JOIN', 'kabupaten_m kab',"kab.kabupaten_id = pasien_m.kabupaten_id")
                ->join('LEFT JOIN', 'kecamatan_m kec',"kec.kecamatan_id = pasien_m.kecamatan_id")
                ->leftjoin(['pasienubah' => $subQuery],"pasienubah.pasien_id = pasien_m.pasien_id")
                ->join('LEFT JOIN', 'loginpemakai_k petugas_1',"petugas_1.loginpemakai_id = pasien_m.last_modified_by")
                ->join('LEFT JOIN', 'pegawai_m petugas_pemakai',"petugas_pemakai.pegawai_id = petugas_1.pegawai_id")
                ->join('LEFT JOIN', 'loginpemakai_k pembuat',"pembuat.loginpemakai_id = pasien_m.created_by")
                ->join('LEFT JOIN', 'pegawai_m petugas_pembuat',"petugas_pembuat.pegawai_id = pembuat.pegawai_id");

        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('tgl_rekam_medik', $filter['advanced-filter'])) {
                $explode = explode(' - ', $filter['advanced-filter']['tgl_rekam_medik']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_rekam_medik']);
            }

            if (array_key_exists('no_rekam_medik', $filter['advanced-filter'])) {
                $no_rekam_medik = $filter['advanced-filter']['no_rekam_medik'];
                $query->andWhere(['like', 'no_rekam_medik', $no_rekam_medik]);
                unset($_GET['advanced-filter']['no_rekam_medik']);
            }

            if (array_key_exists('nama_pasien', $filter['advanced-filter'])) {
                $nama_pasien = $filter['advanced-filter']['nama_pasien'];
                $query->andWhere(['ilike', 'nama_pasien', $nama_pasien]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('jenis_kelamin', $filter['advanced-filter'])) {
                $jenis_kelamin = $filter['advanced-filter']['jenis_kelamin'];
                $query->andWhere(['pasien_m.jeniskelamin' => $jenis_kelamin]);
                unset($_GET['advanced-filter']['jenis_kelamin']);
            }

            if (array_key_exists('alamat_pasien', $filter['advanced-filter'])) {
                $alamat_pasien = $filter['advanced-filter']['alamat_pasien'];
                $query->andWhere(['ilike', 'alamat_pasien', $alamat_pasien]);
                unset($_GET['advanced-filter']['alamat_pasien']);
            }

            if (array_key_exists('propinsi_nama', $filter['advanced-filter'])) {
                $propinsi_nama = $filter['advanced-filter']['propinsi_nama'];
                $query->andWhere(['=', 'pasien_m.propinsi_id', $propinsi_nama]);
                unset($_GET['advanced-filter']['propinsi_nama']);
            }

            if (array_key_exists('kabupaten_nama', $filter['advanced-filter'])) {
                $kabupaten_nama = $filter['advanced-filter']['kabupaten_nama'];
                $query->andWhere(['=', 'pasien_m.kabupaten_id', $kabupaten_nama]);
                unset($_GET['advanced-filter']['kabupaten_nama']);
            }

            if (array_key_exists('kecamatan_nama', $filter['advanced-filter'])) {
                $kecamatan_nama = $filter['advanced-filter']['kecamatan_nama'];
                $query->andWhere(['=', 'pasien_m.kecamatan_id', $kecamatan_nama]);
                unset($_GET['advanced-filter']['kecamatan_nama']);
            }

            if (array_key_exists('petugas', $filter['advanced-filter'])) {
                $petugas = $filter['advanced-filter']['petugas'];
                $query->andWhere(['ilike', 'petugas_pemakai.nama_pegawai', $petugas]);
                unset($_GET['advanced-filter']['petugas']);
            }
        }
        
        if (array_key_exists('order', $filter)) {
            $order = explode(" ", $filter['order']);
            $key = $order[0];
            $type = ($order[1] == 'DESC') ? SORT_DESC : SORT_ASC;

            if($key == 'tgl_rekam_medik') {
                $type = SORT_DESC;
            }

            $query->orderBy([
                $key => $type
            ]);
        }
        
        $result = $query->limit(self::DEFAULT_LIMIT)->asArray()->all();

        return new ArrayDataProvider([
            'allModels' => $result, 
            'pagination' => [
                'pageSize' => $filter['per-page'],
            ],
        ]);
    }

    public function actionGetData()
    {
        $model = new PasienV;
        $query = $model::find()
                ->select([
                    'pasien_id',
                    'no_rekam_medik',
                    'tgl_rekam_medik',
                    'namadepan',
                    'nama_depan',
                    'jeniskelamin',
                    'nama_pasien',
                    'jenisidentitas',
                    'tanggal_lahir',
                    'alamat_pasien',
                    'propinsi_id',
                    'propinsi_nama',
                    'kabupaten_nama',
                    'kabupaten_id',
                    'kecamatan_nama',
                    'kecamatan_id',
                    'tgl_pembuatan',
                    'tgl_update_terakhir',
                    'pembuat_nama',
                    'petugas_nama',
                    'alasan_ubahdata',
                    'jenis_kelamin',
                    'additional_pasien',
                    'no_mobile_pasien',
                    'no_telepon_pasien',
                    'catatanpenting_pasien',
                    'nopeserta_bpjs',
                    'no_identitas_pasien',
                ])->where('no_rekam_medik IS NOT NULL');
        
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['nama_pasien'])) {
                $term = strtolower($_GET['advanced-filter']['nama_pasien']);
                unset($_GET['advanced-filter']['nama_pasien']);

                $query->andWhere(['like', 'LOWER(nama_pasien)', $term])
                    ->orWhere(['ilike', 'no_mobile_pasien', $term]);
            }

            if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                $tanggal = $_GET['advanced-filter']['tanggal_lahir'];
                unset($_GET['advanced-filter']['tanggal_lahir']);
                $startDate = date('Y-m-d 23:59:59', strtotime($tanggal));
                $query->andWhere(['=', 'date(tanggal_lahir)', $startDate]);
            }

            if (isset($_GET['advanced-filter']['no_identitas'])) {
                $term = $_GET['advanced-filter']['no_identitas'];
                unset($_GET['advanced-filter']['no_identitas']);
                $query->andWhere(['=', 'no_identitas_pasien', $term]);
            }
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getQuery($request = null)
    {
        $model = new PasienV;
        $query = $model::find()
            ->select([
                'pasien_v.pasien_id AS pasien_id',
                'tgl_rekam_medik',
                'no_rekam_medik',
                'nama_pasien',
                'tanggal_lahir',
                'jenis_kelamin',
                'propinsi_nama',
                'kabupaten_nama',
                'kecamatan_nama',
                'petugas_nama',
                'nama_depan AS namadepan',
                "CONCAT(nama_depan, '', nama_pasien) AS namapasien",
                'jenisidentitas',
                'no_identitas_pasien',
                'alamat_pasien',
                'propinsi_id',
                'kabupaten_id',
                'kecamatan_id',
                'tgl_pembuatan',
                'pembuat_nama',
                'tgl_update_terakhir',
                'additional_pasien',
            ]);

        return $this->populateFilter($query, $request);
    }

    protected function populateFilter($query, $request = null)
    {
        $advancedFilters = $request->get('advanced-filter', []);
        if (array_key_exists('no_rekam_medik', $advancedFilters)) {
            $no_rekam_medik = $advancedFilters['no_rekam_medik'];
            $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
            unset($advancedFilters['no_rekam_medik']);
        }

        if (array_key_exists('nama_pasien', $advancedFilters)) {
            $nama_pasien = $advancedFilters['nama_pasien'];
            $query->andWhere(['ilike', 'nama_pasien', $nama_pasien]);
            unset($advancedFilters['nama_pasien']);
        }

        if (array_key_exists('jenis_kelamin', $advancedFilters)) {
            $jenis_kelamin = $advancedFilters['jenis_kelamin'];
            $query->andWhere(['jeniskelamin' => $jenis_kelamin]);
            unset($advancedFilters['jenis_kelamin']);
        }

        if (array_key_exists('alamat_pasien', $advancedFilters)) {
            $alamat_pasien = $advancedFilters['alamat_pasien'];
            $query->andWhere(['ilike', 'alamat_pasien', $alamat_pasien]);
            unset($advancedFilters['alamat_pasien']);
        }

        if (array_key_exists('propinsi_nama', $advancedFilters)) {
            $propinsi_nama = $advancedFilters['propinsi_nama'];
            $query->andWhere(['=', 'propinsi_id', $propinsi_nama]);
            unset($advancedFilters['propinsi_nama']);
        }

        if (array_key_exists('kabupaten_nama', $advancedFilters)) {
            $kabupaten_nama = $advancedFilters['kabupaten_nama'];
            $query->andWhere(['=', 'kabupaten_id', $kabupaten_nama]);
            unset($advancedFilters['kabupaten_nama']);
        }

        if (array_key_exists('kecamatan_nama', $advancedFilters)) {
            $kecamatan_nama = $advancedFilters['kecamatan_nama'];
            $query->andWhere(['=', 'kecamatan_id', $kecamatan_nama]);
            unset($advancedFilters['kecamatan_nama']);
        }

        if (array_key_exists('petugas', $advancedFilters)) {
            $petugas = $advancedFilters['petugas'];
            $query->andWhere(['ilike', 'petugas_nama', $petugas]);
            unset($advancedFilters['petugas']);
        }

        if (array_key_exists('no_identitas_pasien', $advancedFilters)) {
            $petugas = $advancedFilters['no_identitas_pasien'];
            $query->andWhere(['=', 'no_identitas_pasien', $petugas]);
            unset($advancedFilters['no_identitas_pasien']);
        }

        if (array_key_exists('nopeserta_bpjs', $advancedFilters)) {
            $petugas = $advancedFilters['nopeserta_bpjs'];
            $query->andWhere(['=', 'nopeserta_bpjs', $petugas]);
            unset($advancedFilters['nopeserta_bpjs']);
        }

        if (array_key_exists('tanggal_lahir', $advancedFilters)) {
            $tanggal = $advancedFilters['tanggal_lahir'];
            unset($advancedFilters['tanggal_lahir']);
            $startDate = date('Y-m-d 23:59:59', strtotime($tanggal));
            $query->andWhere(['=', 'date(tanggal_lahir)', $startDate]);
        }

        return $query;
    }

    public function actionRiwayat()
    {
        $model = new InfPencarianPasien;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }

    public function actionUpdate($id) 
    {
        try {
            $request = Yii::$app->request;
            $model = Pasien::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'InfPencarianPasienForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
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
    public function actionExportExcel()
    {
        $header = array();
        $model = new PasienV;
        $query = $model::find()
        ->select([
            'pasien_id',
            'no_rekam_medik',
            'tgl_rekam_medik',
            'jenis_kelamin',
            // 'jeniskelamin',
            "CONCAT(nama_depan,'',nama_pasien) AS nama_pasien",
            'tanggal_lahir',
            'no_identitas_pasien AS nik',
            'alamat_pasien',
            'propinsi_nama',
            'propinsi_id',
            'kabupaten_nama',
            'kabupaten_id',
            'kecamatan_nama',
            'kecamatan_id',
            'additional_pasien',
        ]);

        $request = Yii::$app->request;
        $query = $this->populateFilter($query, $request);

        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        foreach($data as $idx => $value) {
            if (!empty($value['additional_pasien'])) {
                $pasienAdds = json_decode($value['additional_pasien'], true);
                $nikPasien = null;
                foreach($pasienAdds as $adds) {
                    if ($adds['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) {
                        $nikPasien = $adds['no_identitas_pasien'];
                    }
                }
                $data[$idx]['nik'] = $nikPasien;
            } else {
                $data[$idx]['nik'] = null;
            }
            unset($data[$idx]['additional_pasien']);
        }

        if (isset($_GET['advanced-filter']['no_rekam_medik'])) {
            $header[Yii::t('app', 'No rekam medik')] = $_GET['advanced-filter']['no_rekam_medik'];
        }
        if (isset($_GET['advanced-filter']['nama_pasien'])) {
            $header[Yii::t('app', 'Nama pasien')] = $_GET['advanced-filter']['nama_pasien'];
        }
        if (isset($_GET['advanced-filter']['alamat_pasien'])) {
            $header[Yii::t('app', 'Alamat')] = $_GET['advanced-filter']['alamat_pasien'];
        }
        if (isset($_GET['advanced-filter']['jenis_kelamin'])) {
            $header[Yii::t('app', 'Jenis kelamin')] = $data[0]['jenis_kelamin'];
        }
        if (isset($_GET['advanced-filter']['propinsi_nama'])) {
            $header[Yii::t('app', 'Provinsi')] = $data[0]['propinsi_nama'];
        }
        if (isset($_GET['advanced-filter']['kabupaten_nama'])) {
            $header[Yii::t('app', 'Kabupaten')] = $data[0]['kabupaten_nama'];
        }
        if (isset($_GET['advanced-filter']['kecamatan_nama'])) {
            $header[Yii::t('app', 'Kecamatan')] = $data[0]['kecamatan_nama'];
        }

        $filePath = DocoHelpers::exportExcel('Laporan data pasien', $data, $header, array("uploadPath" => "./uploads"),[],[],true);
        $filePath->save('php://output');
        die;
    }
    /**
    * @controller actionExportPdf
    * @attribute #table_exportpdf# => table 
    **/
    public function actionExportPdf() 
    {
        
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        ini_set("pcre.backtrack_limit", 5000000);
        $header = array();
        $model = new PasienV;
        $query = $model::find()
        ->select([
            'pasien_id',
            'no_rekam_medik',
            'tgl_rekam_medik',
            'jenis_kelamin',
            'jeniskelamin',
            "CONCAT(nama_depan,'',nama_pasien) AS nama_pasien",
            'tanggal_lahir',
            'no_identitas_pasien',
            'alamat_pasien',
            'propinsi_nama',
            'propinsi_id',
            'kabupaten_nama',
            'kabupaten_id',
            'kecamatan_nama',
            'kecamatan_id',
            'additional_pasien',
        ]);

        $request = Yii::$app->request;
        $query = $this->populateFilter($query, $request);

        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        
        foreach($data as $idx => $value) {
            if (!empty($value['additional_pasien'])) {
                $pasienAdds = json_decode($value['additional_pasien'], true);
                $nikPasien = null;
                foreach($pasienAdds as $adds) {
                    if ($adds['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) {
                        $nikPasien = $adds['no_identitas_pasien'];
                    }
                }
                $data[$idx]['no_identitas_pasien'] = $nikPasien;
            } else {
                $data[$idx]['no_identitas_pasien'] = null;
            }
        }

        if (isset($_GET['advanced-filter']['no_rekam_medik'])) {
            $header['no_rekam_medik'] = $_GET['advanced-filter']['no_rekam_medik'];
        }
        if (isset($_GET['advanced-filter']['nama_pasien'])) {
            $header['nama_pasien'] = $_GET['advanced-filter']['nama_pasien'];
        }
        if (isset($_GET['advanced-filter']['alamat_pasien'])) {
            $header['alamat_pasien'] = $_GET['advanced-filter']['alamat_pasien'];
        }
        if (isset($_GET['advanced-filter']['jenis_kelamin'])) {
            $header['jenis_kelamin'] = $data[0]['jenis_kelamin'];
        }
        if (isset($_GET['advanced-filter']['propinsi_nama'])) {
            $header['propinsi_nama'] = $data[0]['propinsi_nama'];
        }
        if (isset($_GET['advanced-filter']['kabupaten_nama'])) {
            $header['kabupaten_nama'] = $data[0]['kabupaten_nama'];
        }
        if (isset($_GET['advanced-filter']['kecamatan_nama'])) {
            $header['kecamatan_nama'] = $data[0]['kecamatan_nama'];
        }
       
        $print = new DocoPrint();
        $print->attributes = [
            '#table_exportpdf#' => $this->renderPartial('index', [
                'header' => $header,
                'data' => $data,
            ]),
        ];
        $print->Output();
    }

    /**
    * @controller actionCetakKartu
    * @attribute #no_rm# => nomor rekam medik
    * @attribute #nama_pasien# => pasien nama
    * @attribute #ttl_pasien# => tempat tanggal lahir pasien
    * @attribute #barcode# => barcode
    * @attribute #foto# => barcode
    **/
    public function actionCetakKartu($id){
        try {
            $model = new InfPencarianPasien;
            $query = $model::find()
                    ->select(['pasien_id',
                        'no_rekam_medik',
                        'nama_pasien',
                        'tempat_lahir',
                        'tanggal_lahir',
                    ])->groupBy(['pasien_id',
                        'no_rekam_medik',
                        'nama_pasien',
                        'tempat_lahir',
                        'tanggal_lahir',
                    ]);
            $query->where(['pasien_id'=>$id]);
            $data = $query->asArray()->one();
            // return $data;

             // Print
            $print = new DocoPrint();

            // Assign attributes
            $print->attributes = [
                '#no_rm#'=>$data['no_rekam_medik'],
                '#nama_pasien#'=>$data['nama_pasien'],
                '#ttl_pasien#'=>$data['tempat_lahir'].' '.$data['tanggal_lahir'],
                '#barcode#'=>'',
                '#foto#'=>'',
            ];
            // Print output
            $print->Output();

        } catch (Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    /**
    * @controller actionCetakDataPasien
    * @attribute #no_rekam_medik# => nomor rekam medik
    * @attribute #no_identitas_pasien# => nomor identitas pasien
    * @attribute #nama_pasien# => pasien nama + nama depan
    * @attribute #nama_panggilan# => pasien nama panggilan
    * @attribute #tempat_lahir# => tempat lahir
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #jenis_kelamin# => jenis kelamin
    * @attribute #status_perkawinan# => status perkawinan
    * @attribute #nama_ibu# => nama ibu
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #rt# => rt
    * @attribute #rw# => rw
    * @attribute #propinsi_nama# => propinsi nama
    * @attribute #kabupaten_nama# => kabupaten nama
    * @attribute #kecamatan_nama# => kecamatan nama
    * @attribute #kelurahan_nama# => kelurahan nama
    * @attribute #no_telepon_pasien# => no telepon pasien
    * @attribute #pekerjaan_nama# => pekerjaan nama
    * @attribute #warganegara# => warganegara
    * @attribute #agama_pasien# => agama pasien
    * @attribute #umur# => umur
    * @attribute #pendidikan# => pendidikan
    * @attribute #suku# => suku
    * @attribute #no_mobile_pasien# => no_mobile_pasien
    * @attribute #anakke# => anak_ke
    * @attribute #nama_ayah# => nama_ayah
    * @attribute #saudara# => saudara
    * @attribute #foto# => foto
    * @attribute #identitas# => identitas
    * @attribute #golongan_darah# => golongan_darah
    * @attribute #alergi# => alergi
    * @attribute #penanggungjawab_nama# => nama penanggung jawab
    * @attribute #hubungankeluarga# => hubungan keluarga penanggung jawab
    * @attribute #penanggungjawab_alamat# => alamat penanggung jawab
    * @attribute #penanggungjawab_notelp# => nomor telepon penanggung jawab
    * @attribute #carabayar_nama# => nama cara bayar
    * @attribute #penjamin_nama# => nama penjamin
    * @attribute #nokartuasuransi# => nomor asuransi
    **/
    public function actionCetakDataPasien($id)
    {
        return Yii::$app->docoPlugin->execute('cetak_data_pasien');
    }

    public function getJenisIdentitas($id)
    {
        return Lookup::findOne($id);
    }

    public function actionView($id)
    {
        return PasienV::find()
        ->where(['pasien_id' => $id])
        ->one();
    }

    public function actionRiwayatTera()
    {
        $model = new RiwayatkunjunganR;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }

    public function actionTotalPasien()
    {
        $queryCount = "
            SELECT 
                COUNT(*) as total 
            FROM PASIEN_M 
            WHERE is_active = true and is_deleted = false;
        ";
        $totalCount = Yii::$app->db->createCommand($queryCount)->queryOne();

        return $totalCount['total'];
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

    /* excel bg-proc */
    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListDataPendaftaranPasienExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListDataPendaftaranPasienExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListDataPendaftaranPasienUpload' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filename' => 'LIST_DATA_PENDAFTARAN_PASIEN',
                    'ext' => '.xlsx',
                ]
            ]
        ], true);
        return [
            'randString' => $randString
        ];
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $fileName = $rootPath.'/'.$no_request;

        if (file_exists($fileName))
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }

    /**
    * * @author : bacengjs (Bambang.Hermawan@sirs.co.id)
    * * A product of PT. Citraraya Nusatama
    * * Powered by Sirs
    */

    public function actionGetDataRiwayatPerubahanDataPasien()
    {
        $model = new PasienUbahDataView;
	$query = $model::find(true);
	$query->orderBy(['created_date' => SORT_DESC]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    // Do not Delete This okee..
    // function kosong untuk set flag function strict enable hak akses pada informasi pencarian data pada modul RM
    public function actionSetFlagHakAksesUpdate()
    {

    }

    public function actionPackModalPrmrj()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id');

        $pasien = PasienV::find()->select([
                'pasien_id',
                'no_rekam_medik',
                'nama_pasien',
                'alamat_pasien',
                'jenis_kelamin',
                'agama_pasien',
                'tanggal_lahir',
                'alergi',
            ])->where([
                'pasien_id' => $pasien_id
            ])->asArray()->one();

        $instalasi_id_rj = (new DocoConstansId)->actionGetId('RJ');
        $instalasi_id_rd = (new DocoConstansId)->actionGetId('RD');
        $instalasi = Instalasi::find()->select([
                'instalasi_id',
                'instalasi_nama',
            ])->where([
                'IN', 'instalasi_id', [$instalasi_id_rj, $instalasi_id_rd]
            ])->asArray()->all();

        return [
            'pasien' => $pasien,
            'instalasi' => $instalasi,
        ];
    }

    public function actionGetDataPrmrj()
    {
        $model = new RingkasanMedisPasienView;
        $limit = Yii::$app->request->get('per-page', 10);
        $page = Yii::$app->request->get('page', 1);
        $order = Yii::$app->request->get('order', null);
        $filter = Yii::$app->request->get('advanced-filter', []);
        
        $query = RingkasanMedisPasienView::find()
            ->select([
                'pendaftaran_id',
                'no_pendaftaran',
                'pasien_id',
                'tgl_pendaftaran',
                'ruangan_nama',
                'nama_dokter',
                'a_diag_utama',
                'obat_tindakan',
                'tindak_lanjut',
                'anamnesa',
                'object',
            ]);

        if (isset($filter['pasien_id'])) {
            $query->andWhere(['pasien_id' => $filter['pasien_id']]);
            unset($_GET['advanced-filter']['pasien_id']);
        }
        
        if (isset($filter['tgl_pendaftaran_awal'])) {
            $startDate = date('Y-m-d 00:00:00', strtotime($filter['tgl_pendaftaran_awal']));
            $endDate = date('Y-m-d 23:59:59', strtotime($filter['tgl_pendaftaran_akhir']));
            $query->andWhere(['between', 'tgl_pendaftaran', $startDate, $endDate]);
            unset($_GET['advanced-filter']['tgl_pendaftaran_awal']);
            unset($_GET['advanced-filter']['tgl_pendaftaran_akhir']);
        }

        if (isset($filter['instalasi_id'])) {
            $query->andWhere(['instalasi_id' => $filter['instalasi_id']]);
            unset($_GET['advanced-filter']['instalasi_id']);
        }

        if (isset($filter['ruangan_id'])) {
            $query->andWhere(['ruangan_id' => $filter['ruangan_id']]);
            unset($_GET['advanced-filter']['ruangan_id']);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        if (!empty($order)) {
            $order = explode(" ", $order);
            $key = $order[0];
            $type = ($order[1] == 'DESC') ? SORT_DESC : SORT_ASC;
            $query->orderBy([
                $key => $type
            ]);
        }
        $totalRecord = $query->count();
        $record = $query->offset(($page - 1) * $limit)->limit($limit)->asArray()->all();

        return [
            'recordsFiltered' => $totalRecord,
            'recordsTotal' => $totalRecord,
            'data' => $record,
        ];
    }

    /**
    * @controller actionCetakPdfPrmrj
    * @attribute #nama_pasien# => Nama Pasien
    * @attribute #no_rekam_medik# => Nomor Rekam Medik
    * @attribute #alamat_pasien# => Alamat Pasien
    * @attribute #data_kunjungan# => Data Kunjungan
    * @attribute #umur# => Umur
    * @attribute #tgl_pembuatan_resume# => Tanggal Pembuatan Resume
    * @attribute #jenis_kelamin# => Jenis Kelamin
    * @attribute #alergi# => Alergi
    * @attribute #agama_pasien# => Agama
    * @attribute #data# => Data Tabel
    **/
    public function actionCetakPdfPrmrj() 
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id', null);
        $tgl_pendaftaran = $request->get('tgl_pendaftaran', null);
        $instalasi_id = $request->get('instalasi_id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        
        $header = $this->getHeaderPdfPrmrj($pasien_id, $instalasi_id);
        $data = $this->getDataPdfPrmrj($pasien_id, $tgl_pendaftaran, $instalasi_id, $ruangan_id);
        
        $print = new DocoPrint();
        $print->attributes = [
            '#nama_pasien#' => $header['nama_pasien'],
            '#no_rekam_medik#' => $header['no_rekam_medik'],
            '#alamat_pasien#' => $header['alamat_pasien'],
            '#data_kunjungan#' => $header['data_kunjungan'],
            '#umur#' => $header['umur'],
            '#tgl_pembuatan_resume#' => $header['tgl_pembuatan_resume'],
            '#jenis_kelamin#' => $header['jenis_kelamin'],
            '#alergi#' => $header['alergi'],
            '#agama_pasien#' => $header['agama_pasien'],
            '#data#' => $this->renderPartial('cetak_prmrj', [
                'data' => $data,
            ]),
        ];
        $print->Output();
    }

    private function getHeaderPdfPrmrj($pasien_id, $instalasi_id)
    {
        $pasien = PasienV::find()->select([
            'pasien_id',
            'no_rekam_medik',
            'nama_pasien',
            'alamat_pasien',
            'jenis_kelamin',
            'agama_pasien',
            'tanggal_lahir',
            'alergi',
        ])->where([
            'pasien_id' => $pasien_id
        ])->asArray()->one();
        
        $queryInstalasi = Instalasi::find()->select([
            'instalasi_id',
            'instalasi_nama',
        ]);
        if ($instalasi_id) {
            $queryInstalasi->andWhere(['instalasi_id' => $instalasi_id]);
        } else {
            $instalasi_id_rj = (new DocoConstansId)->actionGetId('RJ');
            $instalasi_id_rd = (new DocoConstansId)->actionGetId('RD');
            $queryInstalasi->andWhere([
                'IN', 'instalasi_id', [$instalasi_id_rj, $instalasi_id_rd]
            ]);
        }
        $instalasi = $queryInstalasi->asArray()->all();

        $pasien['umur'] = $pasien['tanggal_lahir'] ? 
            DocoHelpers::getUmur(ArrayHelper::getValue($pasien, 'tanggal_lahir')) : null;
        $tgl_pembuatan_resume = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $pasien['tgl_pembuatan_resume'] = $tgl_pembuatan_resume['tanggal'].' '.
            $tgl_pembuatan_resume['bulan'].' '.
            $tgl_pembuatan_resume['tahun'];
        $pasien['data_kunjungan'] = join(" & ", ArrayHelper::getColumn(
            $instalasi, 'instalasi_nama'
        ));

        return $pasien;
    }

    private function getDataPdfPrmrj($pasien_id, $tgl_pendaftaran, $instalasi_id, $ruangan_id)
    {
        $query = RingkasanMedisPasienView::find()
            ->select([
                'pendaftaran_id',
                'no_pendaftaran',
                'pasien_id',
                'tgl_pendaftaran',
                'ruangan_nama',
                'nama_dokter',
                'a_diag_utama',
                'obat_tindakan',
                'tindak_lanjut',
                'anamnesa',
                'object',
            ]);

        if ($pasien_id) {
            $query->andWhere(['pasien_id' => $pasien_id]);
        }
        if ($tgl_pendaftaran) {
            $tgl_pendaftaran_range = explode(' - ', $tgl_pendaftaran);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $startDate = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $endDate = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $query->andWhere(['between', 'tgl_pendaftaran', $startDate, $endDate]);
        }
        if ($instalasi_id) {
            $query->andWhere(['instalasi_id' => $instalasi_id]);
        }
        if ($ruangan_id) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }
        $query->orderBy(['tgl_pendaftaran' => SORT_ASC]);
        $data = $query->asArray()->all();

        return $data;
    }
    public function actionGetDataRuanganDokter()
    {
        $request = Yii::$app->request;
        $dokter = DokterView::find()->select([
                'pegawai_id',
                'nama_pegawai',
            ])->asArray()->all();
        $ruangan = Ruangan::find()->select([
                'ruangan_id',
                'ruangan_nama',
            ])->asArray()->all();

        return [
            'dokter' => $dokter,
            'ruangan' => $ruangan,
        ];
    }

}
