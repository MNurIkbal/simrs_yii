<?php

/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

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
use app\modules\v1\models\LoginPemakai; 

use app\modules\v1\payload\PasienBatalKunjungan;
use app\modules\v1\cache\Cache;

use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;

class BatalPendaftaranRanapProcess extends \Doco\components\DocoBaseProcessExtension
{
    const PENDAFTARAN_ID = 'pendaftaran_id';
    const RUANGAN_ID = 'ruangan_id';
    const PENJAMIN_ID = 'penjamin_id';
    const NO_PENDAFTARAN = 'no_pendaftaran';
    const ERR_GAGAL_BATAL = 'Pendaftaran tidak bisa dibatalkan.';

    protected function findPasienKeUnitLain()
    {
        return PasienKirimUnitLain::find();
    }

    protected function findAdmisi()
    {
        return PasienAdmisi::find();
    }

    protected function deleteSep($bpjs_id)
    {
        if (!$bpjs_id) return '';

        $model = Bpjs::findOne($bpjs_id);
        if(!empty($model)){
            $model->is_deleted = true;
            $model->norujukan = '-';
            $sep = $model->nosep;
            if($model->validate() && $model->save()) {
                $result = $model->deleteSepNew($sep);
                return $result;
            }else{
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        }
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
        $countPembayaran = TindakanPelayanan::find()
                ->where([self::PENDAFTARAN_ID => $model->pendaftaran_id])
                ->andWhere(['is not', 'tindakansudahbayar_id', null])
                ->count();
        if($countPembayaran != 0) {
            return [
                'data'   => 'Pasien sudah melakukan pembayaran.',
                'status' => 500
            ];
        }

        $pasienAdmisi = $this->findAdmisi()
        ->where(['pendaftaran_id' => $model->pendaftaran_id])
        ->one();

        $connection  = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if ($model && $pasienAdmisi) {
                //** Cek tagihan */
                $countTagihan = TindakanPelayanan::find()
                ->where([
                    self::PENDAFTARAN_ID => $model->pendaftaran_id,
                    self::RUANGAN_ID => $pasienAdmisi->ruangan_id
                    ])
                ->count();

                if($countTagihan != 0) {
                    $isTagihan = true;
                }

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
                    if($model->carabayar_id == DocoConstants::VAR_ID_CARABAYAR_BPJS){
                        $delSep = $this->deleteSep($model->bpjs_id);
                    }
                    
                    $transaction->commit();

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
