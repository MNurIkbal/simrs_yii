<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\PasienKirimUnitlain;
use app\modules\v1\models\PermintaanKepenunjangan;
use app\modules\v1\models\InfoTarifTindakanRadView;
use app\modules\v1\models\Daftartindakan;
use app\modules\v1\models\PemeriksaanLab;
use app\modules\v1\models\InfoPaketTindakanView;
use app\modules\v1\models\Pendaftaran;
use Doco\Notifications\RadiologiNotification;

class OrderController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoTarifTindakanRadView';

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
        $is_rujukan = $request->post('is_rujukan',null);
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
            //     'pasienmasukpenunjang_id' => null,
            //     'ruangan_id' => $ruangan_id
            // ];
            // if (!empty($instruksi_id)) {
            //     $conUnitLain['instruksi_id'] = $instruksi_id;
            // }

            // $model = PasienKirimUnitlain::find()->where($conUnitLain)->one();

            // if (empty($model)) $model = new PasienKirimUnitlain;
            $model = new PasienKirimUnitlain;
            $tgl = $request->post('tgl_kirimpasien');
            $tanggalKirim =  !empty($tgl) ? date('Y-m-d H:i:s',strtotime($request->post('tgl_kirimpasien'))) : null;

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
                'status_penunjang' => DocoConstants::BELUM_SETUJU,
                'is_rujukan' => $is_rujukan,
                'diag_utama' => !empty($request->post('diagnosa_utama')) && is_array($request->post('diagnosa_utama')) ? json_encode($request->post('diagnosa_utama')) : null,
                'diag_penyerta' => !empty($request->post('diagnosa_penyerta')) && is_array($request->post('diagnosa_penyerta')) ? json_encode($request->post('diagnosa_penyerta')) : null,
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

            $list_order = json_decode($list_order,true);
            if (!is_array($list_order) && $is_rujukan == null) {
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

            if($list_order != null){
                foreach ($list_order as $key => $value) {
                    $id_tarif = $value['tariftindakan_id'];
                    if (!empty($value['is_paket'])) {
                        $listPaket[$id_tarif] = [
                            'is_cyto' => $value['is_cyto']
                        ];
                        $condPaket[] = $id_tarif;
                    } else {
                        $listTindakan[$id_tarif] = [
                            'is_cyto' => $value['is_cyto']
                        ];
                        $condTindakan[] = $id_tarif;
                    }
                }
            }


            $paket = $tindakan = [];
            $listInsert = [];
            /** Get Data Paket **/
            if ($condPaket) {
                $tarif = InfoPaketTindakanView::find()->where([
                    'tariftindakan_id' => $condPaket,
                    'ruangan_id' => $ruangan_id
                ])->asArray()->all();
                foreach ($tarif as $value) {
                    if (isset($validPaket[$value['tipepaket_id']])) {
                        $transaction->rollBack();
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal !',
                            'text' => $value['tipepaket_nama'] . ' sudah ada'
                        ];
                    }
                    $isCyto = !empty($listPaket[$value['tariftindakan_id']]['is_cyto']) ? true : false;
                    $tarifPelayanan = !empty($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0;
                    $persenCyto = $tarifPelayanan * ($value['persencyto_tindakan'] / 100);
                    $listInsert[] = [
                        'daftartindakan_id' => null,
                        'pasienkirimkeunitlain_id' => $idParent,
                        'pemeriksaanrad_id' => !empty($value['pemeriksaanradiologi_id'])
                                        ? $value['pemeriksaanradiologi_id'] : null,
                        'tglpermintaankepenunjang' => $tanggalKirim,
                        'qtypermintaan' => 1,
                        'tarif_pelayanan' => $tarifPelayanan,
                        'is_cyto' => $isCyto,
                        'tipepaket_id' => !empty($value['tipepaket_id']) ? $value['tipepaket_id'] : null,
                        'tarif_cytotindakan' => $isCyto ? $persenCyto : 0,
                        'is_approve' => false,
                    ];
                }
            }

            /** Get data tindakan **/
            if ($condTindakan) {
                $tarif = InfoTarifTindakanRadView::find()->where([
                    'tariftindakan_id' => $condTindakan,
                    'ruangan_id' => $ruangan_id
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
                    $isCyto = !empty($listTindakan[$value['tariftindakan_id']]['is_cyto']) ? true : false;
                    $tarifPelayanan = !empty($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0;
                    $persenCyto = $tarifPelayanan * ($value['persencyto_tindakan'] / 100);
                    $listInsert[] = [
                        'daftartindakan_id' => !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null,
                        'pasienkirimkeunitlain_id' => $idParent,
                        'pemeriksaanrad_id' => !empty($value['pemeriksaanradiologi_id'])
                                        ? $value['pemeriksaanradiologi_id'] : null,
                        'tglpermintaankepenunjang' => $tanggalKirim,
                        'qtypermintaan' => 1,
                        'tarif_pelayanan' => $tarifPelayanan,
                        'is_cyto' => $isCyto,
                        'tipepaket_id' => null,
                        'tarif_cytotindakan' => $isCyto ? $persenCyto : 0,
                        'is_approve' => false,
                    ];
                }
            }
            if ($listInsert) {
                $modelDetail = PermintaanKepenunjangan::batchInsert($listInsert,false);
                $transaction->commit();
                $dataPayload = [
                    'pasienkeunitlain_id' => $idParent,
                    'pendaftaran_id' => $id,
                ];
    
                RadiologiNotification::addNotification($dataPayload, 'neworder');
                return [
                    'messages' => 'Data berhasil di simpan',
                    'pasienkirimkeunitlain_id'=>$model->pasienkirimkeunitlain_id,
                ];
            } else if ($is_rujukan == 1){
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
