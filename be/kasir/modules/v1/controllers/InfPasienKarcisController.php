<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoPasienKarcisView;
use app\modules\v1\models\CetakKwitansiBkm;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoConstansId;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;

class InfPasienKarcisController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienKarcisView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPasienKarcisView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $insMcu = DocoConstansId::actionGetId('MCU');
        $isInstalasiRj = DocoConstansId::actionGetId('RJ');
        // if($between) {
            $query->andWhere(['or',
                ['carabayar_id' => DocoConstants::VAR_UMUM],
                ['instalasi_id' => $insMcu],
            ]);
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            $query->andFilterWhere([
                'not',
                [
                    'and',
                    ['=', 'status_periksa', DocoConstants::STATUS_PERIKSA_BTL_PERIKSA],
                    ['=', 'instalasi_id', $isInstalasiRj]
                ],
            ]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }

    public function actionGetDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select pendaftaran_id, no_pendaftaran from infopasienkarcis_v where no_pendaftaran LIKE '%{$term}%' 
            and tgl_pendaftaran BETWEEN '{$start}' AND'{$end}' 
            group by no_pendaftaran, pendaftaran_id 
            order by no_pendaftaran asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    /**
    * @controller actionCetakBkm
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #total_terbayar# => total 
    * @attribute #keterangan# => keterangan pembayaran
    * @attribute #terbilang# => terbilang
    * @attribute #no_bkm# => no bkm
    * @attribute #kasir# => nama kasir
    * @attribute #instalasi_nama# => nama instalasi
    * @attribute #keterangan_tanggal# => keterangan tanggal
    * @attribute #no_pendaftaran# => no pendaftaran
    * @attribute #tanggal# => tanggal sekarang 
    **/
    public function actionCetakBkm($id)
    {
        // Get pemeriksaan fisik
        $model = new CetakKwitansiBkm;
        $id = 59;
        $query = $model::find()->andWhere(['pembayaranpelayanan_id' => $id])->one();
        $tgl_pulang = is_null($query->tglpulang_pendaftaran) ? '' : ' s/d '.$query->tglpulang_pendaftaran;
        $keterangan_tanggal = $query->tgl_pendaftaran.$tgl_pulang;
        $terbilang = self::Terbilang((int)$query->total_terbayar). 'Rupiah';
        $tanggal = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $tanggal_sekarang = date('d').' '.$tanggal['bulan'].' '.$tanggal['tahun'];
        
        if (!empty($model)) {
            // Print
            $print = new DocoPrint();

            // Assign attributes
            $print->attributes = [
                '#nama_pasien#' => $query->nama_pasien,
                '#total_terbayar#' => $query->total_terbayar,
                '#terbilang#' => $terbilang,
                '#no_bkm#' => $query->no_bkm,
                '#tanggal#' => $tanggal_sekarang,
                '#instalasi_nama#' => $query->instalasi_nama,
                '#keterangan_tanggal#' => $keterangan_tanggal,
                '#no_pendaftaran#' => $query->no_pendaftaran,
                '#kasir#' => $query->kasir, 
            ];

            // Print output
            $print->Output();
        }
    }

    /**
    * @controller actionCetakKwitansi
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #total_terbayar# => total 
    * @attribute #keterangan# => keterangan pembayaran
    * @attribute #terbilang# => terbilang
    * @attribute #no_kwitansi# => no kwitansi
    * @attribute #kasir# => nama kasir
    * @attribute #tanggal# => tanggal sekarang 
    **/
    public function actionCetakKwitansi($id)
    {
        // Get pemeriksaan fisik
        $model = new CetakKwitansiBkm;
        $id = 59;
        $query = $model::find()->andWhere(['pembayaranpelayanan_id' => $id])->one();
        $tgl_pulang = is_null($query->tglpulang_pendaftaran) ? '' : ' s/d '.$query->tglpulang_pendaftaran;
        $keterangan_tanggal = $query->tgl_pendaftaran.$tgl_pulang;
        $terbilang = self::Terbilang((int)$query->total_terbayar). 'Rupiah';
        $tanggal = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $tanggal_sekarang = date('d').' '.$tanggal['bulan'].' '.$tanggal['tahun'];
        
        if (!empty($model)) {
            // Print
            $print = new DocoPrint();

            // Assign attributes
            $print->attributes = [
                '#nama_pasien#' => $query->nama_pasien,
                '#total_terbayar#' => $query->total_terbayar,
                '#terbilang#' => $terbilang,
                '#no_kwitansi#' => $query->no_kwitansi,
                '#tanggal#' => $tanggal_sekarang,
                '#instalasi_nama#' => $query->instalasi_nama,
                '#keterangan_tanggal#' => $keterangan_tanggal,
                '#no_pendaftaran#' => $query->no_pendaftaran,
                '#kasir#' => $query->kasir, 
            ];

            // Print output
            $print->Output();
        }
    }
}