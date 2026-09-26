<?php

namespace app\modules\v1\entities;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\modules\v1\models\WorklistDetailView;
use app\modules\v1\models\WorklistView;
use Doco\components\DocoPrint;
use Doco\Services\Cache;

class Etiket
{
    public function execute($controller)
    {
        try {
            $request = Yii::$app->request;
            $identifier = $request->get('identifier');
            $is_oral = $request->get('is_oral');
            $dataPrint = $this->getDataOralAndNonOral($identifier, $is_oral);
            $print = new DocoPrint();
            $print->attributes = [
                '#data#' => $controller->renderPartial('etiket-new', [
                    'data' => $dataPrint
                ]),
            ];
            $print->Output();

        } catch (\Yii\db\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
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
                $qty = $value['qty_obat'];
                if(!empty($value['det_medis'])){
                    $qty = $value['det_medis'];
                }else if(!empty($value['det'])){
                    $qty = $value['det'];
                }
                $arrNonRacikan[] = [
                    'no_rm' => !empty($dataHeader['no_rm']) ? $dataHeader['no_rm'] : '-',
                    'nama_pasien' => $dataHeader['nama_pasien'],
                    'dokter' => !empty($dataHeader['dokter']) ? $dataHeader['dokter'] : "-",
                    'tanggal_lahir' => ($dataHeader['tanggal_lahir'] != NULL) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-',
                    'no_resep' => $value['no_resep'],
                    'no_reseptur' => $value['no_reseptur'],
                    'nama_obat' => $value['nama_obat'],
                    'komponen_obat' => [],
                    'signa' => $value['signa'],
                    'qty_obat' => $qty,
                    'is_oral' => $value['is_oral'],
                    'satuan_input' => $value['satuan_input'],
                    'catatan' => !empty($value['etiket']) ? $value['etiket'] : "-"
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
                $identifier = $v['racikan_id'] . '-' . $v['rke'];
                $no_reseptur = $v['no_reseptur'];
                $no_resep = $v['no_resep'];
                $etiket = $v['etiket'];
                $signa = $v['signa'];
                $qty_obat = $v['qty_obat'];
                $qty_obat = $v['qty_obat'];
                $satuan_input = $v['satuan_input'];
                $catatan = $v['etiket'];
                $rke = $v['rke'];
                $komponen_obat[] = $v['nama_obat'];
                $temp_id = $identifier;
            }
            $resultRacikan[] = [
                'no_rm' => !empty($dataHeader['no_rm']) ? $dataHeader['no_rm'] : '-',
                'nama_pasien' => $dataHeader['nama_pasien'],
                'dokter' => !empty($dataHeader['dokter']) ? $dataHeader['dokter'] : "-",
                'tanggal_lahir' => ($dataHeader['tanggal_lahir'] != NULL) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-',
                'no_reseptur' => $no_reseptur,
                'no_resep' => $no_resep,
                'nama_obat' => Yii::t('app', 'Racikan Ke') . ' ' . $rke,
                'komponen_obat' => $komponen_obat,
                'signa' => $signa,
                'qty_obat' => '',
                'is_oral' => $isOral,
                'satuan_input' => '',
                'catatan' => !empty($catatan) ? $catatan : "-"
            ];
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
}
