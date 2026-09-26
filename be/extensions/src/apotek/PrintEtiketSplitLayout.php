<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\apotek;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\WorklistDetailView;
use app\modules\v1\models\WorklistView;

class PrintEtiketSplitLayout extends \Doco\processes\PrintEtiketProcess
{
    protected function cetakEtiketSplitLayout()
    {
        try {
            $request = Yii::$app->request;
            $identifier = $request->get('identifier');
            $is_oral = $request->get('is_oral');

            $dataPrint = self::getDataOralAndNonOral($identifier, $is_oral);

            $print = new DocoPrint();
            $print->attributes = [
                '#data#' => Yii::$app->controller->renderPartial('etiket-split-layout', [
                    'data' => $dataPrint
                ]),
            ];
            $print->Output();
        } catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getDataOralAndNonOral($identifier, $is_oral)
    {
        $header = WorklistView::find()
            ->where(['no_reseptur' => $identifier])
            ->orWhere(['no_resep' => $identifier]);
        $dataHeader = $header->asArray()->one();

        $detail = WorklistDetailView::find()
            ->where(['no_reseptur' => $identifier])
            ->orWhere(['no_resep' => $identifier])
            ->andwhere(['detail_is_deleted' => false]);
        $data = $detail->asArray()->all();

        $arrRacikan = [];
        $arrNonRacikan = [];
        foreach ($data as $key => $value) {
            if ($value['racikan_id'] == DocoConstants::ID_RACIKAN) {
                $arrRacikan[$value['rke']][] = $value;
            } else {
                $arrNonRacikan[] = [
                    'nama_pasien' => substr(strtoupper($dataHeader['nama_pasien']), 0, 20),
                    'dokter' => !empty($dataHeader['dokter']) ? strtoupper($dataHeader['dokter']) : "-",
                    'tanggal_lahir' => ($dataHeader['tanggal_lahir'] != NULL) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-',
                    'no_resep' => $value['no_resep'],
                    'no_reseptur' => $value['no_reseptur'],
                    'nama_obat' => substr(strtoupper($value['nama_obat']), 0, 50),
                    'komponen_obat' => [],
                    'signa' => strtoupper($value['signa']),
                    'qty_obat' => $value['det_medis'],
                    'is_oral' => $value['is_oral'],
                    'satuan_input' => $value['satuan_input'],
                    'catatan' => !empty($value['etiket']) ? strtoupper($value['etiket']) : "-",
                    'tgl_cetak' => date('d M Y', strtotime(date('Y-m-d'))),
                    'no_rm' => $dataHeader['no_rm'],
                    'gender' => isset($dataHeader['jeniskelamin']) ? $dataHeader['jeniskelamin'] : '-'
                ];
            }
        }

        $resultRacikan = [];
        foreach ($arrRacikan as $key => $value) {
            $isOral = true;
            $temp_id = "";
            $komponen_obat = [];
            foreach ($value as $k => $v) {
                if ($v['is_oral'] == false) {
                    $isOral = false;
                }

                $dataObat = [
                    'nama_obat' => substr($v['nama_obat'], 0, 20),
                    'qty_obat' => $v['det_medis'],
                    'satuan_input' => $v['satuan_input']
                ];

                $identifier = $v['racikan_id'] . '-' . $v['rke'];
                $no_reseptur = $v['no_reseptur'];
                $no_resep = $v['no_resep'];
                $etiket = $v['etiket'];
                $signa = $v['signa'];
                $qty_obat = $v['qty_obat'];
                $satuan_input = $v['satuan_input'];
                $catatan = $v['etiket'];
                $rke = $v['rke'];
                $komponen_obat[] = $dataObat;
                $nama_racikan = isset($v['nama_racikan']) ? substr($v['nama_racikan'], 0, 25) : '-';
                $qty_racikan = isset($v['qty_racikan']) ? $v['qty_racikan'] : '-';
                $temp_id = $identifier;
            }

            $content = [
                'nama_pasien' => substr(strtoupper($dataHeader['nama_pasien']), 0, 20),
                'dokter' => !empty($dataHeader['dokter']) ? strtoupper($dataHeader['dokter']) : "-",
                'tanggal_lahir' => ($dataHeader['tanggal_lahir'] != NULL) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-',
                'no_reseptur' => $no_reseptur,
                'no_resep' => $no_resep,
                'nama_obat' => Yii::t('app', 'Racikan Ke') . ' ' . $rke,
                'komponen_obat' => $komponen_obat,
                'signa' => strtoupper($signa),
                'qty_obat' => '',
                'is_oral' => $isOral,
                'satuan_input' => $satuan_input,
                'catatan' => !empty($catatan) ? strtoupper($catatan) : "-",
                'tgl_cetak' => date('d M Y', strtotime(date('Y-m-d'))),
                'no_rm' => $dataHeader['no_rm'],
                'nama_racikan' => strtoupper($nama_racikan),
                'qty_racikan' => $qty_racikan,
                'type' => 'header',
                'gender' => isset($dataHeader['jeniskelamin']) ? $dataHeader['jeniskelamin'] : '-'
            ];

            $resultRacikan[] = $content;

            $components = array_chunk($komponen_obat, 5, true);
            $iter = 1;

            foreach ($components as $compKey => $compVal) {
                $content['komponen_obat'] = array_values($compVal);
                $content['type'] = 'content';
                $content['length'] = count($components);
                $content['segment'] = $iter;

                $iter++;
                $resultRacikan[] = $content;
            }
        }

        $arrResult = array_merge($arrNonRacikan, $resultRacikan);
        $dataPrint = [];
        foreach ($arrResult as $key => $value) {
            if ($value['is_oral'] == $is_oral) {
                $dataPrint[] = $value;
            }
        }
        return $dataPrint;
    }

    protected function processFlow()
    {
        $this->cetakEtiketSplitLayout();
    }
}
