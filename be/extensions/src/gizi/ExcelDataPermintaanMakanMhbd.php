<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Extensions\gizi;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPermintaanMakanView;
use GuzzleHttp\Client;

class ExcelDataPermintaanMakanMhbd extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow() {
        $request = Yii::$app->request;
        
        $permintaanMakan = Yii::$app->docoPlugin->execute('get_data_permintaan_makan');
        $tanggal = $request->get('advanced-filter', []);
        $tanggal = isset($tanggal['tgl_admisi']) ? $tanggal['tgl_admisi'] : date('Y-m-d');
        $tanggal = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($tanggal)), true, false);

        

        $data = isset($permintaanMakan['data']) ? $permintaanMakan['data'] : [];
        if(empty($data)) {
            throw new \Exception("Error ssssProcessing Request", 500);
        }
        $result = [];
        foreach ($data as $permintaan) {
            $attr = $permintaan->attributes;
            $firstDate = date_create(Date('Y-m-d', strtotime($attr['tgl_admisi'])));
            $lastDate = date_create(Date('Y-m-d'));
            $diff = date_diff($firstDate, $lastDate);
            $LOS = $diff->days > 0 ? $diff->days : 1;

            $result[] = [
                // 'No' => (sizeof($result) + 1) . ' ',
                'Bed No (Billable Class)' => $attr['kamarruangan_nokamar'] . " / " . $attr['no_tempattidur']."\n".$attr['kelaspelayanan_nama'],
                'MRID Patient Name' => $attr['no_rekam_medik']."\n".$attr['nama_pasien'],
                'DOB Age' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($attr['tanggal_lahir'])), true, false)."\n".DocoHelpers::convertDateToAge($attr['tanggal_lahir']) . " Y",
                'Admit Date / LOS Primary Doctor' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($attr['tgl_admisi'])), true, true)."\n".$LOS." D - ".$attr['dokter_dpjp'],
                //'Diet' => $attr['menu_makan'],
                'Diet Type' => $attr['jenisdiet_nama'],
                //'New Diet' => '',
                'Remark' => '',
                'Latest Diagnosa' => isset($attr['diagnosa']['nama']) ? $attr['diagnosa']['nama'] : '-',
                'BF' => '',
                'S1' => '',
                'LN' => '',
                'S2' => '',
                'DN' => '',
                'S3' => '',
                'SP' => '',
                'EX' => ''
            ];
        }
        $header = [
            "Date" => $tanggal
        ];
        $filePath = DocoHelpers::exportExcel(Yii::t('app', 'DAFTAR PESANAN MAKANAN PASIEN'), $result, $header, [
            "uploadPath" => "./uploads",
            "mergeHeader" => [
                4=> 'rpe',
            ],
            "skipHeader" => true,
            // "customHeader" => [[
            //     ['label' => "Date : " . $tanggal],
            //     ['label' => ""], // location
            // ]],
            'customHeader' => [[
                ['label' => 'No', 'rowspan' => 2],
                ['label' => 'Bed No (Billable Class)', 'rowspan' => 2],
                ['label' => 'MRID Patient Name', 'rowspan' => 2],
                ['label' => 'DOB Age', 'rowspan' => 2],
                ['label' => 'Admit Date / LOS Primary Doctor', 'rowspan' => 2],
                //['label' => 'Diet', 'rowspan' => 2],
                ['label' => 'Diet Type', 'rowspan' => 2],
                //['label' => 'New Diet', 'rowspan' => 2],
                ['label' => 'Remark', 'rowspan' => 2],
                ['label' => 'Latest Diagnosa', 'rowspan' => 2],
                ['label' => 'Check List', 'colspan' => 8],
            ], [
                ['label' => 'BF', 'startfrom' => 11],
                ['label' => 'S1'],
                ['label' => 'LN'],
                ['label' => 'S2'],
                ['label' => 'DN'],
                ['label' => 'S3'],
                ['label' => 'SP'],
                ['label' => 'EX'],
            ]],
            'autoFilter' => false,
            'customFormatCode' => [
                ['selectColumn'=>'B','alignment'=>['wrapText'=> true]],
                ['selectColumn'=>'C','alignment'=>['wrapText'=> true]],
                ['selectColumn'=>'D','alignment'=>['wrapText'=> true]],
                ['selectColumn'=>'E','alignment'=>['wrapText'=> true]],
            ]
        ], [], [],true);
        
        $filePath->save('php://output');
        die;   
        throw new \Exception("Error ssssProcessing Request", 500);
        
        exit;
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
                $newValue[\Yii::t('app', 'Jenis Diet')] = $value['jenisdiet_nama'];
                $newValue[\Yii::t('app', 'Menu')] = $value['makanandiet_nama'];
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
    }
}