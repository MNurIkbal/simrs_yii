<?php
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class LapKunjunganRawatInap extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $isExcel = ($this->isExcel == 1) ? true : false;
      $cacheFiles = Yii::$app->cacheFiles;
      $attributes = $this->getDataAttibutes($this->filter, $isExcel);
      if($isExcel) {
         $row = $tmpCache = [];
         $no = 1;
         $prefix = 0;
         foreach ($attributes as $value) {
            $tgl_pendaftaran = !empty($value['tgl_pendaftaran']) ? date('d M Y H:i:s', strtotime($value['tgl_pendaftaran'])) : '-';
            $no_pendaftaran = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '-';
            $no_rekam_medik = isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '-';
            $nama_pasien = isset($value['nama_pasien']) ? $value['nama_pasien'] : '-';
            $info_kunjungan = $no_pendaftaran .' - '.$no_rekam_medik.' - '.$nama_pasien;
            $jenis_kelamin = isset($value['jenis_kelamin']) ? $value['jenis_kelamin'] : '-';
            $umur = isset($value['umur']) ? $value['umur'] : '-';
            $golonganumur_nama = isset($value['golonganumur_nama']) ? $value['golonganumur_nama'] : '-';
            $agama = isset($value['agama']) ? $value['agama'] : null;
            $statusperkawinan = isset($value['statusperkawinan']) ? $value['statusperkawinan'] : '';
            $pekerjaan = isset($value['pekerjaan']) ? $value['pekerjaan'] : '-';
            $kabupaten_nama = isset($value['kabupaten_nama']) ? $value['kabupaten_nama'] : '';
            $kunjungan = isset($value['kunjungan']) ? $value['kunjungan'] : '';
            $jeniskasuspenyakit_nama = isset($value['jeniskasuspenyakit_nama']) ? $value['jeniskasuspenyakit_nama'] : '';
            $penjamin_nama = isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
            $carabayar_nama = isset($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
            $carabayar_penjamin = $carabayar_nama . ' / ' . $penjamin_nama;
            $nama_perujuk = isset($value['nama_perujuk']) ? $value['nama_perujuk'] : '-';
            $ruangan_nama = isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $kamar_bed = isset($value['kamar_bed']) ? $value['kamar_bed'] : '';
            $nama_pegawai = isset($value['nama_pegawai']) ? $value['nama_pegawai'] : '';

            $status_titipan = ' - ';
            if($value['carabayar_id'] == 6){
                $status_titipan = ' - ';
            } else if (!empty($value['is_pasientitipan_pk'])) {
                if($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false){
                    $status_titipan = $value['kelas_ditagihkan_nama'];
                }
            } else if (empty($value['is_pasientitipan_pk'])) {
                if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                    $status_titipan = $value['kelas_ditagihkan_nama'];
                }
            }
            $kelaspelayanan_nama =  $value['kelaspelayanan_nama'].' / '.$status_titipan;

            $status_ranap_nama = isset($value['status_ranap_nama']) ? $value['status_ranap_nama'] : '';
            $carakeluar_nama = isset($value['carakeluar_nama']) ? $value['carakeluar_nama'] : '';
            $diagnosa = isset($value['diagnosa']) ? $value['diagnosa'] : '';
            $tgl_keluar = isset($value['tgl_keluar']) ? date('d M Y', strtotime($value['tgl_keluar'])) : '';

            $tmp[1]  = $no;
            $tmp[2]  = $tgl_pendaftaran;
            $tmp[3]  = $info_kunjungan;
            $tmp[4]  = $jenis_kelamin;
            $tmp[5]  = $umur;
            $tmp[6]  = $golonganumur_nama;
            $tmp[7]  = $agama;
            $tmp[8]  = $statusperkawinan;
            $tmp[9]  = $pekerjaan;
            $tmp[10]  = $kabupaten_nama;
            $tmp[11]  = $kunjungan;
            $tmp[12]  = $jeniskasuspenyakit_nama;
            $tmp[13]  = $carabayar_penjamin;
            $tmp[14]  = $nama_perujuk;
            $tmp[15]  = $ruangan_nama;
            $tmp[16]  = $kamar_bed;
            $tmp[17]  = $nama_pegawai;
            $tmp[18]  = $kelaspelayanan_nama;
            $tmp[19]  = $status_ranap_nama;
            $tmp[20]  = $carakeluar_nama;
            $tmp[21]  = $diagnosa;
            $tmp[22]  = $tgl_keluar;

            $row[] = $tmp;
            $tmpCache[] = $tmp;
            if (($no%50) == 0) {
                Yii::$app->redis->executeCommand('PUBLISH', [
                   'channel' => 'export-excel:'.$this->unique_str,
                   'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
         }
         $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
      }
      else {
         $cacheFiles->set($this->unique_str, $attributes);
      }

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-pdf:'.$this->unique_str,
         'message' => json_encode(['unique_process' => $this->unique_str]),
      ]);

      return json_encode([
         'service' => 'Sirs-LapKunjunganRawatInap',
         'timestamp' => date('Y-m-d H:i:s'),
         'response' => $this->unique_str
      ]);
   }

   private function getDataAttibutes($params, $isExcel)
   {
      $client = $this->setEndPoint();
      $params['isExcel'] = $isExcel;
      try {
         $response = $client->get('lap-kunjungan-rawat-inap/get-object-data', [
            'query' => $params
         ]);
         $response = json_decode($response->getBody(), true);
         $response = $response['response'];
         return $response;
      } catch (\GuzzleHttp\Exception\RequestException $e) {
         if($e->hasResponse()) {
            $response = $e->getResponse();
            return $response->getBody();
         }
      }
   }

   private function setEndPoint()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/pendaftaran/v1/",
			'headers' => $header
		]);

		return $client;
	}
}
