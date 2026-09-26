<?php 
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class InformasiPendaftaran extends \Integrasi\Contracts\DocoImplement
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
            $alamat_pasien = isset($value['alamat_pasien']) ? $value['alamat_pasien'] : '-';
            $nosep = isset($value['nosep']) ? $value['nosep'] : '-';
            $carabayar_id = isset($value['carabayar_id']) ? $value['carabayar_id'] : null;
            $kelaspelayanan_nama = isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '';
            $jeniskasuspenyakit_nama = isset($value['jeniskasuspenyakit_nama']) ? $value['jeniskasuspenyakit_nama'] : '-';
            $caraBayar = isset($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
            $penjamin = isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
            $pembuatNama = isset($value['pembuat_nama']) ? $value['pembuat_nama'] : '';
            $no_asuransi = isset($value['no_asuransi']) ? $value['no_asuransi'] : '-';
            if(isset($value['nama_depan'])) {
               $namaDepan = isset($value['nama_depan']) ? $value['nama_depan'] : '';
            }
            
            if(isset($value['namadepan'])) {
               $namaDepan = isset($value['namadepan']) ? $value['namadepan'] : '';
            }
            $namaPasien = isset($value['nama_pasien']) ? $value['nama_pasien'] : '';
            $namaPasien = !empty($namaDepan) ? $namaDepan.' '.$namaPasien : $namaPasien;
            $nama_pegawai = isset($value['nama_pegawai']) ? $value['nama_pegawai'] : '';
            $ruangan_nama = isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
            $jenis_kelamin = isset($value['jenis_kelamin']) ? $value['jenis_kelamin'] : '';
            $limit_tagihan = isset($value['limit_tagihan']) ? $value['limit_tagihan'] : 0;
            $status_periksa = isset($value['status_periksa']) ? $value['status_periksa'] : '';
            if(isset($value['nama_status_periksa'])) {
               $status_periksa = isset($value['nama_status_periksa']) ? $value['nama_status_periksa'] : '';
            }
            
            if($this->jenis == 'rajal' || $this->jenis == 'igd' || $this->jenis == 'penunjang') {
               $tmp[1]  = $no;
               $tmp[2]  = $tgl_pendaftaran;
               $tmp[3]  = $no_pendaftaran;
               $tmp[4]  = $no_rekam_medik;
               $tmp[5]  = $namaPasien;
               $tmp[6]  = $alamat_pasien;
               $tmp[7]  = $jenis_kelamin;
               $tmp[8]  = $ruangan_nama;
               $tmp[9]  = $jeniskasuspenyakit_nama;
               $tmp[10]  = $kelaspelayanan_nama;
               $tmp[11]  = $nama_pegawai;
               $tmp[12]  = $caraBayar;
               $tmp[13]  = $penjamin;
               $tmp[14]  = $no_asuransi;
               $tmp[15]  = $status_periksa;
               $tmp[16]  = $nosep;
               $tmp[17]  = $limit_tagihan;
               $tmp[18]  = $pembuatNama;
            }
            elseif($this->jenis == 'mcu') {
               $status_periksa = isset($value['status_periksa_nama']) ? $value['status_periksa_nama'] : '';
               $jenis_kelamin = isset($value['j_kelamin']) ? $value['j_kelamin'] : '';

               $tmp[1]  = $no;
               $tmp[2]  = $tgl_pendaftaran;
               $tmp[3]  = $no_pendaftaran;
               $tmp[4]  = $no_rekam_medik;
               $tmp[5]  = $namaPasien;
               $tmp[6]  = $alamat_pasien;
               $tmp[7]  = $jenis_kelamin;
               $tmp[8]  = $kelaspelayanan_nama;
               $tmp[9]  = $nama_pegawai;
               $tmp[10]  = $caraBayar;
               $tmp[11]  = $penjamin;
               $tmp[12]  = $no_asuransi;
               $tmp[13]  = $status_periksa;
               $tmp[14]  = $limit_tagihan;
               $tmp[15]  = $pembuatNama;
            }
            elseif($this->jenis == 'ranap') {
               $statusTitipan = '';
               $is_pasientitipan = isset($value['is_pasientitipan']) ? $value['is_pasientitipan'] : false;
               $is_pasientitipan_pk = isset($value['is_pasientitipan_pk']) ? $value['is_pasientitipan_pk'] : false;
               $is_stoppasientitipan = isset($value['is_stoppasientitipan']) ? $value['is_stoppasientitipan'] : false;
               $kelas_ditagihkan_nama = isset($value['kelas_ditagihkan_nama']) ? $value['kelas_ditagihkan_nama'] : '';
               $str = !empty($statusTitipan) ? ' / ' : '';
               $kelaspelayanan_nama = $kelaspelayanan_nama.$str.$statusTitipan;
               $statusKamar = isset($value['status_kelas']) ? $value['status_kelas'] : '';
               if($carabayar_id != 6){
                  if($is_pasientitipan_pk && !$is_stoppasientitipan) {
                     $statusTitipan = $kelas_ditagihkan_nama;
                  }
                  else {
                     if($is_pasientitipan && !$is_stoppasientitipan) {
                        $statusTitipan = $kelas_ditagihkan_nama;
                     }
                  }
               }

               $kamarruangan_nokamar = isset($value['kamarruangan_nokamar']) ? $value['kamarruangan_nokamar'] : '';
               if(!empty($kamarruangan_nokamar)) {
                  $ruangan_nama = $nama_pegawai.'-'.$ruangan_nama.'-'.$kamarruangan_nokamar;
               }

               $tmp[1]  = $no;
               $tmp[2]  = $tgl_pendaftaran;
               $tmp[3]  = $no_pendaftaran;
               $tmp[4]  = $no_rekam_medik;
               $tmp[5]  = $namaPasien;
               $tmp[6]  = $alamat_pasien;
               $tmp[7]  = $jenis_kelamin;
               $tmp[8]  = $ruangan_nama;
               $tmp[9]  = $jeniskasuspenyakit_nama;
               $tmp[10]  = $kelaspelayanan_nama;
               $tmp[11]  = $statusKamar;
               $tmp[12]  = $nama_pegawai;
               $tmp[13]  = $caraBayar;
               $tmp[14]  = $penjamin;
               $tmp[15]  = $no_asuransi;
               $tmp[16]  = $nama_pegawai;
               $tmp[17]  = $nosep;
               $tmp[18]  = $limit_tagihan;
               $tmp[19]  = $pembuatNama;
            }

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
         'service' => 'Sirs-InformasiPendaftaran',
         'timestamp' => date('Y-m-d H:i:s'),
         'response' => $this->unique_str
      ]);
   }

   private function getDataAttibutes($params, $isExcel)
   {
      $client = $this->setEndPoint();
      $params['isExcel'] = $isExcel;
      try {
         $response = $client->get('inf-pasien/get-object-data', [
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