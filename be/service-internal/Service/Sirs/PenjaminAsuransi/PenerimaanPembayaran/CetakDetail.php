<?php
/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoPrint;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class CetakDetail extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
	{
		ini_set('memory_limit', '-1');
      $cacheFiles = Yii::$app->cacheFiles;
		$path = 'uploads/' . $this->unique_str . '.xlsx';
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
               'messageProcess' => 'Sedang menyiapkan file excel.',
               'progress' => 80
			]),
		]);

		$rowData = $cacheFiles->get($this->unique_str);
		$rowHeader = ArrayHelper::getValue($rowData, 'header', []);
		$rowDetail = ArrayHelper::getValue($rowData, 'detail', []);
		$caraBayarId = ArrayHelper::getValue($rowHeader, 'carabayar_id');
		$tmpCache = $tmp = [];
      $no = 1;
		if(!empty($rowDetail)) {
			foreach ($rowDetail as $key => $value) {
				$tmp[1]  = $no;
            $tmp[2]  = ArrayHelper::getValue($value, 'no_pengajuanklaim');
				if($caraBayarId == DocoConstants::CARA_BAYAR_BPJS) {
					$tmp[3]  = ArrayHelper::getValue($value, 'no_sep');
					$tmp[4]  = ArrayHelper::getValue($value, 'tgl_verifikasi');
					$tmp[5]  = number_format(ArrayHelper::getValue($value, 'tagihan_rs', 0), 0,",",".");
					$tmp[6]  = number_format(ArrayHelper::getValue($value, 'total_pengajuan', 0), 0,",",".");
					$tmp[7]  = number_format(ArrayHelper::getValue($value, 'total_terbayar', 0), 0,",",".");
				}
				else {
					$tmp[3]  = number_format(ArrayHelper::getValue($value, 'total_pengajuan', 0), 0,",",".");
					$tmp[4]  = number_format(ArrayHelper::getValue($value, 'total_terbayar', 0), 0,",",".");
					$tmp[5]  = number_format(ArrayHelper::getValue($value, 'pembayaran', 0), 0,",",".");
					$tmp[6]  = number_format(ArrayHelper::getValue($value, 'total_sisapiutang', 0), 0,",",".");
				}
				$tmpCache[] = $tmp;
            $no++;
            $key++;
			}
		}
		
		$tanggalPenerimaan = ArrayHelper::getValue($rowHeader, 'tgl_terimabayarklaim');
      if(!empty($tanggalPenerimaan)) {
         $tanggalPenerimaan = date('d-M-Y H:i:s', strtotime($tanggalPenerimaan));
      }
		$header = [
			'No. Pembayaran' => ArrayHelper::getValue($rowHeader, 'no_terimabayarklaim', '-'),
			'Tanggal Penerimaan' => $tanggalPenerimaan,
			'Jumlah Penerimaan Pembayaran' => number_format(ArrayHelper::getValue($rowHeader, 'total_terimabayar', 0), 0,",","."),
			'Cara Bayar / Penjamin' => ArrayHelper::getValue($rowHeader, 'carabayar_nama', '-').' / '.ArrayHelper::getValue($rowHeader, 'penjamin_nama', '-'),
			'Penerima Dana' => ArrayHelper::getValue($rowHeader, 'pegawai_penerima', '-'),
			'Pemilik Rekening' => ArrayHelper::getValue($rowHeader, 'pemilik_rekening', '-'),
			'Nama Bank' => ArrayHelper::getValue($rowHeader, 'bank', '-'),
			'No. Rekening' => ArrayHelper::getValue($rowHeader, 'no_rekening', '-'),
		];
		$footer = [];
		
		$custHeader = $this->custHeader($caraBayarId);
		$filePath = DocoHelpers::exportExcel('Detail Penerimaan Pembayaran Klaim', $tmpCache, $header, [
         "skipIncrement" => true,
         'customHeader' => $custHeader,
      ], $footer, [], true);

      $filePath->save($path);
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Sedang mengimport data ke dalam Excel',
            'progress' => 85
			 ]),
		]);
		
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Proses import Excel berhasil',
            'progress' => 90
         ]),
		]);

		return json_encode([
            'service' => 'Sirs-CetakDetail',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
	}

	private function custHeader($caraBayarId)
   {
      $header = [];
		if($caraBayarId == DocoConstants::CARA_BAYAR_BPJS) {
			$header = [
				[
					[
						'label' => 'No',
						'rowspan' => 2,
					],
					[
						'label' => 'No Pengajuan',
						'rowspan' => 2,
					],
					[
						'label' => 'No. SEP',
						'rowspan' => 2,
					],
					[
						'label' => 'Tanggal Verifikasi',
						'rowspan' => 2,
					],
					[
						'label' => 'Tagihan Rumah Sakit',
						'rowspan' => 2,
					],
					[
						'label' => 'Tagihan Diajukan',
						'rowspan' => 2,
					],
					[
						'label' => 'Tagihan Disetujui',
						'rowspan' => 2,
					],
				]
			];
		}
		else {
			$header = [
				[
					[
						'label' => 'No',
						'rowspan' => 2,
					],
					[
						'label' => 'No Pengajuan',
						'rowspan' => 2,
					],
					[
						'label' => 'Total Pengajuan',
						'rowspan' => 2,
					],
					[
						'label' => 'Telah Bayar',
						'rowspan' => 2,
					],
					[
						'label' => 'Pembayaran',
						'rowspan' => 2,
					],
					[
						'label' => 'Sisa Piutang',
						'rowspan' => 2,
					],
				]
			];
		}

		return $header;
   }
}
