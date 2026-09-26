<?php

/**
* @author yaya
*/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\TarifTindakanView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\ObatAlkesView;

use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\ObatAlkesPasien;

use app\modules\v1\businessLogic\TagihanPasien;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\cache\Cache;

class TransaksiPelayananPasienController extends DocoActiveController
{

    public $modelClass = InfoDataPendaftaran::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["save"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {

    }

    public function actionGetNoPendaftaran()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        $start = $request->get('start');
        $end = $request->get('end');
        $statusLunas = DocoConstants::BELUM_LUNAS;

        $start = !empty($start) ? date('Y-m-d',strtotime($start)) : date('Y-m-d');
        $end = !empty($end) ? date('Y-m-d',strtotime($end)) : date('Y-m-d');

        $model = InfoDataPendaftaran::find()->select([
            'pendaftaran_id',
            'no_pendaftaran',
            'no_rekam_medik',
            'nama_pasien'
        ]);

        $model->andWhere(['BETWEEN', 'tgl_pendaftaran', $start . ' 00:00:00', $end . ' 23:59:59'])
              ->andFilterWhere(['OR',
                ['ILIKE','no_pendaftaran',$term],
                ['ILIKE','no_rekam_medik',$term],
                ['ILIKE','nama_pasien',$term],
            ])->andWhere([
                'status_bayar' => $statusLunas
            ])
            ->groupBy([
            'pendaftaran_id',
            'no_pendaftaran',
            'no_rekam_medik',
            'nama_pasien'
            ]);
        return $model->asArray()->limit(10)->all();
    }

