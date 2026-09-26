<?php


namespace app\modules\v1\controllers;

use app\components\ApotekComponent;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\models\Ruangan;
use app\modules\v1\entities\Reseptur;
use app\modules\v1\entities\PenjualanResep;
use app\modules\v1\entities\ObatAlkesPasien;
use app\modules\v1\models\InformasiResepturView;
use SirsCore\features\FeaturePendaftaran;
use SirsCore\features\IntegrasiAkunting;
use Doco\Notifications\FarmasiNotification;
use SirsCore\features\FeatureTindakanBmhp;
use app\modules\v1\businessLogic\ValidasiStok;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\SatuanUnit;
use Doco\components\DocoMessages;
use app\modules\v1\models\Pendaftaran;
use Doco\exceptions\ValidationException;
use Exception;
use yii\db\Exception as DbException;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Services\PlafonBpjsService;

class ResepturController extends DocoActiveController
{
    public $modelClass = '';
    const ATTEMPT = 1;
    protected $prosesError = 0;

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

    public function actionCreate()
    {
    	// return Yii::$app->docoPlugin->execute('reseptur');
    }

    public function actionPenjualanResep()
    {
        $stime = microtime(true);
        $request = Yii::$app->request;
        $inputReseptur = $request->post('data_reseptur',[]);
        $inputResepturDetail = $request->post('data_resepturdetail',[]);
        $pendaftaran_id = ArrayHelper::getValue($inputReseptur, 'pendaftaran_id');
        $pendaftaran = Pendaftaran::find()->where([
            'pendaftaran_id' => $pendaftaran_id
        ])->asArray()->one();
        $isCloseBill = ArrayHelper::getValue($pendaftaran, 'is_close_bill', false);
        if($isCloseBill) {
            return $this->responseJson(422, 'Pasien sudah dilakukan proses Lock Bill.', [], [
                'title' => 'Proses Gagal!',
            ]);
        }

        $reseptur = new Reseptur($inputReseptur);
        $reseptur->setDataPendaftaran($pendaftaran);
        $reseptur->populateDetail($inputResepturDetail);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $stime0 = microtime(true);
            /* create reseptur */
            $reseptur->saveReseptur();
            $waktuSimpanResep = microtime(true) - $stime0;

            /* jika bukan RJ keluar disini */
            $instalasi_id = Yii::$app->jwt->instalasi_id;
            if ($instalasi_id != DocoConstants::INST_ID_RJ) {
                $transaction->commit();

                /* Notifikasi Farmasi */
                (new RabbitBgProcess())->send([
                    'reseptur_id' => $reseptur->reseptur_id,
                ], 'trigger_push_notif_farmasi', 'push_notif_farmasi');

                return [
                    'message' => 'Data Berhasil di simpan',
                    'id' => $reseptur->reseptur_id,
                    'data' => [
                        'pendaftaran_id' => $reseptur->pendaftaran_id,
                        'reseptur_id' => $reseptur->reseptur_id,
                        'from' => 'reseptur_controller'
                    ],
                    'time' => number_format(microtime(true)-$stime, 3)
                ];
            }

            $stime1 = microtime(true);
            /* create penjualanresep */
            $penjualanResep = (new PenjualanResep)->loadByResepturData($reseptur, $pendaftaran);
            $penjualanResep->save();

            $waktuPenjaulan = microtime(true) - $stime1;

            $stime2 = microtime(true);
            /* update status reseptur */
	        $reseptur->updatePenjualan($penjualanResep->penjualanresep_id);
            $waktuUpdatePenjualan = microtime(true) - $stime2;

            /* create obatalkespasien */
            $stime3 = microtime(true);
            if (count($reseptur->details) > 0) {
                $obatAlkesPasien = new ObatAlkesPasien();
                $obatAlkesPasien->loadByResepturData($reseptur, $pendaftaran);
                $obatAlkesPasien->saveObatAlkesPasien();
            }
            $waktuOA = microtime(true) - $stime3;

            /* update status tagihan */
	        $update_tagihan = FeaturePendaftaran::updateTagihan($reseptur->pendaftaran_id);
            if(!$update_tagihan) throw new Exception('Tidak Dapat Memproses Tagihan Pendaftaran', 422);

            $validasiPlafon = new PlafonBpjsService($pendaftaran_id, 0);
            $result = $validasiPlafon->validasiPlafon();
            if (!$result['isValid']) {
                $transaction->rollback();
                return $this->responseJson(422, $result['message'] ? $result['message'] : 'Validasi Plafon Gagal', [], [
                    'title' => 'Proses Gagal!',
                ]);
            }

            $transaction->commit();

            $stime4 = microtime(true);
            /* integrasi akunting */
            IntegrasiAkunting::integrateByNoResep($reseptur->noresep);

            /* Notifikasi Farmasi */
            (new RabbitBgProcess())->send([
                'reseptur_id' => $reseptur->reseptur_id,
            ], 'trigger_push_notif_farmasi', 'push_notif_farmasi');

            $waktuAfterCommit = microtime(true) - $stime4;

            return [
                'message' => 'Data Berhasil di simpan',
                'id' => $reseptur->penjualanresep_id,
                'data' => [
                    'pendaftaran_id' => $reseptur->pendaftaran_id,
                    'reseptur_id' => $reseptur->reseptur_id,
                    'from' => 'reseptur_controller'
                ],
                'time' => number_format(microtime(true)-$stime, 3),
                'time_simpan_reseptur' => number_format($waktuSimpanResep,3),
                'time_penjualan' => number_format($waktuPenjaulan,3),
                'time_update_penjualan' => number_format($waktuUpdatePenjualan,3),
                'time_insert_oa' => number_format($waktuOA,3),
                'time_after_commit' => number_format($waktuAfterCommit,3),
            ];
        } catch (DbException $de) {
            $transaction->rollback();
            Yii::error($de);
            return $this->responseJson(500, $de->getMessage(), [
                'file' => $de->getFile(),
                'line' => $de->getLine(),
            ], [
                'title' => 'Proses Gagal!',
            ]);
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::error($e);
            $code = $e->getCode() ?: 500;
            return $this->responseJson($code, $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], [
                'title' => 'Proses Gagal!',
            ]);
        }
    }

    public function actionPenjualan()
    {
        $stime = microtime(true);
    	$inputReseptur = Yii::$app->request->post('data_reseptur',[]);
    	$inputResepturDetail = Yii::$app->request->post('data_resepturdetail',[]);
    	$transaction = Yii::$app->db->beginTransaction();
    	try {
            $pendaftaranId = ArrayHelper::getValue($inputReseptur, 'pendaftaran_id');
            if(!empty($pendaftaranId)) {
                $dataPendaftaran = Pendaftaran::findOne($pendaftaranId);
                $isCloseBill = ArrayHelper::getValue($dataPendaftaran, 'is_close_bill', false);
                if($isCloseBill) {
                    return [
                        'status' => 422,
                        'failed' => true,
                        'title' => 'Proses Gagal!',
                        'text' => 'Pasien sudah dilakukan proses Lock Bill.'
                    ];
                }
            }

            $inputRacikanFreetext = $inputNonRacikan = [];
            $is_freetext = false;
            $satuanunit = SatuanUnit::find()->select(['satuanunit_id','satuanunit_nama'])->asArray()->all();
            $satuanunit = ArrayHelper::map($satuanunit,'satuanunit_nama','satuanunit_id');
            foreach ($inputResepturDetail as $key => $value) {
                if(ArrayHelper::getValue($value, 'detail_type') == 'racikan_freetext') {
                    $inputRacikanFreetext[] = $value;
                    $is_freetext = true;
                } else {
                    $inputNonRacikan[] = $value;
                }
                if(ArrayHelper::getValue($value, 'detail_type') == 'racikan_detail'){
                    $satuan_racikan_id = ArrayHelper::getValue($value,'satuan_racikan_id',0);
                    if((!isset($value['satuan_racikan_id']) || $satuan_racikan_id = 0) && isset($value['satuan_racikan_nama'])){
                        if(isset($satuanunit[$value['satuan_racikan_nama']])){
                            $inputResepturDetail[$key]['satuan_racikan_id'] = $satuanunit[$value['satuan_racikan_nama']];
                        }else{
                            $new_satuan = new SatuanUnit;
                            $new_satuan->satuanunit_nama = $value['satuan_racikan_nama'];
                            $new_satuan->satuanunit_namalain = $value['satuan_racikan_nama'];
                            $new_satuan->satuanunit_singkatan = $value['satuan_racikan_nama'];
                            if($new_satuan->save()){
                                $inputResepturDetail[$key]['satuan_racikan_id'] = $new_satuan->getPrimaryKey();
                            }

                        }
                    }else if(isset($value['satuan_racikan_id'])){
                        $inputResepturDetail[$key]['satuan_racikan_id'] = intval($value['satuan_racikan_id']);
                    }
                }
            }

            if($is_freetext) {
                $inputResepturDetail = $inputNonRacikan;
            }

            $reseptur = (new Reseptur)->load($inputReseptur, $inputResepturDetail);
            if($is_freetext) {
                $reseptur->loadResepturRacikan($inputRacikanFreetext);
            }
	    	$reseptur->createAntrian();
            $reseptur->save();

            if(isset($inputReseptur['ruanganreseptur_id'])){
                $instalasi_id = Ruangan::find()->select(['instalasi_id'])->where(['ruangan_id'=>$inputReseptur['ruanganreseptur_id']])->scalar();
                if($instalasi_id != DocoConstants::INST_ID_RJ){
                    $transaction->commit();
                    $reseptur_data = InformasiResepturView::find()->where(['reseptur_id' => $reseptur->reseptur_id])->one();
                    FarmasiNotification::newResep($reseptur_data);

                    return [
                        'message' => 'Data Berhasil di simpan',
                        'id' => $reseptur->reseptur_id,
                        'nomor' => @$reseptur_data->noresep,
                        'data' => [
                            'pendaftaran_id' => $inputReseptur['pendaftaran_id'],
                            'reseptur_id' => $reseptur->reseptur_id,
                            'from' => 'reseptur_controller'
                        ]
                    ];
                }
            }

	    	//create penjualanresep
	        $penjualanResep = (new PenjualanResep)->loadByReseptur($reseptur->reseptur_id);
	    	//create obatalkespasien
	    	$penjualanResep->save();
	    	$infoResep = $penjualanResep->getInfoResep();
            if(count($reseptur->getResepturDetail()) > 0) {
    	        $obatAlkesPasien = (new ObatAlkesPasien)->loadFromReseptur(
                                        $reseptur->getResepturDetail(),
                                        $infoResep,
                                        $penjualanResep->penjualanresep_id
                                    );
    	        $obatAlkesPasien->save();
            }

	    	//update status reseptur
	        $reseptur->updatePenjualan($penjualanResep->penjualanresep_id);
	    	//update tagihan
	        $update_tagihan = FeaturePendaftaran::updateTagihan($infoResep['pendaftaran_id']);
            if(!$update_tagihan) throw new \Exception("Tidak Dapat Memproses Tagihan Pendaftaran", 1);
            $transaction->commit();
            $getData = PenjualanResep::findOne($penjualanResep->penjualanresep_id);
            $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
	    	//integrasi akunting
            $return_integrate = IntegrasiAkunting::integrateByNoResep($noresep);

            $reseptur_data = InformasiResepturView::find()->where(['reseptur_id' => $reseptur->reseptur_id])->one();
            FarmasiNotification::newResep($reseptur_data);

	    	return [
	    		'message' => 'Data Berhasil di simpan',
	    		'id' => $penjualanResep->penjualanresep_id,
	    		'nomor' => $noresep,
                'data' => [
                    'pendaftaran_id' => $inputReseptur['pendaftaran_id'],
                    'reseptur_id' => $reseptur->reseptur_id,
                    'from' => 'reseptur_controller'
                ],
                'time' => number_format(microtime(true)-$stime, 3)
	    	];
    	} catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $this->logError($e);
            $exception = preg_match('/(?<=ERROR:  )(.*)/', $e->getMessage(), $errorText);
            $message = $e->getMessage();
            if (isset($errorText[1])) {
                $message = 'Query :' . $errorText[1];
            }
            if(preg_match('/\bduplicate\b/i', $errorText[1])) {
                if($this->prosesError < self::ATTEMPT) {
                    $this->prosesError++;
                    $this->actionPenjualan();
                } else {
                    \Yii::$app->response->statusCode = 500;
                    return [
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'message' => $message
                    ];
                }
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'message' => $e->getMessage()
                ];
            }
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionEditDetail($id)
    {
        $inputListObat = Yii::$app->request->post('listObat',[]);
        $inputHeader = [
            'biaya_administrasi'    => Yii::$app->request->post('biayaadministrasi'),
            'biayaadministrasi'     => Yii::$app->request->post('biayaadministrasi'),
            'totharganetto'         => Yii::$app->request->post('totalharga_netto'),
            'totalhargajual'        => Yii::$app->request->post('totalharga_jual'),
            'ruangan_id'            => Yii::$app->request->post('ruangan_id'),
        ];
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $reseptur = (new Reseptur)->loadById($id);
            $reseptur->validasiCloseBill();
            $updateObat = $reseptur->deleteOrAddDetail($inputListObat, $inputHeader);

            $statusBayar = Yii::$app->db->createCommand(
                "SELECT status_bayar
                FROM penjualanresep_t
                WHERE reseptur_id =:reseptur_id"
            )->bindValue(':reseptur_id',$id)->queryScalar();

            if (isset($statusBayar) && $statusBayar == DocoConstants::LUNAS) {
                \Yii::$app->response->statusCode = 422;
                return ['data'=>['status' => 'sudah_diserahkan'],'message'=>'Gagal edit reseptur, reseptur sudah dibayar, diserahkan atau dibatalkan.'];
            }

            // $inf_reseptur = InformasiResepturView::find()->where(['reseptur_id' => $id])->one();
            $getReseptur = Yii::$app->db->createCommand(
                "SELECT ruangan_m.instalasi_id, reseptur_t.penjualanresep_id, reseptur_t.status_reseptur,
                reseptur_t.pendaftaran_id
                FROM reseptur_t
                LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = reseptur_t.ruanganreseptur_id 
                WHERE reseptur_id =:reseptur_id"
            )->bindValue(':reseptur_id',$id)->queryOne();

            $instalasiReseptur = ArrayHelper::getValue($getReseptur,'instalasi_id');
            $penjuanresep_id = ArrayHelper::getValue($getReseptur,'penjualanresep_id');
            $status_reseptur = ArrayHelper::getValue($getReseptur, 'status_reseptur');
            $pendaftaranId = ArrayHelper::getValue($getReseptur, 'pendaftaran_id');

            if ($status_reseptur == DocoConstants::RESEPTUR_DISERAHKAN || $status_reseptur == DocoConstants::RESEPTUR_DIBATALKAN) {
                \Yii::$app->response->statusCode = 422;
                return ['data'=>['status' => 'sudah_diserahkan'],'message'=>'Gagal edit reseptur, reseptur sudah dibayar, diserahkan atau dibatalkan.'];
            }

            // jika dari rajal & punya penjualanresep_id, update tagihan
            if((isset($instalasiReseptur) && $instalasiReseptur == DocoConstants::VAR_I_RJ && !empty($penjuanresep_id)) || (isset($penjuanresep_id) && !empty($penjuanresep_id))) {
                $obatAlkesPasien = new ObatAlkesPasien();
                $penjualanResep = (new PenjualanResep)->loadByReseptur($id);
                $penjualanResep->updateTagihan($id, $inputHeader);
                $obatAlkesPasien->updateData($id, $updateObat);
                if(!$obatAlkesPasien) {
                    throw new \Exception("Gagal update obatalkespasien2", 1);
                }
                $validasiPlafon = new PlafonBpjsService($pendaftaranId, 0);
                $result = $validasiPlafon->validasiPlafon();
                if (!$result['isValid']) {
                    $transaction->rollback();
                    return $this->responseJson(422, $result['message'] ? $result['message'] : 'Validasi Plafon Gagal', [], [
                        'title' => 'Proses Gagal!',
                    ]);
                }
            }

            if(\Yii::$app->response->statusCode == 200) {
                $transaction->commit();
                return ['data'=>[],'message'=>'Berhasil'];
            } else {
                return ['data'=>[],'message'=>'Gagal edit reseptur'];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }

    public function actionApproveReseptur($id) {
        $inputListObat = Yii::$app->request->post('listObat',[]);
        $inputHeader = [
            'biaya_administrasi' => Yii::$app->request->post('biayaadministrasi'),
            'biayaadministrasi' => Yii::$app->request->post('biayaadministrasi'),
            'totharganetto' => Yii::$app->request->post('totalharga_netto'),
            'totalhargajual' => Yii::$app->request->post('totalharga_jual'),
            'ruangan_id' => Yii::$app->request->post('ruangan_id'),
        ];
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $reseptur = (new Reseptur)->loadById($id);
            $reseptur->validasiCloseBill();
            $updateObat = $reseptur->deleteOrAddDetail($inputListObat, $inputHeader);

            //create penjualanresep
	        $penjualanResep = (new PenjualanResep)->loadByReseptur($id);
	    	//create obatalkespasien
	    	$penjualanResep->saveApprove($inputHeader);
	    	
            $infoResep = $penjualanResep->getInfoResep();
            $detail_resep = $reseptur->getResepturDetailById($id);
            if(count($detail_resep) > 0) {
                $obatAlkesPasien = (new ObatAlkesPasien)->loadFromReseptur($detail_resep,$infoResep,$penjualanResep->penjualanresep_id);
                $obatAlkesPasien->save();
                
                $payload = $obatAlkesPasien->getObatAlkesPasien($penjualanResep->penjualanresep_id);
                $potongStok = FeatureTindakanBmhp::stokObatAlkes($payload, false);
                
                if(is_array($potongStok)) {
                    \Yii::$app->response->statusCode = 422;
                    $obat = ObatAlkes::find()->select(['obatalkes_id', 'obatalkes_nama'])->where(['obatalkes_id' => current($potongStok)])->one();
                    $obat_nama = ArrayHelper::getValue($obat,'obatalkes_nama');
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'message' => 'Gagal potong stok obat',
                        'text' => "Stok obat {$obat_nama} tidak mencukupi",
                        'list_obat_tidak_cukup' => $potongStok
                    ];
                }

                if(!$potongStok){
                    \Yii::$app->response->statusCode = 422;
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'message' => 'Gagal potong stok obat',
                        'text' => 'Stok obat tidak mencukupi',
                        'data' => $potongStok
                    ];
                }

                $validasiStok = ValidasiStok::serahkan($payload);
                if(!$validasiStok || is_array($validasiStok)) {
                    return $validasiStok;
                }
            }

	    	//update status reseptur
            $reseptur = (new Reseptur)->updateApprove($id,$penjualanResep->penjualanresep_id);
            if(\Yii::$app->response->statusCode == 200) {
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                $transaction->commit();
                return $this->responseJson(200, DocoMessages::SUC_MESSAGE);
            } else {
                return $this->responseJson(200, "Gagal approve resep");
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            $this->logError($e);
            return $this->responseJson(500, DocoMessages::ERR_MESSAGE);
        } catch (\Exception $e) {
            $transaction->rollback();
            $this->logError($e);
            return $this->responseJson(500, DocoMessages::ERR_MESSAGE);
        }
    }

    public function validasiCloseBill()
    {
        $reseptur = $this->_reseptur;
        $pendaftaranId = ArrayHelper::getValue($reseptur, 'pendaftaran_id');
        if(!empty($pendaftaranId)) {
            $dataPendaftaran = Pendaftaran::findOne($pendaftaranId);
            $isCloseBill = ArrayHelper::getValue($dataPendaftaran, 'is_close_bill', false);
            if($isCloseBill) {
                \Yii::$app->response->statusCode = 422;
                throw new \Exception('Pasien sudah dilakukan proses Lock Bill.', 1);
            }
        }
    }
}
