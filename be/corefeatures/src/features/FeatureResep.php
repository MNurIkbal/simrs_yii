<?php

namespace SirsCore\features;

use Yii;
use yii\base\Component;
use yii\base\Model;

use GuzzleHttp\Client;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use SirsCore\models\ObatAlkesPasien;
use SirsCore\models\attributevalidation\TrxOa;
use SirsCore\models\attributevalidation\TrxOaDetail;
use SirsCore\models\ObatAlkesFn;

class FeatureResep {
    public static function createOAResep($obatalkes_data = [], $is_distribusi = true) {
        if(!isset($obatalkes_data['trx_oa']) || empty($obatalkes_data['trx_oa'])){
            throw new \Exception("Tidak Ada Transaksi", 1);
        }
        $trx_oa = $obatalkes_data['trx_oa'];
        $modelOA = new TrxOa;
        $modelOA->attributes = $trx_oa;
        if(!$modelOA->validate()) {
            throw new \Exception("Data Obat Alkes Tidak Sesuai", 1);
        }
        $primaryAttributeModelOa = $modelOA->primary_key;
        $primaryValueModelOa = $trx_oa[$primaryAttributeModelOa];

        $trx_detail = [];
        if(isset($obatalkes_data['trx_oa_detail']) && is_array($obatalkes_data['trx_oa_detail'])) {
            foreach ($obatalkes_data['trx_oa_detail'] as $k_oa => $v_oa) {
                $modelOAdetail = new TrxOaDetail;
                $modelOAdetail->attributes = $v_oa;
                if(!$modelOAdetail->validate()) {
                    throw new \Exception("Data Obat Alkes Detail Tidak Sesuai", 1);
                }

                $infoObat = ObatAlkesFn::find()->where([
                    'ruangan_id' =>$trx_oa['ruangan_id'],
                    'obatalkes_id' => $v_oa['obatalkes_id']
                ])->asArray()->one();

                if(empty($infoObat)){
                    throw new \Exception("Data Obat/Alkes Tidak Ada", 1);
                }

                $detailTrans[$v_oa['obatalkes_id']] = [
                    'obatalkes_id' => $v_oa['obatalkes_id'],
                    'satuankecil_id' => isset($v_oa['satuankecil_id']) ? $v_oa['satuankecil_id'] : null,
                    'persendiscount' => $infoObat['persen_disc'],
                    'persenppn' => $infoObat['persen_ppn'],
                    'persenmargin' => $infoObat['persen_margin'],
                    'jmlmargin' => $infoObat['jml_margin'],
                    'jmldiscount' => $infoObat['jml_discount'],
                    'jmlppn' => $infoObat['jml_ppn']
                ];

                // $antrian_id = self::generateAntrian($infoObat['ruangan_id']);
                // var_dump($trx_oa['antrian_id']); exit();

                $generatedDetail= [
                    'tglpelayanan' => date('Y-m-d H:i:s'),
                    'satuankecil_id' => $infoObat['satuankecil_id'],
                    'hargasatuan_oa' => $modelOAdetail->is_ditagihkan == 1 ? $infoObat['jml_hargajual'] : 0,
                    'hargajual_oa' => $modelOAdetail->is_ditagihkan == 1 ? $infoObat['jml_hargajual'] * $v_oa['qty_oa'] : 0,
                    'harganetto_oa' => $infoObat['jml_harganetto'],
                    'carabayar_id' => $modelOA->carabayar_id,
                    'penjamin_id' => $modelOA->penjamin_id,
                    'pendaftaran_id' => $modelOA->pendaftaran_id,
                    'pasien_id' => $modelOA->pasien_id,
                    'pasienadmisi_id' => $modelOA->pasienadmisi_id,
                    'kelaspelayanan_id' => $modelOA->kelaspelayanan_id,
                    // 'antrian_id' => $trx_oa['antrian_id'],
                    $primaryAttributeModelOa => $primaryValueModelOa,
                    'additional_data' => empty($modelOAdetail->additional_data) ? null : $modelOAdetail->additional_data
                ];

                $trx_detail[] = array_replace($generatedDetail, $obatalkes_data['trx_oa_detail'][$k_oa]);
                // var_dump($trx_detail); exit();
            }
            $resOA = ObatAlkesPasien::batchInsert($trx_detail);
        }

        /**
        Set Status Bayar
        **/
        if(!empty($modelOA->pendaftaran_id) && $modelOA->set_tagihan) {
            $statusbayar = DocoConstants::BELUM_LUNAS;
            $res_update_pendaftaran = self::updateStatusBayarPendaftaran($trx_oa['pendaftaran_id'], $statusbayar);
        }

        return true;
    }

    /**
     * @param pendaftaran_id
     * @return boolean
     * @desc
     */
    private static function updateStatusBayarPendaftaran($pendaftaran_id, $statusbayar) {
        $res = Yii::$app->db->createCommand("
            UPDATE pendaftaran_t SET status_bayar = {$statusbayar}
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->execute();
        return $res;
    }

    // private static function generateAntrian($ruangan_id) {
    //     $data_konfigantrianfarmasi = KonfigAntrianFarmasi::find()->where(['fungsiantrian_id'=>$const,'is_default' => true])->one();
    //     $modelAntrian = new Antrian;
    //     $modelAntrian->ruangan_id = $ruangan_id;
    //     $modelAntrian->tgl_antrian = date('Y-m-d H:i:s');
    //     $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_F;
    //     $modelAntrian->racikan_id = $list_racikan[$racikanType];
    //     $fungsiantrian_id = ($data_konfigantrianfarmasi->fungsiantrian_id) ? $data_konfigantrianfarmasi->fungsiantrian_id : null;

    //     $modelAntrian->fungsiantrian_id = $fungsiantrian_id;
    //     $modelAntrian->save(false);
    //     $antrian_id = $modelAntrian->antrian_id;

    //     // $modelResep->antrian_id = $antrian_id;
    //     return $antrian_id;
    // }
}