    public function actionGetDataPendaftaran($id)
    {
        try {

            $infoPasien = InfoDataPendaftaran::find()->where([
                'pendaftaran_id' => $id
            ])->asArray()->one();

            $tagihan = InfoTagihanPasien::find()->where([
                'pendaftaran_id' => $id
            ])
            ->all();

            $config_sistem = Cache::getKonfigSistem();

            return [
                'info' => $infoPasien,
                'tagihan' => $tagihan,
                'konfig_sistem' => $config_sistem,
            ];

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        /** Must be json data **/
        $addTindakan = $request->post('detail_tagihan','{}');
        $addTindakan = json_decode($addTindakan,true);
        $pasienAdmisi = $request->post('pasienadmisi_id');
        if (is_array($addTindakan)) {
            $obat = $tindakan = [];
            $listOa = [];
            foreach ($addTindakan as $key => $value) {
                if (!is_array($value)) continue;
                foreach ($value as $keyItem => $item) {
                    if ($key == 'tindakan' || $key == 'paket') {
                        $row = $item;
                        $row['pasienadmisi_id'] = $pasienAdmisi;
                        $komponen = $row['additional_data'];
                        $row['additional_data'] = json_encode([
                            'list_komponen' => $komponen
                        ]);
                        $tindakan[] = $row;
                    } else if ($key == 'obat') {
                        $row = $item;
                        $row['pasienadmisi_id'] = $pasienAdmisi;
                        $listOa[] = $row['obatalkes_id'];
                        $obat[] = $row;
                    }
                }
            }
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                /** Ini Untuk obat Alkes pasien **/
                if (!empty($obat)) {

                    $totalTrans = count($obat);
                    $listhargaOa = ObatAlkesView::find()->where([
                        'obatalkes_id' => $listOa
                    ])->all();
                    $mappingHarga = [];

                    foreach ($listhargaOa as $value) {
                        $mappingHarga[$value->obatalkes_id] = [
                            'harganetto' => $value->harganetto_ygdipakai,
                            'persendiscount' => $value->disc,
                            'jmldiscount' => $value->hn_diskon,
                            'persenppn' => $value->ppn,
                            'jmlppn' => $value->hn_ppn,
                            'persenmargin' => $value->margin,
                            'jmlmargin' => $value->hn_margin,
                        ];
                    }

                    ObatAlkesPasien::batchInsert($obat,true);

                    $getLastObat = $connection->createCommand("
                        SELECT obatalkespasien_id,qty_oa,obatalkes_id,satuankecil_id,ruangan_id
                        FROM obatalkespasien_t
                        WHERE pendaftaran_id = {$id} ORDER BY obatalkespasien_id DESC LIMIT {$totalTrans}
                    ")->queryAll();

                    $stokObat = [];
                    foreach ($getLastObat as $value) {
                        $idObat = $value['obatalkes_id'];
                        $ruanganId = $value['ruangan_id'];
                        $stokObat[$ruanganId][] = [
                            'qty_satuanpakai' => $value['qty_oa'],
                            'obatalkes_id' => $idObat,
                            'obatalkespasien_id' => $value['obatalkespasien_id'],
                            'satuankecil_id' => $value['satuankecil_id'],
                            'harganetto' => isset($mappingHarga[$idObat]['harganetto'])
                                ? $mappingHarga[$idObat]['harganetto'] : 0,
                            'persendiscount' => isset($mappingHarga[$idObat]['persendiscount'])
                                ? $mappingHarga[$idObat]['persendiscount'] : 0,
                            'jmldiscount' => isset($mappingHarga[$idObat]['jmldiscount'])
                                ? $mappingHarga[$idObat]['jmldiscount'] : 0,
                            'persenppn' => isset($mappingHarga[$idObat]['persenppn'])
                                ? $mappingHarga[$idObat]['persenppn'] : 0,
                            'persenpph' => isset($mappingHarga[$idObat]['persenpph'])
                                ? $mappingHarga[$idObat]['persenpph'] : 0,
                            'persenmargin' => isset($mappingHarga[$idObat]['persenmargin'])
                                ? $mappingHarga[$idObat]['persenmargin'] : 0,
                            'jmlmargin' => isset($mappingHarga[$idObat]['jmlmargin'])
                                ? $mappingHarga[$idObat]['jmlmargin'] : 0,
                            'jmlppn' => isset($mappingHarga[$idObat]['jmlppn'])
                                ? $mappingHarga[$idObat]['jmlppn'] : 0,
                        ];
                    }
                    $tanggalBerlaku = date('Y-m-d');
                    $konfig = $connection->createCommand("
                        SELECT metodeantrian FROM konfigfarmasi_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND konfigfarmasi_aktif = true
                        AND is_active = true
                    ")->queryOne();

                    LogicStokObatAlkes::$distribusi = false;

                    $currentMetode = LogicStokObatAlkes::FEFO;
                    if ($konfig) {
                        $currentMetode = isset($konfig['metodeantrian'])
                                            ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                    }

                    foreach ($stokObat as $ruangan => $detailTrans) {
                        $_POST['ruangan_id'] = $ruangan;
                        if ($currentMetode === LogicStokObatAlkes::FEFO) {
                           $methode = LogicStokObatAlkes::methodeFEFO($detailTrans,$tanggalBerlaku);
                        } else {
                           $methode = LogicStokObatAlkes::methodeFIFO($detailTrans,$tanggalBerlaku);
                        }
                    }
                }

                /** Ini Untuk Tindakan pasien **/
                if (!empty($tindakan)) {
                    TindakanPelayanan::batchInsert($tindakan,true);
                }
                $transaction->commit();

                $tagihan = InfoTagihanPasien::find()->where([
                    'pendaftaran_id' => $id
                ])->asArray()->all();

                $_cache = [
                    'tindakan' => [],
                    'obat' => []
                ];
                $_condition = [];
                foreach ($tagihan as $value) {
                    $is_paket = ($value['kelompoktindakan_nama'] == 'kelompok_paket')
                                    ? true : false;
                    $row = json_encode([
                        'penjamin_pelayanan_id' => $value['penjamin_pelayanan_id'],
                        'carabayar_pelayanan_id' => $value['carabayar_pelayanan_id'],
                        'tindakan_obat_id' => $value['tindakan_obat_id'],
                        'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                        'kelompoktindakan_id' => $value['kelompoktindakan_id'],
                        'pasien_id' => $value['pasien_id'],
                        'pendaftaran_id' => $value['pendaftaran_id'],
                        'dokterpenanggungjawab_id' => $value['dokterpenanggungjawab_id'],
                        'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                        'qty' => $value['qty'],
                        'ruangan_id' => $value['ruangan_id'],
                        'instalasi_id' => $value['instalasi_id'],
                        'sub_total' => $value['sub_total'],
                        'tarif_satuan' => !empty($value['tarif_satuan'])
                                ? $value['tarif_satuan'] : 0,
                        'tarif_cyto' => !empty($value['tarif_cyto'])
                                ? $value['tarif_cyto'] : 0,
                        'pelayanan_id' => $value['pelayanan_id'],
                        'group_jaminan' => $value['groupcarabayar_id'],
                        'is_paket' => $is_paket
                    ]);

                    if (!empty($value['is_obat'])) {
                        $_cache['obat'][$value['pelayanan_id']] = $row;
                    } else {
                        $_cache['tindakan'][$value['pelayanan_id']] = $row;
                        $_condition[] = json_encode([
                            'is_paket' => $is_paket,
                            'penjamin_id' => $value['penjamin_pelayanan_id'],
                            'tindakan' => $value['tindakan_obat_id'],
                            'id_parent' => $value['pelayanan_id'],
                            'is_cyto' => !empty($value['tarif_cyto']) ? true : false,
                        ]);
                    }

                }

                if (empty($tagihan)) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Tidak ada data yang bisa ditransaksikan'
                    ];
                }

                $_POST['condition'] = $_condition;
                $_POST['detail_tagihan'] = $_cache;
                return TagihanPasien::execute();
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                return ['messages' => $e->getMessage(),'status' => 500];
            } catch (\Exception $e) {
                $transaction->rollBack();
                return ['messages' => $e->getMessage(),'status' => 500];
            }
        }
        return [
            'status' => 500,
            'message' => 'Data tidak dapat diproses'
        ];
    }

    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');

