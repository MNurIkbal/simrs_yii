<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPermintaanMakanView;
use GuzzleHttp\Client;

class ExcelDataPermintaanMakanProcess extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow() {
        $request = Yii::$app->request;
        $model = new LaporanPermintaanMakanView;
        $query = $model::find();
        $periode = '';
        $header = $footer = [];
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        if(isset($_GET['advanced-filter']['tgl_permintaanmakan']) && $_GET['advanced-filter']['tgl_permintaanmakan'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaanmakan']);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));

                $query->andWhere(['between', 'tgl_permintaanmakan', $start, $end]);
            }
            unset($_GET['advanced-filter']['tgl_permintaanmakan']);
        } else {
            $now = date('Y-m-d', strtotime('NOW'));

            $query->andWhere(['tgl_permintaanmakan' => $now]);
        }
        $periode = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($start)), false, false).' - '.DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($end)), false, false);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query = $query->all();
        $result = [];
        if (!empty($query)) {
            $total = 0;
            $jenisDiet = $menuDiet = [];
            foreach ($query as $key => $value) {
                $newValue = [];
                $newValue[\Yii::t('app', 'Tanggal Permintaan')] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_permintaanmakan'])), false, false);
                $newValue[\Yii::t('app', 'Menu')] = $value['makanandiet_nama'];
                $newValue[\Yii::t('app', 'Jenis Diet')] = !empty($value['jenisdiet_nama']) ? $value['jenisdiet_nama'] : '-';
                $newValue[\Yii::t('app', 'Jenis Kelamin')] = $value['jenis_kelamin'];
                $newValue[\Yii::t('app', 'Tanggal Lahir')] = date('d M Y',strtotime($value['tanggal_lahir']));
                $newValue[\Yii::t('app', 'Diagnosa')] = !empty($value['diagnosa']) ? $value['diagnosa'] : '-';
                $newValue[\Yii::t('app', 'Alergi')] = !empty($value['riwayat_alergi']) ? $value['riwayat_alergi'] : '-';
                $newValue[\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
                $newValue[\Yii::t('app', 'Jumlah')] = $value['jumlah'];
                $result[] = $newValue;
                $total += $value['jumlah'];
                if(!in_array($value['jenisdiet_nama'], $jenisDiet)){
                    $jenisDiet[] = $value['jenisdiet_nama'];
                }
                if(!in_array($value['makanandiet_nama'], $menuDiet)){
                    $menuDiet[] = $value['makanandiet_nama'];
                }
            }
            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    \Yii::t('app', 'Tanggal Permintaan') => 'Jumlah',
                    \Yii::t('app', 'Jenis Diet') => count($jenisDiet),
                    \Yii::t('app', 'Menu') => count($menuDiet),
                    \Yii::t('app', 'Jumlah') => $total
                ]
            ];
        }
        $filePath = DocoHelpers::exportExcel(Yii::t('app', 'Laporan Permintaan Makan Pasien'), $result, $header,[
                "uploadPath" => "./uploads",
                "subTitle" => $periode
            ], $footer,[],true
        );
        
        $filePath->save('php://output');
        die;
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