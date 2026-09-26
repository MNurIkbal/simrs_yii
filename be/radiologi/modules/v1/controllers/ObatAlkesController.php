<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoAkunting;
use app\modules\v1\models\InfoPasienRadDetailView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\InfoObatAlkesPasien;
use app\modules\v1\models\InfoPasienRadView;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\SyncTindakan;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\ObatAlkesFn;
use SirsCore\features\FeatureTindakanBmhp;
use SirsCore\features\IntegrasiAkunting;
use app\modules\v1\models\PemeriksaanPasienRadiologiView;
use app\modules\v1\models\InfoPasienPenunjangView;
use app\modules\v1\cache\Cache;
use Doco\models\InfoStokObatAlkesFn;
use Doco\Traits\TindakanPenunjangTrait;
class ObatAlkesController extends DocoActiveController
{
    use TindakanPenunjangTrait;
    public $modelClass = 'app\modules\v1\models\InfoObatAlkesPasien';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST"];
        $verbs["delete"] = ["DELETE"];
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

    public function actionIndex($id)
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoObatAlkesPasien;
            $query = $model::find();
            $query->where([
                'pasienmasukpenunjang_id' => $id
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

    public function actionGetAttributes($id)
    {
        $ruangan_id = Yii::$app->jwt->ruangan_id;

        $listPemeriksaan = InfoPasienRadDetailView::find()->where([
            'pasienmasukpenunjang_id' => $id
        ])->asArray()->all();

        $listInfo = InfoPasienPenunjangView::find()->select([
            'pasienmasukpenunjang_id',
            'pendaftaran_id',
            'tgl_pendaftaran',
            'nama_pasien',
            'alamat_pasien',
            'no_masukpenunjang',
            'kelaspelayanan_id',
            'penjamin_id',
        ])->andWhere([
            'pasienmasukpenunjang_id' => $id
        ])->asArray()->one();

        $listOpt = [];
        $status = null;
        foreach ($listPemeriksaan as $value) {
            $listOpt[] = [
                'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                'daftartindakan_nama' => !empty($value['tipepaket_nama']) 
                            ? $value['tipepaket_nama'] : $value['daftartindakan_nama']
            ];
            $status = $value['status_periksa'];
        }

        $listPegawai = Cache::getListPegawaiRuangan($ruangan_id);

        return [
            'list_pemeriksaan' => $listOpt,
            'list_pegawai' => $listPegawai,
            'status_periksa' => $status,
            'listInfo' => $listInfo
        ];
    }

    public function actionCreate($id, $pelayananId)
    {
        $request = Yii::$app->request;
        $idTindakan = $request->post('tindakan_id');
        $info = PemeriksaanPasienRadiologiView::find()->andWhere([
            'pasienmasukpenunjang_id' => $id,
            'tindakanpelayanan_id' => !empty($idTindakan) ? $idTindakan : $pelayananId
        ])->asArray()->one();

        if (!empty($info)) {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $model = new ObatAlkesPasien;

                $idObatAlkes = $request->post('obatalkes_id');
                $tagihkan = $request->post('is_tagihkan');
                $ruangan_id = Yii::$app->jwt->ruangan_id;
                $_POST['ruangan_id'] = $info['ruangan_id'];
                $kelasPelayananId = isset($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : 0;
                $penjaminId = isset($info['penjamin_id']) ? $info['penjamin_id'] : 0;

                $infoObat = (new InfoStokObatAlkesFn(['extParam'=>[$penjaminId,$kelasPelayananId]]))->find()
                ->select([
                        'instalasi_id',
                        'obatalkes_id',
                        'obatalkes_kode',
                        'obatalkes_namalain',
                        'obatalkes_nama',
                        'qty_tersedia',
                        'ppn',
                        'hargaygdipakai as hargajual',
                        'satuankecil_id',
                        'satuankecil_nama',
                        'satuansedang_id',
                        'satuansedang_nama',
                        'satuanbesar_id',
                        'satuanbesar_nama',
                        'harganetto_ygdipakai as harganetto',
                        'ruangan_id',
                        'instalasi_id',
                        'hargaygdipakai',
                        'hn_diskon',
                        'hn_ppn',
                        'hn_margin',
                        'disc',
                        'ppn',
                        'margin',
                        'group_jenisobat',
                        'group_jenisobat_nama',
                        'jenisobatalkes_id',
                        'jenisobatalkes_nama'])
                ->andWhere([
                    'ruangan_id' => $ruangan_id,
                    'obatalkes_id' => $idObatAlkes
                ])->asArray()->one();

                // return $infoObat;
                // $infoObat = InfoStokObatAlkesView::find()->where([
                //     'ruangan_id' => $info['ruangan_id'],
                //     'obatalkes_id' => $idObatAlkes
                // ])->asArray()->one();

                if (empty($infoObat)) {
                    return [
                        'title' => 'Proses Gagal',
                        'text' => 'Obat tidak ditemukan',
                        'status' => 422
                    ];
                }

                $infoTindakan = new TindakanPelayananT;
                if (!empty($idTindakan)) {
                    $infoTindakan = TindakanPelayananT::find()->where([
                        'tindakanpelayanan_id' => $idTindakan
                    ])->asArray()->one();
                }

                $pendafatran_id = isset($info['pendaftaran_id']) ? $info['pendaftaran_id'] : null;
                $model->obatalkes_id = $idObatAlkes;
                $model->pasienmasukpenunjang_id = $id;
                $model->stok_obat = isset($infoObat['qty_tersedia']) ? $infoObat['qty_tersedia'] : 0;
                $model->perawat1_id = $request->post('petugas_satu');
                $model->perawat2_id = $request->post('petugas_dua');
                $model->tipepaket_id = isset($infoTindakan['tipepaket_id']) ? $infoTindakan['tipepaket_id'] : null;
                $model->ruangan_id = $ruangan_id;
                $model->carabayar_id = isset($info['carabayar_id']) ? $info['carabayar_id'] : null;
                $model->pegawai_id = isset($info['pegawai_id']) ? $info['pegawai_id'] : null;
                $model->daftartindakan_id = isset($infoTindakan['daftartindakan_id']) 
                                                ? $infoTindakan['daftartindakan_id'] : null;
                $model->tindakanpelayanan_id = $idTindakan;
                $model->satuankecil_id = isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null;
                $model->pendaftaran_id = $pendafatran_id;
                $model->pasien_id = isset($info['pasien_id']) ? $info['pasien_id'] : null;
                $model->penjamin_id = isset($info['penjamin_id']) ? $info['penjamin_id'] : null;
                $model->kelaspelayanan_id = isset($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : null;
                $model->pasienadmisi_id = isset($info['pasienadmisi_id']) ? $info['pasienadmisi_id'] : null;
                $model->tglpelayanan = date('Y-m-d H:i:s');
                $model->qty_oa = $request->post('qty');
                $model->hargasatuan_oa = isset($infoObat['hargaygdipakai']) && $tagihkan ? $infoObat['hargaygdipakai'] : 0;
                $model->harganetto_oa = isset($infoObat['harganetto']) ? $infoObat['harganetto'] : 0;
                $model->hargajual_oa = $tagihkan ? $model->qty_oa * $model->hargasatuan_oa : 0;

                if ($model->validate() && $model->save()) {
                    $oaId = $model->obatalkespasien_id;
                    $detailTrans[] = [
                        'obatalkes_id' => $idObatAlkes,
                        'qty_satuanpakai' => $request->post('qty'),
                        'satuankecil_id' => isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null,
                        'obatalkespasien_id' => $oaId,
                    ];

                    $listObatAlkes[] = $idObatAlkes;
                    $data = [
                        'detail_trans' => $detailTrans,
                        'list_obat' => $listObatAlkes,
                        'header' => [
                            'pendaftaran_id' => $pendafatran_id,
                            'kelaspelayanan_id' => $kelasPelayananId,
                            'penjamin_id' => $penjaminId,
                        ]
                    ];
                    FeatureTindakanBmhp::tindakanBmhp($data,false);
                    $transaction->commit();
                    $pendaftaran = Pendaftaran::find()->where([
                        'pendaftaran_id' => $pendafatran_id
                    ])->asArray()->one();
                    $instalasi = Yii::$app->jwt->instalasi_id;
                    $return_integrate = IntegrasiAkunting::integrateTindakanBmhp($pendaftaran['no_pendaftaran'],$instalasi);
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }

                return [
                    'messages' => 'Data berhasil di simpan'
                ];
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                return ['messages' => $e->getMessage(),'status' => 500];
            } catch (\Exception $e) {
                $transaction->rollBack();
                return ['messages' => $e->getMessage(),'status' => 500];
            }
        }
        return [
            'messages' => 'Pasien Tidak Di temukan',
            'status' => 422
        ];
    }


    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $getData = ObatAlkesPasien::find()->where([
                'obatalkespasien_id' => $id
            ])->one();
            if (!empty($getData)) {
                $model = (new ObatAlkesPasien)->delete([
                    'obatalkespasien_id' => $id
                ]);
                // $alkes = (new StokObatAlkes)->delete([
                //     'obatalkespasien_id' => $getData->obatalkespasien_id
                // ]);

                $alkes = StokObatAlkes::find()->where(['obatalkespasien_id' => $getData->obatalkespasien_id])->one();
                if (!empty($alkes)) {
                    $arrData[] = [
                        'ruangan_id' => $alkes->ruangan_id,
                        'obatalkespasien_id' => $getData->obatalkespasien_id,
                        'obatalkes_id' => $alkes->obatalkes_id,
                        'tglkadaluarsa' => $alkes->tglkadaluarsa,
                         'nobatch' => $alkes->nobatch,
                         'tglstok_in' => date('Y-m-d H:i:s'),
                         'qtystok_in' => $alkes->qtystok_out,
                         'qtystok_out' => 0,
                         'harganetto' => $alkes->harganetto,
                         'persendiscount' => $alkes->persendiscount,
                         'jmldiscount' => $alkes->jmldiscount,
                         'persenppn' => $alkes->persenppn,
                         'jmlppn' => $alkes->jmlppn,
                         'persenmargin' => $alkes->persenmargin,
                         'jmlmargin' => $alkes->jmlmargin,
                         'stokoa_aktif' => $alkes->stokoa_aktif,
                         'stokobatalkesasal_id' => $alkes->stokobatalkesasal_id,
                         'satuankecil_id' => $alkes->satuankecil_id,
                         'persenpph' => $alkes->persenpph,
                    ];

                    StokObatAlkes::batchInsert($arrData);
                }
                $transaction->commit();
                $instalasi = Yii::$app->jwt->instalasi_id;
                $oaId = $id;
                $return_integrate = IntegrasiAkunting::integrateRevertBmhp($oaId,$instalasi,'DELETE'); // With 3 Params
                return [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil dihapus.',
                ];
            }
            return [
                'title' => 'Proses Gagal!',
                'text' => 'Obat gagal dihapus.',
                'status' => 422,
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 422];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 422];
        }
    }

