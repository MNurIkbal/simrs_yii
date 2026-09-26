<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\cache\Cache;
use app\modules\v1\models\InfoPemakaianAmbulanView;
use app\modules\v1\models\InfoPemakaianAmbulanDetailView;
use app\modules\v1\models\InfoPemakaianObatAlkesDetailView;
use app\modules\v1\models\PemakaianAmbulan;
use app\modules\v1\models\PesanAmbulan;
use app\modules\v1\models\Ambulan;
use app\modules\v1\models\ObatAlkesPasien;

use app\modules\v1\models\ObatAlkesDetailView;
use app\modules\v1\models\TarifAmbulanView;
use app\modules\v1\models\PasienRsAmbulanView;
use app\modules\v1\models\StokObatAlkesR;

use app\modules\v1\models\PesanAmbulanDetail;
use Doco\Services\KasirService;


class InformasiPemakaianAmbulanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemakaianAmbulanView';
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
        $request = Yii::$app->request;
        $model = new InfoPemakaianAmbulanView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $advancedFilters = $request->get('advanced-filter', []);

        if (isset($advancedFilters)) {
            if (isset($advancedFilters['tgl_pesanambulan'])) {
                $explode = explode(" - ", $advancedFilters['tgl_pesanambulan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($advancedFilters['tgl_pesanambulan']);
            }

            if (isset($advancedFilters['status_ambulan'])) {
                $query->andFilterWhere(['status_ambulan' => $advancedFilters['status_ambulan']]);
            }

            if (isset($advancedFilters['jenis_ambulan'])) {
                $query->andFilterWhere(['jenis_ambulan' => $advancedFilters['jenis_ambulan']]);
                unset($advancedFilters['jenis_ambulan']);
            }
        }

        $query->andWhere(['between', 'tgl_pesanambulan', $start, $end]);
        $query->andWhere([
            'status_pesan' => DocoConstants::AMBULAN_SUDAH_DIPROSES
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['created_date' => SORT_DESC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetStatusAmbulan()
    {
        try {

            $statusAmbulan = Cache::getLookUpByKey('status_ambulan');

            return [
                'status_ambulan' => $statusAmbulan
            ];
        } catch (Exception $e) {
            $result_merge = [];
        }

        return [
            'status_ambulan' => $result_merge
        ];
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
        $jenis = $request->get('jenis');
        $title = 'Surat Tugas Ambulan Pasien Rumah Sakit';
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
     * @controller actionExportSuratTugasPdfPasienLuar
     * @attribute #tableDataKaryawan# => Menampilkan data table Surat Tugas
     * @attribute #title# => title
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
     * @attribute #date_now# => Menampilkan Tanggal sekarang
     */

    public function actionExportSuratTugasPdfPasienLuar()
    {
        $request = Yii::$app->request;
        $pemakaianambulan_id = $request->get('pemakaianambulan_id');
        $jenis = $request->get('jenis');
        $title = 'Surat Tugas Ambulan Pasien Luar';
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

    public function actionPemakaianAmbulan($id)
    {
        $model = InfoPemakaianAmbulanView::find()->where([
            'pemakaianambulan_id' => $id
        ])->asArray()->one();
        return $model;
    }

    public function actionSimpanPemakaianAmbulan($id)
    {
        $request = Yii::$app->request;
        $model = PemakaianAmbulan::find()->where([
            'pemakaianambulan_id' => $id
        ])->one();

        if (empty($model)) {
            return [
                'status' => 422,
                'text' => 'Data Pemakaian ambulan tidak ditemukan',
                'title' => 'Proses Gagal!'
            ];
        }

        $header   = $request->post('header');
        $tindakan = $request->post('tindakan');
        $obat     = $request->post('obat');

        $model->tgl_realisasikembali = date('Y-m-d H:i:s', strtotime($header['tgl_kembali']));
        $date_1 = date_create($model->tgl_pemakaiandari);
        $date_2 = date_create($model->tgl_realisasikembali);
        $diff = date_diff($date_1, $date_2);
        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24);
        $lamaPemakaian = $hours . ' Jam ' . $diff->i . ' Menit ' . $diff->s . ' Detik';
        $model->lama_pemakaian = $lamaPemakaian;
        $model->km_akhir = $header['km_akhir'];
        $model->estimasi_jarak = $model->km_akhir - $model->km_awal;
        $model->total_biaya = isset($header['total_biaya']) ? $header['total_biaya'] : 0;
        $model->biaya_pemakaian = $header['biaya_pemakaian'];
        $model->biaya_tambahan = $model->total_biaya - $model->biaya_pemakaian;

        // $model->biaya_tambahan = $header['biaya_tambahan'];
        // $model->total_biaya = $model->biaya_tambahan + $model->biaya_pemakaian;

        try {
            if ($model->save()) {
                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();
                $modelPesan = PesanAmbulan::find()->where([
                    'pemakaianambulan_id' => $id
                ])->one();

                if (empty($modelPesan)) {
                    return [
                        'status' => 422,
                        'text' => 'Ambulan tidak ditemukan',
                        'title' => 'Proses Gagal!'
                    ];
                }

                if (!empty($obat) && is_array($obat)) {
                    foreach ($obat as $key => $value) {
                        if (empty($value['stok'])) continue;
                        $dataObat[] = [
                            'pesanambulan_id' => $modelPesan->pesanambulan_id,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'qty_obat' => $value['qty'],
                        ];
                    }
                }
                if (!empty($dataObat)) {
                    PesanAmbulanDetail::batchInsert($dataObat);
                }

                if (!empty($tindakan) && is_array($tindakan)) {
                    foreach ($tindakan as $key => $value) {
                        $dataTindakan[] = [
                            'pesanambulan_id' => $modelPesan->pesanambulan_id,
                            'daftartindakan_id' => $value['daftartindakan_id'],
                            'qty_tindakan' => $value['qty'],
                            'tarif_satuan' => $value['harga'],
                            'jumlah_tarif' => $value['sub_total'],
                        ];
                    }
                }
                if (!empty($dataTindakan)) {
                    PesanAmbulanDetail::batchInsert($dataTindakan);
                }

                $modelPesan->status_ambulan = DocoConstants::AMBULAN_IDLE;
                $modelPesan->save();
                $transaction->commit();
                $integrasiTindakan = $this->integrasiTagihanTindakan($modelPesan->pesanambulan_id);
                $integrasiObat = $this->integrasiTagihanObat($modelPesan->pesanambulan_id);
                return [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Proses pengembalian ambulan berhasil tersimpan.',
                    'statusIntegrasiObat' => $integrasiObat,
                    'statusIntegrasiTindakan' => $integrasiTindakan,
                ];
            }

            $response = [
                'status' => 422,
                'data' => $model->errors
            ];
        } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());
            $transaction->rollBack();
            $response = [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $response = [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        }
        \Yii::error([
            'res' => $response
        ]);
        return $response;
    }

    public function actionBatalPesan($id)
    {
        $model = PesanAmbulan::updateAll([
            'status_pesan' => DocoConstants::BELUM_PROSES,
            'pemakaianambulan_id' => null
        ], "pemakaianambulan_id = {$id}");

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
    /*
    public function actionGetDataDetail($id)
    {
        $header = InfoPesanAmbulanView::find()->where([
            'pesanambulan_id' => $id
        ])->one();

        $detail = InfoPesanAmbulanDetailView::find()->where([
            'pesanambulan_id' => $id
        ])->all();

        return [
            'header' => $header,
            'detail' => $detail
        ];
    }
    */
    public function actionGetObatan()
    {
        $term  = Yii::$app->request->get('term', null);
        $page  = Yii::$app->request->get('page', 1);
        $ambulan_id = Yii::$app->request->get('ambulan_id', null);
        $ambulan_id = DocoHelpers::decrypt($ambulan_id);
        $ruangan_id = Yii::$app->jwt->ruangan_id;

        $query = InfoPemakaianAmbulanView::find()->where(['pemakaianambulan_id' => $ambulan_id])->one();
        if ($query['kelaspelayanan_id'] == null) {
            $konfig_ambulan = "SELECT default_kelasambulan FROM konfigsystem_k";
            $konfig = Yii::$app->db->createCommand($konfig_ambulan)->queryOne();
            $kelaspelayanan_id = $konfig['default_kelasambulan'];
        } else {
            $kelaspelayanan_id = $query['kelaspelayanan_id'];
        }

        if ($query['penjamin_id'] == null) {
            $penjamin_id = DocoConstants::DEFAULT_PENJAMIN_UMUM_AMBULAN;
        } else {
            $penjamin_id = $query['penjamin_id'];
        }

        $sql   = "SELECT obatalkes_id, obatalkes_nama, satuankecil_nama, hargaygdipakai, qty_stok from infostokobatalkes_fn(" . $kelaspelayanan_id . "," . $penjamin_id . ") WHERE ruangan_id = " . $ruangan_id . "";
        if (!empty($term)) {
            $sql .= " AND WHERE obatalkes_nama ILIKE '%" . $term . "%'";
        }
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $sql .= " ORDER BY obatalkes_nama ASC LIMIT " . ($limit + 1) . " OFFSET " . (($page - 1) * $limit) . "";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionGetTindakan()
    {
        $term  = Yii::$app->request->get('term', null);
        $page  = Yii::$app->request->get('page', 1);
        $pemakaianambulan_id = Yii::$app->request->get('ambulan_id', null);
        $pemakaianambulan_id = DocoHelpers::decrypt($pemakaianambulan_id);

        $query = InfoPemakaianAmbulanView::find()->where(['pemakaianambulan_id' => $pemakaianambulan_id])->one();
        if ($query['kelaspelayanan_id'] == null) {
            $konfig_ambulan = "SELECT default_kelasambulan FROM konfigsystem_k";
            $konfig = Yii::$app->db->createCommand($konfig_ambulan)->queryOne();
            $kelaspelayanan_id = $konfig['default_kelasambulan'];
        } else {
            $kelaspelayanan_id = $query['kelaspelayanan_id'];
        }

        if ($query['penjamin_id'] == null) {
            $penjamin_id = DocoConstants::DEFAULT_PENJAMIN_UMUM_AMBULAN;
        } else {
            $penjamin_id = $query['penjamin_id'];
        }

        $ambulan_id = $query['ambulan_id'];
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $limit      = DocoConstants::LIMIT_INFINITY_SCROLL;
        $offset     = ($page - 1) * $limit;

        $query = "SELECT 
                    daftartindakan_id,
                    daftartindakan_nama,
                    harga_tariftindakan 
                FROM 
                    tariftotalrs_fn(".$ruangan_id.", ".$penjamin_id.", ".$kelaspelayanan_id.", 'ambulan')
                WHERE ambulan_id = ".$ambulan_id." AND daftartindakan_nama LIKE '%".$term."%'
                LIMIT (".$limit."+1) OFFSET ".$offset."
                ";
        
        return Yii::$app->db->createCommand($query)->queryAll();
    }

    public function actionGetFillter()
    {
        // $obatAlkes = ObatAlkesDetailView::find()->select(['blood_type_id', 'name'])->andWhere(['is_active' => '1'])->asArray()->all();
        // $tarifAmbulan = TarifAmbulanView::find()->select(['medical_device_id', 'name'])->asArray()->all();
        $obatAlkes    = ObatAlkesDetailView::find()->asArray()->all();
        // $tarifAmbulan = TarifAmbulanView::find()->asArray()->all();
        return [
            'obatAlkes'    => $obatAlkes,
            // 'tarifAmbulan' => $tarifAmbulan,
        ];
    }

    public function actionGetTarifJarak()
    {
        $ambulan_id          = Yii::$app->request->get('ambulan_id', null);
        $kelaspelayanan_id   = Yii::$app->request->get('kelaspelayanan_id', null);
        $penjamin_id         = Yii::$app->request->get('penjamin_id', null);
        $pemakaianambulan_id = Yii::$app->request->get('pemakaianambulan_id', null);

        $query = InfoPemakaianAmbulanView::find()->where(['pemakaianambulan_id' => $pemakaianambulan_id])->one();
        if ($kelaspelayanan_id == null) {
            $konfig_ambulan = "SELECT default_kelasambulan FROM konfigsystem_k";
            $konfig = Yii::$app->db->createCommand($konfig_ambulan)->queryOne();
            $kelaspelayanan_id = $konfig['default_kelasambulan'];
        }
        if ($penjamin_id == null) {
            $penjamin_id = DocoConstants::DEFAULT_PENJAMIN_UMUM_AMBULAN;
        }

        if ($ambulan_id == null || $kelaspelayanan_id == null || $penjamin_id == null) {
            return [
                'jarakDekat'  => 0,
                'jarakSedang' => 0,
                'jarakJauh'   => 0,
            ];
        }

        $dekat  = DocoConstants::TARIF_DEKAT;
        $sedang = DocoConstants::TARIF_SEDANG;
        $jauh   = DocoConstants::TARIF_JAUH;

        $tarifAmbulan = TarifAmbulanView::find()
            ->select(['daftartindakan_kode', 'daftartindakan_nama', 'harga_tariftindakan'])
            ->where(['IN', 'daftartindakan_kode', [$dekat, $sedang, $jauh]])
            ->andWhere([
                'ambulan_id'        => $ambulan_id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'penjamin_id'       => $penjamin_id
            ])
            ->asArray()
            ->all();
        foreach ($tarifAmbulan as $value) {
            $tmpTarif[$value['daftartindakan_kode']] = $value;
        }
        return [
            'jarakDekat'  => isset($tmpTarif[$dekat]['harga_tariftindakan']) ? $tmpTarif[$dekat]['harga_tariftindakan'] : 0,
            'jarakSedang' => isset($tmpTarif[$sedang]['harga_tariftindakan']) ? $tmpTarif[$sedang]['harga_tariftindakan'] : 0,
            'jarakJauh'   => isset($tmpTarif[$jauh]['harga_tariftindakan']) ? $tmpTarif[$jauh]['harga_tariftindakan'] : 0,
        ];
    }

    public function actionGetDataObat($obatalkes_id = null)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = ObatAlkesDetailView::find();
        if ($obatalkes_id) {
            $query->where(['obatalkes_id' => $obatalkes_id]);
            return $query->one();
        } else {
            $term = $get['term'];
            $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($term)]);
            return $query->limit(10)->asArray()->all();
        }
    }

    public function actionGetDataObatAmbulan($obatalkes_id = null, $id = null)
    {
        $id    = DocoHelpers::decrypt($id);
        $query = InfoPemakaianAmbulanView::find()->where(['pemakaianambulan_id' => $id])->one();
        if ($query['kelaspelayanan_id'] == null) {
            $konfig_ambulan = "SELECT default_kelasambulan FROM konfigsystem_k";
            $konfig = Yii::$app->db->createCommand($konfig_ambulan)->queryOne();
            $kelaspelayanan_id = $konfig['default_kelasambulan'];
        } else {
            $kelaspelayanan_id = $query['kelaspelayanan_id'];
        }

        if ($query['penjamin_id'] == null) {
            $penjamin_id = DocoConstants::DEFAULT_PENJAMIN_UMUM_AMBULAN;
        } else {
            $penjamin_id = $query['penjamin_id'];
        }
        $sql   = "SELECT * from infostokobatalkes_fn(" . $kelaspelayanan_id . "," . $penjamin_id . ")";
        $sql .= " WHERE obatalkes_id = " . $obatalkes_id . "";

        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    public function actionGetDataTindakanAmbulan($daftartindakan_id = null, $id = null)
    {
        $id    = DocoHelpers::decrypt($id);
        $query = InfoPemakaianAmbulanView::find()->where(['pemakaianambulan_id' => $id])->one();
        if ($query['kelaspelayanan_id'] == null) {
            $konfig_ambulan = "SELECT default_kelasambulan FROM konfigsystem_k";
            $konfig = Yii::$app->db->createCommand($konfig_ambulan)->queryOne();
            $kelaspelayanan_id = $konfig['default_kelasambulan'];
        } else {
            $kelaspelayanan_id = $query['kelaspelayanan_id'];
        }
        if ($query['penjamin_id'] == null) {
            $penjamin_id = DocoConstants::DEFAULT_PENJAMIN_UMUM_AMBULAN;
        } else {
            $penjamin_id = $query['penjamin_id'];
        }

        $ambulan_id = $query['ambulan_id'];
        $ruangan_id = Yii::$app->jwt->ruangan_id;

        $query = "SELECT 
                    daftartindakan_id,
                    daftartindakan_nama,
                    harga_tariftindakan 
                FROM 
                    tariftotalrs_fn(".$ruangan_id.", ".$penjamin_id.", ".$kelaspelayanan_id.", 'ambulan')
                WHERE ambulan_id = ".$ambulan_id." AND daftartindakan_id = ".$daftartindakan_id."
                ";
        
        return Yii::$app->db->createCommand($query)->queryOne();
       
    }

    public function actionGetDataTarifAmbulan($daftartindakan_id = null, $pasien_id = null)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ambulan_id = $request->get("ambulan_id", false);
        // $query = InfoTarifRs::find()
        $query = TarifAmbulanView::find();
        $query->andWhere([
            'komponentarif_id' => 6
        ]);
        if ($daftartindakan_id) {
            if ($pasien_id) {
                $dataPasien = PasienRsAmbulanView::find()->where(['pasien_id' => $pasien_id])->one();
                $kelasPelayanan = $dataPasien->kelaspelayanan_id;
                $penjamin = $dataPasien->penjamin_id;
                $tindakan = $query->andWhere([
                    'daftartindakan_id' => $daftartindakan_id
                ])->asArray()->all();
                $tmpTindakan = [];
                $response = [];
                foreach ($tindakan as $value) {
                    if (!isset($tmpTindakan[$value['daftartindakan_id']])) {
                        $tmpTindakan[$value['daftartindakan_id']] = $value;
                        $tmpTindakan[$value['daftartindakan_id']]['harga_tariftindakan'] = 0;
                    }

                    if ($value['kelaspelayanan_id'] == $kelasPelayanan && $value['penjamin_id'] == $penjamin) {
                        $tmpTindakan[$value['daftartindakan_id']]['harga_tariftindakan'] = $value['harga_tariftindakan'];
                    }
                    $response = $tmpTindakan[$value['daftartindakan_id']];
                }
                return $response;
            }

            $query->andWhere([
                'kelaspelayanan_id' => DocoConstants::KELAS_3,
                'penjamin_id' => DocoConstants::NEW_PENJAMIN_UMUM,
                'daftartindakan_id' => $daftartindakan_id
            ]);
            return $query->asArray()->one();
        } else {
            if ($ambulan_id != false) {
                $query->andWhere([
                    'ambulan_id' => $ambulan_id,
                ]);
            }
            $term = $get['term'];
            $query->andWhere([
                'like', 'LOWER(daftartindakan_nama)', strtolower($term)
            ])->andWhere([
                'kelaspelayanan_id' => DocoConstants::KELAS_3,
                'penjamin_id' => DocoConstants::NEW_PENJAMIN_UMUM,
            ]);
            // return $query->createCommand()->getRawSql();
            return $query->limit(10)->asArray()->all();
        }
    }
    /**
     * handle integrasi tindakan ke billing kasir
     * 
     * @param Integer pesanambulanId
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function integrasiTagihanTindakan($pesanambulanId)
    {
        $getListTindakan = PesanAmbulanDetail::find()
            ->select([
                'pesanambulandetail_t.pesanambulan_id',
                'pesanambulandetail_t.daftartindakan_id',
                'pesanambulandetail_t.tarif_satuan',
                'pesanambulandetail_t.qty_tindakan',
                'pesanambulan_t.ruangan_id',
                'pesanambulan_t.pemakaianambulan_id',
                'pesanambulan_t.pendaftaran_id',
                'pendaftaran_t.penjamin_id',
                'pendaftaran_t.carabayar_id',
                'pendaftaran_t.kelaspelayanan_id',
                'pendaftaran_t.pasien_id',
                'pendaftaran_t.no_pendaftaran'
            ])
            ->rightJoin('pesanambulan_t', 'pesanambulan_t.pesanambulan_id = pesanambulandetail_t.pesanambulan_id')
            ->rightJoin('pendaftaran_t', 'pesanambulan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
            ->where(['pesanambulan_t.pesanambulan_id' => $pesanambulanId])
            ->andWhere(['IS NOT', 'pesanambulandetail_t.daftartindakan_id', NULL])
            ->asArray()
            ->all();
        $header = [
            'no_pendaftaran' => null,
            'penjamin_id' => null,
            'kelaspelayanan_id' => null,
        ];
        $detailIntegration = [];
        foreach ($getListTindakan as $item) {
            if (empty($header['no_pendaftaran'])) {
                $header['no_pendaftaran'] = $item['no_pendaftaran'];
            }
            if (empty($header['penjamin_id'])) {
                $header['penjamin_id'] = $item['penjamin_id'];
            }
            if (empty($header['kelaspelayanan_id'])) {
                $header['kelaspelayanan_id'] = $item['kelaspelayanan_id'];
            }
            $detailIntegration[] = [
                'dokter_id' => null,
                'perawat_id' => null,
                'tipepaket_id' => null,
                'daftartindakan_id' => $item['daftartindakan_id'],
                'is_cyto' => null,
                'qty' => $item['qty_tindakan'],
                'is_penyulit' => null,
                'harga' => (int) $item['tarif_satuan']
            ];
        }
        if ($header['kelaspelayanan_id'] == null) {
            $konfig_ambulan = "SELECT default_kelasambulan FROM konfigsystem_k";
            $konfig = Yii::$app->db->createCommand($konfig_ambulan)->queryOne();
            $header['kelaspelayanan_id'] = $konfig['default_kelasambulan'];
        } 

        if ($header['penjamin_id'] == null) {
            $header['penjamin_id'] = DocoConstants::DEFAULT_PENJAMIN_UMUM_AMBULAN;
        } 

        return (new KasirService)->tagihanAmbulan([
            'no_pendaftaran' => $header['no_pendaftaran'],
            'penjamin_id' => $header['penjamin_id'],
            'kelas_pelayanan_id' => $header['kelaspelayanan_id']
        ], $detailIntegration);
    }
    /**
     * handle integration for medicine billings
     * 
     * @param Integer pesanambulanId
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function integrasiTagihanObat($pesanambulanId)
    {
        $state = false;
        $arrTagihanObat = [];
        $getListObat = PesanAmbulanDetail::find()
            ->select([
                'pesanambulandetail_t.pesanambulan_id',
                'pesanambulandetail_t.obatalkes_id',
                'pesanambulandetail_t.qty_obat as qty_oa',
                'pesanambulan_t.ruangan_id',
                'pesanambulan_t.pemakaianambulan_id',
                'pesanambulan_t.pendaftaran_id',
                'pendaftaran_t.penjamin_id',
                'pendaftaran_t.carabayar_id',
                'pendaftaran_t.kelaspelayanan_id',
                'pendaftaran_t.pasien_id',
            ])
            ->rightJoin('pesanambulan_t', 'pesanambulan_t.pesanambulan_id = pesanambulandetail_t.pesanambulan_id')
            ->rightJoin('pendaftaran_t', 'pesanambulan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
            ->where(['pesanambulan_t.pesanambulan_id' => $pesanambulanId])
            ->andWhere(['IS NOT', 'pesanambulandetail_t.obatalkes_id', NULL])
            ->asArray()->all();
        if (empty($getListObat)) {
            return true;
        }
        $listObat = [];
        $stringInObatCondition = '(';
        $params = [
            ':penjamin_id' => null,
            ':ruangan_id' => null,
            ':kelaspelayanan_id' => null,
        ];
        foreach ($getListObat as $obat) {
            if (!in_array($obat['obatalkes_id'], $listObat)) {
                $stringInObatCondition .= $obat['obatalkes_id'] . ',';
            }
            if (is_null($params[':penjamin_id'])) {
                $params[':penjamin_id'] = $obat['penjamin_id'];
            }
            if (is_null($params[':ruangan_id'])) {
                $params[':ruangan_id'] = $obat['ruangan_id'];
            }
            if (is_null($params[':kelaspelayanan_id'])) {
                $params[':kelaspelayanan_id'] = $obat['kelaspelayanan_id'];
            }
            $arrTagihanObat[] = [
                "daftartindakan_id" => null,
                "tindakanpelayanan_id" => null,
                "pasienmasukpenunjang_id" => null,
                "pasienadmisi_id" => null,
                "ruangan_id" => $obat['ruangan_id'],
                "carabayar_id" =>  $obat['carabayar_id'],
                "pegawai_id" => Yii::$app->user->identity->pegawai_id,
                "pendaftaran_id" => $obat['pendaftaran_id'],
                "obatalkes_id" => $obat['obatalkes_id'],
                "pasien_id" => $obat['pasien_id'],
                "penjamin_id" => $obat['penjamin_id'],
                "kelaspelayanan_id" => $obat['kelaspelayanan_id'],
                "tglpelayanan" => date('Y-m-d H:i:s'),
                "qty_oa" => $obat['qty_oa'],
                "hargasatuan_oa" => null,
                "harganetto_oa" => null,
                "hargajual_oa" => null,
                "satuankecil_id" => null,
                'pemakaianambulan_id' => $obat['pemakaianambulan_id']
            ];
        }

        if ($params[':kelaspelayanan_id'] == null) {
            $konfig_ambulan = "SELECT default_kelasambulan FROM konfigsystem_k";
            $konfig = Yii::$app->db->createCommand($konfig_ambulan)->queryOne();
            $params[':kelaspelayanan_id'] = $konfig['default_kelasambulan'];
        } 

        if ($params[':penjamin_id'] == null) {
            $params[':penjamin_id'] = DocoConstants::DEFAULT_PENJAMIN_UMUM_AMBULAN;
        }

        $stringInObatCondition = rtrim($stringInObatCondition, ',');
        $stringInObatCondition .= ')';
        $getObatTarif = Yii::$app->db->createCommand('select obatalkes_id, satuankecil_id, hargaygdipakai, harganetto_ygdipakai, jml_hargajual from infostokobatalkes_fn(:penjamin_id, :kelaspelayanan_id) where ruangan_id = :ruangan_id and obatalkes_id IN ' . $stringInObatCondition);
        $getObatTarif->bindValues($params);
        $obatTarif = [];
        foreach ($getObatTarif->queryAll() as $itemObat) {
            $obatTarif[$itemObat['obatalkes_id']] = $itemObat;
        }
        if (empty($obatTarif)) {
            return true;
        }
        foreach ($arrTagihanObat as $index => $item) {
            $arrTagihanObat[$index]['satuankecil_id'] = (int) $obatTarif[$item['obatalkes_id']]['satuankecil_id'];
            $arrTagihanObat[$index]['hargasatuan_oa'] = (float) $obatTarif[$item['obatalkes_id']]['hargaygdipakai'];
            $arrTagihanObat[$index]['harganetto_oa'] = (float) $obatTarif[$item['obatalkes_id']]['harganetto_ygdipakai'];
            $arrTagihanObat[$index]['hargajual_oa'] = (float) $obatTarif[$item['obatalkes_id']]['jml_hargajual'] * $item['qty_oa'];
        }
        $model = new ObatAlkesPasien;
        $statusIntegrasi = ObatAlkesPasien::batchInsert($arrTagihanObat);
        return $statusIntegrasi ? true : false;
    }
}
