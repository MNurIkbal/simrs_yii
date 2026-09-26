<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Rizal
 * @Date:   2018-11-28 23:50:21
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\models\InfoPasienGiziView;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\PermintaanMakan;
use app\modules\v1\models\PermintaanMakanDetail;
use app\modules\v1\models\JenisDiet;
use app\modules\v1\models\MenuDietView;
use app\modules\v1\models\InfoPermintaanMakanDetailView;
use app\modules\v1\models\InfoPermintaanMakanView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\InfoKunjunganRjView;
use app\modules\v1\models\InfoPasienRdView;
use Doco\models\WorklistPasien;
use Doco\Services\KasirService;
use Doco\models\TindakanPelayanan;
use app\modules\v1\models\PasienAdmisi;

class TransaksiPermintaanMakanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PermintaanMakan';
    protected $_title = 'Transaksi Permintaan Makan';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionBundleDataPermintaanMakan()
    {
        try {
            $waktu_diet = Lookup::find(true)
                            ->where(['lookup_type'=>'waktu'])
                            ->andWhere('is_deleted = FALSE')
                            ->andWhere('is_active = TRUE')
                            ->orderby('lookup_urutan' , SORT_ASC)
                            ->asArray()->all();
            $perubahan_diet = Lookup::find(true)
                            ->where(['lookup_type'=>'perubahan_diet'])
                            ->andWhere('is_deleted = FALSE')
                            ->andWhere('is_active = TRUE')
                            ->orderby('lookup_id' , SORT_ASC)
                            ->asArray()->all();
            $jenis_diet = JenisDiet::find()->where('jenisdiet_m.is_active <> FALSE')->asArray()->all();
            $dataMenuDiet = MenuDietView::find()->where(['is_active'=> TRUE])->asArray()->all();
            $menuDiet = [];
            $jenisdiet = [];
            $no = 0;
            $nomer = 0;

            $jenisdiet[$no] = ['id'=> '', 'text'=> 'Pilih Jenis Diet', 'selected'=>'selected', 'disabled'=>'disabled',];
            foreach ($jenis_diet as $key => $value) {
                $no++;
                $jenisdiet[$no] = [
                    'id' => $value['jenisdiet_id'],
                    'text' => $value['jenisdiet_kode']. ' - ' .$value['jenisdiet_nama']
                ];
            }

            $menuDiet[$nomer] = ['id'=> '', 'text'=> 'Pilih Menu Diet', 'selected'=>'selected', 'disabled'=>'disabled',];
            foreach ($dataMenuDiet as $key => $value) {
                $nomer++;
                $menuDiet[$nomer] = [
                    'id' => $value['makanandiet_id'].'-'.$value['daftartindakan_id'],
                    'text' => $value['makanandiet_nama']
                ];
            }
        } catch (\Exception $e) {
            $waktu_diet = $jenisdiet = [];
        } catch (\yii\db\Exception $e) {
            $waktu_diet = $jenisdiet = [];
        }


        return ['datamaster'=>['waktu_diet'=>$waktu_diet, 'jenis_diet'=>$jenisdiet,'menu_diet' => $menuDiet,'perubahan_diet'=> $perubahan_diet]];
    }

    public function actionSimpanPermintaanMakan()
    {
        try {
            $params = Yii::$app->request;
            $pendaftaran_id = $params->post('pendaftaran_id',0);
            $pegawai_pemesan = $params->post('pegawai_pemesan',0);
            $transaction = Yii::$app->db->beginTransaction();

            $infoPasienRi = InfoPasienRiView::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
            $pasienadmisi_id = $infoPasienRi->pasienadmisi_id;

            // if(!$modelMakan->validate()){
            //     throw new \yii\db\Exception('Gagal Validasi Permintaan Makan', $modelMakan->getErrors(),500);
            // }

            // if(!$modelMakan->save()){
            //     throw new \yii\db\Exception('Gagal Simpan Permintaan Makan', $modelMakan->getErrors(),500);
            // }

            $detailMakan = $params->post('detailMintaMakan',[]);
            if(is_array($detailMakan) && count($detailMakan)<1){
                throw new \yii\base\ErrorException("Tidak Ada Data Detail", 1);
            }

            foreach ($detailMakan as $val_detail_makan) {
                $modelMakan = new PermintaanMakan;
                $modelMakan->pendaftaran_id = $pendaftaran_id;
                $modelMakan->pasienadmisi_id = $pasienadmisi_id;
                $modelMakan->peg_pemesan_id = $pegawai_pemesan;
                $modelMakan->tgl_permintaanmakan = date('Y-m-d H:i:s');
                $modelMakan->status = 1;
                $modelMakan->save();

                $modelMakanDetail = new PermintaanMakanDetail;
                $modelMakanDetail->permintaanmakan_id = $modelMakan->getPrimaryKey();
                $modelMakanDetail->jenisdiet_id = $val_detail_makan['jenis_diet'];
                $modelMakanDetail->makanandiet_id = $val_detail_makan['menu_diet'];
                $modelMakanDetail->waktu_diet = $val_detail_makan['waktu_diet'];
                $modelMakanDetail->jumlah = $val_detail_makan['jumlah_diet'];
                $modelMakanDetail->keterangan = isset($val_detail_makan['keterangan']) ? $val_detail_makan['keterangan'] : null;
                if(!$modelMakanDetail->validate()){
                    throw new \yii\db\Exception('Gagal Validasi Detail Permintaan Makan', $modelMakanDetail->getErrors(),500);
                }

                if(!$modelMakanDetail->save()){
                    throw new \yii\db\Exception('Gagal Simpan Detail Permintaan Makan', $modelMakanDetail->getErrors(),500);
                }
            }

            $transaction->commit();

            $modelMakanAfterSave = PermintaanMakan::findOne($modelMakan->getPrimaryKey());
            return [
                'message'=>'Proses Berhasil!',
                'text' => 'Permintaan Makan Berhasil Dibuat',
                'no_permintaanmakan' => $modelMakanAfterSave->no_permintaanmakan
            ];

        } catch(\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\ErrorException $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'data'=>$e->getMessage(),
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan Internal',
                'errorInfo'=>$e->getName()
            ];
        } catch(\yii\base\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan Internal',
                'errorInfo'=>$e->getName()
            ];
        }
    }

    public function actionCariJenisDiet()
    {
        $params = Yii::$app->request;
        $term = $params->get('term','');
        $dataJenisDiet = JenisDiet::find()->where('jenisdiet_m.is_active <> FALSE');
        if($term){
            $dataJenisDiet->andWhere("LOWER(jenisdiet_kode) LIKE '%".$term."%' OR LOWER(jenisdiet_nama) LIKE '%".$term."%'");
        }

        return $dataJenisDiet->asArray()->all();
    }

    public function actionCariMenuDietByJenis()
    {
        $params = Yii::$app->request;
        $jenis_id = $params->get('jenis_id',0);
        $dataMenuDiet = MenuDietView::find()->where(['jenisdiet_id'=>$jenis_id])->andWhere('is_active = TRUE');

        return $dataMenuDiet->asArray()->all();
    }

    /**
    * @controller actionCetakPermintaanMakan
    * @attribute #infpasien_nama# => Informasi Pasien: Nama
    * @attribute #infpasien_norm# => Informasi Pasien: No Rekam Medik
    * @attribute #infpasien_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
    * @attribute #infpasien_nopendaftaran# => Informasi Pasien: No Pendaftaran
    * @attribute #infpasien_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #infpasien_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
    * @attribute #infpasien_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #infpasien_umur# => Informasi Pasien: Umur
    * @attribute #infpasien_dokterdpjp# => Informasi Pasien: Dokter DPJP
    * @attribute #infpasien_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
    * @attribute #infpasien_nokamar# => Informasi Pasien: No. Kamar
    * @attribute #infpasien_nobed# => Informasi Pasien: No. Bed
    * @attribute #infpasien_carabayar# => Informasi Pasien: Cara Bayar
    * @attribute #infpasien_penjamin# => Informasi Pasien: Penjamin
    * @attribute #tgl_permintaanmakan# => Permintaan Makan: Tanggal Permintaan Makan
    * @attribute #no_permintaanmakan# => Permintaan Makan: No Permintaan Makan
    * @attribute #pegawai_pemesan# => Permintaan Makan: Pegawai Pemesan
    * @attribute #riwayat_mintamakan# => table riwayat
    * @attribute #nama_usercetak# => Nama User Cetak
    * @attribute #tgl_usercetak# => Tanggal User Cetak
    **/
    public function actionCetakPermintaanMakan()
    {
        $params = Yii::$app->request;
        $no_permintaanmakan = $params->get('no_permintaanmakan',0);
        $pendaftaran_id = $params->get('pendaftaran_id',0);
        $data_riwayat = [];
        $get_data_riwayat = InfoPermintaanMakanDetailView::find()->where(['no_permintaanmakan'=>$no_permintaanmakan])->asArray()->all();
        $data_riwayat = $get_data_riwayat;

        $nama_usercetak = $params->get('nama_usercetak','');
        $id_usercetak = $params->get('id_usercetak',0);

        $nama_user = '';
        $mNamaPegawai = PegawaiView::find(true)->where(['pegawai_id'=>$id_usercetak])->asArray()->one();

        if(is_null($mNamaPegawai)){
            $nama_user = $nama_usercetak;
        }else{
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }

        $data_pasien = InfoPasienRiView::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
        if(empty($data_pasien)){
            $data_pasien = InfoKunjunganRjView::find()->select([
                '*',
                'nama_pegawai as dokter_admisi' ,
                'status_periksa1 as stat_ranap' ,
                'kelaspelayanan_nama as kelas_pelayanan' ,
            ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        }
        if(empty($data_pasien)){
            $data_pasien = InfoPasienRdView::find()->select([
                '*',
                'dokter_jaga as dokter_admisi' ,
                'status_periksa as stat_ranap' ,
                'kelaspelayanan_nama as kelas_pelayanan' ,
            ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        }

        $data_permintaan_makan = InfoPermintaanMakanView::find()->where(['no_permintaanmakan'=>$no_permintaanmakan])->one();

        $print = new DocoPrint();
        $print->attributes = [
            '#infpasien_nama#' => @$data_pasien['nama_pasien'],
            '#infpasien_norm#' => @$data_pasien['no_rekam_medik'],
            '#infpasien_tglpendaftaran#' => date('d-m-Y H:i:s',strtotime($data_pasien['tgl_pendaftaran'])),
            '#infpasien_nopendaftaran#' => @$data_pasien['no_pendaftaran'],
            '#infpasien_jeniskelamin#' => @$data_pasien['jenis_kelamin'],
            '#infpasien_kasuspenyakit#' => @$data_pasien['jeniskasuspenyakit_nama'],
            '#infpasien_tgllahir#' => date('d-m-Y',strtotime($data_pasien['tanggal_lahir'])),
            '#infpasien_umur#' => @$data_pasien['umur'],
            '#infpasien_dokterdpjp#' => @$data_pasien['dokter_admisi'],
            '#infpasien_kelaspelayanan#' => @$data_pasien['kelas_pelayanan'],
            '#infpasien_nokamar#' => @$data_pasien['kamarruangan_nokamar'],
            '#infpasien_nobed#' => @$data_pasien['no_tempattidur'],
            '#infpasien_carabayar#' => @$data_pasien['carabayar_nama'],
            '#infpasien_penjamin#' => @$data_pasien['penjamin_nama'],
            '#tgl_permintaanmakan#' => date('d-m-Y H:i:s',strtotime($data_permintaan_makan['tgl_permintaanmakan'])),
            '#no_permintaanmakan#' => @$no_permintaanmakan,
            '#pegawai_pemesan#' => @$data_permintaan_makan['nama_pegawai'],
            '#riwayat_mintamakan#' => $this->renderPartial('cetakan', ['data_riwayat'=>$data_riwayat]),
            '#nama_usercetak#' => @$nama_user,
            '#tgl_usercetak#' => date('d-m-Y H:i:s')
        ];
        $print->Output();
    }

    /**
     * @todo Fungsi untuk mendapatkan data permintaan makan beserta data pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPermintaanMakan($id)
    {
        $permintaan_makan = InfoPermintaanMakanView::find()->where(['permintaaanmakan_id' => $id])->one();
        $permintaan_makan_detail = InfoPermintaanMakanDetailView::find()->where(['permintaaanmakan_id' => $id])->asArray()->all();
        $detail_permintaan_makan = PermintaanMakanDetail::find()->where(['permintaanmakan_id' => $id])
                                        ->select(['permintaanmakandetail_t.*', 'makanandiet_m.daftartindakan_id'])
                                        ->leftJoin('makanandiet_m', 'makanandiet_m.makanandiet_id = permintaanmakandetail_t.makanandiet_id')
                                        ->asArray()->all();
        $data_pasien = null;
        $billFilters = [
            'pendaftaran_id' => $permintaan_makan->pendaftaran_id
        ];
        if (isset($permintaan_makan->pendaftaran_id) && $permintaan_makan->pendaftaran_id != null) {
            $data_pasien = InfoPasienGiziView::find()->where(['pendaftaran_id' => $permintaan_makan->pendaftaran_id])->one();
            if (!empty($data_pasien)) {
                $billFilters = array_merge($billFilters, [
                    'pasienadmisi_id' => $data_pasien->pasienadmisi_id
                ]);
            }
        }
        if(empty($data_pasien)){
            $data_pasien = InfoKunjunganRjView::find()->select([
                '*',
                'nama_pegawai as dokter_admisi' ,
                'status_periksa1 as stat_ranap' ,
            ])->where(['pendaftaran_id' => $permintaan_makan->pendaftaran_id])->asArray()->one();
        }
        if(empty($data_pasien)){
            $data_pasien = InfoPasienRdView::find()->select([
                '*',
                'dokter_jaga as dokter_admisi' ,
                'status_periksa as stat_ranap' ,
            ])->where(['pendaftaran_id' => $permintaan_makan->pendaftaran_id])->asArray()->one();
        }

        $dataMaster = $this->actionBundleDataPermintaanMakan();

        $getRecentBilledStatus = false;
        if ( isset($detail_permintaan_makan[0]['daftartindakan_id']) ) {
            $getRecentBilledStatus = (new TindakanPelayanan)->find()->select([
                'tindakanpelayanan_id',
                'daftartindakan_id'
            ])->where([
                'daftartindakan_id' => $detail_permintaan_makan[0]['daftartindakan_id']
            ])->andWhere($billFilters)->asArray()->all();
        }

        return [
            'data_pasien' => $data_pasien,
            'permintaan_makan' => $permintaan_makan,
            'permintaan_makan_detail' => $permintaan_makan_detail,
            'waktu_diet' => $dataMaster['datamaster']['waktu_diet'],
            'jenis_diet' => $dataMaster['datamaster']['jenis_diet'],
            'menu_diet' => $dataMaster['datamaster']['menu_diet'],
            'perubahan_diet' => $dataMaster['datamaster']['perubahan_diet'],
            'detail_permintaan_makan' => $detail_permintaan_makan,
            'billStatus' => $getRecentBilledStatus ? true : false
        ];
    }

    /**
     * @todo Fungsi untuk mengubah data permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUbahPermintaanMakan($id)
    {
        $params = Yii::$app->request;
        $pegawai_pemesan = $params->post('pegawai_pemesan', 0);
        $detail_permintaan_makan = $params->post('detailPermintaanMakan', []);
        $transaction = Yii::$app->db->beginTransaction();

        if(is_array($detail_permintaan_makan) && count($detail_permintaan_makan) < 1) {
            throw new \yii\base\ErrorException("Tidak Ada Data Detail", 1);
        }
        $dataPendaftaran = $this->getPendaftaranData([
            'id' => $id
        ]);

        $data = $integrateKasir = $daftarTindakanIds = [];
        foreach ($detail_permintaan_makan as $key => $value) {
            $explode = explode("-", $value['makanandiet_id']);
            $data[$key]['permintaanmakan_id']  = $id;
            $data[$key]['jenisdiet_id']        = ArrayHelper::getValue($value, 'jenisdiet_id', 1);
            $data[$key]['makanandiet_id']      = ArrayHelper::getValue($explode, 0, 1);
            $data[$key]['waktu_diet']          = null;
            $data[$key]['jumlah']              = 1;
            $data[$key]['keterangan']          = ArrayHelper::getValue($value, 'keterangan');
            $data[$key]['daftartindakan_id']   = ArrayHelper::getValue($explode, 1);
            $data[$key]['jenisdiet_lainnya']   = ArrayHelper::getValue($value, 'jenisdiet_lainnya');
            $data[$key]['perubahan_diet']      = ArrayHelper::getValue($value, 'perubahan_diet');
            $data[$key]['kesimpulan']          = ArrayHelper::getValue($value, 'kesimpulan');
            $data[$key]['kondisi_puasa']       = ArrayHelper::getValue($value, 'kondisi_puasa');
            $data[$key]['puasa_tgl_awal']      = ArrayHelper::getValue($value, 'puasa_tgl_awal');
            $data[$key]['puasa_tgl_akhir']     = ArrayHelper::getValue($value, 'puasa_tgl_akhir');
            $data[$key]['puasa_operasi_awal']  = ArrayHelper::getValue($value, 'puasa_operasi_awal');
            $data[$key]['puasa_operasi_akhir'] = ArrayHelper::getValue($value, 'puasa_operasi_akhir');
            $data[$key]['buka_puasa']          = ArrayHelper::getValue($value, 'buka_puasa');
            $data[$key]['is_ditagihkan']       = ArrayHelper::getValue($value, 'is_ditagihkan');

            $daftarTindakanIds[] = $data[$key]['daftartindakan_id'];
            $integrateKasir[] = [
                'daftartindakan_id' => $data[$key]['daftartindakan_id'],
                'qty' => $data[$key]['jumlah'],
                'dokter_id' => $dataPendaftaran['pegawai_id'],
            ];
        }

        $this->deletePermintaanMakan([
            'id' => $id,
            'dataPendaftaran' => $dataPendaftaran,
            'daftartindakan_id' => $daftarTindakanIds
        ]);

        PermintaanMakanDetail::batchInsert($data);

        // is ditagihkan diambil dari index 0 dulu, karena datanya didalam array dan defaultnya pasti array dengan 1 data
        if(!empty($detail_permintaan_makan[0]['is_ditagihkan'] && $detail_permintaan_makan[0]['is_ditagihkan'] == true)){
            $postTagihan = (new KasirService)->tagihan($dataPendaftaran, $integrateKasir);
            if(isset($postTagihan['meta']['result'])) {
                $result = $postTagihan['meta']['result'];
                if($result == 'failed') {
                    $message = isset($postTagihan['message']) ? $postTagihan['message'] : 'Terjadi kesalahan saat integerasi dengan kasir';
                    return [
                        'title' => 'Proses Gagal!',
                        'text' => $message,
                        'status' => isset($postTagihan['meta']['code']) ? $postTagihan['meta']['code'] : 400
                    ];
                }
            }
        }

        $transaction->commit();
        $modelMakanAfterSave = PermintaanMakan::findOne($id);
        return [
            'message'=>'Proses Berhasil!',
            'text' => 'Data Berhasil Diubah.',
            'no_permintaanmakan' => $modelMakanAfterSave->no_permintaanmakan
        ];
    }

    public function getPendaftaranData($payload = [])
    {
        $getHeaderData = PermintaanMakan::find()->select([
            'pendaftaran_id',
            'pasienadmisi_id'
        ])->where([
            'permintaanmakan_t.permintaaanmakan_id' => $payload['id']
        ])->asArray()->one();

        $dataPendaftaran = (new \Doco\models\WorklistPasien)->find()->select([
            'pendaftaran_id',
            'kelaspelayanan_id',
            'no_pendaftaran',
            'pegawai_id'
        ])->where([
            'pendaftaran_id' => $getHeaderData['pendaftaran_id'],
            'jenis' => !empty($getHeaderData['pasienadmisi_id']) ? 'RI' : [
                'RD', 'RJ'
            ],
        ])->asArray()->one();

        if(!empty($getHeaderData['pasienadmisi_id'])){
            $pasien_admisi = PasienAdmisi::find()->select([
                'pasienadmisi_id',
                'kelas_ditagihkan_id',
                'is_pasientitipan',
            ])->where([
                'pasienadmisi_id' => $getHeaderData['pasienadmisi_id']
            ])->asArray()->one();
            if($pasien_admisi['is_pasientitipan']){
                $dataPendaftaran['kelaspelayanan_id'] = $pasien_admisi['kelas_ditagihkan_id'];
            }
         }

        return $dataPendaftaran;
    }

    public function deletePermintaanMakan($payload = [])
    {
        $getTindakanPelayanan = TindakanPelayanan::find()->select([
            'tindakanpelayanan_id'
        ])->where([
            'pendaftaran_id' => $payload['dataPendaftaran']['pendaftaran_id'],
            'ruangan_id' => Yii::$app->jwt->ruangan_id,
            'daftartindakan_id' => $payload['daftartindakan_id']
        ])->asArray()->all();

        if (!empty($getTindakanPelayanan)) {
            $deleteTindakanPelayanan = (new KasirService)->batalTindakan($payload['dataPendaftaran'], $getTindakanPelayanan);
        }

        \Yii::$app->db->createCommand()->delete(PermintaanMakanDetail::tableName(), [ 'permintaanmakan_id' => $payload['id'] ])->execute();
        return true;
    }
}
