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
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\KonsulPoli;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\Antrian;

use app\modules\v1\payload\PasienBatalKunjungan;
use app\modules\v1\cache\Cache;
use SirsCore\businessLogic\AntrianPoliLogic;
use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;

class BatalPendaftaranProcess extends \Doco\components\DocoBaseProcessExtension
{
    const PENDAFTARAN_ID = 'pendaftaran_id';
    const RUANGAN_ID = 'ruangan_id';
    const PENJAMIN_ID = 'penjamin_id';
    const NO_PENDAFTARAN = 'no_pendaftaran';
    const ERR_GAGAL_BATAL = 'Pendaftaran tidak bisa dibatalkan.';

    protected function findPasienPasienMasukPenunjang()
    {
        return PasienMasukPenunjang::find();
    }

    protected function findPasienKeUnitLain()
    {
        return PasienKirimUnitLain::find();
    }

    protected function deleteSep($bpjs_id)
    {
        if (!$bpjs_id) return '';

        $model = Bpjs::findOne($bpjs_id);
        $model->is_deleted = true;
        $model->norujukan = '-';
        $sep = $model->nosep;
        if($model->validate() && $model->save()) {
            $result = $model->deleteSepNew($sep);
            return $result;
        }
        else {
            return [
                'data' => $model->errors,
                'status' => 422
            ];
        }
    }

