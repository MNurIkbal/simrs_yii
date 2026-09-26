<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\{
    Models\ObatAlkesPasien,
    Cache\Cache
};

class UpdateHargaObat extends \Integrasi\Contracts\DocoImplement
{
    const RACIKAN = 1;
    const NON_RACIKAN = 2;

    public function execute()
    {
        $uid = $this->user_id;
        $penjaminId = $this->penjamin_id;
        $pendaftaran_id = $this->pendaftaran_id;
        $kelasId = $this->kelaspelayanan_id;
        $getPenjamin = Cache::getPenjaminCaraBayar($penjaminId);
        $caraBayar = ArrayHelper::getValue($getPenjamin, 'carabayar_id');

        $qModel = Yii::$app->db->createCommand("
                SELECT obatalkespasien_t.*, obatalkes_m.obatalkes_nama 
                FROM obatalkespasien_t 
                JOIN obatalkes_m ON obatalkes_m.obatalkes_id = obatalkespasien_t.obatalkes_id 
                WHERE obatalkespasien_t.pendaftaran_id = {$pendaftaran_id} AND obatsudahbayar_id IS NULL AND obatalkespasien_t.is_deleted = FALSE
            ")->queryAll();

        // $qModel = ObatAlkesPasien::find()->andWhere([
        //     'pendaftaran_id' => $pendaftaran_id,
        //     'obatsudahbayar_id' => null
        // ])->asArray()->all();
        
        $countRacikan = 0;
        if(!empty($qModel)) {
            $tmpVal = [];
            foreach ($qModel as $value) {
                $racikanId = ArrayHelper::getValue($value, 'racikan_id', self::NON_RACIKAN);
                if($racikanId == self::RACIKAN) {
                    $countRacikan++;
                }
            }
            
            $countObat = count($qModel);
            foreach ($qModel as $key => $value) {
                $obatAlkesPasienId = ArrayHelper::getValue($value, 'obatalkespasien_id');
                $kelasPelayananId = ArrayHelper::getValue($value, 'kelaspelayanan_id');
                $obatAlkesId = ArrayHelper::getValue($value, 'obatalkes_id');
                $racikanId = ArrayHelper::getValue($value, 'racikan_id', self::NON_RACIKAN);
                $qty = ArrayHelper::getValue($value, 'qty_oa', 1);
                $det = ArrayHelper::getValue($value, 'det');
                $baseprice = ArrayHelper::getValue($value, 'baseprice');
                $obatAlkesNama = ArrayHelper::getValue($value, 'obatalkes_nama');
                if(!empty($det)){
                    $qty = $det;
                }
                $isDitagihkan = !empty($value['hargajual_oa']) ? true : false;
                
                if (empty($obatAlkesId) /* && $obatAlkesId */) continue;
                
                if (!isset($tmpVal[$obatAlkesId])) {
                    $tmpVal[$obatAlkesId] = $this->getTarifObat((int) $penjaminId, (int) $kelasPelayananId, $obatAlkesId);
                }
                
                $rowValue = $tmpVal[$obatAlkesId];
                
                if (!empty($rowValue['jml_hargajual'])) {
                    $persenMargin = ArrayHelper::getValue($rowValue, 'persen_margin', 0);
                    $margin = $baseprice * $persenMargin/100;
                    $persenPpn = ArrayHelper::getValue($rowValue, 'persen_ppn', 0);
                    $ppn = ($baseprice + $margin) * $persenPpn/100;
                    $persenDiscount = ArrayHelper::getValue($rowValue, 'persen_disc', 0);
                    $discount = $baseprice * $persenDiscount/100;
                    $embalaseRacikan = ArrayHelper::getValue($rowValue, 'embalase_racikan', 0);
                    $embalaseNonRacikan = ArrayHelper::getValue($rowValue, 'embalase_nonracikan', 0);
                    $hargaJual = ceil($baseprice + $margin + $ppn - $discount); //harga jual dihitung ulang karena harus berdasarkan base price yang sudah ditransaksikan sebelumnya
                    $hargaEmbalase = ($racikanId == self::RACIKAN) ? $embalaseRacikan/$countRacikan : $embalaseNonRacikan/$qty;
                    $hargaAkhir = ceil($hargaJual + $hargaEmbalase);
                    $cond = [
                        'hargasatuan_oa' => $isDitagihkan ? $hargaAkhir : 0,
                        'hargajual_oa' => $isDitagihkan ? ceil($qty) * $hargaAkhir : 0,
                        'carabayar_id' => $caraBayar,
                        'penjamin_id' => $penjaminId,
                        'kelaspelayanan_id' => $kelasId,
                    ];
                    $this->updateObat($cond, $obatAlkesPasienId);
                }
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:'.$this->pendaftaran_id.':edit_pendaftaran',
                    'message' => json_encode([
                        'status' => 1,
                        'total' => $countObat,
                        'processed' => $key+1,
                        'tindakan' => $obatAlkesNama,
                        'totaltindakanobat' => $this->countTindakanObat,
                        'messageProcess' => 'Obat '.$obatAlkesNama.' berhasil di Update.',
                    ]),
                ]);
            }

            // update penjamin_id,carabayar_id,kelaspelayanan_id di penjualanresep_t
            $arrayCond = [
                'penjamin_id' => $penjaminId, 
                'carabayar_id' => $caraBayar,
                'kelaspelayanan_id' => $kelasPelayananId,
            ];
            Yii::$app->db->createCommand()
            ->update('penjualanresep_t', $arrayCond, ['pendaftaran_id' => $pendaftaran_id])
            ->execute();
        }

        return json_encode([
            'service' => 'Sirs-UpdateHargaObat',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function updateObat($cond = [], $primary)
    {
        $default = array_merge([
            'modified_count' => new \yii\db\Expression('COALESCE(modified_count,0) + 1'),
            'last_modified_date' => date('Y-m-d H:i:s', time()),
            'last_modified_by' => $this->user_id,
        ], $cond);

        Yii::$app->db->createCommand()
            ->update('obatalkespasien_t', $default, ['obatalkespasien_id' => $primary])
            ->execute();
    }

    protected function getTarifObat($penjaminId, $kelasId, $alkesId)
    {
        return Yii::$app->db->createCommand("
            SELECT 
                obatalkes_id, 
                jml_hargajual,
                jml_harganetto,
                persen_disc,
                persen_ppn,
                persen_margin,
                jml_margin,
                jml_discount,
                jml_ppn,
                embalase_racikan,
                embalase_nonracikan
            FROM infostokobatalkes_fn($penjaminId,$kelasId) 
            WHERE obatalkes_id = {$alkesId} 
        ")->queryOne();
    }

    private function secondsToTime($s)
    {
        $h = floor($s / 3600);
        $s -= $h * 3600;
        $m = floor($s / 60);
        $s -= $m * 60;
        return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $s);
    }
}