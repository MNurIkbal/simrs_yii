<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\PasienKirimUnitlain;
use app\modules\v1\models\PermintaanKepenunjangan;
use app\modules\v1\models\InfoTarifPenunjang;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Daftartindakan;
use app\modules\v1\models\RencanaOperasi;
use app\modules\v1\models\PemeriksaanLab;
use app\modules\v1\models\InfoPaketTindakanView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoRencanaOperasi;

class OrderController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoTarifPenunjang';

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

    public function actionIndex()
    {
        return [];
    }

    /**
    * @var $id <integer> pendaftaran_id
    * @return array
    * @throws \yii\db\Exception | \Exception
    **/

    public function actionCreate($id)
    {
        
        $request = Yii::$app->request;
    
        $ruangan_id = $request->post('ruangan_id');
        $instruksi_id = $request->post('instruksi_id');
        $is_paket = $request->post('is_paket');
        $instalasi_id = $request->post('instalasi_id');
        $list_order = $request->post('list_order');
       
        $id_tindakan = $request->post('id_tindakan');
        $daftartindakan_id = $tipepaket_id = null;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaran = Pendaftaran::find()->where([
                'pendaftaran_id' => $id
            ])->one();

            if (empty($pendaftaran)) {
                return [
                    'status' => 422,
                    'messages' => 'Pendaftaran tidak di temukan'
                ];
            }

            // $conUnitLain = [
            //     'pendaftaran_id' => $id,
            //     'pasienadmisi_id' => $request->post('pasienadmisi_id'),
            //     'pasienmasukpenunjang_id' => null,
            //     'ruangan_id' => $ruangan_id,
            //     'status_penunjang' => DocoConstants::BELUM_SETUJU
            // ];
            // if (!empty($instruksi_id)) {
            //     $conUnitLain['instruksi_id'] = $instruksi_id;
            // }

            // $model = PasienKirimUnitlain::find()->where($conUnitLain)->one();

            // if (empty($model)) $model = new PasienKirimUnitlain;
            $model = new PasienKirimUnitlain;
            $tgl = $request->post('tgl_kirimpasien');
            $jamMulai = $request->post('jam_mulai');
            $jamSelesai = $request->post('jam_selesai');
            $PemakaianImplant = $request->post('pemakaian_implant');
            $SewaVendor = $request->post('sewa_vendor');
            $SewaAlatRs = $request->post('sewa_alat_rs');
            $OperasiCito = $request->post('jenis_operasi_cito');
            $OperasiElektif = $request->post('jenis_operasi_elektif');
            $OperasiOdc = $request->post('jenis_operasi_odc');
            $tanggalKirim =  !empty($tgl) ? date('Y-m-d H:i:s',strtotime($request->post('tgl_kirimpasien'))) : null;
            $tanggalOperasi = date('Y-m-d',strtotime($tanggalKirim));
            $jamMulai =  !empty($jamMulai) ? $jamMulai : '00:00';
            $jamSelesai =  !empty($jamSelesai) ? $jamSelesai : '00:00';
            $dokterOperator =  $request->post('dr_operator_id');
            $dokterAnastesi =  $request->post('dr_anastesi_id');
            $statusSetuju = DocoConstants::DISETUJUI;
            $diagnosaUtama = $request->post('diagnosa_utama', null);
            $diagnosaPenyerta = $request->post('diagnosa_penyerta', null);

            /* 
            * CABUT VALIDASI UNTUK DATA JADWAL DI JAM YANG SAMA

            $dataBatal = Yii::$app->db->createCommand("
                SELECT rencanaoperasi_t.pasienkirimkeunitlain_id FROM rencanaoperasi_t 
                INNER JOIN pasienkirimkeunitlain_t 
                    ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = rencanaoperasi_t.pasienkirimkeunitlain_id
                WHERE  (rencanaoperasi_t.jam_rencana_selesai >= '{$jamMulai}' 
                AND rencanaoperasi_t.jam_rencana_mulai <= '{$jamSelesai}') 
                AND DATE(rencanaoperasi_t.tgl_permintaan) = '{$tanggalOperasi}'
                AND rencanaoperasi_t.ruangan_id = {$ruangan_id}
                AND pasienkirimkeunitlain_t.status_penunjang = '{$statusSetuju}'
            ")->queryOne();

            if (!empty($dataBatal)) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Tidak dapat mengorder bedah sentral'
                ];
            }
            */
            
            $kirimUnitLain = [
                'pegawai_id' => $request->post('pegawai_id'),
                'instalasi_id' => $request->post('instalasi_id'),
                'pasien_id' => !empty($pendaftaran->pasien_id) ? $pendaftaran->pasien_id : null,
                'pendaftaran_id' => empty($request->post('pasienadmisi_id')) ? $id : null,
                'kelaspelayanan_id' => !empty($pendaftaran->kelaspelayanan_id) ? $pendaftaran->kelaspelayanan_id : null,
                'ruangan_id' => $request->post('ruangan_id'),
                'tgl_kirimpasien' => $tanggalKirim,
                'catatan_dokterpengirim' => $request->post('catatan_dokterpengirim'),
                'instruksi_id' => $request->post('instruksi_id'),
                'pasienadmisi_id' => $request->post('pasienadmisi_id'),
                'diag_utama' => json_encode($diagnosaUtama),
                'diag_penyerta' => json_encode($diagnosaPenyerta),
                'status_penunjang' => DocoConstants::BELUM_SETUJU
            ];

            $model->attributes = $kirimUnitLain;

            if (!$model->save()) {
                $transaction->rollBack();
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }

            $idParent = $model->pasienkirimkeunitlain_id;

            $rencanaOperasi = [
                'catatan_klinis' => $request->post('catatan_dokterpengirim'),
                'pemakaian_implant' => !empty($PemakaianImplant) ? $PemakaianImplant : "-",
                'sewa_vendor' => !empty($SewaVendor) ? $SewaVendor : "-",
                'sewa_alat_rs' => !empty($SewaAlatRs) ? $SewaAlatRs : "-",
                'jenis_operasi_cyto' => !empty($OperasiCito) ? true : false,
                'jenis_operasi_elektif' => !empty($OperasiElektif) ? true : false,
                'jenis_operasi_odc' => !empty($OperasiOdc) ? true : false,
                'pasienkirimkeunitlain_id' => $idParent,
                'pendaftaran_id' => $id,
                'pasienadmisi_id' => $request->post('pasienadmisi_id'),
                'ruangan_id' => $request->post('ruangan_id'),
                'tgl_permintaan' => $tanggalKirim,
                'jam_rencana_mulai' => $jamMulai,
                'jam_rencana_selesai' => $jamSelesai,
                'dr_operator_id' => !empty($dokterOperator) ? $dokterOperator : null,
                'dr_anastesi_id' => !empty($dokterAnastesi) ? $dokterAnastesi : null,
            ];

            $modelOperasi = RencanaOperasi::find()->where([
                'pasienkirimkeunitlain_id' => $idParent
            ])->one();

            if (empty($modelOperasi)) $modelOperasi = new RencanaOperasi;

            $modelOperasi->attributes = $rencanaOperasi;
            if (!$modelOperasi->save()) {
                $transaction->rollBack();
                return [
                    'data' => $modelOperasi->errors,
                    'status' => 422
                ];
            }

            $konfigSystem = KonfigSystem::find()->one();
            $orderBedahTanpaTindakan = isset($konfigSystem['order_bedah_tanpa_tindakan']) && !empty($konfigSystem['order_bedah_tanpa_tindakan']) ? $konfigSystem['order_bedah_tanpa_tindakan'] : false;
            if(!$orderBedahTanpaTindakan){
                $list_order = json_decode($list_order,true);
                if (!is_array($list_order)) {
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Tindakan Tidak ditemukan'
                    ];
                }

                /** Kumpulkan data untuk validasi **/
                $validPaket = $validTindakan = [];
                $detail = PermintaanKepenunjangan::find()->where([
                    'pasienkirimkeunitlain_id' => $idParent
                ])->asArray()->all();
                foreach ($detail as $value) {
                    if (!empty($value['tipepaket_id'])) {
                        $validPaket[$value['tipepaket_id']] = true;
                    }

                    if (!empty($value['daftartindakan_id'])) {
                        $validTindakan[$value['daftartindakan_id']] = true;
                    }
                }

                $listPaket = $listTindakan = [];
                $condPaket = $condTindakan = [];
                $mappTindakan = [];
                foreach ($list_order as $key => $value) {
                    $id_tarif = $value['tariftindakan_id'];
                    $listTindakan[$id_tarif] = [
                        'is_cyto' => $value['is_cyto']
                    ];
                    $mappTindakan[$id_tarif][$value['golongan_id']][$value['kegiatan_id']] = true;
                    $condTindakan[] = $id_tarif;
                }
                $listInsert = [];
                /** Get data tindakan **/
                if ($condTindakan) {
                    $tarif = InfoTarifPenunjang::find()->where([
                        'tariftindakan_id' => $condTindakan,
                        'ruangan_id' => $ruangan_id,
                        'komponentarif_id' => 6
                    ])->asArray()->all();
                    foreach ($tarif as $value) {
                        if (isset($validTindakan[$value['daftartindakan_id']])) {
                            $transaction->rollBack();
                            return [
                                'status' => 422,
                                'title' => 'Proses Gagal !',
                                'text' => $value['daftartindakan_nama'] . ' sudah ada'
                            ];
                        }
                        $tarif = $value['tariftindakan_id'];
                        $golongan = $value['kelompokpemeriksaanlab_id'];
                        $kegiatan = $value['jenispemeriksaanlab_id'];

                        $isCyto = !empty($listTindakan[$value['tariftindakan_id']]['is_cyto']) ? true : false;
                        $tarifPelayanan = !empty($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0;
                        $persenCyto = $tarifPelayanan * ($value['persencyto_tindakan'] / 100);
                        if (isset($mappTindakan[$tarif][$golongan][$kegiatan])) {
                            $listInsert[] = [
                                'daftartindakan_id' => !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null,
                                'pasienkirimkeunitlain_id' => $idParent,
                                'operasi_id' => !empty($value['pemeriksaanlab_id'])
                                                ? $value['pemeriksaanlab_id'] : null,
                                'tglpermintaankepenunjang' => $tanggalKirim,
                                'qtypermintaan' => 1,
                                'tarif_pelayanan' => !empty($value['harga_tariftindakan'])
                                                ? $value['harga_tariftindakan'] : 0,
                                'is_cyto' => $isCyto,
                                'tipepaket_id' => null,
                                'tarif_cytotindakan' => $isCyto ? $persenCyto : 0,
                                'is_approve' => false,
                            ];
                        }
                    }
                }

                if ($listInsert) {
                    $modelDetail = PermintaanKepenunjangan::batchInsert($listInsert,false);
                    $transaction->commit();
                    return [
                        'messages' => 'Data berhasil di simpan',
                        'pasienkirimkeunitlain_id'=>$model->pasienkirimkeunitlain_id,
                    ];
                } else {
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Tindakan Tidak ditemukan'
                    ];
                }
            }else{
                $transaction->commit();
                return [
                    'messages' => 'Data berhasil di simpan',
                    'pasienkirimkeunitlain_id'=>$model->pasienkirimkeunitlain_id,
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        }
    }

    /**
    * @var $id <integer> permintaankepenunjang_id
    * @return array
    * @throws \yii\db\Exception | \Exception
    **/
    public function actionDelete($id)
    {
        try {
            $result = (new PermintaanKepenunjangan)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}