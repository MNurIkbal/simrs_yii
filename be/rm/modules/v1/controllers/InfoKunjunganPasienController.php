<?php

namespace app\modules\v1\controllers;

use App\Helpers\DocoConstant;
use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoKoreksiPasien;
use app\modules\v1\models\InfoKoreksiPasienDetail;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\InfoDiagnosa;
use app\modules\v1\models\KoreksiDiagnosa;
use app\modules\v1\models\InfoKoreksiDiagnosa;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use yii\data\ArrayDataProvider;

class InfoKunjunganPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoKoreksiPasien';
    const ALLOW_FILTER_VERIFIKASI = [549,550]; 

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
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
        
        $model = new InfoKoreksiPasien;
        $query = $model::find();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $start2 = date('Y-m-d 00:00:00');
        $end2 = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
                $between = true;
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }

            if(isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $explode2 = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                if(count($explode2) == 2) {
                    $start2 = date('Y-m-d 00:00:00',strtotime($explode2[0]));
                    $end2 = date('Y-m-d 23:59:00',strtotime($explode2[1]));
                }
                unset($_GET['advanced-filter']['tglpasienpulang']);
                $between = true;
                // $query->andWhere(['instalasi_id' => DocoConstants::VAR_I_RANAP]);
                // $query->andWhere(['between', 'tglpasienpulang', $start2, $end2]);
            }

            if(isset($_GET['advanced-filter']['diagnosadokter_utama'])) {
                $q = $_GET['advanced-filter']['diagnosadokter_utama'];
                unset($_GET['advanced-filter']['diagnosadokter_utama']);
            }

            if(isset($_GET['advanced-filter']['instalasi_id']) && $_GET['advanced-filter']['instalasi_id'] == DocoConstants::VAR_I_RANAP) {
                $query->andWhere(['not', ['pasienadmisi_id' => null]]);
                unset($_GET['advanced-filter']['instalasi_id']);
            }

            if(isset($_GET['advanced-filter']['instalasi_id']) && $_GET['advanced-filter']['instalasi_id'] == DocoConstants::VAR_I_RD) {
                $query->andWhere(['pasienadmisi_id' => null]);
            }
        }
        $query->andWhere(['between', 'tglpasienpulang', $start2, $end2]);
        if(isset($q)) {
            // $query->andWhere(['ilike', 'diagnosa_utama', $q]);
            $condition = "'%$q%'";
            $query->andWhere(new \yii\db\Expression('diagnosadokter_utama::text ILIKE '.$condition.''));
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => false,
        // ]);
        $tmpData = $query->all();
        
        return $this->generateData($tmpData);
    }

    public function actionGetRequest()
    {
        $request = Yii::$app->request;

        $modelCaraBayar = CaraBayar::find();
        $queryCaraBayar = $modelCaraBayar->where([
            'is_active' => true
        ])->all();

        $modelPenjamin = Penjamin::find();
        $queryPenjamin = $modelPenjamin->where([
            'is_active' => true
        ])->all();

        $instalasi = Instalasi::find();
        $instalasi = $instalasi->where([
            'is_active' => true,
            'instalasi_id' => [1,2,3]
        ])->all();

        $ruangan = Ruangan::find();
        $ruangan = $ruangan->where([
            'is_active' => true,
            'instalasi_id' => [1,2,3]
        ])->all();

        $statusverifikasi = Lookup::find(true
        )->where(['lookup_type'=>'status_verifikasi'])->andWhere(['in', 'lookup_id', self::ALLOW_FILTER_VERIFIKASI])
        ->asArray()
        ->all();

        $jk = Lookup::find(true)
        ->where(['lookup_type'=>'jenis_kelamin'])
        ->asArray()
        ->all();

        return [
            'penjamin' => ArrayHelper::map($queryPenjamin, 'penjamin_id', 'penjamin_nama'),
            'cara_bayar' => ArrayHelper::map($queryCaraBayar, 'carabayar_id', 'carabayar_nama'),
            'instalasi' => ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
            'ruangan' => ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'),
            'status_verivikasi' => ArrayHelper::map($statusverifikasi, 'lookup_id', 'lookup_name'),
            'jenis_kelamin' => ArrayHelper::map($jk, 'lookup_name', 'lookup_name')
        ];
    }

    public function actionDetail($id,$admisi)
    {
        $admisi = $admisi ? $admisi : null;
        $model = InfoKoreksiPasien::find()->where([
            'pendaftaran_id' => $id,
            'pasienadmisi_id' => $admisi
        ])->one();

        $detail = InfoKoreksiPasienDetail::find()->where([
            'pendaftaran_id' => $id,
            'pasienadmisi_id' => $admisi
        ])->orderBy([
            'diagnosapasien_id' => SORT_DESC
        ]);
        $tmp = [
            'diagnosa_utama' => [],
            'diagnosa_masuk' => [],
            'diagnosa_penyerta' => [],
            'diagnosa_terapi' => [],

        ];
        if ($admisi) {
            $detail = $detail->one();
            $tmp['diagnosa_utama'] = is_array($detail['diagnosa_utama']) ? $detail['diagnosa_utama'] : json_decode($detail['diagnosa_utama'], true);
            $tmp['diagnosa_masuk'][] = is_array($detail['diagnosa_masuk']) ? $detail['diagnosa_masuk'] : json_decode($detail['diagnosa_masuk'], true);
            $tmp['diagnosa_penyerta'] = is_array($detail['diagnosa_penyerta']) ? $detail['diagnosa_penyerta'] : json_decode($detail['diagnosa_penyerta'], true);
            $tmp['diagnosa_terapi'] = is_array($detail['diagnosa_terapi']) ? $detail['diagnosa_terapi'] : json_decode($detail['diagnosa_terapi'], true);
            if (empty($tmp['diagnosa_masuk'])) {
                $tmp['diagnosa_masuk'][] = [];
            }

            if (empty($tmp['diagnosa_penyerta'])) {
                $tmp['diagnosa_penyerta'][] = [];
            }

            if (empty($tmp['diagnosa_terapi'])) {
                $tmp['diagnosa_terapi'][] = [];
            }

        } else {
            $detail = $detail->all();

            if (count($detail) == 1) {
                foreach ($detail as $value) {
                    if (!empty($value['diagnosa_utama']) && empty($tmp['diagnosa_utama'])) {
                        $tmp['diagnosa_utama'] = is_array($value['diagnosa_utama']) ? $value['diagnosa_utama'] : json_decode($value['diagnosa_utama'], true);
                    }
                    if (!empty($value['diagnosa_masuk'])) {
                        if(DocoHelpers::isJson($value['diagnosa_masuk']) != true) {
                            $diagMasuk = explode(',', $value['diagnosa_masuk']);
                            $tmp['diagnosa_masuk'][] = is_array($value['diagnosa_masuk']) ? $value['diagnosa_masuk'] : [
                                'id' => $diagMasuk[0],
                                'text' => $diagMasuk[1]
                            ];
                        } else {
                            $tmp['diagnosa_masuk'] = is_array($value['diagnosa_masuk']) ? $value['diagnosa_masuk'] : json_decode($value['diagnosa_masuk'], true);
                        }
                    }
                    if (!empty($value['diagnosa_penyerta'])) {
                        $tmp['diagnosa_penyerta'] = is_array($value['diagnosa_penyerta']) ? $value['diagnosa_penyerta'] : json_decode($value['diagnosa_penyerta'], true);
                    }
                    if (!empty($value['diagnosa_terapi'])) {
                        $tmp['diagnosa_terapi'] = is_array($value['diagnosa_terapi']) ? $value['diagnosa_terapi'] : json_decode($value['diagnosa_terapi'], true);
                    }
                }
            } else {
                foreach ($detail as $value) {
                    if (!empty($value['diagnosa_utama']) && empty($tmp['diagnosa_utama'])) {
                        $tmp['diagnosa_utama'] = is_array($value['diagnosa_utama']) ? $value['diagnosa_utama'] : json_decode($value['diagnosa_utama'], true);
                    }
                    if (!empty($value['diagnosa_masuk'])) {
                        $tmp['diagnosa_masuk'][] = is_array($value['diagnosa_masuk']) ? $value['diagnosa_masuk'] : json_decode($value['diagnosa_masuk'], true);
                    }
                    if (!empty($value['diagnosa_penyerta'])) {
                        $tmp['diagnosa_penyerta'][] = is_array($value['diagnosa_penyerta']) ? $value['diagnosa_penyerta'] : json_decode($value['diagnosa_penyerta'], true);
                    }
                    if (!empty($value['diagnosa_terapi'])) {
                        $tmp['diagnosa_terapi'][] = is_array($value['diagnosa_terapi']) ? $value['diagnosa_terapi'] : json_decode($value['diagnosa_terapi'], true);
                    }
                }
            }

            if (empty($tmp['diagnosa_masuk'])) $tmp['diagnosa_masuk'][] = [];
            if (empty($tmp['diagnosa_penyerta'])) $tmp['diagnosa_penyerta'][] = [];
            if (empty($tmp['diagnosa_terapi'])) $tmp['diagnosa_terapi'][] = [];
        }
        
        if (!empty($tmp['diagnosa_penyerta']) && !empty($tmp['diagnosa_penyerta'][0]) && !isset($tmp['diagnosa_penyerta'][0][0])) {
            $tmpDiag = $diagnosa =  $diagnosa_id = [];
            foreach ($tmp['diagnosa_penyerta'] as $key => $value) {
                $idDiag = isset($value['id']) ? $value['id'] : null;
                $cleanId = preg_replace('/[^0-9]/', '', $idDiag);
                if(!empty($cleanId)) {
                    $diagnosa_id[] = (int) $value['id'];
                } else if (isset($value['text']) && empty($cleanId)) {
                    $diagOnlyText = [
                        'text' => $value['text']
                    ];
                    array_push($tmpDiag, $diagOnlyText);
                }
            }

            if(!empty($diagnosa_id)) {
                $diagnosa = (new \yii\db\Query())
                ->select([
                    'diagnosa_id AS id',
                    'diagnosa_kode AS kode',
                    'diagnosa_namalainnya AS nama',
                    "CONCAT(diagnosa_kode, ' - ', diagnosa_namalainnya) as text"
                ])
                ->from('diagnosa_m')
                ->where(['IN', 'diagnosa_id', $diagnosa_id])
                ->all();
            }

            if(!empty($tmpDiag)) {
                for ($i=0; $i < count($tmpDiag); $i++) { 
                    array_push($diagnosa, $tmpDiag[$i]);
                }
            }

            $tmp['diagnosa_penyerta'] =  $diagnosa;
        } else if (!empty($tmp['diagnosa_penyerta']) && !empty($tmp['diagnosa_penyerta'][0]) && isset($tmp['diagnosa_penyerta'][0][0])) {
            $tmpDiag = $diagnosa = $diagnosa_id = [];
            foreach ($tmp['diagnosa_penyerta'] as $key => $value) {
                $idDiag = isset($value['id']) ? $value['id'] : null;
                $cleanId = preg_replace('/[^0-9]/', '', $idDiag);
                if(!empty($cleanId)) {
                    $diagnosa_id[] = (int) $value['id'];
                } else if (isset($value['text']) && empty($cleanId)) {
                    $diagOnlyText = [
                        'text' => $value['text']
                    ];
                    array_push($tmpDiag, $diagOnlyText);
                }
            }

            if(!empty($diagnosa_id)) {
                $diagnosa = (new \yii\db\Query())
                ->select([
                    'diagnosa_id AS id',
                    'diagnosa_kode AS kode',
                    'diagnosa_namalainnya AS nama',
                    "CONCAT(diagnosa_kode, ' - ', diagnosa_namalainnya) as text"
                ])
                ->from('diagnosa_m')
                ->where(['IN', 'diagnosa_id', $diagnosa_id])
                ->all();
            }

            if(!empty($tmpDiag)) {
                for ($i=0; $i < count($tmpDiag); $i++) { 
                    array_push($diagnosa, $tmpDiag[$i]);
                }
            }

            if (empty($diagnosa)) {
                $tmp['diagnosa_penyerta'][0] = [];
            }else{
                $tmp['diagnosa_penyerta'] =  $diagnosa;
            }
        }

        if (!empty($tmp['diagnosa_masuk']) && !empty($tmp['diagnosa_masuk'][0])) {
            $tmpDiag = $diagnosa = $diagnosa_id = [];
            foreach ($tmp['diagnosa_masuk'] as $key => $value) {
                $idDiag = isset($value['id']) ? $value['id'] : null;
                $cleanId = preg_replace('/[^0-9]/', '', $idDiag);
                if(!empty($cleanId)) {
                    $diagnosa_id[] = (int) $value['id'];
                } else if (isset($value['text']) && empty($cleanId)) {
                    $diagOnlyText = [
                        'text' => $value['text']
                    ];
                    array_push($tmpDiag, $diagOnlyText);
                }
            }

            if(!empty($diagnosa_id)) {
                $diagnosa = (new \yii\db\Query())
                ->select([
                    'diagnosa_id AS id',
                    'diagnosa_kode AS kode',
                    'diagnosa_namalainnya AS nama',
                    "CONCAT(diagnosa_kode, ' - ', diagnosa_namalainnya) as text"
                ])
                ->from('diagnosa_m')
                ->where(['IN', 'diagnosa_id', $diagnosa_id])
                ->all();
            }

            if(!empty($tmpDiag)) {
                for ($i=0; $i < count($tmpDiag); $i++) { 
                    array_push($diagnosa, $tmpDiag[$i]);
                }
            }

            $tmp['diagnosa_masuk'] =  $diagnosa;
        }

        if (!empty($tmp['diagnosa_terapi']) && !empty($tmp['diagnosa_terapi'][0])) {
            $tmpDiag = $diagnosa = $diagnosa_id = [];
            foreach ($tmp['diagnosa_terapi'] as $key => $value) {
                $idDiag = isset($value['id']) ? $value['id'] : null;
                $cleanId = preg_replace('/[^0-9]/', '', $idDiag);
                if(!empty($cleanId)) {
                    $diagnosa_id[] = (int) $value['id'];
                } else if (isset($value['text']) && empty($cleanId)) {
                    $diagOnlyText = [
                        'text' => $value['text']
                    ];
                    array_push($tmpDiag, $diagOnlyText);
                }
            }

            if(!empty($diagnosa_id)) {
                $diagnosa = (new \yii\db\Query())
                ->select([
                    'diagnosa_id AS id',
                    'diagnosa_kode AS kode',
                    'diagnosa_namalainnya AS nama',
                    "CONCAT(diagnosa_kode, ' - ', diagnosa_namalainnya) as text"
                ])
                ->from('diagnosa_m')
                ->where(['IN', 'diagnosa_id', $diagnosa_id])
                ->all();
            }

            if(!empty($tmpDiag)) {
                for ($i=0; $i < count($tmpDiag); $i++) { 
                    array_push($diagnosa, $tmpDiag[$i]);
                }
            }

            $tmp['diagnosa_terapi'] =  $diagnosa;
        }

        $detail = $tmp;
        $hasil = InfoKoreksiDiagnosa::find()->where([
            'pendaftaran_id' => $id,
            'pasienadmisi_id' => $admisi
        ])->all();
        $hasilDiagnosa = [];
        $tmpHasilDiagnosa = [
            'diagnosa_utama' => [],
            'diagnosa_masuk' => [],
            'diagnosa_penyerta' => [],
            'diagnosa_terapi' => [],

        ];
        foreach ($hasil as $value) {
            $kelompok = $value['kelompokdiagnosa_id'];
            $valArr = [$value['diagnosa_id'] => $value['diagnosa_kode'] .' - '. $value['diagnosa_nama']];
            switch ($kelompok) {
                case 1:
                    $key = $kelompok .'-'. $value['diag_asal_masuk'];
                    array_push($tmpHasilDiagnosa['diagnosa_masuk'], [
                        'id' => $value['diagnosa_id'],
                        'kode' => $value['diagnosa_kode'],
                        'nama' => $value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'] .' - '. $value['diagnosa_nama']
                    ]);
                    break;
                case 2:
                    $key = $kelompok .'-'. $value['diag_asal_utama'];
                    array_push($tmpHasilDiagnosa['diagnosa_utama'], [
                        'id' => $value['diagnosa_id'],
                        'kode' => $value['diagnosa_kode'],
                        'nama' => $value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'] .' - '. $value['diagnosa_nama']
                    ]);
                    break;
                case 3:
                    $key = $kelompok .'-'. $value['diag_asal_penyerta'];
                    array_push($tmpHasilDiagnosa['diagnosa_penyerta'], [
                        'id' => $value['diagnosa_id'],
                        'kode' => $value['diagnosa_kode'],
                        'nama' => $value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'] .' - '. $value['diagnosa_nama']
                    ]);
                    break;
                default:
                    $key = $kelompok .'-'. $value['diag_asal_terapi'];
                    array_push($tmpHasilDiagnosa['diagnosa_terapi'], [
                        'id' => $value['diagnosa_id'],
                        'kode' => $value['diagnosa_kode'],
                        'nama' => $value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'] .' - '. $value['diagnosa_nama']
                    ]);
                    break;
            }
            $hasilDiagnosa[$key] = $valArr;
        }

        if (empty($tmpHasilDiagnosa['diagnosa_masuk'])) $tmpHasilDiagnosa['diagnosa_masuk'][] = [];
        if (empty($tmpHasilDiagnosa['diagnosa_penyerta'])) $tmpHasilDiagnosa['diagnosa_penyerta'][] = [];
        if (empty($tmpHasilDiagnosa['diagnosa_terapi'])) $tmpHasilDiagnosa['diagnosa_terapi'][] = [];

        $tmpAllDiagnosa = [];
        if(!empty($tmpHasilDiagnosa)) {
            $tmpAllDiagnosa = $this->compareDiagnosa($detail, $tmpHasilDiagnosa);
        }

        return [
            'header' => $model,
            'detail' => $detail,
            'mapping' => DocoConstants::$_mapp_kel_diagnosa,
            'hasil_diagnosa' => $hasilDiagnosa,
            'fl_detail' => $tmpAllDiagnosa
        ];
    }

    public function actionSave($id, $admisi)
    {
        $admisi = $admisi ? $admisi : null;
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $data_koreksi = $request->post('data_koreksi');
            $data_koreksi = json_decode($data_koreksi,true);
            $delete = (new KoreksiDiagnosa)->delete([
                'pendaftaran_id' => $id,
                'pasienadmisi_id' => $admisi
            ]);
            if (is_array($data_koreksi)) {
                $tmp = [];
                $pasien_id = $request->post('pasien_id');
                $checkKasus = KoreksiDiagnosa::find()->where([
                    'pasien_id' => $pasien_id
                ])->asArray()->all();
                $listCheck = [];
                foreach ($checkKasus as $value) {
                    $listCheck[$value['diagnosa_id']] = true;
                }
                $dpjp = $request->post('dokter_dpjp_id');
                foreach ($data_koreksi as $value) {
                    if (empty($value['diagnosa_id'])) continue;
                    $tmp[] = [
                        'pendaftaran_id' => (int) $id,
                        'pasienadmisi_id' => !empty($admisi) ? $admisi : null,
                        'pasien_id' => (int) $pasien_id,
                        'dokterdpjp_id' => (int) $dpjp,
                        'tgl_koreksidiagnosa' => date('Y-m-d H:i:s'),
                        'kelompokdiagnosa_id' => (int) $value['diagnosa_kelompok'],
                        'diagnosa_id' => (int) $value['diagnosa_id'],
                        'diagnosaasal_id' => !empty($value['diagnosa_asal']) && is_numeric($value['diagnosa_asal']) ? $value['diagnosa_asal'] : null,
                        'diag_asal_masuk' => $value['diagnosa_kelompok'] == 1 ? @$value['diagnosa_text'] : null,
                        'diag_asal_utama' => $value['diagnosa_kelompok'] == 2 ? @$value['diagnosa_text'] : null,
                        'diag_asal_penyerta' => $value['diagnosa_kelompok'] == 3 ? @$value['diagnosa_text'] : null,
                        'diag_asal_terapi' => $value['diagnosa_kelompok'] == 6 ? @$value['diagnosa_text'] : null,
                        'is_diagnosa_baru' => isset($listCheck[$value['diagnosa_id']]) ? false : true,
                        'additional_data' => json_encode($data_koreksi)
                    ];
                }
            }
            $status = DocoConstants::SUDAH_KOREKSI;
            KoreksiDiagnosa::batchInsert($tmp,false);
            if($admisi){
                Yii::$app->db->createCommand("
                    UPDATE pasienadmisi_t SET status_verifikasi = {$status} WHERE pasienadmisi_id = {$admisi}
                ")->execute();
            } else {
                Yii::$app->db->createCommand("
                    UPDATE pendaftaran_t SET status_verifikasi = {$status} WHERE pendaftaran_id = {$id}
                ")->execute();
            }
            $transaction->commit();
            return [
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

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $term = $request->get('type');
        $word = $request->get('term');
        if ($term && $word) {
            return InfoDiagnosa::find()->where([
                'ILIKE', 'LOWER(tabularlist_versi)', strtolower($term)
            ])->andFilterWhere([
                'OR',
                ['ILIKE', 'LOWER(diagnosa_kode)', strtolower($word)],
                ['ILIKE', 'LOWER(diagnosa_namalainnya)', strtolower($word)]
            ])->limit(10)->all();
        }
        return [];
    }

    private function generateData($tmpData)
    {
        $request = Yii::$app->request;
        $data = [];

        if(!empty($tmpData)) {
            $tmp = $diag = [];
            foreach($tmpData as $k => $v) {
                $pendaftranId = $v['pendaftaran_id'];
                $admisi = $v['pasienadmisi_id'];
                $jenis = $v['jenis_rawat'];

                if(!isset($tmp[$jenis][$pendaftranId])) {
                    $tmp[$jenis][$pendaftranId] = $v;
                    $diag[$jenis][$pendaftranId] = [];
                }

                if(!empty($v['diagnosa_penyerta_id'])) {
                    if(!isset($diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA][$v['diagnosa_penyerta_id']])) {
                        $diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA][$v['diagnosa_penyerta_id']][] = [
                            'id' => $v['diagnosa_penyerta_id'],
                            'text' => $v['diagnosa_penyerta'],
                            'type' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA
                        ];
                    }
                }

                if(!empty($v['diagnosa_masuk_id'])) {
                    if(!isset($diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK][$v['diagnosa_masuk_id']])) {
                        $diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK][$v['diagnosa_masuk_id']][] = [
                            'id' => $v['diagnosa_masuk_id'],
                            'text' => $v['diagnosa_masuk'],
                            'type' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK
                        ];
                    }
                }

                if(!empty($v['diagnosa_utama_id'])) {
                    if(!isset($diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA][$v['diagnosa_utama_id']])) {
                        $diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA][$v['diagnosa_utama_id']][] = [
                            'id' => $v['diagnosa_utama_id'],
                            'text' => $v['diagnosa_utama'],
                            'type' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA
                        ];
                    }
                }

                if(!empty($v['diagnosa_terapi_id'])) {
                    if(!isset($diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI][$v['diagnosa_terapi_id']])) {
                        $diag[$jenis][$pendaftranId][DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI][$v['diagnosa_terapi_id']][] = [
                            'id' => $v['diagnosa_terapi_id'],
                            'text' => $v['diagnosa_terapi'],
                            'type' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI
                        ];
                    }
                }
               
                $tmp[$jenis][$pendaftranId]['diagnosa_coding'] = json_encode($diag[$jenis][$pendaftranId]);
                // $data[] = $tmp[$jenis][$pendaftranId];
            }

            if(!empty($tmp)) {
                foreach($tmp as $k => $v) {
                    foreach($v as $kk => $vv) {
                        $data[] = $vv;
                    }
                }
            }
        }
        
        return new ArrayDataProvider([
            'allModels' => $data, 
            'pagination' => [
                'pageSize' => $request->get('per-page'),
            ],
        ]);
    }

    private function compareDiagnosa($old, $new)
    {
        foreach($new as $key => $value) {
            if($key != 'diagnosa_utama') {
                foreach($value as $k => $v) {
                    if(isset($new[$key][$k]['text']) && !isset($old[$key][$k]['text'])) {
                        $old[$key][$k]['text'] = '-';
                    } 
                }
            }
        }
        return $old;
    }

    public function actionGetListDiagnosa() {
        try{
            $request = Yii::$app->request;
            $model = new InfoDiagnosa;
            $diagnosa = $model::find()->select([
                'diagnosa_id', 
                'diagnosa_kode', 
                'diagnosa_nama', 
                'tabularlist_versi', 
                'tabularlist_chapter', 
                'lower(diagnosa_nama) as diagnosa_lower', 
                'LOWER(diagnosa_kode) as diagnosa_kode_lower',
                'validcode',
                'ina_grouper'
            ])->asArray()->all();

            return [
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
}
