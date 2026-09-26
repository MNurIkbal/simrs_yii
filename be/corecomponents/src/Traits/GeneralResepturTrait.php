<?php

namespace Doco\Traits;

use Yii;

use Doco\components\DocoConstants;
use Doco\models\Ruangan;
use Doco\models\KonfigFarmasi;
use Doco\models\Racikan;
use Doco\models\ResepTemp;
use Doco\models\ResepTempDetail;
use app\modules\v1\models\SignaObat;
use yii\helpers\ArrayHelper;
use Doco\models\InfoStokObatAlkesFnrNew;
use Doco\models\InfoStokObatAlkesFn;
use Doco\models\Pendaftaran;
use Doco\models\PasienAdmisi;
use Doco\models\InfoStokObatAlkes;
use Doco\models\KetersediaanObatView;
use Doco\models\LookupTransaksi;
use Doco\models\FgetKetersediaanobatFn;
use Doco\components\DocoConstansId;



trait GeneralResepturTrait
{

    /**
     * Function for get reseptur default data
     *
     * @param Integer pendaftaran_id
     * @return Array
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDefaultDataReseptur()
    {
        $ruangan_id = Yii::$app->request->get('ruangan_id', 0);
        $defaultData = [
            'diagnosa_id' => null,
            'diagnosa_nama' => null,
            'berat_badan' => null,
            'tinggi_badan' => null,
            'depo_id' => $this->getDepo($this->type,$ruangan_id),
            'list-depo' => (new Ruangan)->find()->select([
                'ruangan_id',
                'ruangan_nama',
            ])->where(['instalasi_id' => DocoConstants::INST_ID_APT])->asArray()->all(),
            'lookuptransaksi_m' => [
                'config_zero_stock' => DocoConstansId::actionGetId('config_zero_stock'),
            ]
        ];

        $getLatestDiagnosa = $this->cpptModel->find()->select([
            'a_diag_utama'
        ]);

        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
        switch ($this->type) {
            case 'RI':
                $getLatestDiagnosa->andWhere([
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => $pasienadmisi_id,
                ])->orderBy(['cppt_id' => SORT_DESC]);

                $getTtv = $this->asesmenMedis->find()->select([
                    'tinggi_badan',
                    'berat_badan'
                ])->andWhere([
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienadmisi_id' => $pasienadmisi_id
                ]);
                $getTtv = $getTtv->asArray()->one();
                if ($getTtv) {
                    $defaultData['tinggi_badan'] = $getTtv['tinggi_badan'];
                    $defaultData['berat_badan'] = $getTtv['berat_badan'];
                }
                break;
            case 'RD':
                $getLatestDiagnosa->andWhere([
                    'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id', null),
                ])->andWhere(
                    [
                        'is', 'pasienadmisi_id', new \yii\db\Expression('null')
                    ]
                )->orderBy(['cppt_id' => SORT_DESC]);
                $getTtv = $this->asmedModel->find()->select([
                    'tinggi_badan',
                    'berat_badan'
                ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
                if (empty($getTtv)) {
                    $getAskep = $this->askepModel->find()->select([
                        'tinggi_badan',
                        'berat_badan'
                    ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
                    $defaultData['tinggi_badan'] = $getAskep['tinggi_badan'];
                    $defaultData['berat_badan'] = $getAskep['berat_badan'];
                } else {
                    $defaultData['tinggi_badan'] = $getTtv['tinggi_badan'];
                    $defaultData['berat_badan'] = $getTtv['berat_badan'];
                }
                break;
            case 'RJ':
                $getLatestDiagnosa->andWhere([
                    'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id', null),
                ])->orderBy(['soaprj_id' => SORT_DESC]);

                $getTtv = $this->asmedModel->find()->select([
                    'beratbadan_kg',
                    'tinggibadan_cm'
                ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
                if (empty($getTtv)) {
                    $getAskep = $this->askepModel->find()->select([
                        'tinggi_badan',
                        'berat_badan'
                    ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
                    $defaultData['tinggi_badan'] = $getAskep['tinggi_badan'];
                    $defaultData['berat_badan'] = $getAskep['berat_badan'];
                } else {
                    $defaultData['tinggi_badan'] = $getTtv['tinggibadan_cm'];
                    $defaultData['berat_badan'] = $getTtv['beratbadan_kg'];
                }
                break;
            default:
                # code...
                break;
        }
        $getLatestDiagnosa = $getLatestDiagnosa->asArray()->one();
        if ($getLatestDiagnosa && !empty($getLatestDiagnosa['a_diag_utama'])) {
            $decodedDiagnosa = json_decode($getLatestDiagnosa['a_diag_utama'], true);
            $defaultData['diagnosa_nama'] = $decodedDiagnosa['text'];
            $defaultData['diagnosa_id'] = isset($decodedDiagnosa['id']) ? $decodedDiagnosa['id'] : null;
        }

        $is_freetext = KonfigFarmasi::find()->select([
            'is_freetext', 'is_others', 'enable_split_kronis', 'hari_resep_kronis'
        ])->where([
            'konfigfarmasi_id' => DocoConstants::KONFIG_FARMASI_FREETEXT,
        ])->asArray()->one();
        $defaultData = array_merge($defaultData, $is_freetext);
        return $defaultData;
    }

    private function getDepo($type,$ruangan_id = 0)
    {
        $constDepo = [
            'RD' => 'apotek_rd',
            'RI' => 'apotek_ri',
            'RJ' => 'apotek_rj'
        ];
        $get_ruangan_default = Ruangan::find()->select(['ruangan_id', 'ruangan_default_depo_id'])->where(['ruangan_id' => $ruangan_id])->asArray()->one();
        if(!empty($get_ruangan_default['ruangan_default_depo_id'])){
            $default_depo = isset($get_ruangan_default['ruangan_default_depo_id']) ? $get_ruangan_default['ruangan_default_depo_id'] : 0;
        }else{
            $default_depo = $this->constans->actionGetId(isset($constDepo[$type]) ? $constDepo[$type] : 'apotek_rd');
        }
        return $default_depo;
    }

    public function actionSaveTemplateReseptur() {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $data_template = $data['data_template'];
            $data_template_detail = $data['data_template_detail'];
            $racikanKode = [];

            $list_racikan = Racikan::find()->all();
            $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');

            $modelResepTemp = new ResepTemp;
            $modelResepTemp->attributes = $data_template;

            $isNotUnique = ResepTemp::find()
                ->where([
                    'LOWER(reseptemp_nama)' => strtolower($modelResepTemp->reseptemp_nama),
                    'dokter_id' => $modelResepTemp->dokter_id
                ])->count();

            if($isNotUnique) {
                Yii::$app->response->statusCode = 422;
                return [
                    'data' => [],
                    'message' => 'Resep Template sudah ada',
                    'status' => 422
                ];
            }

            if ($modelResepTemp->validate()) {
                if ($modelResepTemp->save()) {
                    $idResepTemp = $modelResepTemp->reseptemp_id;
                    // define missing attributes
                    foreach ($data_template_detail as $key => $value) {
                        $data_template_detail[$key]['reseptemp_id'] = $idResepTemp;
                        $data_template_detail[$key]['racikan_id'] = $list_racikan[$value['racikan_id']];
                        $data_template_detail[$key]['harga'] = isset($value['qty']) ? $value['qty'] : null;
                        if ($value['rke'] == 'null') {
                            $data_template_detail[$key]['rke'] = '';
                        }
                        $data_template_detail[$key]['is_kronis'] = isset($value['is_kronis']) && $value['is_kronis'] == 'true' ? true : false;
                        $data_template_detail[$key]['signa'] = isset($data_template_detail[$key]['signa']) ? json_encode($data_template_detail[$key]['signa']) : null;
                        $data_template_detail[$key]['additional_data'] = json_encode($data_template_detail[$key]['additional_data']);
                    }

                    $a = ResepTempDetail::batchInsert($data_template_detail);
                    $transaction->commit();
                    $return = ['message' => 'Data Berhasil di simpan'];
                } else {
                    $transaction->rollBack();
                }
            } else {
                $errors = $this->helper->parseError($modelResepTemp->errors, 'ResepTemp');
                $return = [
                    'data' => $errors,
                    'message' => $errors,
                    'status' => 422
                ];
                Yii::$app->response->statusCode = 422;
                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 422;
            $return = [
                'data' => [],
                'message' => 'Terjadi kesalahan server',
                'status' => 422
            ];
            $transaction->rollBack();
        }
    }

    public function actionUpdateTemplateReseptur() {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $data_template = $data['data_template'];
            $data_template_detail = $data['data_template_detail'];

            $list_racikan = Racikan::find()->all();
            $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');
            /**
             *  1. INDEXING
             *  2. QUERY
             *  3 LOOP HASIL QUERY
             *  3a check hasil query ada di index
             *  3a1 update
             *  3a2 remove unset(index)
             *  3b. else
             *  3b1 delete
             *  4 selesai loop
             *  5. looping index sisa
             *  5a. create
             */

