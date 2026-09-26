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
use Doco\models\DocMapping;
use Doco\models\DocHeader;
use Doco\models\HasilBridgingRad;
use Doco\models\Pegawai;

class CetakHasilProcess extends \Doco\components\DocoBaseProcessExtension
{

    protected function cetak()
    {
        $pasienmasukpenunjang_id = $this->_requestData->get('pasienmasukpenunjang_id', null);
        $tindakanpelayanan_id = $this->_requestData->get('tindakanpelayanan_id', null);
        $daftartindakan_id = $this->_requestData->get('daftartindakan_id', null);
        $hasilpemeriksaanrad_id = $this->_requestData->get('hasilpemeriksaanrad_id', null);
        $jenis_cetakan = $this->_requestData->get('jenis_cetakan', 1);
        $preview = $this->_requestData->get('preview', null);
        $modul = $this->_requestData->get('modul', null);
        $type = !empty($daftartindakan_id) ? 'printRadiologi' : null;
        $kodeDok = 'chpr';
        $indikasiKlinis = !empty($dataPasien['catatan_dokterpengirim']) ? $dataPasien['catatan_dokterpengirim'] : '-';
        $connection = Yii::$app->db;
        $helpers = new DocoHelpers;
        $dataRs = $this->getDataRs(); 
        $namaRs = str_replace(' ', '_', !empty($dataRs['nama_rumahsakit']) ? $dataRs['nama_rumahsakit'] : '');
        $this->setConfigLogo($kodeDok, $jenis_cetakan);
        $print = new DocoPrint($kodeDok);
        if($daftartindakan_id) {
            if (!empty($preview)) {
                $data = [
                    'tgl_hasilrad' => $this->_requestData->post('tgl_hasil', date("Y-m-d H:i:s")),
                    'tgl_ambilfoto' => date("Y-m-d H:i:s"),
                    'created_date' => date("Y-m-d H:i:s"),
                    'daftartindakan_nama' => $this->_requestData->post('daftartindakan_nama', ''),
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
            $no_foto = preg_replace($pattern,'', $data['tgl_ambilfoto']);
            $create_date = preg_replace($pattern,'', $data['created_date']);
            $dataPasien = $this->getDataPasien($pasienmasukpenunjang_id, 'cetak_hasil_pemeriksaan', $daftartindakan_id);
            $indikasiKlinis = !empty($dataPasien['catatan_dokterpengirim']) ? $dataPasien['catatan_dokterpengirim'] : '-';
            $diagnosa = '';
            $nama_diagnosa = !empty($dataPasien['nama_diagnosa']) ? $dataPasien['nama_diagnosa'] : '';
            if(!empty($nama_diagnosa)) {
                $diagnosa = json_decode($nama_diagnosa, true);
                $diagnosa = isset($diagnosa['text']) ? $diagnosa['text'] : '';
            }
            $daftartindakan_nama = !empty($data['daftartindakan_nama']) ? $data['daftartindakan_nama'] : '';
            $keterangan = $daftartindakan_nama;
            $tgl_hasilrad = !empty($data['tgl_hasilrad']) ? $helpers->convertDate($data['tgl_hasilrad'], 'd-m-Y H:i') : null;
            $namaPasien = str_replace(' ', '_', $dataPasien['nama_pasien']);
            $formatDefault = $namaPasien.'_'.$dataPasien['no_rekam_medik'];
            $formatDocName = $formatDefault;
            $tglMasuk = !empty($dataPasien['tglmasukpenunjang']) 
                    ? $helpers->convertDate($dataPasien['tglmasukpenunjang'], 'd-m-Y H:i') : null;
            if($tglMasuk) {
                $formatDocName = $formatDefault.'_'.$tglMasuk;
            }
            $print->docName = $modul.'_'.$namaRs.'_'.$formatDocName;
            $dokterPenunjangId = isset($dataPasien['pegawai_id']) ? $dataPasien['pegawai_id'] : null;
            $nip = Pegawai::find()->select(['nomorindukpegawai'])->andWhere(['pegawai_id' => $dokterPenunjangId])->asArray()->one();
            $nip = isset($nip['nomorindukpegawai']) ? $nip['nomorindukpegawai'] : null;
            $signaturePath = Pegawai::signatureEmployee($dokterPenunjangId);
            $print->attributes = [
                '#name#' => $dataPasien['nama_pasien'],
                '#age#' => $dataPasien['umur'] . ' / ' . $dataPasien['j_kelamin'],
                '#tanggal_lahir#' => date('d M Y', strtotime($dataPasien['tanggal_lahir'])),
                '#refereed_by#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                '#pat_arr_date#' => $tglMasuk,
                '#payer_name#' => $dataPasien['penjamin_nama'],
                '#phone_patient#' => !empty($dataPasien['no_telepon_pasien']) ? $dataPasien['no_telepon_pasien'] : '-',
                '#requestion_no#' => $dataPasien['no_masukpenunjang'],
                '#uhid_no#' => $dataPasien['no_rekam_medik'],
                '#station_name#' => $dataPasien['ruangan_nama'],
                '#date#' => !empty($data['tgl_hasilrad']) ? $helpers->convertDate($data['tgl_hasilrad'], 'd-m-Y H:i:s') : '-',
                '#daftartindakan_nama#' => $keterangan,
                '#bill_no#'=> '-',
                '#kesan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif">', $kesan),
                '#kesimpulan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif">', $kesimpulan),
                '#dokter_perujuk_nama#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                '#dokter_penunjang#' => !empty($dataPasien['dokter_penunjang']) ? $dataPasien['dokter_penunjang'] : '-',
                '#tglmasukpenunjang#' => $tglMasuk,
                '#no_hasilrad#' => !empty($data['no_hasilrad']) ? $data['no_hasilrad'] : '-',
                '#diagnosa#' => $diagnosa,
                '#no_foto#'=> !empty($no_foto) ? $no_foto : $create_date,
                '#indikasi_klinis#' => $indikasiKlinis,
                '#ttd_dokter#' => $signaturePath,
                '#nip#' => $nip,
                '#cetak_pemeriksaan#' => Yii::$app->controller->renderPartial('index', [
                    'expertiseData' => $data,
                    'patientData' => $dataPasien
                ]),
            ];
            $print->Output();
        }
        else {
            $tgl_header = null;
            $data = $this->getHasilPemeriksaan($pasienmasukpenunjang_id,  $tindakanpelayanan_id, $daftartindakan_id, $hasilpemeriksaanrad_id);
            $list_detail = $data;
            if(!$tindakanpelayanan_id) {
                if (!empty($list_detail)) {
                    $countData = count($list_detail);
                    $no = 0;
                    
                    foreach ($list_detail as $sample_id => $query) {
                        // $data_hasil = isset($query) ? $query : [];
                        $kesan = $kesimpulan = '';
                        if (!empty($query['kesan'])) {
                            $kesan = trim(preg_replace('/\s\s+/', ' ', $query['kesan']));
                        }
                        if (!empty($query['kesimpulan'])) {
                            $kesimpulan = trim(preg_replace('/\s\s+/', ' ', $query['kesimpulan']));
                        }
                        $pattern = "/[-\s:]/";
                        $no_foto = preg_replace($pattern,'', $query['tgl_ambilfoto']);
                        $create_date = preg_replace($pattern,'', $query['created_date']);
                        $daftartindakan_nama = !empty($query['pemeriksaanrad_nama']) ? $query['pemeriksaanrad_nama'] : '';
                        $keterangan = $daftartindakan_nama;
                        $tgl_hasilrad = !empty($query['tgl_hasilrad']) ? $helpers->convertDate($query['tgl_hasilrad'], 'd-m-Y H:i') : null;
                        $dataPasien = $this->getDataPasien($pasienmasukpenunjang_id, 'cetak_hasil_pemeriksaan_satuan', $query['daftartindakan_id']);
                        $ruanganAsal = ArrayHelper::getValue($dataPasien, 'rujukandari_nama', '-');
                        $dokterPenunjangId = isset($dataPasien['pegawai_id']) ? $dataPasien['pegawai_id'] : null;
                        $nip = Pegawai::find()->select(['nomorindukpegawai'])->andWhere(['pegawai_id' => $dokterPenunjangId])->asArray()->one();
                        $nip = isset($nip['nomorindukpegawai']) ? $nip['nomorindukpegawai'] : null;
                        $signaturePath = Pegawai::signatureEmployee($dokterPenunjangId);
                        $tglMasuk = !empty($dataPasien['tglmasukpenunjang']) 
                                ? $helpers->convertDate($dataPasien['tglmasukpenunjang'], 'd-m-Y H:i') : null;
                        $tgl_header = $tglMasuk;
                        if(!empty($query['tgl_hasilrad'])) {
                            $tgl_header = $helpers->convertDate($query['tgl_hasilrad'], 'd-m-Y H:i');
                        }
                        $diagnosa = '';
                        $nama_diagnosa = !empty($dataPasien['nama_diagnosa']) ? $dataPasien['nama_diagnosa'] : '';
                        if(!empty($nama_diagnosa)) {
                            $diagnosa = json_decode($nama_diagnosa, true);
                            $diagnosa = isset($diagnosa['text']) ? $diagnosa['text'] : '';
                        }
                        $namaPasien = str_replace(' ', '_', $dataPasien['nama_pasien']);
                        $formatDefault = $namaPasien.'_'.$dataPasien['no_rekam_medik'];
                        $formatDocName = !empty($tgl_header) ? $formatDefault.'_'.$tgl_header : $formatDefault;
                        $print->docName = $modul.'_'.$namaRs.'_'.$formatDocName;
                       
                        if (empty($kesan)) {
                            $queryBridgingRad = HasilBridgingRad::find()->where(['like', 'order_no', '-'.ArrayHelper::getValue($query, 'tindakanpelayanan_id')])->orderBy(['hasilbridgingradiologi_id' => SORT_DESC])->asArray()->one();
                            $kesan = ArrayHelper::getValue($queryBridgingRad, 'obv_value_html', '-');
                        }

                        $print->attributes = [
                            '#name#' => $dataPasien['nama_pasien'],
                            '#age#' => $dataPasien['umur'] . ' / ' . $dataPasien['j_kelamin'],
                            '#tanggal_lahir#' => date('d M Y', strtotime($dataPasien['tanggal_lahir'])),
                            '#refereed_by#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                            '#pat_arr_date#' => $tglMasuk,
                            '#payer_name#' => $dataPasien['penjamin_nama'],
                            '#phone_patient#' => !empty($dataPasien['no_telepon_pasien']) ? $dataPasien['no_telepon_pasien'] : '-',
                            '#requestion_no#' => $dataPasien['no_masukpenunjang'],
                            '#uhid_no#' => $dataPasien['no_rekam_medik'],
                            '#station_name#' => $dataPasien['ruangan_nama'],
                            '#date#' => !empty($query['tgl_hasilrad']) ? $helpers->convertDate($query['tgl_hasilrad'], 'd-m-Y H:i:s') : '-',
                            '#daftartindakan_nama#' => $keterangan,
                            '#kesan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif">', $kesan),
                            '#kesimpulan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif">', $kesimpulan),
                            '#dokter_perujuk_nama#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                            '#dokter_penunjang#' => !empty($dataPasien['dokter_penunjang']) ? $dataPasien['dokter_penunjang'] : '-',
                            '#tglmasukpenunjang#' => $tglMasuk,
                            '#no_hasilrad#' => !empty($query['no_hasilrad']) ? $query['no_hasilrad'] : '-',
                            '#diagnosa#' => $diagnosa,
                            '#no_foto#'=> !empty($no_foto) ? $no_foto : $create_date,
                            '#indikasi_klinis#' => !empty($dataPasien['catatan_dokterpengirim']) ? $dataPasien['catatan_dokterpengirim'] : '-',
                            '#rs_name#' => !empty($dataRs['nama_rumahsakit']) ? $dataRs['nama_rumahsakit'] : '',
                            '#alamat#' => !empty($dataRs['alamatlokasi_rumahsakit']) ? $dataRs['alamatlokasi_rumahsakit'] : '',
                            '#no_telp#' => !empty($dataRs['no_telp_profilrs']) ? $dataRs['no_telp_profilrs'] : '',
                            '#kota#' => !empty($dataRs['kota']) ? $dataRs['kota'] : '',
                            '#ttd_dokter#' => $signaturePath,
                            '#ruangan_asal#' => $ruanganAsal,
                            '#nip#' => $nip,
                            '#cetak_pemeriksaan#' => Yii::$app->controller->renderPartial('index', [
                                'expertiseData' => $query,
                                'patientData' => $dataPasien
                            ]),
                        ];
                        $no++;
                        $break = ($no == $countData) ? false : true;
                        $print->generateHtml($break,null,true,true);
                    }
                    $print->Output(true);
                }
                else {
                    $data_hasil = [];
                    $dataPasien = $this->getDataPasien($pasienmasukpenunjang_id, 'cetak_hasil_pemeriksaan_satuan', null);
                    $ruanganAsal = ArrayHelper::getValue($dataPasien, 'rujukandari_nama', '-');
                    $dokterPenunjangId = isset($dataPasien['pegawai_id']) ? $dataPasien['pegawai_id'] : null;
                    $nip = Pegawai::find()->select(['nomorindukpegawai'])->andWhere(['pegawai_id' => $dokterPenunjangId])->asArray()->one();
                    $nip = isset($nip['nomorindukpegawai']) ? $nip['nomorindukpegawai'] : null;
                    $signaturePath = Pegawai::signatureEmployee($dokterPenunjangId);
                    $tglMasuk = $keterangan = $kesan = $kesimpulan = $diagnosa = $no_foto = '-';
                    $namaPasien = str_replace(' ', '_', $dataPasien['nama_pasien']);
                    $formatDefault = $namaPasien.'_'.$dataPasien['no_rekam_medik'];
                    $formatDocName = !empty($tgl_header) ? $formatDefault.'_'.$tgl_header : $formatDefault;
                    $print->docName = $modul.'_'.$namaRs.'_'.$formatDocName;

                    if (empty($kesan)) {
                        $queryBridgingRad = HasilBridgingRad::find()->where(['like', 'order_no', '-'.ArrayHelper::getValue($dataPasien, 'tindakanpelayanan_id')])->orderBy(['hasilbridgingradiologi_id' => SORT_DESC])->asArray()->one();
                        $kesan = ArrayHelper::getValue($queryBridgingRad, 'obv_value_html', '-');
                    }
                    
                    $print->attributes = [
                        '#name#' => $dataPasien['nama_pasien'],
                        '#age#' => $dataPasien['umur'] . ' / ' . $dataPasien['j_kelamin'],
                        '#tanggal_lahir#' => date('d M Y', strtotime($dataPasien['tanggal_lahir'])),
                        '#refereed_by#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                        '#pat_arr_date#' => $tglMasuk,
                        '#payer_name#' => $dataPasien['penjamin_nama'],
                        '#phone_patient#' => !empty($dataPasien['no_telepon_pasien']) ? $dataPasien['no_telepon_pasien'] : '-',
                        '#requestion_no#' => $dataPasien['no_masukpenunjang'],
                        '#uhid_no#' => $dataPasien['no_rekam_medik'],
                        '#station_name#' => $dataPasien['ruangan_nama'],
                        '#date#' => !empty($data_hasil['tgl_hasilrad']) ? $helpers->convertDate($data_hasil['tgl_hasilrad'], 'd-m-Y') : '-',
                        '#daftartindakan_nama#' => $keterangan,
                        '#kesan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif">', $kesan),
                        '#kesimpulan#' => str_replace('<p>', '<p style="font-family:Tahoma,Geneva,sans-serif">', $kesimpulan),
                        '#dokter_perujuk_nama#' => !empty($dataPasien['dokter_perujuk_nama']) ? $dataPasien['dokter_perujuk_nama'] : '-',
                        '#dokter_penunjang#' => !empty($dataPasien['dokter_penunjang']) ? $dataPasien['dokter_penunjang'] : '-',
                        '#tglmasukpenunjang#' => $tglMasuk,
                        '#no_hasilrad#' => !empty($data_hasil['no_hasilrad']) ? $data_hasil['no_hasilrad'] : '-',
                        '#diagnosa#' => $diagnosa,
                        '#no_foto#'=> $no_foto,
                        '#indikasi_klinis#' => !empty($dataPasien['catatan_dokterpengirim']) ? $dataPasien['catatan_dokterpengirim'] : '-',
                        '#rs_name#' => !empty($dataRs['nama_rumahsakit']) ? $dataRs['nama_rumahsakit'] : '',
                        '#alamat#' => !empty($dataRs['alamatlokasi_rumahsakit']) ? $dataRs['alamatlokasi_rumahsakit'] : '',
                        '#no_telp#' => !empty($dataRs['no_telp_profilrs']) ? $dataRs['no_telp_profilrs'] : '',
                        '#kota#' => !empty($dataRs['kota']) ? $dataRs['kota'] : '',
                        '#ttd_dokter#' => $signaturePath,
                        '#ruangan_asal#' => $ruanganAsal,
                        '#nip#' => $nip,
                        '#cetak_pemeriksaan#' => Yii::$app->controller->renderPartial('index', [
                            'expertiseData' => $data_hasil,
                            'patientData' => $dataPasien
                        ]),
                    ];
                    $print->Output();
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
                'tanggal_lahir',
                'nama_diagnosa',
                'catatan_dokterpengirim',
            ]);
        }

        if (!empty($tindakan_id)) {
            $model->andWhere(['daftartindakan_id' => $tindakan_id]);
            // dd
            if($type == "cetak_hasil_pemeriksaan_satuan"){
                $data = $model->asArray()->one();
            }else{
                $data = $model->asArray()->one();
            }
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
                $data = $model->asArray()->all();
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

    protected function getDataRs()
    {
        $dataRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });
        return $dataRs;
    }

    protected function setConfigLogo($kodeDok, $jenis_cetakan)
    {
        $docMaping = DocMapping::find()->where(['kode_doc' => $kodeDok])->one();
        $kodeHeaderTanpaLogo = 'chpr-header';
        $kodeHeaderWithLogo = 'chpr-header-with-logo';
        $docHeaderTanpaLogo = DocHeader::find()->where(['kode_header' => $kodeHeaderTanpaLogo])->one();
        $docHeaderWithLogo = DocHeader::find()->where(['kode_header' => $kodeHeaderWithLogo])->one();
        $headerIdTanpaLogo = ($docHeaderTanpaLogo) ? $docHeaderTanpaLogo['docheader_id'] : null;
        $headerIdWithLogo = ($docHeaderWithLogo) ? $docHeaderWithLogo['docheader_id'] : null;
        if(is_null($headerIdWithLogo)) {
            $headerIdWithLogo = $headerIdTanpaLogo;
        }
        if($docMaping) {
            $docMaping->docheader_id = ($jenis_cetakan == 2) ? $headerIdWithLogo : $headerIdTanpaLogo;
            $docMaping->save();
        }
    }

    protected function processFlow()
    {
        return $this->cetak();
    }
}
