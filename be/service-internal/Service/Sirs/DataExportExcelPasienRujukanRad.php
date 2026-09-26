<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

use Integrasi\Service\Sirs\Models\LaporanPasienRujukanRadView;
use Integrasi\Service\Sirs\Models\Penjamin;

class DataExportExcelPasienRujukanRad extends \Integrasi\Contracts\DocoImplement
{
    
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
		
        $attributes = $this->getDataAttributes($this->filter);
        $cacheFiles->set($this->unique_str, $attributes);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
        return json_encode([
            'service' => 'Sirs-DataExportExcelPasienRujukanRad',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttributes($params)
    {
        try {
			$_GET = $params;
			$model = new LaporanPasienRujukanRadView;
            $query = $model::find();
            /**
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

			//Query Data Baru
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
				$newValue['tgl_rujukan'] = date('d M Y H:i', strtotime($tglRujukan));
				$newValue['tgl_persetujuan'] =  date('d M Y H:i', strtotime($tglPersetujuan));
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
			return $result;
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
    }

    private function setUrl()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => isset($this->params['base_uri']) ? $this->params['base_uri'] : '',
			'headers' => $header
		]);

		return $client;
	}
}