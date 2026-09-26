<?php

namespace app\components\rabbitmq\laporan;

use app\modules\v1\models\GetHasilAkhirSensusFn;
use app\modules\v1\models\KelasPelayanan;
use Doco\rabbitmq\task\ReportTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use app\modules\v1\models\KonfigLaporan;
use app\modules\v1\models\LaporansensusharianrekapitulasiFn;
use app\modules\v1\models\LaporansensusharianriPasienkeluarpindahrslainV;
use app\modules\v1\models\LaporansensusharianriPasienkeluarV;
use app\modules\v1\models\LaporansensusharianriPasienmasukV;
use app\modules\v1\models\LaporansensusharianriPasienmeninggalV;
use app\modules\v1\models\LaporansensusharianriPasienpindahanV;
use app\modules\v1\models\LaporansensusharianriPasienpindahkanV;
use app\modules\v1\models\LaporansensusharianriSedangRanapv;
use app\modules\v1\models\LaporanSensusHarianRjV;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\SensuspasienranapR;
use Doco\components\DocoHelpers;

class LaporanSensusHarianRanapTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $totalPerPage;
    protected $countData;
    protected $manual_excel;
    protected $list_data = [];

    const TITLE = 'Laporan Sensus Harian Pasien Rawat Inap';
    const TGL_ADMISI = 'tgl_admisi';
    const TGL_PENDAFTARAN = 'tgl_pendaftaran';
    const TGL_PASIEN_PLG = 'tgl_pasienplg';
    const TGL_PINDAH_KAMAR = 'tgl_pindahkamar';
    const GROUP_KONDISIKELUAR_MORE_48 = [5];

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttibutes($this->filter);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Berhasil menyiapkan data.',
                'progress' => 70
            ]),
        ]);
    }

    /** proses export */
    protected function prosesExport()
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang menyiapkan file excel.',
                'progress' => 80
            ]),
        ]);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Sedang mengimport data ke dalam excel.',
                'progress' => 85
            ]),
        ]);

        $this->generateExcel();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses import excel berhasil.',
                'progress' => 90
            ]),
        ]);

        $this->uploadFile();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil.',
                'progress' => 100,
                'filename' => $this->unique_str,
                'tgl_pendaftaran' => $this->filter['tgl_pendaftaran']
            ]),
        ]);
    }

    private function getDataAttibutes()
    {
        try {
            $tgl_pendaftaran = ArrayHelper::getValue($this->filter, 'tgl_pendaftaran');
            $start = date('Y-m-d 00:00:00', strtotime($tgl_pendaftaran));
            $end = date('Y-m-d 23:59:59', strtotime($tgl_pendaftaran));
            $kelaspelayanan = ArrayHelper::getValue($this->filter, 'kelas');
            $ruangan = ArrayHelper::getValue($this->filter, 'ruangan');
            $statusRanap = ArrayHelper::getValue($this->filter, 'statusperiksa');
            $data = $this->generateData($start, $end, $kelaspelayanan, $ruangan, false, $statusRanap);

            return $data;
        } catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);

        $tgl_pendaftaran = ArrayHelper::getValue($this->filter, 'tgl_pendaftaran');
        $start = date('Y-m-d 00:00:00', strtotime($tgl_pendaftaran));
        $end = date('Y-m-d 23:59:59', strtotime($tgl_pendaftaran));
        $kelaspelayanan = ArrayHelper::getValue($this->filter, 'kelas');
        $ruangan = ArrayHelper::getValue($this->filter, 'ruangan');
        $statusRanap = ArrayHelper::getValue($this->filter, 'statusperiksa');

        $kelasNama = '';
        $ruanganNama = '';
        $statusRanapNama = '';

        if(isset($kelaspelayanan) && $kelaspelayanan != '') {
            $kelasNama = KelasPelayanan::findOne($kelaspelayanan)->kelaspelayanan_nama;
        }

        if(isset($ruangan) && $ruangan != '') {
            $ruanganNama = Ruangan::findOne($ruangan)->ruangan_nama;
        }

        if(isset($statusRanap) && $statusRanap != '') {
            $statusRanapNama = Lookup::findOne($statusRanap)->lookup_name;
        }

        $tmp = $this->generateData($start, $end, $kelaspelayanan, $ruangan, true, $statusRanap);
        $no = 0;
        foreach ($tmp as $key => $value) {
            switch ($key) {
                case 'dataMasuk':
                    $result[$no] = (!empty($tmp['dataMasuk']) ? $tmp['dataMasuk'] : [$this->modelPasienMasuk(true)->getAttributes()]);
                    $subTitle[$no] = 'PASIEN MASUK RAWAT INAP';
                    $SheetName[$no] = 'PASIEN MASUK RAWAT INAP';
                    break;
                case 'dataSedangRanap':
                    $result[$no] = (!empty($tmp['dataSedangRanap']) ? $tmp['dataSedangRanap'] : [$this->modelPasienSedangRanap(true)->getAttributes()]);
                    $subTitle[$no] = 'PASIEN SEDANG DIRAWAT INAP';
                    $SheetName[$no] = 'PASIEN SEDANG DIRAWAT INAP';
                    break;
                case 'dataKeluar':
                    $result[$no] = (!empty($tmp['dataKeluar']) ? $tmp['dataKeluar'] : [$this->modelPasienKeluar(true)->getAttributes()]);
                    $subTitle[$no] = 'PASIEN KELUAR RAWAT';
                    $SheetName[$no] = 'PASIEN KELUAR RAWAT';
                    break;
                case 'dataKeluarRujuk':
                    $result[$no] = (!empty($tmp['dataKeluarRujuk']) ? $tmp['dataKeluarRujuk'] : [$this->modelPasienKeluarRujuk(true)->getAttributes()]);
                    $subTitle[$no] = 'PASIEN KELUAR RUJUK RS LAIN';
                    $SheetName[$no] = 'PASIEN KELUAR RUJUK RS LAIN';
                    break;
                case 'dataPindahanDari':
                    $result[$no] = (!empty($tmp['dataPindahanDari']) ? $tmp['dataPindahanDari'] : [$this->modelPasienPindahanDari(true)->getAttributes()]);
                    $subTitle[$no] = 'PASIEN PINDAHAN';
                    $SheetName[$no] = 'PASIEN PINDAHAN';
                    break;
                case 'dataPindahanKe':
                    $result[$no] = (!empty($tmp['dataPindahanKe']) ? $tmp['dataPindahanKe'] : [$this->modelPasienPindahanKe(true)->getAttributes()]);
                    $subTitle[$no] = 'PASIEN DIPINDAHAKAN';
                    $SheetName[$no] = 'PASIEN DIPINDAHAKAN';
                    break;
                case 'dataMeninggal':
                    $result[$no] = (!empty($tmp['dataMeninggal']) ? $tmp['dataMeninggal'] : [$this->modelPasienMeninggal(true)->getAttributes()]);
                    $subTitle[$no] = 'PASIEN MENINGGAL';
                    $SheetName[$no] = 'PASIEN MENINGGAL';
                    break;
                case 'dataRekapitulasi':
                    $result[$no] = $tmp['dataRekapitulasi'];
                    $subTitle[$no] = 'REKAPITULASI';
                    $SheetName[$no] = 'REKAPITULASI';
                    break;
                default:
                    continue;
                    break;
            }
            $no++;
        }
        $header = [
            'Periode' => date('d F Y', strtotime($start)),
            'Ruangan' => $ruanganNama,
            'Kelas' => $kelasNama,
            'Status Pasien' => $statusRanapNama
        ];
        $path = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $filePath = DocoHelpers::exportExcelMultiSheet(self::TITLE, $result, $header,  array("uploadPath" => "./uploads", "skipIncrement" => false, "subHeader" => $subTitle), [], [], true, $SheetName);

        $filePath->save($path);
        
        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function generateData($start, $end, $kelaspelayanan = null, $ruangan = null, $isExcel = false, $status = null)
    {
        $dataMasuk = $dataKeluar = $dataKeluarRujuk = $dataPindahanDari = $dataPindahanKe = $dataMeninggal = $dataRekapitulasi = [];

        $dataMasukExcel = $dataKeluarExcel = $dataKeluarRujukExcel = $dataPindahanDariExcel = $dataPindahanKeExcel = $dataMeninggalExcel = $dataRekapitulasiExcel = $dataSedangRanapExcel = [];

        $modelMasuk = $this->modelPasienMasuk();
        $modelKeluar = $this->modelPasienKeluar();
        $modelKeluarRujuk = $this->modelPasienKeluarRujuk();
        $modelPindahanDari = $this->modelPasienPindahanDari();
        $modelPindahanKe = $this->modelPasienPindahanKe();
        $modelMeninggal = $this->modelPasienMeninggal();
        $modelSedangRanap = $this->modelPasienSedangRanap();
        $tmp = [];

        /* move up to avoid multiple OR condition */
        $modelSedangRanap->andWhere(['and', self::TGL_PASIEN_PLG . ' ' . new \yii\db\Expression('is null'), ['<=', self::TGL_ADMISI, $start]]);
        $modelSedangRanap->orWhere(['and', self::TGL_PASIEN_PLG . ' ' . new \yii\db\Expression('is not null'), ['>=', self::TGL_PASIEN_PLG, $start], ['<=', self::TGL_ADMISI, $start]]);

        if ($kelaspelayanan) {
            $modelMasuk->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelKeluar->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelKeluarRujuk->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelPindahanDari->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelPindahanKe->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelMeninggal->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelSedangRanap->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
        }

        if ($ruangan) {
            $modelMasuk->andWhere(['ruangan_id' => $ruangan]);
            $modelKeluar->andWhere(['ruangan_id' => $ruangan]);
            $modelKeluarRujuk->andWhere(['ruangan_id' => $ruangan]);
            $modelPindahanDari->andWhere(['ruangan_id' => $ruangan]);
            $modelPindahanKe->andWhere(['ruangan_id' => $ruangan]);
            $modelMeninggal->andWhere(['ruangan_id' => $ruangan]);
            $modelSedangRanap->andWhere(['ruangan_id' => $ruangan]);
        }

        if ($status) {
            $modelMasuk->andWhere(['status_ranap_id' => $status]);
            $modelKeluar->andWhere(['status_ranap_id' => $status]);
            $modelKeluarRujuk->andWhere(['status_ranap_id' => $status]);
            $modelPindahanDari->andWhere(['status_ranap_id' => $status]);
            $modelPindahanKe->andWhere(['status_ranap_id' => $status]);
            $modelMeninggal->andWhere(['status_ranap_id' => $status]);
            $modelSedangRanap->andWhere(['status_ranap_id' => $status]);
        }

        $dataMasuk = $modelMasuk->andWhere(['between', self::TGL_ADMISI, $start, $end])->orderBy([self::TGL_ADMISI => SORT_ASC])->asArray()->all();
        $this->sendProgressPercentages('Menyiapkan data pasien masuk rawat inap', 10);
        $dataKeluar = $modelKeluar->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end])->orderBy([self::TGL_PASIEN_PLG => SORT_ASC])->asArray()->all();
        $this->sendProgressPercentages('Menyiapkan data pasien keluar rawat', 20);
        $dataKeluarRujuk = $modelKeluarRujuk->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end])->orderBy([self::TGL_PASIEN_PLG => SORT_ASC])->asArray()->all();
        $this->sendProgressPercentages('Menyiapkan data pasien keluar rujuk rs lain', 30);
        $dataPindahanDari = $modelPindahanDari->andWhere(['between', self::TGL_PINDAH_KAMAR, $start, $end])->orderBy([self::TGL_PINDAH_KAMAR => SORT_ASC])->asArray()->all();
        $this->sendProgressPercentages('Menyiapkan data pasien pindahan', 40);
        $dataPindahanKe = $modelPindahanKe->andWhere(['between', self::TGL_PINDAH_KAMAR, $start, $end])->orderBy([self::TGL_PINDAH_KAMAR => SORT_ASC])->asArray()->all();
        $this->sendProgressPercentages('Menyiapkan data pasien dipindahlan', 45);
        $dataMeninggal = $modelMeninggal->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end])->orderBy([self::TGL_PASIEN_PLG => SORT_ASC])->asArray()->all();
        $this->sendProgressPercentages('Menyiapkan data pasien meninggal', 55);
        $dataRekapitulasi = $this->generateRekapitulasiData($start, $end, $kelaspelayanan, $ruangan, $isExcel, $status);
        $this->sendProgressPercentages('Menyiapkan data rakapitulasi', 60);
        $dataSedangRanap = $modelSedangRanap->orderBy([self::TGL_ADMISI => SORT_ASC])->asArray()->all();
        $this->sendProgressPercentages('Menyiapkan data pasien sedang ranap inap', 65);

        if (!empty($dataMasuk)) {
            foreach ($dataMasuk as $k => $val) {
                if (!empty($val['diagnosa_nama'])) {
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if ($isExcel) {
                    $tmpMsk['nama_pasien'] = $val['nama_pasien'];
                    $tmpMsk['norm'] = $val['no_rekam_medik'];
                    $tmpMsk['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpMsk['ruangan'] = $val['ruangan_nama'];
                    $tmpMsk['kamar'] = $val['kamar'];
                    $tmpMsk['bed'] = $val['tempattidur'];
                    $tmpMsk['jaminan'] = $val['penjamin_nama'];
                    $tmpMsk['dokter'] = $val['nama_dokter'];
                    $tmpMsk['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $dataMasukExcel[] = $tmpMsk;
                } else {
                    $dataMasuk[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if (!empty($dataSedangRanap)) {
            error_reporting(0); 
            foreach ($dataSedangRanap as $k => $val) {
                if (!empty($val['diagnosa_nama'])) {
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if ($isExcel) {
                    $tmpSdgRnp['nama_pasien'] = $val['nama_pasien'];
                    $tmpSdgRnp['norm'] = $val['no_rekam_medik'];
                    $tmpSdgRnp['jeniskelamin_nama'] = $val['jeniskelamin_nama'];
                    $tmpSdgRnp['no_telepon_pasien'] = $val['no_telepon_pasien'];
                    $tmpSdgRnp['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpSdgRnp['ruangan'] = $val['ruangan_nama'];
                    $tmpSdgRnp['kamar'] = $val['kamar'];
                    $tmpSdgRnp['tempattidur'] = $val['tempattidur'];
                    $tmpSdgRnp['penjamin_nama'] = $val['penjamin_nama'];
                    $tmpSdgRnp['nama_dokter'] = $val['nama_dokter'];
                    $tmpSdgRnp['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpSdgRnp['tgl_masukkamar'] = $val['tgl_masukkamar'];

                    $waktu = DocoHelpers::getDiffDateTime($val['tgl_masukkamar_1'], date('Y-m-d H:i:s'));
                    $tmpSdgRnp['jam_rawat'] = $waktu['jam'];
                    $tmpSdgRnp['lama_rawat'] = $waktu['hari'];
                    $dataSedangRanapExcel[] = $tmpSdgRnp;
                } else {
                    $waktu = DocoHelpers::getDiffDateTime($val['tgl_masukkamar_1'], date('Y-m-d H:i:s'));
                    $dataSedangRanap[$k]['jam_rawat'] = $waktu['jam'];
                    $dataSedangRanap[$k]['lama_rawat'] = $waktu['hari'];
                    $dataSedangRanap[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if (!empty($dataKeluar)) {
            foreach ($dataKeluar as $k => $val) {
                if (!empty($val['diagnosa_nama'])) {
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if ($isExcel) {
                    $tmpKlr['nama_pasien'] = $val['nama_pasien'];
                    $tmpKlr['norm'] = $val['no_rekam_medik'];
                    $tmpKlr['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpKlr['ruangan'] = $val['ruangan_nama'];
                    $tmpKlr['kamar'] = $val['kamar'];
                    $tmpKlr['bed'] = $val['tempattidur'];
                    $tmpKlr['jaminan'] = $val['penjamin_nama'];
                    $tmpKlr['dokter'] = $val['nama_dokter'];
                    $tmpKlr['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpKlr['tgl_msk_ruangan'] = $val['tgl_masukkamar'];
                    $tmpKlr['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $tmpKlr['hari'] = $val['lama_rawat'];
                    $dataKeluarExcel[] = $tmpKlr;
                } else {
                    $dataKeluar[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $dataKeluar[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if (!empty($dataKeluarRujuk)) {
            foreach ($dataKeluarRujuk as $k => $val) {
                if (!empty($val['diagnosa_nama'])) {
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if ($isExcel) {
                    $tmpKlrRjk['nama_pasien'] = $val['nama_pasien'];
                    $tmpKlrRjk['norm'] = $val['no_rekam_medik'];
                    $tmpKlrRjk['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpKlrRjk['ruangan'] = $val['ruangan_nama'];
                    $tmpKlrRjk['kamar'] = $val['kamar'];
                    $tmpKlrRjk['bed'] = $val['tempattidur'];
                    $tmpKlrRjk['jaminan'] = $val['penjamin_nama'];
                    $tmpKlrRjk['dokter'] = $val['nama_dokter'];
                    $tmpKlrRjk['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpKlrRjk['tgl_msk_ruangan'] = $val['tgl_masukkamar'];
                    $tmpKlrRjk['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $tmpKlrRjk['hari'] = $val['lama_rawat'];
                    $tmpKlrRjk['RS_tujuan'] = $val['rumahsakit_rujukan'];
                    $dataKeluarRujukExcel[] = $tmpKlrRjk;
                } else {
                    $dataKeluarRujuk[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $dataKeluarRujuk[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if (!empty($dataPindahanDari)) {
            foreach ($dataPindahanDari as $k => $val) {
                if (!empty($val['diagnosa_nama'])) {
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if ($isExcel) {
                    $tmpPindahDari['nama_pasien'] = $val['nama_pasien'];
                    $tmpPindahDari['norm'] = $val['no_rekam_medik'];
                    $tmpPindahDari['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpPindahDari['ruangan'] = $val['ruangan_skrg'];
                    $tmpPindahDari['kamar'] = $val['kamar_dari'];
                    $tmpPindahDari['bed'] = $val['tempattidur_dari'];
                    $tmpPindahDari['jaminan'] = $val['penjamin_nama'];
                    $tmpPindahDari['ruangan_asal'] = $val['ruangan_dari'];
                    $tmpPindahDari['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $dataPindahanDariExcel[] = $tmpPindahDari;
                } else {
                    $dataPindahanDari[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if (!empty($dataPindahanKe)) {
            foreach ($dataPindahanKe as $k => $val) {
                if (!empty($val['diagnosa_nama'])) {
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if ($isExcel) {
                    $tmpPindahKe['nama_pasien'] = $val['nama_pasien'];
                    $tmpPindahKe['norm'] = $val['no_rekam_medik'];
                    $tmpPindahKe['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpPindahKe['ruangan'] = $val['ruangan_skrg'];
                    $tmpPindahKe['kamar'] = $val['kamar_skrg'];
                    $tmpPindahKe['bed'] = $val['tempattidur_skrg'];
                    $tmpPindahKe['jaminan'] = $val['penjamin_nama'];
                    $tmpPindahKe['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpPindahKe['tanggal_masuk'] = $val['tgl_masukkamar'];
                    $tmpPindahKe['lama_rawat_(Jam)'] = 0;
                    $tmpPindahKe['hari'] = $val['lama_rawat'];
                    $tmpPindahKe['kamar_tujuan'] = $val['kamar_ke'];
                    $tmpPindahKe['dokter'] = $val['dokter_admisi'];
                    if (empty($val['lama_rawat'])) {
                        $lamaRawat =  $this->generateJamRawat($val['tgl_masukkamar_1']);
                        $tmpPindahKe['lama_rawat'] = $lamaRawat;
                        if ($lamaRawat == 0) {
                            $tmpPindahKe['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, null, true);
                            $tmpPindahKe['lama_rawat'] = 1;
                        } else {
                            $tmpPindahKe['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, $lamaRawat, true);
                        }
                    } else {
                        $tmpPindahKe['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    }
                    $dataPindahanKeExcel[] = $tmpPindahKe;
                } else {
                    $dataPindahanKe[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    if (empty($val['lama_rawat'])) {
                        $lamaRawat =  $this->generateJamRawat($val['tgl_masukkamar_1']);
                        $dataPindahanKe[$k]['lama_rawat'] = $lamaRawat;
                        if ($lamaRawat == 0) {
                            $dataPindahanKe[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, null, true);
                            $dataPindahanKe[$k]['lama_rawat'] = 1;
                        } else {
                            $dataPindahanKe[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, $lamaRawat, true);
                        }
                    } else {
                        $dataPindahanKe[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    }
                }
            }
        }

        if (!empty($dataMeninggal)) {
            foreach ($dataMeninggal as $k => $val) {
                if (!empty($val['diagnosa_nama'])) {
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if ($isExcel) {
                    $tmpMeninggal['nama_pasien'] = $val['nama_pasien'];
                    $tmpMeninggal['norm'] = $val['no_rekam_medik'];
                    $tmpMeninggal['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpMeninggal['ruangan'] = $val['ruangan_nama'];
                    $tmpMeninggal['kamar'] = $val['kamar'];
                    $tmpMeninggal['bed'] = $val['tempattidur'];
                    $tmpMeninggal['jaminan'] = $val['penjamin_nama'];
                    $tmpMeninggal['dokter'] = $val['nama_dokter'];
                    $tmpMeninggal['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpMeninggal['tanggal_masuk'] = $val['tgl_masukkamar'];
                    $tmpMeninggal['<_48'] = 0;
                    $tmpMeninggal['>_48'] = 0;
                    if ($val['lama_rawat'] <= 2) {
                        $tmpMeninggal['<_48'] = 1;
                    } else {
                        $tmpMeninggal['>_48'] = 1;
                    }
                    $dataMeninggalExcel[] = $tmpMeninggal;
                } else {
                    $dataMeninggal[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $dataMeninggal[$k]['lama_rawat_leb48'] = 0;
                    $dataMeninggal[$k]['lama_rawat_kur48'] = 0;
                    if ($val['lama_rawat'] <= 2) {
                        $dataMeninggal[$k]['lama_rawat_kur48'] = 1;
                    } else {
                        $dataMeninggal[$k]['lama_rawat_leb48'] = 1;
                    }
                }
            }
        }

        return [
            'dataMasuk' => ($isExcel) ? $dataMasukExcel : $dataMasuk,
            'dataSedangRanap' => ($isExcel) ? $dataSedangRanapExcel : $dataSedangRanap,
            'dataKeluar' => ($isExcel) ? $dataKeluarExcel : $dataKeluar,
            'dataKeluarRujuk' => ($isExcel) ? $dataKeluarRujukExcel : $dataKeluarRujuk,
            'dataPindahanDari' => ($isExcel) ? $dataPindahanDariExcel : $dataPindahanDari,
            'dataPindahanKe' => ($isExcel) ? $dataPindahanKeExcel : $dataPindahanKe,
            'dataMeninggal' => ($isExcel) ? $dataMeninggalExcel : $dataMeninggal,
            'dataRekapitulasi' => $dataRekapitulasi,
        ];
    }

    private function modelPasienMasuk($new = false)
    {
        if ($new) {
            return new LaporansensusharianriPasienmasukV;
        } else {
            return LaporansensusharianriPasienmasukV::find();
        }
    }

    private function modelPasienKeluar($new = false)
    {
        if ($new) {
            return new LaporansensusharianriPasienkeluarV;
        } else {
            return LaporansensusharianriPasienkeluarV::find();
        }
    }

    private function modelPasienSedangRanap($new = false)
    {
        if ($new) {
            return new LaporansensusharianriSedangRanapv;
        } else {
            return LaporansensusharianriSedangRanapv::find();
        }
    }

    private function modelPasienPindahanDari($new = false)
    {
        if ($new) {
            return new LaporansensusharianriPasienpindahanV;
        } else {
            return LaporansensusharianriPasienpindahanV::find();
        }
    }

    private function modelPasienPindahanKe($new = false)
    {
        if ($new) {
            return new LaporansensusharianriPasienpindahkanV;
        } else {
            return LaporansensusharianriPasienpindahkanV::find();
        }
    }

    private function modelPasienMeninggal($new = false)
    {
        if ($new) {
            return new LaporansensusharianriPasienmeninggalV;
        } else {
            return LaporansensusharianriPasienmeninggalV::find();
        }
    }

    private function modelRekapitulasiFn()
    {
        return LaporansensusharianrekapitulasiFn::find();
    }

    private function modelPasienKeluarRujuk($new = false)
    {
        if ($new) {
            return new LaporansensusharianriPasienkeluarpindahrslainV;
        } else {
            return LaporansensusharianriPasienkeluarpindahrslainV::find();
        }
    }

    private function modelSensusPasienRanap($new = false)
    {
        if ($new) {
            return new SensuspasienranapR;
        } else {
            return SensuspasienranapR::find();
        }
    }

    private function getRuangan()
    {
        return Ruangan::find()
            ->where(['is_deleted' => false, 'is_active' => true])
            ->orderBy(['ruangan_nama' => SORT_ASC]);
    }

    private function getKelaspelayanan()
    {
        return KelasPelayanan::find()
            ->where(['is_deleted' => false, 'is_active' => true])
            ->orderBy(['kelaspelayanan_nama' => SORT_ASC]);
    }

    private function getSatusRanap()
    {
        return Lookup::find()
            ->where(['in', 'lookup_id', [441, 487]])
            ->orderBy(['lookup_name' => SORT_ASC]);
    }

    private function generateRekapitulasiData($start, $end, $kelaspelayanan = null, $ruangan = null, $isExcel = false, $status = null)
    {
        $result = [];
        $data = [];
        $tmp = [];
        $isCurrentDate = true;
        $currentDate =  date('Y-m-d', strtotime('NOW'));

        if ($currentDate != $start) {
            $isCurrentDate = false;
        }

        if (!$kelaspelayanan) {
            $kelaspelayanan = null;
        }
        if (!$ruangan) {
            $ruangan = null;
        }
        if (!$status) {
            $status = null;
        }

        $model = Yii::$app->db->createCommand('
            SELECT * FROM laporansensusharianri_rekapitulasi_fn(:firstdate,:lastdate, :ruangan_id,:kelaspelayanan_id, :status)
            ')
            ->bindParam(':firstdate', $start)
            ->bindParam(':lastdate', $end)
            ->bindParam(':ruangan_id', $ruangan)
            ->bindParam(':kelaspelayanan_id', $kelaspelayanan)
            ->bindParam(':status', $status);

        $query = $model->queryAll();
        $result = array_shift($query);
        $getJmlAwal =  (new GetHasilAkhirSensusFn(['extParam' => [date('Y-m-d', strtotime("-1 day", strtotime($start)))]]))->find()->asArray()->one();
        $jmlAwal = isset($getJmlAwal['hasil_sensus']) ? $getJmlAwal['hasil_sensus'] : 0;
        unset($result['vtanggal']);
        unset($result['pasien_hari_sebelumnya']);
        unset($result['jml_123']);
        unset($result['jml_567']);
        unset($result['keluar_meninggalkur48']);
        unset($result['keluar_meninggalleb48']);
        unset($result['pasien_akhir']);

        foreach ($result as $key => &$value) {
            $value += array_sum(array_column($query, $key));
        }

        if (!isset($result['pasien_hari_sebelumnya'])) {
            $result['pasien_hari_sebelumnya'] = (int) ($jmlAwal == null) ? 0 : $jmlAwal;
        }

        if (!isset($result['jml_123'])) {
            $result['jml_123'] = (int) $jmlAwal + $result['pasien_masuk']; //pasien pindahan jgn di hitung
        }

        if (!isset($result['jml_567'])) {
            $result['jml_567'] = (int) $result['keluar_hidup'] + $result['keluar_meninggaljml'] + $result['keluar_rujukrslain']; // pasien pindah kamar jgn di hitung
        }

        if (!isset($result['pasien_akhir'])) {
            $result['pasien_akhir'] = (int) $result['jml_123'] - $result['jml_567'];
        }

        if (!empty($result)) {
            foreach ($result as $key => $val) {
                if (!$isExcel) {
                    $tmp['no'] = $this->generateOrder($key);
                }
                $tmp['order'] =  $this->generateOrder($key);
                $tmp['keterangan'] = $this->generateText($key);
                $tmp['jumlah'] = $val;
                $data[] = $tmp;
            }
        }

        ArrayHelper::multisort($data, ['order'], [SORT_ASC]);
        return $data;
    }

    private function generateText($text)
    {
        switch ($text) {
            case 'pasien_masuk':
                return 'Pasien Masuk Perawatan';
                break;
            case 'jml_123':
                return 'Jumlah Pasien Sedang dirawat (1+2)';
                break;
            case 'keluar_hidup':
                return 'Pasien Keluar Hidup';
                break;
            case 'keluar_dipindahkan':
                return 'Pasien Dipindahkan';
                break;
            case 'keluar_meninggaljml':
                return 'Pasien Meninggal';
                break;
            case 'keluar_rujukrslain':
                return 'Pasien Keluar RS rujuk lain';
                break;
            case 'jml_567':
                return 'Jumlah Pasien Keluar Rawat(5+7+8)';
                break;
            case 'pasien_akhir':
                return 'Jumlah Sisa (4-9)';
                break;
            default:
                return ucwords(str_replace("_", " ", $text));
                break;
        }
    }

    private function generateOrder($text)
    {
        switch ($text) {
            case 'pasien_hari_sebelumnya':
                return 1;
                break;
            case 'pasien_masuk':
                return 2;
                break;
            case 'pasien_pindahan':
                return 3;
                break;
            case 'jml_123':
                return 4;
                break;
            case 'keluar_hidup':
                return 5;
                break;
            case 'keluar_dipindahkan':
                return 6;
                break;
            case 'keluar_meninggaljml':
                return 7;
                break;
            case 'keluar_rujukrslain':
                return 8;
                break;
            case 'jml_567':
                return 9;
                break;
            case 'pasien_akhir':
                return 10;
                break;
            default:
                return 0;
                break;
        }
    }

    private function generateJamRawat($start, $end = null, $lamaRawat = null, $toHours = false)
    {
        $dateOne = new \DateTime($start);

        if ($end) {
            $dateTwo = new \DateTime($end);
        } else {
            $dateTwo = new \DateTime();
        }
        $tmpResult = ($dateTwo->diff($dateOne));

        if ($lamaRawat) {
            $result = ($tmpResult->d * 24) + $tmpResult->h;
        } else {
            if ($toHours) {
                $result = $tmpResult->h;
            } else {
                $result = $tmpResult->days;
            }
        }

        return $result;
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        try {
            $response = $client->post('lap-sensus-harian-pasien-ranap/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str .'.xlsx'),
                        'filename' => 'LAP_SENSUS_HARIAN_PASIEN_RAWAT_INAP.xlsx'
                    ],
                ]
            ]);
            return json_decode($response->getBody(),true);
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
            'base_uri' => "http://localhost:8858/rm/v1/",
            'headers' => $header
        ]);
        return $client;
    }

    private function sendProgressPercentages($messageProcess = null, $progress = 10)
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => $messageProcess,
                'progress' => $progress
            ]),
        ]);

        return true;
    }
}
