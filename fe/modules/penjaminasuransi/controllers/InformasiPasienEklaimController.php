<?php
namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;

use Doco\penjaminasuransi\models\SuratKeteranganDokterForm;

class InformasiPasienEklaimController extends DocoController
{
    protected $_title;
    protected $_restRajal;
    protected $_restMaster;
    protected $_restPenjaminAsuransi;
    protected $_module = '/penjaminasuransi/informasi-pasien-eklaim/';

    private $_id_diagnosa_utama = DocoConstants::DIAGNOSA_UTAMA;
    private $_id_diagnosa_penyerta = DocoConstants::DIAGNOSA_PENYERTA;

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Transaksi Kirim Online');
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/generate-api');
        $response = json_decode($response->getBody(),TRUE);
        $response = $response['response']['penjamin'];

        $tipe_klaim = [
            [
                'id' => 'RJ',
                'name' => 'Rawat Jalan'
            ],
            [
                'id' => 'RI',
                'name' => 'Rawat Inap'
            ],
            [
                'id' => 'RJ-RI',
                'name' => 'Rawat Jalan dan Rawat Inap'
            ]
        ];
        $filter = [
            [
                'id' => 'tgl_keluar',
                'name' => 'Tanggal Keluar'
            ],
            [
                'id' => 'tgl_group',
                'name' => 'Tanggal Grouping'
            ],
        ];

        $tipe_klaim = ArrayHelper::map($tipe_klaim,'id','name');
        $filter = ArrayHelper::map($filter,'id','name');
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tipe_klaim = $request->get('tipe_klaim') ? $request->get('tipe_klaim') : '';
        $jenis_tanggal = $request->get('jenis_tanggal') ? $request->get('jenis_tanggal') : '';
        $tanggal = $request->get('tanggal') ? $request->get('tanggal') : '';

        $tgl_awal = null;
        $tgl_akhir = null;

