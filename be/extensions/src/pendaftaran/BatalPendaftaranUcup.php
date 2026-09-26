<?php

/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\pendaftaran;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyPasienMasukPenunjangView;
use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\SyncEditsantoyusupR;

use app\modules\v1\payload\PasienBatalKunjungan;
use app\modules\v1\cache\Cache;

use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;

class BatalPendaftaranUcup extends \Doco\processes\BatalPendaftaranProcess
{
    const IGD = 'igd';
    const RANAP = 'ranap';
    const RAJAL = 'rajal';
    const MCU = 'mcu';
    const ST_YUSUP = 'st-yusup';
    const PENUNJANG = 'penunjang';
    const PAKET = 'paket';
    const TINDAKAN = 'tindakan';

    private function syncUpdatePendaftaran($idPendaftaran)
    {
        $result = $asuransi = $keluarga = $penanggung = $pasien = $penjamin = $penanggungJawab = [];
        $pdftrn = SyPendaftaranView::find()
        ->where(['pendaftaran_id' => $idPendaftaran])
        ->asArray()
        ->one();

        if(!empty($pdftrn)) {
            $add = json_decode($pdftrn['additional_data'], true);

            if(!empty($pdftrn['asuransipasien_id'])) {
                $asuransi = SyAsuransiPasienView::find()
                ->where(['asuransipasien_id' => $pdftrn['asuransipasien_id']])
                ->asArray()
                ->one();
            }

            //** keluarga harus selalu di kirim */
            // if(isset($add['keluargapasien']) && !empty($add['keluargapasien'])) {
                $keluarga = SyKeluargaPasienView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['keluargapasien_id' => SORT_DESC])
                ->asArray()
                ->one();
            // }

            if(isset($add['penanggungbiaya']) && !empty($add['penanggungbiaya'])) {
                $penanggung = SyPenanggungBiayaView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['penanggungbiaya_id' => SORT_DESC])
                ->asArray()
                ->one();
            }

