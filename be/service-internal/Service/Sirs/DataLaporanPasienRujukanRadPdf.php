<?php 

namespace Integrasi\Service\Sirs;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

use Integrasi\Service\Sirs\Models\LaporanPasienRujukanRadView;
use Yii;
use GuzzleHttp\Client;
use yii\base\View;

class DataLaporanPasienRujukanRadPdf extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getDataAttributes();
        $cacheFiles->set($this->unique_str, $attributes);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:'.$this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        return json_encode([
            'service' => 'Sirs-DataLaporanRujukanRadPdf',
            'payload' => $attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttributes()
    {
        try {
			$_GET = $this->filter;
			$model = new LaporanPasienRujukanRadView;
            $query = $model::find();
            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $betweenPersetujuan = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $startPersetujuan = '';
            $endPersetujuan = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if (isset($_GET['advanced-filter']['tgl_persetujuan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_persetujuan']);
                if (count($explode) == 2) {
                        $startPersetujuan = date('Y-m-d', strtotime($explode[0]));
                        $endPersetujuan = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_persetujuan']); // Unset Advanced Filter  date range
                    $betweenPersetujuan = true;
                }
                if(isset($_GET['advanced-filter']['no_rekam_medik'])) {
                    $namaPasienRM = $_GET['advanced-filter']['no_rekam_medik'];
                    $query->andWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($namaPasienRM)]);
                    $query->orWhere(['ILIKE', 'no_rekam_medik', $namaPasienRM]);
                    unset($_GET['advanced-filter']['no_rekam_medik']);
                }
            }
            
            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);

           
            if (!empty($startPersetujuan) && !empty($endPersetujuan) && $betweenPersetujuan) {
                $query->andWhere(['between', 'tgl_persetujuan', $startPersetujuan, $endPersetujuan]);
            }

			$query = DocoRestActiveFilter::advancedFilter($model, $query, $_GET);
			$result = [];
			$rowNum = 0;
			foreach ($query->asArray()->all() as $key => $value) {
				$rowNum++;
				$tglRujukan = ArrayHelper::getValue($value, 'tgl_rujukan');
				$tglPersetujuan = ArrayHelper::getValue($value, 'tgl_persetujuan');
				$noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
				$noRujukan = ArrayHelper::getValue($value, 'no_rujukan');
				$namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
				$noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
				$tanggalLahir = ArrayHelper::getValue($value, 'tanggal_lahir');
				$daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
				$jenisRujukan = ArrayHelper::getValue($value, 'jenis_rujukan');
				$rujukan = ArrayHelper::getValue($value, 'rujukan','-');
				$namaRs = ArrayHelper::getValue($value, 'rujukandari_nama','-');
				$asalRujukanNama = ArrayHelper::getValue($value, 'asalrujukan_nama','-');
				$dokterPerujuk = ArrayHelper::getValue($value, 'dokter_perujuk','-');
				$caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama','-');
				$penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama','-');
				$statusPeriksaNama = ArrayHelper::getValue($value, 'status_periksa_nama','-');
				
				$newValue = [];
				$newValue['rowNum'] = $rowNum;
				$newValue['tgl_rujukan'] = date('d M Y H:i:s', strtotime($tglRujukan));
				$newValue['tgl_persetujuan'] =  date('d M Y H:i:s', strtotime($tglPersetujuan));
				$newValue['no_pendaftaran'] = $noPendaftaran;
				$newValue['no_rujukan'] = $noRekamMedik;
				$newValue['nama_pasien'] = $namaPasien;
				$newValue['no_rekam_medik'] = $noRekamMedik;
				$newValue['tanggal_lahir'] =  date('d M Y', strtotime($tanggalLahir));
				$newValue['daftartindakan_nama'] = $daftarTindakanNama;
				$newValue['jenis_rujukan'] = $jenisRujukan;
				$newValue['rujukan'] = $rujukan;
				$newValue['nama_rs'] = $namaRs;
				$newValue['asalrujukan_nama'] = $asalRujukanNama;
				$newValue['dokter_perujuk'] = $dokterPerujuk;
				$newValue['carabayar_nama'] = $caraBayarNama;
				$newValue['penjamin_nama'] = $penjaminNama;
				$newValue['status_periksa_nama'] = $statusPeriksaNama;
				$result[$key] = $newValue;
			}
            $dokPath = 'pdf';
            
            $periode = date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end));
            $table =  [
                'model' => $result,
                'periode' => $periode,
                'tanggal' => date('d-M-Y'),
                'dokPath' => $dokPath,
                'start' => $start,
                'end' => $end,
            ];
            $datatable = $this->renderAttributes($table);
            $result = [
                'periode' => $periode,
                'attributes' => $datatable,
                'countData' => $rowNum,
            ];

			return $result;
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
    }

    private function renderAttributes($getData)
    {
        $pathView = "@app/views/lap-pasien-rujukan-radiologi/pdf";
        $model = ArrayHelper::getValue($getData, 'model', []);
        $start = ArrayHelper::getValue($getData, 'start', '');
        $end = ArrayHelper::getValue($getData, 'end', '');
        $periode = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
        $header = array();
        return [
            '#tgl_awal_bulan#'=> DocoHelpers::convDateTime($start, true, false),
            '#tgl_akhir_bulan#'=> DocoHelpers::convDateTime($end, true, false),
            '#table_exportpdf#' => (new View)->render($pathView, [
                'header' => $header,
                'model' => $model,
                'periode' => $periode,
                'tanggal' => date('d-M-Y H:i')
            ]),
        ];
    }
}