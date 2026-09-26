<?php
/**
 * @author: arief saputra
 * @description: informasi konsul poli
**/

namespace app\modules\v1\controllers;

use app\modules\v1\models\Anamnesa;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\Infokonsulpoli;
use app\modules\v1\models\Konsulpoli;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JadwalPoliklinik;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Pendaftaran;

use app\modules\v1\models\DokterV;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\PermintaanMakan;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\SoapRj;
use app\modules\v1\models\TindakanPelayanan;
use Doco\models\RencanakontrolT;

use Integrasi\Service\Satusehat\Models\InfoDataKunjunganView;
use yii\db\Expression;

class InfKonsulPoliController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Infokonsulpoli';
    
    public $messageBroker = [
        'approve' => [
            'services' => [
                'Satusehat' => [
                    'Encounter' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ],
                ],
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-layarantrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        $verbs["get-data-dropdown"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id');
            $kelompok_pegawai_id = $request->get('kelompokpegawai_id');
            $pegawai_id = Yii::$app->jwt->user->pegawai_id;

            $model = new Infokonsulpoli;
            $query = $model::find();

            // manual filter, for unsupported feature in advancedFilter
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            // return $request->get('advanced-filter');
            
            if(isset($_GET['advanced-filter'])) {
                $filter = $_GET['advanced-filter'];
                if(isset($filter['tgl_konsulpoli'])) {
                    $explode = explode(" - ", $filter['tgl_konsulpoli']);

                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }

                    unset($_GET['advanced-filter']['tgl_konsulpoli']);
                }

                if(isset($filter['nama_pasien'])) {
                    $query->orWhere(['ILIKE', 'nama_pasien', $filter['nama_pasien']]);
                    $query->orWhere(['ILIKE', 'no_rekam_medik', $filter['nama_pasien']]);
                    $query->orWhere(['ILIKE', 'no_pendaftaran', $filter['nama_pasien']]);

                    unset($_GET['advanced-filter']['nama_pasien']);
                }

                if(isset($filter['status_konsul'])) {
                    if ($filter['status_konsul'] == DocoConstants::STATUS_PERIKSA_NAMA_BATAL_KONSULTASI) {
                        $query->andWhere(['ILIKE', 'status', $filter['status_konsul']]);    
                    } else {
                        $query->andWhere(['ILIKE', 'status_konsul', $filter['status_konsul']]);
                        $query->andWhere(['NOT ILIKE', 'status', DocoConstants::STATUS_PERIKSA_NAMA_BATAL_KONSULTASI]);  
                    }
                }

                if(isset($filter['ruangan_asal'])){
                    $query->andWhere(['ruanganasal_id'=>$filter['ruangan_asal']]);
                }
                if(isset($filter['ruangan_tujuan'])){
                    $query->andWhere(['ruangan_id'=>$filter['ruangan_tujuan']]);
                }

                if(isset($filter['nama_dokter'])){
                    $query->andWhere(['pegawai_id'=>$filter['nama_dokter']]);
                }
            }

            $query->andWhere(['between', 'tgl_poli', $start, $end ]);
            $query->orderBy(['tgl_poli' => SORT_DESC]);

            if (!is_null($kelompok_pegawai_id) && $kelompok_pegawai_id != DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
                $query->andWhere(['or',
                    ['pegawai_id' => $pegawai_id],
                    ['dok_mengkonsul_id' => $pegawai_id]
                ]);
            }

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id = null)
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');

            $data = Infokonsulpoli::findOne(['konsulpoli_id' => $id]);

            return [
                'data' => $data
            ];
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

    public function actionUpdate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $message = 'Data berhasil dibatalkan.';

            $modelKonsulPoli = Konsulpoli::findOne($post['konsulpoli_id']);
            $statusPeriksa = ArrayHelper::getValue($modelKonsulPoli, 'status_periksa');
            $pendaftaran_id = ArrayHelper::getValue($modelKonsulPoli, 'pendaftaran_id');
            $ruangan_asal = ArrayHelper::getValue($modelKonsulPoli, 'ruangan_id');
            $konsulpoli_id = ArrayHelper::getValue($post, 'konsulpoli_id');

            if (($post) && ($modelKonsulPoli)) {
                $batalValidation = self::batalValidation($pendaftaran_id, $ruangan_asal, $konsulpoli_id);
                if ($batalValidation != 'VALID') {
                    return [
                        'title' => 'Data Gagal Dibatalkan!',
                        'text' => $batalValidation,
                        'status' => 422
                    ];
                }
                // if ($statusPeriksa == DocoConstants::STATUS_PERIKSA_DIPERIKSA || $statusPeriksa == DocoConstants::STATUS_PERIKSA_AN_KSR) {
                //     return [
                //         'title' => 'Data Gagal Dibatalkan!',
                //         'text' => 'Konsul tidak dapat dibatalkan karena sudah dilakukan pemeriksaan.',
                //         'status' => 422
                //     ];
                // }
                $id_antrian_before = $modelKonsulPoli->antrian_id;

                // batalin antrian poli sebelumnya
                $modelAntrianBefore = Antrian::findOne($id_antrian_before);
                if (!empty($modelAntrianBefore)) {
                    $modelAntrianBefore->status_antrian = DocoConstants::VAR_SA_B;
                    $modelAntrianBefore->is_deleted = true;
                    $modelAntrianBefore->is_active = false;
                    $modelAntrianBefore->deleted_date = date('Y-m-d h:i:s');
                    $modelAntrianBefore->save(false);
                }

                // fill data konsul poli from post
                $modelKonsulPoli->attributes = $post;

                $deleted_surat_kontrol = $this->hapusSuratKontrol($konsulpoli_id);

                if ($deleted_surat_kontrol && ArrayHelper::getValue($deleted_surat_kontrol, 'response.metaData.code', 500) != 200) {
                    $message .= 'tetapi surat kontrol gagal dibatalkan, Silahkan hubungi administrasi rawat jalan';
                }

                $modelKonsulPoli->save(false);

                $transaction->commit();
            }else{
                throw new \Exception('Data Tidak Di Temukan');
            }

            return [
                'title' => 'Proses Berhasil !',
                'text' => $message
            ];
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

    private function getData()
    {
        $data = Infokonsulpoli::find();
        return $data;
    }

    // Get daftar status periksa
    public function actionGetDataDropdown() {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->post('ruangan_id');

            // Find beberapa data
            $ruangan = Ruangan::find()->joinWith('instalasi')->where([Ruangan::tableName().'.is_active' => true])->andWhere([Instalasi::tableName().'.instalasi_singkatan' => DocoConstants::INSTALASI_RAWAT_JALAN])->all();
            $dokter = DokterV::find()->andWhere(['ruangan_id' => $ruangan_id])->all();
            $statusPeriksa = Lookup::find()->where(['lookup_type' => 'status_periksa'])->all();

            // Merge data
            $data['ruangan'] = $ruangan;
            $data['dokter'] = $dokter;
            $data['statusPeriksa'] = $statusPeriksa;

            // Return data
            return $data;
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

    public function actionListRuangan() {
        $request = Yii::$app->request;

        try {
            $ruangan_id = $request->get('ruangan_id');

            $data_jadwalpoli = $this->getJadwalPoli($ruangan_id);
            $data_jadwalpoli = $data_jadwalpoli->asArray()->all();


            return [
                'data-jadwalpoli' => $data_jadwalpoli
            ];
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
     * @see Fungsi get data poli dari jadwal poli
     * @return array, activeQueryRecords
     *
     */
    private function getJadwalPoli($ruangan_id = null)
    {
        $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $mapp_hari = DocoConstants::$look_hari;
        $jam = date('H:i:s');
        $hari = isset($mapp_hari[$date_now['urutan_hari']]) ? $mapp_hari[$date_now['urutan_hari']] : 0;

        $sql = "
            SELECT
                t.jadwalbukapoli_id,
                t.ruangan_id,
                lu.lookup_name as hari,
                t.jam_mulai,
                t.jam_tutup,
                r.ruangan_nama
            FROM
                jadwalbukapoli_m t
            LEFT JOIN ruangan_m r ON r.ruangan_id = t.ruangan_id
            LEFT JOIN lookup_m lu ON lu.lookup_id = t.hari
            CROSS JOIN (
                SELECT
                    '{$jam}' :: TIME AS event
            ) sub
            WHERE
                t.is_deleted = FALSE
            AND lu.lookup_id = '{$hari}'
            AND t.ruangan_id != 1
            AND CASE
            WHEN jam_mulai <= jam_tutup THEN
                jam_mulai <= event
            AND jam_tutup >= event
            ELSE
                jam_mulai <= event
            OR jam_tutup >= event
            END
        ";

        $result = JadwalPoliklinik::findBySql($sql);

        return $result;
    }

    public function actionGetBundleData()
    {
        $statuskonsul = Lookup::find()
            ->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value', 'lookup_kode'])
            ->andWhere(['lookup_id' => DocoConstants::UrutanStatusPeriksaKonsul])
            ->andWhere(['is_active' => TRUE])
            ->all();

        $ruangan = Ruangan::find()->joinWith('instalasi')->where([Ruangan::tableName().'.is_active' => true])->andWhere([Instalasi::tableName().'.instalasi_singkatan' => DocoConstants::INSTALASI_RAWAT_JALAN])->all();
        $dokter = DokterV::find()->all();
        
        return [
            'status_konsulpoli' => $statuskonsul,
            'ruangan' => $ruangan,
            'dokter' => $dokter
        ];
    }

    /**
    * @controller actionExportPdf
    * @attribute #table# => table data
    * @attribute #judul_dokumen# => title
    * @attribute #info_pasien# => Judul Info Pasien
    * @attribute #no_rm# => no_rm
    * @attribute #tgl_daftar# => Tanggal Daftar
    * @attribute #no_daftar# => no_daftar
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #jeniskelamin# => jeniskelamin
    * @attribute #kasuspenyakit# => kasuspenyakit
    * @attribute #tanggal_lahir# => tanggal_lahir
    * @attribute #umur# => umur
    * @attribute #dokter_pemeriksaan# => dokter_pemeriksaan
    * @attribute #kelaspelayanan# => kelaspelayanan
    * @attribute #info_konsul# => subtitle2
    * @attribute #dokter_konsulpoli# => dokter_konsulpoli
    * @attribute #ruangan_tujuan# => ruangan_tujuan
    * @attribute #catatan_konsulpoli# => catatan_konsulpoli
    **/
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $jenis = $request->get('jenis');
            $modelKonsulPoli = new Infokonsulpoli;
            $data_konsul = $modelKonsulPoli::find()->andWhere(['konsulpoli_id' => $id])->asArray()->one();
            $data_pasien = $this->getDataPasien($data_konsul['pendaftaran_id'])->asArray()->one();
            $title = ($jenis == 'jawaban-konsul' ? Yii::t('app', 'Jawaban Konsul poli') : Yii::t('app', 'Permintaan Konsul poli'));
            $subtitle1 = Yii::t('app', 'Data Pasien');
            $subtitle2 = Yii::t('app', 'Detail Konsul');

            $data = [
                'pasien' => $data_pasien,
                'konsul' => $data_konsul,
            ];

            // return $data;

            $header = [];
            $print = new DocoPrint();
            $print->attributes = [
                '#table#' => $this->renderPartial('index',[
                    'title'=> $title,
                    'header'=> $header,
                    'data' => $data,
                    'jenis' => $jenis,
                ]),
                '#judul_dokumen#' => $title,
                '#info_pasien#' => $subtitle1,
                '#no_rm#' => $data_pasien['no_rekam_medik'],
                '#tgl_daftar#' => date('l, d F Y  H:i:s',strtotime($data_pasien['tgl_pendaftaran'])),
                '#no_daftar#' => $data_pasien['no_pendaftaran'],
                '#nama_pasien#' => $data_pasien['nama_pasien'],
                '#jeniskelamin#' => $data_pasien['jeniskelamin'],
                '#kasuspenyakit#' => $data_pasien['jeniskasuspenyakit_nama'],
                '#tanggal_lahir#' => date('d F Y',strtotime($data_pasien['tanggal_lahir'])),
                '#umur#' => $data_pasien['umur'],
                '#dokter_pemeriksaan#' => $data_pasien['nama_pegawai'],
                '#kelaspelayanan#' => $data_pasien['kelaspelayanan_nama'],
                '#info_konsul#' => $subtitle2,
                '#ruangan_tujuan#' => $data['konsul']['ruangan_tujuan'],
                '#dokter_konsulpoli#' => $data['konsul']['nama_dokter'],
                '#catatan_konsulpoli#' => $data['konsul']['catatan_dokter_konsul'],
            ];
            $print->Output();

            // return true;
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get data pasienpendaftaran pendaftaran_t
     * @var params integer id = primary key pendaftaran_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataPasien($id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pendaftaran_t.pasien_id as pasien_id,
                    pasien_m.no_rekam_medik AS no_rekam_medik,
                    pendaftaran_t.tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    pasien_m.nama_pasien AS nama_pasien,
                    lookup_m.lookup_name AS jeniskelamin,
                    pendaftaran_t.jeniskasuspenyakit_id AS jeniskasuspenyakit_id,
                    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jeniskasuspenyakit_nama,
                    pasien_m.tanggal_lahir AS tanggal_lahir,
                    pendaftaran_t.golonganumur_id AS golonganumur_id,
                    pegawai_m.nama_pegawai AS nama_pegawai,
                    pendaftaran_t.kelaspelayanan_id,
                    pendaftaran_t.pegawai_id,
                    pendaftaran_t.penjamin_id,
                    pendaftaran_t.carabayar_id,
                    pendaftaran_t.umur,
                    ruangan_m.ruangan_nama as poliklinik,
                    kelaspelayanan_m.kelaspelayanan_nama AS kelaspelayanan_nama
                FROM
                    pendaftaran_t
                LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                LEFT JOIN lookup_m ON lookup_m.lookup_id = pasien_m.jeniskelamin::INT
                LEFT JOIN jeniskasuspenyakit_m ON jeniskasuspenyakit_m.jeniskasuspenyakit_id = pendaftaran_t.jeniskasuspenyakit_id
                LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = pendaftaran_t.pegawai_id
                LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
                LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = pendaftaran_t.ruangan_id
                WHERE
                    pendaftaran_t.is_deleted = FALSE
            ";

        // filter
        if ($id) {
            $sql .= " AND pendaftaran_t.pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        $result = Pendaftaran::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }

    public function actionGetLookupType()
    {
        // return [
        //     'status_konsulpoli' => $this->lookup_type->actionGetLookupType('status_konsulpoli'),
        // ];

        $query = Lookup::find()
            ->select(['lookup_id', 'lookup_type', 'lookup_name', 'lookup_value', 'lookup_kode'])
            ->andWhere(['lookup_id' => DocoConstants::UrutanStatusPeriksaKonsul])
            ->andWhere(['is_active' => TRUE])
            ->all();
        
        return [
                'status_konsulpoli' => $query,
        ];
    }

    public function actionApprove()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            $post = $request->post();

            $konsulpoliId = $post['konsulpoli_id'];

            $modelKonsulPoli = Konsulpoli::findOne($konsulpoliId);
            $modelPendaftaran = Pendaftaran::findOne($modelKonsulPoli->pendaftaran_id);
            
            $tanggalKonsulPoli = date('Y-m-d', strtotime($modelKonsulPoli->tgl_konsulpoli));
            $tanggalPendaftaran = date('Y-m-d', strtotime($modelPendaftaran->tgl_pendaftaran));

            if ($tanggalKonsulPoli != $tanggalPendaftaran) {
                return [
                        'status' => 500,
                        'title' => 'Proses Gagal !',
                        'message' => 'Pasien Konsul di Hari yang Berbeda, Silakan Daftarkan di Pendaftaran'
                    ];
            }


            if (($post) && ($modelKonsulPoli)) {


                $modelKonsulPoli->status_approve = $post['status_approve'];
                $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                $modelKonsulPoli->disetujui_oleh = isset($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : 0;
                $modelKonsulPoli->save(false);

                $transaction->commit();
                $return = [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Konsul Berhasil di Setujui',
                        'ruangan_id' => $modelKonsulPoli->ruangan_id,
                        'pegawai_id' => $modelKonsulPoli->pegawai_id,
                        'konsulpoli_id' => $konsulpoliId,
                        'pendaftaran' => $modelPendaftaran->toArray()
                ];

                return $return;

            }else{
                throw new \Exception('Data Tidak Di Temukan');
            }

            return $return;
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

    private function batalValidation($pendaftaran_id, $ruangan_asal, $konsulpoli_id) {
        if (!empty($pendaftaran_id && $ruangan_asal)) {
            // get data konsulpoli
            $konsulpoli = Konsulpoli::find()
            ->select(['status_konsul'])
            ->where([
                'konsulpoli_id' => $konsulpoli_id,
                'status_konsul'=> DocoConstants::STATUS_KONSUL_DIJAWAB
            ])
            ->count();

            // get data soap
            $soap = SoapRj::find()
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_id' => $ruangan_asal
            ])
            ->andWhere(new Expression("COALESCE((additional_data::json->>'via_soap')::boolean, false) = true"))
            ->count();
            
            // get data penunjang
            $countPasienKirimUnitLain = PasienKirimUnitLain::find()
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_asal' => $ruangan_asal,
                'pasienmasukpenunjang_id' => null
            ])
            ->andWhere([
                'not in', 'status_penunjang', [DocoConstants::BTL_APPROVE, DocoConstants::DI_TOLAK]
            ])
            ->count();

            $pasienmasukpenunjang = PasienMasukPenunjangT::find()
            ->innerJoin(
                'pasienkirimkeunitlain_t', 
                'pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id'
            )
            ->where([
                'pasienkirimkeunitlain_t.pendaftaran_id' => $pendaftaran_id,
                'pasienkirimkeunitlain_t.ruangan_asal' => $ruangan_asal
            ])
            ->andWhere(['not', ['pasienkirimkeunitlain_t.pasienmasukpenunjang_id' => null]])
            ->andWhere(['<>', 'pasienmasukpenunjang_t.status_periksa', DocoConstants::ST_P_PEN_BTL])
            ->count();

            $penunjang = $countPasienKirimUnitLain + $pasienmasukpenunjang;

            // get data diet
            $diet = PermintaanMakan::find()
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_asal' => $ruangan_asal
            ])->andWhere([
                '<>', 'status', '0'
            ])->count();

            //data askep
            // $askep = Anamnesa::find()
            // ->where([
            //     'pendaftaran_id' => $pendaftaran_id
            // ])
            // ->count();

            // data asmed
            // $asmed = PemeriksaanFisik::find()
            // ->where([
            //     'pendaftaran_id' => $pendaftaran_id
            // ])
            // ->count();

            // data reseptur
            $reseptur = Reseptur::find()
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                'ruanganreseptur_id' => $ruangan_asal
            ])
            ->andWhere(['<>', 'status_reseptur', DocoConstants::RESEPTUR_DIBATALKAN])
            ->count();

            // data tindakan
            $tindakan_pelayanan = TindakanPelayanan::find()
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_id' => $ruangan_asal,
                'alasan_batal' => null
            ])
            ->count();

            $message = [];
            $message[] = 'Konsul tidak dapat dibatalkan!';
            if ($konsulpoli > 0) {
                $message[] = 'Konsul sudah dijawab';
            }
            // if ($askep > 0) {
            //     $message[] = 'Sudah isi Asesmen Keperawatan';
            // }
            // if ($asmed > 0) {
            //     $message[] = 'Sudah isi Asesemen Medis';
            // }
            if ($soap > 0) {
                $message[] = 'Sudah isi SOAP';
            }
            if ($tindakan_pelayanan > 0) {
                $message[] = 'Sudah isi Tindakan';
            }
            if ($penunjang > 0) {
                $message[] = 'Sudah order Penunjang';
            }
            if ($reseptur > 0) {
                $message[] = 'Sudah order Reseptur';
            }
            if ($diet > 0) {
                $message[] = 'sudah order Permintaan Makan';
            }
            
            if (count($message) === 1) {
                $message = [];
                $message[] = 'VALID';
            }
        } else {
            $message[] = 'Pendaftaran ID atau Ruangan ID tidak ada!';
        }
        $stringMessage = implode(', ', $message);
        return $stringMessage;
    }

    private function hapusSuratKontrol($konsulpoli_id)
    {
        $rencanakontrol_id = RencanakontrolT::find()->select(['rencanakontrol_id'])->where(['konsulpoli_id' => $konsulpoli_id])->scalar();
        if (!empty($rencanakontrol_id)) {
            $res_hapus_surat_kontrol = Yii::$app->docoRest->pendaftaran->post('rencana-kontrol-inap/hapus', [
                'form_params' => [
                    'bypass' => true, // karena sudah mengisi auth sebelum eksekusi endpoint hapus konsul
                    'rencanakontrol_id' => $rencanakontrol_id,
                    'username' => Yii::$app->jwt->user->nama_pemakai
                ]
            ]);

            $res_hapus_surat_kontrol = json_decode($res_hapus_surat_kontrol->getBody(), true);
            return $res_hapus_surat_kontrol;
        }
        return false;
    }
}
