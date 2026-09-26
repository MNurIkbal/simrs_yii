<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 15:52:41
 */

// Namespace
namespace app\modules\v1\controllers;

// Using yii
use Yii;
use yii\helpers\ArrayHelper;

// Using doco
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

// Using models
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\InfoResepturView;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Penomoran;
use app\modules\v1\models\PemberianObat;
use app\modules\v1\models\PemberianObatDetail;
use app\modules\v1\models\PemberianObatView;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\PemberianObatDetailView;
use app\modules\v1\models\PermintaanRetur;
use app\modules\v1\models\PermintaanReturDetail;
use app\modules\v1\models\PermintaanReturView;
use app\modules\v1\models\PermintaanReturDetailView;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\StokObatPasien;
use app\modules\v1\models\StokObatPasienView;
use app\modules\v1\models\StokObatPasienDetailView;
use app\modules\v1\models\StokReturPasienView;

// Class
class PemberianObatController extends DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\PemberianObat';

    /* Fungsi untuk membuat paretn dari verbs */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /* Fungsi untuk unset actions */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    /* Fungsi untuk mendapatkan list data yang diperlukan submodule pemberian obat */
    public function actionGetListData()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = DocoHelpers::decrypt($params['id']);
            $ruangan_id = $params['ruangan_id'];

            $listDiagnosa = $this->getListDiagnosa();
            $listEfek = $this->getListLookup('efek_obat');
            $listKeterangan = $this->getListLookup('ket_pemberian');
            $listPegawai = $this->getListPegawai('t_keperawatan', $ruangan_id);
            $listPemberianObat = $this->getListPemberianObat($pendaftaran_id);
            $diagnosaCpptDokter = $this->getDiagnosaCpptDokter($pendaftaran_id);
            $listJenisObatRiwayat = $this->getListJenisObatRiwayat($pendaftaran_id);
            $listStokObatPasien = $this->getStokObatPasien($pendaftaran_id);
            $listStokObatPasienDetail = $this->getStokObatPasienDetail($pendaftaran_id);
            $lastReseptur = $this->getLastReseptur($pendaftaran_id);

            return [
                'listDiagnosa' => $listDiagnosa,
                'listEfek' => $listEfek,
                'listKeterangan' => $listKeterangan,
                'listPegawai' => $listPegawai,
                'listPemberianObat' => $listPemberianObat,
                'diagnosaCpptDokter' => $diagnosaCpptDokter,
                'listJenisObatRiwayat' => $listJenisObatRiwayat,
                'listStokObatPasien' => $listStokObatPasien,
                'listStokObatPasienDetail' => $listStokObatPasienDetail,
                'lastReseptur' => $lastReseptur,
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

    /* Fungsi untuk mendapatkan list data riwayat pemberian obat */
    public function actionGetDataRiwayatPemberianObat()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = $params['id'];

            $data = $this->getDataRiwayatPemberianObat($pendaftaran_id);
            $listEfek = $this->getListLookup('efek_obat');
            $listKeterangan = $this->getListLookup('ket_pemberian');

            return [
                'riwayatPemberianObat' => $data,
                'listEfek' => $listEfek,
                'listKeterangan' => $listKeterangan,
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

    /* Fungsi untuk mendapatkan list data riwayat permintaan retur */
    public function actionGetDataRiwayatPermintaanRetur()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = $params['id'];

            $data = $this->getDataRiwayatPermintaanRetur($pendaftaran_id);
            $dataDetail = $this->getDataRiwayatPermintaanReturDetail($data);

            return [
                'riwayatPermintaanRetur' => $data,
                'riwayatPermintaanReturDetail' => $dataDetail,
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

    /* Fungsi untuk mendapatkan list obat */
    public function actionGetListObat()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = DocoHelpers::decrypt($params['id']);

            $data = StokObatPasienDetailView::find()->where(['pendaftaran_id' => $pendaftaran_id])->all();

            return $data;
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

    /* Fungsi untuk mendapatkan stok obat pasien */
    public function actionGetStokObatPasien()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = DocoHelpers::decrypt($params['id']);
            $obatalkes_id = $params['obatalkes_id'];
            $nomor = $params['nomor'];

            $data = StokObatPasienDetailView::find()->where(['pendaftaran_id' => $pendaftaran_id, 'obatalkes_id' => $obatalkes_id, 'nomor' => $nomor])->one();

            return $data;
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

    /* Fungsi untuk mendapatkan data retur obat */
    public function actionGetDataReturObat()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = $params['id'];

            $data = StokReturPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->all();

            return [
                'dataReturObat' => $data
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

    /* Fungsi untuk mendapatkan data detail permintaan retur */
    public function actionGetDataDetailReturObat()
    {
        try {
            $params = Yii::$app->request->get();
            $permintaanretur_id = $params['id'];

            $data = PermintaanReturDetailView::find()->where(['permintaanretur_id' => $permintaanretur_id])->all();

            return [
                'dataReturObat' => $data
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

    /* Fungsi untuk mendapatkan data retur obat ubah */
    public function actionGetDataReturObatUntukUbah()
    {
        try {
            $additionalField = [];
            $params = Yii::$app->request->get();
            $pendaftaran_id = $params['id'];
            $permintaanretur_id = $params['permintaanretur_id'];

            $stokReturPasien = StokReturPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->all();
            $permintaanRetur = PermintaanReturDetail::find()->where(['permintaanretur_id' => $permintaanretur_id])->all();

            if (!empty($stokReturPasien)) {
                foreach ($stokReturPasien as $key => $value) {
                    if (!empty($permintaanRetur)) {
                        foreach ($permintaanRetur as $index => $content) {
                            if (($value['obatalkespasien_id'] == $content['obatalkespasien_id']) && ($value['obatalkes_id'] == $content['obatalkes_id'])) {
                                $additionalField[$key]['jumlah_retur'] = $content['qty_retur'];
                                $additionalField[$key]['alasan'] = $content['alasan'];
                                $additionalField[$key]['permintaanreturdetail_id'] = $content['permintaanreturdetail_id'];
                            }
                        }
                    }
                }
            }

            return [
                'dataReturObat' => $stokReturPasien,
                'additionalField' => $additionalField,
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

    /* Fungsi untuk menyimpan pemberian obat */
    public function actionSimpanPemberianObat()
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $post = Yii::$app->request->post();
            $pemberianObat = $post['pemberianObat'];
            $pemberianObatDetail = isset($post['pemberianObatDetail']) ? $post['pemberianObatDetail'] : [];

            $model = new PemberianObat;

            if ($model->load($pemberianObat, '')) {

                if(isset($model->diagnosa_id)){
                    $valDiag= [];
                    $splitVal = explode('_', $model->diagnosa_id);
                    if(count($splitVal)>1){
                        $valDiag['id'] = $splitVal[0];
                        $splitTxt = explode('-', $splitVal[1]);
                        if (count($splitTxt)>1) {
                            $valDiag['kode'] = $splitTxt[0];
                            $valDiag['nama'] = $splitTxt[1];
                        }
                        $valDiag['text'] = $splitVal[1];
                    }else{
                        $valDiag['text'] = $model->diagnosa_id;
                    }
                    $model->diagnosa_id = ($valDiag);
                }

                if ($model->save()) {
                    if (!empty($pemberianObatDetail)) {
                        $listKeterangan = $this->getListLookup('ket_pemberian');
                        $listKeterangan = ArrayHelper::map($listKeterangan, 'lookup_id', 'lookup_name');

                        foreach ($pemberianObatDetail as $key => $value) {
                            $stokObatPasienModel = StokObatPasien::findOne($value['stokobatpasien_id']);

                            if ($stokObatPasienModel) {
                                if (isset($listKeterangan[$value['keterangan']])) {
                                    if (($value['keterangan'] == DocoConstants::VAR_KET_PO_MUNTAH) || ($value['keterangan'] == DocoConstants::VAR_KET_PO_DIKONSUMSI)) {
                                        $stokObatPasienModel->stok_dipakai = (float)$stokObatPasienModel->stok_dipakai + (float)$value['jumlah'];
                                        $stokObatPasienModel->stok_sisa = (float)$stokObatPasienModel->stok_sisa - (float)$value['jumlah'];
                                        $stokObatPasienModel->stok_retur = (float)$stokObatPasienModel->stok_sisa + (float)$stokObatPasienModel->stok_pending;
                                        $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_sisa + (float)$stokObatPasienModel->stok_pending;
                                    } else {
                                        $stokObatPasienModel->stok_pending = (float)$stokObatPasienModel->stok_pending + (float)$value['jumlah'];
                                        $stokObatPasienModel->stok_sisa = (float)$stokObatPasienModel->stok_sisa - (float)$value['jumlah'];
                                        $stokObatPasienModel->stok_retur = (float)$stokObatPasienModel->stok_sisa + (float)$stokObatPasienModel->stok_pending;
                                        $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_sisa + (float)$stokObatPasienModel->stok_pending;
                                    }

                                    $stokObatPasienModel->save();
                                }
                            }

                            $pemberianObatDetail[$key]['pemberianobat_id'] = $model->pemberianobat_id;
                            $pemberianObatDetail[$key]['is_resep'] = $pemberianObatDetail[$key]['no_res_rekon'] != 'REKONSILIASI OBAT' ? true : false;
                            $pemberianObatDetail[$key]['signa_obat'] = $pemberianObatDetail[$key]['signa_obat'] != '' ? $pemberianObatDetail[$key]['signa_obat'] : null;
                            $pemberianObatDetail[$key]['wkt_pemberian'] = date('Y-m-d h:i:s', strtotime($pemberianObatDetail[$key]['wkt_pemberian']));
                        }

                        PemberianObatDetail::batchInsert($pemberianObatDetail);
                    }
                    $transaction->commit();
                    return ['message' => 'Data Berhasil di simpan'];
                }
            }

            return ['message' => 'Data gagal disimpan'];
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

    /* Fungsi untuk menyimpan permintaan retur */
    public function actionSimpanPermintaanRetur()
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $post = Yii::$app->request->post();
            $isUbah = false;
            $permintaanRetur = $post['permintaanRetur'];
            $permintaanReturDetail = $post['permintaanReturDetail'];
            $signaObat = ArrayHelper::map(SignaObat::find()->all(), 'signa_nama', 'signa_id');

            if (isset($permintaanRetur['permintaanretur_id']) && $permintaanRetur['permintaanretur_id'] != '') {
                $model = PermintaanRetur::findOne($permintaanRetur['permintaanretur_id']);
            } else {
                $model = new PermintaanRetur;

                $penomoran = Penomoran::find()->where(['penomoran_id' => DocoConstants::VAR_PENOMORAN_PERMINTAAN_RETUR])->one();

                if ($penomoran) {
                    if ($penomoran->flag_refresh == 1) {
                        $penomoran->last_number = 1;
                        $penomoran->last_generate = $penomoran->prefix.''.str_pad($penomoran->last_number, 4, '0', STR_PAD_LEFT);
                        $penomoran->last_number = strval($penomoran->last_number);

                        if ($penomoran->save()) {
                            $model->no_permintaanretur = $penomoran->last_generate;
                        }
                    } else {
                        $penomoran->last_number = $penomoran->last_number + 1;
                        $penomoran->last_generate = $penomoran->prefix.''.str_pad($penomoran->last_number, 4, '0', STR_PAD_LEFT);
                        $penomoran->last_number = strval($penomoran->last_number);
                        
                        if ($penomoran->save()) {
                            $model->no_permintaanretur = $penomoran->last_generate;
                        }
                    }
                }
            }

            if ($model->load($permintaanRetur, '')) {
                if ($model->save()) {
                    if (!empty($permintaanReturDetail)) {
                        foreach ($permintaanReturDetail as $key => $value) {
                            $permintaanReturDetail[$key]['permintaanretur_id'] = $model->permintaanretur_id;
                            $permintaanReturDetail[$key]['signa'] = isset($signaObat[$value['signa']]) ? $signaObat[$value['signa']] : null;
                            $lastQty = 0;

                            if ($value['permintaanreturdetail_id'] != '') {
                                $isUbah = true;
                                $modelPermintaanReturDetail = PermintaanReturDetail::findOne($value['permintaanreturdetail_id']);
                                $lastQty = $modelPermintaanReturDetail->qty_retur;
                                $modelPermintaanReturDetail->load($permintaanReturDetail[$key], '');
                                $modelPermintaanReturDetail->save();
                            } else {
                                unset($value['permintaanreturdetail_id']);
                                $modelPermintaanReturDetail = new PermintaanReturDetail;
                                $modelPermintaanReturDetail->load($permintaanReturDetail[$key], '');
                                $modelPermintaanReturDetail->save();
                            }

                            $stokObatPasienModel = StokObatPasien::find()->where(['obatalkespasien_id' => $value['obatalkespasien_id']])->one();

                            if ($isUbah == true) {
                                if ($stokObatPasienModel) {
                                    $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa + (float)$lastQty;
                                    $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa - (float)$value['qty_retur'];
                                    $stokObatPasienModel->save();
                                    $isUbah = false;
                                }
                            } else {
                                if ($stokObatPasienModel) {
                                    $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa - (float)$value['qty_retur'];
                                    $stokObatPasienModel->save();
                                }
                            }
                        }

                        $transaction->commit();
                        return [
                            'message' => 'Data Berhasil di simpan'
                        ];
                    }
                }
            }

            return ['message' => 'Data gagal disimpan'];
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
    * @controller actionCetakPemberianObat
    * @attribute #table_detail# => table
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #tanggal_lahir# => tanggal_lahir
    * @attribute #umur# => umur
    * @attribute #jenis_kelamin# => jenis_kelamin
    * @attribute #ruangan_kelas# => ruangan_kelas
    * @attribute #dokter_dpjp# => dokter_dpjp
    * @attribute #penjamin_nama# => penjamin_nama
    * @attribute #berat_badan# => berat_badan
    * @attribute #tinggi_badan# => tinggi_badan
    * @attribute #luas_tubuh# => luas_tubuh
    * @attribute #status_kehamilan# => status_kehamilan
    * @attribute #diagnosa_nama# => diagnosa_nama
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #tanggal# => tanggal
    * @attribute #status_alergi# => status_alergi
    **/
    public function actionCetakPemberianObat()
    {
        $pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->get('id'));
        $pegawai_id = Yii::$app->request->get('pegawai_id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');

        $data = '';
        $dataDetail = [];
        $pemberianObat = [];
        $pemberianObatDetail = [];

        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one();
        $jenisObatAlkes = $this->getListJenisObatRiwayat($pendaftaran_id);

        $pemberianObat = (new \yii\db\Query())
        ->select([
            PemberianObatView::tableName().'.*',
        ])
        ->from(PemberianObatView::tableName())
        ->where([PemberianObatView::tableName().'.pendaftaran_id' => $pendaftaran_id])
        ->orderBy([PemberianObatView::tableName().'.pemberianobat_id' => SORT_ASC])
        ->all();

        if (!empty($pemberianObat)) {
            foreach ($pemberianObat as $key => $value) {
                $data = $value;

                $tempPemberianObatDetail = (new \yii\db\Query())
                ->select([
                    PemberianObatDetailView::tableName().'.*',
                ])
                ->from(PemberianObatDetailView::tableName())
                ->where([PemberianObatDetailView::tableName().'.pemberianobat_id' => $value['pemberianobat_id']])
                ->all();

                if (!empty($tempPemberianObatDetail)) {
                    foreach ($tempPemberianObatDetail as $value) {
                        $pemberianObatDetail[] = $value;
                    }
                }
            }
        }

        $hamil = [
            '0' => 'Tidak',
            '1' => 'Ya',
        ];
        $alergi = [
            '0' => 'Tidak',
            '1' => 'Ya',
        ];
        $efek = ArrayHelper::map($this->getListLookup('efek_obat'), 'lookup_id', 'lookup_name');
        $keterangan = ArrayHelper::map($this->getListLookup('ket_pemberian'), 'lookup_id', 'lookup_name');

        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('pdf', [
                'dataDetail' => $pemberianObatDetail,
                'jenisObatAlkes' => $jenisObatAlkes,
                'pegawai' => $pegawai,
                'efek' => $efek,
                'keterangan' => $keterangan,
            ]),
            '#nama_pasien#' => $data['nama_pasien'],
            '#no_rekam_medik#' => $data['no_rekam_medik'],
            '#tanggal_lahir#' => date('d-m-Y', strtotime($data['tanggal_lahir'])),
            '#umur#' => $data['umur'],
            '#jenis_kelamin#' => $data['jenis_kelamin'],
            '#ruangan_kelas#' =>$data['ruangan_nama'] . ' / ' . $data['kelaspelayanan_nama'],
            '#dokter_dpjp#' => $data['dokter_dpjp'],
            '#penjamin_nama#' => $data['penjamin_nama'],
            '#berat_badan#' => $data['berat_badan'],
            '#tinggi_badan#' => $data['tinggi_badan'],
            '#luas_tubuh#' => $data['luas_tubuh'],
            '#status_kehamilan#' => isset($hamil[$data['is_hamil']]) ? $hamil[$data['is_hamil']] : '',
            '#diagnosa_nama#' => $data['diagnosa_nama'],
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#tanggal#' => date('j F Y h:i:s', strtotime('NOW')),
            '#status_alergi#' => isset($alergi[$data['is_alergi']]) ? $alergi[$data['is_alergi']] : '',
        ];

        $print->Output();
    }

    /**
    * @controller actionCetakRiwayatRetur
    * @attribute #table# => table
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #tanggal# => tanggal
    **/
    public function actionCetakRiwayatRetur()
    {
        $pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->get('id'));
        $pegawai_id = Yii::$app->request->get('pegawai_id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $riwayatRetur = [];

        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one();

        $riwayatRetur = (new \yii\db\Query())
        ->select([
            PermintaanReturView::tableName().'.*',
        ])
        ->from(PermintaanReturView::tableName())
        ->where([PermintaanReturView::tableName().'.pendaftaran_id' => $pendaftaran_id])
        ->orderBy([PermintaanReturView::tableName().'.tgl_permintaanretur' => SORT_DESC])
        ->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#table#' => $this->renderPartial('pdf_riwayat', [
                'data' => $riwayatRetur,
            ]),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#tanggal#' => date('d F Y h:i:s', strtotime('NOW')),
        ];
        $print->Output();
    }

    /**
    * @controller actionCetakDetailRetur
    * @attribute #table# => table
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #kepala_ruangan# => kepala_ruangan
    * @attribute #pegawai_approve# => pegawai_approve
    * @attribute #tanggal# => tanggal
    **/
    public function actionCetakDetailRetur()
    {
        $permintaanretur_id = DocoHelpers::decrypt(Yii::$app->request->get('permintaanretur_id'));
        $pegawai_id = Yii::$app->request->get('pegawai_id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $jabatan_id = DocoConstants::VAR_JABATAN_KEPALA_RUANGAN;
        $detailRetur = [];

        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one();
        $kepalaRuangan = PegawaiView::find()->where(['jabatan_id' => $jabatan_id, 'ruangan_id' => $ruangan_id])->one();

        $permintaanRetur = (new \yii\db\Query())
        ->select([
            PermintaanReturView::tableName().'.*',
        ])
        ->from(PermintaanReturView::tableName())
        ->where([PermintaanReturView::tableName().'.permintaanretur_id' => $permintaanretur_id])
        ->one();

        $detailRetur = (new \yii\db\Query())
        ->select([
            PermintaanReturDetailView::tableName().'.*',
        ])
        ->from(PermintaanReturDetailView::tableName())
        ->where([PermintaanReturDetailView::tableName().'.permintaanretur_id' => $permintaanretur_id])
        ->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#table#' => $this->renderPartial('pdf_detail', [
                'data' => $detailRetur,
            ]),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#kepala_ruangan#' => isset($kepalaRuangan['nama_pegawai']) ? $kepalaRuangan['nama_pegawai'] : '',
            '#pegawai_approve#' => $permintaanRetur['pegawai_approve'],
            '#tanggal#' => date('d F Y h:i:s', strtotime('NOW')),
        ];
        $print->Output();
    }

    /* Fungsi untuk menghapus permintaan retur */
    public function actionHapusPermintaanRetur()
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $id = Yii::$app->request->post('id');

            if (isset($id) && $id != '') {
                $permintaanRetur = PermintaanRetur::findOne($id);

                if ($permintaanRetur) {
                    $permintaanReturDetail = PermintaanReturDetail::find()->where(['permintaanretur_id' => $id])->all();

                    if (!empty($permintaanReturDetail)) {
                        foreach ($permintaanReturDetail as $key => $value) {
                            $stokObatPasienModel = StokObatPasien::find()->where(['obatalkespasien_id' => $value['obatalkespasien_id']])->one();

                            if ($stokObatPasienModel) {
                                $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa + (float)$value['qty_retur'];
                                $stokObatPasienModel->save();
                            }
                        }
                    }

                    (new PermintaanReturDetail)->delete([
                        'permintaanretur_id' => $id
                    ]);
                }

                (new PermintaanRetur)->delete($id);
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Gagal menghapus data'
                ];
            }

            $transaction->commit();
            return [
                'message' => 'Data Berhasil di hapus'
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

    /* Fungsi untuk mendapatkan list diagnosa */
    private function getListDiagnosa()
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                DiagnosaView::tableName().'.diagnosa_id',
                DiagnosaView::tableName().'.diagnosa_nama'
            ])
            ->from(DiagnosaView::tableName());

            return $query->all();
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

    /* Fungsi untuk mendapatkan list lookup */
    private function getListLookup($lookup_type = '')
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                Lookup::tableName().'.*'
            ])
            ->from(Lookup::tableName())
            ->where([Lookup::tableName().'.is_active' => true, Lookup::tableName().'.is_deleted' => false]);

            if ($lookup_type != '') {
                $query->andWhere([Lookup::tableName().'.lookup_type' => $lookup_type]);
            }

            return $query->all();
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

    /* Fungsi untuk mendapatkan list pegawai */
    private function getListPegawai($kelompokpegawai_namalainnya = '', $ruangan_id = null)
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                PegawaiView::tableName().'.*'
            ])
            ->from(PegawaiView::tableName());

            if ($kelompokpegawai_namalainnya != '') {
                $query->where([PegawaiView::tableName().'.kelompokpegawai_namalainnya' => $kelompokpegawai_namalainnya]);
            }

            if ($ruangan_id != '') {
                $query->andWhere([PegawaiView::tableName().'.ruangan_id' => $ruangan_id]);
            }

            return $query->all();
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

    /* Fungsi untuk mendapatkan list pemberian obat */
    private function getListPemberianObat($pendaftaran_id = '')
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                PemberianObatView::tableName().'.*'
            ])
            ->from(PemberianObatView::tableName())
            ->orderBy([PemberianObatView::tableName().'.pemberianobat_id' => SORT_ASC]);

            if ($pendaftaran_id != '') {
                $query->where([PemberianObatView::tableName().'.pendaftaran_id' => $pendaftaran_id]);
            }

            return $query->all();
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

    /* Fungsi untuk mendapatkan diagnosa nama dari inputan cppt oleh dokter */
    private function getDiagnosaCpptDokter($pendaftaran_id = '')
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                Cppt::tableName().'.*'
            ])
            ->from(Cppt::tableName())
            ->orderBy([Cppt::tableName().'.cppt_id' => SORT_DESC]);

            if ($pendaftaran_id != '') {
                $query->andWhere([Cppt::tableName().'.pendaftaran_id' => $pendaftaran_id]);
            }
                $query->andWhere([Cppt::tableName().'.catatan_perawat' => null]);

            return $query->one();
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

    /* Fungsi untuk mendapatkan list jenis obat riwayat */
    private function getListJenisObatRiwayat($pendaftaran_id)
    {
        try {
            $jenisObat = [];
            $riwayat = $this->getDataRiwayatPemberianObat($pendaftaran_id);
            $tempJenisObat = ArrayHelper::map(JenisObatAlkes::find()->all(), 'jenisobatalkes_id', 'jenisobatalkes_nama');

            if (!empty($riwayat)) {
                foreach ($riwayat as $value) {
                    if ($value['jenisobat_id'] != '') {
                        if (isset($tempJenisObat[$value['jenisobat_id']]) && ($tempJenisObat[$value['jenisobat_id']] != '')) {
                            $jenisObat[$value['jenisobat_id']] = $tempJenisObat[$value['jenisobat_id']];
                        }
                    }
                }
            }

            return $jenisObat;
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

    /* Fungsi untuk mendapatkan stok obat pasien */
    private function getStokObatPasien($pendaftaran_id)
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                StokObatPasienView::tableName().'.*'
            ])
            ->from(StokObatPasienView::tableName())
            ->where([StokObatPasienView::tableName().'.pendaftaran_id' => $pendaftaran_id]);

            return $query->all();
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

    /* Fungsi untuk mendapatkan stok obat pasien detail */
    private function getStokObatPasienDetail($pendaftaran_id)
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                StokObatPasienDetailView::tableName().'.*'
            ])
            ->from(StokObatPasienDetailView::tableName())
            ->where([StokObatPasienDetailView::tableName().'.pendaftaran_id' => $pendaftaran_id]);

            return $query->all();
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

    /* Fungsi untuk mendapatkan data riwayat pemberian obat */
    private function getDataRiwayatPemberianObat($pendaftaran_id)
    {
        try {
            $pemberianObatDetail = [];

            $pemberianObat = (new \yii\db\Query())
            ->select([
                PemberianObatView::tableName().'.*',
            ])
            ->from(PemberianObatView::tableName())
            ->where([PemberianObatView::tableName().'.pendaftaran_id' => $pendaftaran_id])
            ->all();

            if (!empty($pemberianObat)) {
                foreach ($pemberianObat as $key => $value) {
                    $tempPemberianObatDetail = (new \yii\db\Query())
                    ->select([
                        PemberianObatDetailView::tableName().'.*',
                    ])
                    ->from(PemberianObatDetailView::tableName())
                    ->where([PemberianObatDetailView::tableName().'.pemberianobat_id' => $value['pemberianobat_id']])
                    ->all();

                    if (!empty($tempPemberianObatDetail)) {
                        foreach ($tempPemberianObatDetail as $value) {
                            $pemberianObatDetail[] = $value;
                        }
                    }
                }
            }

            return $pemberianObatDetail;
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

    /* Fungsi untuk mendapatkan data riwayat permintaan retur */
    private function getDataRiwayatPermintaanRetur($pendaftaran_id)
    {
        try {
            $pemberianObat = (new \yii\db\Query())
            ->select([
                PermintaanReturView::tableName().'.*',
            ])
            ->from(PermintaanReturView::tableName())
            ->where([PermintaanReturView::tableName().'.pendaftaran_id' => $pendaftaran_id])
            ->all();

            return $pemberianObat;
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

    /* Fungsi untuk mendapatkan data riwayat permintaan retur detail */
    private function getDataRiwayatPermintaanReturDetail($data)
    {
        try {
            $permintaanReturDetail = [];

            if (!empty($data)) {
                foreach ($data as $value) {
                    $model = PermintaanReturDetailView::find()->where(['permintaanretur_id' => $value['permintaanretur_id']])->all();

                    if (!empty($model)) {
                        foreach ($model as $content) {
                            $permintaanReturDetail[] = $content;
                        }
                    }
                }
            }

            return $permintaanReturDetail;
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

    /* Fungsi untuk mendapatkan reseptur terakhir */
    private function getLastReseptur($pendaftaran_id)
    {
        try {
            $query = (new \yii\db\Query())
            ->select([
                InfoResepturView::tableName().'.*'
            ])
            ->from(InfoResepturView::tableName())
            ->where([InfoResepturView::tableName().'.pendaftaran_id' => $pendaftaran_id])
            ->orderBy([InfoResepturView::tableName().'.reseptur_id' => SORT_DESC])
            ->limit(1);

            return $query->all();
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
}