<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use app\modules\v1\models\AlatTerpasang;
use app\modules\v1\models\HistoriJenazah;
use app\modules\v1\models\PersetujuanJenazah;
use app\modules\v1\models\AmbilJenazah;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\Pendaftaran;

use app\modules\v1\businessLogic\StokObatAlkes as BLStokObatAlkes;

class OrderController extends DocoActiveController
{
    public $modelClass = '';

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
        /** list orderan tindakan/obat dan linen atau alat pasang **/
        /** Must be json data **/
        $list_order = $request->post('list_order','{}');
        $list_linen = $request->post('list_linen','{}');
        /** Untuk Data Penangung Jenazah **/
        $kondisi = $request->post('kondisi');
        $hubungan_keluarga = $request->post('hubungan_keluarga');
        $nama_pj = $request->post('nama_pj');
        $jeniskelamin_id = $request->post('jeniskelamin_id');
        $umur = $request->post('umur');
        $no_kontak = $request->post('no_kontak');
        $alamat = $request->post('alamat');
        $pegawai = Yii::$app->user->identity->pegawai_id;
        $now = date('Y-m-d H:i:s');
        $linen = $tindakan = $obat = [];
        $_POST['ruangan_id'] = DocoConstants::VAR_R_JNZ;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $list_linen = json_decode($list_linen,true);
            $list_order = json_decode($list_order,true);

            $linen = [];

            $pendaftaran = Pendaftaran::find()->where([
                'pendaftaran_id' => $id
            ])->one();

            if (empty($pendaftaran)) {
                return [
                    'status' => 422,
                    'messages' => 'Pendaftaran tidak di temukan'
                ];
            }
            /** Untuk barang linen aja **/
            if (isset($list_linen['linen']) && is_array($list_linen['linen'])) {
                foreach ($list_linen['linen'] as $value) {
                    $linen[] = [
                        'pendaftaran_id' => $id,
                        'pasienadmisi_id' => $pendaftaran['pasienadmisi_id'],
                        'ruangan_id' => DocoConstants::VAR_R_JNZ,
                        'tgl_alatterpasang' => $now,
                        'obatalkes_id'=>null,
                        'barang_id' => $value['barang_id'],
                        'qty' => $value['qty'],
                    ];
                }
            }
            if (isset($list_linen['alat']) && is_array($list_linen['alat'])) {
                foreach ($list_linen['alat'] as $value) {
                    $linen[] = [
                        'pendaftaran_id' => $id,
                        'pasienadmisi_id' => $pendaftaran['pasienadmisi_id'],
                        'ruangan_id' => DocoConstants::VAR_R_JNZ,
                        'tgl_alatterpasang' => $now,
                        'barang_id'=>null,
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty' => $value['qty'],
                    ];
                }
            }

