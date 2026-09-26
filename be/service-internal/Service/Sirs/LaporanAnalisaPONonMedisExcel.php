<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanAnalisaPoNonMedisView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class LaporanAnalisaPONonMedisExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data = $this->loadData()->asArray()->all();
        // $cacheFiles = Yii::$app->cacheFiles;
        $cache = Yii::$app->cache;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) {
            $tmp[1]  = $no;
            $tmp[2]  = !is_null($value['kode_barang']) ? $value['kode_barang'] : '';
            $tmp[3]  = !is_null($value['nama_barang']) ? $value['nama_barang'] : '';
            $tmp[4]  = !is_null($value['no_pr']) ? $value['no_pr'] : '';
            $tmp[5]  = !is_null($value['tgl_pr']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['created_date_pr']))) : null;
            $tmp[6]  = !is_null($value['tgl_approve']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_approve']))) : null;
            $tmp[7]  = !is_null($value['qty_pr']) ? $value['qty_pr'] : '';
            $tmp[8]  = !is_null($value['satuan_pr']) ? $value['satuan_pr'] : '-';
            $tmp[9]  = !is_null($value['catatan']) ? $value['catatan'] : '-';
            $tmp[10]  = !is_null($value['no_po']) ? $value['no_po'] : '-';
            $tmp[11]  = !is_null($value['po_cito']) ? $value['po_cito'] : '-';
            $tmp[12]  = !is_null($value['po_admin']) ? $value['po_admin'] : '-';
            $tmp[13]  = !is_null($value['tgl_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_po']))) : '-';
            $tmp[14]  = !is_null($value['tgl_validasi_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_validasi_po']))) : '-';
            $tmp[15]  = !is_null($value['tgl_batal_po']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_batal_po']))) : '-';
            $tmp[16]  = !is_null($value['catatan_batal_po']) ? $value['catatan_batal_po'] : '-';
            $tmp[17]  = !is_null($value['qty_po']) ? $value['qty_po'] : '-';
            $tmp[18]  = !is_null($value['satuan_po']) ? $value['satuan_po'] : '-';
            $tmp[19]  = !is_null($value['harga']) ? $value['harga'] : '-';
            $tmp[20]  = !is_null($value['diskon']) ? $value['diskon'] : '-';
            $tmp[21]  = !is_null($value['ppn']) ? $value['ppn'] : '-';
            $tmp[22]  = !is_null($value['subtotal']) ? $value['subtotal'] : '-';
            $tmp[23]  = !is_null($value['total']) ? $value['total'] : '-';
            $tmp[24]  = !is_null($value['no_penerimaan']) ? $value['no_penerimaan'] : '-';
            $tmp[25]  = !is_null($value['tgl_penerimaan']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_penerimaan']))) : '';
            $tmp[26]  = !is_null($value['qty_penerimaan']) ? $value['qty_penerimaan'] : '-';
            $tmp[27]  = !is_null($value['penerimaan']) ? $value['penerimaan'] : '-';
            $tmp[28]  = !is_null($value['sisa_penerimaan']) ? $value['sisa_penerimaan'] : '-';
            $tmp[29]  = !is_null($value['penerimaan']) ? $value['penerimaan'] : '-';
            $tmp[30]  = !is_null($value['nofaktur_penerimaan']) ? $value['nofaktur_penerimaan'] : '-';
            $tmp[31]  = !is_null($value['tgl_verifikasi_penerimaan']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date("d M Y H:i:s", strtotime($value['tgl_verifikasi_penerimaan']))) : '';
            $tmp[32]  = !is_null($value['pr_jarak_po']) ? $value['pr_jarak_po'].' Hari' : '-';
            $tmp[33]  = !is_null($value['po_jarak_validasi_po']) ? $value['po_jarak_validasi_po'].' Hari' : '-';
            $tmp[34]  = !is_null($value['po_jarak_tgl_penerimaan']) ? $value['po_jarak_tgl_penerimaan'].' Hari' : '-';
            $tmp[35]  = !is_null($value['pr_jarak_tgl_penerimaan']) ? $value['pr_jarak_tgl_penerimaan'].' Hari' : '-';
            $tmp[36]  = !is_null($value['po_validasi_tgl_penerimaan']) ? $value['po_validasi_tgl_penerimaan'].' Hari' : '-';
            $tmp[37]  = !is_null($value['kode_supplier']) ? $value['kode_supplier'] : '-';
            $tmp[38]  = !is_null($value['nama_supplier']) ? $value['nama_supplier'] : '-';
            $tmp[39]  = !is_null($value['catatan_po']) ? $value['catatan_po'] : '-';
            $tmpCache[] = $tmp;
            if (($no%50) == 0)
            {
                Yii::$app->redis->executeCommand('PUBLISH', [
                   'channel' => 'export-excel:'.$this->unique_str,
                   'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cache->set($this->unique_str .'-'. $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }
        $cache->set($this->unique_str .'-'. $prefix, $tmpCache);        
        return json_encode([
            'service' => 'Sirs-LaporanAnalisaPONonMedisExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str,
        ]);
    }

    private function loadData()
    {
        $request = $this->filter;
        $model = new LaporanAnalisaPoNonMedisView;
        $query = $model::find();
        $advancedFilter = $request['advanced-filter'];
        if ( count($advancedFilter) > 0) {
            $start = $end = '01-01-01';
            if (empty(ArrayHelper::getValue($advancedFilter, 'tgl_pr')) && empty(ArrayHelper::getValue($advancedFilter, 'tgl_po'))) {
                $query->andWhere(['between', 'tgl_pr', $start, $end]);
            } else {
                if (!empty(ArrayHelper::getValue($advancedFilter, 'tgl_pr'))) {
                   $explodePr = explode(" - ", $advancedFilter['tgl_pr']);
                   if (count($explodePr) == 2) {
                      $start = date('Y-m-d 00:00:00', strtotime($explodePr[0]));
                      $end = date('Y-m-d 23:59:59', strtotime($explodePr[1]));
                   }
                   $query->andWhere(['between', 'tgl_pr', $start, $end]);
                } 
                if (!empty(ArrayHelper::getValue($advancedFilter, 'tgl_po'))) {
                   $explodePo = explode(" - ", $advancedFilter['tgl_po']);
                   if (count($explodePo) == 2) {
                      $start = date('Y-m-d 00:00:00', strtotime($explodePo[0]));
                      $end = date('Y-m-d 23:59:59', strtotime($explodePo[1]));
                   }
                   $query->andWhere(['between', 'tgl_po', $start, $end]);
                }
            }
        }
        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }
}
