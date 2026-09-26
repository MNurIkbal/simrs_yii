<?php

/**
 * @author: yaya
 * @since 22 March 2018
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoTerimaMutasiBarangDetailView;
use app\modules\v1\models\InfoTerimaMutasiBarangView;
use app\modules\v1\models\TerimaMutasiBarang;
use app\modules\v1\models\TerimaMutasiBarangDetail;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;


class InformasiPenerimaanBarangController extends \Doco\components\DocoActiveController
{

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-fillter"] = ["GET"];
        $verbs["get-data-detail"] = ["GET"];
        $verbs["get-detail"] = ["GET"];
        $verbs["delete"] = ["DELETE","POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoTerimaMutasiBarangView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpemesanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        if($between) {
            $query->andWhere(['between', 'tglpemesanan', $start, $end]);
        }
        /**
         * End Special Condition date range
        **/
        
        // return [$start, $end];

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetFillter()
    {
        $instalasi = ArrayHelper::map(Instalasi::find()->all(),'instalasi_id','instalasi_nama');
        $ruangan = ArrayHelper::map(Ruangan::find()->all(),'ruangan_id','ruangan_nama');
        return [
            'instalasi' => $instalasi,
            'ruangan' => $ruangan
        ];
    }

    public function actionGetDetail($id)
    {
        $model = new InfoTerimaMutasiBarangView;
        $query = $model::find(true)->where([
            'terimamutasibarang_id' => $id
        ])->one();

        return [
            'data' => $query
        ];
    }

    public function actionGetDataDetail($id)
    {
        $model = new InfoTerimaMutasiBarangDetailView;
        $query = $model::find(true);

        $query->where([
            'terimamutasibarang_id' => $id
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDelete($id)
    {
        
        $header = (new TerimaMutasiBarang)->delete($id);
        $detail = (new TerimaMutasiBarangDetail)->delete([
            'terimamutasibarang_id' => $id
        ]);

        return [
            'status' => 204
        ];
    }

    /**
    * @controller actionCetakPenerimaanMutasi
    * @attribute #tanggal_penerimaan# => Tanggal Penerimaan
    * @attribute #no_penerimaan# => nomor penerimaan
    * @attribute #instalasi_pengirim# => Instalasi pengirim
    * @attribute #ruangan_pengirim# => Ruangan Pengirim
    * @attribute #ruangan_penerima# => Ruangan Penerima
    * @attribute #tabel_detail# => Untuk menampilkan semua data item transaksi
    * @attribute #pegawai_mengetahui# => Untuk Pegawai yang mengetahui
    * @attribute #pegawai_menyetujui# => Untuk Pegawai yang meyetujui penerimaan
    **/
    public function actionCetakPenerimaanMutasi($id)
    {
        // Get Header
        $query = InfoTerimaMutasiBarangView::find(true)->where([
            'terimamutasibarang_id' => $id
        ])->one();

        // Get Detail
        $detail = InfoTerimaMutasiBarangDetailView::find(true)->where([
            'terimamutasibarang_id' => $id
        ])->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal_penerimaan#' => !empty($query->tglterima) ? date('d M Y',strtotime($query->tglterima)) : '',
            '#no_penerimaan#' => !empty($query->noterimamutasi) ? $query->noterimamutasi : '',
            '#instalasi_pengirim#' => !empty($query->instalasi_pengirim) ? $query->instalasi_pengirim : '',
            '#ruangan_pengirim#' => !empty($query->ruangan_pengirim) ? $query->ruangan_pengirim : '',
            '#pegawai_mengetahui#' => !empty($query->pegawai_mengetahui) ? $query->pegawai_mengetahui : '',
            '#pegawai_menyetujui#' => !empty($query->pegawai_menyetujui) ? $query->pegawai_menyetujui : '',
            '#ruangan_penerima#' => !empty($query->ruangan_penerima) ? strtoupper($query->ruangan_penerima) : '',
            '#tabel_detail#' => $this->renderPartial('index',[
                'detail' => $detail
            ]),
        ];

        $print->Output();
    }
}