<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use Doco\models\radiologi\HasilPemeriksaanRadView;
use Doco\models\radiologi\PemeriksaanPasienRadiologiView;
use Doco\models\ProfilRsView;

class CetakHasilKramatProcess extends \Doco\components\DocoBaseProcessExtension
{

    protected function cetak()
    {
        $connection = Yii::$app->db;
        $pasienmasukpenunjang_id = $this->_requestData->get('pasienmasukpenunjang_id', null);
        $tindakanpelayanan_id = $this->_requestData->get('tindakanpelayanan_id', null);
        $daftartindakan_id = $this->_requestData->get('daftartindakan_id', null);
        $hasilpemeriksaanrad_id = $this->_requestData->get('hasilpemeriksaanrad_id', null);
        $preview = $this->_requestData->get('preview', null);
        $type = !empty($daftartindakan_id) ? 'printRadiologi' : null;
        $dataPasien = $this->getDataPasien($pasienmasukpenunjang_id, $type, $daftartindakan_id);
        $dataPasien = isset($dataPasien[0]) ? $dataPasien[0] : $dataPasien;
        $profilRs = $this->getProfileRs();
        $print = new DocoPrint('chpr');
        if($daftartindakan_id) {
            if (!empty($preview)) {
                $data = [
                    'tgl_hasilrad' => date("Y-m-d H:i:s"),
                    'daftartindakan_nama' => $this->_requestData->post('daftartindakan_nama', ''),
                    'tgl_ambilfoto' => date("Y-m-d H:i:s"),
                    'created_date' => date("Y-m-d H:i:s"),
                    'kesan' => $this->_requestData->post('kesan', ''),
                    'kesimpulan' => $this->_requestData->post('kesimpulan', '')
                ];
            } else if($hasilpemeriksaanrad_id) {
                    $data = $connection->createCommand("
                    SELECT * FROM hasilpemeriksaanrad_v
                    WHERE hasilpemeriksaanrad_id = '{$hasilpemeriksaanrad_id}'
                ")->queryOne();
            } else {
                $data = $this->getHasilPemeriksaan($pasienmasukpenunjang_id, $tindakanpelayanan_id, $daftartindakan_id, $hasilpemeriksaanrad_id);
            }
            $kesan = $kesimpulan = '';
            if (!empty($data['kesan'])) {
                $kesan = trim(preg_replace('/\s\s+/', ' ', $data['kesan']));
            }
            if (!empty($data['kesimpulan'])) {
                $kesimpulan = trim(preg_replace('/\s\s+/', ' ', $data['kesimpulan']));
            }

            $pattern = "/[-\s:]/";
            $create_date = preg_replace($pattern,'', $data['created_date']);
            $no_foto = preg_replace($pattern,'', $data['tgl_ambilfoto']);
            $daftartindakan_nama = !empty($data['daftartindakan_nama']) ? $data['daftartindakan_nama'] : '';
            $tgl_hasil = $data['tgl_hasilrad'];
            $keterangan = !empty($tgl_hasil) ? $daftartindakan_nama : $daftartindakan_nama;
            $tgl_hasilrad = !empty($data['tgl_hasilrad']) ? DocoHelpers::convertDate($data['tgl_hasilrad'], 'd-m-Y') : '';
            $print->attributes = [
                '#name#' => $dataPasien['nama_pasien'],
                '#age#' => $dataPasien['umur'] . ' / ' . $dataPasien['j_kelamin'],
                '#bill_no#' => '-',
                '#tanggal_lahir#' => date('d M Y', strtotime($dataPasien['tanggal_lahir'])),
                '#refereed_by#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                '#pat_arr_date#' => DocoHelpers::convertDate($dataPasien['tglmasukpenunjang'], 'd-m-Y'),
                '#payer_name#' => $dataPasien['penjamin_nama'],
                '#phone_patient#' => !empty($dataPasien['no_telepon_pasien']) ? $dataPasien['no_telepon_pasien'] : '-',
                '#requestion_no#' => $dataPasien['no_masukpenunjang'],
                '#uhid_no#' => $dataPasien['no_rekam_medik'],
                '#station_name#' => $dataPasien['ruangan_nama'],
                '#date#' => DocoHelpers::convertDate($data['tgl_hasilrad'], 'd-m-Y'),
                '#no_foto#'=> !empty($no_foto) ? $no_foto : $create_date,
                '#daftartindakan_nama#' => $keterangan,
                '#kesan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif; font-size: 12px">', $kesan),
                '#kesimpulan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif; font-size: 12px">', $kesimpulan),
                '#dokter_perujuk_nama#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                '#dokter_penunjang#' => !empty($dataPasien['dokter_penunjang']) ? $dataPasien['dokter_penunjang'] : '-',
                '#tglmasukpenunjang#' => DocoHelpers::convertDate($dataPasien['tglmasukpenunjang'], 'd-m-Y'),
                '#no_hasilrad#' => !empty($expertiseData['no_hasilrad']) ? $expertiseData['no_hasilrad'] : '-',
                '#nama_rumahsakit#' => $profilRs['namaRs'],
                '#alamat#' => $profilRs['alamat'],
                '#kota#' => $profilRs['kota'],
                '#nomor_tlp#' => $profilRs['nomor_tlp'],
            ];
            $print->Output();
        }
        else {
            $data = $this->getHasilPemeriksaan($pasienmasukpenunjang_id,  $tindakanpelayanan_id, $daftartindakan_id, $hasilpemeriksaanrad_id);
            if(!$tindakanpelayanan_id) {
                $list_pemeriksaan_id = $list_data = $list_detail = [];
                foreach ($data as $key => $value) {
                    if (!empty($value['hasilpemeriksaanrad_id'])) {
                        $list_data[$value['hasilpemeriksaanrad_id']][] = $value;
                    }
                }
                foreach ($list_data as $hasilpemeriksaanrad_id => $data) {
                    foreach ($data as $key => $value) {
                        $data_hasil = $this->getHasilPemeriksaan($pasienmasukpenunjang_id, $tindakanpelayanan_id, $daftartindakan_id, $hasilpemeriksaanrad_id);
                        $nama_pegawai = !empty($data_hasil['penanggung_jawab'])
                            ? $data_hasil['penanggung_jawab']
                            : '';
                        $nama_pemeriksaan = !empty($data_hasil['daftartindakan_nama'])
                            ? $data_hasil['daftartindakan_nama']
                            : '';
                        $nama_kelompok = !empty($data_hasil['nama_kelompok'])
                            ? $data_hasil['nama_kelompok']
                            : '';
                        $tanggal_pemeriksaan = !empty($data_hasil['tgl_hasilrad'])
                            ? $data_hasil['tgl_hasilrad']
                            : date('Y-m-d H:i:s');
                        $no_hasilrad = !empty($data_hasil['no_hasilrad'])
                            ? $data_hasil['no_hasilrad']
                            : '';
                        $hasil_expertise = !empty($data_hasil['hasil_expertise'])
                            ? $data_hasil['hasil_expertise']
                            : '';
                        $kesan = !empty($data_hasil['kesan'])
                            ? $data_hasil['kesan']
                            : '';
                        $kesimpulan = !empty($data_hasil['kesimpulan'])
                            ? $data_hasil['kesimpulan']
                            : '';
                        $list_detail[$hasilpemeriksaanrad_id]['data_hasil'] = [
                            'nama_pemeriksaan' => $nama_pemeriksaan,
                            'nama_kelompok' => $nama_kelompok,
                            'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                            'no_hasilrad' => $no_hasilrad,
                            'nama_pegawai' => $nama_pegawai,
                            'hasil_expertise' => $hasil_expertise,
                            'kesan' => $kesan,
                            'kesimpulan' => $kesimpulan,
                            'tgl_ambilfoto' => $data_hasil['tgl_ambilfoto'],
                            'created_date' => $data_hasil['created_date']
                        ];
                    }
                }

                if (!empty($list_detail)) {
                    $countData = count($list_detail);
                    $no = 0;
                    foreach ($list_detail as $sample_id => $query) {
                        $data_hasil = isset($query['data_hasil']) ? $query['data_hasil'] : [];
                        $kesan = $kesimpulan = '';
                        if (!empty($data_hasil['kesan'])) {
                            $kesan = trim(preg_replace('/\s\s+/', ' ', $data_hasil['kesan']));
                        }
                        if (!empty($data_hasil['kesimpulan'])) {
                            $kesimpulan = trim(preg_replace('/\s\s+/', ' ', $data_hasil['kesimpulan']));
                        }

                        $pattern = "/[-\s:]/";
                        $no_foto = preg_replace($pattern,'', $data_hasil['tgl_ambilfoto']);
                        $create_date = preg_replace($pattern,'', $data_hasil['created_date']);
                        $daftartindakan_nama = !empty($data_hasil['nama_pemeriksaan']) ? $data_hasil['nama_pemeriksaan'] : '';
                        $tgl_hasil = $data_hasil['tanggal_pemeriksaan'];
                        $keterangan = !empty($tgl_hasil) ? $daftartindakan_nama : $daftartindakan_nama;
                        $tgl_hasilrad = !empty($data_hasil['tanggal_pemeriksaan']) ? DocoHelpers::convertDate($data_hasil['tanggal_pemeriksaan'], 'd-m-Y') : '';

                        $print->attributes = [
                            '#name#' => $dataPasien['nama_pasien'],
                            '#age#' => $dataPasien['umur'] . ' / ' . $dataPasien['j_kelamin'],
                            '#bill_no#' => '-',
                            '#tanggal_lahir#' => date('d M Y', strtotime($dataPasien['tanggal_lahir'])),
                            '#refereed_by#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                            '#pat_arr_date#' => DocoHelpers::convertDate($dataPasien['tglmasukpenunjang'], 'd-m-Y'),
                            '#payer_name#' => $dataPasien['penjamin_nama'],
                            '#phone_patient#' => !empty($dataPasien['no_telepon_pasien']) ? $dataPasien['no_telepon_pasien'] : '-',
                            '#requestion_no#' => $dataPasien['no_masukpenunjang'],
                            '#uhid_no#' => $dataPasien['no_rekam_medik'],
                            '#station_name#' => $dataPasien['ruangan_nama'],
                            '#date#' => DocoHelpers::convertDate($data_hasil['tanggal_pemeriksaan'], 'd-m-Y'),
                            '#daftartindakan_nama#' => $keterangan,
                            '#kesan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif; font-size: 12px">', $kesan),
                            '#kesimpulan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif; font-size: 12px">', $kesimpulan),
                            '#dokter_perujuk_nama#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                            '#dokter_penunjang#' => !empty($dataPasien['dokter_penunjang']) ? $dataPasien['dokter_penunjang'] : '-',
                            '#tglmasukpenunjang#' => DocoHelpers::convertDate($dataPasien['tglmasukpenunjang'], 'd-m-Y'),
                            '#no_hasilrad#' => !empty($data_hasil['no_hasilrad']) ? $data_hasil['no_hasilrad'] : '-',
                            '#nama_rumahsakit#' => $profilRs['namaRs'],
                            '#alamat#' => $profilRs['alamat'],
                            '#kota#' => $profilRs['kota'],
                            '#nomor_tlp#' => $profilRs['nomor_tlp'],
                            '#no_foto#'=> !empty($no_foto) ? $no_foto : $create_date,
                            // '#cetak_hasil_pemeriksaan#' => Yii::$app->controller->renderPartial('cetak_hasil', [
                            //     'header'=> $dataPasien,
                            //     'query' => $query,
                            // ]),
                        ];
                        $no++;
                        $break = ($no == $countData) ? false : true;
                        $print->generateHtml($break);
                    }
                    $print->Output(true);
                }
            }
        }
    }

    protected function getDataPasien($penunjang_id = null, $type = null, $tindakan_id = null)
    {
        $model = PemeriksaanPasienRadiologiView::find()
            ->where(['pasienmasukpenunjang_id' => $penunjang_id]);
        
        if ($type == 'printRadiologi') {
            $model->select([
                'nama_pasien',
                'no_telepon_pasien',
                'umur',
                'j_kelamin',
                'no_masukpenunjang',
                'no_rekam_medik',
                'dokter_perujuk_id',
                'dokter_perujuk_nama',
                'ruangan_nama',
                'tglmasukpenunjang',
                'penjamin_nama',
                'dokter_penunjang',
                'tanggal_lahir'
            ]);
        }

        if (!empty($tindakan_id)) {
            $model->andWhere(['daftartindakan_id' => $tindakan_id]);
            $data = $model->asArray()->all();
        }
        else {
            $data = $model->asArray()->one();
        }

        return $data;
    }

    protected function getHasilPemeriksaan($penunjang_id, $tindakanpelayanan_id, $daftartindakan_id, $hasilpemeriksaanrad_id)
    {
        $model = HasilPemeriksaanRadView::find();
        $model->where([
            'pasienmasukpenunjang_id' => $penunjang_id,
        ]);

        if(empty($tindakanpelayanan_id)) {
            if($hasilpemeriksaanrad_id) {
                $model->andWhere(['hasilpemeriksaanrad_id' => $hasilpemeriksaanrad_id]);
                $data = $model->one();
            }
            else {
                $data = $model->all();
            }
        }
        else {
            $model->andWhere([
                'tindakanpelayanan_id' => $tindakanpelayanan_id,
                'daftartindakan_id' => $daftartindakan_id
            ]);
            $data = $model->one();
        }

        return $data;
    }

    private function getProfileRs()
    {
        $profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });

        $kota = $namaRs = '';
        $alamat = isset($profilRs['alamatlokasi_rumahsakit']) ? $profilRs['alamatlokasi_rumahsakit'] : '';
        $nomor_tlp = isset($profilRs['no_telp_profilrs']) ? $profilRs['no_telp_profilrs'] : '';

        if (!empty($profilRs['nama_rumahsakit'])) {
          $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
          if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
              $pattern = "KOTA ADM. ";
          }
          elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
              $pattern = "KAB. ADM. ";
          }
          elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
              $pattern = "KAB. ";
          }
          elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
              $pattern = "KOTA ";
          }

          $kota = str_replace($pattern,"", $profilRs['kota']);
        }
          
        return [
          'namaRs' => $namaRs,
          'kota' => $kota,
          'alamat' => $alamat,
          'nomor_tlp' => $nomor_tlp,
        ];
    }

    protected function processFlow()
    {
       $this->cetak();
    }
}