        try {
            $response = $this->_restPenjaminAsuransi->get(
                'inf-pasien-eklaim/index?',
                [
                    'query' => [
                        'tipe_klaim' => $tipe_klaim,
                        'jenis_tanggal' => $jenis_tanggal,
                        'tanggal' => $tanggal
                    ]
                ]
            );
            $body = json_decode($response->getBody(), True);
            $response = $body['response'];
            $data = array(
                'rajal' => 0,
                'ranap' => 0,
                'total' => 0,
                'unsend' => 0,
                'send' => 0,
                'aksi' => '<button type="button" id="btn-detail-klaim" data-tipe-klaim="' . $tipe_klaim . '" data-jenis-tanggal="' . $jenis_tanggal . '" data-tanggal="' . $tanggal . '" class="btn btn-xs btn-info" style="padding-left: 8px !important;">Tampilkan Klaim</button>',
            );
            if ($response) {
                foreach ($response as $key => $value) {
                    $date = strtotime($value['tgl_keluar']);
                    if (!$tgl_awal) {
                        $tgl_awal = $date;
                        $tgl_akhir = $date;
                    } else {
                        if ($tgl_awal > $date) {
                            $tgl_awal = $date;
                        } else if ($tgl_akhir < $date) {
                            $tgl_akhir = $date;
                        }
                    }

                    if ($value['tipe'] == 'RJ') {
                        $data['rajal'] += (int)$value['total'];
                    }
                    if ($value['tipe'] == 'RI') {
                        $data['ranap'] += (int)$value['total'];
                    }
                    $data['total'] += (int)$value['total'];
                    $data['unsend'] += (int)$value['belum_kirim'];
                    $data['send'] += (int)$value['sudah_kirim'];
                }
            }

            $jenis_rawat = 3;
            $tipe_tanggal = 1;

            if ($tipe_klaim == 'RI') {
                $jenis_rawat = 1;
            } else if ($tipe_klaim == 'RJ') {
                $jenis_rawat = 2;
            }
     
            $klaim = [
                'tgl_awal'  => date('Y-m-d', $tgl_awal),
                'tgl_akhir' => date('Y-m-d', $tgl_akhir),
                'jenis_rawat' => $jenis_rawat,
                'tipe_tanggal' => $tipe_tanggal,
            ];
            $response = [
                'total'     => count($response),
                'klaim'     => $klaim,
                'data'      => $data
            ];

            return $response;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tipe_klaim = $request->get('tipe_klaim') ? $request->get('tipe_klaim') : '';
        $jenis_tanggal = $request->get('jenis_tanggal') ? $request->get('jenis_tanggal') : '';
        $tanggal = $request->get('tanggal') ? $request->get('tanggal') : '';
        $data = array();
        $total_klaim = 0;
        $total_rs = 0;

        try {
            $response = $this->_restPenjaminAsuransi->get(
                'inf-pasien-eklaim/get-list?',
                [
                    'query' => [
                        'tipe_klaim' => $tipe_klaim,
                        'jenis_tanggal' => $jenis_tanggal,
                        'tanggal' => $tanggal
                    ]
                ]
            );
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];
            if ($body) {
                $no = 0;
                foreach ($body as $key => $value) {
                    $tarif_klaim = $value['tarif_klaim'] ? $value['tarif_klaim'] : 0;
                    $total_tarifrs = $value['total_tarifrs'] ? $value['total_tarifrs'] : 0;
                    $tambahan_biaya = 0;
                    $total_klaim += $tarif_klaim;
                    $total_rs += $total_tarifrs;

                    $diagnosa_primer = '';
                    if ($value['diagnosa_primer']) {
                        $exp = explode('#', $value['diagnosa_primer']);
                        $diagnosa_primer = $exp[0];
                        // foreach ($exp as $val) {
                        //     $diagnosa_primer .= $val .'<br>'; 
                        // }
                    }

                    $icd = $diagnosa_primer;
                    if ($value['diagnosa_sekunder']) {
                        $icd .=  $value['diagnosa_sekunder'] . ' <br> ';
                    }

                    $cbg_icd = $value['cbg'];
                    $value['cbg_icd'] = $cbg_icd . '/ <br>' . $diagnosa_primer;

                    $sp_procedure_kode = $value['sp_procedure_kode'] ? $value['sp_procedure_kode']  . '<br>' : '';
                    $sp_prosthesis_kode = $value['sp_prosthesis_kode'] ? $value['sp_prosthesis_kode'] . '<br>' : '';
                    $sp_investigation_kode = $value['sp_investigation_kode'] ? $value['sp_investigation_kode'] . '<br>' : '';
                    $sp_drug_kode   = $value['sp_drug_kode'] ? $value['sp_drug_kode'] . '<br>' : '';

                    $value['special_all'] = $sp_procedure_kode . $sp_prosthesis_kode . $sp_investigation_kode . $sp_drug_kode;
                    $value['special_all'] = $value['special_all'] ? $value['special_all'] : '-';

                    $value['tgl_masuk_1'] = date('d-m-Y', strtotime($value['tgl_masuk']));
                    $value['tgl_keluar_1'] = date('d-m-Y', strtotime($value['tgl_keluar']));

                    $value['tgl_masuk'] = date('d-m-Y H:i', strtotime($value['tgl_masuk']));
                    $value['tgl_keluar'] = date('d-m-Y H:i', strtotime($value['tgl_keluar']));
                    $value['pasien'] = $value['nama_pasien'] . ' <br> ' . $value['no_rekam_medik'];
                    $value['icd']   = $icd;
                    $value['special_group'] = $value['special_group'] ? $value['special_group'] : '-';
                    $value['tarif_klaim'] = DocoHelpers::formatNumber($tarif_klaim);
                    $value['total_tarifrs'] = DocoHelpers::formatNumber($total_tarifrs);
                    $value['terkirim'] = $value['is_terkirim'] ? '<span class="text-success">Terkirim</span>' : '<span class="text-danger">Belum Terkirim</span>';
                    $value['status_kirim'] = $value['is_terkirim'] ? '<span class="text-success">Terkirim</span>' : '<span class="text-danger">Klaim belum terkirim ke Pusat Data Kementerian Kesehatan</span>';
                    $value['adl_cronic'] = $value['adl_cronic'] ? DocoHelpers::formatNumber($value['adl_cronic']) : 0;
                    $value['adl_subacute'] = $value['adl_subacute'] ? DocoHelpers::formatNumber($value['adl_subacute']) : 0;
                    $value['cara_keluar'] = $value['cara_keluar'] ? $value['cara_keluar'] : '-';
                    $value['group_tarif'] = $value['group_tarif'] ? DocoHelpers::formatNumber($value['group_tarif']) : 0;
                    $value['special_group'] = $value['special_group'] ? $value['special_group'] : '-';
                    $value['sp_procedure_kode'] = $value['sp_procedure_kode'] ? $value['sp_procedure_kode'] : '-';
                    $value['sp_prosthesis_kode'] = $value['sp_prosthesis_kode'] ? $value['sp_prosthesis_kode'] : '-';
                    $value['sp_investigation_kode'] = $value['sp_investigation_kode'] ? $value['sp_investigation_kode'] : '-';
                    $value['sp_drug_kode'] = $value['sp_drug_kode'] ? $value['sp_drug_kode'] : '-';
                    $value['sp_procedure_nama'] = $value['sp_procedure_nama'] ? $value['sp_procedure_nama'] : '-';
                    $value['sp_prosthesis_nama'] = $value['sp_prosthesis_nama'] ? $value['sp_prosthesis_nama'] : '-';
                    $value['sp_investigation_nama'] = $value['sp_investigation_nama'] ? $value['sp_investigation_nama'] : '-';
                    $value['sp_drug_nama'] = $value['sp_drug_nama'] ? $value['sp_drug_nama'] : '-';
                    // $value['infoTxt'] = "INACBG @ ".date('d M Y H:i').' - '.DocoConstants::DEFAULT_KELAS_INACBG.' - TARIF : '.$value['jenis_tarif'];
                    $value['infoTxt'] = $value['info'];
                    $value['spesial_procedure'] = $value['spesial_procedure'] ? DocoHelpers::formatNumber($value['spesial_procedure']) : 0;
                    $value['spesial_prosthesis'] = $value['spesial_prosthesis'] ? DocoHelpers::formatNumber($value['spesial_prosthesis']) : 0;
                    $value['spesial_investigation'] = $value['spesial_investigation'] ? DocoHelpers::formatNumber($value['spesial_investigation']) : 0;
                    $value['spesial_drug'] = $value['spesial_drug'] ? DocoHelpers::formatNumber($value['spesial_drug']) : 0;

                    $value['is_tambahan_biaya'] = (($value['kelas'] == 'kelas VIP') || ($value['kelas'] == 'kelas VVIP')) ? true : false;
                    $value['tambahan_biaya'] = $value['tambahan_biaya'] ? DocoHelpers::formatNumber($value['tambahan_biaya']) : '';

                    if (($value['kelas'] == 'kelas VIP') || ($value['kelas'] == 'kelas VVIP')) {
                        $tambahan_biaya = 'Rp. ' . DocoHelpers::formatNumber($value['total_naikkelas']) . ' - Rp. ' . DocoHelpers::formatNumber($value['total_kelaspelayanan']) . ' + ( Rp. ' . DocoHelpers::formatNumber($value['total_kelaspelayanan']) . ' x ' . $value['persen_tambahan'] . '% )';
                    } else {
                        $tambahan_biaya = 'Rp. ' . DocoHelpers::formatNumber($value['total_naikkelas']) . ' - Rp. ' . DocoHelpers::formatNumber($value['total_kelaspelayanan']);
                    }

                    $value['info_tambahan_biaya'] = $tambahan_biaya;
                    $value['prosedur_bedah'] = $value['prosedur_bedah'] ? DocoHelpers::formatNumber($value['prosedur_bedah']) : 0;
                    $value['prosedur_nonbedah'] = $value['prosedur_nonbedah'] ? DocoHelpers::formatNumber($value['prosedur_nonbedah']) : 0;
                    $value['konsultasi'] = $value['konsultasi'] ? DocoHelpers::formatNumber($value['konsultasi']) : 0;
                    $value['tenaga_ahli'] = $value['tenaga_ahli'] ? DocoHelpers::formatNumber($value['tenaga_ahli']) : 0;
                    $value['keperawatan'] = $value['keperawatan'] ? DocoHelpers::formatNumber($value['keperawatan']) : 0;
                    $value['penunjang'] = $value['penunjang'] ? DocoHelpers::formatNumber($value['penunjang']) : 0;
                    $value['radiologi'] = $value['radiologi'] ? DocoHelpers::formatNumber($value['radiologi']) : 0;
                    $value['laboratorium'] = $value['laboratorium'] ? DocoHelpers::formatNumber($value['laboratorium']) : 0;
                    $value['pelayanan_darah'] = $value['pelayanan_darah'] ? DocoHelpers::formatNumber($value['pelayanan_darah']) : 0;
                    $value['rehabilitasi'] = $value['rehabilitasi'] ? DocoHelpers::formatNumber($value['rehabilitasi']) : 0;
                    $value['kamar_akomodasi'] = $value['kamar_akomodasi'] ? DocoHelpers::formatNumber($value['kamar_akomodasi']) : 0;
                    $value['rawat_intensif'] = $value['rawat_intensif'] ? DocoHelpers::formatNumber($value['rawat_intensif']) : 0;
                    $value['obat'] = $value['obat'] ? DocoHelpers::formatNumber($value['obat']) : 0;
                    $value['alkes'] = $value['alkes'] ? DocoHelpers::formatNumber($value['alkes']) : 0;
                    $value['bmhp'] = $value['bmhp'] ? DocoHelpers::formatNumber($value['bmhp']) : 0;
                    $value['sewa_alat'] = $value['sewa_alat'] ? DocoHelpers::formatNumber($value['sewa_alat']) : 0;
                    $value['obat_kemoterapi'] = $value['obat_kemoterapi'] ? DocoHelpers::formatNumber($value['obat_kemoterapi']) : 0;
                    $value['obat_kronis'] = $value['obat_kronis'] ? DocoHelpers::formatNumber($value['obat_kronis']) : 0;

                    $lama_inap = '';
                    if ($value['tipe'] == DocoConstants::SINGKATAN_RI) {
                        $lama_inap = ' (' . $value['los'] . ' Hari)';
                        $value['jenis_rawat_group'] = $value['jenis_rawat'] . ' Kelas ' . $value['kelas_hak'] . $lama_inap;
                    } else {
                        $poli = '/ Kelas Reguler';
                        if (is_numeric($value['tarif_polieksekutif'])) {
                            $poli = '/ Kelas Eksekutif';
                        }
                        $value['jenis_rawat_group'] = $value['jenis_rawat'] . $poli;
                    }

                    $value['tarif_polieksekutif'] = is_numeric($value['tarif_polieksekutif']) ? DocoHelpers::formatNumber($value['tarif_polieksekutif']) : null;

                    $value['kelas_hak'] = $value['kelas_hak'] ? $value['kelas_hak'] : '';
                    $value['no_asuransi'] = $value['no_asuransi'] ? $value['no_asuransi'] : '';



                    array_push($data, $value);
                }
            }

            $response = [
                'total' => count($data),
                'total_klaim' => DocoHelpers::formatNumber($total_klaim),
                'total_rs' => DocoHelpers::formatNumber($total_rs),
                'data'  => $data
            ];

            return $response;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetProsedur()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $klaiminacbg_id = $request->get('klaiminacbg_id');

        try {
            $response = $this->_restPenjaminAsuransi->get(
                'inf-pasien-eklaim/get-prosedur?',
                [
                    'query' => [
                        'klaiminacbg_id' => $klaiminacbg_id
                    ]
                ]
            );
            $body = json_decode($response->getBody(), True);
            $body = $body['response'];

            $data = [
                'icd_10' => [],
                'icd_9'  => []
            ];

            if ($body) {
                foreach ($body as $key => $value) {
                    if (in_array($value['icd_versi'], DocoConstants::$icd_utama)) {
                        array_push($data['icd_10'], $value);
                    } else if (in_array($value['icd_versi'], DocoConstants::$icd_sekunder)) {
                        array_push($data['icd_9'], $value);
                    }
                }
            }

            usort($data['icd_10'], function ($a, $b) {
                return $a['is_icdprimer'] > $b['is_icdprimer'] ? -1 : 1;
            });

            return $data;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetail()
    {
        $title = $this->_title;
        $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/generate-api');
        $response = json_decode($response->getBody(),TRUE);
        $response = $response['response'];

        return $this->render('detail', get_defined_vars());   
    }

    public function actionEklaim($id, $admisi)
    {
        $request = Yii::$app->request;
        $title = 'E-Klaim INACBGS';
        $info = [];
        $opsi = [];
        $id_dec = DocoHelpers::decrypt($id);
        $admisi_dec = DocoHelpers::decrypt($admisi);
        $state = false;
        $isAjukan = false;
        $detailDiagnosa = json_encode([]);

        try {
            $response = $this->_restPenjaminAsuransi->get('inf-pasien-ranap-bpjs/get-data', ['query'=>['id'=>$id_dec, 'admisi'=>$admisi_dec]]);
            $body = json_decode($response->getBody(), true);
            $dataInfo = $body['response']['info'];
            $detail = $body['response']['detailtarif'];
            $opsi = $body['response']['opsi'];
            $dataDiagnosa = $body['response']['diagnosa'];

            $diagnosa9 = [];
            $diagnosa10 = [];
            $temp1 = [];
            $temp2 = [];
            if ($dataDiagnosa) {
                foreach ($dataDiagnosa as $key => $value) {
                    if($value['is_icdprimer'] == 'true'){
                        $icdPrimer = $value['diagnosa_kode'];
                    }
                    if (!isset($temp1[$value['diagnosa_kode']])) {
                        $temp1[$value['diagnosa_kode']] = array(
                            'diagnosa_nama' => $value['diagnosa_nama'],
                            'diagnosa_kode' => []
                        );
                    }
                    if (!isset($temp2[$value['diagnosa_kode']])) {
                        $temp2[$value['diagnosa_kode']] = array(
                            'diagnosa_nama' => $value['diagnosa_nama'],
                            'diagnosa_kode' => []
                        );
                    }
                    if($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_TERAPI){
                        $item = '<span class="badge badge-primary">'.$value['diagnosa_kode'].'</span>';
                        array_push($temp2[$value['diagnosa_kode']]['diagnosa_kode'], $item);
                    }else {
                        if($value['is_icdprimer'] == 'true' || $icdPrimer == $value['diagnosa_kode']){
                            $item = '<span class="badge badge-warning" style="float:right">ICD Primary</span>'; 
                            array_push($temp1[$value['diagnosa_kode']]['diagnosa_kode'], $item);
                        }
                        $item = '<span class="badge badge-primary" style="float:right;margin-right: 3px">'. $value['diagnosa_kode'].'</span>';
                        array_push($temp1[$value['diagnosa_kode']]['diagnosa_kode'], $item);
                    }
                }

                if ($temp1) {
                    foreach ($temp1 as $key => $value) {
                        array_push($diagnosa10, $value);
                    }
                }
                if ($temp2) {
                    foreach ($temp2 as $key => $value) {
                        array_push($diagnosa9, $value);
                    }
                }
            }

            $cara_pulang = '';
            if (isset($opsi['carakeluar']) && $opsi['carakeluar']) {
                $key = array_search($dataInfo['carakeluar_value'], array_column($opsi['carakeluar'], 'lookup_value'));
                $cara_pulang = $opsi['carakeluar'][$key]['lookup_name'];
            }

            //Get tarif rumah sakit
            $tarif_rumah_sakit = DocoHelpers::formatNumber($this->getTotalTagihanRs($detail)); 
            $jenistarif = DocoConstants::JENIS_TARIF_INACBG;
            $inacbgtarif = DocoConstants::INACBG_TARIF;
            $tarif = $jenistarif['AP'];

            $jenisRawat = $dataInfo['instalasi_nama']; 
            $tanggal_rawat = 'Masuk : '. date('d M Y H:i:s', strtotime($dataInfo['tgl_pendaftaran'])) . '<br> Keluar : '. date('d M Y H:i:s', strtotime($dataInfo['tglpasienpulang']));
            $umur = '-';
            
            if ($dataInfo['is_naikkelas']) {
                $jenisRawat .= ' Naik/Turun Kelas';
            }
            if ($dataInfo['is_rawatintensif']) {
                $jenisRawat .= ' Rawat Intensif';
            }

            if ($dataInfo['umur']) {
                $umur = explode(" Tahun", $dataInfo['umur']);
                $umur = $umur[0].' Tahun';
            }
            
            //Kelas Pelayanan / Kelas rawat
            if($dataInfo['instalasi_id'] == 1) {
                $dataKelasRawat = [3=>'Reguler', 1=>'Eksekutif'];
            }else {
                $dataKelasRawat = [1 =>'Kelas 1', 2=>'Kelas 2', 3 => 'Kelas 3', 4=>'Kelas VIP', 5=>'Kelas VVIP'];
            }
            $kelas_pelayanan = $dataKelasRawat[$dataInfo['naik_kelas']];

            $info = [
                'carabayar_nama'=> $dataInfo['carabayar_nama'],
                'no_peserta'    => $dataInfo['no_pendaftaran'],
                'nama_pasien'   => $dataInfo['nama_pasien'] ? $dataInfo['nama_pasien'] : '-',
                'nosep'         => $dataInfo['nosep'] ? $dataInfo['nosep'] : '-',
                'jenis_rawat'   => $jenisRawat,
                'kelas_rawat'   => $kelas_pelayanan,
                'tanggal_rawat' => $tanggal_rawat,
                'hak_kelas'     => '',
                'los'           => $dataInfo['lama_rawat'],
                'umur'          => $umur,
                'adl_score'     => '',
                'adl_subacute'  => '-',
                'adl_cronic'    => '-',
                'berat_lahir'   => isset($dataInfo['berat_lahir']) ? $dataInfo['berat_lahir'] : '-',
                'dokter_penanggung_jawab'  => $dataInfo['dokter_dpjp'] ? $dataInfo['dokter_dpjp'] : '-',
                'cara_pulang'   => $cara_pulang,
                'dpjp'          => isset($dataInfo['dokter_dpjp']) && $dataInfo ? $dataInfo['dokter_dpjp'] : '-',
                'tarif_rumah_sakit' => 'Rp. '.$tarif_rumah_sakit,
                'tarif'         => $tarif,
            ];

            $prosedur = [
                'prosedur_bedah' => isset($detail['prosedur_bedah']) ? DocoHelpers::formatNumber($detail['prosedur_bedah']) : 0,
                'prosedur_nonbedah' => isset($detail['prosedur_non_bedah']) ? DocoHelpers::formatNumber($detail['prosedur_non_bedah']) : 0,
                'konsultasi' => isset($detail['konsultasi']) ? DocoHelpers::formatNumber($detail['konsultasi']) : 0,
                'tenaga_ahli' => isset($detail['tenaga_ahli']) ? DocoHelpers::formatNumber($detail['tenaga_ahli']) : 0,
                'keperawatan' => isset($detail['keperawatan']) ? DocoHelpers::formatNumber($detail['keperawatan']) : 0,
                'penunjang' => isset($detail['penunjang']) ? DocoHelpers::formatNumber($detail['penunjang']) : 0,
                'radiologi' => isset($detail['radiologi']) ? DocoHelpers::formatNumber($detail['radiologi']) : 0,
                'laboratorium' => isset($detail['laboratorium']) ? DocoHelpers::formatNumber($detail['laboratorium']) : 0,
                'pelayanan_darah' => isset($detail['pelayanan_darah']) ? DocoHelpers::formatNumber($detail['pelayanan_darah']) : 0,
                'rehabilitasi' => isset($detail['rehabilitasi']) ? DocoHelpers::formatNumber($detail['rehabilitasi']) : 0,
                'kamar_akomodasi' => isset($detail['kamar_akomodasi']) ? DocoHelpers::formatNumber($detail['kamar_akomodasi']) : 0,
                'rawat_intensif' => isset($detail['rawat_intensif']) ? DocoHelpers::formatNumber($detail['rawat_intensif']) : 0,
                'obat'  => isset($detail['obat']) ? DocoHelpers::formatNumber($detail['obat']) : 0,
                'alkes' => isset($detail['alkes']) ? DocoHelpers::formatNumber($detail['alkes']) : 0,
                'bmhp'  => isset($detail['bmhp']) ? DocoHelpers::formatNumber($detail['bmhp']) : 0,
                'sewa_alat' => isset($detail['sewa_alat']) ? DocoHelpers::formatNumber($detail['sewa_alat']) : 0,
                'obat_kemoterapi' => isset($detail['obat_kemoterapi']) ? DocoHelpers::formatNumber($detail['obat_kemoterapi']) : 0,
                'obat_kronis' => isset($detail['obat_kronis']) ? DocoHelpers::formatNumber($detail['obat_kronis']) : 0,
            ];

            $diagnosa = [
                'diagnosa_icd_10' => $diagnosa10,
                'diagnosa_icd_9'  => $diagnosa9
            ];

            $final_grouper = [];
            $_kelasBaru = '';
            $status_data_klaim = '';

            if ($dataInfo['nosep']) {
                $eklaim = $this->getKlaim($dataInfo['nosep']);
                $eklaim = json_decode($eklaim);
                $response_data = $eklaim->response->data;
                $grouper = $response_data->grouper;
                $grouper_response = $grouper->response;

                $_cbg = $grouper_response->cbg;
                $_subacute = isset($grouper_response->sub_acute) ? $grouper_response->sub_acute : [];
                $_chronic = isset($grouper_response->chronic) ? $grouper_response->chronic : [];
                $_kelasAwalBaru = $grouper_response->kelas;
                $_spesial_cmg = isset($grouper_response->special_cmg) ? $grouper_response->special_cmg : [];
                $status_data_klaim = ($response_data->kemenkes_dc_status_cd == 'sent') ? 'Terkirim' : 'Klaim belum terkirim ke Pusat Data Kementerian Kesehatan'; 
               
                if($_kelasAwalBaru == 'kelas_1') {
                    $_kelasBaru = 1;
                }
                else if($_kelasAwalBaru == 'kelas_2') {
                    $_kelasBaru = 2;
                }
                else {
                    $_kelasBaru = 3;
                }

                if ($_spesial_cmg) {
                    foreach ($_spesial_cmg as $key => $value) {
                        switch (strtolower($value->type)) {
                            case 'special procedure':
                                array_push($_spesial_prosedur, $value);
                                break;
                            case 'special prosthesis':
                                array_push($_spesial_prosthesis, $value);
                                break;
                            case 'special investigation':
                                array_push($_spesial_investigation, $value);
                                break;
                            case 'special drug':
                                array_push($_spesial_drug, $value);
                                break;
                        }
                    }
                }
            }

            $cbg = [
                'nama' => isset($_cbg->description) ? $_cbg->description : '-',
                'kode' => isset($_cbg->code) ? $_cbg->code : '-',
                'harga' => isset($_cbg->tariff) ? $_cbg->tariff :  0
            ];

            $subacute = [
                'nama' => isset($_subacute->description) ? $_subacute->description : '-',
                'kode' => isset($_subacute->code) ? $_subacute->code : '-',
                'harga'=> isset($_subacute->tariff) ? $_subacute->tariff : 0 
            ];

            $chronic = [
                'nama' => isset($_chronic->description) ? $_chronic->description : '-',
                'kode' => isset($_chronic->code) ? $_chronic->code : '-',
                'harga'=> isset($_chronic->tariff) ? $_chronic->tariff : 0 
            ];

            $spesial_prosedure = [
                'nama' => isset($_spesial_prosedur->description) ? $_spesial_prosedur->description : '-',
                'kode' => isset($_spesial_prosedur->code) ? $_spesial_prosedur->code : '-',
                'harga'=> isset($_spesial_prosedur->tariff) ? $_spesial_prosedur->tariff : 0   
            ];

            $spesial_prosthesis = [
                'nama' => isset($_spesial_prosthesis->description) ? $_spesial_prosthesis->description : '-',
                'kode' => isset($_spesial_prosthesis->code) ? $_spesial_prosthesis->code : '-',
                'harga'=> isset($_spesial_prosthesis->tariff) ? $_spesial_prosthesis->tariff : 0   
            ];

            $spesial_investigation = [
                'nama' => isset($_spesial_investigation->description) ? $_spesial_investigation->description : '-',
                'kode' => isset($_spesial_investigation->code) ? $_spesial_investigation->code : '-',
                'harga'=> isset($_spesial_investigation->tariff) ? $_spesial_investigation->tariff : 0   
            ];

            $spesial_drug = [
                'nama' => isset($_spesial_drug->description) ? $_spesial_drug->description : '-',
                'kode' => isset($_spesial_drug->code) ? $_spesial_drug->code : '-',
                'harga'=> isset($_spesial_drug->tariff) ? $_spesial_drug->tariff : 0   
            ];
            $total = $cbg['harga'] + $subacute['harga'] + $chronic['harga'] + $spesial_prosedure['harga'] + $spesial_prosthesis['harga'] + $spesial_investigation['harga'] + $spesial_drug['harga'];
            $final_grouper = [
                'infoTxt'   => "INACBG @ ".date('d M Y').' - '.DocoConstants::DEFAULT_KELAS_INACBG.' - TARIF : '.$tarif,
                'jenis_rawat'=> $dataInfo['instalasi_nama'] + ' Kelas ' +  $_kelasBaru + ' ( ' + $dataInfo['lama_rawat'] + ' Hari)',
                'group'     => $cbg,
                'subcute'   => $subacute,
                'chronic'   => $chronic,
                'spesial_prosedur'     => $spesial_prosedure,
                'spesial_prosthesis'    => $spesial_prosthesis,
                'spesial_investigasi' => $spesial_investigation,
                'spesial_drug' => $spesial_drug,
                'status_data_klaim' => $status_data_klaim,
                'status_klaim'  => '',
                'total'         => 'Rp. ' . $total
            ];

            $data = [
                'info' => $info,
                'prosedur' => $prosedur,
                'diagnosa' => $diagnosa,
                'final_grouper' => $final_grouper
            ];

            return json_encode($data);
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            $info = [];
            $diagnosa = [];
        } catch (\RequestException $e){
            var_dump($e->getMessage()); die();
            $info = [];
            $diagnosa = [];
        }
    }

    private function getTotalTagihanRs($detail)
    {
        $prosedur_bedah = isset($detail['prosedur_bedah']) ? $detail['prosedur_bedah'] : 0;
        $prosedur_nonbedah = isset($detail['prosedur_non_bedah']) ? $detail['prosedur_non_bedah'] : 0;
        $konsultasi = isset($detail['konsultasi']) ? $detail['konsultasi'] : 0;
        $tenaga_ahli = isset($detail['tenaga_ahli']) ? $detail['tenaga_ahli'] : 0;
        $keperawatan = isset($detail['keperawatan']) ? $detail['keperawatan'] : 0;
        $penunjang = isset($detail['penunjang']) ? $detail['penunjang'] : 0;
        $radiologi = isset($detail['radiologi']) ? $detail['radiologi'] : 0;
        $laboratorium = isset($detail['laboratorium']) ? $detail['laboratorium'] : 0;
        $pelayanan_darah = isset($detail['pelayanan_darah']) ? $detail['pelayanan_darah'] : 0;
        $rehabilitasi = isset($detail['rehabilitasi']) ? $detail['rehabilitasi'] : 0;
        $kamar_akomodasi = isset($detail['kamar_akomodasi']) ? $detail['kamar_akomodasi'] : 0;
        $rawat_intensif = isset($detail['rawat_intensif']) ? $detail['rawat_intensif'] : 0;
        $obat = isset($detail['obat']) ? $detail['obat'] : 0;
        $alkes = isset($detail['alkes']) ? $detail['alkes'] : 0;
        $bmhp = isset($detail['bmhp']) ? $detail['bmhp'] : 0;
        $sewa_alat = isset($detail['sewa_alat']) ? $detail['sewa_alat'] : 0;
        $obat_kemoterapi = isset($detail['obat_kemoterapi']) ? $detail['obat_kemoterapi'] : 0;
        $obat_kronis = isset($detail['obat_kronis']) ? $detail['obat_kronis'] : 0;

        $totalTagihanRs = ($prosedur_bedah + $prosedur_nonbedah + $konsultasi + $tenaga_ahli + $keperawatan + $penunjang + $radiologi + $laboratorium + $pelayanan_darah + $rehabilitasi + $kamar_akomodasi + $rawat_intensif + $obat + $alkes + $bmhp + $sewa_alat + $obat_kemoterapi + $obat_kronis);

        return $totalTagihanRs;
    }

    public function getKlaim($nosep)
    {
        if ($nosep) {
            $getklaim = [
                'metadata'=>[
                    'method'=>'get_claim_data',
                ],
                'data'=>[
                    'nomor_sep'=>$nosep,
                ]
            ];
            return DocoHelpers::restInacbgs($getklaim);     
        }
        return false;
    }

    public function actionKirimKlaimOnline()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $tgl_awal = $post['tgl_awal'];
        $tgl_akhir = $post['tgl_akhir'];
        $jenis_rawat= $post['jenis_rawat'];
        $tipe_tanggal = $post['tipe_tanggal'];
        try {
            $response = $this->_restPenjaminAsuransi->post('inf-pasien-eklaim/kirim-klaim-online', [
                'form_params'=>
                [
                    'tgl_awal'  =>$tgl_awal,
                    'tgl_akhir' =>$tgl_akhir,
                    'jenis_rawat' => $jenis_rawat,
                    'tipe_tanggal' => $tipe_tanggal
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body, 422);
        } catch (\RequestException $e) {
            $result = ['response'=>['title'=>"Terjadi Kesalahan", 'text'=>'Edit Data Klaim Gagal']];
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionKirimKlaimOnlineBatch($randString) 
    {
        $request = Yii::$app->request;
        $post = $request->get();
        $tgl_awal = $post['tgl_awal'];
        $tgl_akhir = $post['tgl_akhir'];
        $jenis_rawat= $post['jenis_rawat'];
        $tipe_tanggal = $post['tipe_tanggal'];
        $response = $this->_restPenjaminAsuransi->post('inf-pasien-eklaim/kirim-klaim-online-batch', [
            'form_params'=>
            [
                'tgl_awal'  =>$tgl_awal,
                'tgl_akhir' =>$tgl_akhir,
                'jenis_rawat' => $jenis_rawat,
                'tipe_tanggal' => $tipe_tanggal,
                'randString' => $randString
            ]
        ]);

        $body = json_decode($response->getBody(), true);
        return DocoHelpers::response($body, 200);
    }

    // public function actionGetPenjamin($assign_id = "")
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $request = Yii::$app->request;
    //     $params = '';
    //     if ($request->post()) {
    //         $depdrop_parents = $request->post('depdrop_parents');
    //         $parent_label = $depdrop_parents[0];
    //         $params = '?id='.$parent_label;
    //     }

    //     $result = [];
    //     $result['output'] = [];
    //     $result['selected'] = '';

    //     try {
    //         $response = $this->_restMaster->get('allow/get-list-penjamin'. $params);
    //         $body = json_decode($response->getBody(), True);
    //         foreach ($body['response'] as $value)
    //             $result['output'][] = [
    //                 'id' => $value['penjamin_id'],
    //                 'name' => $value['penjamin_nama']
    //             ];
    //         return $result;
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    // public function actionGetCarabayar($assign_id = "")
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $request = Yii::$app->request;
    //     $depdrop_parents = $request->post('depdrop_parents');
    //     $parent_label = $depdrop_parents[0];

    //     $result = [];
    //     $result['output'] = [];
    //     $result['selected'] = '';

    //     try {
    //         $response = $this->_restPenjaminAsuransi->get('allow/get-list-carabayar?penjamin_id='.$parent_label);
    //         $body = json_decode($response->getBody(), True);
    //         foreach ($body['response'] as $value)
    //             $result['output'][] = [
    //                 'id' => $value['carabayar_id'],
    //                 'name' => $value['carabayar_nama']
    //             ];
    //         return $result;
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    // public function actionGetInstalasi($assign_id = "")
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $request = Yii::$app->request;
    //     $depdrop_parents = $request->post('depdrop_parents');
    //     $parent_label = $depdrop_parents[0];

    //     $result = [];
    //     $result['output'] = [];
    //     $result['selected'] = '';

    //     try {
    //         $response = $this->_restPenjaminAsuransi->get('allow/get-list-instalasi?id='.$parent_label);
    //         $body = json_decode($response->getBody(), True);
    //         foreach ($body['response'] as $value)
    //             $result['output'][] = [
    //                 'id' => $value['instalasi_id'],
    //                 'name' => $value['instalasi_nama']
    //             ];
    //         return $result;
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    // public function actionGetRuangan($assign_id = "")
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $request = Yii::$app->request;
    //     $params = '';
    //     if ($request->post()) {
    //         $depdrop_parents = $request->post('depdrop_parents');
    //         $parent_label = $depdrop_parents[0];
    //         $params = '?instalasi_id='.$parent_label;
    //     }

    //     $result = [];
    //     $result['output'] = [];
    //     $result['selected'] = '';

    //     try {
    //         $response = $this->_restPenjaminAsuransi->get('allow/get-list-ruangan'. $params);
    //         $body = json_decode($response->getBody(), True);
    //         foreach ($body['response'] as $value)
    //             $result['output'][] = [
    //                 'id' => $value['ruangan_id'],
    //                 'name' => $value['ruangan_nama']
    //             ];
    //         return $result;
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    // public function actionSkd($id, $admisi)
    // {
    //     $title = 'Surat Keterangan Dokter';
    //     $id_dec = DocoHelpers::decrypt($id);
    //     $admisi_dec = DocoHelpers::decrypt($admisi);

    //     $model = new SuratKeteranganDokterForm;
    //     try {
    //         $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/get-attributes',[
    //             'query' => [
    //                 'id' => $id_dec,
    //                 'admisi' => $admisi_dec
    //             ]
    //         ]);
    //         $response = json_decode($response->getBody(),true);
    //         $response = $response['response'];
    //         $data = $response['header'];
    //         $skd = $response['skd'];
    //         $model->attributes = $skd;
    //         $sebab = $response['sebab'];
    //         $dataDetail = $response['detail'];
    //         $defaultDokter = $response['default_dokter'];
    //         $detail = [];

    //         foreach ($dataDetail as $val) {
    //             if ($val["kelompokdiagnosa_id"] == $this->_id_diagnosa_utama) {
    //                 $detail["Utama"]["id"] = $val["diagnosa_id"];
    //                 $detail["Utama"]["text"] = $val["diagnosa_kode"]."-".$val["diagnosa_nama"];
    //             }

    //             if ($val["kelompokdiagnosa_id"] == $this->_id_diagnosa_penyerta) {
    //                 $detail["Peyerta"][] = [
    //                     "id" => $val["diagnosa_id"],
    //                     "text" => $val["diagnosa_kode"]."-".$val["diagnosa_nama"]
    //                 ];
    //             }
    //         }
    //         // if (count($dataDetail)) {
    //         //     $penyerta = json_decode($dataDetail['diagnosa_penyerta'],true);
    //         //     $detail = [
    //         //         'Utama' => json_decode($dataDetail['diagnosa_utama'],true),
    //         //         'Peyerta' => $penyerta,
    //         //     ];
    //         // }
    //     } catch (RequestException $e) {
    //         $data = $skd = $sebab = $detail = $defaultDokter = [];
    //     }
    //     return $this->render('form',get_defined_vars());
    // }

    // public function actionGetIcd()
    // {
    //     $request = Yii::$app->request;
    //     $type_icd = $request->get('type');
    //     $term = $request->get('term');
    //     try {
    //         $response = $this->_restPenjaminAsuransi->get('informasi-pasien-non-bpjs/get-icd',[
    //             'query' => [
    //                 'type' => $type_icd,
    //                 'term' => $term,
    //             ]
    //         ]);
    //         $response = json_decode($response->getBody(),true);
    //         $response = $response['response'];
    //         $data = [];
    //         foreach ($response as $value) {
    //             $data[] = [
    //                 'id' => $value['diagnosa_id'],
    //                 'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_namalainnya'],
    //                 'kode' => $value['diagnosa_kode'],
    //             ];
    //         }
    //     } catch (RequestException $e) {
    //         $data = [];
    //     }

    //     return DocoHelpers::response([
    //         'result' => $data
    //     ]);
    // }

    // public function actionGetDokter()
    // {
    //     $request = Yii::$app->request;
    //     $term = $request->get('term');
    //     try {
    //         $response = $this->_restPenjaminAsuransi->get('allow/pegawai-medis',[
    //             'query' => [
    //                 'term' => $term,
    //             ]
    //         ]);
    //         $response = json_decode($response->getBody(),true);
    //         $response = $response['response'];
    //         $data = [];
    //         foreach ($response as $value) {
    //             $data[] = [
    //                 'id' => $value['pegawai_id'],
    //                 'text' => $value['nama_pegawai'],
    //             ];
    //         }
    //     } catch (RequestException $e) {
    //         $data = [
    //             'messages' => $e->getMessage()
    //         ];
    //     }

    //     return DocoHelpers::response([
    //         'result' => $data
    //     ]);
    // }

    // public function actionEklaim($id, $admisi)
    // {

    // }

    // private function getValueNaikKelas($info) 
    // {
    //     $naikKelasKlaim = !is_null($info['naik_kelas_klaim']) ? $info['naik_kelas_klaim'] : $info['naik_kelas'];

    //     if(stripos(strtolower($naikKelasKlaim), 'kelas_') !== false) {
    //         if($naikKelasKlaim == 'kelas_5' || $naikKelasKlaim == 5) {
    //             $naikKelas = 5;
    //         }
    //         elseif($naikKelasKlaim == 'kelas_4' || $naikKelasKlaim == 4) {
    //             $naikKelas = 4;
    //         }
    //         elseif($naikKelasKlaim == 'kelas_3' || $naikKelasKlaim == 3) {
    //             $naikKelas = 3;
    //         }
    //         elseif($naikKelasKlaim == 'kelas_2' || $naikKelasKlaim == 2) {
    //             $naikKelas = 2;
    //         }
    //         else {
    //             $naikKelas = 1;
    //         }
    //     }
    //     else {
    //         if($naikKelasKlaim == 'vip') {
    //             $naikKelas = 4;
    //         }
    //         elseif($naikKelasKlaim == 'vvip') {
    //             $naikKelas = 5;
    //         }
    //         else {
    //             $naikKelas = $naikKelasKlaim;
    //         }
    //     }

    //     return $naikKelas;
    // }
}