    protected function updateStatusPeriksaMcu($no_pendaftaran) {
        $pendaftaran = Pendaftaran::find()->andWhere([
            self::NO_PENDAFTARAN => $no_pendaftaran
        ])->one();

        $instalasi_mcu_id = LookupTransaksi::find()->where("kode_transaksi = 'MCU'")->one()->kode_id;

        if($pendaftaran->instalasi_id == $instalasi_mcu_id) {
            $getKonsul = (new \yii\db\Query)
                ->select(['konsulpoli_t.konsulpoli_id', 'pendaftaran_t.pendaftaran_id'])
                ->from('pendaftaran_t')
                ->innerJoin('konsulpoli_t', 'konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where('pendaftaran_t.pendaftaran_id = '.$pendaftaran->pendaftaran_id)
                ->andWhere('pendaftaran_t.instalasi_id = '.$instalasi_mcu_id)
                ->andWhere('konsulpoli_t.is_active = true')
                ->andWhere('konsulpoli_t.is_deleted = false')
                ->all();

            if(!empty($getKonsul)) {
                $statusBatalKonsulpoli = DocoConstants::STATUS_BATAL_PERIKSA;

                $konsulpoli_id = [];
                foreach ($getKonsul as $key => $value) {
                    $konsulpoli_id[] = $value['konsulpoli_id'];
                }

                $konsulpoli_id = implode(',', $konsulpoli_id);

                // update status
                $updateExec = KonsulPoli::updateAll([
                    'status_periksa' => $statusBatalKonsulpoli
                ], "konsulpoli_id IN({$konsulpoli_id}) AND is_active = true AND is_deleted = false");
            }

            $getPenunjang = (new \yii\db\Query)
                ->select(['pasienmasukpenunjang_t.pasienmasukpenunjang_id', 'pendaftaran_t.pendaftaran_id'])
                ->from('pendaftaran_t')
                ->innerJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where('pendaftaran_t.pendaftaran_id = '.$pendaftaran->pendaftaran_id)
                ->andWhere('pendaftaran_t.instalasi_id = '.$instalasi_mcu_id)
                ->all();

            if(!empty($getPenunjang)) {
                $statusBatalPenunjang = DocoConstants::ST_P_PEN_BTL;

                $pasienpenunjang_id = [];
                foreach ($getPenunjang as $key => $value) {
                    $pasienpenunjang_id[] = $value['pasienmasukpenunjang_id'];
                }

                $pasienpenunjang_id = implode(',', $pasienpenunjang_id);

                // update status radiologi & lab
                $updateExecPenunjang = PasienMasukPenunjang::updateAll([
                    'status_periksa' => $statusBatalPenunjang
                ], "pasienmasukpenunjang_id IN({$pasienpenunjang_id})");
            }
        }
    }

  private function deleteAntrian($pendaftaran_id)
  {
      $antrian = Antrian::find()->where(['and', ['pendaftaran_id' => $pendaftaran_id, 'jenisantrian_id' => DocoConstants::VAR_JA_P, 'is_konsulpoli' => FALSE]])->one();
      if (!empty($antrian)) {
          $antrian->no_antrian = null;
          $antrian->status_antrian = DocoConstants::VAR_SA_B;
          $antrian->is_deleted = true;
          $antrian->is_active = false;
          $antrian->deleted_date = date('Y-m-d h:i:s');
          $antrian->save(false);
      }
  }

	protected function processFlow()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $isTagihan = false;
        $isPenunjang = false;
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
        $countPembayaran = TindakanPelayanan::find()
                ->where([self::PENDAFTARAN_ID => $model->pendaftaran_id])
                ->andWhere(['is not', 'tindakansudahbayar_id', null])
                ->count();
        if($countPembayaran != 0) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Pasien sudah melakukan pembayaran.'
            ]);
        }

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
                $countTagihan = TindakanPelayanan::find()
                ->where([self::PENDAFTARAN_ID => $registId])
                ->andWhere(['is_deleted' => false])
                ->count();

                if($countTagihan != 0) {
                    $isTagihan = true;
                }

                //** Case APS Asuransi */
                $pasienAps = $this->findPasienPasienMasukPenunjang()
                ->where([self::PENDAFTARAN_ID => $registId])
                ->all();

                $tindakanPenunjang = TindakanPelayanan::find()
                ->where([self::PENDAFTARAN_ID => $registId])->asArray()->one();


                if(!empty($pasienAps) && $groupCaraBayar != DocoConstants::GROUP_UMUM) {
                    $stringIds = '';
                    $tmpPenunjang = [];
                    $bolehBatal = [
                        DocoConstants::ST_P_PEN_BLM_PRKS,
                        DocoConstants::ST_P_PEN_BTL,
                    ];

                    foreach($pasienAps as $k => $v){
                        // if($v->status_periksa != DocoConstants::ST_P_PEN_BLM_PRKS) {
                        if (!in_array($v->status_periksa, $bolehBatal)) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                'text' => self::ERR_GAGAL_BATAL
                            ]);
                        } else {
                            $tmpPenunjang[] = $v->pasienmasukpenunjang_id;
                            if($tindakanPenunjang!=null){
                                $isPenunjang = true;
                            }
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
                            if($tindakanPenunjang!=null){
                                $isPenunjang = true;
                            }
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

                if($isPenunjang){
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => 'Masih terdapat tindakan / order penunjang yang belum di batalkan!'
                    ]);
                }

                $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                $petugas_id = $jwt->loginpemakai_id;
                $petugas_tgl_pembuat = date('Y-m-d H:i:s');

                $model->petugas_id = $petugas_id;
                $model->petugas_tgl_pembuat = $petugas_tgl_pembuat;

                if ($model->save()) {
                    // delete SEP if cara bayar bpjs
                    $delSep = $this->deleteSep($model->bpjs_id);

                    /**
                    * Delete Antrian Ketika konfig menyala,
                    * ketika didelete maka kuota dokter akan diretur
                    **/
                    $konfig_batal_antrian = LookupTransaksi::find()->where(['kode_transaksi' => 'batal_antrian'])->one();
                    if (ArrayHelper::getValue($konfig_batal_antrian, 'additional_value', false) == TRUE && strtolower(ArrayHelper::getValue($konfig_batal_antrian, 'additional_value', false)) == 'true') {
                        $delAntrian = $this->deleteAntrian($model->pendaftaran_id);
                    }

                    // Update status periksa konsulpoli MCU
                    $update_mcu = $this->updateStatusPeriksaMcu($model->no_pendaftaran);

                    AntrianPoliLogic::validateBatalKunjungan($model->pendaftaran_id);

                    $transaction->commit();

                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                        'text' => 'Kunjungan berhasil dibatalkan.',
                        'pendaftaran_id' => $model->pendaftaran_id,
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
