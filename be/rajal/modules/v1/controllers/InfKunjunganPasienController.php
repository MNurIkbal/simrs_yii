<?php

/**
 * @Author: afil
 * @Date:   2018-01-12 14:52:58
 * @Description: controller informasi kunjungan pasien rajal
 */

namespace app\modules\v1\controllers;

use yii\helpers\ArrayHelper;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\Services\RmService;

use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\BuatJanjiPoli;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Konsulpoli;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\payload\ParamModel;
use app\modules\v1\models\LoginPemakai;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\PasienPulang;;
use Doco\exceptions\ValidationException;
use app\modules\v1\models\TindakanPelayanan;
use Doco\Services\KasirService;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\Antrian;
use Doco\components\PelayananHelpers;
use yii\db\Query;

class InfKunjunganPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\infoKunjunganRajal';
    protected $_instalasiPenunjang = [
        DocoConstants::INST_ID_LAB,
        DocoConstants::INST_ID_RAD,
        DocoConstants::INST_ID_REHAB,
        DocoConstants::INST_ID_BEDAH
    ];

    public $messageBroker = [
        'ubah-dokter' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                        'taskid' => '4',
                    ]
                ],
                'Satusehat' => [
                    'EncounterUpdateInprogres' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ]
            ]
        ],
        'batal-periksa' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'successProcess' => true,
                        'taskid' => 99,
                        'result' => true
                    ]
                ],
            ],
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        /* $verbs["index"] = ["GET", "POST"];
        $verbs["get-list-data"] = ["GET"];
        $verbs["buat-janji-poli"] = ["POST"];
        $verbs["ubah-dokter"] = ["POST"];
        $verbs["batal-periksa"] = ["POST"];*/
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    /**
     *
     * @see Fungsi get list data diagnosa untuk ajax request
     * @return array
     *
     */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoKunjunganRajal;
            $query = InfoKunjunganRajal::find()
                ->where([
                    'ruangan_id' => $request->get('ruangan_id', null),
                ])
                ->orWhere([
                    'konsulpoli_id' => $request->get('ruangan_id', null),
                ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

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

    /**
     *
     * @see Fungsi get list data
     * @return array
     *
     */
    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;

            $find_statusperiksa = $this->getStatusPeriksa();
            $data_statusperiksa = $find_statusperiksa->asArray()->all();

            $find_pegawai = $this->getPegawaiRuangan($request->get('id_ruangan'));
            $data_pegawai = $find_pegawai->asArray()->all();

            $find_penjamin = $this->getPenjamin();
            $data_penjamin = $find_penjamin->asArray()->all();

            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-pegawai' => $data_pegawai,
                'data-penjamin' => $data_penjamin,
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
     *
     * @see Fungsi get data pasien
     * @return array
     *
     */
    public function 
    actionGetPasien($id)
    {
        try {
            $request = Yii::$app->request;
            $params = [];
            $konsulpoli_id = $request->get('konsulpoli_id');
            $find = $this->getPasienDetail($id,$konsulpoli_id)->asArray()->one();
            if(!empty($konsulpoli_id)) {
                $konsul = KonsulPoli::findOne(PelayananHelpers::decryptId($konsulpoli_id));
                $dokterKonsulId = ArrayHelper::getValue($konsul, 'pegawai_id');
                if($dokterKonsulId) {
                    $find['dokter_konsul_id'] = $dokterKonsulId;
                }
            }
            
            return [
                'data' => $find
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
     *
     * @see Fungsi create janji poli
     * @return array
     *
     */
    public function actionBuatJanjiPoli()
    {
        try {
            $request = Yii::$app->request;

            if ($data_post = $request->post()) {
                $modelBuatJanjiPoli = new BuatJanjiPoli;
                $modelBuatJanjiPoli->attributes = $data_post;

                if ($modelBuatJanjiPoli->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($modelBuatJanjiPoli->errors, 'BuatJanjiPoliForm');
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
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi ubah data pasien pendaftaran
     * @return array
     *
     */
    public function actionUbahDokter()
    {
        $request = Yii::$app->request;
        $dokterSebelumnya = null;
        try {
            if ($post = $request->post()) {
                if (!empty($post["konsulpoli_id"])) {
                    $konsulPoliId = $post["konsulpoli_id"];
                    $data_konsul = Konsulpoli::find()
                        ->where([
                            'konsulpoli_id' => $konsulPoliId,
                            'is_deleted' => false,
                        ])->one();

                    if ($data_konsul) {
                        if(isset($data_konsul->status_periksa) && $data_konsul->status_periksa == DocoConstants::STATUS_PERIKSA){
                            $data_dokter = InfoKunjunganRajal::find()->select(['nama_pegawai'])
                            ->where(['konsulpoli_id' => $konsulPoliId,'is_deleted' => false,])
                            ->asArray()->one();
                            $dokter = isset($data_dokter['nama_pegawai']) ? $data_dokter['nama_pegawai'] : ' - ';
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                'title' => 'Gagal Assign Dokter',
                                'text' => 'Pasien Sudah Memiliki Dokter DPJP!<br>Dokter DPJP : '.$dokter,
                            ]);
                        }
                        $data_konsul->pegawai_id = $post['pegawai_id'];
                        if (isset($data_konsul->tgl_masukperiksa)) {
                            $data_konsul->tgl_masukperiksa = !empty($post["tgl_masukperiksa"]) ? $post["tgl_masukperiksa"] : null;
                        }
                        $data_konsul->status_periksa = (string)DocoConstants::STATUS_PERIKSA;
                        $data_konsul->save();

                        // integrasi ke kasir untuk case MCU
                        $this->integrateKasir(
                            $post["pendaftaran_id"],
                            $post["pegawai_id"],
                            PelayananHelpers::decryptId($post["konsulpoli_id"])
                        );

                        return [
                            'message' => 'status_success',
                            'data' => $data_konsul,
                            'pendaftaran_id' => $post["pendaftaran_id"],
                            'konsulpoli_id' => PelayananHelpers::decryptId($post["konsulpoli_id"]),
                        ];
                    }
                } else {
                    $data_pendaftaran = Pendaftaran::find()
                        ->where([
                            'pendaftaran_id' => $post["pendaftaran_id"],
                            'is_deleted' => false,
                        ])->one();

                    if ($data_pendaftaran) {
                        $dokterSebelumnya = $data_pendaftaran->pegawai_id;
                        if(isset($data_pendaftaran->status_periksa) && $data_pendaftaran->status_periksa == DocoConstants::STATUS_PERIKSA){
                            $data_dokter = InfoKunjunganRajal::find()->select(['nama_pegawai'])
                            ->where(['pendaftaran_id' => $post["pendaftaran_id"],'is_deleted' => false,])
                            ->asArray()->one();
                            $dokter = isset($data_dokter['nama_pegawai']) ? $data_dokter['nama_pegawai'] : ' - ';
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                'title' => 'Gagal Assign Dokter',
                                'text' => 'Pasien Sudah Memiliki Dokter DPJP!<br>Dokter DPJP : '.$dokter,
                            ]);
                        }
                        $data_pendaftaran->pegawai_id = $post["pegawai_id"];
                        $data_pendaftaran->tgl_masukperiksa = $post["tgl_masukperiksa"];
                        $data_pendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA;
                        if ($data_pendaftaran->save()) {
                            $rmService = new RmService;
                            $respn = $rmService->periksa([
                                'pendaftaran_id' => [$post['pendaftaran_id']],
                                'status' => DocoConstants::MONITORING_RM_ISSUE,
                            ]);
                        }

                        // integrasi ke kasir
                        $this->integrateKasir($post["pendaftaran_id"], $post["pegawai_id"], null, $post["no_pendaftaran"]);

                        return [
                            'message' => 'status_success',
                            'data' => $data_pendaftaran,
                            'pendaftaran_id' => $post["pendaftaran_id"],
                            'konsulpoli_id' => 0,
                            'dokter_sebelumnya' => $dokterSebelumnya,
                        ];
                    } else {
                        \Yii::$app->response->statusCode = 203;

                        return [
                            'message' => 'status_notfound'
                        ];
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function integrateKasir($pendaftaranId, $pegawaiId, $konsulPoliId = null, $no_pendaftaran = null)
    {
        $query = (new Query())
            ->from(['a' => TindakanPelayanan::tableName()])
            ->select([
                'a.tindakanpelayanan_id',
                'a.tgl_tindakan',
                'a.instalasi_id',
            ])->where(['a.pendaftaran_id' => $pendaftaranId]);

        if (!empty($konsulPoliId)) {
            $query->andWhere([
                'a.konsulpoli_id' => $konsulPoliId
            ]);
        }

        if (empty($no_pendaftaran)) {
            $query->leftJoin(['b' => Pendaftaran::tableName()], 'a.pendaftaran_id = b.pendaftaran_id')
                ->addSelect(['b.no_pendaftaran']);
        }

        $dataTindakan = $query->all();

        $detail_tindakan = [];
        if (!empty($dataTindakan)) {
            foreach ($dataTindakan as $value) {
                if (in_array($value['instalasi_id'], $this->_instalasiPenunjang)) {
                    $detail_tindakan[] = [
                        'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                        'tgl_tindakan' => $value['tgl_tindakan'],
                    ];
                }else{
                    $detail_tindakan[] = [
                        'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                        'tgl_tindakan' => $value['tgl_tindakan'],
                        'dokter_id' => $pegawaiId,
                    ];
                }

                if (empty($no_pendaftaran)) {
                    $no_pendaftaran = $value['no_pendaftaran'];
                }
            }

            $params = [
                'no_pendaftaran' => $no_pendaftaran,
                'detail_tindakan' => $detail_tindakan,
            ];

            $billKasir = (new KasirService)->post('api/edit-tindakan', [
                'form_params' => $params,
                'failed' => function ($data) {
                    \Yii::error([
                        "Message-Error" => $data
                    ]);
                    return [
                        'failed' => true,
                        'message' => [
                            'status' => 422,
                            'text' => $data['message']
                        ]
                    ];
                }
            ]);

            if (isset($billKasir['failed'])) {
                throw new ValidationException(422, 'Integrasi Tindakan Gagal', [
                    'text' => $billKasir['message']['text']
                ]);
            }
        }
    }

    /**
     *
     * @see Fungsi get data penjamin
     * @return array, activeQueryRecords
     *
     */
    private function getPenjamin()
    {
        $penjamin = Penjamin::find()->select([
            "penjamin_id",
            "penjamin_nama",
        ]);

        return $penjamin;
    }

    /**
     *
     * @see Fungsi get data pegawai ruangan
     * @return ActiveRecord Model, activeQueryRecords
     *
     */
    private function getPegawaiRuangan($id_ruangan)
    {
        $model = DokterView::find()->where(['ruangan_id' => $id_ruangan]);
        // $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id WHERE ruanganpegawai_mp.ruangan_id=:ruangan_id';
        // $result = RuanganPegawai::findBySql($sql, [':ruangan_id' => $id_ruangan]);

        return $model;
    }

    /**
     *
     * @see Fungsi get data status periksa
     * @return array, activeQueryRecords
     *
     */
    private function getStatusPeriksa()
    {
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_periksa1 FROM lookup_m WHERE lookup_type = 'status_periksa'";
        $result = Lookup::findBySql($sql);

        return $result;
    }

    /**
     *
     * @see Fungsi get data pasien joins
     * @var params array
     * @return array, activeQueryRecords
     *
     */
    private function getPasienDetail($id = null, $konsulpoli_id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pendaftaran_id,
                    antrian_id,
                    pendaftaran_t.pegawai_id as pegawai_id,
                    pasien_m.no_rekam_medik AS no_rekam_medik,
                    pendaftaran_t.no_pendaftaran AS no_pendaftaran,
                    pasien_m.nama_pasien AS nama_pasien,
                    pasien_m.pasien_id AS pasien_id,
                    pegawai_m.nama_pegawai AS nama_dokter,
                    pendaftaran_t.status_bayar,
                    pendaftaran_t.status_periksa,
                    pendaftaran_t.instalasi_id
                FROM
                    pendaftaran_t
                JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = pendaftaran_t.pegawai_id
                WHERE
                    pendaftaran_t.is_deleted = FALSE
            ";

        // filter
        if ($id) {
            $sql .= " AND pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        $result = Pendaftaran::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }

    public function actionBatalPeriksa()
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $post = $request->post('PasienBatalPeriksaForm');
            $id = $post['pendaftaran_id'];
            $message = 'Pemeriksaan Tidak Dapat Dibatalkan, Karena Sudah Ada ';
            // cek jika ada reseptur dari rajal batal resep
            $reseptur = Reseptur::find()->where(['pendaftaran_id' => $id])->andWhere(['<>', 'status_reseptur', DocoConstants::VAR_B_R])->one();

            // cek jika ada reseptur dari apotek
            $penjualanResep = PenjualanResep::find()->where(['pendaftaran_id' => $id])->andWhere(['<>', 'status_reseptur', DocoConstants::VAR_B_R])->one();

            //cek jika ada order penunjang
            $penunjang = PasienKirimUnitLain::find()
            ->where(['pendaftaran_id' => $id])
            ->andWhere([
                'not in', 'status_penunjang', [DocoConstants::BTL_APPROVE, DocoConstants::DI_TOLAK]
            ])->one();
            if ($reseptur || $penjualanResep || $penunjang) {
                if ($reseptur || $penjualanResep && !$penunjang) {
                    $extMessage = 'Reseptur.';
                } elseif ($reseptur || $penjualanResep && $penunjang) {
                    $extMessage = 'Reseptur dan Order Penunjang.';
                } else {
                    $extMessage = 'Order Penunjang.';
                }

                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => $message . '' . $extMessage
                ]);
            }

            $payload = new ParamModel;
            $payload->scenario = 'batal-periksa';
            $payload->pendaftaran_id = $id;
            $payload->attributes = $post;
            if ($payload->validate()) {
                $user = LoginPemakai::find()->where([
                    'nama_pemakai' => $payload->username
                ])->one();

                if (!$user) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'User tidak ditemukan.'
                    ]);
                }

                $check = $user['katakunci_pemakai'];
                $valid = Yii::$app->security->validatePassword($payload->password, $check);
                if (!$valid) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Password salah.'
                    ]);
                }

                $dataTagihan = $this->actionGetTagihanPasien($id);
                $tagihan_karcis = $dataTagihan['tagihan_karcis'];
                $tagihan_non_karcis = $dataTagihan['tagihan_non_karcis'];

                if (!empty($tagihan_non_karcis) || !empty($tagihan_karcis)) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => 'Maaf, Pasien Masih Memiliki Tagihan.'
                    ]);
                }

                $pendaftaran = Pendaftaran::findOne($id);
                $check = Yii::$app->jwt->user->katakunci_pemakai;
                $range_periksa = [DocoConstants::STATUS_PERIKSA, DocoConstants::STATUS_ANTRIAN_POLI];

                if (in_array($pendaftaran->status_periksa, $range_periksa)) {
                    $modelBatal = new PasienBatalPeriksa;
                    $modelBatal->attributes = $post;
                    $modelBatal->pendaftaran_id = $id;
                    if ($modelBatal->validate()) {
                        $modelBatal->save();
                        $pendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                        $pendaftaran->pasienbatalperiksa_id = $modelBatal->pasienbatalperiksa_id;
                        $pendaftaran->save(false);

                        // if (!empty($tagihan_karcis)) {
                        //     Yii::$app->db->createCommand("
                        //         UPDATE tindakanpelayanan_t SET is_deleted = TRUE WHERE (tindakanpelayanan_id IN {$tagihan_karcis})
                        //     ")->execute();
                        // }

                        if (!empty($dataTagihan['data_bpjs'])) {
                            $modelBpjs = $dataTagihan['data_bpjs'];
                            $modelBpjs->is_deleted = true;
                            $modelBpjs->norujukan = '-';
                            $nosep = !empty($modelBpjs->nosep) ? $modelBpjs->nosep : null;
                            if (!empty($nosep)) {
                                if ($modelBpjs->validate() && $modelBpjs->save()) {
                                    $deleteSep = new Bpjs;
                                    $deleteSep = $deleteSep->deleteSep($nosep);
                                } else {
                                    return [
                                        'data' => $modelBpjs->errors,
                                        'status' => 422
                                    ];
                                }
                            }
                        }

                        /**
                        * Delete Antrian Ketika konfig menyala,
                        * ketika didelete maka kuota dokter akan diretur
                        **/
                        $konfig_batal_antrian = LookupTransaksi::find()->where(['kode_transaksi' => 'batal_antrian'])->one();
                        if (ArrayHelper::getValue($konfig_batal_antrian, 'additional_value', false) == TRUE && strtolower(ArrayHelper::getValue($konfig_batal_antrian, 'additional_value', false)) == 'true') {
                            $this->deleteAntrian($id);
                        }

                        $transaction->commit();
                        return [
                            'text' => 'Data Pasien Berhasil Dibatalkan',
                            'pendaftaran_id' => $id,
                        ];
                    } else {
                        return [
                            'data' => $modelBatal->errors,
                            'status' => 422
                        ];
                    }
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => 'Maaf, Pasien Yang Dapat Dibatalkan adalah Hanya Pasien dengan Status Antrian Poliklinik atau Diperiksa.'
                    ]);
                }
            } else {
                return [
                    'data' => $payload->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetTagihanPasien($id)
    {
        try {
            $pendaftaran = Pendaftaran::findOne($id);
            $data_bpjs = Bpjs::find()->where(['pendaftaran_id' => $id])->one();
            $status_bayar = $tindakanpelayanan_id = $tindakanpelayanan_id_non_karcis = '';

            if ($pendaftaran) {
                $status_bayar = $pendaftaran->status_bayar;
            }

            $tagihan = InfoTagihanPasien::find()->select(['COALESCE(SUM(sub_total), 0) AS tagihan'])->where(['pendaftaran_id' => $id])->scalar();

            $tagihanKarcis = InfoTagihanPasien::find()->where(['pendaftaran_id' => $id, 'kelompoktindakan_id' => DocoConstants::VAR_KEL_KRCS])->asArray()->all();

            $tagihanNonKarcis = InfoTagihanPasien::find()->where(['pendaftaran_id' => $id])
                ->andWhere(['<>', 'kelompoktindakan_id', DocoConstants::VAR_KEL_KRCS])
                ->asArray()->all();

            $listTindakan = $listTindakanNonKarcis = [];
            if (!empty($tagihanKarcis)) {
                foreach ($tagihanKarcis as $key => $value) {
                    $listTindakan[] = $value['pelayanan_id'];
                }
            }

            if (!empty($tagihanNonKarcis)) {
                foreach ($tagihanNonKarcis as $key => $value) {
                    $listTindakanNonKarcis[] = $value['pelayanan_id'];
                }
            }

            if (!empty($listTindakan)) {
                $tindakanpelayanan_id = "(" . implode(",", $listTindakan) . ")";
            }

            if (!empty($listTindakanNonKarcis)) {
                $tindakanpelayanan_id_non_karcis = "(" . implode(",", $listTindakanNonKarcis) . ")";
            }

            return [
                'tagihan' => $tagihan,
                'tagihan_karcis' => $tindakanpelayanan_id,
                'tagihan_non_karcis' => $tindakanpelayanan_id_non_karcis,
                'status_bayar' => $status_bayar,
                'data_bpjs' => $data_bpjs
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

    private function deleteAntrian($pendaftaran_id)
    {
        $antrian = Antrian::find()->where(['and', ['pendaftaran_id' => $pendaftaran_id, 'jenisantrian_id' => DocoConstants::VAR_JA_P, 'is_konsulpoli' => FALSE]])->one();
        if (!empty($antrian)) {
            $antrian->no_antrian = null;
            $antrian->status_antrian = DocoConstants::VAR_SA_B;
            $antrian->is_deleted = true;
            $antrian->is_active = false;
            $antrian->deleted_date = date('Y-m-d h:i:s');
            $antrian->save(false);
        }
    }

    public function actionBundleConfirmPeriksa()
    {
        $requestGet = Yii::$app->request->get();
        $result = [];
        foreach ($requestGet as $key => $value) {
            switch ($key) {
                case 'getPasien':
                    $pasien_id = ArrayHelper::getValue($requestGet[$key], 'id');
                    $_GET['konsulpoli_id'] = ArrayHelper::getValue($requestGet[$key], 'konsulpoli_id');
                    $response = Yii::$app->runAction('v1/inf-kunjungan-pasien/get-pasien', [
                        'id' => $pasien_id
                    ]);
                    $result[$key] = $response;
                    break;
                case 'dataPegawai':
                    $ruangan_id = ArrayHelper::getValue($requestGet[$key], 'ruangan_id');
                    $response = $this->getPegawaiRuangan($ruangan_id)->asArray()->all();
                    $result[$key] = $response;
                    break;
                default:
                    /* do nothing */
            }
        }
        return $result;
    }
}
