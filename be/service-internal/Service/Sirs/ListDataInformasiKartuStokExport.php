<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;

use Integrasi\Contracts\DocoImplement;
use Integrasi\Service\Sirs\Models\KartuStokObatFn;

class ListDataInformasiKartuStokExport extends DocoImplement
{
    public function execute()
    {
        $cache = Yii::$app->cache;
        $model = $this->getData();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Melakukan pencarian data',
                'progress' => 15
            ]),
        ]);

        $result = $model::find()->asArray()->all();
        $tempRows = [];
        $rowsIndex = 0;
        $namaObat = null;
        $stokAwal = null;
        if (!empty($result)) {
            $crs = count($result);
            for ($idx = 0; $idx < $crs; $idx++) {
                $temp = [];
                $value = $result[$idx];

                if ($idx == 0) {
                    $namaObat  = $value['obatalkes_nama'];
                    $stokAwal  = $value['total'];
                    $stokOut   = $value['qtystok_out'];
                    $stokIn    = $value['qtystok_in'];
                    $stokAwal += $stokOut - $stokIn;
                }

                $temp['Tanggal Transaksi']  = date('d-M-Y',strtotime($value['tanggal_transaksi']));
                $temp['Kode Obat/Alkes']    = $value['obatalkes_kode'];
                $temp['Nama Obat/Alkes']    = $value['obatalkes_nama'];
                $temp['Tanggal Kadaluarsa'] = date('d-M-Y',strtotime($value['tglkadaluarsa']));
                $temp['No Transaksi']       = $value['no_transaksi'];
                $temp['Keterangan']         = $value['keterangan'];
                $temp['Reference']          = $value['reference'];
                $temp['Ruangan Asal']       = $value['ruangan_asal_nama'];
                $temp['Ruangan Tujuan']     = $value['ruangan_tujuan_nama'];
                $temp['Qty Masuk']          = $value['qtystok_in'];
                $temp['Qty Keluar']         = $value['qtystok_out'];
                $temp['Stok']               = $value['total'];
                $temp['Satuan']             = $value['satuanunit_nama'];

                $tempRows[] = $temp;
                $ctr = ($idx+1);
                if (($ctr%100) == 0) {
                    $cache->set($this->unique_str .'-data'. $rowsIndex, $tempRows);
                    $tempRows = [];
                    $rowsIndex++;
                    $progress = round(($ctr / $crs) * 50);
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-excel:'.$this->unique_str,
                        'message' => json_encode([
                            'status' => 'finish',
                            'messageProcess' => 'Mempersiapkan data hasil pencarian: ' . $ctr . '/' . $crs . ' data',
                            'progress' => 15 + $progress
                        ]),
                    ]);
                }
            }

            if (!empty($tempRows)) {
                $cache->set($this->unique_str .'-data'. $rowsIndex, $tempRows);
                $tempRows = [];
                $rowsIndex++;
                $progress = 50;
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'export-excel:'.$this->unique_str,
                    'message' => json_encode([
                        'status' => 'finish',
                        'messageProcess' => 'Mempersiapkan data hasil pencarian: ' . $crs . '/' . $crs . ' data',
                        'progress' => 15 + $progress
                    ]),
                ]);
            }

            if ($rowsIndex > 0) {
                $cache->set($this->unique_str .'-total-index', $rowsIndex);
                $cache->set($this->unique_str .'-total-data', $crs);

                $cache->set($this->unique_str .'-nama-obat-alkes', $namaObat);
                $cache->set($this->unique_str .'-stok-awal', $stokAwal);
            }
        }

        return json_encode([
            'service' => 'Sirs-ListDataInformasiKartuStokExport',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function getData()
    {
        $get = $this->filter;
        $range_tanggal = ArrayHelper::getValue($get,'advanced-filter.tanggal_transaksi');
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        if(!is_null($range_tanggal)){
            $range_explode = explode(' - ', $range_tanggal);
            if (count($range_explode) == 2) {
                $start_date = date('Y-m-d',strtotime($range_explode[0]));
                $end_date = date('Y-m-d',strtotime($range_explode[1]));
            }
        }

        $ruangan_id = ArrayHelper::getValue($get,'advanced-filter.ruangan_id');
        $obatalkes_id = ArrayHelper::getValue($get,'advanced-filter.obatalkes_id');
        $model = new KartuStokObatFn(['extParam' => [$start_date, $end_date, $ruangan_id, $obatalkes_id]]);

        return $model;
    }
}