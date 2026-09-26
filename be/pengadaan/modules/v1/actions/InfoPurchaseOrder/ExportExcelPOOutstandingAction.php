<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\LaporanPurchaseOrderOutstandingView;
use app\modules\v1\models\LaporanPurchaseOrderOutstandingBarangView;
use app\modules\v1\models\ProfilRumahSakit;

class ExportExcelPOOutstandingAction extends Action {
    public function run() {
        try {
            $hospital_name = ProfilRumahSakit::find()->where(['profilrs_id' => 1])->one();
            $title = $hospital_name->nama_rumahsakit;
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $type = $request->get('type');

            if($type == DocoConstants::JENIS_OBAT) {
                $model = new LaporanPurchaseOrderOutstandingView;
                $dateKey = 'tgl_po_dibuat';

                $options = [
                        "titleStyle" => [
                        "fontSize" => 11,
                        "alignment" => "left"
                    ],
                    "subTitle" => "PO Outstanding Report",
                    "customFormatCode" => [
                        [
                            'selectColumn' => 'B',
                            'formatCode' => 'datetime'
                        ],
                        [
                            'selectColumn' => 'E',
                            'formatCode' => 'datetime'
                        ],
                        [
                            'selectColumn' => 'F',
                            'formatCode' => 'datetime'
                        ],
                        ['selectColumn' => 'Q'],
                        ['selectColumn' => 'R'],
                        ['selectColumn' => 'S'],
                        ['selectColumn' => 'T'],
                        ['selectColumn' => 'U']
                    ],
                ];
            } else {
                // non-medis
                $model = new LaporanPurchaseOrderOutstandingBarangView;
                $dateKey = 'tanggal_po';

                $options = [
                        "titleStyle" => [
                        "fontSize" => 11,
                        "alignment" => "left"
                    ],
                    "subTitle" => "PO Outstanding Report",
                    "customFormatCode" => [
                        [
                            'selectColumn' => 'C',
                            'formatCode' => 'datetime'
                        ],
                        [
                            'selectColumn' => 'E',
                            'formatCode' => 'datetime'
                        ],
                        [
                            'selectColumn' => 'F',
                            'formatCode' => 'datetime'
                        ],
                        [
                            'selectColumn' => 'Q',
                            'formatCode' => 'number'
                        ],
                        [
                            'selectColumn' => 'T',
                            'formatCode' => 'number'
                        ],
                        [
                            'selectColumn' => 'U',
                            'formatCode' => 'number'
                        ]
                    ],
                ];

            }

            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($advanced_filter)) {
                if (isset($advanced_filter['tgl_po_dibuat'])) {
                    $tanggal = explode(' - ', $advanced_filter['tgl_po_dibuat']);
                    $start = date('Y-m-d H:i:s', strtotime($tanggal[0] . ' 00:00:00'));
                    $end = date('Y-m-d H:i:s', strtotime($tanggal[1] . ' 23:59:59'));
                    unset($advanced_filter['tgl_po_dibuat']);
                } else {
                    $tanggal = explode(' - ', $advanced_filter['tanggal_po']);
                    $start = date('Y-m-d H:i:s', strtotime($tanggal[0] . ' 00:00:00'));
                    $end = date('Y-m-d H:i:s', strtotime($tanggal[1] . ' 23:59:59'));
                    unset($advanced_filter['tanggal_po']);
                }
            }

            $query->andWhere(['between', $dateKey, $start, $end]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $header = !is_null($advanced_filter) ? $model->setHeaderExcel($advanced_filter) : [];

            $result = $model->mappingDataExcel($query);
            $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }


    }
}
