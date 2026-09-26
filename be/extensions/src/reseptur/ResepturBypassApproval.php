<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\reseptur;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;

use Doco\components\DocoConstants;

use Doco\models\Reseptur;
use Doco\models\ResepturDetail;
use Doco\models\ResepturRacikan;
use app\modules\v1\models\InfoStokObatAlkesAllRuanganFnr;
use Doco\models\InfoStokObatAlkesFnr;
use Doco\models\HargaObatAlkesFn;

class ResepturBypassApproval extends \Doco\processes\ResepturProcess
{
    protected function saveResep()
    {
        $model = new Reseptur;
        $model->attributes = $this->dataReseptur;
        $model->tglreseptur = date('Y-m-d H:i:s');
        $model->status_reseptur = DocoConstants::VAR_AR;
        $model->antrian_id = $this->antrianId;
        $model->created_date = date('Y-m-d H:i:s');
        $model->pasienadmisi_id = $this->_requestData->post('pasienadmisi_id', null);
        $model->instruksi_id = $this->_requestData->post('instruksi_id', null);

        if (!$model->save(false)) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Simpan Reseptur gagal.'
            ]);
        }

        $countRke = [];
        $totalRke = [];

        // untuk menghitung jumlah obat di masing-masing racikan
        $group_rke = ArrayHelper::index($this->resepturDetail, null, 'rke');
        foreach($group_rke as $rke => $obatRacikan) {
            if (is_array($obatRacikan)) {
                foreach ($obatRacikan as $totalQtys) {
                    $totalQty = ArrayHelper::getValue($totalQtys, 'qty_reseptur', 0);
                    if (empty($totalRke[$rke])) {
                        $totalRke[$rke] = ceil($totalQty);
                    } else {
                        $totalRke[$rke] += ceil($totalQty);
                    }
                }
            }

            $countRke[$rke] = count($obatRacikan);
        }

        $countRke = $totalRke;
        $listInfoObatR = $this->listInfoObatR();
        $listInfoObatR = ArrayHelper::index($listInfoObatR, 'obatalkes_id');
        
        $detail = [];
        foreach ($this->resepturDetail as $key => $value) {
            $row = $value;
            $infoObatR = isset($listInfoObatR[ArrayHelper::getValue($row, 'obatalkes_id')]) ? $listInfoObatR[ArrayHelper::getValue($row, 'obatalkes_id')] : [];
            
            $row['reseptur_id'] = $model->reseptur_id;
            $row['racikan_id'] = $this->listRacikan[$value['racikan_id']];
            // rizal
            $qty_reseptur = isset($value['qty_reseptur']) ? $value['qty_reseptur'] : 0;
            $qty_konversi = isset($value['qty_konversi']) ? $value['qty_konversi'] : 0;
            $qty_rounded = ceil($qty_reseptur);
            $nilai_konversi = $qty_konversi / $qty_reseptur;
            $hargaNetto = isset($value['harganetto_reseptur']) ? $value['harganetto_reseptur'] : 0;
            $hargaJual = $qty_rounded * $value['hargasatuan_reseptur'];

            $row['qty_reseptur'] = $qty_rounded;
            $row['harganetto_reseptur'] = $hargaNetto;
            $row['hargasatuan_reseptur'] = $this->calculateHargaObat($infoObatR, $value, $qty_rounded, $countRke);
            $row['hargajual_reseptur'] = $row['hargasatuan_reseptur'] * $qty_rounded;
            $row['created_date'] = date('Y-m-d H:i:s');
            $row['status_implementasi'] = 454;
            $row['rke'] = isset($row['rke']) && !empty($row['rke']) ? $row['rke'] : null;
            $row['r'] = ArrayHelper::getValue($row, 'r', null);
            $row['signa_id'] = isset($value['signa_id']) ? $value['signa_id'] : null;
            
            if(!empty($value['signa_id'])) {
                $signa_index = array_search($value['signa_id'], array_column($this->masterSigna, 'signa_id'));
                $signa_json = json_encode([
                    'id' => $value['signa_id'],
                    'text' => $this->masterSigna[$signa_index]['signa_nama'],
                    'kode' => $this->masterSigna[$signa_index]['signa_kode']
                ]);
            } else {
                $signa_json = json_encode([
                    'id' => null,
                    'text' => $value['signa'],
                    'kode' => null
                ]);
            }
            $row['signa'] = $signa_json;
            $row['tgl_resepturdetail'] = date('Y-m-d H:i:s');
            $row['qty_medis'] = $qty_reseptur;
            $row['qty_konversi'] = $qty_rounded * $nilai_konversi;
            $row['nama_racikan'] = ArrayHelper::getValue($value,'nama_racikan');
            $row['qty_racikan'] = ArrayHelper::getValue($value,'qty_racikan');
            $row['satuan_racikan_id'] = ArrayHelper::getValue($value,'satuan_racikan_id');
            $row['is_kronis'] = ArrayHelper::getValue($value, 'is_kronis', false);
            if(isset($row['is_kronis'])){
                if(is_string($row['is_kronis']) && strtolower($row['is_kronis']) == 'true'){
                    $row['is_kronis'] = true;
                }elseif(is_string($row['is_kronis']) && strtolower($row['is_kronis']) == 'false'){
                    $row['is_kronis'] = false;
                }
            }
            $row['etiket'] = ArrayHelper::getValue($value, 'etiket', NULL);
            $row['hari'] = empty($value['hari']) || $value['hari'] == '' ? null : $value['hari'];
            $detail[] = $row;
        }

        ResepturDetail::batchInsert($detail);
        $model->refresh();
        $this->dataReseptur['reseptur_id'] = $model->reseptur_id;
    }

    protected function listInfoObatR()
    {
        $obatalkesIds = ArrayHelper::getColumn($this->resepturDetail, 'obatalkes_id');
        
        /* deprecated
        return (new InfoStokObatAlkesAllRuanganFnr([
            'extParam' => [
                (string) ArrayHelper::getValue($this->dataPasien, 'penjamin_id'),
                (string) ArrayHelper::getValue($this->dataPasien, 'kelaspelayanan_id')
            ]
        ]))
        ->find()->where([
            'obatalkes_id' => $obatalkesIds
        ])->asArray()->all();

        return (new InfoStokObatAlkesFnr([
            'extParam' => [
                (string) ArrayHelper::getValue($this->dataPasien, 'penjamin_id'),
                (string) ArrayHelper::getValue($this->dataPasien, 'kelaspelayanan_id'),
                (string) ArrayHelper::getValue($this->dataReseptur, 'ruangan_id')
            ]
        ]))
        ->find()->where([
            'obatalkes_id' => $obatalkesIds
        ])->asArray()->all();
         */

        return (new HargaObatAlkesFn([
            'extParam' => [
                (string) ArrayHelper::getValue($this->dataPasien, 'penjamin_id'),
                (string) ArrayHelper::getValue($this->dataPasien, 'kelaspelayanan_id')
            ]
        ]))
        ->find()->where([
            'obatalkes_id' => $obatalkesIds
        ])->asArray()->all();
    }

    protected function calculateHargaObat($infoObat, $detail, $qty_rounded, $countRke) {
        $hargasatuan = 0;
        
        /**
         * Rumus harga obat dirubah ke Function.
         */
        // $harganetto = ArrayHelper::getValue($infoObat,'harganetto');
        // $nett_p_margin = $harganetto + (($infoObat['margin'] * $harganetto)/100); // harga netto - margin
        // $nett_m_disc = $nett_p_margin - (($infoObat['disc'] * $nett_p_margin)/100); // harga netto setelah margin - diskon
        // $nett_p_ppn = ceil($nett_m_disc + (($infoObat['ppn'] * $nett_m_disc)/100)); // harga netto setelah diskon + ppn

        $nett_p_ppn = isset($infoObat['hargaygdipakai']) ? ceil($infoObat['hargaygdipakai']) : 0;
        if($detail['racikan_id'] == "OR") { // obat racikan
            $hargasatuan = ceil($nett_p_ppn + ($infoObat['embalase_racikan'] / $countRke[$detail['rke']]));
        } else { // obat non-racikan
            $hargasatuan = ceil($nett_p_ppn + ($infoObat['embalase_nonracikan'] / $qty_rounded));
        }

        return $hargasatuan;
    }

    protected function processFlow()
    {
        $this->validation();
        $this->populateData();

        $this->startDBTransaction();
        $this->createAntrian();
        $this->saveResep();
        $this->insertRacikanFreetext();
        $this->commitDBTransaction();

        $this->getReseptur($this->dataReseptur['reseptur_id']);
        $this->afterSave();

        return [
            'message' => 'Data Berhasil di simpan',
            'data' => [
                'reseptur_id' => $this->dataReseptur['reseptur_id']
            ]
        ];
    }
}