<?php 

/**
 * @author Randy Vianda Putra
 * @todo Riwayat Pasien
 * @copyright 14 Agustus 2018 aweutist
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\components\Traits\HistoryPatientTrait;
use app\components\Traits\Pelayanan\TerraMedikTrait;
// use Doco\radiologi\models\UploadHasilForm;

class RiwayatPasienController extends DocoController
{
    use HistoryPatientTrait;
    use TerraMedikTrait;

    protected $_title = 'Riwayat Pasien';
    protected $_module = '/rajal/riwayat-pasien';
    protected $_restRajal;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->restGeneral = $this->_restRajal;
        $this->type = 'RJ';
    }

    public function actionIndex($norm)
    {
        $title = $this->_title;
        // $response = $this->_restRajal->get('tra-pemeriksaan/get-pasien?id=' . $pendaftaran_id);
        // $response = json_decode($response->getBody(), true);
        // $data_pasien = $response["response"]["data"];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData($norm)
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->request('get', 'riwayat-pasien/index?norm=' . $norm, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $draw = $request->get('draw', 1);
            $result = [];
            $result['data'] = [];
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $btn_pelayanan = '';
                $btn_penunjang = '';
                $cara_keluar = '';
                $id_pendaftaran_encrypt = DocoHelpers::encrypt($value['pendaftaran_id']);
                $id_pendaftaran = $value['pendaftaran_id'];

                if (($value['r_anamesa'] == 1) && ($value['pasienadmisi_id'] == '')) {
                    $btn_pelayanan .= Html::a('Asesmen',
                        [
                            '/rajal/pemeriksaan/cetak-anamnesa?pendaftaran_id='.$id_pendaftaran
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if (($value['r_pemeriksaanfisik'] == 1 )&& ($value['pasienadmisi_id'] == '')) {
                    $btn_pelayanan .= Html::a('Pemeriksaan Fisik',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if (($value['r_diagnosa'] == 1) && ($value['pasienadmisi_id'] == '')) {
                    $btn_pelayanan .= Html::a('Diagnosa',
                        [
                            '/rajal/pemeriksaan/export-pdf-diagnosa?pendaftaran_id='.$id_pendaftaran_encrypt
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if (($value['r_konsulpoli'] == 1) && ($value['pasienadmisi_id'] == '')) {
                    $btn_pelayanan .= Html::a('Konsul Poli',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ((($value['r_tindakan'] == 1) || ($value['r_bmhp'] == 1)) && ($value['pasienadmisi_id'] == '')) {
                    $btn_pelayanan .= Html::a('Tindakan & BMHP',
                        [
                            '/rajal/pemeriksaan/cetak-tindakan?id='.$id_pendaftaran_encrypt
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if (($value['r_reseptur'] == 1) && ($value['pasienadmisi_id'] == '')) {
                    $btn_pelayanan .= Html::a('Reseptur',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if (($value['r_resumemedis_rj_rd'] == 1) && ($value['pasienadmisi_id'] == '')) {
                    $btn_pelayanan .= Html::a('Resume Medis',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_asesmenawal'] == 1) {
                    $btn_pelayanan .= Html::a('Asesmen Awal Keperawatan',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_rekonsiliasiobat'] == 1) {
                    $btn_pelayanan .= Html::a('Rekonsilasi Obat',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_asesmenmedis'] == 1) {
                    $btn_pelayanan .= Html::a('Asesmen Awal Medis',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_dischargeplan'] == 1) {
                    $btn_pelayanan .= Html::a('Discharge Planning',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_cppt'] == 1) {
                    $btn_pelayanan .= Html::a('CPPT',
                        [
                            '/ranap/pemeriksaan-rawat-inap/cetak-list-cppt?id='.$id_pendaftaran_encrypt
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_instruktitindakan'] == 1) {
                    $btn_pelayanan .= Html::a('Instruksi & Implementasi',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_instruktitindakanbmhp'] == 1) {
                    $btn_pelayanan .= Html::a('Tindakan & BMHP',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_pemberianobat'] == 1) {
                    $btn_pelayanan .= Html::a('Pemberian Obat',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_permintaankonsul'] == 1) {
                    $btn_pelayanan .= Html::a('Permintaan Konsul',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_pindahkamar'] == 1) {
                    $btn_pelayanan .= Html::a('Pindah Kamar',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_resumemedis_ri'] == 1) {
                    $btn_pelayanan .= Html::a('Resume Medis',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['r_visitedokter'] == 1) {
                    $btn_pelayanan .= Html::a('Visite Dokter',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }

                if ($value['p_laboratorium'] == 1) {
                    $btn_penunjang .= Html::a('Laboratorium',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['p_radiologi'] == 1) {
                    $btn_penunjang .= Html::a('Radiologi',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }
                if ($value['p_operasi'] == 1) {
                    $btn_penunjang .= Html::a('Bedah Sentral',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }

                if (!empty($value['tglpasienpulang'])) {
                    $cara_keluar .= date('d M Y H:i:s', strtotime($value['tglpasienpulang'])) . "<br>";
                    $cara_keluar .= $value['carakeluar_nama'] . "<br>";
                }
                if ($value['carakeluar_id'] == 5) {
                    $cara_keluar .= Html::a('Rujuk',
                        [
                            'index?norm='.$norm
                        ], [
                            'class'=>'btn btn-info btn-sm btn-riwayat',
                            'target' => 'blank'
                        ]
                    );
                }

                $data[] = [
                    'pendaftaran_id' => $value['pendaftaran_id'],
                    'tgl_pendaftaran' => date('d M Y', strtotime($value['tgl_pendaftaran'])) . ' / ' . $value['no_pendaftaran'],
                    'ruangan_pend' => $value['ruangan_pend'],
                    'dok_rjrd' => $value['dok_rjrd'],
                    'cara_keluar' => $cara_keluar,
                    'aksi_pelayanan' => $btn_pelayanan,
                    'aksi_penunjang' => $btn_penunjang,
                ];
    
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}