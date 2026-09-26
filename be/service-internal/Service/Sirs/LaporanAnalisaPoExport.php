<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\LaporanAnalisaPurchaseOrderView;

class LaporanAnalisaPoExport extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cache = Yii::$app->cache;
        $filter = $this->filter;
        $advanced_filter = isset($filter['advanced-filter']) ? $filter['advanced-filter'] : [];

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Melakukan pencarian data',
                'progress' => 6
            ]),
        ]);

        $model = new LaporanAnalisaPurchaseOrderView;
        $query = $model::find();
        $query = $this->populateFilter($query, $advanced_filter);
        if (!empty($filter['order'])) {
            $query->orderBy($filter['order']);
        }
        $result = $model->mappingDataExcel($query);
        $tempRows = [];
        $rowsIndex = 0;
        if (!empty($result)) {

            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-excel:'.$this->unique_str,
                'message' => json_encode([
                    'status' => 'finish',
                    'messageProcess' => 'Mempersiapkan data hasil pencarian',
                    'progress' => 10
                ]),
            ]);

            $crs = count($result);
            for ($idx = 0; $idx < $crs; $idx++) {
                $temp = [];
                foreach ($result[$idx] as $key => $value) {
                    if (($value === null) && !in_array($key, $this->emptyFields())) {
                        $temp[$key] = '-';
                    } else {
                        $temp[$key] = $value;
                    }
                }
                $tempRows[] = $temp;
                $ctr = ($idx+1);
                if (($ctr%100) == 0) {
                    $cache->set($this->unique_str .'-data'. $rowsIndex, $tempRows);
                    $tempRows = [];
                    $rowsIndex++;

                    $progress = round(($ctr / $crs) * 70);
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-excel:'.$this->unique_str,
                        'message' => json_encode([
                            'status' => 'finish',
                            'messageProcess' => 'Mempersiapkan data hasil pencarian: ' . $ctr . '/' . $crs . ' data',
                            'progress' => 10 + $progress
                        ]),
                    ]);

                }
            }
            if (!empty($tempRows)) {
                $cache->set($this->unique_str .'-data'. $rowsIndex, $tempRows);
                $tempRows = [];
                $rowsIndex++;

                $progress = 70;
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:'.$this->unique_str,
                    'message' => json_encode([
                        'status' => 'finish',
                        'messageProcess' => 'Mempersiapkan data hasil pencarian: ' . $crs . '/' . $crs . ' data',
                        'progress' => 10 + $progress
                    ]),
                ]);
            }
            if ($rowsIndex > 0) {
                $cache->set($this->unique_str .'-total-index', $rowsIndex);
                $cache->set($this->unique_str .'-total-data', $crs);
            }
        }
        return json_encode([
            'service' => 'Sirs-LaporanAnalisaPoExport',
            'payload' => $this->attributes,
            'response'  => $this->unique_str,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    protected function populateFilter($query, $advFilter)
    {
        foreach($advFilter as $key => $value) {
            if (!empty($value)) {
                if (in_array($key, ['tgl_pr', 'tgl_po'])) {
                    $explodeDate = explode(" - ", $value);
                    if (count($explodeDate) == 2) {
                        $startDate = date('Y-m-d 00:00:00', strtotime($explodeDate[0]));
                        $endDate = date('Y-m-d 23:59:59', strtotime($explodeDate[1]));
                        $query->andWhere(['between', $key, $startDate, $endDate]);
                    }
                } else if (in_array($key, ['no_po'])) {
                    $query->andWhere([$key => $value]);
                } else {
                    $query->andWhere(['ILIKE', $key, $value]);
                }
            }
        }
        return $query;
    }

    protected function emptyFields()
    {
        return [
            'Qty PO', 'Qty PR', 'Qty Penerimaan', 'Sisa Penerimaan',
            'Harga (Rp.)', 'Diskon (%)', 'PPn (%)', 'Sub Total (Rp.)', 'Total setelah PPn (Rp.)'
        ];
    }
}