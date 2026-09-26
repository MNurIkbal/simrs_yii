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

use Integrasi\Service\Sirs\Models\InfoPasienSudahBayarView;
use Integrasi\Service\Sirs\Models\Penjamin;

class DataExportExcelSudahBayar extends \Integrasi\Contracts\DocoImplement
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
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttibutes($params)
    {
        try {
			$get = $this->filter;
			$model = new InfoPasienSudahBayarView;
			$query = $model::find();
			$start = date('Y-m-d 00:00:00');
			$end = date('Y-m-d 23:59:59');
			$listPenjamin = $caraBayar = '';
			
			if(isset($get['advanced-filter'])) {
				if(isset($get['advanced-filter']['tgl_pembayaran'])) {
					$explode = explode(" - ", $get['advanced-filter']['tgl_pembayaran']);
					if(count($explode) == 2) {
						$start = date('Y-m-d 00:00:00', strtotime($explode[0]));
						$end = date('Y-m-d 23:59:59', strtotime($explode[1]));
					}
					unset($get['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
				}

				if(isset($get['advanced-filter']['tgl_pulang'])) {
					$explode = explode(" - ", $get['advanced-filter']['tgl_pulang']);
					if(count($explode) == 2) {
						$outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
						$outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
					}
	
					$query->andWhere(['between', 'tgl_pulang', $outStart, $outEnd]);
					unset($get['advanced-filter']['tgl_pulang']);
				}
	
				if(isset($get['advanced-filter']['tgl_pendaftaran'])) {
					$explode = explode(" - ", $get['advanced-filter']['tgl_pendaftaran']);
					if(count($explode) == 2) {
						$inStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
						$inEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
					}
	
					$query->andWhere(['between', 'tgl_pendaftaran', $inStart, $inEnd]);
					unset($get['advanced-filter']['tgl_pulang']);
				}

				if(isset($get['advanced-filter']['carabayar_nama'])) {
					$caraBayarId = $get['advanced-filter']['carabayar_nama'];
					$query->andWhere(['carabayar_id' => $caraBayarId]);
					unset($get['advanced-filter']['carabayar_nama']);
				}
				if(isset($get['advanced-filter']['penjamin_nama'])) {
					$penjaminId = $get['advanced-filter']['penjamin_nama'];
					$penjaminId = explode(",",$penjaminId);
					$query->andWhere(['in', 'penjamin_id', $penjaminId]);
					unset($get['advanced-filter']['penjamin_nama']);
				}
			}

			$query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
			$query = DocoRestActiveFilter::advancedFilter($model, $query, $get);
			$result = [];
			foreach ($query->asArray()->all() as $key => $value) {
				$biayaAdministrasi = ArrayHelper::getValue($value, 'biaya_administrasi', 0);
				$totalTagihan = ArrayHelper::getValue($value, 'total_tagihan', 0);
				$totalTagihan = $totalTagihan + $biayaAdministrasi;
				$totalDijamin = ArrayHelper::getValue($value, 'subsidi_asuransi', 0);
				$totalDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran', 0);
				$totalDibayar = $totalTagihan - $totalDijamin - $totalDiskon;

				$instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
				$ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
				$namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
				$noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
				$caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
				$penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
				$tglPembayaran = ArrayHelper::getValue($value, 'tgl_pembayaran');
				$tglMasuk = ArrayHelper::getValue($value, 'tgl_pendaftaran');
				$tglPulang = ArrayHelper::getValue($value, 'tgl_pulang');
				$noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
				
				$newValue = [];
				$newValue['Tanggal Pembayaran'] = date('d M Y H:i:s', strtotime($tglPembayaran));
				$newValue['Tanggal Masuk - Keluar'] = date('d M Y H:i:s', strtotime($tglMasuk)) . ' - ' . date('d M Y H:i:s', strtotime($tglPulang));
				$newValue['Instalasi - Ruangan Akhir'] = $instalasiNama. ' - '.$ruanganNama;
				$newValue['No Pendaftaran'] = $noPendaftaran;
				$newValue['Nama Pasien'] = $namaPasien;
				$newValue['Nomor Rekam Medik'] = $noRekamMedik;
				$newValue['Cara Bayar'] = $caraBayarNama;
				$newValue['Penjamin'] = $penjaminNama;
				$newValue['Jumlah Tagihan'] = number_format($totalTagihan, 2);
				$newValue['Diskon'] = number_format($totalDiskon, 2);
				$newValue['Jumlah Dibayar Penjamin'] = number_format($totalDijamin, 2);
				$newValue['Jumlah Dibayar Pasien'] = number_format($totalDibayar, 2);
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