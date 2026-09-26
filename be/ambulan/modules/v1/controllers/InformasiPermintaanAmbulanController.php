<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\PesanAmbulan;
use app\modules\v1\models\PesanAmbulanDetail;
use app\modules\v1\models\InfoPesanAmbulanView;
use app\modules\v1\models\InfoPesanAmbulanDetailView;
use app\modules\v1\models\KetersediaanAmbulanView;
use app\modules\v1\models\AmbulanDetailView;
use app\modules\v1\models\AmbulanDetail;
use app\modules\v1\models\TarifAmbulanView;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\ObatAlkesDetailView;
use app\modules\v1\models\PasienRsAmbulanView;
use app\modules\v1\models\ObatAmbulanView;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\PemakaianAmbulan;
use app\modules\v1\models\PemakaianAmbulanDetail;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\ObatAlkesFn;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\InfoPemakaianAmbulanDetailView;
use app\modules\v1\models\InfoPemakaianAmbulanView;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\cache\Cache;
use Doco\Services\KasirService;

class InformasiPermintaanAmbulanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPesanAmbulanView';
    public function verbs()
    {
        $verbs = parent::verbs();
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
        $model = new InfoPesanAmbulanView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pesanambulan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesanambulan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pesanambulan']);
            }
            if (isset($_GET['advanced-filter']['jenis_ambulan'])) {
                $query->andWhere([
                    'jenis_ambulan' => $_GET['advanced-filter']['jenis_ambulan']
                ]);
                unset($_GET['advanced-filter']['jenis_ambulan']);
            }
        }
        $query->andWhere(['between', 'tgl_pesanambulan', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['created_date' => SORT_DESC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $id_parent = $request->post('id_parent');
        $model = PesanAmbulan::find()->where([
            'pesanambulan_id' => $id_parent
        ])->one();
        if (empty($model)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Tidak ada data yang diproses.'
            ];
        }
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        try {
            $dataInsert = [];
            $dataTindakan = $request->post('cacheTindakan', "{}");
            $dataCacheTindakan = json_decode($dataTindakan, true);

            $dataObat = $request->post('cacheObat', "{}");
            $dataCacheObat = json_decode($dataObat, true);

            $postData = $post['post']['PesanAmbulanForm'];
            $ambulan_id  = isset($postData['ambulan_id']) ? $postData['ambulan_id'] : null;
            $model->ambulan_id = $ambulan_id;
            $model->tgl_pesanambulan = !empty($postData['tgl_pesanambulan'])
                ? date('Y-m-d', strtotime($postData['tgl_pesanambulan'])) : null;
            $model->ruangan_id = Yii::$app->jwt->ruangan_id;
            $model->status_ambulan = DocoConstants::PESAN_AMBULAN;

            if ($postData['jenis'] == "luar") {
                $model->tgl_lahir = !empty($postData['tgl_lahir'])
                    ? date('Y-m-d', strtotime($postData['tgl_lahir'])) : $postData['tgl_lahir'];
                $model->umur = DocoHelpers::convertDateToAge($model->tgl_lahir);
                $model->pemesan = !empty($postData['pemesan']) ? $postData['pemesan'] : null;
                $model->jenis_kelamin = !empty($postData['jenis_kelamin']) ? $postData['jenis_kelamin'] : null;
                $model->tempat_lahir = !empty($postData['tempat_lahir']) ? $postData['tempat_lahir'] : null;
                $model->asal_pasien = !empty($postData['asal_pasien']) ? $postData['asal_pasien'] : null;
                $model->keluhan = !empty($postData['keluhan']) ? $postData['keluhan'] : null;
                $model->is_nafas = !is_null($postData['is_nafas']) ? $postData['is_nafas'] : null;
                $model->is_sadar = !is_null($postData['is_sadar']) ? $postData['is_sadar'] : null;
                $model->is_nadi = !is_null($postData['is_nadi']) ? $postData['is_nadi'] : null;
                $model->nama_pj = !empty($postData['nama_pj']) ? $postData['nama_pj'] : null;
                $model->kontak_pj = !empty($postData['kontak_pj']) ? $postData['kontak_pj'] : null;
            } else {
                $model->pasien_id = !empty($postData['pasien_id']) ? $postData['pasien_id'] : null;
                $model->pendaftaran_id = !empty($postData['pendaftaran_id']) ? $postData['pendaftaran_id'] : null;
                $model->detaknadi = !empty($postData['detaknadi']) ? $postData['detaknadi'] : null;
                $model->kesadaran = !empty($postData['kesadaran']) ? $postData['kesadaran'] : null;
                $model->respirasi = !empty($postData['respirasi']) ? $postData['respirasi'] : null;
                $model->saturasi = !empty($postData['saturasi']) ? $postData['saturasi'] : null;
                $model->tanda_vital = !empty($postData['tanda_vital']) ? $postData['tanda_vital'] : null;
                $model->td_diastolic = !empty($postData['td_diastolic']) ? $postData['td_diastolic'] : null;
                $model->tujuan_pasien = !empty($postData['tujuan_pasien']) ? $postData['tujuan_pasien'] : null;
            }

            if ($model->save()) {
                $idParent = $model->pesanambulan_id;
                if (!empty($dataCacheTindakan) && is_array($dataCacheTindakan)) {
                    foreach ($dataCacheTindakan as $key => $value) {
                        if (empty($value['harga_tariftindakan'])) continue;
                        $dataInsert[] = [
                            'pesanambulan_id' => $idParent,
                            'daftartindakan_id' => $value['daftartindakan_id'],
                            'is_default' => ($value['is_default'] == "Tetap") ? true : false,
                            'qty_tindakan' => $value['qty'],
                            'tarif_satuan' => $value['harga_tariftindakan'],
                            'jumlah_tarif' => (int) $value['jumlah_tarif'],
                        ];
                    }
                }

                if (!empty($dataCacheObat) && is_array($dataCacheObat)) {
                    foreach ($dataCacheObat as $key => $value) {
                        if (empty($value['stok'])) continue;
                        $dataInsert[] = [
                            'pesanambulan_id' => $idParent,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'is_default' => null,
                            'qty_obat' => $value['qty'],
                            'tarif_satuan' => null,
                            'jumlah_tarif' => null,
                        ];
                    }
                }

                $delete = (new PesanAmbulanDetail)->delete([
                    'pesanambulan_id' => $id_parent
                ]);
                if (!empty($dataInsert)) {
                    PesanAmbulanDetail::batchInsert($dataInsert);
                }
                $transaction->commit();

                $response = [
                    'text' => 'Permintaan Ambulan berhasil disimpan',
                    'title' => 'Proses berhasil !'
                ];

                return $response;
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function getKomponenTarifTindakan($idDaftarTindakan, $data)
    {
        try {
            $model = InfoTarifRs::find()
                ->where(['instalasi_id' => $data['instalasi_id']])
                ->andWhere(['ruangan_id' => $data['ruangan_id']])
                ->andWhere(['kelaspelayanan_id' => $data['kelaspelayanan_id']])
                ->andWhere(['penjamin_id' => $data['penjamin_id']]);
            $result = $model->andWhere(['IN', 'daftartindakan_id', $idDaftarTindakan])
                ->asArray()
                ->all();

            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionSavePemakaian()
    {
        $request = Yii::$app->request;
        $user_login = Yii::$app->user->identity->pegawai_id;
        $now = date('Y-m-d H:i:s');
        $post = $request->post();
        // $dataPoses = json_decode($post['data_proses'], true);
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $pesanambulan_id = $request->post('id_parent');
        $dataPegawai = $request->post('pegawai');
        $dataObatAlkes = $request->post('obat_alkes');
        $dataTindakan = $request->post('tindakan');
        $dataPesanAmbulan = $request->post('pesan_ambulan');
        $pemakaianAmbulan = $request->post('pemakaian');
        // $pesanambulan_id = $dataPoses['id_parent'];
        // $dataPegawai = $dataPoses['pegawai'];
        // $dataObatAlkes = $dataPoses['obat_alkes']; 
        // $dataTindakan = $dataPoses['tindakan'];
        // $dataPesanAmbulan = $dataPoses['pesan_ambulan'];
        $model = PesanAmbulan::find()
            ->where(['pesanambulan_id' => $pesanambulan_id])->one();
        if (empty($model)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Tidak ada data yang diproses.'
            ];
        }
        $modelPemakaianAmbulan =  new PemakaianAmbulan;
        $modelPemakaianAmbulanDetail =  new PemakaianAmbulanDetail;
        $instalasi_id = Yii::$app->jwt->instalasi_id;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        // $instalasi_id = getallheaders()['instalasi_id'];
        // $ruangan_id = getallheaders()['ruangan_id'];

        try {
            // $pemakaianAmbulan = $dataPoses['pemakaian'];
            unset($pemakaianAmbulan['tgl_pemakaian']);
            unset($pemakaianAmbulan['jenis']);
            $modelPemakaianAmbulan->attributes = $pemakaianAmbulan;
            if ($modelPemakaianAmbulan->validate()) {
                if ($modelPemakaianAmbulan->save()) {
                    $pemakaianambulan_id = $modelPemakaianAmbulan->pemakaianambulan_id;
                    if (!empty($dataPegawai) && is_array($dataPegawai)) {
                        foreach ($dataPegawai as $key => $value) {
                            // if (empty($value['pegawai_id'])) continue;
                            $dataInsert[] = [
                                'pemakaianambulan_id' => $pemakaianambulan_id,
                                'petugas_id' => $value['pegawai_id']
                            ];
                        }
                    }

                    if (!empty($dataObatAlkes) && is_array($dataObatAlkes)) {
                        foreach ($dataObatAlkes as $key => $value) {
                            if (empty($value['stok'])) continue;
                            $dataInsert[] = [
                                'pemakaianambulan_id' => $pemakaianambulan_id,
                                'obatalkes_id' => $value['obatalkes_id'],
                                'qty' => $value['qty'],
                            ];
                        }
                    }
                    $delete = (new PemakaianAmbulanDetail)->delete([
                        'pemakaianambulan_id' => $pemakaianambulan_id
                    ]);
                    if (!empty($dataInsert)) {
                        PemakaianAmbulanDetail::batchInsert($dataInsert);
                    }

                    $model->status_ambulan = DocoConstants::AMBULAN_SEDANG_DIPAKAI;
                    $model->status_pesan = DocoConstants::AMBULAN_SUDAH_DIPROSES;
                    $model->pemakaianambulan_id = $pemakaianambulan_id;
                    $model->update();

                    if (empty($dataTindakan)) {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal !',
                            'text' => 'Data Tindakan Pelayanan tidak ada.'
                        ];
                    }
                    /* check tarif and  tindakan pelayanan  
                    *  start
                    */
                    $tindakanPelayanan = [];
                    $paramTindakan = [];
                    $extTindakan = [];
                    $total_biaya_tindakan = 0;
                    foreach ($dataTindakan as $key => $value) {

                        $paramTindakan[$value['daftartindakan_id']] = $value['daftartindakan_id'];
                        $extTindakan = [
                            'instalasi_id' => $instalasi_id,
                            'ruangan_id' => $ruangan_id,
                            'kelaspelayanan_id' =>  !empty($dataPesanAmbulan['kelaspelayanan_id']) ? $dataPesanAmbulan['kelaspelayanan_id'] : 3,
                            'penjamin_id' => !empty($dataPesanAmbulan['penjamin_id']) ? $dataPesanAmbulan['penjamin_id'] : 1,
                            'daftartindakan_id' => $value['daftartindakan_id'],
                        ];
                        $total_biaya_tindakan += $value['jumlah_tarif'];
                        $tindakanPelayanan[$key] = [
                            'kelaspelayanan_id' => !empty($dataPesanAmbulan['kelaspelayanan_id']) ? $dataPesanAmbulan['kelaspelayanan_id'] : 0,
                            'pasien_id' => !empty($dataPesanAmbulan['pasien_id']) ? $dataPesanAmbulan['pasien_id'] : 0,
                            'instalasi_id' => $instalasi_id,
                            'daftartindakan_id' => !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null,
                            'tipepaket_id' => NULL,
                            'carabayar_id' => !empty($dataPesanAmbulan['carabayar_id']) ? $dataPesanAmbulan['carabayar_id'] : 0,
                            'pendaftaran_id' => !empty($dataPesanAmbulan['pendaftaran_id']) ? $dataPesanAmbulan['pendaftaran_id'] : 0,
                            'jeniskasuspenyakit_id' => DocoConstants::SATUAN_TINDAKAN_KALI,
                            'ruangan_id' => $ruangan_id,
                            'pasienmasukpenunjang_id' => NULL,
                            'penjamin_id' => !empty($dataPesanAmbulan['penjamin_id']) ? $dataPesanAmbulan['penjamin_id'] : 0,
                            'pasienadmisi_id' => NULL,
                            'tgl_tindakan' => $now,
                            'tarif_rsakomodasi' => 0,
                            'tarif_medis' => 0,
                            'tarif_paramedis' => 0,
                            'tarif_bhp' => 0,
                            'tarif_satuan' => $value['harga_tariftindakan'],
                            'tarif_tindakan' => 0, #$tarif_tindakan,
                            'tarifcyto_tindakan' => 0,
                            'satuan_tindakan' => $value['harga_tariftindakan'],
                            'qty_tindakan' => $value['qty'],
                            'cyto_tindakan' => NULL,
                            'dokterpenanggungjawab_id' => NULL,
                            'discount_tindakan' => 0,
                            'pembebasan_tindakan' => 0,
                            'subsidiasuransi_tindakan' => 0,
                            'subsidipemerintah_tindakan' => 0,
                            'subsisidirumahsakit_tindakan' => 0,
                            'uangditerima_tindakan' => 0,
                            'additional_data' => NULL, #json_encode($arrListKomponen),
                            'pembulatan' => 0,
                            'pemakaianambulan_id' => $pemakaianambulan_id
                        ];
                    }

                    $arrTindakan = $this->getKomponenTarifTindakan($paramTindakan, $extTindakan);
                    $arrListKomponen = [];
                    $harga_tariftindakan = [];
                    if (!empty($arrTindakan)) {
                        foreach ($arrTindakan as $k => $v) {
                            if ($v['komponentarif_id'] == 6) {
                                if (!empty($v['harga_tariftindakan'])) {
                                    $harga_tariftindakan[$v['daftartindakan_id']] = $v['harga_tariftindakan'];
                                }
                            }

                            if ($v['komponentarif_id'] != 6) {
                                $arrListKomponen[$v['daftartindakan_id']][] = $v;
                            }
                        }
                    }

                    $res_tindakanPelayanan = [];
                    foreach ($tindakanPelayanan as $key => $value) {
                        if (isset($harga_tariftindakan[$key])) {
                            $price_tariftindakan = $harga_tariftindakan[$value['daftartindakan_id']];
                        } else {
                            $price_tariftindakan =  $value['tarif_satuan'];
                        }
                        $tarif_tindakan = ($price_tariftindakan * $value['qty_tindakan']) + 0;
                        $value['tarif_tindakan'] = $tarif_tindakan;
                        if (isset($arrListKomponen[$key])) {
                            foreach ($arrListKomponen[$key] as $k => $v) {
                                $data_listKomponen['list_komponen'][] = [
                                    'komponentarif_id' => $v['komponentarif_id'],
                                    'tindakanpelayanan_id' => null,
                                    'tarif_kompsatuan' => $v['harga_tariftindakan'],
                                    'tarif_tindakankomp' => ($value['qty_tindakan'] * $v['harga_tariftindakan']) + 0,
                                    'tarif_tindakankomp' => 0,
                                    'tarifcyto_tindakankomp' => 0,
                                    'subsidiasuransikomp' => 0,
                                    'subsidipemerintahkomp' => 0,
                                    'subsidirumahsakitkomp' => 0,
                                    'iurbiayakomp' => 0
                                ];
                            }
                            $value['additional_data'] = json_encode($data_listKomponen);
                        }
                        $res_tindakanPelayanan[] = $value;
                    }

                    // $resTindakanPelayanan = TindakanPelayanan::batchInsert($res_tindakanPelayanan, false);

                    /* check tarif and  tindakan pelayanan  
                    *  end
                    */
                    /* check stok obat alkes   
                    *  start
                    * 1. hapus pemakaianambulan_detail obat sblumnya by pemakaianambulan_id
                    * 2. pemakaianambulan_detail
                    * 3. obatalkespasien_t
                    * 4. stokobatalkes_t 
                    */
                    $tanggalBerlaku = date('Y-m-d');
                    $konfig = $connection->createCommand("
                        SELECT metodeantrian FROM konfigfarmasi_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND konfigfarmasi_aktif = true
                        AND is_active = true
                    ")->queryOne();
                    // Mencari Metode dengan nilai default FEFO
                    $currentMetode = LogicStokObatAlkes::FEFO;
                    if ($konfig) {
                        $currentMetode = isset($konfig['metodeantrian'])
                            ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                    }

                    $arr_obatalkesId = [];
                    $arr_pemakaianAmbDetail = [];
                    $arr_obatAlkesPasien = [];
                    $total_biaya_obat = 0;
                    if (!empty($dataObatAlkes) && is_array($dataObatAlkes)) {
                        foreach ($dataObatAlkes as $key => $value) {
                            $arr_obatalkesId[] = $value['obatalkes_id'];

                            $arr_obatAlkesPasien[$value['obatalkes_id']] = [
                                'ruangan_id' => $ruangan_id,
                                'carabayar_id' => !empty($dataPesanAmbulan['carabayar_id']) ? $dataPesanAmbulan['carabayar_id'] : 0,
                                'pendaftaran_id' => !empty($dataPesanAmbulan['pendaftaran_id']) ? $dataPesanAmbulan['pendaftaran_id'] : 0,
                                'pasien_id' => !empty($dataPesanAmbulan['pasien_id']) ? $dataPesanAmbulan['pasien_id'] : 0,
                                'penjamin_id' => !empty($dataPesanAmbulan['penjamin_id']) ? $dataPesanAmbulan['penjamin_id'] : 0,
                                'penjualanresep_id' => NULL,
                                'tglpelayanan' => $now,
                                'pegawai_id' => $user_login,
                                'racikan_id' => NULL,
                                'satuankecil_id' => 0,
                                'obatalkes_id' => $value['obatalkes_id'],
                                'qty_oa' => $value['qty'],
                                'signa_oa' => NULL,
                                'rke' => NULL,
                                'hargasatuan_oa' => NULL,
                                'harganetto_oa' => NULL,
                                'hargajual_oa' => NULL,
                                'resepturdetail_id' => NULL,
                                'created_by' => $user_login,
                                'additional_data' => NULL
                            ];

                            /*if (empty($value['petugas_id'])){
                                $arr_pemakaianAmbDetail[] = [
                                    'pemakaianambulan_id' =>  $pemakaianambulan_id, #$pemakaianambulan_id,
                                    'obatalkes_id' => $value['obatalkes_id'],
                                    'qty' => $value['qty'],
                                ];
                            }*/
                        }
                    }
                    /*if (!empty($arr_pemakaianAmbDetail)) {
                        PemakaianAmbulanDetail::batchInsert($arr_pemakaianAmbDetail);
                    }*/
                    $modelObatAlkesFn = new ObatAlkesFn();
                    $getObatAlkesFn = $modelObatAlkesFn::find()
                        ->where(['IN', 'obatalkes_id', $arr_obatalkesId])
                        ->andWhere(['ruangan_id' => $ruangan_id])
                        ->all();
                    if (!empty($getObatAlkesFn)) {
                        foreach ($getObatAlkesFn as $key => $value) {
                            if (isset($arr_obatAlkesPasien[$value->obatalkes_id])) {
                                $arr_obatAlkesPasien[$value->obatalkes_id]['satuankecil_id'] = $value->satuankecil_id;
                                $arr_obatAlkesPasien[$value->obatalkes_id]['hargasatuan_oa'] = $value->jml_hargajual;
                                $arr_obatAlkesPasien[$value->obatalkes_id]['harganetto_oa'] = $value->jml_harganetto;
                                $arr_obatAlkesPasien[$value->obatalkes_id]['hargajual_oa'] = $arr_obatAlkesPasien[$value->obatalkes_id]['qty_oa'] *
                                    $value->jml_hargajual;
                                $arr_obatAlkesPasien[$value->obatalkes_id]['pemakaianambulan_id'] = $pemakaianambulan_id; #$pemakaianambulan_id;
                            }
                        }
                    }
                    ObatAlkesPasien::batchInsert($arr_obatAlkesPasien);
                    $arrStokObatAlkes = [];
                    $getAlkesPasien = ObatAlkesPasien::find()->where(['pemakaianambulan_id' => $pemakaianambulan_id])->all(); #$pemakaianambulan_id
                    if (!empty($getAlkesPasien)) {
                        foreach ($getAlkesPasien as $key => $value) {
                            $arrStokObatAlkes[$value['obatalkes_id']] = [
                                'obatalkes_id' => $value['obatalkes_id'],
                                'qty_satuanpakai' => $value['qty_oa'],
                                'satuankecil_id' => $value['satuankecil_id'],
                                'obatalkespasien_id' => $value['obatalkespasien_id'],
                            ];
                        }
                        if (!empty($getObatAlkesFn)) {
                            foreach ($getObatAlkesFn as $key => $value) {
                                if (isset($arrStokObatAlkes[$value->obatalkes_id])) {
                                    $arrStokObatAlkes[$value->obatalkes_id]['satuankecil_id'] = $value->satuankecil_id;
                                    $arrStokObatAlkes[$value->obatalkes_id]['hargasatuan_oa'] = $value->jml_hargajual;
                                    $arrStokObatAlkes[$value->obatalkes_id]['harganetto_oa'] = $value->jml_harganetto;
                                    $arrStokObatAlkes[$value->obatalkes_id]['hargajual_oa'] = $arr_obatAlkesPasien[$value->obatalkes_id]['qty_oa'] *
                                        $value->jml_hargajual;
                                    $arrStokObatAlkes[$value->obatalkes_id]['persendiscount'] = $value->persen_disc;
                                    $arrStokObatAlkes[$value->obatalkes_id]['persenppn'] = $value->persen_ppn;
                                    $arrStokObatAlkes[$value->obatalkes_id]['persenmargin'] = $value->persen_margin;
                                    $arrStokObatAlkes[$value->obatalkes_id]['jmldiscount'] = $value->jml_discount;
                                    $arrStokObatAlkes[$value->obatalkes_id]['jmlmargin'] = $value->jml_margin;
                                    $arrStokObatAlkes[$value->obatalkes_id]['jmlppn'] = $value->jml_ppn;

                                    $total_biaya_obat += $arrStokObatAlkes[$value->obatalkes_id]['hargajual_oa'];
                                }
                            }
                        }
                    }
                    $res_total_biaya = $total_biaya_tindakan + $total_biaya_obat;
                    $getPesanAmbulan = PemakaianAmbulan::find()
                        ->where(['pemakaianambulan_id' => $pemakaianambulan_id])->one();
                    $getPesanAmbulan->total_biaya = $res_total_biaya;
                    $getPesanAmbulan->update();
                    $_POST['ruangan_id'] = $ruangan_id;

                    // Execute By Condition
                    LogicStokObatAlkes::$distribusi = false;
                    if ($currentMetode === LogicStokObatAlkes::FEFO) {
                        $methode = LogicStokObatAlkes::methodeFEFO($arrStokObatAlkes, $now);
                    } else {
                        $methode = LogicStokObatAlkes::methodeFIFO($arrStokObatAlkes, $now);
                    }
                    /* check stok obat alkes  
                    *  end
                    */

                    $dataPendaftaran = [];
                    if (isset($pemakaianAmbulan['pendaftaran_id']) && !empty($pemakaianAmbulan['pendaftaran_id'])) {
                        $dataPendaftaran = PasienRsAmbulanView::find()
                            ->select([
                                'kelaspelayanan_id',
                                'penjamin_id',
                                'pendaftaran_id',
                                'no_pendaftaran'
                            ])
                            ->andWhere([
                                'pendaftaran_id' => $pemakaianAmbulan['pendaftaran_id']
                            ])
                            ->asArray()
                            ->one();
                    }
                    if (!empty($dataPendaftaran) && !empty($pemakaianAmbulan['estimasi_jarak'])) {
                        $dataTarif = TarifAmbulanView::find()
                            ->select([
                                'ambulan_id',
                                'no_polisi',
                                'is_default',
                                'kelaspelayanan_id',
                                'penjamin_id',
                                'daftartindakan_id',
                                'daftartindakan_nama'
                            ])
                            ->andWhere([
                                'is_default' => true,
                                'ambulan_id' => $dataPesanAmbulan['ambulan_id'],
                                'kelaspelayanan_id' => $dataPendaftaran['kelaspelayanan_id'],
                                'penjamin_id' => $dataPendaftaran['penjamin_id'],
                                'komponentarif_id' => 6
                            ])
                            ->asArray()
                            ->all();
                        $detailTindakan = [];
                        $usedTindakanId = [];
                        foreach ($dataTarif as $defaultTarif) {
                            $usedTindakanId[] = $defaultTarif['daftartindakan_id'];
                            $detailTindakan[] = array_merge($defaultTarif, [
                                'qty' => $pemakaianAmbulan['estimasi_jarak'],
                                "dokter_id" => null,
                                "perawat_id" => null,
                                "tipepaket_id" => null,
                                "is_cyto" => false
                            ]);
                        }
                        $dataTindakan = array_values($dataTindakan);
                        foreach ($dataTindakan as $indexTindakan => $tindakan) {
                            if (isset($tindakan['is_default']) && trim(strtolower($tindakan['is_default'])) == 'tetap' || in_array($tindakan['daftartindakan_id'], $usedTindakanId)) {
                                unset($dataTindakan[$indexTindakan]);
                            }
                            $usedTindakanId[] = $tindakan['daftartindakan_id'];
                        }
                        $detailTindakan = array_merge($detailTindakan, $dataTindakan);
                        if (!empty($detailTindakan)) {
                            (new KasirService)->tagihan($dataPendaftaran, $detailTindakan);
                        }
                    }

                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                        'pemakaianambulan_id' => DocoHelpers::encrypt($pemakaianambulan_id),
                        'id' => $model->pesanambulan_id,
                        'id_encrypt' => DocoHelpers::encrypt($model->pesanambulan_id),
                        'no_pesanambulan' => $model->no_pesanambulan
                    ];
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($modelPemakaianAmbulan->errors, 'FormProses');
                    $responseMessage =  ['data' => $errors, 'status' => 422];
                }
            } else {
                $transaction->rollBack();
                $response = $modelPemakaianAmbulan->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionGetAttributes()
    {
        $lookUp = Cache::getLookUp('status_pesanambulan');

        return [
            'status_ambulan' => $lookUp
        ];
    }

    public function actionGetPelayananAmbulan()
    {
        $lookUp = Cache::getLookUp('pelayanan_ambulan');

        return $lookUp;
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk mengganti data di table
     * @attribute #tgl_pesanambulan# => tanggal pesan
     * @attribute #no_pesanambulan# => nomor pesan
     * @attribute #title# => title
     * @attribute #pemesan# => pemesan
     * @attribute #jenis_kelamin# => jenis_kelamin
     * @attribute #tempat_lahir# => tempat_lahir
     * @attribute #tgl_lahir# => tgl_lahir
     * @attribute #asal_pasien# => asal_pasien
     * @attribute #keluhan# => keluhan
     * @attribute #is_sadar# => is_sadar
     * @attribute #is_nafas# => is_nafas
     * @attribute #is_nadi# => is_nadi
     * @attribute #jenis_ambulan# => jenis_ambulan
     * @attribute #kontak_pj# => kontak_pj
     * @attribute #nama_pj# => nama_pj
     */

    public function actionExportPdf($id)
    {
        $request = Yii::$app->request;
        $no_pesanambulan = $request->get('no_pesanambulan');
        $jenis = $request->get('jenis');

        $title = 'Permintaan Ambulan';
        $model = new PesanAmbulan;
        $header = $model::find()->where(['pesanambulan_id' => $id])->one();
        $info = InfoPesanAmbulanView::find()->where(['pesanambulan_id' => $id])->asArray()->one();
        $detail = InfoPesanAmbulanDetailView::find()->where(['pesanambulan_id' => $id])->asArray()->all();
        $print = new DocoPrint();
        $print->attributes = [
            '#no_pesanambulan#' => $info['no_pesanambulan'],
            '#tgl_pesanambulan#' => date('d M Y', strtotime($header->tgl_pesanambulan)),
            '#pemesan#' => $header->pemesan,
            '#nama_pj#' => $header->nama_pj,
            '#jenis_kelamin#' => $info['jns_kelamin'],
            '#tempat_lahir#' => $header->tempat_lahir,
            '#tgl_lahir#' => date('d M Y', strtotime($header->tgl_lahir)),
            '#umur#' => DocoHelpers::getUmur($header->tgl_lahir),
            '#asal_pasien#' => $header->asal_pasien,
            '#keluhan#' => !empty($header->keluhan) ? $header->keluhan : '-',
            '#is_sadar#' => ($header->is_sadar == true) ? "Sadar" : "Tidak Sadar",
            '#is_nafas#' => ($header->is_nafas == true) ? "Ada" : "Tidak Ada",
            '#is_nadi#' => ($header->is_nadi == true) ? "Ada" : "Tidak Ada",
            '#jenis_ambulan#' => $info['jenis_ambulan'],
            '#kontak_pj#' => $header->kontak_pj,
            '#title#' => $title,
            '#no_polisi#' => $info['no_polisi'],
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $detail,
            ]),
        ];

        $print->Output();
    }


    /**
     * @controller actionExportPdfPasienRs
     * @attribute #datatable# => Untuk mengganti data di table
     * @attribute #tgl_pesanambulan# => tanggal pesan
     * @attribute #no_pesanambulan# => nomor pesan
     * @attribute #title# => title
     * @attribute #pemesan# => pemesan
     * @attribute #jenis_kelamin# => jenis_kelamin
     * @attribute #tempat_lahir# => tempat_lahir
     * @attribute #tgl_lahir# => tgl_lahir
     * @attribute #no_rekam_medik# => no_rekam_medik
     * @attribute #tujuan_pasien# => tujuan_pasien
     * @attribute #kesadaran# => kesadaran
     * @attribute #tanda_vital# => tanda_vital
     * @attribute #td_diastolic# => td_diastolic
     * @attribute #detaknadi# => detaknadi
     * @attribute #respirasi# => respirasi
     * @attribute #saturasi# => saturasi
     * @attribute #jenis_ambulan# => jenis_ambulan
     * @attribute #instalasi_asal# => instalasi_asal
     * @attribute #ruangan_asal# => ruangan_asal
     * @attribute #no_kamar# => no_kamar
     * @attribute #no_bed# => no_bed
     * @attribute #diagnosa# => diagnosa
     * @attribute #saturasi# => saturasi
     */

    public function actionExportPdfPasienRs($id)
    {
        $request = Yii::$app->request;
        $no_pesanambulan = $request->get('no_pesanambulan');
        $jenis = $request->get('jenis');

        $title = 'Permintaan Ambulan';
        $model = new PesanAmbulan;
        $header = $model::find()->where(['pesanambulan_id' => $id])->one();

        $info = InfoPesanAmbulanView::find()->where(['pesanambulan_id' => $id])->asArray()->one();
        $detail = InfoPesanAmbulanDetailView::find()
            ->where(['pesanambulan_id' => $id])
            ->andWhere(['IS NOT', 'daftartindakan_id', null])
            ->asArray()->all();

        $detailObat = InfoPesanAmbulanDetailView::find()
            ->where(['pesanambulan_id' => $id])
            ->andWhere(['IS NOT', 'obatalkes_id', null])
            ->asArray()->all();

        $pasienRs = PasienRsAmbulanView::find()->where(['pendaftaran_id' => $header->pendaftaran_id])->one();
        $diagnosa = !empty($pasienRs->diagnosa) ? $pasienRs->diagnosa : '-';
        $nama_diagnosa = $diagnosa;

        $print = new DocoPrint();
        $print->attributes = [
            '#no_pesanambulan#' => $info['no_pesanambulan'],
            '#tgl_pesanambulan#' => date('d M Y', strtotime($header->tgl_pesanambulan)),
            '#pemesan#' => $info['nama_pemesan'],
            '#no_rekam_medik#' => $info['no_rekam_medik'],
            '#jenis_kelamin#' => $info['jns_kelamin'],
            '#tempat_lahir#' => $info['tempat_lahir'],
            '#tgl_lahir#' => date('d M Y', strtotime($info['tgl_lahir'])),
            '#umur#' => DocoHelpers::getUmur($info['tgl_lahir']),
            '#tujuan_pasien#' => $header->tujuan_pasien,
            '#kesadaran#' => $header->kesadaran,
            '#tanda_vital#' => $header->tanda_vital,
            '#td_diastolic#' => $header->td_diastolic,
            '#detaknadi#' => $header->detaknadi,
            '#respirasi#' => $header->respirasi,
            '#saturasi#' => $header->saturasi,
            '#instalasi_asal#' => $info['instalasi_asal'],
            '#ruangan_asal#' => $info['ruangan_asal'],
            '#no_kamar#' => !empty($info['kamarruangan_nokamar']) ? $info['kamarruangan_nokamar'] : '-',
            '#no_bed#' => !empty($info['no_tempattidur']) ? $info['no_tempattidur'] : '-',
            '#jenis_ambulan#' => $info['jenis_ambulan'],
            '#diagnosa#' => $pasienRs->diagnosa,
            '#title#' => $title,
            '#kelas_pelayanan#' => $pasienRs->kelaspelayanan_nama,
            '#cara_bayar#' => $pasienRs->carabayar_nama,
            '#penjamin#' => $pasienRs->penjamin_nama,
            '#no_polisi#' => $info['no_polisi'],
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $detail,
                'dataObat' => $detailObat,
            ]),
        ];

        $print->Output();
    }

    public function actionBatalPesan($id)
    {
        $model = PesanAmbulan::updateAll([
            'status_pesan' => DocoConstants::BATAL_PESAN_AMBULAN,
            'status_ambulan' => DocoConstants::AMBULAN_IDLE
        ], "pesanambulan_id = {$id}");

        if ($model) {
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Permintaan Ambulan berhasil dibatalkan.'
            ];
        }
        return [
            'status' => 500,
            'title' => 'Proses Gagal !',
            'text' => 'Permintaan Ambulan gagal dibatalkan.'
        ];
    }

    public function actionGetDataDetail($id)
    {
        $header = InfoPesanAmbulanView::find()->where([
            'pesanambulan_id' => $id
        ])->one();

        $pasien = PasienRsAmbulanView::find()->where([
            'pendaftaran_id' => $header->pendaftaran_id
        ])->one();

        $detail = InfoPesanAmbulanDetailView::find()->where([
            'pesanambulan_id' => $id
        ])->all();
        $pelayananAmbulan = $this->actionGetPelayananAmbulan();

        return [
            'pasien' => $pasien,
            'header' => $header,
            'detail' => $detail,
            'pelayanan_ambulan' => $pelayananAmbulan
        ];
    }


    public function actionGetPegawai($pegawai_id = null)
    {
        $request = Yii::$app->request;
        $query = PegawaiMasterView::find();
        if ($pegawai_id) {
            $pegawai = $query->where([
                'pegawai_id' => $pegawai_id
            ])->asArray()->one();
            return $pegawai;
        } else {
            $term = $request->get('term');
            $query->andFilterWhere([
                'or',
                ['like', 'LOWER(nama_pegawai)', strtolower($term)],
                ['like', 'nomorindukpegawai', $term],
            ]);
            return $query->limit(10)->asArray()->all();
        }
    }

    /**
     * @controller actionExportSuratTugasPdf
     * @attribute #tableDataKaryawan# => Menampilkan data table Surat Tugas
     * @attribute #title# => title
     * @attribute #no_rekam_medik# => Menampilkan data No Rekam Medik
     * @attribute #nama_pemesan# => Menampilkan data Nama Pemesan
     * @attribute #jenis_kelamin# => Menampilkan data Jenis Kelamin
     * @attribute #tgl_pesanambulan# => Menampilkan data Tanggal Pesan
     * @attribute #tgl_pemakaiandari# => Menampilkan data Tanggal Pemakaian Dari 
     * @attribute #tgl_pemakaiansampai# => Menampilkan data Pemakaian Sampai
     * @attribute #durasi_pemakaian# => Menampilkan data Durasi Pemakaian
     * @attribute #asal_pasien# => Menampilkan data Asal Pasien
     * @attribute #keluhan# => Menampilkan data Keluhan
     * @attribute #jenis_ambulan# => Menampilkan data Jenis Ambulan
     * @attribute #pelayanan# => Menampilkan data Pelayanan
     * @attribute #km_awal# => Menampilkan data KM Awal
     * @attribute #tableObatAlkes# => Menampilkan data table Obat Alkes
     * @attribute #date_now# => Menampilkan Tanggal sekarang
     */

    public function actionExportSuratTugasPdf()
    {
        $request = Yii::$app->request;
        $pemakaianambulan_id = $request->get('pemakaianambulan_id');
        $title = 'Surat Tugas Ambulan';
        try {
            $modelPemakaiAmbulan = new InfoPemakaianAmbulanDetailView;
            $dataKaryawan = $modelPemakaiAmbulan::find()
                ->select(['nama_pegawai', 'nomorindukpegawai', 'jabatan_nama'])
                ->where(['pemakaianambulan_id' => $pemakaianambulan_id])
                ->andWhere(['NOT', ['nama_pegawai' => null]])
                ->all();

            $dataObatAlkes = $modelPemakaiAmbulan::find()
                ->select(['obatalkes_nama', 'qty'])
                ->where(['pemakaianambulan_id' => $pemakaianambulan_id])
                ->andWhere(['NOT', ['obatalkes_nama' => null]])
                ->all();

            $dataPemakaiAmbulan = InfoPemakaianAmbulanView::find()
                ->where(['pemakaianambulan_id' => $pemakaianambulan_id])->one();
            $print = new DocoPrint();
            $print->attributes = [
                '#tableDataKaryawan#' => $this->renderPartial('_data_karyawan', [
                    'dataKaryawan' => $dataKaryawan,
                ]),
                '#no_rekam_medik#' => isset($dataPemakaiAmbulan->no_rekam_medik) ? $dataPemakaiAmbulan->no_rekam_medik : '-',
                '#nama_pemesan#' => isset($dataPemakaiAmbulan->nama_pemesan) ? $dataPemakaiAmbulan->nama_pemesan : '-',
                '#jenis_kelamin#' => isset($dataPemakaiAmbulan->jns_kelamin) ? $dataPemakaiAmbulan->jns_kelamin : '-',
                '#tgl_pesanambulan#' => date('d M Y H:i:s', strtotime($dataPemakaiAmbulan->tgl_pesanambulan)),
                '#tgl_pemakaiandari#' => date('d M Y H:i:s', strtotime($dataPemakaiAmbulan->tgl_pemakaiandari)),
                '#tgl_pemakaiansampai#' => date('d M Y H:i:s', strtotime($dataPemakaiAmbulan->tgl_pemakaiansampai)),
                '#durasi_pemakaian#' => isset($dataPemakaiAmbulan->durasi_pemakaian) ? $dataPemakaiAmbulan->durasi_pemakaian : '-',
                '#asal_pasien#' => isset($dataPemakaiAmbulan->asal_pasien) ? $dataPemakaiAmbulan->asal_pasien : '-',
                '#keluhan#' => isset($dataPemakaiAmbulan->keluhan) ? $dataPemakaiAmbulan->keluhan : '-',
                '#jenis_ambulan#' => isset($dataPemakaiAmbulan->jenis_ambulan) ? $dataPemakaiAmbulan->jenis_ambulan : '-',
                '#pelayanan#' => isset($dataPemakaiAmbulan->pelayanan) ? $dataPemakaiAmbulan->pelayanan : '-',
                '#km_awal#' => isset($dataPemakaiAmbulan->km_awal) ? DocoHelpers::formatNumber($dataPemakaiAmbulan->km_awal) : '-',
                '#date_now#' => date('d F Y'),
                '#tableObatAlkes#' => $this->renderPartial('_data_obatAlkes', [
                    'dataObatAlkes' => $dataObatAlkes,
                ]),
            ];
            $print->Output();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @controller actionExportSuratTugasUmumPdf
     * @attribute #tableDataKaryawan# => Menampilkan data table Surat Tugas
     * @attribute #title# => title
     * @attribute #no_rekam_medik# => Menampilkan data No Rekam Medik
     * @attribute #nama_pemesan# => Menampilkan data Nama Pemesan
     * @attribute #jenis_kelamin# => Menampilkan data Jenis Kelamin
     * @attribute #tgl_pesanambulan# => Menampilkan data Tanggal Pesan
     * @attribute #tgl_pemakaiandari# => Menampilkan data Tanggal Pemakaian Dari 
     * @attribute #tgl_pemakaiansampai# => Menampilkan data Pemakaian Sampai
     * @attribute #durasi_pemakaian# => Menampilkan data Durasi Pemakaian
     * @attribute #asal_pasien# => Menampilkan data Asal Pasien
     * @attribute #keluhan# => Menampilkan data Keluhan
     * @attribute #jenis_ambulan# => Menampilkan data Jenis Ambulan
     * @attribute #pelayanan# => Menampilkan data Pelayanan
     * @attribute #km_awal# => Menampilkan data KM Awal
     * @attribute #tableObatAlkes# => Menampilkan data table Obat Alkes
     * @attribute #date_now# => Menampilkan Tanggal sekarang
     * @attribute #is_sadar# => Menampilkan Sadar Pasien
     * @attribute #is_nafas# => Menampilkan nafas Pasien
     * @attribute #is_nadi# => Menampilkan Nadi Pasien
     * @attribute #nama_pj# => Menampilkan Nama Penanggung Jawab
     * @attribute #kontak_pj# => Menampilkan Kontak Penanggung Jawab
     */

    public function actionExportSuratTugasUmumPdf()
    {
        $request = Yii::$app->request;
        $pemakaianambulan_id = $request->get('pemakaianambulan_id');
        $title = 'Surat Tugas Ambulan';
        try {
            $modelPemakaiAmbulan = new InfoPemakaianAmbulanDetailView;
            $dataKaryawan = $modelPemakaiAmbulan::find()
                ->select(['nama_pegawai', 'nomorindukpegawai', 'jabatan_nama'])
                ->where(['pemakaianambulan_id' => $pemakaianambulan_id])
                ->andWhere(['NOT', ['nama_pegawai' => null]])
                ->all();

            $dataObatAlkes = $modelPemakaiAmbulan::find()
                ->select(['obatalkes_nama', 'qty'])
                ->where(['pemakaianambulan_id' => $pemakaianambulan_id])
                ->andWhere(['NOT', ['obatalkes_nama' => null]])
                ->all();

            $dataPemakaiAmbulan = InfoPemakaianAmbulanView::find()
                ->where(['pemakaianambulan_id' => $pemakaianambulan_id])->one();

            $print = new DocoPrint();
            $print->attributes = [
                '#tableDataKaryawan#' => $this->renderPartial('_data_karyawan', [
                    'dataKaryawan' => $dataKaryawan,
                ]),
                '#no_rekam_medik#' => isset($dataPemakaiAmbulan->no_rekam_medik) ? $dataPemakaiAmbulan->no_rekam_medik : '-',
                '#nama_pemesan#' => isset($dataPemakaiAmbulan->nama_pemesan) ? $dataPemakaiAmbulan->nama_pemesan : '-',
                '#jenis_kelamin#' => isset($dataPemakaiAmbulan->jns_kelamin) ? $dataPemakaiAmbulan->jns_kelamin : '-',
                '#tgl_pesanambulan#' => date('d M Y H:i:s', strtotime($dataPemakaiAmbulan->tgl_pesanambulan)),
                '#tgl_pemakaiandari#' => date('d M Y H:i:s', strtotime($dataPemakaiAmbulan->tgl_pemakaiandari)),
                '#tgl_pemakaiansampai#' => date('d M Y H:i:s', strtotime($dataPemakaiAmbulan->tgl_pemakaiansampai)),
                '#durasi_pemakaian#' => isset($dataPemakaiAmbulan->durasi_pemakaian) ? $dataPemakaiAmbulan->durasi_pemakaian : '-',
                '#asal_pasien#' => isset($dataPemakaiAmbulan->asal_pasien) ? $dataPemakaiAmbulan->asal_pasien : '-',
                '#keluhan#' => isset($dataPemakaiAmbulan->keluhan) ? $dataPemakaiAmbulan->keluhan : '-',
                '#jenis_ambulan#' => isset($dataPemakaiAmbulan->jenis_ambulan) ? $dataPemakaiAmbulan->jenis_ambulan : '-',
                '#pelayanan#' => isset($dataPemakaiAmbulan->pelayanan) ? $dataPemakaiAmbulan->pelayanan : '-',
                '#km_awal#' => isset($dataPemakaiAmbulan->km_awal) ? DocoHelpers::formatNumber($dataPemakaiAmbulan->km_awal) : '-',
                '#is_sadar#' => ($dataPemakaiAmbulan->is_sadar == true) ? 'Sadar' : 'Tidak Sadar',
                '#is_nafas#' => ($dataPemakaiAmbulan->is_nafas == true) ? 'Ada' : 'Tidak Ada',
                '#is_nadi#' => ($dataPemakaiAmbulan->is_nadi == true) ? 'Ada' : 'Tidak Ada',
                '#nama_pj#' => $dataPemakaiAmbulan->nama_pj,
                '#kontak_pj#' => $dataPemakaiAmbulan->kontak_pj,
                '#date_now#' => date('d F Y'),
                '#tableObatAlkes#' => $this->renderPartial('_data_obatAlkes', [
                    'dataObatAlkes' => $dataObatAlkes,
                ]),
            ];
            $print->Output();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
