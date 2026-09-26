<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\InfoPasienBpjsView;
use app\modules\v1\models\SyInfoPasienBpjsView;
use app\modules\v1\models\InfoPasienBpjsDiagnosaView;
use app\modules\v1\models\SyInfoPasienBpjsDiagnosaView;
use app\modules\v1\models\InfoPasienBpjsKlaimView;
use app\modules\v1\models\SyInfoPasienBpjsKlaimView;
use app\modules\v1\models\InfoKlaimInacbg;
use app\modules\v1\models\KoreksiDiagnosaView;
use app\modules\v1\models\KoreksiDiagnosaNewView;
use app\modules\v1\models\KoreksiDiagnosa;
use app\modules\v1\models\SyKoreksiDiagnosa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KlaimInacbg;
use app\modules\v1\models\KlaimInacbgDetail;
use app\modules\v1\models\SyKlaimInacbg;
use app\modules\v1\models\SyKlaimInacbgDetail;
use app\modules\v1\models\KlaimInacbgGroup;
use app\modules\v1\models\SyKunjunganView;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganDetail;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganTagihan;
use app\modules\v1\models\SyKunjunganDetailView;
use app\modules\v1\models\SyBagian;
use app\modules\v1\models\InfoDiagnosa;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\SyKunjunganAdjusmentDetail;
use app\modules\v1\models\CaraBayar;
use Doco\components\DocoMessages;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pasien;
use Doco\Services\InternalService;

class InfPasienRajalBpjsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienBpjsView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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
        $newActions = [
            'index' => 'app\modules\v1\actions\InfPasienRajalBpjs\IndexAction',
            'get-request' => 'app\modules\v1\actions\InfPasienRajalBpjs\GetRequestAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionInitIndex()
    {
        $result['ruangan'] = [];
        $result['penjamin'] = [];
        $result['status_verif'] = [];
        try {
            $result['ruangan'] = Yii::$app->runAction('v1/allow/get-ruangan', ['id' => DocoConstants::INST_ID_RJ]);
            $result['ruangan'] = isset($result['ruangan']['response']['ruangan']) ? $result['ruangan']['response']['ruangan'] : [];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin', ['id' => DocoConstants::PENJAMIN_BPJS]);
            $result['penjamin'] = isset($result['penjamin']['response']) ? $result['penjamin']['response'] : [];
            $result['status_verif'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'status_verifikasi']);
            $result['status_verif'] = isset($result['status_verif']['response']) ? $result['status_verif']['response'] : [];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    public function initKlaim()
    {
        $result['carakeluar'] = [];
        $result['tarifrs'] = [];
        $conf = @parse_ini_file('' . realpath(Yii::$app->basePath) . '/config/env/.env', true);
        $type_tarif = (isset($conf['inacbg']['env_vclaim']) && $conf['inacbg']['env_vclaim'] ==  DocoConstants::LOOKUP_BPJS_LIVE) ?  DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;
        $kodeTarif = DocoConstants::KODE_TARIF_BPJS;
        try {
            $result['carakeluar'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'carapulang_inacbg']);
            $result['carakeluar'] = isset($result['carakeluar']['response']) ? $result['carakeluar']['response'] : [];
            $result['tarifrs'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => $kodeTarif/* , 'name' => 'default_tarif' */]);
            $result['tarifrs'] = isset($result['tarifrs']['response']) ? $result['tarifrs']['response'] : [];
            $result['rujukanrs'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'rujukanbpjs']);
            $result['rujukanrs'] = isset($result['rujukanrs']['response']) ? $result['rujukanrs']['response'] : [];
            $result['jenis_identitas'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'jenis_identitas_bpjs']);
            $result['jenis_identitas'] = isset($result['jenis_identitas']['response']) ? $result['jenis_identitas']['response'] : [];
            $result['inacbg_penjamin'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'inacbg_penjamin']);
            $result['inacbg_penjamin'] = isset($result['inacbg_penjamin']['response']) ? $result['inacbg_penjamin']['response'] : [];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }
    public function actionProsesKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        // $post = json_decode($post['data'], true);
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $stateUpdate = false;
        try {
            $pendaftaran_id = $post['pendaftaran_id'];

            $getDiagnosa = $this->getDiagnosa($pendaftaran_id);
            $arrOldDiagnosa = [];
            if (count($getDiagnosa) > 0) {
                foreach ($getDiagnosa as $key => $value) {
                    $arrOldDiagnosa[$value['koreksidiagnosa_id']] = $value;
                }
            } else {
                $stateUpdate = true;
            }

            foreach ($post['data'] as $key => $value) {
                if (!$stateUpdate && isset($arrOldDiagnosa[$value['koreksidiagnosa_id']])) {
                    if (($arrOldDiagnosa[$value['koreksidiagnosa_id']]['is_icdprimer'] != $value['is_icdprimer']) || ($arrOldDiagnosa[$value['koreksidiagnosa_id']]['is_inacbg'] != $value['is_inacbg'])) {
                        $stateUpdate = true;
                    }
                }
                $model = SyKoreksiDiagnosa::find()->where(['sy_koreksidiagnosa_id' => $value['koreksidiagnosa_id']])->one();
                $model->is_inacbg = $value['is_inacbg'];
                $model->is_icdprimer = $value['is_icdprimer'];
                $save = $model->save();
                if (!$save) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Proses eklaim gagal.'
                    ];
                }
            }
            $find = SyKunjunganPasien::find()->where(['kunjungan_id' => $pendaftaran_id])->one();
            if (!empty($find)) {
                if ($find->status_kunjungan != DocoConstants::STATUS_SUDAH_KOREKSI) {
                    $find->status_kunjungan = DocoConstants::STATUS_SUDAH_KOREKSI;
                    $find->save();
                }
                $transaction->commit();
            } else {
                // $save = $find->save();
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Proses eklaim gagal pasien tidak ditemukan!'
                ];
            }
            return ['updated' => $stateUpdate];
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function getData()
    {
        $model = new InfoPasienBpjsView;
        $query = $model::find(true);

        $between = false;
        $start = $startOperasi = date('Y-m-d 00:00:00');
        $end = $endOperasi = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->andWhere(['jenis' => 'RJ-RD']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionView($id)
    {
        try {
            $model = SyInfoPasienBpjsView::find()
                ->where(['kunjungan_id' => $id])
                ->andWhere(['<>','instalasi_kode', DocoConstants::RINP])
                ->asArray()->all();
            
            if(count($model) > 1) {
                foreach ($model as $key => $value) {
                    if($key != 0) {
                        $model[0]['layanan_tarif'] += $model[$key]['layanan_tarif'];
                    }
                }
            }

            $pasienId = ArrayHelper::getValue($model[0], "pasien_id");
            if(isset($pasienId)) {
                $viewPasien = Pasien::find()->where(['pasien_id' => $pasienId])->one();
                $model[0]['no_kartu'] = isset($viewPasien->nopeserta_bpjs) ? $viewPasien->nopeserta_bpjs : null;
            }
            $result = $model[0];
            return $result;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionViewClaim($id)
    {
        try {
            $model = InfoKlaimInacbg::find()->where(['kunjungan_id' => $id])->asArray()->one();
            return $model;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function getDiagnosa($id)
    {
        try {
            $model = InfoPasienBpjsDiagnosaView::find()->where(['pendaftaran_id' => $id, 'jenis' => 'RJ-RD'])->orderBy(['is_icdprimer' => SORT_DESC])->asArray()->all();
            return $model;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    
    public function getDetailKlaim($noRm, $tglPlg, $kunjunganId = null)
    {
        $hasil = [];
        try {
            if($kunjunganId == null) {
                $filter = [
                    'tgl_pulang' => $tglPlg,
                    'no_rekammedik' => $noRm
                ];
            } else {
                $filter = ['kunjungan_id' => $kunjunganId];
            }

            $model = SyInfoPasienBpjsKlaimView::find()->where($filter)->asArray()->all();            
            $result = [];
            foreach ($model as $key => $value) {
                $groupInacbgNama = ArrayHelper::getValue($value, 'groupinacbg_nama');
                $tindakanKode = ArrayHelper::getValue($value, 'layanan_kode');
                $tindakanTarif = ArrayHelper::getValue($value, 'layanan_tarif', 0);
                if(!empty($groupInacbgNama)) {
                    if ($groupInacbgNama == 'Kamar/Akomodasi' || $groupInacbgNama == 'Kamar') {
                        $value['groupinacbg_nama'] = 'Kamar Akomodasi';
                    }

                    $name = str_replace(' ', '_', strtolower($value['groupinacbg_nama']));
                    if (isset($result[$name][$tindakanKode])) {
                        $result[$name][$tindakanKode] += $tindakanTarif;
                    } else {
                        $result[$name][$tindakanKode] = $tindakanTarif;
                    }
                }
            }

            foreach ($result as $key => $v) {
                $hasil[$key] = array_sum($v);
            }
            
            return $hasil;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
            return [];
        } catch (\yii\db\Exception $e) {
            throw new \Exception($e->getMessage());
            return [];
        }
    }

    public function getLastKlaim($id)
    {
        try {
            $detail = [];
            $model = SyKlaimInacbg::find()->where(['kunjungan_id' => $id])
                ->select(['kunjungan_id', 'is_deleted', 'sy_klaiminacbg_id', 'jenis_kelasrawat', 'status_klaim', 'is_deleted'])->one();
            
            $syKlaimInacbgId = ArrayHelper::getValue($model, 'sy_klaiminacbg_id');
            if ($model) {
                $detail = SyKlaimInacbgDetail::find()
                    ->where(['sy_klaiminacbg_id' => $syKlaimInacbgId, 'is_deleted' => false])
                    ->select(['sy_klaiminacbg_id', 'is_deleted', 'diagnosa_id', 'icd_versi', 'kode_diagnosa', 'nama_diagnosa'])
                    ->asArray()->all();
            }
            return ['header' => $model, 'detailinacbg' => $detail];
        } catch (\yii\db\Exception $e) {
            return ['header' => [], 'detailinacbg' => []];
        }
    }

    public function getDiagnosaInacbgs($id)
    {
        try {
            return SyInfoPasienBpjsDiagnosaView::find()
                ->where(['kunjungan_id' => $id, 'is_inacbg' => true])
                ->orderBy(['is_icdprimer' => SORT_DESC])
                ->asArray()->all();
            
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionGetData($id)
    {
        try {
            $info = $this->actionView($id);
            $result['klaim'] = $this->actionViewClaim($id);
            $result['info'] = $info ? $info : [];
            $result['diagnosa'] = $this->getDiagnosaInacbgs($id);
            $result['opsi'] = $this->initKlaim();
            $result['detailtarif'] = [];
            $result['inacbg'] = $this->getLastKlaim($id);
            
            if(!empty($info)){
                $norm = ArrayHelper::getValue($info, 'no_rekammedik');
                $tglPlg = ArrayHelper::getValue($info, 'tgl_pulang');
                $tglPlg = !empty($tglPlg) ? date('Y-m-d', strtotime($tglPlg)) : null;
                $result['detailtarif'] = $this->getDetailKlaim($norm, $tglPlg, $id);
            }

            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
            return $result = ['info' => [], 'diagnosa' => [], 'opsi' => [], 'detailtarif' => [], 'inacbg' => []];
        }
    }

    public function actionGetKoreksi($id)
    {
        try {
            $model = KoreksiDiagnosaView::find()->where(['pendaftaran_id' => $id]);
            $model->orderBy(['kelompokdiagnosa_id' => SORT_ASC]);
            return $model->asArray()->all();
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionExportExcel()
    {
        try {
            $filters = isset($_GET['advanced-filter']) ? $_GET['advanced-filter'] : [];
            $query = $this->getData()->asArray()->all();
            $data = [];
            $header = [];
            foreach ($query as $key => $value) {
                if (isset($filters['tgl_pendaftaran'])) {
                    $explode = explode(' - ', $filters['tgl_pendaftaran']);
                    if (count($explode) == 2) {
                        $start = date('d M Y', strtotime($explode[0]));
                        $end = date('d M Y', strtotime($explode[1]));
                    }
                    $header['Tanggal pendaftaran'] = $start . ' - ' . $end;
                }
                if (isset($filters['no_rekam_medik'])) {
                    $header['No Rekam Medik'] = $filters['no_rekam_medik'];
                }
                if (isset($filters['no_pendaftaran'])) {
                    $header['No Pendaftaran'] = $filters['no_pendaftaran'];
                }
                if (isset($filters['nama_pasien'])) {
                    $header['Nama Pasien'] = $filters['nama_pasien'];
                }
                if (isset($filters['carabayar_id'])) {
                    if (!isset($header['Cara Bayar'])) {
                        $header['Cara Bayar'] = $value['carabayar_nama'];
                    }
                }
                if (isset($filters['ruangan_id'])) {
                    if (!isset($header['Ruangan'])) {
                        $header['Ruangan'] = $value['ruangan_nama'];
                    }
                }
                if (isset($filters['pegawai_id'])) {
                    if (!isset($header['Dokter Penanggung Jawab'])) {
                        $header['Dokter Penanggung Jawab'] = $value['dokter_dpjp'];
                    }
                }
                if (isset($filters['status_verifikasi'])) {
                    if (!isset($header['Status'])) {
                        $header['Status'] = $value['status_verif'];
                    }
                }
                $newData = [];
                $newData['Tanggal Masuk'] = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $newData['No Rekam Medik'] = $value['no_rekam_medik'];
                $newData['No Pendaftaran'] = $value['no_pendaftaran'];
                $newData['Nama Pasien'] = $value['nama_pasien'];
                $newData['Cara Bayar'] = $value['carabayar_nama'];
                $newData['Penjamin'] = $value['penjamin_nama'];
                $newData['Ruangan'] = $value['ruangan_nama'];
                $newData['Jenis Kasus Penyakit'] = $value['jeniskasuspenyakit_nama'];
                $newData['Dokter Penanggung Jawab'] = $value['dokter_dpjp'];
                $newData['Status'] = $value['status_verif'];
                $data[] = $newData;
            }
            $filePath = DocoHelpers::exportExcel('Pasien Rawat Jalan BPJS', $data, $header, array("uploadPath" => "./uploads"), [], [], true);

            $filePath->save('php://output');
            die;
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
     * @controller actionExportPdf
     * @attribute #table_laporan# => Menampilkan Table Pasien Rajal Bpjs
     * @attribute #periode# => Periode laporan
     * @attribute #nama_pengguna# => Nama Pengguna
     * @attribute #tgl_cetak# => Nama Pengguna
     * @attribute #nip# => NIP
     **/

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $periode = date('d M Y') . ' - ' . date('d M Y');
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                    $periode = $start . ' - ' . $end;
                }
            }
        }
        $getKepalaRuangan = PegawaiView::find()->where(['ruangan_id' => Yii::$app->jwt->ruangan_id, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one();
        try {
            $data = $this->getData()->asArray()->all();
            $print = new DocoPrint();
            $print->attributes = [
                '#table_laporan#' => $this->renderPartial('index', ['data' => $data]),
                '#periode#' => $periode,
                '#nama_pengguna#' => isset($getKepalaRuangan['nama_pegawai']) ? $getKepalaRuangan['nama_pegawai'] : '',
                '#tgl_cetak#' => date('d F Y'),
                '#nip#' => isset($getKepalaRuangan['nomorindukpegawai']) ? $getKepalaRuangan['nomorindukpegawai'] : '',
            ];
            $print->Output();
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

    public function actionFinalKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $postData = ArrayHelper::getValue($post, 'data', []);
        $pendaftaranId = ArrayHelper::getValue($postData, 'pendaftaran_id');
        try {
            $modelKlaim = KlaimInacbg::find()->where(['pendaftaran_id' => $pendaftaranId, 'is_deleted' => false])->one();
            $klaiminacbg_id = $modelKlaim->klaiminacbg_id;
            $pendaftaran_id = $pendaftaranId;

            $modelGroup = new KlaimInacbgGroup;
            if (isset($post['grouper'])) {
                $modelGrouper = new KlaimInacbgGroup;
                $modelGrouper->attributes = $post['grouper'];
                $modelGrouper->klaiminacbg_id = $klaiminacbg_id;
                $item = [
                    'is_pemulasaranjenazah' => $post['data']['is_pemulasaranjenazah'],
                    'is_kantongjenazah' => $post['data']['is_kantongjenazah'],
                    'is_petijenazah' => $post['data']['is_petijenazah'],
                    'is_plastikerat' => $post['data']['is_plastikerat'],
                    'is_desinfektanjenazah' => $post['data']['is_desinfektanjenazah'],
                    'is_transport' => $post['data']['is_transport'],
                    'is_desinfektanmobil' => $post['data']['is_desinfektanmobil']
                ];
                $modelGrouper->add_jenazah = json_encode($item);

                if (isset($post['additional'])) {
                    $add = $post['additional'];
                    $arrSpecial = [];
                    $modelGrouper->group_nama = $post['additional']['cbg_desc'];
                    $modelGrouper->cbg = $post['additional']['cbg_code'];
                    $modelGrouper->group_tarif = $post['additional']['cbg_tarif'];
                    $modelGrouper->additional_data = json_encode($add);
                    if (isset($add['proc_code']) && isset($add['proc_name'])) {
                        array_push($arrSpecial, $add['proc_code']);
                        $modelGrouper->sp_procedure_kode = $add['proc_code'];
                        $modelGrouper->sp_procedure_nama = $add['proc_name'];
                    }
                    if (isset($add['pros_code']) && isset($add['pros_name'])) {
                        array_push($arrSpecial, $add['pros_code']);
                        $modelGrouper->sp_prosthesis_kode = $add['pros_code'];
                        $modelGrouper->sp_prosthesis_nama = $add['pros_name'];
                    }
                    if (isset($add['drug_code']) && isset($add['drug_name'])) {
                        array_push($arrSpecial, $add['drug_code']);
                        $modelGrouper->sp_drug_kode = $add['drug_code'];
                        $modelGrouper->sp_drug_nama = $add['drug_name'];
                    }
                    if (isset($add['inv_code']) && isset($add['inv_name'])) {
                        array_push($arrSpecial, $add['inv_code']);
                        $modelGrouper->sp_drug_kode = $add['inv_code'];
                        $modelGrouper->sp_drug_nama = $add['inv_name'];
                    }
                    $modelGrouper->special_group = (!empty($arrSpecial) ? implode(',', $arrSpecial) : '');
                }
                if ($modelGrouper->save()) {
                    $modelKlaim->klaimgroup_id = $modelGrouper->klaimgroup_id;
                } else {
                    return $modelKlaim->errors;
                    throw new \Exception(json_encode($modelGrouper->getErrors()));
                }
            }
            $modelKlaim->status_klaim = true;
            $modelKlaim->is_komplikasi = $post['data']['is_komplikasi'];
            $modelKlaim->is_pemulasaranjenazah = $post['data']['is_pemulasaranjenazah'];
            $modelKlaim->is_kantongjenazah = $post['data']['is_kantongjenazah'];
            $modelKlaim->is_petijenazah = $post['data']['is_petijenazah'];
            $modelKlaim->is_plastikerat = $post['data']['is_plastikerat'];
            $modelKlaim->is_desinfektanjenazah = $post['data']['is_desinfektanjenazah'];
            $modelKlaim->is_transport = $post['data']['is_transport'];
            $modelKlaim->is_desinfektanmobil = $post['data']['is_desinfektanmobil'];

            if (!$modelKlaim->save()) {
                throw new \Exception(json_encode($modelKlaim->getErrors()));
            }
            $find = SyKunjunganPasien::find()->where(['kunjungan_id' => $pendaftaran_id])->one();
            $find->status_kunjungan = DocoConstants::STATUS_FINAL_KLAIM;
            $save = $find->save();
            if (!$save) {
                throw new \Exception("Data gagal disimpan");
            }
            $finalklaim = [
                'metadata' => [
                    'method' => 'claim_final',
                ],
                'data' => [
                    'nomor_sep' => $post['nosep'],
                    'coder_nik' =>  DocoConstants::INACBG_NIK,
                ],
            ];
            $finalklaim = DocoHelpers::restInacbgs($finalklaim);
            if (isset($finalklaim['metadata']['status'])) {
                if ($finalklaim['metadata']['status'] != 200) {
                    throw new \Exception("Terjadi Kesalahan");
                }
            }
            $transaction->commit();
            return true;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }

    public function actionHapusKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $noSep = ArrayHelper::getValue($post, 'no_sep');
        $kunjunganId = ArrayHelper::getValue($post, 'kunjungan_id');
        $noPendaftaran = ArrayHelper::getValue($post, 'no_pendaftaran');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $status = DocoConstants::STATUS_SUDAH_KOREKSI;

        try {
            $findParent = SyKlaimInacbg::findOne(['kunjungan_id' => $kunjunganId]);

            if (!empty($findParent)) {
                $sy_klaiminacbg_id = $findParent['sy_klaiminacbg_id'];
                $deleteGroup = $connection->createCommand('
                    update sy_klaiminacbg 
                    set is_deleted = true 
                    where sy_klaiminacbg_id = ' . $sy_klaiminacbg_id . ' 
                    and is_deleted = false
                ')->execute();
            }

            $findParent->status_klaim = false;
            $delete = $findParent->delete();
            
            if (!$findParent->save()) {
                throw new \Exception("Terjadi Kesalahan");
            }

            $dataKunjunganSy = $this->getDataKunjunganSy($noPendaftaran);
            $kunjunganId = ArrayHelper::getValue($dataKunjunganSy, 'kunjungan_id');
            $find = SyKunjunganPasien::find()->where(['kunjungan_id' => $kunjunganId])->one();
            $find->status_kunjungan = $status;
            $find->no_klaimcovid = null;
            $save = $find->save();
            if (!$save) {
                throw new \Exception("Terjadi Kesalahan");
            }
            $hapusklaim = [
                'metadata' => [
                    'method' => 'delete_claim',
                ],
                'data' => [
                    'nomor_sep' => $noSep,
                    'coder_nik' => DocoConstants::INACBG_NIK,
                ],
            ];
            $hapus = json_decode(DocoHelpers::restInacbgs($hapusklaim), true);
            if (isset($hapus['metadata']['status'])) {
                if ($hapus['metadata']['status'] != 200) {
                    throw new \Exception("Terjadi Kesalahan");
                }
            }
            $hapusProcedure = [
                'metadata' => [
                    'method' => 'set_claim_data',
                    'nomor_sep' => $noSep,
                ],
                'data' => [
                    'procedur' => '#',
                    'coder_nik' => DocoConstants::INACBG_NIK,
                ],
            ];
            $proc = json_decode(DocoHelpers::restInacbgs($hapusProcedure), true);
            if (isset($proc['metadata']['status'])) {
                if ($proc['metadata']['status'] != 200) {
                    throw new \Exception("Terjadi Kesalahan");
                }
            }
            if (isset($proc['response']['status'])) {
                if ($proc['response']['status'] != 200) {
                    throw new \Exception("Terjadi Kesalahan");
                }
            }

            (new InternalService)->sendTo([
                'Sirs' => [
                        'SinkronDataBpjs\TriggerFreezeBilling' => [
                            'type_sinkron' => 'cancel',
                            'kunjungan_id' => $kunjunganId
                        ]
                    ]
            ], true);

            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new \Exception('Terjadi Kesalahan');
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception('Terjadi Kesalahan');
        }
        return true;
    }

    public function actionUpdateKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $pendaftaran_id = $post['pendaftaran_id'];
        $nosep = $post['nomor_sep'];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $status = DocoConstants::STATUS_PROSES_KLAIM;
        try {
            $findParent = KlaimInacbg::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            $findParent->status_klaim = false;
            if ($findParent->save()) {
                $deleteGroup = $connection->createCommand('update klaimgroup_t set is_deleted = true where klaiminacbg_id = ' . $findParent->klaiminacbg_id . ' and is_deleted = false')->execute();

                $find = SyKunjunganPasien::find()->where(['kunjungan_id' => $pendaftaran_id])->one();
                $find->status_kunjungan = $status;
                $save = $find->save();
                if (!$save) {
                    throw new \Exception("Data gagal disimpan");
                }
                $reedit = [
                    'metadata' => [
                        'method' => 'reedit_claim',
                    ],
                    'data' => [
                        'nomor_sep' => $nosep,
                    ],
                ];
                $response = json_decode(DocoHelpers::restInacbgs($reedit), true);
                if ($response['metadata']['code'] != 200) {
                    return $response;
                    // throw new \Exception("Terjadi Kesalahan");
                }
                $transaction->commit();
                return true;
            } else {
                throw new \Exception("\Terjadi Kesalahan");
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new \Exception("Terjadi Kesalahan");
        }
    }

    public function actionProsesEklaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $status = DocoConstants::STATUS_PROSES_KLAIM;
        $postData = ArrayHelper::getValue($post, 'data', []);
        $deleted = ArrayHelper::getValue($post, 'deleted', []);
        $primer = ArrayHelper::getValue($post, 'primer', []);

        try {
            // $pendaftaranId = ArrayHelper::getValue($postData, 'kunjungan_id');
            // $noPendaftaran = ArrayHelper::getValue($postData, 'no_pendaftaran');
            // $getDataKunjunganSy = $this->getDataKunjunganSy($noPendaftaran);
            $kunjunganId = ArrayHelper::getValue($postData, 'kunjungan_id');
            
            $dokterId = ArrayHelper::getValue($postData, 'dokter_id');
            $modelParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjunganId, 'is_deleted' => false])->one();
            if (!$modelParent) {
                $modelParent = new SyKlaimInacbg;
            }
            if (!empty($dokterId)) {
                $selectedDokter = $this->actionCariDokter($dokterId);
                $modelParent->dokterdpjp_id = ArrayHelper::getValue($selectedDokter, 'dokter_id');
                $modelParent->nama_dokter = ArrayHelper::getValue($selectedDokter, 'nama_pegawai');
                $post['data']['nama_dokter'] = ArrayHelper::getValue($selectedDokter, 'nama_pegawai');
            }

            $modelParent->attributes =  $postData;
            /* Rajal tidak bisa naik atau turun */
            $modelParent->is_naikkelas = false;
            /* hak kelas di tampung di naik_kelas dengan melihat is_naikkelas */
            $modelParent->naik_kelas = 'kelas_' . ArrayHelper::getValue($postData, 'kelas_bpjs');
            $modelParent->tarif_polieksekutif = ArrayHelper::getValue($postData, 'tarif_poli_eks', 0);
            // $kunjunganId = (int) $modelParent->pendaftaran_id;
            $eksDiagnosaPrimer = explode("#", $modelParent->diagnosa_primer);
            $eksDiagnosaSekunder = explode("#", $modelParent->diagnosa_sekunder);
            
            if (!empty($deleted)) {
                
                $arrDelDiag = [];
                $arrKode10 = [];
                $arrKode9 = [];
                foreach ($deleted as $key => $value) {
                    $kodeDiagnosa = ArrayHelper::getValue($value, 'kode_diagnosa');
                    $diagnosaType = ArrayHelper::getValue($value, 'diagnosa_type');
                    $arrDelDiag[] = ArrayHelper::getValue($value, 'diagnosa_id');
                    if ($diagnosaType == '10') {
                        $arrKode10[] = $kodeDiagnosa;
                    } else {
                        $arrKode9[] = $kodeDiagnosa;
                    }
                }
                
                $arrDelDiag = implode($arrDelDiag, ",");
                if ($arrKode10 || $arrKode9) {
                    $newDiagnosaPrimer = [];
                    $newDiagnosaSekunder = [];
                    if (!empty($primer)) {
                        $newDiagnosaPrimer[] = $primer;
                        array_push($arrKode10, $primer);
                    }
                    if ($arrKode10) {
                        foreach ($eksDiagnosaPrimer as $key => $value) {
                            if (!in_array($value, $arrKode10)) {
                                $newDiagnosaPrimer[$key + 1] = $value;
                            }
                        }
                        $modelParent->diagnosa_primer = implode($newDiagnosaPrimer, "#");
                        $post['data']['diagnosa_primer'] = $modelParent->diagnosa_primer;
                    }
                    if ($arrKode9) {
                        foreach ($eksDiagnosaSekunder as $key => $value) {
                            if (!in_array($value, $arrKode9)) {
                                $newDiagnosaSekunder[$key] = $value;
                            }
                        }
                        $modelParent->diagnosa_sekunder = implode($newDiagnosaSekunder, "#");
                        $post['data']['diagnosa_sekunder'] = $modelParent->diagnosa_sekunder;
                    }
                }
                if(!empty($arrDelDiag) && !empty($pendaftaranId)) {
                    $sql = 'update koreksidiagnosa_t SET is_deleted = true where diagnosa_id IN (' . $arrDelDiag . ') and pendaftaran_id = ' . $pendaftaranId . ' ';
                    $hapus = Yii::$app->db->createCommand($sql)->execute();
                }
            }  
            
            // set primer 
            if (!empty($primer)) {
                if(!isset($arrDelDiag)){
                    $primer = [];
                    $finalPrimer[] = $primer;
                    array_push($primer, $primer);
                    foreach ($eksDiagnosaPrimer as $key => $value) {
                        if (!in_array($value, $primer)) {
                            $finalPrimer[$key + 1] = $value;
                        }
                    }
                    $modelParent->diagnosa_primer = implode($finalPrimer, "#");
                    $post['data']['diagnosa_primer'] = $modelParent->diagnosa_primer;
                }
            }
            
            if(!$modelParent->validate()) {
                return $modelParent->errors;
            }

            $modelParent->save();

            if ($kunjunganId) {
                (new InternalService)->sendTo([
                    'Sirs' => [
                            'SinkronDataBpjs\TriggerFreezeBilling' => [
                                'type_sinkron' => 'confirm',
                                'kunjungan_id' => $kunjunganId
                            ]
                        ]
                ], true);
                $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => $status], ' kunjungan_id =' . $kunjunganId . '')->execute();
            } else {
                $transaction->rollBack();
                throw new \Exception("Pasien Tidak Ditemukan");
            }
            $result = $this->prosesBpjs($post['data']);
            $transaction->commit();
            return $result;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return json_encode($e->getMessage());
            throw new \Exception("Terjadi Kesalahan");
        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode($e->getMessage());
            throw new \Exception("Terjadi Kesalahan");
        }
    }
    
    public function prosesBpjs($data)
    {
        $jenisKelamin = ArrayHelper::getValue($data, 'jeniskelamin');
        $noSep = ArrayHelper::getValue($data, 'no_sep');
        $noKartu = ArrayHelper::getValue($data, 'no_kartu');

        $newClaim['metadata']['method'] = 'new_claim';
        $newClaim['data']['nomor_kartu'] = $noKartu;
        $newClaim['data']['nomor_sep'] = $noSep;
        $newClaim['data']['nomor_rm'] = ArrayHelper::getValue($data, 'no_rekam_medik');
        $newClaim['data']['nama_pasien'] = ArrayHelper::getValue($data, 'nama_pasien');
        $newClaim['data']['tgl_lahir'] = ArrayHelper::getValue($data, 'tgl_lahir');
        $newClaim['data']['gender'] = ($jenisKelamin == DocoConstants::LAKI) ? DocoConstants::JENIS_LAKI : DocoConstants::JENIS_PEREMPUAN;
        $response = json_decode(DocoHelpers::restInacbgs($newClaim), true);
        $metadata = ArrayHelper::getValue($response, 'metadata', []);
        $code = ArrayHelper::getValue($metadata, 'code');
        $errorNo = ArrayHelper::getValue($metadata, 'error_no');
        if(!empty($code)) {
            if ($code != 200) {
                if ($code != 400 && $errorNo != 'E2007') {
                    throw new \Exception("Terjadi Kesalahan");
                }
            }
        }
        $jenisRawat = ArrayHelper::getValue($data,"jenis_kelasrawat");
        $instalasi = ArrayHelper::getValue($data, "instalasi_kode");
        $prosesKlaim['metadata']['method'] = 'set_claim_data';
        $prosesKlaim['metadata']['nomor_sep'] = $noSep;
        $prosesKlaim['data'] = [
            'nomor_sep' => $noSep,
            'nomor_kartu' => $noKartu,
            'tgl_masuk' => ArrayHelper::getValue($data, 'tgl_masuk'),
            'tgl_pulang' => ArrayHelper::getValue($data, 'tgl_keluar'),
            'jenis_rawat' => DocoConstants::CLAIM_RAJAL,
            'kelas_rawat' => $jenisRawat,
            'birth_weight' => ArrayHelper::getValue($data, 'berat_lahir'),
            'discharge_status' => ArrayHelper::getValue($data, 'carapulang_id'),
            'diagnosa' => ArrayHelper::getValue($data, 'diagnosa_primer'),
            'procedure' => ArrayHelper::getValue($data, 'diagnosa_sekunder'),
            'nama_dokter' => ArrayHelper::getValue($data, 'nama_dokter'),
            "sistole" => 120,
            'diastole' => 70,
            'upgrade_class_payor' => 'peserta',
            'cara_masuk' => ArrayHelper::getValue($data, 'rujukanrs'),
            'ventilator' => '',
            'dializer_single_use' => '0',
            'kantong_darah' => 0,
            'apgar' => '',
            'persalinan' => '',
            'tarif_rs' => [
                'prosedur_non_bedah' => ArrayHelper::getValue($data, 'prosedur_nonbedah'),
                'prosedur_bedah' => ArrayHelper::getValue($data, 'prosedur_bedah'),
                'konsultasi' => ArrayHelper::getValue($data, 'konsultasi'),
                'tenaga_ahli' => ArrayHelper::getValue($data, 'tenaga_ahli'),
                'keperawatan' => ArrayHelper::getValue($data, 'keperawatan'),
                'penunjang' => ArrayHelper::getValue($data, 'penunjang'),
                'radiologi' => ArrayHelper::getValue($data, 'radiologi'),
                'laboratorium' => ArrayHelper::getValue($data, 'laboratorium'),
                'pelayanan_darah' => ArrayHelper::getValue($data, 'pelayanan_darah'),
                'rehabilitasi' => ArrayHelper::getValue($data, 'rehabilitasi'),
                'kamar' => ArrayHelper::getValue($data, 'kamar_akomodasi'),
                'rawat_intensif' => ArrayHelper::getValue($data, 'rawat_intensif'),
                'obat' => ArrayHelper::getValue($data, 'obat'),
                'obat_kronis' => ArrayHelper::getValue($data, 'obat_kronis'),
                'obat_kemoterapi' => ArrayHelper::getValue($data, 'obat_kemoterapi'),
                'alkes' => ArrayHelper::getValue($data, 'alkes'),
                'bmhp' => ArrayHelper::getValue($data, 'bmhp'),
                'sewa_alat' => ArrayHelper::getValue($data, 'sewa_alat'),
            ],
            'tarif_poli_eks' => ArrayHelper::getValue($data, 'tarif_poli_eks'),
            'kode_tarif' => ArrayHelper::getValue($data, 'tarif'),
            'payor_id' => $this->getPayorId(),
            'payor_cd' => $this->getPayorCd(),
            'coder_nik' => DocoConstants::INACBG_NIK,
        ];
        $klaim = json_decode(DocoHelpers::restInacbgs($prosesKlaim), true);
        $metadata = ArrayHelper::getValue($klaim, 'metadata', []);
        $code = ArrayHelper::getValue($metadata, 'code');
        $errorNo = ArrayHelper::getValue($metadata, 'error_no');
        $message = ArrayHelper::getValue($metadata, 'message');
        if(!empty($code)) {
            if ($code != 200) {
                $hapusklaim = [
                    'metadata' => [
                        'method' => 'delete_claim',
                    ],
                    'data' => [
                        'nomor_sep' => $noSep,
                        'coder_nik' => DocoConstants::INACBG_NIK,
                    ],
                ];
                $hapus = json_decode(DocoHelpers::restInacbgs($hapusklaim), true);
                return [
                    'status' => $code,
                    'title' => $errorNo,
                    'text' => $message
                ];
            }
            
        }
        
        $grouping['metadata']['method'] = 'grouper';
        $grouping['metadata']['stage'] = '1';
        $grouping['data']['nomor_sep'] = $noSep;
        $groupResult = json_decode(DocoHelpers::restInacbgs($grouping), true);
        $metadata = ArrayHelper::getValue($groupResult, 'metadata', []);
        $code = ArrayHelper::getValue($metadata, 'code');
        if ($code != 200) {
            throw new \Exception("Terjadi Kesalahan");
        }
        
        return json_encode($groupResult);
    }

    public function actionKirimKlaimOnline()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $kunjungan_id = ArrayHelper::getValue($post, 'pendaftaran_id');
        $nosep = ArrayHelper::getValue($post, 'nomor_sep');
        $connection = Yii::$app->db;
        try {
            $data = [
                'metadata' => [
                    'method' => 'send_claim_individual',
                ],
                'data' => [
                    'nomor_sep' => $nosep,
                ],
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            $metadata = ArrayHelper::getValue($response, 'metadata', []);
            $code = ArrayHelper::getValue($metadata, 'code');
            $message = ArrayHelper::getValue($metadata, 'message');
            if (!empty($code) && $code != 200) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => $message
                ];
            }

            $findParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjungan_id, 'is_deleted' => false])->one();
            $findParent->is_terkirim = true;
            if (!$findParent->save()) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Data gagal disimpan'
                ];
            }
            return true;
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionUpdateData()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $kunjungan_id = $post['kunjungan_id'];
        $no_sep       = $post['no_sep'];
        $no_kartu     = $post['no_kartu'];
        $kelas_kode   = $post['kelas_kode'];

        try {
            $model = SyKunjunganPasien::find()->where(['kunjungan_id' => $kunjungan_id])->one();
            $model->no_sep = $no_sep;
            $model->no_asuransi = $no_kartu;
            $model->hak_kelasbpjs = $kelas_kode;

            if (!$model->save()) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Proses update no sep gagal.'
                ];
            } else {
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil !',
                    'text' => 'Proses update no sep berhasil.'
                ];
            }
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan");
        }
    }

    public function actionDetail($id,$no_pendaftaran)
    {
        $old = [];
        $listId = [];
        $fl_detail = false;
        $model = SyKunjunganPasien::findOne($id);
        $detail = KoreksiDiagnosaNewView::find()->where(['kunjungan_id' => $id])->all();
        if (!$detail) {
            $fl_detail = true;
            $detail = SyKunjunganDetailView::find()->where([
                'kunjungan_id' => $id
            ])->all();
        } else {
            $old = SyKunjunganDetailView::find()->where([
                'kunjungan_id' => $id
            ])->all();
        }
        $tmp = [
            'diagnosa_utama' => [],
            'diagnosa_tambahan' => [],
            'diagnosa_opertindakan' => [],
            'diagnosa_luar' => [],
            'diagnosa_morfologi' => [],
        ];
        $tmp_old = [
            'diagnosa_utama' => [],
            'diagnosa_tambahan' => [],
            'diagnosa_opertindakan' => [],
            'diagnosa_luar' => [],
            'diagnosa_morfologi' => [],
        ];
        if ($old) {
            $i = 1;
            foreach ($old as $key => $value) {
                $inacbg = true;
                $icdprime = null;

                $diagnosa = InfoDiagnosa::find()->where([
                    'diagnosa_kode' => $value['diagnosa_kode']
                ])->select([
                    'diagnosa_id as id', 'diagnosa_kode as kode', 'diagnosa_nama as nama'
                ])->asArray()->one();

                if ($diagnosa) {
                    if (isset($value['is_inacbg']) || isset($value['is_icdprimer'])) {
                        $inacbg = $value['is_inacbg'];
                        $icdprime = $value['is_icdprimer'];
                    }
                    $diagnosa['is_inacbg'] = false;
                    $diagnosa['is_icdprimer'] = null;

                    $diagnosa['text'] = '';
                    $diagnosa_lama = $diagnosa['kode'] . ' - ' . $diagnosa['nama'];
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_UTAMA) {
                        if (!isset($tmp_old['diagnosa_utama'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_utama'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_TAMBAHAN) {
                        if (!isset($tmp_old['diagnosa_tambahan'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_tambahan'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }

                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_OPERTINDAKAN || $value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_K) {
                        if (!isset($tmp_old['diagnosa_opertindakan'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_opertindakan'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_LUAR) {
                        if (!isset($tmp_old['diagnosa_luar'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_luar'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_MORFOLOGI) {
                        if (!isset($tmp_old['diagnosa_morfologi'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_morfologi'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                }
            }
        }
        if ($detail) {
            $i = 1;
            foreach ($detail as $key => $value) {
                $inacbg = true;
                $icdprime = null;

                $diagnosa = InfoDiagnosa::find()->where([
                    'diagnosa_kode' => $value['diagnosa_kode']
                ])->select([
                    'diagnosa_id as id', 'diagnosa_kode as kode', 'diagnosa_nama as nama'
                ])->asArray()->one();
                if ($diagnosa) {
                    if (isset($value['is_inacbg']) || isset($value['is_icdprimer'])) {
                        $inacbg = $value['is_inacbg'];
                        $icdprime = $value['is_icdprimer'];
                    }
                    $diagnosa['is_inacbg'] = $inacbg;
                    $diagnosa['is_icdprimer'] = $icdprime;

                    $diagnosa['text'] = $diagnosa['kode'] . ' - ' . $diagnosa['nama'];

                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_UTAMA) {
                        if ($fl_detail) {
                            $diagnosa['is_icdprimer'] = true;
                        }
                        if (!isset($tmp['diagnosa_utama'][$diagnosa['kode']])) {
                            $tmp['diagnosa_utama'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false
                            ];
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_utama'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_TAMBAHAN) {
                        if (!isset($tmp['diagnosa_tambahan'][$diagnosa['kode']])) {
                            $tmp['diagnosa_tambahan'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false
                            ];
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_tambahan'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_OPERTINDAKAN || $value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_TERAPI) {
                        if (!isset($tmp['diagnosa_opertindakan'][$diagnosa['kode']])) {
                            $tmp['diagnosa_opertindakan'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false
                            ];
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_opertindakan'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_LUAR) {
                        if (!isset($tmp['diagnosa_luar'][$diagnosa['kode']])) {
                            $tmp['diagnosa_luar'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false
                            ];
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_luar'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_MORFOLOGI) {
                        if (!isset($tmp['diagnosa_morfologi'][$diagnosa['kode']])) {
                            $tmp['diagnosa_morfologi'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false
                            ];
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_morfologi'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                }
            }
        }
        $result = [];
        if ($old) {
            $i = 0;
            foreach ($tmp_old as $diagnosa => $value) {
                if (!isset($result[$diagnosa])) {
                    $result[$diagnosa] = [];
                }
                if ($tmp_old[$diagnosa]) {
                    $total_diagnosa_lama = count($tmp_old[$diagnosa]);
                    $total_diagnosa_baru = count($tmp[$diagnosa]);
                    $tmp_check = 1;
                    foreach ($tmp_old[$diagnosa] as $code => $old_value) {
                        $item = [
                            'id' => '',
                            'kode' => '',
                            'nama' => '',
                            'text' => '',
                            'diagnosa_lama' => '',
                            'is_inacbg' => false,
                            'is_icdprimer' => null,
                            'is_update' => true,
                        ];

                        if ($total_diagnosa_lama < $total_diagnosa_baru) {
                            if ($tmp_check <= $total_diagnosa_baru) {
                                foreach ($tmp[$diagnosa] as $new_code => $items) {
                                    if (isset($tmp[$diagnosa][$code]) && !$tmp[$diagnosa][$code]['is_update']) {
                                        $item['id'] = $tmp[$diagnosa][$code]['id'];
                                        $item['kode'] = $tmp[$diagnosa][$code]['kode'];
                                        $item['nama'] = $tmp[$diagnosa][$code]['nama'];
                                        $item['text'] = $tmp[$diagnosa][$code]['text'];
                                        $item['is_inacbg'] = $tmp[$diagnosa][$code]['is_inacbg'];
                                        $item['is_icdprimer'] = $tmp[$diagnosa][$code]['is_icdprimer'];
                                        $item['diagnosa_lama'] = $old_value['diagnosa_lama'];
                                        $tmp[$diagnosa][$code]['is_update'] = true;
                                        $tmp_check++;
                                        array_push($result[$diagnosa], $item);
                                    } else {
                                        if ($tmp_check <= $total_diagnosa_baru) {
                                            foreach ($tmp[$diagnosa] as $new_code => $items) {
                                                $item = [
                                                    'id' => '',
                                                    'kode' => '',
                                                    'nama' => '',
                                                    'text' => '',
                                                    'diagnosa_lama' => '',
                                                    'is_inacbg' => false,
                                                    'is_icdprimer' => null,
                                                    'is_update' => true,
                                                ];
                                                if (!$items['is_update']) {
                                                    $item['id'] = $items['id'];
                                                    $item['kode'] = $items['kode'];
                                                    $item['nama'] = $items['nama'];
                                                    $item['text'] = $items['text'];
                                                    $item['is_inacbg'] = $items['is_inacbg'];
                                                    $item['is_icdprimer'] = $items['is_icdprimer'];
                                                    $tmp[$diagnosa][$new_code]['is_update'] = true;
                                                    array_push($result[$diagnosa], $item);
                                                    $tmp_check++;
                                                    break;
                                                }
                                            }
                                        }else {
                                            $tmp[$diagnosa][$new_code]['is_update'] = true;
                                            array_push($result[$diagnosa], $item);
                                            $tmp_check++;
                                        }
                                    } 
                                }
                            }
                        } else {
                            if (isset($tmp[$diagnosa][$code]) && !$tmp[$diagnosa][$code]['is_update']) {
                                $item['id'] = $tmp[$diagnosa][$code]['id'];
                                $item['kode'] = $tmp[$diagnosa][$code]['kode'];
                                $item['nama'] = $tmp[$diagnosa][$code]['nama'];
                                $item['text'] = $tmp[$diagnosa][$code]['text'];
                                $item['is_inacbg'] = $tmp[$diagnosa][$code]['is_inacbg'];
                                $item['is_icdprimer'] = $tmp[$diagnosa][$code]['is_icdprimer'];
                                $item['diagnosa_lama'] = $old_value['diagnosa_lama'];
                                $tmp[$diagnosa][$code]['is_update'] = true;
                                $tmp_check++;
                                array_push($result[$diagnosa], $item);
                            } else {
                                if ($tmp_check <= $total_diagnosa_baru) {
                                    foreach ($tmp[$diagnosa] as $new_code => $items) {
                                        $item = [
                                            'id' => '',
                                            'kode' => '',
                                            'nama' => '',
                                            'text' => '',
                                            'diagnosa_lama' => $old_value['diagnosa_lama'],
                                            'is_inacbg' => false,
                                            'is_icdprimer' => null,
                                            'is_update' => true,
                                        ];
                                        if (!$items['is_update']) {
                                            $item['id'] = $items['id'];
                                            $item['kode'] = $items['kode'];
                                            $item['nama'] = $items['nama'];
                                            $item['text'] = $items['text'];
                                            $item['is_inacbg'] = $items['is_inacbg'];
                                            $item['is_icdprimer'] = $items['is_icdprimer'];
                                            $tmp[$diagnosa][$new_code]['is_update'] = true;
                                            array_push($result[$diagnosa], $item);
                                            $tmp_check++;
                                            break;
                                        }
                                    }
                                } else {
                                    $item['diagnosa_lama'] = $old_value['diagnosa_lama'];
                                    $tmp_check++;
                                    array_push($result[$diagnosa], $item);
                                }
                            }
                        }
                    }
                } else {
                    if ($tmp[$diagnosa]) {
                        foreach ($tmp[$diagnosa] as $code => $items) {
                            $item = [
                                'id' => $items['id'],
                                'kode' => $items['kode'],
                                'nama' => $items['nama'],
                                'text' => $items['text'],
                                'diagnosa_lama' => $items['diagnosa_lama'],
                                'is_inacbg' => $items['is_inacbg'],
                                'is_icdprimer' => $items['is_icdprimer'],
                                'is_update' => true,
                            ];
                            array_push($result[$diagnosa], $item);
                        }
                    }
                }
            }
        } else {
            if ($tmp) {
                foreach ($tmp as $diagnosa => $value) {
                    if (!isset($result[$diagnosa])) {
                        $result[$diagnosa] = [];
                    }
                    if ($tmp[$diagnosa]) {
                        foreach ($tmp[$diagnosa] as $code => $items) {
                            $item = [
                                'id' => $items['id'],
                                'kode' => $items['kode'],
                                'nama' => $items['nama'],
                                'text' => $items['text'],
                                'diagnosa_lama' => $items['diagnosa_lama'],
                                'is_inacbg' => $items['is_inacbg'],
                                'is_icdprimer' => $items['is_icdprimer'],
                                'is_update' => true,
                            ];
                            array_push($result[$diagnosa], $item);
                        }
                    }
                }
            }
        }

        return [
            'header' => $model,
            'detail' => $detail,
            'mapping' => DocoConstants::$mapp_kel_diagnosa,
            'hasil_diagnosa' => $result,
            'fl_detail' => $fl_detail,
            'kunjungan_id' => $id,
        ];
    }

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $term = $request->get('type');
        $word = $request->get('term');
        $not_in = $request->get('not_in') ? $request->get('not_in') : [];

        if ($term && $word) {
            return InfoDiagnosa::find()->where([
                'ILIKE', 'LOWER(tabularlist_versi)', strtolower($term)
            ])->andFilterWhere([
                'OR',
                ['ILIKE', 'LOWER(kode)', strtolower($word)],
                ['ILIKE', 'LOWER(diagnosa_kode)', strtolower($word)],
                ['ILIKE', 'LOWER(diagnosa_namalainnya)', strtolower($word)]
            ])->andWhere(['NOT IN', 'diagnosa_id', $not_in])->limit(10)->all();
        }
        return [];
    }

    public function actionGetKunjunganDetail($id)
    {
        try {
            $find = SyKunjungan::find()->where(['kunjungan_id' => $id])->andWhere(['<>', 'status_kunjungan', 0])->asArray()->one();
            return $find;
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan");
        }
    }

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $temp_inacbg = [];
        $transaction = $connection->beginTransaction();
        
        try {
            $kunjunganId = $request->post('kunjungan_id');
            $data_koreksi = $request->post('data_koreksi');
            $data_koreksi = json_decode($data_koreksi, true);
            $is_inacbg = $request->post('is_inacbg');
            $is_icdprimer = $request->post('is_icdprimer');
            $edit_koreksi = $request->get('edit_koreksi');
            $existKoreksi = SyKoreksiDiagnosa::find()
            ->where(['kunjungan_id' => $kunjunganId])
            ->one();

                $delete = (new SyKoreksiDiagnosa)->delete([
                    'kunjungan_id' => $kunjunganId
                ]);
                if (is_array($is_inacbg)) {
                    foreach ($is_inacbg as $key => $value) {
                        $temp_inacbg[] = $key;
                    }
                }
                if (is_array($data_koreksi)) {
                    $tmp = [];
                    $listCheck = [];
                    
                    $checkKasus = SyKoreksiDiagnosa::find()->where([
                        'kunjungan_id' => $kunjunganId
                    ])->asArray()->all();
                    
                    foreach ($checkKasus as $value) {
                        $listCheck[$value['diagnosa_id']] = true;
                    }
                    foreach ($data_koreksi as $value) {
                        $diagnosaId = ArrayHelper::getValue($value, 'diagnosa_id');
                        $diagnosaText = ArrayHelper::getValue($value, 'diagnosa_text');
                        $diagnosaKelompok = ArrayHelper::getValue($value, 'diagnosa_kelompok');

                        if (empty($diagnosaId)) continue;

                        $diagnosaKode = InfoDiagnosa::find()->where([
                            'diagnosa_id' => $diagnosaId
                        ])->select([
                            'diagnosa_kode'
                        ])->asArray()->one();
                        
                        $detailKunjungan = SyKunjunganDetailView::find()->where([
                            'kunjungan_id' => $kunjunganId,
                            'diagnosa_kode' => $diagnosaKode
                        ])->asArray()->one();
                        
                        $item = [
                            'kunjungan_id' => (int) $kunjunganId,
                            'tgl_koreksidiagnosa' => date('Y-m-d H:i:s'),
                            'kelompokdiagnosa_id' => (int) $diagnosaKelompok,
                            'diagnosa_id' => (int) $diagnosaId,
                            'diagnosaasal_id' => ArrayHelper::getValue($detailKunjungan, 'kunjungandetail_id'),
                            'diag_asal_masuk' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_TAMBAHAN ? @$diagnosaText : null,
                            'diag_asal_utama' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_UTAMA ? @$diagnosaText : null,
                            'diag_asal_penyerta' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_OPERTINDAKAN ? @$diagnosaText : null,
                            'diag_asal_terapi' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_LUAR ? @$diagnosaText : null,
                            'is_diagnosa_baru' => isset($listCheck[$diagnosaId]) ? false : true,
                            'is_inacbg' => false,
                            'is_icdprimer' => false,
                        ];
                        if (in_array($diagnosaId, $temp_inacbg)) {
                            $item['is_inacbg'] = true;
                        }
                        if ($is_icdprimer == $diagnosaId) {
                            $item['is_icdprimer'] = true;
                        }
                        array_push($tmp, $item);
                    }
                }

            SyKoreksiDiagnosa::batchInsert($tmp, false);
            $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => DocoConstants::STATUS_SUDAH_KOREKSI], ' kunjungan_id =' . $kunjunganId . '')->execute();
            $transaction->commit();
            return [
                'status' => 200,
                'message' => 'Data berhasil disimpan'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetKlaimGroup($id)
    {
        try {
            $find = KlaimInacbg::find()->select('klaimgroup_t.spesial_procedure, klaimgroup_t.spesial_prosthesis, klaimgroup_t.spesial_investigation, klaimgroup_t.spesial_drug')->join('INNER JOIN', 'klaimgroup_t', 'klaiminacbg_t.klaiminacbg_id = klaimgroup_t.klaiminacbg_id')->where(['klaiminacbg_t.pendaftaran_id' => $id, 'klaiminacbg_t.is_deleted' => false])->asArray()->one();
            return $find;
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan");
        }
    }

    public function actionCariDokter($dokter_id)
    {
        $dokter = Pegawai::find()->where([
            'pegawai_id' => $dokter_id,
            'kelompokpegawai_id' => 1
        ])->select([
            'pegawai_id', 'nama_pegawai', 'dokter_id'
        ])->asArray()->one();

        return $dokter;
    }

    public function actionSetPrimer()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $id = $post['id'];
        $dig = $post['diagnosa'];
        $connection = Yii::$app->db;
        try {
            $new = KoreksiDiagnosa::find()->where([
                'pendaftaran_id' => $id,
                'diagnosa_id' => $dig,
                'is_deleted' => false
            ])->one();

            $old = KoreksiDiagnosa::find()->where([
                'pendaftaran_id' => $id,
                'is_icdprimer' => true,
                'is_deleted' => false
            ])->one();

            if ($new && $old) {
                if ($new->diagnosa_id == $old->diagnosa_id) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Diagnosa Sudah Primer!'
                    ];
                }
                $new->is_icdprimer = true;
                $new->kelompokdiagnosa_id = DocoConstants::MAP_DIAGNOSA_UTAMA;
                $new->save();
                $old->kelompokdiagnosa_id = DocoConstants::MAP_DIAGNOSA_TAMBAHAN;
                $old->is_icdprimer = false;
                $old->save();
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil!',
                    'text' => 'Set Primer Berhasil.'
                ];
            }
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionAddDiagnosaTambahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        try {
            $koreksiTambahan = new SyKoreksiDiagnosa;
            $koreksiTambahan->kunjungan_id = (int) $post['pendaftaran_id'];
            $koreksiTambahan->kelompokdiagnosa_id = ($post['type'] == 10) ? (int) DocoConstants::MAP_DIAGNOSA_TAMBAHAN : (int) DocoConstants::MAP_DIAGNOSA_OPERTINDAKAN;
            $koreksiTambahan->diagnosa_id = (int) $post['diagnosa_id'];
            $koreksiTambahan->tgl_koreksidiagnosa = date('Y-m-d H:i:s');
            $koreksiTambahan->is_inacbg = true;
            $koreksiTambahan->is_icdprimer = false;
            $koreksiTambahan->is_diagnosa_baru = true;
            if ($koreksiTambahan->save(false)) {
                return true;
            }
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => $e->getMessage()
            ];
        }
    }

    private function getCoderNik()
    {
        $conf = @parse_ini_file(''.realpath(Yii::$app->basePath).'/config/env/.env', true);
        $vclaim = isset($conf['inacbg']['env_vclaim']) ? $conf['inacbg']['env_vclaim'] :'';
        $env = ($vclaim == DocoConstants::LOOKUP_BPJS_LIVE) ? DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;
        $result = [];
        $coderNik = '';
        try {
            $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => $env, 'name' => 'coder_nik']);
            $result = isset($result['response']) ? $result['response'] : [];
            foreach($result as $res){
                $coderNik = $res['lookup_value'];
            }
            return $coderNik;
        } catch (\Exception $e) {
            return $result;
        }
    }

    public function actionGetListDiagnosa() 
    {
        try{
            $request = Yii::$app->request;
            $model = new DiagnosaView;
            $diagnosa = $model::find()->select(['diagnosa_id', 'diagnosa_kode', 'diagnosa_nama', 'tabularlist_versi'])->all();

            return $response = [
                'diagnosa'   => $diagnosa,
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => $e->getMessage()
            ];
        }
    }

    public function actionUploadBerkas()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        // $connection = Yii::$app->db;
        try {
            $data = [
                'metadata' => [
                    'method' => 'file_upload',
                    "nomor_sep" => $post['nosep'],
                    "file_class" => $post['label'],
                    "file_name" => $post['filename']
                ],
                'data' => $post['data']
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            return $response;

        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionDeleteBerkas()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        try {
            $data = [
                'metadata' => [
                    'method' => 'file_delete',
                ],
                'data' => [
                    'nomor_sep' => $post['nosep'],
                    'file_id'   => $post['file_id']
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            return $response['metadata'];

        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionGetBerkas()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $data = [
                'metadata' => [
                    'method' => 'file_get',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep']
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            return isset($response['response']) ? $response['response'] : $response;   

        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }
    
    private function getPayorId()
    {
        $staticPayorId = 3;
        $payorId = null;
        $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'bpjs_live', 'name' => 'payor_id']);
        $result = isset($result['response']) ? $result['response'] : [];

        foreach($result as $res){
            $payorId = $res['lookup_value'];
        }

        return (!empty($payorId) && !is_null($payorId)) ? $payorId : $staticPayorId;
    }

    private function getPayorCd()
    {
        $staticPayorCd = 3;
        $payorCd = null;
        $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'bpjs_live', 'name' => 'payor_cd']);
        $result = isset($result['response']) ? $result['response'] : [];
        
        foreach($result as $res){
            $payorCd = $res['lookup_value'];
        }

        return (!empty($payorCd) && !is_null($payorCd)) ? $payorCd : $staticPayorCd;
    }

    public function actionFilters()
    {
        
        $payload = Yii::$app->request->get('additionalPayload', []);
        $page = Yii::$app->request->get('page', 1);
        $type = Yii::$app->request->get('type', null);
        $term = Yii::$app->request->get('term', null);
        $case = Yii::$app->request->get('case', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $result = [];
        if($type == 'ruangan') {
            if($case == strtolower('ruangan_nama')) {
                $result = Ruangan::find()->select(['ruangan_nama AS id', 'ruangan_nama AS text'])->where(['is_active' => true]);
            }else {
                $result = Ruangan::find()->select(['ruangan_id AS id', 'ruangan_nama AS text'])->where(['is_active' => true]);
            }
            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
            }

            $result->orderBy(['ruangan_nama' => SORT_ASC]);
        }
        elseif($type == 'status') {
            $result = Lookup::find()
                ->select(['lookup_id AS id', 'lookup_name AS text'])
                ->where(['lookup_type' => 'status_verifikasi', 'is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
            }

            $result->orderBy(['lookup_name' => SORT_ASC]);
        }
        
        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }

    private function getDataKunjunganSy($noPendaftaran)
    {
        $result = [];
        if(!empty($noPendaftaran)) {
            $result = Yii::$app->db->createCommand("
                SELECT kunjungan_id 
                FROM sy_kunjungan 
                WHERE no_pendaftaran = '".$noPendaftaran."' 
            ")->queryOne();
        }
        return $result;
    }

    public function actionValidasiSitb()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $data = [
                'metadata' => [
                    'method' => 'sitb_validate',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep'],
                    "nomor_register_sitb" => ArrayHelper::getValue($get, 'nomer_sitb') 
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            if($response['metadata']['code'] == 400) {
                return [
                    "code" => $response['metadata']['code'],
                    "message" => $response['metadata']['message'],
                    "response" => []
                ];
            }

            return [
                "code" => $response['metadata']['code'],
                "message" => $response['metadata']['message'],
                "response" => $response['response']
            ];
                
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionUpdateNoklaim()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $kunjunganId = ArrayHelper::getValue($get, 'kunjungan_id');

            
            if(!empty($kunjunganId)) {
                $existingData = SyKunjunganPasien::find()
                ->where(['kunjungan_id'  => $kunjunganId])
                ->one();

                if($existingData) {
                    $existingData->no_klaimcovid = null;
                    $existingData->save();

                    return  [
                        'status' => 200,
                        'message' => "Data berhasil di update!"
                    ];
                }

                return  [
                    'status' => 200,
                    'message' => "Data ditemukan, Namun tidak di update!"
                ];
            }

            return  [
                'status' => 200,
                'message' => "Data kunjungan tidak ditemukan"
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }
}