        $model = PegawaiView::find()->select([
            'pegawai_id',
            'nama'
        ])->groupBy([
            'pegawai_id',
            'nama'
        ]);
        $model->andWhere([
           'kelompokpegawai_id' => 1
        ]);
        $model->andFilterWhere(['AND',
            ['ILIKE', 'nama', $term]
        ]);
        return $model->limit(10)->all();
    }

    public function actionGetAttributes($id)
    {
        $instalasi = Instalasi::find()->where([
            'is_active' => true,
            'is_pelayanan' => true
        ])->all();

        $infoPasien = InfoDataPendaftaran::find()->select([
            'tgl_pendaftaran'
        ])->where([
            'pendaftaran_id' => $id
        ])->asArray()->one();

        $kelasPelayanan = KelasPelayanan::find()->where([
            'is_active' => true
        ])->all();

        return [
            'instalasi' => $instalasi,
            'kelas_pelayanan' => $kelasPelayanan,
            'info_pasien' => $infoPasien
        ];
    }

    public function actionGetTindakan()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        $id = DocoHelpers::decrypt($request->get('id'));
        $jenis = $request->get('jenis_pelayanan');
        $kelas = $request->get('kelas_pelayanan');
        $ruangan = $request->get('ruangan_id');
        $pendaftaran = InfoDataPendaftaran::find()->where([
            'pendaftaran_id' => $id
        ])->one();

        if (empty($pendaftaran)) {
            return [
                'status' => 422,
                'text' => 'Pendaftaran tidak ditemukan'
            ];
        }

        $penjamin = $pendaftaran->penjamin_id;

        switch ($jenis) {
            case 'tindakan':
                $listTarif = TarifTindakanView::find()->select([
                    'nama_tindakan_paket',
                    'tindakan_paket_id'
                ])->where([
                    'jenis_tindakan_paket' => 'TINDAKAN',
                    'komponentarif_id' => 6,
                    'kelaspelayanan_id' => $kelas,
                    'penjamin_id' => $penjamin,
                    'ruangan_id' => $ruangan,
                ]);
                $listTarif->andFilterWhere(['AND',
                    ['ILIKE','nama_tindakan_paket',$term]
                ])->groupBy([
                    'nama_tindakan_paket',
                    'tindakan_paket_id'
                ]);
                break;
            case 'paket':
                $listTarif = TarifTindakanView::find()->select([
                    'nama_tindakan_paket',
                    'tindakan_paket_id'
                ])->where([
                    'jenis_tindakan_paket' => 'PAKET',
                    'komponentarif_id' => 6,
                    'kelaspelayanan_id' => $kelas,
                    'penjamin_id' => $penjamin,
                    'ruangan_id' => $ruangan,
                ]);
                $listTarif->andFilterWhere(['AND',
                    ['ILIKE','nama_tindakan_paket',$term]
                ])->groupBy([
                    'nama_tindakan_paket',
                    'tindakan_paket_id'
                ]);
                break;
            /** default sebagai obat **/
            default:
                $listTarif = InfoStokObatAlkesView::find()->select([
                    'tindakan_paket_id' => 'obatalkes_id',
                    'nama_tindakan_paket' => "obatalkes_nama"
                ])->where([
                    'ruangan_id' => $ruangan
                ]);

                $listTarif->andFilterWhere(['AND',
                    ['ILIKE','obatalkes_nama',$term]
                ]);
                break;
        }

        return $listTarif->limit(10)->asArray()->all();
    }

    public function actionValidasiTindakan($id)
    {
        $request = Yii::$app->request;
                /** Post dari FE **/
        $tindakan = $request->post('tindakan_pelayanan_id');
        $jenis = $request->post('jenis_pelayanan');
        $dokter_pj = $request->post('dokter_pj');
        $kelas = $request->post('kelas_pelayanan');
        $instalasi_id = $request->post('instalasi_id');
        $ruangan = $request->post('ruangan_id');
        $tanggal_tindakan = $request->post('tanggal_tindakan');
        $qty = $request->post('qty',0);
        $is_cyto = $request->post('is_cyto',false);
        $jwt = Yii::$app->jwt->user;
        $data_insert = [];
        $pendaftaran = InfoDataPendaftaran::find()->where([
            'pendaftaran_id' => $id
        ])->one();

        if (empty($pendaftaran)) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Pendaftaran tidak ditemukan'
            ];
        }

        $penjamin = $pendaftaran->penjamin_id;
        $carabayar = $pendaftaran->carabayar_id;
        $carabayarNama = $pendaftaran->carabayar_nama;
        $penjaminNama = $pendaftaran->penjamin_nama;
        $kasusPenyakit = $pendaftaran->jeniskasuspenyakit_id;

        /** Kebutuhan untuk manampilkan di datatabels **/
        $data = [
            'carabayar_pelayanan' => $carabayarNama,
            'penjamin_pelayanan' => $penjaminNama,
            'penjamin_pelayanan_id' => $penjamin,
            'carabayar_pelayanan_id' => $carabayar,
            'tindakan_obat_id' => $tindakan,
            'tgl_pelayanan' => date('d M Y',strtotime($tanggal_tindakan)),
            'is_obat' => false,
            'is_paket' => false,
            'tarif_satuan_label' => null,
            'qty_label' => DocoHelpers::formatNumber($qty),
            'tarif_cyto_label' => 0,
            'sub_total_label' => 0,
            'tarif_satuan' => 0,
            'qty' => $qty,
            'tarif_cyto' => 0,
            'sub_total' => 0,
        ];

        switch ($jenis) {
            case ($jenis == 'tindakan' || $jenis == 'paket'):
                $is_paket = ($jenis == 'paket' ? true : false);
                $listTarif = TarifTindakanView::find()->where([
                    'jenis_tindakan_paket' => strtoupper($jenis),
                    'kelaspelayanan_id' => $kelas,
                    'penjamin_id' => $penjamin,
                    'ruangan_id' => $ruangan,
                    'tindakan_paket_id' => $tindakan
                ])->orderBy([
                    'tariftindakan_id' => SORT_ASC
                ])->all();

                if (empty($listTarif)) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Tindakan / Paket tidak ditemukan'
                    ];
                }
                $data['is_paket'] = $is_paket ? true : false;
                /** Templete Untuk Tindakan **/
                $tmpTindakan = [
                    'kelaspelayanan_id' => $pendaftaran->kelaspelayanan_id,
                    'pasien_id' => $pendaftaran->pasien_id,
                    'instalasi_id' => $instalasi_id,
                    'daftartindakan_id' => $is_paket ? null : $tindakan,
                    'tipepaket_id' => $is_paket ? $tindakan : null,
                    'carabayar_id' => $carabayar,
                    'pendaftaran_id' => $id,
                    'jeniskasuspenyakit_id' => $kasusPenyakit,
                    'ruangan_id' => $ruangan,
                    'penjamin_id' => $penjamin,
                    'tgl_tindakan' => date('Y-m-d',strtotime($tanggal_tindakan)) . ' ' . date('H:i:s'),
                    'tarif_satuan' => 0,
                    'tarif_tindakan' => 0,
                    'tarifcyto_tindakan' => 0,
                    'qty_tindakan' => $qty,
                    'cyto_tindakan' => $is_cyto ? true : false,
                    'dokterpenanggungjawab_id' => $dokter_pj,
                    'additional_data' => []
                ];

                $listKomponen = $eliminate = [];
                foreach ($listTarif as $value) {
                    $tarifTindakan = $value->tindakan_paket_id;
                    $komponenTarif = $value->komponentarif_id;
                    $persenCyto = $value->persencyto_tindakan;
                    $harga = $value->harga_tariftindakan;
                    $totalharga = $harga * $qty;
                    $tarifCyto = $is_cyto ? $totalharga * ($persenCyto/100) : 0;

                    if ($komponenTarif == 6) {
                        $tmpTindakan['tarif_satuan'] = $harga;
                        $tmpTindakan['tarif_tindakan'] = $totalharga + $tarifCyto;
                        $tmpTindakan['tarifcyto_tindakan'] = $tarifCyto;
                        $data['tarif_satuan'] = $harga;
                        $data['tarif_cyto'] = $tmpTindakan['tarifcyto_tindakan'];
                        $data['sub_total'] = $tmpTindakan['tarif_tindakan'];
                        $data['tarif_satuan_label'] = DocoHelpers::formatNumber($data['tarif_satuan']);
                        $data['tarif_cyto_label'] = DocoHelpers::formatNumber($tmpTindakan['tarifcyto_tindakan']);
                        $data['sub_total_label'] = DocoHelpers::formatNumber($tmpTindakan['tarif_tindakan']);
                        continue;
                    }

                    if (!isset($eliminate[$tarifTindakan][$komponenTarif])) {
                        $listKomponen[] = [
                            'komponentarif_id' => $komponenTarif,
                            'tindakanpelayanan_id' => null,
                            'tarif_kompsatuan' => $totalharga,
                            'tarif_tindakankomp' => $totalharga + $tarifCyto,
                            'tarifcyto_tindakankomp' => $tarifCyto,
                            'subsidiasuransikomp' => 0,
                            'subsidipemerintahkomp' => 0,
                            'subsidirumahsakitkomp' => 0,
                            'iurbiayakomp' => 0,
                        ];
                        $eliminate[$tarifTindakan][$komponenTarif] = true;
                    }
                }
                $tmpTindakan['additional_data'] = $listKomponen;
                $data_insert = $tmpTindakan;
                break;
            /** default sebagai obat **/
            default:
                $listTarif = InfoStokObatAlkesView::find()->where([
                    'ruangan_id' => $ruangan,
                    'obatalkes_id' => $tindakan
                ])->one();
                $hargaObat = ObatAlkesView::find()->select([
                    'hargaygdipakai',
                    'harganetto_ygdipakai'
                ])->where([
                    'obatalkes_id' => $tindakan
                ])->one();

                if (empty($listTarif) || empty($hargaObat)) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Obat tidak ditemukan'
                    ];
                }

                $qtyTersedia = $listTarif->qty_tersedia;
                $hargapakai = $hargaObat->hargaygdipakai;
                $hargaNetto = $hargaObat->harganetto_ygdipakai;

                if ($qty > $qtyTersedia) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Stok Obat tidak mencukupi'
                    ];
                }

                $data['is_obat'] = true;
                $data['tarif_satuan'] = $hargapakai;
                $data['sub_total'] = $qty * $hargapakai;
                $data['tarif_satuan_label'] = DocoHelpers::formatNumber($data['tarif_satuan']);
                $data['sub_total_label'] = DocoHelpers::formatNumber($data['sub_total']);
                /** Templete buat obat **/
                $tmpObat = [
                    'ruangan_id' => $ruangan,
                    'carabayar_id' => $carabayar,
                    'pegawai_id' => $jwt->pegawai_id,
                    'satuankecil_id' => $listTarif->satuankecil_id,
                    'pendaftaran_id' => $id,
                    'obatalkes_id' => $tindakan,
                    'pasien_id' => $pendaftaran->pasien_id,
                    'penjamin_id' => $penjamin,
                    'kelaspelayanan_id' => $pendaftaran->kelaspelayanan_id,
                    'tglpelayanan' => date('Y-m-d H:i:s',strtotime($tanggal_tindakan)),
                    'qty_oa' => $qty,
                    'hargasatuan_oa' => $hargapakai,
                    'harganetto_oa' => $hargaNetto,
                    'hargajual_oa' => $qty * $hargapakai,
                ];
                $data_insert = $tmpObat;
                break;
        }

        return [
            'data' => $data,
            'data_insert' => $data_insert,
            'type' => $jenis
        ];
    }
}