<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\models\Lookup;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\ProfilRumahSakit;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\WorklistDetailView;
use app\modules\v1\models\WorklistView;
use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\Pegawai;

use Doco\components\DocoPrint;
use Doco\actions\GetDataAction;
use app\modules\v1\entities\Etiket;

class WorklistController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\WorklistView';

    public $messageBroker = [
        'update-status-cetak-etiket' => [
            'services' =>[
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                        'taskid' => '6',
                        'update_from' => 'cetak_etiket'
                    ]
                ],
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        $request = Yii::$app->request;

        $action_path = 'app\modules\v1\actions\Worklist';
        $custom_actions = [
            'get-list-nomor-resep' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new WorklistView(),
                'selected' => [
                    'penjualanresep_id',
                    'reseptur_id',
                    'no_resep',
                    'no_reseptur'
                ],
                'field_search' => [
                    'no_resep',
                    'no_reseptur'
                ],
                'orderby' => [
                    ['no_resep', 'ASC'],
                    ['no_reseptur', 'ASC']
                ],
                'groupby' => [
                    'penjualanresep_id',
                    'reseptur_id',
                    'no_resep',
                    'no_reseptur'
                ],
            ],
            'get-list-pegawai' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new PegawaiView(),
                'selected' => [
                    'pegawai_id',
                    'nomorindukpegawai',
                    'nama_pegawai',
                ],
                'field_search' => [
                    'nomorindukpegawai',
                    'nama_pegawai'
                ],
                'default_where' => [
                    ['ruangan_id', $request->get('ruangan_id')],
                ],
                'orderby' => [
                    ['nomorindukpegawai', 'ASC']
                ],
                'groupby' => [
                    'pegawai_id',
                    'nomorindukpegawai',
                    'nama_pegawai',
                ],
            ],
            'get-data-worklist'     => $action_path . '\GetDataWorklistAction',
            'get-history-worklist'  => $action_path . '\GetHistoryWorklistAction',
        ];
        $actions = array_merge($actions, $custom_actions);
        return $actions;
    }

    public function actionIndex($instalasi = null)
    {
        $today_start = date('Y-m-d 00:00:00');
        $today_end   = date('Y-m-d 23:59:59');
        $worklist    = WorklistView::find()->select([
                        'jenispenjualan_id',
                        'instalasi_id',
                        'pasienadmisi_id',
                        'status_reseptur_id',
                        'status_bayar_id',
                        'status_bayar',
                        'status_worklist',
                        'status_worklist_id',
                        'tanggal_lahir',
                        'no_resep',
                        'no_reseptur',
                        'no_pendaftaran',
                        'nama_pasien',
                        'tanggal',
                        'add_reseptur',
                        'add_penjualaanresep',
                        'dokter',
                        'tinggi_badan',
                        'berat_badan',
                        'alergi',
                        'reseptur_id',
                        'penjualanresep_id',
                        'kategori_resep',
                        'kategori_resep_nama',
                        'kategori_resep_kode'
                    ]);

        $request  = Yii::$app->request;
        $no_resep = $request->get('no_resep', "");
        $tanggal  = $request->get('tanggal', "");

        $list_instalasi = [
            DocoConstants::INST_ID_RJ,
            DocoConstants::INST_ID_RD,
            DocoConstants::INST_ID_RI
        ];

        if ($tanggal != "") {
            $explode = explode(" - ", $tanggal);
            if(count($explode) == 2) {
                $today_start = date('Y-m-d H:i:s', strtotime($explode[0]));
                $today_end   = date('Y-m-d H:i:s', strtotime($explode[1]."23:59:59"));
            }
        }

        if (in_array($instalasi, $list_instalasi)) {
            if ($instalasi == DocoConstants::INST_ID_RJ) {
                $worklist->andwhere([
                    'jenispenjualan_id' => DocoConstants::PENJUALAN_RESEP_BEBAS
                ]);
                $worklist->orWhere([
                    'jenispenjualan_id' => DocoConstants::PENJUALAN_RESEP_KARYAWAN
                ]);
                $worklist->orWhere([
                    'instalasi_id' => DocoConstants::INST_ID_RJ
                ]);
            } else {
                if($instalasi == DocoConstants::INST_ID_RI) {
                    $worklist->andWhere([
                        'instalasi_id' => $instalasi
                    ])->orWhere([
                        'not', ['pasienadmisi_id' => null] # Pasien RD dari pasien admisi masuk ke WL RI
                    ]);
                } else {
                    $worklist->andWhere([
                        'instalasi_id' => $instalasi,
                        'pasienadmisi_id' => null
                    ]);
                }
            }

            $worklist->andWhere(['<>', 'status_reseptur_id', DocoConstants::RESEPTUR_DISERAHKAN]);
            $worklist->andWhere(['<>', 'status_reseptur_id', DocoConstants::VAR_B_R]); # Batal Reseptur tidak muncul
        } else {
            $worklist->andWhere(['NOT IN', 'instalasi_id', $list_instalasi]);
            $worklist->andWhere(['status_bayar_id' => DocoConstants::LUNAS]);
            $worklist->andWhere(['<>', 'status_reseptur_id', DocoConstants::RESEPTUR_DISERAHKAN]);
            $worklist->andWhere(['<>', 'status_reseptur_id', DocoConstants::VAR_B_R]);
        }

        if($no_resep != "") {
            $worklist->andWhere(['or',
                ['LIKE', 'no_resep', $no_resep],
                ['LIKE', 'no_reseptur', $no_resep],
                ['ILIKE', 'nama_pasien', $no_resep]
            ]);
        }

        $worklist->andWhere(['BETWEEN', 'tanggal', $today_start, $today_end]);

        if (in_array($instalasi, [DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI])) {
            $worklist->orderBy('tanggal asc');
        } else {
            $worklist->orderBy([
                'status_bayar_id' => SORT_ASC,
                'tgl_pembayaran' => SORT_ASC,
                'tanggal' => SORT_ASC,
            ]);
        }

        $worklist->limit(10);
        $data =  $worklist->asArray()->all();

        $list = [];
        foreach ($data as $index => $value) {
            $identifier = is_null($value['no_reseptur']) ? $value['no_resep'] : $value['no_reseptur'];
            $value['tanggal_lahir'] = is_null($value['tanggal_lahir']) ? "-" : date('d M Y', strtotime($value['tanggal_lahir']));
            $value['no_resep'] = is_null($value['no_resep']) ? "-" : $value['no_resep'];
            $value['no_reseptur'] = is_null($value['no_reseptur']) ? "-" : $value['no_reseptur'];
            $value['dokter'] = is_null($value['dokter']) ? "-" : $value['dokter'];
            $value['no_pendaftaran'] = is_null($value['no_pendaftaran']) ? "-" : $value['no_pendaftaran'];
            $value['nama_pasien'] = is_null($value['nama_pasien']) ? "-" : $value['nama_pasien'];
            $value['tinggi_badan'] = is_null($value['tinggi_badan']) ? "-" : $value['tinggi_badan'];
            $value['berat_badan'] = is_null($value['berat_badan']) ? "-" : $value['berat_badan'];
            $value['alergi'] = is_null($value['alergi']) || $value['alergi'] == "{NULL}" ? "-" : $value['alergi'];
            $value['reseptur_id'] = is_null($value['reseptur_id']) ? $value['penjualanresep_id'] : $value['reseptur_id'];
            $value['kategori_resep'] = !is_null($value['kategori_resep']) ? $value['kategori_resep'] : '-';
            $value['kategori_resep_nama'] = !is_null($value['kategori_resep_nama']) ? $value['kategori_resep_nama'] : '-';
            $value['kategori_resep_kode'] = !is_null($value['kategori_resep_kode']) ? $value['kategori_resep_kode'] : '-';
            $list[$identifier] = $value;
        }

        return $list;
    }

    public function actionDetail($identifier)
    {
        $detail = WorklistDetailView::find()
            ->where(['no_reseptur' => $identifier])
            ->orWhere(['no_resep' => $identifier])
            ->andWhere(['detail_is_deleted'=>FALSE])
            ->orderBy('rke asc');

        $data = $detail->asArray()->all();

        $list = [];
        $no = 1;
        foreach ($data as $index => $value) {
            $value['rowNum'] = $no;
            $value['is_racikan'] = $value['racikan_id'] == DocoConstants::ID_RACIKAN ? true : false;
            $value['rke'] = is_null($value['rke']) ? "-" : $value['rke'];
            $value['etiket'] = empty($value['etiket']) ? "-" : $value['etiket'];
            $list[$index] = $value;
            $no++;
        }

        return $list;
    }

    public function actionUpdateStatus()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $jwt = Yii::$app->jwt;
            $user_profile = $jwt->user;
            $pegawai_id = isset($post['pegawai_id']) ? $post['pegawai_id'] : $jwt->user->pegawai_id;
            $multi_status = isset($post['multi_status']) ? $post['multi_status'] : false;

            $pegawai = PegawaiView::find()
                ->where(['pegawai_id' => $pegawai_id])
                ->asArray()->one();

            $nama_pegawai = is_null($pegawai) ? $user_profile->nama_pemakai : $pegawai['nama_pegawai'];

            $status_worklist = DocoConstants::$WORKLIST_STATUS;
            $lookup_status_worklist = Lookup::find()
                ->select("lookup_id, lookup_name")
                ->where(['lookup_id' => $status_worklist])->asArray()->all();

            $lookup_status_worklist = ArrayHelper::map($lookup_status_worklist, 'lookup_id', 'lookup_name');

            $identifier = $post['identifier'];
            $current_status = $post['status_worklist'];
            $current_status_index = array_search($current_status, $status_worklist);

            if ($current_status_index === false) {
                throw new \Exception('Status tidak Valid.');
            }

            $worklist = WorklistView::find()
                ->where(['no_reseptur' => $identifier])
                ->orWhere(['no_resep' => $identifier])
                ->asArray()->one();

            if (empty($worklist) || ( is_null($worklist['penjualanresep_id']) && is_null($worklist['reseptur_id']) ) ) {
                throw new \Exception('Data tidak ditemukan.');
            }

            // multi status condition
            // for futher usage, label the var with _ms (multi status)
            // run this condition if $status_worklist[$current_status_index] = $trigger_ms
            // log will have multi status listed in $list_status
            $trigger_ms = DocoConstants::WORKLIST_DISIAPKAN;

            // log status
            if($multi_status && $status_worklist[$current_status_index] == $trigger_ms) {
                $list_status = [DocoConstants::WORKLIST_QC, DocoConstants::WORKLIST_SIAP_DISERAHKAN];
            } else {
                $list_status = [$status_worklist[$current_status_index + 1]];
            }

            for ($i=1; $i <= count($list_status) ; $i++) {
                $log_key = date('YmdHis', strtotime("+$i sec"));
                $status_worklist_id = $status_worklist[$current_status_index + $i];
                $log_status[$log_key] = [
                    'tanggal' => date('d M Y H:i:s', strtotime("+$i sec")),
                    'pegawai_id' => $pegawai_id,
                    'nama_pegawai' => $nama_pegawai,
                    'status_worklist_id' => $status_worklist_id,
                    'status_worklist' => $lookup_status_worklist[$status_worklist_id],
                ];
            }

            if (!is_null($worklist['penjualanresep_id'])) {
                $penjualan_resep = PenjualanResep::find()->where(['penjualanresep_id' => $worklist['penjualanresep_id']])->one();
                $status_history = json_decode($penjualan_resep->additional_data, true);
                
                if(is_null($status_history)) {
                    $status_history['log_status'] = [];
                }

                foreach($log_status as $key => $log) {
                    $status_history['log_status'][$key] = $log;    
                }

                $penjualan_resep->additional_data = json_encode($status_history);
                $penjualan_resep->status_worklist = $status_worklist[$current_status_index + count($list_status)];
                $penjualan_resep->save();
            }

            if (!is_null($worklist['reseptur_id'])) {
                $reseptur = Reseptur::find()->where(['reseptur_id' => $worklist['reseptur_id']])->one();
                $status_history = json_decode($reseptur->additional_data, true);
                
                if(is_null($status_history)) {
                    $status_history['log_status'] = [];
                }
                
                foreach($log_status as $key => $log) {
                    $status_history['log_status'][$key] = $log;    
                }

                $reseptur->additional_data = json_encode($status_history);
                $reseptur->status_worklist = $status_worklist[$current_status_index + count($list_status)];
                $reseptur->save();
            }

            $transaction->commit();

            return $this->responseJson(200, 'Worklist telah terupdate', [
                'status_worklist_id' => $status_worklist_id
            ]);
        } catch(\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
        } catch(\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    /**
    * @controller actionPrintEtiket
    * @attribute #data# => Menampilkan data table print etiket
    **/
    public function actionPrintEtiket()
    {
        return Yii::$app->docoPlugin->execute('print_etiket');
    }
    
    /**
    * @controller actionPrintEtiketNew
    * @attribute #data# => Menampilkan data table print etiket
    **/
    public function actionPrintEtiketNew()
    {
       return (new Etiket)->execute($this);
    }

    public function actionUpdateStatusCetakEtiket() {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $reseptur = Reseptur::find()->select(['penjualanresep_id', 'pendaftaran_id'])->where(['noresep' => Yii::$app->request->post('identifier')])->one();
            if(!empty($reseptur)){
                /** Update Reseptur */
                Reseptur::updateAll(
                    [
                        'is_cetak_etiket' => true,
                        'tgl_cetak_etiket' => date('Y-m-d H:i:s'),
                        'cetak_etiket_oleh' => @Yii::$app->jwt->user->loginpemakai_id
                    ], 
                    "noresep = :noresep and is_cetak_etiket is false",
                    [
                        ':noresep' => Yii::$app->request->post('identifier')
                    ]
                );
            }else{
                $penjualanResep = PenjualanResep::find()->select(['penjualanresep_id','reseptur_id', 'pendaftaran_id'])->where(['noresep' => Yii::$app->request->post('identifier')])->one();
            }

            if(empty($reseptur) && !empty($penjualanResep['reseptur_id'])){
                Reseptur::updateAll(
                    [
                        'is_cetak_etiket' => true,
                        'tgl_cetak_etiket' => date('Y-m-d H:i:s'),
                        'cetak_etiket_oleh' => @Yii::$app->jwt->user->loginpemakai_id
                    ], 
                    'reseptur_id = :reseptur_id and is_cetak_etiket is false',
                    [
                        ':reseptur_id'=>$penjualanResep['reseptur_id']
                    ]
                );
            }

            $penjualanResepId = !empty($reseptur['penjualanresep_id']) ? $reseptur['penjualanresep_id'] : (!empty($penjualanResep['penjualanresep_id']) ? $penjualanResep['penjualanresep_id'] : null);
            $pendaftaranId = !empty($reseptur['pendaftaran_id']) ? $reseptur['pendaftaran_id'] : (!empty($penjualanResep['penjualanresep_id']) ? $penjualanResep['pendaftaran_id'] : null);
            if(!empty($penjualanResepId)){
                /** Update Penjualan Resep */
                PenjualanResep::updateAll(
                    [
                        'is_cetak_etiket' => true,
                        'tgl_cetak_etiket' => date('Y-m-d H:i:s'),
                        'cetak_etiket_oleh' => @Yii::$app->jwt->user->loginpemakai_id
                    ], 
                    "penjualanresep_id = :penjualanresep_id and is_cetak_etiket is false",
                    [
                        ':penjualanresep_id'=>$penjualanResepId
                    ]
                );
            }

            $transaction->commit();
            return [
                'message' => 'Berhasil Cetak Etiket',
                'pendaftaran_id' => $pendaftaranId
            ];
        } catch(\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
        } catch(\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    private function getDataOralAndNonOral($identifier, $is_oral){
        $header = WorklistView::find()
                ->where(['no_reseptur' => $identifier])
                ->orWhere(['no_resep' => $identifier]);
        $dataHeader = $header->asArray()->one();

        $detail = WorklistDetailView::find()
            ->where(['no_reseptur' => $identifier])
            ->orWhere(['no_resep' => $identifier])
            ->andWhere(['detail_is_deleted' => false])
            ->andWhere(['>', 'det_medis', 0]);
        $data = $detail->asArray()->all();
        $arrRacikan = [];
        $arrNonRacikan = [];
        foreach ($data as $key => $value) {
            if ($value['racikan_id'] == DocoConstants::ID_RACIKAN) {
                $arrRacikan[$value['rke']][] = $value;
            }else{
                $satuanUnit = SatuanUnit::find()->where(['satuanunit_id' => $value['satuan_racikan_id']])->asArray()->one();
                $arrNonRacikan[] = [
                    'nama_pasien' => substr($dataHeader['nama_pasien'], 0, 25),
                    'dokter' => !empty($dataHeader['dokter']) ? $dataHeader['dokter'] : "-",
                    'tanggal_lahir' => ($dataHeader['tanggal_lahir'] != NULL) ? date('d M Y', strtotime( $dataHeader['tanggal_lahir'] )) : '-',
                    'no_resep' => $value['no_resep'],
                    'no_reseptur' => $value['no_reseptur'],
                    'nama_obat' => substr($value['nama_obat'], 0, 50),
                    'komponen_obat' => [],
                    'signa' => $value['signa'],
                    'qty_obat' => $value['det_medis'],
                    'is_oral' => $value['is_oral'],
                    'satuan_input' => $value['satuan_input'],
                    'catatan' => !empty($value['etiket']) ? $value['etiket'] : "-",
                    'tgl_cetak' => date('d M Y', strtotime(date('Y-m-d'))),
                    'no_rm' => $dataHeader['no_rm'],
                    'gender' => $dataHeader['jeniskelamin'],
                    'nama_racikan' => !empty($value['nama_racikan']) ? $value['nama_racikan'] : '-',
                    'qty_racikan' => $value['qty_racikan'],
                    'satuan_unit_nama' => !empty($satuanUnit) ? $satuanUnit['satuanunit_nama'] : '-'
                ];
            }
        }

        $resultRacikan = [];
        foreach ($arrRacikan as $key => $value) {
            $isOral = true;
            $temp_id = "";
            $komponen_obat = [];
            foreach ($value as $k => $v) {
                if ($v['is_oral'] == false) {
                    $isOral = false;
                }

                $dataObat = [
                    'nama_obat' => substr($v['nama_obat'], 0, 20),
                    'qty_obat' => $v['qty_obat'],
                    'satuan_input' => $v['satuan_input']
                ];

                $satuanUnit = SatuanUnit::find()->where(['satuanunit_id' => $v['satuan_racikan_id']])->asArray()->one();
                $identifier = $v['racikan_id'].'-'.$v['rke'];
                $no_reseptur = $v['no_reseptur'];
                $no_resep = $v['no_resep'];
                $etiket = $v['etiket'];
                $signa = $v['signa'];
                $qty_obat = $v['qty_obat'];
                $satuan_input = $v['satuan_input'];
                $catatan = $v['etiket'];
                $rke = $v['rke'];
                $komponen_obat[] = $dataObat;
                $nama_racikan = isset($v['nama_racikan']) ? substr($v['nama_racikan'], 0, 25) : '-';
                $qty_racikan = isset($v['qty_racikan']) ? $v['qty_racikan'] : '-';
                $temp_id = $identifier;
                $nama_racikan = !empty($v['nama_racikan']) ? $v['nama_racikan'] : null;
                $qty_racikan = !empty($v['qty_racikan']) ? $v['qty_racikan'] : null;
                $satuan_unit_nama = !empty($satuanUnit) ? $satuanUnit['satuanunit_nama'] : null;
            }

            $content = [
                'nama_pasien' => substr($dataHeader['nama_pasien'], 0, 25),
                'dokter' => !empty($dataHeader['dokter']) ? $dataHeader['dokter'] : "-",
                'tanggal_lahir' => ($dataHeader['tanggal_lahir'] != NULL) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-',
                'no_reseptur' => $no_reseptur,
                'no_resep' => $no_resep,
                'nama_obat' => Yii::t('app', 'Racikan Ke') . ' ' . $rke,
                'komponen_obat' => $komponen_obat,
                'signa' => $signa,
                'qty_obat' => '',
                'is_oral' => $isOral,
                'satuan_input' => $satuan_input,
                'catatan' => !empty($catatan) ? $catatan : "-",
                'tgl_cetak' => date('d M Y', strtotime(date('Y-m-d'))),
                'no_rm' => $dataHeader['no_rm'],
                'nama_racikan' => $nama_racikan,
                'qty_racikan' => $qty_racikan,
                'type' => 'header',
                'gender' => $dataHeader['jeniskelamin'],
                'satuan_unit_nama' => $qty_racikan .' - '. $satuan_unit_nama,
            ];

            $resultRacikan[] = $content;

            $components = array_chunk($komponen_obat, 4, true);
            $iter = 1;
            
            foreach ($components as $compKey => $compVal) {
                $content['komponen_obat'] = array_values($compVal);
                $content['type'] = 'content';
                $content['length'] = count($components);
                $content['segment'] = $iter;

                $iter++;
                $resultRacikan[] = $content;
            }
        }

        $arrResult = array_merge($arrNonRacikan, $resultRacikan);
        $dataPrint = [];
        foreach ($arrResult as $key => $value) {
            if ($value['is_oral'] == $is_oral) {
                $dataPrint[] = $value;
            }
        }
        return $dataPrint;
    }

    public function setWorklistFarmasiLog($history, $header)
    {
        $waktu_tunggu = null;

        if($header['status_bayar_id'] == DocoConstants::LUNAS) {
            $obj_key = date('YmdHis', strtotime($header['tgl_pembayaran']));
            $status_bayar = [
                "tanggal" => date('d M Y H:i:s', strtotime($header['tgl_pembayaran'])),
                "pegawai_id" => null,
                "nama_pegawai" => is_null($header['nama_kasir']) ? 'Petugas Kasir' : $header['nama_kasir'],
                "status_worklist_id" => $header['status_bayar_id'],
                "status_worklist" => $header['status_bayar']
            ];

            if(empty($history)) {
                $history['log_status'] = [$obj_key => $status_bayar];
            }

            if(isset($history['log_status'][$obj_key])) {
                $history['log_status'][$obj_key] = $status_bayar;
            } else {
                $history['log_status'] = [$obj_key => $status_bayar] + $history['log_status'];
            }
        }

        if($header['status_reseptur_id'] == DocoConstants::RESEPTUR_DISERAHKAN) {
            if($header['tgl_menyerahkan'] == null) {
                $header['tgl_menyerahkan'] = date('Y-m-d H:i:s');
            }

            // Hitung waktu tunggu resep
            if($header['status_bayar_id'] == DocoConstants::BELUM_LUNAS) {
                $waktu_awal = date_create($header['tanggal']);
            } else {
                $waktu_awal = date_create($header['tgl_pembayaran']);
            }

            $waktu_akhir = date_create($header['tgl_menyerahkan']);
            $waktu_tunggu = date_diff($waktu_awal, $waktu_akhir)->format('%H:%I:%S');

            if($header['pegawai_menyerahkan_id'] != null) {
                $pegawai = Pegawai::find()
                ->where(['pegawai_id' => $header['pegawai_menyerahkan_id']])
                ->asArray()
                ->one();
            }

            $obj_key = date('YmdHis', strtotime($header['tgl_menyerahkan']));

            $status_serahkan = [
                "tanggal" => date('d M Y H:i:s', strtotime($header['tgl_menyerahkan'])),
                "pegawai_id" => $header['pegawai_menyerahkan_id'],
                "nama_pegawai" => is_null($header['pegawai_menyerahkan_id']) ? 'Petugas Menyerahkan' : $pegawai['nama_pegawai'],
                "status_worklist_id" => $header['status_reseptur_id'],
                "status_worklist" => $header['status_reseptur'],
                "waktu_tunggu" => $waktu_tunggu
            ];

            if(isset($history['log_status'][$obj_key])) {
                $history['log_status'][$obj_key] = $status_serahkan;
            } else {
                $history['log_status'] += [$obj_key => $status_serahkan];    
            }
        }

        return ['history' => $history, 'waktu_tunggu' => $waktu_tunggu];
    }
}
