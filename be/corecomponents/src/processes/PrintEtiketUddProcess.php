<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use Doco\components\DocoPrint;
use app\modules\v1\models\UnitDoseDispensingDetail;
use app\modules\v1\models\InfoUDDDosisView;
use app\modules\v1\models\InfoUDDView;
use yii\helpers\ArrayHelper;

class PrintEtiketUddProcess extends \Doco\processes\PrintEtiketProcess {
    protected $kodeDoc = 'worklist-etiket';

    protected function getDataPasien() {
        $pasien = InfoUDDView::find()
            ->select([
                'no_udd',
                'nama_pasien',
                'no_rm',
                'dok_dpjp',
                'tanggal_lahir',
            ])->where(['udd_id' => $this->identifier])->one();

        $this->dataPasien = [
            'nama_pasien' => ArrayHelper::getValue($pasien, 'nama_pasien', '-'),
            'no_rm' => ArrayHelper::getValue($pasien, 'no_rm', '-'),
            'dokter' => ArrayHelper::getValue($pasien, 'dok_dpjp', '-'),
            'tanggal_lahir' => ($pasien['tanggal_lahir'] != NULL) ? date('d M Y', strtotime($pasien['tanggal_lahir'])) : '-',
            'no_transaksi' => ArrayHelper::getValue($pasien, 'no_udd', '-')
        ];
    }

    protected function getDetailResep() {
        $detail = UnitDoseDispensingDetail::find()
            ->select(['udd_detail_id'])
            ->where([
                'udd_id' => $this->identifier
            ])->asArray()->all();
        $udd_detail_ids = array_column($detail, 'udd_detail_id');

        $this->detailResep = InfoUDDDosisView::find()
            ->select([
                'obatalkes_nama as nama_obat', 
                'waktu_pemberian as signa',
                'dosis as qty_obat',
                'is_oral',
                'satuan_input',
                'keterangan as catatan',
            ])
            ->where(['in', 'udd_detail_id', $udd_detail_ids])
            ->andWhere(['is_oral' => $this->isOral])
            ->asArray()->all();
    }

    protected function setRenderView() {
        $this->print = new DocoPrint($this->kodeDoc);
        $this->renderView = $this->print->getRenderView('../worklist/etiket');
    }

    protected function processFlow() {
        $this->getParams();
        $this->validateParams();
        $this->getDataPasien();
        $this->getDetailResep();
        $this->setRenderView();
        return $this->cetak();
    }
}
