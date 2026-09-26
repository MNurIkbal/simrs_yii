<?php

namespace Doco\processes;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Shift;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoClosingKasirView;
use app\modules\v1\models\InfoClosingKasirDetailView;
use app\modules\v1\models\InfoClosingKasirHeaderView;
use Doco\components\DocoHelpers;

class CetakClosingKasirProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $id;
    protected $nama_ruangan;

    public function cetak($id, $nama_ruangan){
        $modelHeader = $this->actionHeader($id);
        $modelRincian = $this->actionRincianClosing($id);
        
        $print = new DocoPrint();
        $tgl_closingkasir = date('d-M-Y H:i:s', strtotime($modelHeader['tgl_closingkasir']));
        $print->attributes = [
            '#no_closingkasir#' => $modelHeader['no_closingkasir'],
            '#tgl_closingkasir#' => $tgl_closingkasir,
            '#now#' => date('d-M-Y'),
            '#nama_pegawai#' => $modelHeader['nama_pegawai'],
            '#shift_nama#' => $modelHeader['shift_nama'],
            '#saldo_awal#' => DocoHelpers::rupiahDisplay($modelHeader['closing_saldoawal']),
            '#tunai#' => DocoHelpers::rupiahDisplay($modelHeader['terima_uangpelayanan']),
            '#saldo_akhir#' => DocoHelpers::rupiahDisplay($modelHeader['closing_saldoawal'] + $modelHeader['terima_uangpelayanan']),
            '#table#' => Yii::$app->controller->renderPartial('index', [
                'modelRincian' => $modelRincian,
                'nama_ruangan' => $nama_ruangan,
            ]),
        ];

        $print->Output();
    }

    public function actionRincianClosing($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);
        return $query->asArray()->all();
    }

    public function actionHeader($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirHeaderView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);
        return $query->asArray()->one();
    }
   
    protected function processFlow()
        {
            $request = $this->_requestData;
            $id = $request->get('id', null);
            $nama_ruangan = $request->get('nama_ruangan', null);
            $this->cetak($id, $nama_ruangan);
        }
}