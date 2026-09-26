<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PesanAmbulan;
use app\modules\v1\models\Ambulan;
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
use app\modules\v1\models\StokObatAlkesR;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class PermintaanAmbulanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PesanAmbulan';
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

    public function actionPemesanan()
    {
        $request = Yii::$app->request;
        $model = new KetersediaanAmbulanView;
        $tanggal = date('Y-m-d',strtotime($request->post('tgl_pesanambulan')));

        $query = (new \yii\db\Query())
            ->from("ab_pgetketersediaanambulanparam('{$tanggal}')");

        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['jenis_ambulan'])) {
                $jenis_ambulan = ($advancedFilter['jenis_ambulan'] == 1) ? true : false;
                $query->andWhere(['is_emergency' => $jenis_ambulan]);
            }

            if(isset($advancedFilter['no_polisi'])) {
                $query->andWhere(['ILIKE','no_polisi', $advancedFilter['no_polisi']]);
            }
        }

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
        $model = new PesanAmbulan;
        $ruangan_id = Yii::$app->jwt->ruangan_id;

        try {
            $dataInsert = [];
            $dataTindakan = $request->post('cacheTindakan',"{}");
            $dataCacheTindakan = json_decode($dataTindakan,true);

            $dataObat = $request->post('cacheObat',"{}");
            $dataCacheObat = json_decode($dataObat,true);

            $postData = $post['post']['PesanAmbulanForm'];
            $ambulan_id  = $postData['ambulan_id'];
            $model->ambulan_id = $ambulan_id;
            $model->ruangan_id = Yii::$app->jwt->ruangan_id;
            $model->status_ambulan = DocoConstants::PESAN_AMBULAN;

            if($postData['jenis'] == "luar") {
                $model->tgl_lahir = date('Y-m-d',strtotime($postData['tgl_lahir']));
                $model->umur = DocoHelpers::convertDateToAge($model->tgl_lahir);
                $model->pemesan = $postData['pemesan'];
                $model->jenis_kelamin = $postData['jenis_kelamin'];
                $model->tempat_lahir = $postData['tempat_lahir'];
                $model->asal_pasien = $postData['asal_pasien'];
                $model->keluhan = $postData['keluhan'];
                $model->is_nafas = $postData['is_nafas'];
                $model->is_sadar = $postData['is_sadar'];
                $model->is_nadi = $postData['is_nadi'];
                $model->nama_pj = $postData['nama_pj'];
                $model->kontak_pj = $postData['kontak_pj'];
                $model->tgl_pesanambulan = date('Y-m-d H:i:s',strtotime($postData['tgl_pesanambulan']));
            } else {
                $model->pasien_id = $postData['pasien_id'];
                $model->pendaftaran_id = $postData['pendaftaran_id'];
                $model->detaknadi = $postData['detaknadi'];
                $model->kesadaran = $postData['kesadaran'];
                $model->respirasi = $postData['respirasi'];
                $model->saturasi = $postData['saturasi'];
                $model->tanda_vital = $postData['tanda_vital'];
                $model->td_diastolic = $postData['td_diastolic'];
                $model->tujuan_pasien = $postData['tujuan_pasien'];
                $model->tgl_pesanambulan = date('Y-m-d H:i:s',strtotime($postData['tgl_pesanambulan_rs']));

            }

            if($model->validate() && $model->save()) {
                $idParent = $model->pesanambulan_id;
                if(!empty($dataCacheTindakan)) {
                    if(is_array($dataCacheTindakan)) {
                        if(!empty($dataCacheTindakan)) {
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
                    }
                }

                if(!empty($dataCacheObat)) {
                    if(is_array($dataCacheObat)) {
                        if(!empty($dataCacheObat)) {
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
                    }
                }
                if (!empty($dataInsert)) {
                    PesanAmbulanDetail::batchInsert($dataInsert);
                }
                $transaction->commit();
                $dataPermintaan = PesanAmbulan::find()
                    ->select([
                        'pesanambulan_t.pesanambulan_id',
                        'pesanambulan_t.no_pesanambulan',
                        'pesanambulan_t.tujuan_pasien',
                        // 'pesanambulan_t.tgl_pesanambulan',
                        'pesanambulan_t.created_date as tgl_pesanambulan',
                        'pesanambulan_t.ambulan_id',
                        'pesanambulan_t.status_pesan',
                        'ambulan_m.no_polisi'
                    ])
                    ->join('JOIN', 'ambulan_m', 'ambulan_m.ambulan_id=pesanambulan_t.ambulan_id')
                    ->where(['pesanambulan_id' => $idParent])
                    ->asArray()
                    ->one();

                $response = [
                    'text' => 'Permintaan Ambulan berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'orderRecord' => $dataPermintaan,
                    'no_pesanambulan' => $dataPermintaan['no_pesanambulan']
                ];

                return $response;
            }
            else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        }
        catch (\yii\db\Exception $e) {
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

    public function actionGetDataTarifAmbulan($daftartindakan_id = null,$pasien_id = null)
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
        if($daftartindakan_id) {
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

    public function actionGetDataObatAmbulan($obatalkes_id = null)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = ObatAlkesDetailView::find();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        if($obatalkes_id) {
            $obat = $query->where(['obatalkes_id' => $obatalkes_id])->asArray()->one();
            $stokObat = StokObatAlkesR::find()->where([
                'obatalkes_id' => $obatalkes_id,
                'ruangan_id' => $ruangan_id
            ])->asArray()->one();
            $obat['stok'] = !empty($stokObat['qty_tersedia']) ? $stokObat['qty_tersedia'] : 0;
            return $obat;
        } else {
            $term = $get['term'];
            $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($term)]);
            return $query->limit(10)->asArray()->all();
        }
    }

    public function actionGetDataObat($obatalkes_id = null)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = ObatAlkesDetailView::find();
        if($obatalkes_id) {
            $query->where(['obatalkes_id' => $obatalkes_id]);
            return $query->one();
        } else {
            $term = $get['term'];
            $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($term)]);
            return $query->limit(10)->asArray()->all();
        }
    }

    public function actionGetDataPasien()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = PasienRsAmbulanView::find();
        $term = $get['term'];
        $query->where(['like', 'LOWER(no_rekam_medik)', strtolower($term)])
        ->orWhere(['like', 'LOWER(nama_pasien)', strtolower($term)]);

        return $query->limit(10)->asArray()->all();
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

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $no_pesanambulan = $request->get('no_pesanambulan');
        $jenis = $request->get('jenis');

        $title = 'Permintaan Ambulan';
        $model = new PesanAmbulan;
        $header = $model::find()->where(['no_pesanambulan' => $no_pesanambulan])->one();
        $info = InfoPesanAmbulanView::find()->where(['pesanambulan_id' => $header->pesanambulan_id])->asArray()->one();
        $detail = InfoPesanAmbulanDetailView::find()->where(['pesanambulan_id' => $header->pesanambulan_id])->asArray()->all();
        $print = new DocoPrint();
        $print->attributes = [
            '#no_pesanambulan#' => $no_pesanambulan,
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
    * @attribute #umur# => untuk menampilkan Umur
    */

    public function actionExportPdfPasienRs()
    {
        $request = Yii::$app->request;
        $no_pesanambulan = $request->get('no_pesanambulan');
        $jenis = $request->get('jenis');

        $title = 'Permintaan Ambulan';
        $model = new PesanAmbulan;
        $header = $model::find()->where(['no_pesanambulan' => $no_pesanambulan])->one();
        $info = InfoPesanAmbulanView::find()->where(['pesanambulan_id' => $header->pesanambulan_id])->asArray()->one();
        $detail = InfoPesanAmbulanDetailView::find()
            ->where(['pesanambulan_id' => $header->pesanambulan_id])
            ->andWhere(['IS NOT', 'daftartindakan_id', null])
            ->asArray()->all();

        $detailObat = InfoPesanAmbulanDetailView::find()
            ->where(['pesanambulan_id' => $header->pesanambulan_id])
            ->andWhere(['IS NOT', 'obatalkes_id', null])
            ->asArray()->all();

        $pasienRs = PasienRsAmbulanView::find()->where(['pasien_id' => $header->pasien_id])->one();

        $print = new DocoPrint();
        $print->attributes = [
                '#no_pesanambulan#' => $no_pesanambulan,
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
                    'is_rs' => true
                ]),
            ];

        $print->Output();
    }

    public function actionGetDetailTindakan()
    {
        $request = Yii::$app->request;
        $ambulan_id = $request->get('ambulan_id');
        $pasien_id = $request->get('pasien_id');
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $tipe = $request->get('tipe');
        $model = new TarifAmbulanView;
        $query = $model::find()->where([
            'ambulan_id' => $ambulan_id,
        ]);
        if ($tipe == 2) {
            $dataPasien = PasienRsAmbulanView::find()->where(['pasien_id' => $pasien_id])->one();
            $kelasPelayanan = $dataPasien->kelaspelayanan_id;
            $penjamin = $dataPasien->penjamin_id;
            $tindakan = $query->andWhere([
                'komponentarif_id' => 6
            ])->asArray()->all();
            $tmpTindakan = [];
            foreach ($tindakan as $value) {
                $kelas = $value['kelaspelayanan_id'];
                $jamin = $value['penjamin_id'];
                if (!isset($tmpTindakan[$value['daftartindakan_id']])) {
                    $tmpTindakan[$value['daftartindakan_id']] = $value;
                    $tmpTindakan[$value['daftartindakan_id']]['harga_tariftindakan'] = 0;
                }

                if ($kelas == $kelasPelayanan && $penjamin == $jamin) {
                    $tmpTindakan[$value['daftartindakan_id']]['harga_tariftindakan'] = $value['harga_tariftindakan'];
                }

            }
            $dataObat = ObatAmbulanView::find()->where([
                'ambulan_id' => $ambulan_id
            ])->asArray()->all();
            $obatAlkesId = $tmpObat = [];
            foreach ($dataObat as $value) {
                $row = $value;
                $row['stok'] = 0;
                $dataObat[] = $value['obatalkes_id'];
                $tmpObat[$value['obatalkes_id']] = $row;
            }

            $stokObat = StokObatAlkesR::find()->where([
                'obatalkes_id' => $dataObat,
                'ruangan_id' => $ruangan_id
            ])->all();

            foreach ($stokObat as $value) {
                if (isset($tmpObat[$value['obatalkes_id']])) {
                    $tmpObat[$value['obatalkes_id']]['stok'] = $value['qty_tersedia'];
                }
            }
            return [
                'tindakan' => $tmpTindakan,
                'obat' => $tmpObat
            ];
        } else {

            $query->andWhere([
                'komponentarif_id' => 6,
                'kelaspelayanan_id' => DocoConstants::KELAS_3,
                'penjamin_id' => DocoConstants::NEW_PENJAMIN_UMUM
            ]);
            $resTindakan =  $query->asArray()->all();

            $dataObat = ObatAmbulanView::find()->where([
                'ambulan_id' => $ambulan_id
            ])->asArray()->all();
            $obatAlkesId = $tmpObat = [];
            foreach ($dataObat as $value) {
                $row = $value;
                $row['stok'] = 0;
                $dataObat[] = $value['obatalkes_id'];
                $tmpObat[$value['obatalkes_id']] = $row;
            }

            $stokObat = StokObatAlkesR::find()->where([
                'obatalkes_id' => $dataObat,
                'ruangan_id' => $ruangan_id
            ])->all();

            return [
                'tindakan' => $resTindakan,
                'obat' => $tmpObat
            ];
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
                'query' => $query,
        ]);
    }

    public function actionGetDetailObat()
    {
        $request = Yii::$app->request;
        $ambulan_id = $request->get('ambulan_id');
        $model = new ObatAmbulanView;
        $query = $model::find()->where([
            'ambulan_id' => $ambulan_id,
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return new ActiveDataProvider([
                'query' => $query,
        ]);
    }

    public function actionDeleteTindakanLuar($ambulan_id, $daftartindakan_id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = AmbulanDetail::find()
            ->where(['ambulan_id' => $ambulan_id, 'daftartindakan_id' => $daftartindakan_id])
            ->one();

            $model->is_deleted = true;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Tarif Berhasil',
                ];
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['text'] = "Gagal Menghapus Data";
                $result['text'] = $model->getErrors();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }
}