            if(isset($add['penanggung_jawab']) && !empty($add['penanggung_jawab'])) {
                $penanggungJawab = SyPenanggungJawabView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['penanggungjawab_id' => SORT_DESC])
                ->asArray()
                ->one();
            }

            $pasien = SyPasienView::find()
            ->where(['pasien_id' => $pdftrn['pasien_id']])
            ->asArray()
            ->one();

            if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                $umurExp = explode(" ",$pdftrn['umur']);
                $pdftrn['umur_hari'] = $umurExp[4];
                $pdftrn['umur_bulan'] = $umurExp[2];
                $pdftrn['umur_tahun'] = $umurExp[0];
            }

            if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                $additionalPasien = json_decode($pasien['additional_pasien']);
                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                    if(isset($ktp)) {
                        $pasien['nik'] = $ktp;
                    }
                }
            }

            if (isset($pdftrn['dokterkonsul']) && !empty($pdftrn['dokterkonsul'])) {
                $listDokter = json_decode($pdftrn['dokterkonsul']);
                $pdftrn['dok_konsul'] = [];
                $getDokter = Pegawai::find()
                            ->select('additional_data')
                            ->where(['IN', 'pegawai_id', $listDokter])
                            ->asArray()
                            ->all();
                for ($i=0; $i < count($getDokter); $i++) { 
                    $pdftrn['dok_konsul'][] = $getDokter[$i]['additional_data'];
                }
            }

            $penjamin = SyPenjaminView::find()
            ->where(['penjamin_id' => $pdftrn['penjamin_id']])
            ->asArray()
            ->one();

            $penunjang = SyPasienMasukPenunjangView::find()
            ->where(['pendaftaran_id' => $idPendaftaran])
            ->asArray()
            ->one();
        }

        $result = [
            'pendaftaran' => $pdftrn,
            'pasien' => $pasien,
            'asuransi' => $asuransi,
            'keluarga' => $keluarga,
            'penanggung' => $penanggung,
            'penjamin' => $penjamin,
            'penunjang' => $penunjang,
            'penanggungJawab' => $penanggungJawab,
        ];

        $modelSync = new SyncEditsantoyusupR;
        $modelSync->pendaftaran_id = $pdftrn['pendaftaran_id'];
        $modelSync->pasien_id = $pdftrn['pasien_id'];
        $modelSync->created_date = date("Y-m-d H:i:s");
        $modelSync->count_sync = 0;
        $modelSync->payload = json_encode($result);
        $modelSync->state = 'batal';
        $modelSync->save(false);

        return $result;
    }

    protected function processFlow()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $isTagihan = false;
        $payload = new PasienBatalKunjungan;
        $payload->attributes = $request->post();
        if (!$payload->validate()) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

        $check = $jwt->katakunci_pemakai;
        $valid = Yii::$app->security->validatePassword($payload->password, $check);
        if (!$valid) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Password salah.'
            ]);
        }

        $model = Pendaftaran::find()->andWhere([
            self::NO_PENDAFTARAN => $payload->no_pendaftaran
        ])->one();

        //Cek Pembayaran
        // $countPembayaran = TindakanPelayanan::find()
        //         ->where([self::PENDAFTARAN_ID => $model->pendaftaran_id])
        //         ->andWhere(['is not', 'tindakansudahbayar_id', null])
        //         ->count();
        // if($countPembayaran != 0) {
        //     return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
        //         'text' => 'Pasien sudah melakukan pembayaran.'
        //     ]);
        // }

        if (!empty($model)) {
            $statBayar = $model->status_bayar;
            $statPeriksa = $model->status_periksa;
            $whiteList = [
                DocoConstants::STATUS_PERIKSA_ANTRIAN_POLI,
                DocoConstants::STATUS_PERIKSA_AN_PENDFTRN,
                DocoConstants::STATUS_PERIKSA_AN_KSR,
                DocoConstants::VAR_SP_BP,
            ];

            if (!in_array($statPeriksa, $whiteList)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => self::ERR_GAGAL_BATAL
                ]);
            }

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
            // add row batal periksa
                $registId = $model->pendaftaran_id;
                $batalPeriksa = new PasienBatalPeriksa;
                $batalPeriksa->pendaftaran_id = $registId;
                $batalPeriksa->tgl_batal = date('Y-m-d');
                $batalPeriksa->alasan_batal = $payload->alasan_batal;
                $pasienbatalperiksa_id = '';
                if ($batalPeriksa->save()) {
                    $pasienbatalperiksa_id = $batalPeriksa->pasienbatalperiksa_id;
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $batalPeriksa->errors
                    ]);
                }
                $model->pasienbatalperiksa_id = $pasienbatalperiksa_id;
                $model->status_periksa        = DocoConstants::STATUS_PERIKSA_BTL_KUNJ;

                $caraBayarId = Cache::getCaraBayarPenjamin($model->penjamin_id);
                $attrCaraBayar = Cache::getAttrCaraBayar($caraBayarId);
                $groupCaraBayar = !empty($attrCaraBayar['groupcarabayar_id']) ? $attrCaraBayar['groupcarabayar_id'] : null;

                //** Cek tagihan */
                // $countTagihan = TindakanPelayanan::find()
                // ->where([self::PENDAFTARAN_ID => $registId])
                // ->count();

                // if($countTagihan != 0) {
                //     $isTagihan = true;
                // }

                //** Case APS Asuransi */
                $pasienAps = $this->findPasienPasienMasukPenunjang()
                ->where([self::PENDAFTARAN_ID => $registId])
                ->all();
                    
                if(!empty($pasienAps) && $groupCaraBayar != DocoConstants::GROUP_UMUM) {
                    $stringIds = '';
                    $tmpPenunjang = [];

                    foreach($pasienAps as $k => $v){
                        if($v->status_periksa != DocoConstants::ST_P_PEN_BLM_PRKS) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                'text' => self::ERR_GAGAL_BATAL
                            ]);
                        } else {
                            $tmpPenunjang[] = $v->pasienmasukpenunjang_id;
                            $isTagihan = true;
                        }
                    }

                    if(!empty($tmpPenunjang)) {
                        $stringIds = implode(',', $tmpPenunjang);
                        $status = DocoConstants::ST_P_PEN_BTL;

                        Yii::$app->db->createCommand("
                            UPDATE pasienmasukpenunjang_t SET status_periksa = {$status} WHERE pasienmasukpenunjang_id IN ({$stringIds})
                        ")->execute();
                    }
                }

                //** Case Pasien RS*/
                $pasienRs = $this->findPasienKeUnitLain()
                ->where([self::PENDAFTARAN_ID => $registId])
                ->all();
                   
                if(!empty($pasienRs)) {
                    $stringIdsRs = '';
                    $tmpPenunjangRs = [];

                    foreach($pasienRs as $k => $v) {
                        if($v->status_penunjang == DocoConstants::DISETUJUI){
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                'text' => self::ERR_GAGAL_BATAL
                            ]);
                        } else {
                            $tmpPenunjangRs[] = $v->pasienkirimkeunitlain_id;
                            $isTagihan = true;
                        }
                    }

                    if(!empty($tmpPenunjangRs)) {
                        $stringIdsRs = implode(',', $tmpPenunjangRs);
                        $statusRs = DocoConstants::BTL_APPROVE;

                        Yii::$app->db->createCommand("
                            UPDATE pasienkirimkeunitlain_t SET status_penunjang = {$statusRs} WHERE pasienkirimkeunitlain_id IN ({$stringIdsRs})
                        ")->execute();
                    }
                }

                if($isTagihan) {
                    //** Integrasi kasir */ 
                    $tagihanTindakan = (new KasirService)->batalTindakan([
                        self::NO_PENDAFTARAN => $model->no_pendaftaran,
                        self::RUANGAN_ID => $model->ruangan_id,
                    ]);
    
                    if(isset($tagihanTindakan['meta']) && $tagihanTindakan['meta']['code'] >= 400){
                        $transaction->rollBack();
                        $e = isset($tagihanTindakan['message']) ? $tagihanTindakan['message'] : 'Terjadi Kesalahan API';
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => $e
                        ]);
                    }
                }

                $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                $petugas_id = $jwt->loginpemakai_id;
                $petugas_tgl_pembuat = date('Y-m-d H:i:s');

                $model->petugas_id = $petugas_id;
                $model->petugas_tgl_pembuat = $petugas_tgl_pembuat;
                    
                if ($model->save()) {
                    // delete SEP if cara bayar bpjs
                    $delSep = $this->deleteSep($model->bpjs_id);

                    $transaction->commit();

                    switch($payload->jenis){
                        case self::IGD:
                            $route = 'app/batal-pendaftaran-igd';
                            break;
                        case self::PENUNJANG:
                            $route = 'app/batal-pendaftaran-penunjang';
                            break;
                        default:
                            $route = 'app/batal-pendaftaran-rajal';
                            break;
                    }

                    $params['route'] = $route;
                    $params['data'] = $this->syncUpdatePendaftaran($model->pendaftaran_id);
                    (new PendaftaranService)->syncPendaftaranSty($params);  

                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                        'text' => 'Kunjungan berhasil dibatalkan.',
                        'additional' => [
                            'pendaftaran_id' => $model->pendaftaran_id,
                        ],
                    ]);
                } else {
                    $transaction->rollBack();
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }
        }
        throw new \Exception("Data Tidak Di Temukan");
    }
}