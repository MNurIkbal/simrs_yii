<?php
namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\RujukanBantaranView;
use app\modules\v1\models\RujukanBantaran;
use app\modules\v1\models\DokumenMedisBantaran;
use app\modules\v1\models\DokumenRujukanBantaran;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\MasterUpt;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienPulang;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoBaconQrCode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use yii\web\HttpException;
use yii\web\Response;
use yii\data\ActiveDataProvider;

class RujukanBantaranController extends \Doco\components\DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\RujukanBantaran';

    public $messageBroker = [
        'approve-bantaran' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'response_pengajuan'
                    ]
                ]
            ]
        ],
        'verification' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'response_pengajuan'
                    ]
                ]
            ]
        ],
        'periksa' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'save_pendaftaran'
                    ]
                ]
            ]
        ],
        'reject-bantaran' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'response_pengajuan'
                    ]
                ]
            ]
        ],
        'upload-dokumen' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'send_document'
                    ]
                ]
            ]
        ],
        'pemulangan-tahanan' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'pemulangan_pasien'
                    ]
                ]
            ]
        ],
        'batal-verifikasi' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'batal_verifikasi'
                    ]
                ]
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["upload-dokumen"] = ["POST"];
        $verbs["print-qr"] = ["GET"];
        $verbs["print-qr-data"] = ["GET"];
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

        // Action to accomodate SIRS Only
        $newActions = [
            'verification' => 'app\modules\v1\actions\RujukanBantaran\VerificationAction',
            'periksa' => 'app\modules\v1\actions\RujukanBantaran\PeriksaAction',
            'batal-verifikasi' => 'app\modules\v1\actions\RujukanBantaran\BatalVerifikasiAction',
            'insert-cppt' => 'app\modules\v1\actions\RujukanBantaran\InsertCpptAction',
            'remove-cppt' => 'app\modules\v1\actions\RujukanBantaran\RemoveCpptAction',
            'get-cppt-data' => 'app\modules\v1\actions\RujukanBantaran\GetCpptDataAction',
            'pemulangan-tahanan' => 'app\modules\v1\actions\RujukanBantaran\PemulanganTahananAction',
            'get-ttv-data' => 'app\modules\v1\actions\RujukanBantaran\GetTtvDataAction',
            'reject-bantaran' => 'app\modules\v1\actions\RujukanBantaran\RejectBantaranAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new RujukanBantaranView;
        $request = Yii::$app->request;
        $query = $model::find();
        $advancedFilters = $request->get('advanced-filter', []);

        $tglAwal = $tglAkhir = date('Y-m-d');

        if(isset($advancedFilters['tgl_kunjungan'])) {
            $tgl = explode(' - ', $advancedFilters['tgl_kunjungan']);
            $tglAwal = date('Y-m-d', strtotime($tgl[0]));
            $tglAkhir = date('Y-m-d', strtotime($tgl[1]));
            
            unset($_GET['advanced-filter']['tgl_kunjungan']);
        }

        if(isset($advancedFilters['uptasal_nama'])) {
            $query->andWhere(['uptasal_id' => $advancedFilters['uptasal_nama']]);
            unset($_GET['advanced-filter']['uptasal_nama']);
        }

        if(isset($advancedFilters['instalasi_id'])) {
            $query->andWhere(['instalasi_id' => $advancedFilters['instalasi_id']]);
            unset($_GET['advanced-filter']['instalasi_id']);
        }

        $query->andWhere(['between', new \yii\db\Expression('(tgl_kunjungan::date)'), $tglAwal, $tglAkhir]);
        $query->orderBy('tgl_order DESC');

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        throw new HttpException(404, 'The requested Item could not be found.');
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = RujukanBantaran::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanForm');
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

    public function actionDelete($id)
    {
        throw new HttpException(404, 'The requested Item could not be found.');
    }

    public function actionDetailRujukan() {
        $request = Yii::$app->request;
        $bantaranId = $request->get('bantaran_id');

        $rujukanBantaran = RujukanBantaranView::find()
                            ->andWhere(['rujukanbantaran_id' => $bantaranId])
                            ->asArray()
                            ->one();
        
        // Get catatan pemulangan from pasienpulang_t if exists
        if (!empty($rujukanBantaran['pendaftaran_id'])) {
            $pendaftaran = Pendaftaran::find()
                ->select(['pasienpulang_id'])
                ->andWhere(['pendaftaran_id' => $rujukanBantaran['pendaftaran_id']])
                ->asArray()
                ->one();
            
            if (!empty($pendaftaran['pasienpulang_id'])) {
                $pasienPulang = PasienPulang::find()
                    ->select(['keterangan_keluar'])
                    ->andWhere(['pasienpulang_id' => $pendaftaran['pasienpulang_id']])
                    ->asArray()
                    ->one();
                
                if (!empty($pasienPulang)) {
                    $rujukanBantaran['catatan_pemulangan'] = $pasienPulang['keterangan_keluar'];
                }
            }
        }
        
        $dokumenBantaran = DokumenRujukanBantaran::find()
                            ->andWhere(['rujukanbantaran_id' => $bantaranId])
                            ->asArray()
                            ->all();

        $dokumenMedis = DokumenMedisBantaran::find()
                            ->andWhere(['rujukanbantaran_id' => $bantaranId])
                            ->andWhere(['is_deleted' => false])
                            ->asArray()
                            ->all();

        $allDokumen = array_merge($dokumenBantaran, $dokumenMedis);

        $caraBayar = CaraBayar::find()
                        ->select(['carabayar_id as id', 'carabayar_nama as text'])
                        ->andWhere(['is_active' => true])
                        ->andWhere(['is_deleted' => false])
                        ->orderBy('carabayar_nama ASC')
                        ->asArray()
                        ->all();

        $dataCaraBayar = [];
        foreach($caraBayar as $key => $value) {
            $dataCaraBayar[$value['id']] = $value['text'];
        }

        $instalasi = Instalasi::find()
                        ->select(['instalasi_id as id', 'instalasi_nama as text'])
                        ->andWhere(['is_active' => true])
                        ->andWhere(['is_deleted' => false])
                        ->andWhere(['instalasi_id' => [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD]])
                        ->orderBy('instalasi_nama ASC')
                        ->asArray()
                        ->all();

        $dataInstalasi = [];
        foreach($instalasi as $key => $value) {
            $dataInstalasi[$value['id']] = $value['text'];
        }

        return [
            'bantaran' => $rujukanBantaran,
            'dokumen' => $allDokumen,
            'dokumen_medis' => $dokumenMedis,
            'dokumen_bantaran' => $dokumenBantaran,
            'list_carabayar' => $dataCaraBayar,
            'list_instalasi' => $dataInstalasi,
        ];
    }

    public function actionPrintQr()
    {
        $request = Yii::$app->request;
        $bantaranId = $request->get('bantaran_id');

        if (empty($bantaranId)) {
            throw new HttpException(400, 'Parameter bantaran_id wajib diisi.');
        }

        $bantaran = RujukanBantaranView::find()
            ->andWhere(['rujukanbantaran_id' => $bantaranId])
            ->asArray()
            ->one();

        if (empty($bantaran)) {
            throw new HttpException(404, 'Data rujukan bantaran tidak ditemukan.');
        }

        $detailPembantaran = RujukanBantaran::find()
            ->select(['tahanan_url'])
            ->andWhere(['rujukanbantaran_id' => $bantaranId])
            ->asArray()
            ->one();

        $bantaran['tahanan_url'] = isset($detailPembantaran['tahanan_url']) ? $detailPembantaran['tahanan_url'] : null;

        return [
            'bantaran' => $bantaran,
        ];
    }

    public function actionPrintQrData()
    {
        $request = Yii::$app->request;
        $bantaranId = $request->get('bantaran_id');

        if (empty($bantaranId)) {
            throw new HttpException(400, 'Parameter bantaran_id wajib diisi.');
        }

        $bantaran = RujukanBantaranView::find()
            ->andWhere(['rujukanbantaran_id' => $bantaranId])
            ->asArray()
            ->one();

        if (empty($bantaran)) {
            throw new HttpException(404, 'Data rujukan bantaran tidak ditemukan.');
        }

        $detailPembantaran = RujukanBantaran::find()
            ->select(['tahanan_url'])
            ->andWhere(['rujukanbantaran_id' => $bantaranId])
            ->asArray()
            ->one();

        $bantaran['tahanan_url'] = isset($detailPembantaran['tahanan_url']) ? $detailPembantaran['tahanan_url'] : null;

        $qrCode = DocoBaconQrCode::renderQrCode(
            isset($bantaran['tahanan_url']) ? $bantaran['tahanan_url'] : null,
            [
                'height' => 512,
                'width' => 512,
                'style_height' => '220px',
                'style_width' => '220px',
                'margin' => 2,
            ]
        );

        return [
            'bantaran' => $bantaran,
            'qr_image' => $qrCode
        ];
    }

    public function actionShowDokumen() {
        $request = Yii::$app->request;
        $dokBantaranId = $request->get('dokumenbantaran_id');

        $dokumenBantaran = DokumenRujukanBantaran::find()
                    ->andWhere(['dokumenrujukanbantaran_id' => $dokBantaranId])
                    ->asArray()
                    ->one();

        return $dokumenBantaran;
    }

    public function actionUploadDokumen()
    {
        $request = Yii::$app->request;
        $bantaranId = $request->post('rujukanbantaran_id');
        $namaDokumen = trim($request->post('nama_dokumen'));
        $urlDokumen = $request->post('url_dokumen');

        if (empty($bantaranId) || empty($namaDokumen) || empty($urlDokumen)) {
            return DocoHelpers::responseTemplate(422, 'Parameter dokumen tidak lengkap.');
        }

        $rujukan = RujukanBantaran::find()
            ->select(['rujukanbantaran_id', 'pengajuan_id'])
            ->andWhere(['rujukanbantaran_id' => $bantaranId])
            ->asArray()
            ->one();

        if (empty($rujukan)) {
            return DocoHelpers::responseTemplate(404, 'Rujukan tidak ditemukan.');
        }
        if (empty($rujukan['pengajuan_id'])) {
            return DocoHelpers::responseTemplate(422, 'Pengajuan tidak ditemukan untuk rujukan ini.');
        }

        $model = new DokumenMedisBantaran();
        $model->rujukanbantaran_id = $bantaranId;
        $model->pengajuan_id = $rujukan['pengajuan_id'];
        $model->nama_dokumen = $namaDokumen;
        $model->url_dokumen = $urlDokumen;
        $model->created_date = date('Y-m-d H:i:s');
        $model->is_active = true;
        $model->is_deleted = false;

        if ($model->save()) {
            return [
                'message' => 'Dokumen "' . $namaDokumen . '" berhasil diunggah.',
                'pengajuan_id' => $rujukan['pengajuan_id'],
                'nama_dokumen' => $namaDokumen,
                'url_dokumen' => $urlDokumen
            ];
        }

        return DocoHelpers::responseTemplate(422, 'Dokumen gagal disimpan.', $model->getErrors());
    }

    public function actionApproveBantaran() {
        $request = Yii::$app->request;
        $post = $request->post();

        $bantaran_id = $request->post('bantaran_id');
        $carabayar_id = $request->post('carabayar_id');
        $penjamin_id = $request->post('penjamin_id');
        $instalasi_id = $request->post('instalasi_id');
        $ruangan_id = $request->post('ruangan_id');
        $tgl_kunjungan = $request->post('tgl_kunjungan');
        $dokter_id = $request->post('dokter_id');
        $jadwaldokter_id = $request->post('jadwaldokter_id', null);
        $rencana_tindakan = $request->post('rencana_tindakan', null);

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $user_id = !empty(Yii::$app->jwt) ? $jwt->loginpemakai_id : null;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            if(!empty($user_id)) {
                $queryRawUser = "
                    SELECT
                    loginpemakai_id,
                    pegawai_id
                    FROM loginpemakai_k
                    WHERE loginpemakai_id = ".$user_id."
                ";
                
                $userVerif = Yii::$app->db->createCommand($queryRawUser)->queryOne();
            } else {
                $userVerif = [];
            }

            if($instalasi_id == DocoConstants::INST_ID_RJ) {
                $jadwalDokter = JadwalDokter::find()
                                ->andWhere(['jadwaldokter_id' => $jadwaldokter_id])
                                ->asArray()
                                ->one();
                $post['jadwal_dokter'] = $jadwalDokter;
                
                $generateReservasi = $this->generateReservasi($post);

                if($generateReservasi['is_success'] == false) {
                    $transaction->rollBack();
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'message' => 'Data gagal disimpan '. $generateReservasi['message'],
                        'status' => 422
                    ];
                }
            }

            $updateRujukan = RujukanBantaran::updateAll([
                'tgl_kunjungan' => date('Y-m-d', strtotime($tgl_kunjungan)),
                'tgl_verifikasi_bantaran' => date('Y-m-d H:i:s'),
                'instalasi_id' => $instalasi_id,
                'ruangan_id' => $ruangan_id,
                'dokter_id' => $dokter_id,
                'jadwaldokter_id' => $jadwaldokter_id,
                'jam_buka' => !empty($jadwaldokter_id) ? $jadwalDokter['jadwaldokter_mulai'] : null,
                'jam_tutup' => !empty($jadwaldokter_id) ? $jadwalDokter['jadwaldokter_tutup'] : null,
                'carabayar_id' => $carabayar_id,
                'penjamin_id' => $penjamin_id,
                'pegawaiverifikasi_id' => isset($userVerif['pegawai_id']) ? $userVerif['pegawai_id'] : null,
                'rencana_tindakan' => $rencana_tindakan,
                'last_modified_date' => date('Y-m-d H:i:s'),
                'pendaftaranol_id' => isset($generateReservasi['pendaftaranol_id']) ? $generateReservasi['pendaftaranol_id'] : null,
                'status_verifikasi_bantaran' => DocoConstants::SUDAH_VERIFIKASI_BANTARAN
            ], ['rujukanbantaran_id' => $bantaran_id]);

            // find pengajuan_id by rujukanbantaran_id
            $rujukanBantaran = RujukanBantaran::find()
                ->select(['pengajuan_id', 'no_rujukanbantaran', 'rencana_tindakan'])
                ->andWhere(['rujukanbantaran_id' => $bantaran_id])
                ->asArray()
                ->one();

            if($updateRujukan) {
                $transaction->commit();
                
                return [
                    'message' => 'Data Berhasil di simpan',
                    'pengajuan_id' => $rujukanBantaran['pengajuan_id'],
                    'nomor_pengajuan' => $rujukanBantaran['no_rujukanbantaran'],
                    'status_pengajuan_id' => 4, // Bantaran app approved status
                    'alasan_ditolak' => '',
                    'rencana_tindakan' => $rujukanBantaran['rencana_tindakan']
                ];
            } else {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 422;
                return [
                    'message' => 'Data gagal disimpan',
                    'status' => 422
                ];
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

    protected function generateReservasi($data = array()) {
        $rujukanBantaran = RujukanBantaran::find()->andWhere(
                                ['rujukanbantaran_id' => $data['bantaran_id']]
                            )->asArray()->one();

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $user_id = !empty(Yii::$app->jwt) ? $jwt->loginpemakai_id : null;

        // Payload reservasi pasien baru
        $jam_buka = $data['jadwal_dokter']['jadwaldokter_mulai'];
        $jam_tutup = $data['jadwal_dokter']['jadwaldokter_tutup'];

        $payload = [
            'pendaftaranol_id' => null,
            'pendaftaran_id' => null,
            'no_pendaftaranol' => null,
            'tgl_pendaftaranol' => date('Y-m-d', strtotime($data['tgl_kunjungan'])),
            'jam_kunjungan' => $jam_buka.'-'.$jam_tutup,
            'pasien_id' => !empty($rujukanBantaran['pasien_id']) ? $rujukanBantaran['pasien_id'] : null,
            'carabayar_id' => $data['carabayar_id'],
            'penjamin_id' => $data['penjamin_id'],
            'ruangan_id' => $data['ruangan_id'],
            'pegawai_id' => $data['dokter_id'],
            'shift_id' => null,
            'no_asuransi' => null,
            'no_rujukan' => '',
            'status_pasien' => null,
            'status_daftar_ol' => null,
            'antrian_id' => null,
            'klasifikasipasien_id' => null,
            'jadwaldokter_id' => $data['jadwaldokter_id'],
            'jam_mulai' => $jam_buka,
            'jam_tutup' => $jam_tutup,
            'jenis_reservasi' => null,
            'keterangan' => '',
            'jadwalbukapoli_id' => null,
            'user_id' => $user_id,
            'is_nomor_urut' => null,
            'no_bpjs' => null,
            'jeniskunjungan' => null,
            'jenisidentitas' => $rujukanBantaran['jenis_identitas'],
            'no_identitas_pasien' => $rujukanBantaran['no_identitas_pasien'],
            'namadepan' => '',
            'nama_pasien' => $rujukanBantaran['nama_pasien'],
            'tempat_lahir' => $rujukanBantaran['tempat_lahir'],
            'tanggal_lahir' => date('Y-m-d', strtotime($rujukanBantaran['tgl_lahir'])),
            'jeniskelamin' => $rujukanBantaran['jenis_kelamin'],
            'no_telepon_pasien' => $rujukanBantaran['no_telp'],
            'alamat_pasien' => '',
            'tipe_pasien' => ($rujukanBantaran['pasien_lama'] == true) ? 0 : 1 // 0 = Lama, 1 = Baru
        ];
        

        $response = Yii::$app->docoRest->pendaftaran->post('pendaftaran-online/daftar-online', [
            'form_params' => $payload
        ]);

        $saveReservasi = json_decode($response->getBody(), true);

        if($saveReservasi['metadata']['status'] != 200) {
            return [
                'is_success' => false,
                'message' => 'Gagal menambahkan reservasi ' . $saveReservasi['response']['text'],
            ];

        } else {
            return [
                'is_success' => true,
                'message' => 'Berhasil menambahkan reservasi',
                'pendaftaranol_id' => $saveReservasi['response']['data'][0]['pendaftaranol_id']
            ];
        }
    }

    public function actionGetIndexData() {
        $instalasi = Instalasi::find()
                        ->select(['instalasi_id as id', 'instalasi_nama as text'])
                        ->andWhere(['is_active' => true])
                        ->andWhere(['is_deleted' => false])
                        ->andWhere(['instalasi_id' => [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD]])
                        ->orderBy('instalasi_nama ASC')
                        ->asArray()
                        ->all();

        $dataInstalasi = [];
        foreach($instalasi as $key => $value) {
            $dataInstalasi[$value['id']] = $value['text'];
        }

        $master_upt = MasterUpt::find()
                        ->select(['upt_id as id', 'upt_nama as text'])
                        ->andWhere(['is_active' => true])
                        ->andWhere(['is_deleted' => false])
                        ->orderBy('upt_nama ASC')
                        ->asArray()
                        ->all();

        $dataUpt = [];
        foreach($master_upt as $key => $value) {
            $dataUpt[$value['id']] = $value['text'];
        }
        
        return [
            'master_upt' => $dataUpt,
            'instalasi' => $dataInstalasi,
        ];
    }


}