            // Cek Header
            $modelResepTemp = ResepTemp::findOne($data_template['reseptemp_id']);
            $modelResepTemp->attributes = $data_template;

            $isNotUnique = ResepTemp::find()
                ->where([
                    'LOWER(reseptemp_nama)' => strtolower($modelResepTemp->reseptemp_nama),
                    'dokter_id' => $modelResepTemp->dokter_id
                ])->andWhere([
                    '!=', 'reseptemp_id', $modelResepTemp->reseptemp_id
                ])->count();

            if ($isNotUnique) {
                Yii::$app->response->statusCode = 422;
                return [
                    'data' => [],
                    'message' => 'Resep Template sudah ada',
                    'status' => 422
                ];
            }

            if ($modelResepTemp->validate()) {
                if ($modelResepTemp->save()) {
                    // define missing attributes | formatting data
                    foreach ($data_template_detail as $key => $value) {
                        $data_template_detail[$key]['reseptemp_id'] = $modelResepTemp->reseptemp_id;
                        $data_template_detail[$key]['racikan_id'] = $list_racikan[$value['racikan_id']];
                        $data_template_detail[$key]['harga'] = isset($value['qty']) ? $value['qty'] : null;
                        if ($value['rke'] == 'null') {
                            $data_template_detail[$key]['rke'] = '';
                        }
                        $data_template_detail[$key]['is_kronis'] = isset($value['is_kronis']) && $value['is_kronis'] == 'true' ? true : false;
                        $data_template_detail[$key]['signa'] = isset($data_template_detail[$key]['signa']) ? json_encode($data_template_detail[$key]['signa']) : null;
                        $data_template_detail[$key]['additional_data'] = json_encode($data_template_detail[$key]['additional_data']);
                    }

                    // Step 1: Indexing
                    $mappingPost = ArrayHelper::index($data_template_detail, 'obatalkes_id');

                    // Step 2: Query
                    $qResepTempDetail = ResepTempDetail::find()->where(['reseptemp_id' => $modelResepTemp->reseptemp_id])->asArray()->all();

                    // Step 3: Loop Query
                    foreach ($qResepTempDetail as $k => $v) {
                        if (in_array($v['obatalkes_id'], array_keys($mappingPost))) {
                            // Step 3a1: kalau ada di list, update
                            $mResepTempDetail = ResepTempDetail::findOne($v['reseptempdetail_id']);
                            $mResepTempDetail->attributes = $mappingPost[$v['obatalkes_id']];
                            if ($mResepTempDetail->save()) {
                                // Step 3a2
                                unset($mappingPost[$v['obatalkes_id']]);
                            }
                        } else {
                            // Step 3b1: Delete resep template detail kalau gaada di list baru
                            (new ResepTempDetail)->delete([
                                'reseptempdetail_id' => $v['reseptempdetail_id']
                            ]);
                        }
                    }

                    // Step 5: Loop sisa data indexing
                    if (!empty($mappingPost)) {
                        foreach ($mappingPost as $key => $value) {
                            $newResepTempDetail = new ResepTempDetail;
                            $newResepTempDetail->attributes = $value;
                            $newResepTempDetail->save();
                        }
                    }
                    $transaction->commit();
                    $return = ['message' => 'Data Berhasil di simpan'];
                } else {
                    $transaction->rollBack();
                }
            } else {
                $errors = $this->helper->parseError($modelResepTemp->errors, 'ResepTemp');
                $return = [
                    'data' => $errors,
                    'message' => $errors,
                    'status' => 422
                ];
                Yii::$app->response->statusCode = 422;
                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 422;
            $return = [
                'data' => [],
                'message' => 'Terjadi kesalahan server',
                'status' => 422
            ];
            $transaction->rollBack();
        }
    }

    public function actionDeleteTemplateReseptur()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $reseptemp_id = $request->post('reseptemp_id', null);
            $getData = ResepTemp::findOne($reseptemp_id);
            if (!empty($getData)) {
                // Delete resep template header
                $getData->delete();

                // Delete resep template detail
                (new ResepTempDetail)->delete([
                    'reseptemp_id' => $reseptemp_id
                ]);
            } else {
                $result = [
                    'title' => 'Proses Gagal!',
                    'text' => 'Template resep tidak ditemukan.',
                    'status' => 422,
                ];
            }
            $transaction->commit();
            $result = [
                'title' => 'Proses Berhasil !',
                'text' => 'Template resep berhasil dihapus.',
            ];
            return $result;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(), 'status' => 422];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(), 'status' => 422];
        }
    }
    
    protected function validationResepturDetail()
    {
        $ruangan_id = ArrayHelper::getValue(Yii::$app->request->post('data_reseptur', []), 'ruangan_id', null);
        $pendaftaran_id = ArrayHelper::getValue(Yii::$app->request->post('data_reseptur', []), 'pendaftaran_id', null);
        // $obatalkes_ids = ArrayHelper::getColumn(Yii::$app->request->post('data_resepturdetail', []), 'obatalkes_id');
        $data_resepturdetail = Yii::$app->request->post('data_resepturdetail', []);
        $obatalkes_ids = [];
        foreach($data_resepturdetail as $_resepturdetail){
            if(isset($_resepturdetail['obatalkes_id']) && !empty($_resepturdetail['obatalkes_id'])){
                $obatalkes_ids[] = $_resepturdetail['obatalkes_id'];
            }
        }
        $instalasi_id = Yii::$app->jwt->instalasi_id;
      
        // cek dan filtering untuk obat freetext (others obatalkes_id = 0)
        $obatalkes_ids = array_filter($obatalkes_ids, function($val){
            if (is_numeric($val) && intval($val) != 0) {
                return true;
            }
            return false;
        });
        
        // TODO: 
        // 1. Ambil penjamin id dan kelas pelayanan id dari pendaftaran_t, jika ranap ke pasienadmisi_t
        // 2. setelah itu get data ke infostokobatalkes_fnr_new
        if (Yii::$app->jwt->instalasi_id == DocoConstants::INST_ID_RI) {
            $pendaftaran = PasienAdmisi::find()->select(['pendaftaran_id', 'kelaspelayanan_id', 'penjamin_id'])
                ->where(['pendaftaran_id' => $pendaftaran_id])->one();
        } else {
            $pendaftaran = Pendaftaran::find()->select(['pendaftaran_id', 'kelaspelayanan_id', 'penjamin_id'])
                ->where(['pendaftaran_id' => $pendaftaran_id])->one();
        }
        if(count($obatalkes_ids)>0){
            $obatalkes_ruangan = (new InfoStokObatAlkesFnrNew([
                'extParam' => [
                    $pendaftaran->penjamin_id ? $pendaftaran->penjamin_id : 0,
                    $pendaftaran->kelaspelayanan_id ? $pendaftaran->kelaspelayanan_id : 0 ,
                    $ruangan_id,
                ]
            ]))->find()->select(['obatalkes_id'])->where([
                'in', 'obatalkes_id', $obatalkes_ids
            ])->asArray()->all();
            
            $obatalkes_ruangan_ids = ArrayHelper::getColumn($obatalkes_ruangan, 'obatalkes_id');
            $obatalkes_tidak_tersedia = array_diff($obatalkes_ids, $obatalkes_ruangan_ids);
            
            return array_values($obatalkes_tidak_tersedia);
        }else{
            return [];
        }
    }

    protected function validationResepturStok() {
        $ruangan_id = ArrayHelper::getValue(Yii::$app->request->post('data_reseptur', []), 'ruangan_id', null);
        $data_resepturdetail = Yii::$app->request->post('data_resepturdetail', []);
        $pendaftaran_id = ArrayHelper::getValue(Yii::$app->request->post('data_reseptur', []), 'pendaftaran_id', null);
        $obatalkes_ids = [];
        $lookup = LookupTransaksi::find()
                ->where(['kode_transaksi' => DocoConstants::KONFIG_VALIDASI_STOK_OBAT_ALKES])
                ->asArray()->one();
        $konfig = ArrayHelper::getValue($lookup, 'additional_value');

        if (Yii::$app->jwt->instalasi_id == DocoConstants::INST_ID_RI) {
            $pendaftaran = PasienAdmisi::find()->select(['pendaftaran_id', 'kelaspelayanan_id', 'penjamin_id'])
                ->where(['pendaftaran_id' => $pendaftaran_id])->one();
        } else {
            $pendaftaran = Pendaftaran::find()->select(['pendaftaran_id', 'kelaspelayanan_id', 'penjamin_id'])
                ->where(['pendaftaran_id' => $pendaftaran_id])->one();
        }

        if ($konfig == 'true') {
            foreach($data_resepturdetail as $key => $value){
                if(isset($value['obatalkes_id']) && !empty($value['obatalkes_id'])){
                    $model = KetersediaanObatView::find()->select(['qty_tersedia', 'satuankecil_nama'])
                    ->andWhere(['obatalkes_id' => $value['obatalkes_id']])
                    ->andWhere(['ruangan_id' => $ruangan_id])
                    ->one();
                    $qty_tersedia = ArrayHelper::getValue($model, 'qty_tersedia');
                    $satuankecil_nama = ArrayHelper::getValue($model, 'satuankecil_nama');
                    if ($value['qty_reseptur'] > $qty_tersedia) {
                        $obatalkes_ids[$key]['obatalkes_id'] = $value['obatalkes_id'];
                        $obatalkes_ids[$key]['qty_tersedia'] = $qty_tersedia;
                        $obatalkes_ids[$key]['satuankecil_nama'] = $satuankecil_nama;
                        $obatalkes_ids[$key]['valid'] = false;
                    }
                }
            }
        } else {
            $obatalkes_ids = [];
        }

        $errValid = [
            'data' => $obatalkes_ids
        ];
        return $errValid;
    }

    protected static function getDataPasien($pendaftaran_id)
    {
        return  Yii::$app->db->createCommand("
            SELECT
                pendaftaran.pendaftaran_id,
                CASE
                    WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN
                        CASE
                            WHEN pasienadmisi_t.is_pasientitipan IS TRUE THEN pasienadmisi_t.kelas_ditagihkan_id
                            ELSE pasienadmisi_t.kelaspelayanan_id
                        END
                    ELSE
                        pendaftaran.kelaspelayanan_id
                    END AS kelaspelayanan_id,
                CASE
                    WHEN pasienadmisi_t.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id
            FROM pendaftaran_t pendaftaran
            LEFT JOIN (
                    SELECT
                        pasienadmisi.pasienadmisi_id,
                        pasienadmisi.penjamin_id,
                        pasienadmisi.kelaspelayanan_id,
                        pasienadmisi.kelas_ditagihkan_id,
                        pasienadmisi.is_pasientitipan
                        FROM pasienadmisi_t pasienadmisi
                ) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pendaftaran.pendaftaran_id = {$pendaftaran_id}
        ")->queryOne();
    }

    public static function extractValidationPayload($data_reseptur, $data_resepturdetail)
    {
        $payload = [];
        $payload['ruangan_id'] = ArrayHelper::getValue($data_reseptur, 'ruangan_id', 0);
        $payload['penjamin_id'] = ArrayHelper::getValue($data_reseptur, 'penjamin_id', 0);
        $payload['kelaspelayanan_id'] = ArrayHelper::getValue($data_reseptur, 'kelaspelayanan_id', 0);
        if (empty($payload['penjamin_id']) || empty($payload['kelaspelayanan_id'])) {
            $dataPasien = self::getDataPasien(ArrayHelper::getValue($data_reseptur, 'pendaftaran_id', null));
            $payload['penjamin_id'] = ArrayHelper::getValue($dataPasien, 'penjamin_id', 0);
            $payload['kelaspelayanan_id'] = ArrayHelper::getValue($dataPasien, 'kelaspelayanan_id', 0);
        }
        $payload['resepturdetail'] = [];
        $resepturdetail = [];
        foreach($data_resepturdetail as $_resepturdetail) {
            $obatalkes_id = ArrayHelper::getValue($_resepturdetail, 'obatalkes_id', 0);
            if (is_numeric($obatalkes_id) && intval($obatalkes_id) != 0) {
                $obatalkes_id = intval($obatalkes_id);
                if (isset($resepturdetail[$obatalkes_id])) {
                    $resepturdetail[$obatalkes_id]['qty_reseptur'] += ArrayHelper::getValue($_resepturdetail, 'qty_reseptur', null);
                } else {
                    $resepturdetail[$obatalkes_id] = [
                        'obatalkes_id' => $obatalkes_id,
                        'obatalkes_nama' => ArrayHelper::getValue($_resepturdetail, 'obatalkes_nama', null),
                        'satuankecil_nama' => ArrayHelper::getValue($_resepturdetail, 'satuankecil_text', null),
                        'qty_reseptur' => ArrayHelper::getValue($_resepturdetail, 'qty_reseptur', null),
                    ];
                }
            }
        }
        $payload['resepturdetail'] = $resepturdetail;
        return $payload;
    }

    protected static function validateResepturDetail($resepturdetail = [], $ruangan_id = 0, $penjamin_id = 0, $kelaspelayanan_id = 0)
    {
        $obatalkes_tidak_tersedia = [];
        $obatalkes_ids = array_keys($resepturdetail);
        if(count($obatalkes_ids) > 0){
            $obatalkes_ruangan = (new InfoStokObatAlkesFnrNew([
                'extParam' => [
                    $penjamin_id,
                    $kelaspelayanan_id,
                    $ruangan_id,
                ]
            ]))->find()->select(['obatalkes_id', 'obatalkes_nama', 'satuankecil_nama'])->where([
                'in', 'obatalkes_id', $obatalkes_ids
            ])->all();

            $obatalkes_ruangan_ids = ArrayHelper::getColumn($obatalkes_ruangan, 'obatalkes_id');
            $obatalkes_tidak_tersedia_ids = array_diff($obatalkes_ids, $obatalkes_ruangan_ids);
            if (!empty($obatalkes_tidak_tersedia_ids)) {
                foreach(array_values($obatalkes_tidak_tersedia_ids) as $id) {
                    $obatalkes_tidak_tersedia[] = $resepturdetail[$id];
                }
            }
        }

        return $obatalkes_tidak_tersedia;
    }

    protected static function validateResepturDetailStok($resepturdetail = [], $ruangan_id = 0)
    {
        $obatalkes_tidak_cukup = [];
        $obatalkes_ids = array_keys($resepturdetail);
        $lookup = LookupTransaksi::find()
                ->where(['kode_transaksi' => DocoConstants::KONFIG_VALIDASI_STOK_OBAT_ALKES])
                ->asArray()->one();

        $konfig = ArrayHelper::getValue($lookup, 'additional_value');
        if ($konfig == 'true' && !empty($obatalkes_ids)) {
            $implodeIdObat = implode(',', $obatalkes_ids);
            $ketersediaanObat = (new FgetKetersediaanobatFn(['extParam'=>[$ruangan_id, $implodeIdObat]]))
                ->find()
                ->select(['obatalkes_id', 'qty_tersedia'])
                ->asArray()
                ->all();

            foreach($ketersediaanObat as $obat) {
                $detail = $resepturdetail[$obat['obatalkes_id']];
                if ($detail['qty_reseptur'] > $obat['qty_tersedia']) {
                    $detail['valid'] = false;
                    $detail['qty_tersedia'] = $obat['qty_tersedia'];
                    $obatalkes_tidak_cukup[] = $detail;
                }
            }
        }

        return $obatalkes_tidak_cukup;
    }

    /** 
     * new get data default resep RJ
     */
    public function actionDefaultDataResepturPelayanan()
    {
        try {
            return [
                'defaultData' => $this->actionDefaultDataReseptur(),
                'lookupTransaksi' => $this->actionGetLookupTransaksiByKode(),
            ];
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

    /** get multiple lookup transaksi */
    public function actionGetLookupTransaksiByKode()
    {
        $request = Yii::$app->request;
        $kode_transaksi = $request->get('kode_transaksi', []);

        $result = [];
        if (!empty($kode_transaksi)) {
            $result = Yii::$app->cache->getOrSet(implode('-', $kode_transaksi), function ($cache) use ($kode_transaksi) {
                $result = LookupTransaksi::find()->where(['IN', 'kode_transaksi', $kode_transaksi])->asArray()->all();
                return ArrayHelper::index($result, 'kode_transaksi');
            }, 300);
        }
        return $result;
    }
}
