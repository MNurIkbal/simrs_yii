<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\exceptions\ValidationException;
use app\modules\v1\models\WorklistDetailView;
use app\modules\v1\models\WorklistView;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\PenjualanResep;
use yii\helpers\ArrayHelper;
use Doco\Repositories\KonfigRepositories;
use Doco\components\DocoBaconQrCode;

class PrintEtiketProcess extends \Doco\components\DocoBaseProcessExtension
{  
    protected $renderView;
    protected $identifier;
    protected $isOral;
    protected $print;
    protected $detailResep = [];
    protected $dataPasien = [];
    protected $konfigEtiket;
    protected $renderQrCode;
    protected $styleQrCode = [
        'style_height' => '30px',
        'style_width' => '30px'
    ];

    protected function getParams() {
        $request = Yii::$app->request;
        $this->identifier = $request->get('identifier', null);
        $this->isOral = $request->get('is_oral', false);
    }

    protected function validateParams() {
        if(empty($this->identifier)) {
            throw new ValidationException(422, $this->_error, ['text' => 'identifier tidak boleh kosong']);
        }

        if(is_null($this->isOral)) {
            throw new ValidationException(422, $this->_error, ['text' => 'is_oral tidak boleh kosong']);
        }
    }

    protected function getKonfigEtiket() {
        $getKonfig = KonfigRepositories::getKonfigFarmasi();
        $this->konfigEtiket = $getKonfig['layout_etiket'];
    }

    private function getDataPasien() {
        $header = WorklistView::find()
            ->where(['no_reseptur' => $this->identifier])
            ->orWhere(['no_resep' => $this->identifier]);
        $dataHeader = $header->asArray()->one();

        $this->dataPasien = [
            'nama_pasien' => ArrayHelper::getValue($dataHeader, 'nama_pasien'),
            'no_rm' => ArrayHelper::getValue($dataHeader, 'no_rm', '-'),
            'dokter' => ArrayHelper::getValue($dataHeader, 'dokter', '-'),
            'tanggal_lahir' => ($dataHeader['tanggal_lahir'] != NULL) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-',
            'no_transaksi' => $this->identifier,
            'tgl_cetak' => date('d M Y', strtotime(date('Y-m-d'))),
            'gender' => isset($dataHeader['jeniskelamin']) ? $dataHeader['jeniskelamin'] : '-'
        ];
    }

    private function getDetailResep() {
        $detail = WorklistDetailView::find()
            ->where(['no_reseptur' => $this->identifier])
            ->orWhere(['no_resep' => $this->identifier])
            ->andwhere(['detail_is_deleted' => false]);
        $data = $detail->asArray()->all();

        $arrRacikan = [];
        $arrNonRacikan = [];
        foreach ($data as $value) {
            if ($value['racikan_id'] == DocoConstants::ID_RACIKAN) {
                $arrRacikan[$value['rke']][] = $value;
            } else {
                $arrNonRacikan[] = [
                    'nama_obat' => strtoupper(ArrayHelper::getValue($value, 'nama_obat')),
                    'komponen_obat' => [],
                    'signa' => strtoupper(ArrayHelper::getValue($value, 'signa')),
                    'qty_obat' => isset($value['det_medis']) ? $value['det_medis'] : $value['qty_obat'],
                    'is_oral' => ArrayHelper::getValue($value, 'is_oral'),
                    'satuan_input' => ArrayHelper::getValue($value, 'satuan_input'),
                    'catatan' => !empty($value['etiket']) ? strtoupper($value['etiket']) : "-",
                    'expired_date' => "Exp. Date : ",
                ];
            }
        }

        $resultRacikan = [];
        foreach ($arrRacikan as $value) {
            $isOral = true;
            $komponen_obat = $komponen_racikan = [];
            foreach ($value as $v) {
                if ($v['is_oral'] == false) {
                    $isOral = false;
                }
                
                $dataObat = [
                    'nama_obat' => strtoupper(ArrayHelper::getValue($v, 'nama_obat')),
                    'qty_obat' => ArrayHelper::getValue($v, 'det_medis'),
                    'satuan_input' => ArrayHelper::getValue($v, 'satuan_input')
                ];

                $signa = $v['signa'];
                $satuan_racikan = isset($v['satuan_racikan']) ? $v['satuan_racikan'] : NULL;
                $catatan = $v['etiket'];
                $rke = $v['rke'];
                $komponen_obat[] = $dataObat;
                $komponen_racikan[] = substr(ArrayHelper::getValue($v, 'nama_obat'), 0, 8);
                $nama_racikan = isset($v['nama_racikan']) ? substr($v['nama_racikan'], 0, 25) : '-';
                $qty_racikan = isset($v['qty_racikan']) ? $v['qty_racikan'] : '-';
            }

            $content = [
                'nama_obat' => Yii::t('app', 'Racikan Ke') . ' ' . $rke,
                'komponen_obat' => $komponen_obat,
                'komponen_racikan' => $komponen_racikan,
                'signa' => strtoupper($signa),
                'qty_obat' => '',
                'is_oral' => $isOral,
                'satuan_input' => '',
                'satuan_racikan' => $satuan_racikan,
                'catatan' => !empty($catatan) ? strtoupper($catatan) : "-",
                'nama_racikan' => strtoupper($nama_racikan),
                'qty_racikan' => $qty_racikan,
                'type' => 'header',
                'expired_date' => "Exp. Date : ",
            ];

            $resultRacikan[] = $content;
            $iter = 1;
            
            if($this->konfigEtiket['template_racikan'] == DocoConstants::MULTI_PAGE) {
                $components = array_chunk($komponen_obat, 5, true);
                foreach ($components as $compVal) {
                    $content['komponen_obat'] = array_values($compVal);
                    $content['type'] = 'content';
                    $content['length'] = count($components);
                    $content['segment'] = $iter;
                    $iter++;
                    $resultRacikan[] = $content;
                }
            }
        }

        $arrResult = array_merge($arrNonRacikan, $resultRacikan);
        $dataPrint = [];
        foreach ($arrResult as $value) {
            if ($value['is_oral'] == $this->isOral) {
                $dataPrint[] = $value;
            }
        }
        $this->detailResep = $dataPrint;
        return $this->detailResep;
    }

    protected function setLayout() {
        $this->print = new DocoPrint();
        $this->renderView = $this->konfigEtiket['layout'];
    }

    protected function updateStatusCetakEtiket() {
        $updReseptur = Reseptur::updateAll(
            ['is_cetak_etiket' => true], "noresep = '".$this->identifier."'"
        );

        $updPenjualanResep = PenjualanResep::updateAll(
            ['is_cetak_etiket' => true], "noresep = '".$this->identifier."'"
        );
    }

    protected function cetak() {
        $this->print->attributes = [
            '#data#' => Yii::$app->controller->renderPartial($this->renderView, [
                'pasien' => $this->dataPasien,
                'detail' => $this->detailResep,
                'renderQrCode' => DocoBaconQrCode::renderQrCode($this->identifier, $this->styleQrCode)
            ])
        ];
        return $this->print->Output();
    }

    protected function processFlow() {
        try {
            $this->getParams();
            $this->validateParams();
            $this->getKonfigEtiket();
            $this->getDataPasien();
            $this->getDetailResep();
            $this->setLayout();
            $this->updateStatusCetakEtiket();
            return $this->cetak();
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage());
        } catch (\Yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }
}
