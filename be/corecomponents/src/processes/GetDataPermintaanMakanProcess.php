<?php

/**
 * @author : ilham.pramono
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPermintaanMakanView;
use GuzzleHttp\Client;

class GetDataPermintaanMakanProcess extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow() {
        try {
            $request = Yii::$app->request;
            $model = new LaporanPermintaanMakanView;
            $query = $model::find();
            $params = [];
            $start = $end = date('Y-m-d');
            if(isset($_GET['advanced-filter']['tgl_permintaanmakan']) && $_GET['advanced-filter']['tgl_permintaanmakan'] != '') {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaanmakan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_permintaanmakan']);
            }
            $query->andWhere(['between', 'tgl_permintaanmakan', $start, $end]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $countData = $this->countData($query->asArray()->all());
            $data = new ActiveDataProvider([
                    'query' => $query,
                ]);
            return [
                'data' => $data->getModels(),
                '_meta' => [
                    'totalCount' => $data->getTotalCount()
                ],
                'counter' => $countData
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function countData($data)
    {
        $count_jenis = [];
        $count_makanan = [];
        $data_count = [];
        $data_count['jenisdiet_nama'] = [];
        $data_count['makanandiet_nama'] = [];
        $data_count['jumlah'] = 0;

        foreach ($data as $key => $value ) {
            $data_count['jenisdiet_nama'][] = $value['jenisdiet_id'];
            $data_count['makanandiet_nama'][] = $value['makanandiet_id'];
            $data_count['jumlah'] = $data_count['jumlah'] + $value['jumlah'];
        }

        if (!empty($data_count['jenisdiet_nama'])) {
            $count_jenis = array_count_values($data_count['jenisdiet_nama']);
        }

        if (!empty($data_count['makanandiet_nama'])) {
            $count_makanan = array_count_values($data_count['makanandiet_nama']);
        }

        return [
            'count_jenis' => count($count_jenis),
            'count_makanan' => count($count_makanan),
            'count_jumlah' => $data_count['jumlah'],
        ];
    }

}