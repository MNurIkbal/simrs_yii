<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\KlaimInacbg;
use app\modules\v1\models\KlaimInacbgGroup;
use app\modules\v1\models\KoreksiDiagnosa;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\SyKlaimGroup;
use app\modules\v1\models\SyKlaimInacbg;
use app\modules\v1\models\SyKunjunganPasien;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use Yii;
use yii\helpers\ArrayHelper;

class InfPasienRajalBpjsCovidController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienBpjsView';
    public $vclaim = '';

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
        return $actions;
    }

    public function actionProsesEklaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $status = DocoConstants::STATUS_PROSES_KLAIM;
        $listIdentitas = DocoConstants::LIST_IDENTITAS_PASIEN;
        $listStatusCovid = DocoConstants::LIST_STATUS_COVID;

        try {
            $modelParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $post['data']['kunjungan_id'], 'is_deleted' => false])->one();
            if (!$modelParent) {
                $modelParent = new SyKlaimInacbg;
            }
            if (!empty($post['data']['dokter_id'])) {
                $selectedDokter = $this->actionCariDokter($post['data']['dokter_id']);
                $modelParent->dokterdpjp_id = $selectedDokter['dokter_id'];
                $modelParent->nama_dokter = $selectedDokter['nama_pegawai'];
                $post['data']['nama_dokter'] = $selectedDokter['nama_pegawai'];
            }
            $modelParent->attributes = $post['data'];
            /* Rajal tidak bisa naik atau turun */
            $modelParent->is_naikkelas = false;
            /* hak kelas di tampung di naik_kelas dengan melihat is_naikkelas */
            $modelParent->naik_kelas = 'kelas_' . $post['data']['kelas_bpjs'];
            $modelParent->tarif_polieksekutif = isset($post['data']['tarif_poli_eks']) ? $post['data']['tarif_poli_eks'] : 0;
            $post['data']['nomor_kartu_t'] = ArrayHelper::getValue($listIdentitas, $post['data']['identitas_id']);
            $post['data']['covid19_status_cd'] = ArrayHelper::getValue($listStatusCovid, $modelParent->status_covid);
            $kunjunganId = (int) $modelParent->kunjungan_id;
            $eksDiagnosaPrimer = explode("#", $modelParent->diagnosa_primer);
            $eksDiagnosaSekunder = explode("#", $modelParent->diagnosa_sekunder);
            if (isset($post['deleted']) && !empty($post['deleted'])) {
                $arrDelDiag = [];
                $arrKode10 = [];
                $arrKode9 = [];
                foreach ($post['deleted'] as $key => $value) {
                    $arrDelDiag[] = $value['diagnosa_id'];
                    if ($value['diagnosa_type'] == '10') {
                        $arrKode10[] = $value['kode_diagnosa'];
                    } else {
                        $arrKode9[] = $value['kode_diagnosa'];
                    }
                }
                $arrDelDiag = implode($arrDelDiag, ",");
                if ($arrKode10 || $arrKode9) {
                    $newDiagnosaPrimer = [];
                    $newDiagnosaSekunder = [];
                    if (isset($post['primer']) && !empty($post['primer'])) {
                        $newDiagnosaPrimer[] = $post['primer'];
                        array_push($arrKode10, $post['primer']);
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
                    $sql = 'update koreksidiagnosa_t SET is_deleted = true where diagnosa_id IN (' . $arrDelDiag . ') and pendaftaran_id = ' . $post['data']['pendaftaran_id'] . ' ';
                    $hapus = Yii::$app->db->createCommand($sql)->execute();
                }
            }

            // set primer
            if (isset($post['primer']) && !empty($post['primer'])) {
                if (!isset($arrDelDiag)) {
                    $primer = [];
                    $finalPrimer[] = $post['primer'];
                    array_push($primer, $post['primer']);
                    foreach ($eksDiagnosaPrimer as $key => $value) {
                        if (!in_array($value, $primer)) {
                            $finalPrimer[$key + 1] = $value;
                        }
                    }
                    $modelParent->diagnosa_primer = implode($finalPrimer, "#");
                    $post['data']['diagnosa_primer'] = $modelParent->diagnosa_primer;
                }
            }

            if ($modelParent->save()) {
                if ($kunjunganId) {
                    (new InternalService)->sendTo([
                        'Sirs' => [
                                'SinkronDataBpjs\TriggerFreezeBilling' => [
                                    'type_sinkron' => 'confirm',
                                    'kunjungan_id' => $kunjunganId
                                ]
                            ]
                    ], true);
                    $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => $status, 'identitas_value' => $post['data']['identitas_value'], 'identitas_id' => $post['data']['identitas_id'], 'identitas_nama' => $post['data']['nama_pasien']], ' kunjungan_id =' . $kunjunganId . '')->execute();
                } else {
                    $transaction->rollBack();
                    throw new \Exception("Pasien Tidak Ditemukan");
                }
            } else {
                return $modelParent->errors;
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
        $prosesKlaim['metadata']['method'] = 'set_claim_data';
        $prosesKlaim['metadata']['nomor_sep'] = $data['no_klaimcovid'];
        $prosesKlaim['data'] = [
            'nomor_sep' => $data['no_klaimcovid'],
            'nomor_kartu' => $data['no_kartu'],
            'tgl_masuk' => $data['tgl_masuk'],
            'tgl_pulang' => $data['tgl_keluar'],
            'jenis_rawat' => DocoConstants::CLAIM_RAJAL,
            'kelas_rawat' => $data['jenis_kelasrawat'],
            'birth_weight' => $data['berat_lahir'],
            'discharge_status' => $data['carapulang_id'],
            'diagnosa' => $data['diagnosa_primer'],
            'procedure' => $data['diagnosa_sekunder'],
            'nama_dokter' => $data['nama_dokter'],
            'tarif_rs' => [
                'prosedur_non_bedah' => $data['prosedur_nonbedah'],
                'prosedur_bedah' => $data['prosedur_bedah'],
                'konsultasi' => $data['konsultasi'],
                'tenaga_ahli' => $data['tenaga_ahli'],
                'keperawatan' => $data['keperawatan'],
                'penunjang' => $data['penunjang'],
                'radiologi' => $data['radiologi'],
                'laboratorium' => $data['laboratorium'],
                'pelayanan_darah' => $data['pelayanan_darah'],
                'rehabilitasi' => $data['rehabilitasi'],
                'kamar' => $data['kamar_akomodasi'],
                'rawat_intensif' => $data['rawat_intensif'],
                'obat' => $data['obat'],
                'obat_kronis' => $data['obat_kronis'],
                'obat_kemoterapi' => $data['obat_kemoterapi'],
                'alkes' => $data['alkes'],
                'bmhp' => $data['bmhp'],
                'sewa_alat' => $data['sewa_alat'],
            ],
            'tarif_poli_eks' => $data['tarif_poli_eks'],
            'pemulasaraan_jenazah' => isset($data['is_pemulasaranjenazah']) ? $data['is_pemulasaranjenazah'] : 0,
            'kantong_jenazah' => isset($data['is_kantongjenazah']) ? $data['is_kantongjenazah'] : 0,
            'peti_jenazah' => isset($data['is_petijenazah']) ? $data['is_petijenazah'] : 0,
            'plastik_erat' => isset($data['is_plastikerat']) ? $data['is_plastikerat'] : 0,
            'desinfektan_jenazah' => isset($data['is_desinfektanjenazah']) ? $data['is_desinfektanjenazah'] : 0,
            'mobil_jenazah' => isset($data['is_transport']) ? $data['is_transport'] : 0,
            'desinfektan_mobil_jenazah' => isset($data['is_desinfektanmobil']) ? $data['is_desinfektanmobil'] : 0,
            'covid19_status_cd' => isset($data['covid19_status_cd']) ? $data['covid19_status_cd'] : 0, //status covid
            'nomor_kartu_t' => $data['nomor_kartu_t'], //jenis kartu
            'episodes' => '', //episode
            'covid19_cc_ind' => isset($data['is_komplikasi']) ? $data['is_komplikasi'] : 0,
            'payor_id' => $data['klaim_penjamin'],
            'payor_cd' => DocoConstants::PAYOR_CD[$data['klaim_penjamin']],
            'kode_tarif' => $data['tarif'],
            'coder_nik' => DocoConstants::INACBG_NIK,
        ];

        // return $prosesKlaim;
        $klaim = json_decode(DocoHelpers::restInacbgs($prosesKlaim), true);
        if ($klaim['metadata']['code'] != 200) {
            return [
                'status' => $klaim['metadata']['code'],
                'title' => $klaim['metadata']['error_no'],
                'text' => $klaim['metadata']['message'],
            ];
        }
        $grouping['metadata']['method'] = 'grouper';
        $grouping['metadata']['stage'] = '1';
        $grouping['data']['nomor_sep'] = $data['no_klaimcovid'];
        $groupResult = json_decode(DocoHelpers::restInacbgs($grouping), true);
        if ($groupResult['metadata']['code'] != 200) {
            throw new \Exception("Terjadi Kesalahan");
        }

        if (isset($groupResult['special_cmg_option'])) {
            $stage2 = [];
            foreach ($groupResult['special_cmg_option'] as $key => $value) {
                $stage2[] = $value['code'];
            }
        }
        return json_encode($groupResult);
    }

    public function actionHapusKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $kunjungan_id = $post['kunjungan_id'];
        $nosep = $post['no_sep'];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $status = DocoConstants::STATUS_SUDAH_KOREKSI;

        try {
            $findParent = SyKlaimInacbg::findOne(['kunjungan_id' => $kunjungan_id]);
            if ($findParent) {
                $klaiminacbg_id = $findParent['sy_klaiminacbg_id'];
                $deleteGroup = $connection->createCommand('update sy_klaimgroup_t set is_deleted = true where sy_klaiminacbg_id = ' . $klaiminacbg_id . ' and is_deleted = false')->execute();
                $delete = $findParent->delete();

                if (!$delete) {
                    throw new \Exception("Hapus Terjadi Kesalahan");
                }
            }
            $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => $status, 'no_klaimcovid' => null], ' kunjungan_id =' . $kunjungan_id . '')->execute();

            $hapusklaim = [
                'metadata' => [
                    'method' => 'delete_claim',
                ],
                'data' => [
                    'nomor_sep' => $nosep,
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
                    'nomor_sep' => $nosep,
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
            (new InternalService)->sendTo([
                'Sirs' => [
                        'SinkronDataBpjs\TriggerFreezeBilling' => [
                            'type_sinkron' => 'cancel',
                            'kunjungan_id' => $kunjungan_id
                        ]
                    ]
            ], true);
            $transaction->commit();
            return true;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }
        return true;
    }

     public function actionFinalKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $noSep = ArrayHelper::getValue($post, 'nosep');
        $postData = ArrayHelper::getValue($post, 'data', []);
        $grouper = ArrayHelper::getValue($post, 'grouper', []);
        $additional = ArrayHelper::getValue($post, 'additional', []);
        $kunjunganId = ArrayHelper::getValue($postData, 'kunjungan_id');
        $noPendaftaran = ArrayHelper::getValue($postData, 'no_pendaftaran');
        try {
            $modelKlaim = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjunganId, 'is_deleted' => false])->one();
        
            $klaiminacbg_id = $modelKlaim->sy_klaiminacbg_id;
            // $dataKunjunganSy = $this->getDataKunjunganSy($noPendaftaran);
            // $kunjunganId = ArrayHelper::getValue($dataKunjunganSy, 'kunjungan_id');
            
            if (!empty($grouper)) {
                $modelGrouper = new SyKlaimGroup;
                $modelGrouper->attributes = $grouper;
                $modelGrouper->sy_klaiminacbg_id = $klaiminacbg_id;
                $item = [
                    'is_pemulasaranjenazah' => ArrayHelper::getValue($postData, 'is_pemulasaranjenazah', false),
                    'is_kantongjenazah' => ArrayHelper::getValue($postData, 'is_kantongjenazah', false),
                    'is_petijenazah' => ArrayHelper::getValue($postData, 'is_petijenazah', false),
                    'is_plastikerat' => ArrayHelper::getValue($postData, 'is_plastikerat', false),
                    'is_desinfektanjenazah' => ArrayHelper::getValue($postData, 'is_desinfektanjenazah', false),
                    'is_transport' => ArrayHelper::getValue($postData, 'is_transport', false),
                    'is_desinfektanmobil' => ArrayHelper::getValue($postData, 'is_desinfektanmobil', false)
                ];

                $modelGrouper->add_jenazah = json_encode($item);
                if (!empty($additional)) {
                    $arrSpecial = [];
                    $modelGrouper->group_nama = ArrayHelper::getValue($additional, 'cbg_desc');
                    $modelGrouper->cbg = ArrayHelper::getValue($additional, 'cbg_code');
                    $modelGrouper->group_tarif = ArrayHelper::getValue($additional, 'cbg_tarif');
                    $modelGrouper->additional_data = json_encode($additional);

                    $procCode = ArrayHelper::getValue($additional, 'proc_code');
                    $procName = ArrayHelper::getValue($additional, 'proc_name');
                    $prosCode = ArrayHelper::getValue($additional, 'pros_code');
                    $prosName = ArrayHelper::getValue($additional, 'pros_name');
                    $drugCode = ArrayHelper::getValue($additional, 'drug_code');
                    $drugName = ArrayHelper::getValue($additional, 'drug_name');
                    $invCode = ArrayHelper::getValue($additional, 'inv_code');
                    $invName = ArrayHelper::getValue($additional, 'inv_name');

                    if (!empty($procCode) && !empty($procName)) {
                        array_push($arrSpecial, $procCode);
                        $modelGrouper->sp_procedure_kode = $procCode;
                        $modelGrouper->sp_procedure_nama = $procName;
                    }
                    if (!empty($prosCode) && !empty($prosName)) {
                        array_push($arrSpecial, $prosCode);
                        $modelGrouper->sp_prosthesis_kode = $prosCode;
                        $modelGrouper->sp_prosthesis_nama = $prosName;
                    }
                    if (!empty($drugCode) && !empty($drugCode)) {
                        array_push($arrSpecial, $drugCode);
                        $modelGrouper->sp_drug_kode = $drugCode;
                        $modelGrouper->sp_drug_nama = $drugName;
                    }
                    if (!empty($invCode) && !empty($invName)) {
                        array_push($arrSpecial, $invCode);
                        $modelGrouper->sp_drug_kode = $invCode;
                        $modelGrouper->sp_drug_nama = $invName;
                    }
                    $modelGrouper->special_group = (!empty($arrSpecial) ? implode(',', $arrSpecial) : '');
                }
                
                if ($modelGrouper->save()) {
                    $modelKlaim->klaimgroup_id = $modelGrouper->sy_klaimgroup_id;
                } else {
                    return $modelKlaim->errors;
                    throw new \Exception(json_encode($modelGrouper->getErrors()));
                }
            }
            
            $modelKlaim->status_klaim = true;
            $modelKlaim->is_komplikasi = ArrayHelper::getValue($postData, 'is_komplikasi', false);
            $modelKlaim->is_pemulasaranjenazah = ArrayHelper::getValue($postData, 'is_pemulasaranjenazah', false);
            $modelKlaim->is_kantongjenazah = ArrayHelper::getValue($postData, 'is_kantongjenazah', false);
            $modelKlaim->is_petijenazah = ArrayHelper::getValue($postData, 'is_petijenazah', false);
            $modelKlaim->is_plastikerat = ArrayHelper::getValue($postData, 'is_plastikerat', false);
            $modelKlaim->is_desinfektanjenazah = ArrayHelper::getValue($postData, 'is_desinfektanjenazah', false);
            $modelKlaim->is_transport = ArrayHelper::getValue($postData, 'is_transport', false);
            $modelKlaim->is_desinfektanmobil = ArrayHelper::getValue($postData, 'is_desinfektanmobil', false);
            
            if (!$modelKlaim->save()) {
                throw new \Exception(json_encode($modelKlaim->getErrors()));
            }

            if(!empty($kunjunganId)) {
                $find = SyKunjunganPasien::findOne($kunjunganId);
                if(!empty($find)) {
                    $find->status_kunjungan = DocoConstants::STATUS_FINAL_KLAIM;
                    $save = $find->save();
                    if (!$save) {
                        throw new \Exception("Data gagal disimpan");
                    }
                }
            }
            
            $finalklaim = [
                'metadata' => [
                    'method' => 'claim_final',
                ],
                'data' => [
                    'nomor_sep' => $noSep,
                    'coder_nik' => DocoConstants::INACBG_NIK,
                ],
            ];
            
            $finalklaim = json_decode(DocoHelpers::restInacbgs($finalklaim), true);
            if (isset($finalklaim['metadata']['code'])) {
                if ($finalklaim['metadata']['code'] != 200) {
                    $transaction->rollBack();
                    return [
                        'status' => $finalklaim['metadata']['code'],
                        'title' => 'Proses Gagal',
                        'text' => $finalklaim['metadata']['message'],
                    ];
                } 
            } else {
                return  [
                    'status' => 400,
                    'title' => 'Proses Gagal',
                    'text' => 'Terjadi Kesalahan'
                ];
            }
            $transaction->commit();
            return [
                'status' => 200,
                'title' => 'Proses Berhasil',
                'text' => 'Final Klaim Berhasil',
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }

    public function actionUpdateKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $kunjunganId = $post['pendaftaran_id'];
        $nosep = $post['nomor_sep'];

        $connection = Yii::$app->db;
        $status = DocoConstants::STATUS_PROSES_KLAIM;
        $transaction = $connection->beginTransaction();
        try {
            $findParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjunganId])->one();
            $findParent->status_klaim = false;
            if ($findParent->save()) {
                $deleteGroup = $connection->createCommand('update sy_klaimgroup_t set is_deleted = true where sy_klaiminacbg_id = ' . $findParent->sy_klaiminacbg_id . ' and is_deleted = false')->execute();
                $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => DocoConstants::STATUS_PROSES_KLAIM], ' kunjungan_id =' . $kunjunganId . '')->execute();
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
                    throw new \Exception("Terjadi Kesalahan");
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

    public function actionUpdateData()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = SyKunjunganPasien::find()->where(['kunjungan_id' => $post['kunjunganId']])->one();
            $model->no_sep = !empty($post['nosep']) ? $post['nosep'] : null;
            // $model->no_asuransi = $peserta['response']['peserta']['noKartu'];
            $model->no_klaimcovid = $post['noPengajuan'];
            $model->identitas_id = $post['jenisIdentitas'];
            $model->identitas_value = $post['noIdentitas'];

            if ($model->save()) {
               $newClaim['metadata']['method'] = 'new_claim';
                $newClaim['data']['nomor_kartu'] = '';
                $newClaim['data']['nomor_sep'] =  $model->no_klaimcovid;
                $newClaim['data']['nomor_rm'] = $model->no_rekammedik;
                $newClaim['data']['nama_pasien'] = $model->nama_pasien;
                $newClaim['data']['tgl_lahir'] = $model->tgl_lahir;
                $newClaim['data']['gender'] = ($model->jenis_kelamin == DocoConstants::LAKI) ? DocoConstants::JENIS_LAKI : DocoConstants::JENIS_PEREMPUAN;
                $response = json_decode(DocoHelpers::restInacbgs($newClaim), true);
                if ($response['metadata']['code'] != 200) {
                    if ($response['metadata']['code'] != 400 && $response['metadata']['error_no'] != 'E2007') {
                        $transaction->rollBack();
                        return [
                            'status' => $response['metadata']['code'],
                            'title' => $response['metadata']['error_no'],
                            'text' => $response['metadata']['message'],
                        ];
                    }
                }
                $transaction->commit();
                return $model;
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 422,
                'title' => 'Terjadi Kesalahan !',
                'text' => $e->getMessage(),
            ];
        }
    }

    private function cariDokter($dokter_id)
    {
        $dokter = Pegawai::find()->where([
            'pegawai_id' => $dokter_id,
            'kelompokpegawai_id' => 1,
        ])->select([
            'pegawai_id', 'nama_pegawai', 'dokter_id',
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
                'is_deleted' => false,
            ])->one();

            $old = KoreksiDiagnosa::find()->where([
                'pendaftaran_id' => $id,
                'is_icdprimer' => true,
                'is_deleted' => false,
            ])->one();

            if ($new && $old) {
                if ($new->diagnosa_id == $old->diagnosa_id) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Diagnosa Sudah Primer!',
                    ];
                }
                $new->is_icdprimer = true;
                $new->kelompokdiagnosa_id = DocoConstants::MAP_DIAGNOSA_UTAMA;
                $old->kelompokdiagnosa_id = DocoConstants::MAP_DIAGNOSA_TAMBAHAN;
                $old->is_icdprimer = false;
                if ($new->save() && $old->save()) {
                    return [
                        'status' => 200,
                        'title' => 'Proses Berhasil!',
                        'text' => 'Set Primer Berhasil.',
                    ];
                } else {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Terjadi Kesalahan',
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan',
            ];
        }
    }

    public function actionAddDiagnosaTambahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        try {
            $koreksiTambahan = new KoreksiDiagnosa;
            $koreksiTambahan->pendaftaran_id = (int) $post['pendaftaran_id'];
            $koreksiTambahan->pasien_id = (int) $post['pendaftaran_id'];
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
                'text' => $e->getMessage(),
            ];
        }
    }

    private function getCoderNik()
    {
        $conf = @parse_ini_file('' . realpath(Yii::$app->basePath) . '/config/env/.env', true);
        $vclaim = isset($conf['inacbg']['env_vclaim']) ? $conf['inacbg']['env_vclaim'] : '';
        $env = ($vclaim == DocoConstants::LOOKUP_BPJS_LIVE) ? DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;
        $result = [];
        $coderNik = '';
        try {
            $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => $env, 'name' => 'coder_nik']);
            $result = isset($result['response']) ? $result['response'] : [];
            foreach ($result as $res) {
                $coderNik = $res['lookup_value'];
            }
            return $coderNik;
        } catch (\Exception $e) {
            return $result;
        }
    }

    private function getDataKunjunganSy($noPendaftaran)
    {
        $result = [];
        if(!empty($noPendaftaran)) {
            $result = Yii::$app->db->createCommand("SELECT kunjungan_id FROM sy_kunjungan WHERE no_pendaftaran = '".$noPendaftaran."' ")->queryOne();
        }
        return $result;
    }
}