            if (!empty($linen)) {
                $saveLinen = AlatTerpasang::batchInsert($linen);
                if(!$saveLinen){
                    throw new \yii\db\Exception("Terjadi Kesalahan - Linen", 1);
                }
            }
            /** End Linen **/
            $model = new PasienMasukPenunjang;
            $model->kelaspelayanan_id = $pendaftaran['kelaspelayanan_id'];
            $model->jeniskasuspenyakit_id = $pendaftaran['jeniskasuspenyakit_id'];
            $model->pasienadmisi_id = $pendaftaran['pasienadmisi_id'];
            $model->pegawai_id = $pegawai;
            $model->ruangan_id = DocoConstants::VAR_R_JNZ;
            $model->pasien_id = $pendaftaran['pasien_id'];
            $model->pendaftaran_id = $pendaftaran['pendaftaran_id'];
            $model->ruanganasal_id = $pendaftaran['ruangan_id'];
            $model->tglmasukpenunjang = $now;
            $model->kunjungan = DocoConstants::VAR_K_L;
            $model->status_periksa = DocoConstants::VAR_BLM_DTRM_JNZ;
            if ($model->save()) {
                $idParent = $model->getPrimaryKey();
                $getNomorPenunjang = PasienMasukPenunjang::find()->select(['pasienmasukpenunjang_id', 'no_masukpenunjang'])->where(['pasienmasukpenunjang_id' => $idParent])->asArray()->one();
                $modelSetuju = new PersetujuanJenazah;
                $modelSetuju->pasien_id = $pendaftaran['pasien_id'];
                $modelSetuju->ruangan_id = DocoConstants::VAR_R_JNZ;
                $modelSetuju->pendaftaran_id = $pendaftaran['pendaftaran_id'];
                $modelSetuju->pasienadmisi_id = $pendaftaran['pasienadmisi_id'];
                $modelSetuju->kondisi = $kondisi;
                $modelSetuju->hubungan_keluarga = $hubungan_keluarga;
                $modelSetuju->nama_pj = $nama_pj;
                $modelSetuju->jeniskelamin_id = $jeniskelamin_id;
                $modelSetuju->umur = $umur;
                $modelSetuju->no_kontak = $no_kontak;
                $modelSetuju->alamat = $alamat;
                if ($modelSetuju->save()) {
                    $idPersetujuan = $modelSetuju->persetujuanjenazah_id;
                    $modelHistori = new HistoriJenazah;
                    $modelHistori->pasien_id = $pendaftaran['pasien_id'];
                    $modelHistori->pendaftaran_id = $pendaftaran['pendaftaran_id'];
                    $modelHistori->pasienadmisi_id = $pendaftaran['pasienadmisi_id'];
                    $modelHistori->pasienmasukpenunjang_id = $idParent;
                    $modelHistori->persetujuanjenazah_id = $idPersetujuan;
                    $modelHistori->tgl_pelayanan = $now;
                    $modelHistori->pegawai_id = $pegawai;
                    $modelHistori->status = DocoConstants::VAR_BLM_DTRM_JNZ;
                    if(!$modelHistori->save()):
                        $transaction->rollBack();
                        return [
                            'status' => 422,
                            'data' => $modelHistori->errors
                        ];
                    endif;

                    /** Order Tindakan dan Obat Alkes **/
                    if (isset($list_order['tindakan']) && is_array($list_order['tindakan'])) {
                        foreach($list_order['tindakan'] as $key =>$value):
                            $tindakan[] = [
                                'kelaspelayanan_id' => $pendaftaran['kelaspelayanan_id'],
                                'pasien_id' => $pendaftaran['pasien_id'],
                                'instalasi_id' => DocoConstants::VAR_I_JNZ,
                                'daftartindakan_id' => isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null,
                                'tipepaket_id' => isset($value['tipepaket_id']) ? $value['tipepaket_id'] : null,
                                'jeniskasuspenyakit_id' => $pendaftaran['jeniskasuspenyakit_id'],
                                'carabayar_id' => $pendaftaran['carabayar_id'],
                                'pendaftaran_id' => $pendaftaran['pendaftaran_id'],
                                'ruangan_id' => DocoConstants::VAR_R_JNZ,
                                'pasienmasukpenunjang_id' => $idParent,
                                'penjamin_id' => $pendaftaran['penjamin_id'],
                                'pasienadmisi_id' => $pendaftaran['pasienadmisi_id'],
                                'tgl_tindakan' => $now,
                                'tarif_satuan' => $value['tarif_satuan'],
                                'tarif_tindakan' => ($value['tarif_tindakan']+$value['tarifcyto_tindakan']) *$value['qty_tindakan'],
                                'tarifcyto_tindakan' => $value['tarifcyto_tindakan'],
                                'qty_tindakan' => $value['qty_tindakan'],
                                'cyto_tindakan' => isset($value['cyto_tindakan']) ? true : false,
                                'dokterpenanggungjawab_id' => isset($value['dokterpenanggungjawab_id']) ? $value['dokterpenanggungjawab_id'] : null,
                                'additional_data' => isset($value['additional_data']) ? json_encode($value['additional_data']) : '{}',
                            ];
                        endforeach;
                        if(!empty($tindakan)){
                            $save = TindakanPelayanan::batchInsert($tindakan);
                        }
                    }
                    if (isset($list_order['obat']) && is_array($list_order['obat'])) {
                        $tanggalBerlaku = date('Y-m-d');
                        $konfig = $connection->createCommand("
                            SELECT metodeantrian FROM konfigfarmasi_k
                            WHERE tglberlaku >= '{$tanggalBerlaku}'
                            AND konfigfarmasi_aktif = true
                            AND is_active = true
                        ")->queryOne();
                        // Mencari Metode dengan nilai default FEFO
                        $currentMetode = BLStokObatAlkes::FEFO;
                        $inputStok = [];
                        foreach($list_order['obat'] as $key => $value):
                            $obat[] = [
                                'pendaftaran_id' => $pendaftaran['pendaftaran_id'],
                                'pasien_id' => $pendaftaran['pasien_id'],
                                'pasienadmisi_id' => $pendaftaran['pasienadmisi_id'],
                                'ruangan_id' => DocoConstants::VAR_R_JNZ,
                                'carabayar_id' => $pendaftaran['carabayar_id'],
                                'penjamin_id' => $pendaftaran['penjamin_id'],
                                'pegawai_id' => $pegawai,
                                'satuankecil_id' => $value['satuankecil_id'],
                                'obatalkes_id' => $value['obatalkes_id'],
                                'pasienmasukpenunjang_id' => $idParent,
                                'tglpelayanan' => $now,
                                'qty_oa' => $value['qty'],
                                'hargasatuan_oa' => $value['hargajual'],
                                'harganetto_oa' => $value['harganetto'],
                                'hargajual_oa' => $value['hargajual']*$value['qty'],
                                'signa_oa' => isset($value['signa']) ? $value['signa'] : null,
                                'created_by' => $pegawai
                            ];
                            $inputStok[] = [
                                'obatalkes_id' => $value['obatalkes_id'],
                                'qty_satuanpakai' => $value['qty'],
                                'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                                'nobatch' => isset($value['nobatch']) ? $value['nobatch'] : null,
                                'harganetto' => $value['harganetto'],
                                'persendiscount' => $value['persendiscount'],
                                'persenppn' => $value['persenppn'],
                                'persenmargin' => $value['persenmargin'],
                                'jmldiscount' => $value['jmldiscount'],
                                'jmlmargin' => $value['jmlmargin'],
                                'jmlppn' => $value['jmlppn'],
                            ];
                        endforeach;
                        ObatAlkesPasien::batchInsert($obat);
                        $getAlkesPasien = ObatAlkesPasien::find()->select(['pasienmasukpenunjang_id', 'obatalkespasien_id','obatalkes_id'])->where(['pasienmasukpenunjang_id'=>$idParent])->asArray()->all();
                        $dataAlkes = [];
                        foreach($getAlkesPasien as $key => $value):
                            $dataAlkes[$value['obatalkes_id']] = $value;
                        endforeach;
                        foreach($inputStok as $key => $value):
                            $inputStok[$key]['obatalkespasien_id'] = $dataAlkes[$value['obatalkes_id']]['obatalkespasien_id'];
                        endforeach;
                        BLStokObatAlkes::$distribusi = false;
                        if ($currentMetode === BLStokObatAlkes::FEFO) {
                            $methode = BLStokObatAlkes::methodeFEFO($inputStok,$now);
                        } else {
                            $methode = BLStokObatAlkes::methodeFIFO($inputStok,$now);
                        }
                    }
                    /** End Order Tindakan **/
                    $result = [
                        'response'=>[
                            'text' => 'Simpan Data Sukses!',
                            'title' => 'Proses Berhasil!',
                            'pasienmasukpenunjang_id' => DocoHelpers::encrypt($idParent),
                            'persetujuanjenazah_id' => DocoHelpers::encrypt($idPersetujuan),
                            'nomor' => $getNomorPenunjang['no_masukpenunjang'],
                            'pendaftaran_id' => DocoHelpers::encrypt($pendaftaran['pendaftaran_id']),
                        ]
                    ];
                    $transaction->commit();
                    return $result;
                } else {
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'data' => $modelSetuju->errors
                    ];
                }
            } else {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'data' => $model->errors
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

}