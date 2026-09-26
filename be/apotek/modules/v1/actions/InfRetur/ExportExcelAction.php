<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\InfoReturResepView;

class ExportExcelAction extends Action {
    public function run() {
        try {
            $title = 'Informasi Retur Resep';
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $model = new InfoReturResepView;
            $query = $model::find();
            $dateKey = ['tgl_retur'];
            if(count($advanced_filter) > 0) {
                $this->controller->dateFilter($query, $request, $dateKey);
            }
            if(isset($_GET['advanced-filter']['status_retur'])){
                $query->andWhere(['=','status_retur', $_GET['advanced-filter']['status_retur']]);
                unset($_GET['advanced-filter']['status_retur']);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $query->andWhere(['ruangan_nama' => $_GET['advanced-filter']['ruangan_nama']]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }            
            if(isset($_GET['advanced-filter']['nama_pasien'])){
                $query->andWhere(['ILIKE','nama_pasien', $_GET['advanced-filter']['nama_pasien']]);
                $query->orWhere(['ILIKE','no_pendaftaran', $_GET['advanced-filter']['nama_pasien']]);
                $query->orWhere(['ILIKE','no_rekam_medik', $_GET['advanced-filter']['nama_pasien']]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $header = !is_null($advanced_filter) ? $model->setHeaderExcel($advanced_filter) : [];
            $result = $model->mappingDataExcel($query);

            $filePath = DocoSpout::exportExcel($title, $result, $header, [], [], [], true);
            $filePath->close();
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }


    }
}