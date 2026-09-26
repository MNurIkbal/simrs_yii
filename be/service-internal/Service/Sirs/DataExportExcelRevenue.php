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

use Integrasi\Service\Sirs\Models\LaporanDetailRevenueView;
use Integrasi\Service\Sirs\Models\Penjamin;

class DataExportExcelRevenue extends \Integrasi\Contracts\DocoImplement
{
    
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getDataAttibutes($this->filter);
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
            'service' => 'Sirs-DataExportExcel',
            'payload' => $this->filter,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttibutes($params)
    {
        try {
    
            $model = new LaporanDetailRevenueView;
            $query = $model::find(true);
            $start = date('Y-m-01');
            $end = date('Y-m-d');
            $title = 'Laporan Revenue Detail';

            $jenis_periode = isset($params['periode_tanggal']) ? $params['periode_tanggal'] : null;
            $range_bulan = isset($params['periode_bulan']) ? $params['periode_bulan'] : null;
            $unit = isset($params['unit']) ? $params['unit'] : null;
            $kategori = isset($params['kategori']) ? $params['kategori'] : null;
            $start_date = isset($params['start_date']) ? $params['start_date'] : null;
            $end_date = isset($params['end_date']) ? $params['end_date'] : null;
            $range_tanggal = isset($params['range_tanggal']) ? $params['range_tanggal'] : null;


            if($jenis_periode == "date_range") {
                if($range_tanggal) {
                    $explode = explode(" - ", $range_tanggal);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d', strtotime($explode[0]));
                        $end = date('Y-m-d', strtotime($explode[1]));
                    }
                }
            }
            else {
                if($range_bulan) {
                    $rangeBulan = $range_bulan;
                    $explode = explode("-", $rangeBulan);
                    $maxDate = cal_days_in_month(CAL_GREGORIAN, $explode[1], $explode[0]);
                    $start = "01-".$explode[1].'-'.$explode[0];
                    $start = date('Y-m-d', strtotime($start));
                    $end = $maxDate.'-'.$explode[1].'-'.$explode[0];
                    $end = date('Y-m-d', strtotime($end));
                }
            }

            $header = [
                Yii::t("app", "Periode") => $start . ' - ' . $end,
                Yii::t("app", "Unit") => $unit,
            ];

            if($jenis_periode == "date_range") {
                if($range_tanggal) {
                    $explode = explode(" - ", $range_tanggal);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d', strtotime($explode[0]));
                        $end = date('Y-m-d', strtotime($explode[1]));
                    }
                }
            }
            else {
                if($range_bulan) {
                    $rangeBulan = $range_bulan;
                    $explode = explode("-", $rangeBulan);
                    $maxDate = cal_days_in_month(CAL_GREGORIAN, $explode[1], $explode[0]);
                    $start = "01-".$explode[1].'-'.$explode[0];
                    $start = date('Y-m-d', strtotime($start));
                    $end = $maxDate.'-'.$explode[1].'-'.$explode[0];
                    $end = date('Y-m-d', strtotime($end));
                }
            }

            $subTitle = date('d-M-Y', strtotime($start)) .' s/d '. date('d-M-Y', strtotime($end));
            if($kategori && !empty($kategori)) {
                $query->andWhere(['tipe' => $kategori]);
            }

            if($unit && $unit != "null") {
                $query->andWhere(['unit' => $unit]);
            }

            $query->andWhere(['between', 'tanggal', $start, $end]);
            $data = $query->asArray()->all();

			$result = [];
			foreach ($data as $key => $value) {
				$tipe = ArrayHelper::getValue($value, 'tipe', 0);
                $unit = ArrayHelper::getValue($value, 'unit', '');
                $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik', '');
                $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran', '');
                $noPembayaran = ArrayHelper::getValue($value, 'no_pembayaran', '');
                $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama', '');
                $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama', '');
                $namaPasien = ArrayHelper::getValue($value, 'nama_pasien', '');
                $tindakanObatPaket = ArrayHelper::getValue($value, 'tindakan_obat_paket', '');
                $satuanUnitNama = ArrayHelper::getValue($value, 'satuanunit_nama', '');
                $hargaSatuan = ArrayHelper::getValue($value, 'harga_satuan', null);
                $qty = ArrayHelper::getValue($value, 'qty', null);
                $tarifDiskon = ArrayHelper::getValue($value, 'tarif_diskon', null);
                $total = ArrayHelper::getValue($value, 'total', '');
                $tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', '');
                $tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', '');
                $dpjp = ArrayHelper::getValue($value, 'dpjp', '');
                $dokter = ArrayHelper::getValue($value, 'dokter', '');
                $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran', '');
                $tglPulang = ArrayHelper::getValue($value, 'tgl_pulang', '');
                $tanggal = ArrayHelper::getValue($value, 'tanggal', '');
                $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama', '');
                $kelasPelayananNama = ArrayHelper::getValue($value, 'kelaspelayanan_nama', '');
				
				$newValue = [];
                $newValue['Tipe'] = $tipe;
                $newValue['Unit'] = $unit;
                $newValue['No Rekam Medik'] = $noRekamMedik;
                $newValue['No Pendaftaran'] = $noPendaftaran;
                $newValue['No Pembayaran'] = $noPembayaran;
                $newValue['Nama Cara Bayar'] = $caraBayarNama;
                $newValue['Nama Penjamin'] = $penjaminNama;
                $newValue['Nama Pasien'] = $namaPasien;
                $newValue['Nama Tindakan/Obat/Paket'] = $tindakanObatPaket;
                $newValue['Satuan'] = $satuanUnitNama;
                $newValue['Harga Satuan'] = $hargaSatuan;
                $newValue['Qty'] = $qty;
                $newValue['Tarif Diskon'] = $tarifDiskon;
                $newValue['Total'] = $total;
                $newValue['Tarif Dijamin'] = $tarifDijamin;
                $newValue['Tarif Dibayarkan'] = $tarifDibayarkan;
                $newValue['Dpjp'] = $dpjp;
                $newValue['Dokter'] = $dokter;
                $newValue['Tanggal Pendaftaran'] = $tglPendaftaran;
                $newValue['Tanggal Pulang'] = $tglPulang;
                $newValue['Tanggal'] = $tanggal;
                $newValue['Nama Instalasi'] = $instalasiNama;
                $newValue['Nama Kelas Pelayanan'] = $kelasPelayananNama;
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