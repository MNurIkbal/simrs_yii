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
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\TindakanPelayanan; 
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\LoginPemakai; 
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

class BatalPendaftaranRanapUcup extends \Doco\processes\BatalPendaftaranRanapProcess
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
        $request     = Yii::$app->request;
        $isTagihan = false;
        $post        = $request->post();
        $user        = LoginPemakai::find()->where(['nama_pemakai'=>$post['username']])->one();
        if (!$user) {
            return [
                'data'   => 'User tidak ditemukan.',
                'status' => 500
            ];
        }

        $check = $user['katakunci_pemakai'];
        $valid = Yii::$app->security->validatePassword($post['password'], $check);
        if (!$valid) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Password salah.'
            ]);
        }
        
        if($post['alasan_batal'] == ''){
                return [
                'data'   => 'Alasan batal belum diisi.',
                'status' => 500
            ];
        }

        $jwt = Yii::$app->jwt->user;

        $model = Pendaftaran::find()->andWhere([
            self::NO_PENDAFTARAN => $post[self::NO_PENDAFTARAN]
        ])->one();

        //Cek Pembayaran
        // $countPembayaran = TindakanPelayanan::find()
        //         ->where([self::PENDAFTARAN_ID => $model->pendaftaran_id])
        //         ->andWhere(['is not', 'tindakansudahbayar_id', null])
        //         ->count();
        // if($countPembayaran != 0) {
        //     return [
        //         'data'   => 'Pasien sudah melakukan pembayaran.',
        //         'status' => 500
        //     ];
        // }

        $pasienAdmisi = $this->findAdmisi()
        ->where(['pendaftaran_id' => $model->pendaftaran_id])
        ->one();

        $connection  = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if ($model && $pasienAdmisi) {
                //** Cek tagihan */
                // $countTagihan = TindakanPelayanan::find()
                // ->where([self::PENDAFTARAN_ID => $model->pendaftaran_id])
                // ->count();

                // if($countTagihan != 0) {
                //     $isTagihan = true;
                // }

                //** Case Pasien RS*/
                $pasienRs = $this->findPasienKeUnitLain()
                ->where(['pasienadmisi_id' => $pasienAdmisi->pasienadmisi_id])
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
                        self::RUANGAN_ID => $pasienAdmisi->ruangan_id,
                    ]);
    
                    if(isset($tagihanTindakan['meta']) && $tagihanTindakan['meta']['code'] >= 400){
                        $transaction->rollBack();
                        $e = isset($tagihanTindakan['message']) ? $tagihanTindakan['message'] : 'Terjadi Kesalahan API';
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                            'text' => $e
                        ]);
                    }
                }

                // Modified function, override with DB function
                $query = "SELECT * FROM fbatalranap(".$model->pendaftaran_id.", ".$jwt->loginpemakai_id.", '".$post['alasan_batal']."')";
                $res = $connection->createCommand($query)->queryAll();

                if ($res) {
                    $message = 'Kunjungan berhasil dibatalkan.';

                    // delete SEP if cara bayar bpjs
                    $delSep = $this->deleteSep($model->bpjs_id);
                    
                    $transaction->commit();

                    $params['route'] = 'app/batal-pendaftaran-ranap';
                    $params['data'] = $this->syncUpdatePendaftaran($model->pendaftaran_id);
                    (new PendaftaranService)->syncPendaftaranSty($params);  

                    return [
                        'message' => $message,
                    ];
                } else {
                    $transaction->rollBack();
                    return [
                        'data'   => 'Kunjungan gagal dibatalkan',
                        'status' => 500,
                        'return' => $model->errors
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            // $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}