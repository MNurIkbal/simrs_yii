<?php

namespace Integrasi\Service\Mhg;

use Yii;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Mhg\Models\RekapanBsl;
use yii\db\Query;

class BslCancelBill extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $billId = (int) $this->id;
        $detailTindakan = $this->detail_tindakan;
        $noPendaftaran = $this->no_pendaftaran;
        if (!empty($billId)) {
            /** Kondisi untuk hapus tindakan per no bayar */
            $qBill = (new Query())->select([
                'no_pembayaran'
            ])
            ->from('pembayaranpelayanan_t')
            ->andWhere([
                'pembayaranpelayanan_id' => $billId
            ])->one();
            $billNo = isset($qBill['no_pembayaran']) ? $qBill['no_pembayaran'] : null;
            if (!empty($billNo)) {
                $qRekap = RekapanBsl::find()->andWhere([
                    'no_pembayaran' => $billNo
                ])->asArray()->all();
                foreach ($qRekap as $value) {
                    $this->setUpdate($value);
                }
            }
        } else if (!empty($detailTindakan) && is_array($detailTindakan)) {
            /** Kondisi untuk hapus tindakan pe id tindakanpelayanan */
            $tindPel = isset($detailTindakan['tindakanpelayanan_id']) 
                                ? $detailTindakan['tindakanpelayanan_id'] : null;
            if (!empty($tindPel)) {
                $qRekap = RekapanBsl::find()->andWhere([
                    'tindakanpelayanan_id' => $tindPel
                ])->orderBy([
                    'id' => SORT_DESC
                ])->asArray()->one();
                if (!empty($qRekap)) {
                    $this->setUpdate($qRekap);
                }
            }
        } else if (!empty($noPendaftaran)) {
            $qPdftrn = (new Query())->select([
                'pendaftaran_id'
            ])
            ->from('pendaftaran_t')
            ->andWhere([
                'no_pendaftaran' => $noPendaftaran
            ])->one();
            
            $idPendaftaran = !empty($qPdftrn) ? $qPdftrn['pendaftaran_id'] : null;
            if (!empty($idPendaftaran)) {
                $qRekap = RekapanBsl::find()->andWhere([
                    'pendaftaran_id' => $idPendaftaran
                ])->asArray()->all();
                foreach ($qRekap as $value) {
                    $this->setUpdate($value);
                }
            }
        }

        return json_encode([
            'service' => 'Mhg-BslCancelBill',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function setUpdate($value)
    {
        $isSent = $value['is_sent'];
        $idRekap = $value['id'];
        $payload = json_decode($value['payload'], true);
        if ($isSent) {
            $payload['IsCanceled'] = true;
            $insert[] = [
                'pendaftaran_id' => $value['pendaftaran_id'],
                'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                'daftartindakan_id' => $value['daftartindakan_id'],
                'tipepaket_id' => $value['tipepaket_id'],
                'no_pembayaran' => $value['no_pembayaran'],
                'payload' => json_encode($payload),
            ];
            RekapanBsl::batchInsert($insert);
        } else {
            Yii::$app->db->createCommand("
                UPDATE rekapanbsl_r SET is_deleted = true WHERE id = $idRekap
            ")->execute();
        }
    }
}