    public function integrateBmhp($pendaftaran_id)
    {
        try{
            $id = $pendaftaran_id;
            $resep = SyncPengeluaranobat::find()->where(['id' => $id, 'jenis' => 'BMHP', 'instalasi_id' => 5, 'is_jurnal' => 'f'])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if($config->is_akunting != null) {
                if(!empty($resep)){
                    foreach($resep as $key => $value){
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->jenisobatalkes_kode,
                            'xtransaction_at' => $value->tgl_transaksi,
                            'xamount' => $value->harga,
                            'xdiscount_amount' => $value->discount,
                            'xamount_netto' => $value->harga_netto,
                            'xamount_ppn' => $value->jmlppn,
                            'xmedical_number' => $value->no_rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => $value->is_ditagihkan,
                        ];
                        $obatalkes = ObatAlkesPasien::findOne($value->id);
                        $obatalkes->is_jurnal = true;
                        $obatalkes->scenario = "jurnal";
                        $obatalkes->update();
                        // var_dump($obatalkes->getErrors(),100,true);
                        // die;
                    }
                    $var = DocoAkunting::api('POST', 'integrations', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateBmhpTrx($id, $method)
    {
        try{
            // $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $resep = SyncPengeluaranobat::find()->where(['id' => $id])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if($config->is_akunting != null) {
                if(!empty($resep)){
                    foreach($resep as $key => $value){
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->jenisobatalkes_kode,
                            'xtransaction_at' => $value->tgl_transaksi,
                            'xamount' => $value->harga,
                            'xdiscount_amount' => $value->discount,
                            'xamount_netto' => $value->harga_netto,
                            'xamount_ppn' => $value->jmlppn,
                            'xmedical_number' => $value->no_rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => $value->is_ditagihkan,
                            'xcrud' => $method
                        ];
                        // $obatalkes = ObatAlkesPasien::findOne($value->id);
                        // $obatalkes->is_jurnal = true;
                        // $obatalkes->scenario = "jurnal";
                        // $obatalkes->update();
                        // var_dump($obatalkes->getErrors(),100,true);
                        // die;
                    }
                    $var = DocoAkunting::api('POST', 'integrations_crud', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

}