<?php
/*
@author: Ardi Pratama
*/

// Namespace
namespace app\modules\v1\controllers;

// Using

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use yii\db\Expression;

// Using model
use app\modules\v1\models\CpptRjDetailV;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\SoapRj;
use app\modules\v1\models\SoapRjView;
use app\modules\v1\models\RiwayatSoapTerra;
use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\ProgramTerapi;
use app\modules\v1\models\ProgramTerapiRajal;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\UploadPayload;
use Doco\Traits\GeneralCpptTrait;
use Doco\Traits\GeneralResepturTrait;
use Doco\models\KelompokPegawai;
use Doco\models\TerapiRjView;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Traits\TindakanPenunjangTrait;
use Exception;
use yii\web\UploadedFile;

// Class
class CpptController extends DocoActiveController
{

    use GeneralCpptTrait;
    use GeneralResepturTrait;
    use TindakanPenunjangTrait;

    // Model class
    public $modelClass = 'app\modules\v1\models\CpptRjDetailV';
    protected $_groupingTipeTindakanBMHP = "Tindakan & BMHP";
    public $konfigCpptKosong;
    public $konfigEditCpptCoret;

    public $laporanTindakanModel;

    public function init()
    {
        parent::init();
        $this->laporanTindakanModel = (new SoapRjView);
        $this->konfigSystemCppt();
    }

    // Verbs
    public function verbs()
    {
        // Parent
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    // Acions
    public function actions()
    {
        // Parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);
        unset($actions['delete']);

        // Return
        return $actions;
    }

    public function konfigSystemCppt() {
        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong','is_edit_cppt_coret'])->asArray()->one();
        $this->konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];
        $this->konfigEditCpptCoret = $konfigCppt['is_edit_cppt_coret'];
    }

    public function actionIndex()
    {
        $params = Yii::$app->request;
        $pendaftaran_id = $params->get('pendaftaran_id', 0);
        $source = $params->get('source', null);

        $page = $params->get('page', 1);
        $limit = $params->get('per-page', 10);
        $offset = ($page - 1) * $limit;
        $getPasien = Pendaftaran::find()->select([
            'pasien_id', 'ruangan_id', 'instalasi_id'
        ])->asArray()->where([
            'pendaftaran_id' => $pendaftaran_id
        ])->one();
        if (empty($getPasien['pasien_id'])) {
            return [
                'status' => 422,
                'messages' => 'Pendaftaran Tidak di temukan'
            ];
        }
        $orderSoap = (new DocoConstansId)->actionGetAdditional('orderby_soap');
        $ruangan_id = $params->get('ruangan_id', null);
        $pegawai_id = $params->get('pegawai_id', null);
        $tgl_cppt = $params->get('tgl_cppt', null);
        $filter_pasien = $params->get('filter_pasien', null);
        $kelompokpegawai_id = $params->get('filter_kelompokpegawai_id', null);
        if (empty($ruangan_id) && empty($pegawai_id) && $getPasien['instalasi_id'] != DocoConstants::INST_ID_MCU && $getPasien['instalasi_id'] != DocoConstants::INST_ID_RJ) {
            $ruangan_id = $getPasien['ruangan_id'];
        }
        $orders = $params->get('order', []);
        $columns = $params->get('columns', []);
        $orderBy = [];

        if (empty($orderBy)) {
                $orderBy ='tgl_soaprj '.$orderSoap.',created_date '.$orderSoap;
        } else {
                $orderBy = $orderBy;
        }


        // $data_cppt = CpptRjDetailV::find()
        //     ->where(['pasien_id' => $getPasien['pasien_id']]);
        // $data = $this->actionGetRawDataTindakanBmhp($getPasien['pasien_id'], $source, null/*$pendaftaran_id*/, $ruangan_id, $pegawai_id, $orderBy);

        $data = $this->getRawDataTindakanBmhp(
            $source,
            [
                'pasien_id' => $getPasien['pasien_id'],
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_id' => $ruangan_id,
                'pegawai_id' => $pegawai_id,
                'kelompokpegawai_id' => $kelompokpegawai_id,
                'tgl_cppt' => $tgl_cppt,
                'filter_pasien' => isset($filter_pasien) && $filter_pasien == true ? true : null,
            ],
            $orderBy
        );

        // $totalCount = $data_cppt->count();

        return [
            'data' => $data['mapping_jenis'],
            'totalCount' => count($data['mapping_jenis']['soap'])
        ];
    }

    private function getPendaftaranIdsFisioRajal($pendaftaranId, $pasienId)
    {
        $pendaftaranIds = [];
        $query = (new \yii\db\Query())
            ->select([
                'programterapirajal_r.pendaftaran_id',
                'programterapirajal_r.programterapi_id',
                'programterapirajal_r.pasienmasukpenunjang_id',
                'programterapi_t.tgl_permintaan',
            ])
            ->from('programterapi_t')
            ->innerJoin('programterapirajal_r', 'programterapi_t.programterapi_id = programterapirajal_r.programterapi_id')
            ->innerJoin('pendaftaran_t', 'programterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id');
        if (!$pendaftaranId) {
            $query->where(['pendaftaran_t.pasien_id' => $pasienId]);
        } else {
            $query->where(['pendaftaran_t.pendaftaran_id' => $pendaftaranId]);
        }
        $programTerapiRajals = $query->all();
        foreach ($programTerapiRajals as $key => $value) {
            $pendaftaran_id = ArrayHelper::getValue($value, 'pendaftaran_id');
            array_push($pendaftaranIds, $pendaftaran_id);
        }
        return $pendaftaranIds;
    }

    private function whereClauseFisioPendaftaranIds($query, $pendaftaranId, $pasienId)
    {
        $pendaftaranIdsFisioRajal = $this->getPendaftaranIdsFisioRajal($pendaftaranId, $pasienId);
        return $query->orWhere(['in', 'pendaftaran_id', $pendaftaranIdsFisioRajal]);
    }

    public function actionGetRawDataTindakanBmhp($pasien_id, $source = null, $pendaftaran_id = null, $ruangan_id = null, $pegawai_id = null, $orderBy = ['tgl_soaprj' => SORT_ASC])
    {
        try {
            if (isset($pasien_id['all']) && $pasien_id['all'] != 1) {
                $dataGroup = SoapRjView::find()->select([
                    'soaprj_v.*',
                    'soaprj_v.pegawai_soap as nama_pegawai',
                    'soaprj_v.nama_pegawai as pegawai_soap',
                    'soaprj_v.kelompokpegawai_nama as kelompokpegawai_soap',
                    'soaprj_v.kelompokpegawai_soap as kelompokpegawai_nama',
                    'soaprj_v.spesialis_nama',
                ])->where([$pasien_id['field'] => $pasien_id['fieldId']]);

                if($this->konfigCpptKosong == TRUE) {
                    $dataGroup->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
                }
                if(!$this->konfigEditCpptCoret) {
                    $dataGroup->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
                }

                $dataGroup->orderBy($orderBy)->asArray()->all();
            } elseif ($source == 'cppt') {
                $dataGroup = SoapRjView::find(true)->select([
                    'soaprj_v.*',
                    'soaprj_v.pegawai_soap as nama_pegawai',
                    'soaprj_v.nama_pegawai as pegawai_soap',
                    'soaprj_v.kelompokpegawai_nama as kelompokpegawai_soap',
                    'soaprj_v.kelompokpegawai_soap as kelompokpegawai_nama',
                    'soaprj_v.spesialis_nama',
                ])->where(['grouping_tipe' => $this->_groupingTipeTindakanBMHP ]);

                if ( $pendaftaran_id != null) {
                    $dataGroup->andWhere([
                        'pendaftaran_id' => $pendaftaran_id
                    ]);
                } else {
                    $dataGroup->andWhere([
                        'pasien_id' => $pasien_id
                    ]);
                }

                if (!empty($ruangan_id)) {
                    $dataGroup->andWhere([
                        'ruangan_id' => $ruangan_id
                    ]);
                }

                if (!empty($pegawai_id)) {
                    $dataGroup->andWhere([
                        'pegawai_id' => $pegawai_id
                    ]);
                }

                if($this->konfigCpptKosong == TRUE) {
                    $dataGroup->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
                }
                if(!$this->konfigEditCpptCoret) {
                    $dataGroup->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
                }

                $dataGroup = $this->whereClauseFisioPendaftaranIds($dataGroup, $pendaftaran_id, $pasien_id);
                $dataGroup = $dataGroup->orderBy($orderBy)->asArray()->all();
            } else {
                $dataGroup = SoapRjView::find()->select([
                    'soaprj_v.*',
                    'soaprj_v.pegawai_soap as nama_pegawai',
                    'soaprj_v.nama_pegawai as pegawai_soap',
                    'soaprj_v.kelompokpegawai_nama as kelompokpegawai_soap',
                    'soaprj_v.kelompokpegawai_soap as kelompokpegawai_nama',
                    'soaprj_v.spesialis_nama',
                ]);
                if ( $pendaftaran_id != null) {
                    $dataGroup->andWhere([
                        'pendaftaran_id' => $pendaftaran_id
                    ]);
                } else {
                    $dataGroup->andWhere([
                        'pasien_id' => $pasien_id
                    ]);
                }

                if($this->konfigCpptKosong == TRUE) {
                    $dataGroup->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
                }
                if(!$this->konfigEditCpptCoret) {
                    $dataGroup->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
                }

                $dataGroup = $dataGroup->andWhere([
                    'not in', 'COALESCE(kelompoktindakan_id, 0)', [
                        DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN
                    ]
                ])->orderBy($orderBy)->asArray()->all();
            }

            $data_mapping = [
                'soap' => []
            ];
            $data_mapping_tindakan = [];
            $totalRow = [];
            if (isset($dataGroup) && count($dataGroup) > 0) {
                foreach ($dataGroup as $v_dataGroup) {
                    if ($v_dataGroup['grouping_tipe'] == 'Penunjang') {
                        $data_mapping[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]['list_orderpenunjang'][$v_dataGroup['no_penunjang']]['jenis'][$v_dataGroup['jenis']][] = $v_dataGroup;
                        $data_mapping[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]['list_orderpenunjang'][$v_dataGroup['no_penunjang']]['penunjang'] = $v_dataGroup;
                    } else {
                        $data_mapping[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]['jenis'][$v_dataGroup['jenis']][] = $v_dataGroup;
                    }
                    $data_mapping['soap'][$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']] = $v_dataGroup;
                    $data_mapping_tindakan[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']][] = $v_dataGroup;
                    if (!isset($totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']])) {
                        $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']]['sub_total'] = 0;
                    }
                    if (!isset($totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']])) {
                        $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']] = 0;
                    }
                    $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']]['sub_total']++;
                    $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]++;
                    $pendaftaran_id = $v_dataGroup['pendaftaran_id'];
                }
            }
            return [
                'mapping_jenis' => $data_mapping,
                'mapping_tindakan' => $data_mapping_tindakan,
                'total_row' => $totalRow
            ];
        } catch (Exception $e) {
            return [
                'mapping_jenis' => [],
                'mapping_tindakan' => []
            ];
        }
    }

    protected function getRawDataTindakanBmhp($source = null, $params = [], $orderBy = ['tgl_soaprj' => SORT_DESC])
    {
        try {
            if (isset($params['pasien_id']['all']) && $params['pasien_id']['all'] != 1) {
                $dataGroup = SoapRjView::find()->select([
                    'soaprj_v.*',
                    'soaprj_v.pegawai_soap as nama_pegawai',
                    'soaprj_v.nama_pegawai as pegawai_soap',
                    'soaprj_v.kelompokpegawai_nama as kelompokpegawai_soap',
                    'soaprj_v.kelompokpegawai_soap as kelompokpegawai_nama',
                    'soaprj_v.spesialis_nama',
                ])->orderBy([
                    'tgl_soaprj' => SORT_DESC
                ])->where([$params['pasien_id']['field'] => $params['pasien_id']['fieldId']]);
                if($this->konfigCpptKosong == TRUE) {
                    $dataGroup->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
                }
                if(!$this->konfigEditCpptCoret) {
                    $dataGroup->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
                }

                $dataGroup->orderBy($orderBy)->asArray()->all();
            } elseif ($source == 'cppt') {
                $dataGroup = SoapRjView::find(true)->select([
                    'soaprj_v.*',
                    'soaprj_v.pegawai_soap as nama_pegawai',
                    'soaprj_v.nama_pegawai as pegawai_soap',
                    'soaprj_v.kelompokpegawai_nama as kelompokpegawai_soap',
                    'soaprj_v.kelompokpegawai_soap as kelompokpegawai_nama',
                    'soaprj_v.spesialis_nama',
                ])->orderBy($orderBy)->where(['grouping_tipe' => $this->_groupingTipeTindakanBMHP ]);

                if ( $params['pendaftaran_id'] != null && !isset($params['filter_pasien'])) {
                    $dataGroup->andWhere([
                        'pendaftaran_id' => $params['pendaftaran_id']
                    ]);
                } else {
                    $dataGroup->andWhere([
                        'pasien_id' => $params['pasien_id']
                    ]);
                }

                if (!empty($params['ruangan_id'])) {
                    $dataGroup->andWhere([
                        'ruangan_id' => $params['ruangan_id']
                    ]);
                }

                if (!empty($params['pegawai_id'])) {
                    $dataGroup->andWhere([
                        'pegawai_id' => $params['pegawai_id']
                    ]);
                }

                if(!empty($params['tgl_cppt'])){
                    $start = date('Y-m-d 00:00:00');
                    $end = date('Y-m-d 23:59:00');
                    $explode = explode("-", $params['tgl_cppt']);
                    if (count($explode) == 2) {
                        $start = date_format(date_create_from_format('d/m/Y', $explode[0]), 'Y-m-d').date(' 00:00:00');
                        $end = date_format(date_create_from_format('d/m/Y', $explode[1]), 'Y-m-d').date(' 23:59:59');
                    }
                    $dataGroup->andWhere(['between', 'tgl_soaprj', $start, $end]);
                }

                if($this->konfigCpptKosong == TRUE) {
                    $dataGroup->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
                }
                if(!$this->konfigEditCpptCoret) {
                    $dataGroup->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
                }

                if (
                    !empty($params['kelompokpegawai_id']) &&
                    in_array($params['kelompokpegawai_id'], [
                        DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                        DocoConstants::KELOMPOK_PEGAWAI_DOKTER
                    ])
                ) {
                    if (
                        $kp = KelompokPegawai::find()
                            ->where(['kelompokpegawai_id' => $params['kelompokpegawai_id']])
                            ->one()
                    ) {
                        $dataGroup->andWhere([
                            'kelompokpegawai_soap' => $kp->kelompokpegawai_nama,
                        ]);
                    } else {
                        $dataGroup->andWhere([
                            'kelompokpegawai_id' => $params['kelompokpegawai_id'],
                        ]);
                    }
                }

                $dataGroup = $this->whereClauseFisioPendaftaranIds($dataGroup, $params['pendaftaran_id'], $params['pasien_id']);
                
                $dataGroup = $dataGroup->orderBy($orderBy)->asArray();
                $dataGroup = $dataGroup->all();

            } else {
                $dataGroup = SoapRjView::find()->select([
                    'soaprj_v.*',
                    'soaprj_v.pegawai_soap as nama_pegawai',
                    'soaprj_v.nama_pegawai as pegawai_soap',
                    'soaprj_v.kelompokpegawai_nama as kelompokpegawai_soap',
                    'soaprj_v.kelompokpegawai_soap as kelompokpegawai_nama',
                    'soaprj_v.spesialis_nama',
                ])->orderBy($orderBy);
                if ( $params['pendaftaran_id'] != null && !isset($params['filter_pasien'])) {
                    $dataGroup->andWhere([
                        'pendaftaran_id' => $params['pendaftaran_id']
                    ]);
                } else {
                    $dataGroup->andWhere([
                        'pasien_id' => $params['pasien_id']
                    ]);
                }

                if($this->konfigCpptKosong == TRUE) {
                    $dataGroup->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
                }
                if(!$this->konfigEditCpptCoret) {
                    $dataGroup->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
                }
                
                if (
                    !empty($params['kelompokpegawai_id']) &&
                    in_array($params['kelompokpegawai_id'], [
                        DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                        DocoConstants::KELOMPOK_PEGAWAI_DOKTER
                    ])
                ) {
                    $dataGroup->andWhere([
                        'kelompokpegawai_id' => $params['kelompokpegawai_id'],
                    ]);
                }

                $dataGroup = $dataGroup->andWhere([
                    'not in', 'COALESCE(kelompoktindakan_id, 0)', [
                        DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN
                    ]
                ])->orderBy($orderBy)->asArray()->all();
            }

            $data_mapping = [
                'soap' => []
            ];
            $data_mapping_tindakan = [];
            $totalRow = [];
            if (isset($dataGroup) && count($dataGroup) > 0) {
                foreach ($dataGroup as $v_dataGroup) {
                    if ($v_dataGroup['grouping_tipe'] == 'Penunjang') {
                        $data_mapping[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]['list_orderpenunjang'][$v_dataGroup['no_penunjang']]['jenis'][$v_dataGroup['jenis']][] = $v_dataGroup;
                        $data_mapping[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]['list_orderpenunjang'][$v_dataGroup['no_penunjang']]['penunjang'] = $v_dataGroup;
                    } else {
                        $data_mapping[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]['jenis'][$v_dataGroup['jenis']][] = $v_dataGroup;
                    }
                    $data_mapping['soap'][$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']] = $v_dataGroup;
                    $data_mapping_tindakan[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']][] = $v_dataGroup;
                    if (!isset($totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']])) {
                        $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']]['sub_total'] = 0;
                    }
                    if (!isset($totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']])) {
                        $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']] = 0;
                    }
                    $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']]['sub_total']++;
                    $totalRow[$v_dataGroup['pendaftaran_id'] . '-' . $v_dataGroup['soaprj_id']][$v_dataGroup['grouping_tipe']]++;
                    $pendaftaran_id = $v_dataGroup['pendaftaran_id'];
                }
            }
            return [
                'mapping_jenis' => $data_mapping,
                'mapping_tindakan' => $data_mapping_tindakan,
                'total_row' => $totalRow
            ];
        } catch (Exception $e) {
            return [
                'mapping_jenis' => [],
                'mapping_tindakan' => []
            ];
        }
    }


    /**
     * @controller actionCetakListCpptRajal
     * @attribute #no_pendaftaran# => no_pendaftaran
     * @attribute #nama_pasien# => nama_pasien
     * @attribute #tgl_cetak# => tgl_cetak
     * @attribute #nama_user# => nama_user
     * @attribute #table_list_cppt# => table
     * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
     * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
     * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
     * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
     * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
     * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
     * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
     * @attribute #inf_umur# => Informasi Pasien: Umur
     * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter DPJP
     * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
     * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
     * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
     **/
    public function actionCetakListCpptRajal()
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $all = $request->get('all', 0);
        $pendaftaran_id = $request->get('pendaftaran_id', 0);
        $ruangan_id = $request->get('ruangan_id', 0);

        $filterruangan_id = $request->get('filterruangan_id', null);
        $pegawai_id = $request->get('pegawai_id', null);
        $tgl_cppt = $request->get('tgl_cppt', null);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id', null);
        $nama_usercetak = $request->get('nama_usercetak', '');
        $id_usercetak = $request->get('id_usercetak', 0);
        $filter_pasien = $request->get('filter_pasien', null);

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id' => $id_usercetak])->asArray()->one();

        if (is_null($mNamaPegawai)) {
            $nama_user = $nama_usercetak;
        } else {
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }

        $resultHeader = [];
        $resultHeader = InfoKunjunganRajal::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();

        $params = [
            'all' => $all,
            'field' => ($all == 1) ? 'pasien_id' : 'pendaftaran_id',
            'fieldId' => ($all == 1) ? $resultHeader['pasien_id'] : $pendaftaran_id,
        ];

        $getPasien = Pendaftaran::find()->select([
            'pasien_id', 'ruangan_id', 'instalasi_id'
        ])->asArray()->where([
            'pendaftaran_id' => $pendaftaran_id
        ])->one();

        if (empty($getPasien['pasien_id'])) {
            return [
                'status' => 422,
                'messages' => 'Pendaftaran Tidak di temukan'
            ];
        }

        if ((is_null($ruangan_id) || empty($ruangan_id)) && $getPasien['instalasi_id'] != DocoConstants::INST_ID_MCU) {
            $ruangan_id = ArrayHelper::getValue($getPasien, 'ruangan_id');
        }

        $orders = $request->get('order', []);
        $orderBy = [];
        if (empty($orders)) {
            $orderBy = ['tgl_soaprj' => 'SORT_ASC'];
        } else {
            $orderBy = $orders;
        }

        // $data_cppt = $this->actionGetRawDataTindakanBmhp($getPasien['pasien_id'], 'cppt', null, $ruangan_id, null, $orderBy);
        $kelompokpegawai_id = $request->get('filter_kelompokpegawai_id', null);

        $data_cppt = $this->getRawDataTindakanBmhp(
            'cppt',
            [
                'pasien_id' => $getPasien['pasien_id'],
                'pendaftaran_id' => $pendaftaran_id,
                'ruangan_id' => $filterruangan_id,
                'pegawai_id' => $pegawai_id,
                'kelompokpegawai_id' => $kelompokpegawai_id,
                'tgl_cppt' => $tgl_cppt,
                'filter_pasien' => isset($filter_pasien) && $filter_pasien == true ? true : null,
            ],
            $orderBy
        );

        $no = 0;
        $numbering = 0;
        $datas = [];
        $pendId = '';
        foreach ($data_cppt['mapping_jenis']['soap'] as $k_data => $v_data) {
            $ruangan_nama = @$v_data['ruangan_nama'];
            $nama_profesi = @$v_data['nama_profesi'];
            $nama_pegawai = @$v_data['nama_pegawai'];
            $tgl_soaprj = isset($v_data['tgl_soaprj']) ? date('d/m/Y', strtotime($v_data['tgl_soaprj'])) . '<br/>' . date('H:i:s', strtotime($v_data['tgl_soaprj'])) : '';

            $datas[$numbering]['pendaftaran_id'] = $k_data;
            $datas[$numbering]['ruangan'] = $ruangan_nama . '<br/> <hr>' . $v_data['kelompokpegawai_nama'] .' - '. ucwords(strtolower($v_data['spesialis_nama'])) . '<br/><hr>' . $nama_pegawai;
            $datas[$numbering]['tgl_cppt'] = $tgl_soaprj;
            $datas[$numbering]['nama_pegawai'] = $v_data['nama_pegawai'];
            $datas[$numbering]['tgl_soaprj'] = $v_data['tgl_soaprj'];
            $datas[$numbering]['penatalaksanaan'] = nl2br($this->getPengkajian($v_data));
            $datas[$numbering]['soap_key'] = $k_data;
            $datas[$numbering]['verifikasi_dpjp'] = !empty($v_data['soaprj_id']) ? $v_data['nama_pegawai'] . ' <br /> <hr>'. date('d/m/Y H:i:s', strtotime($v_data['tgl_soaprj'])) : '';
            $datas[$numbering]['instruksi_dpjp'] = nl2br($v_data['verbal_instruksi']);
            $datas[$numbering]['is_deleted_soap'] = $v_data['is_deleted_soap'] ? $v_data['is_deleted_soap'] : false;
            $datas[$numbering]['created_date'] = $v_data['created_date'] ? date('d/m/Y / H:i:s', strtotime($v_data['created_date'])) : '';
            $datas[$numbering]['pegawai_update_nama'] = $v_data['pegawai_update_nama'] ? $v_data['pegawai_update_nama'] : '';
            $datas[$numbering]['soap_key'] = $k_data;
            $numbering++;
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d/m/Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d/m/Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_dokterdpjp#' => $resultHeader ? $resultHeader['nama_pegawai'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? $resultHeader['kelaspelayanan_nama'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#' => $resultHeader ? $resultHeader['carabayar_nama'] : '',
            '#table_list_cppt#' => $this->renderPartial('detail_cppt', [
                'data' => $datas,
            ]),
            '#no_pendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#nama_pasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        if ($request->get('only_attributes')) {
            return $print->attributes;
        }
        return $print->Output();
    }

    public function actionExportPdfCpptBgproses()
    {
        $kode_doc = 'RJ-list-cppt';
        $request = Yii::$app->request;
        $get = $request->get();
        $get['only_attributes'] = true;
        $fileName = ArrayHelper::getValue($get, 'nama_dokumen');
        $filter_pasien = ArrayHelper::getValue($get, 'filter_pasien');
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 20;
        
        $orders = $request->get('order', []);
        $orderBy = [];
        if (empty($orders)) {
            $orderBy = ['tgl_soaprj' => 'SORT_ASC'];
        } else {
            $orderBy = $orders;
        }
        $data_cppt = $this->getRawDataTindakanBmhp(
            'cppt',
            [
                'pasien_id' => null,
                'pendaftaran_id' => ArrayHelper::getValue($get, 'pendaftaran_id'),
                'ruangan_id' => ArrayHelper::getValue($get, 'filterruangan_id'),
                'pegawai_id' => ArrayHelper::getValue($get, 'pegawai_id'),
                'kelompokpegawai_id' => ArrayHelper::getValue($get, 'kelompokpegawai_id'),
                'tgl_cppt' => ArrayHelper::getValue($get, 'tgl_cppt'),
                'filter_pasien' => isset($filter_pasien) && $filter_pasien == true ? true : null,
            ],
            $orderBy
        );
        $data_cppt = ArrayHelper::getValue($data_cppt, 'mapping_jenis.soap');
        $countData = count($data_cppt);
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);

        (new RabbitBgProcess())->send([
            'uniqueStr' => $randString,
            'request' => $get,
            'token' => $auth,
            'xOwner' => $xOwner,
            'kodeDoc' => $kode_doc,
            'fileName' => $fileName,
            'is_ftp' => ArrayHelper::getValue($get, 'is_ftp'),
            'pendaftaran_id' => ArrayHelper::getValue($get, 'pendaftaran_id')
        ], 'cppt_pdf_rajal', 'import_data_cppt_pdf_rajal');

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionSendFile()
    {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            $path = 'uploads/'. $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path.'/'.$model->file;
            if($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }

    public function actionDownloadFilePdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('no_request', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }

    private function getRuanganCppt($pasien_id)
    {
        $data = SoapRj::find()
            ->select(['soaprj_t.ruangan_id', 'ruangan_m.ruangan_nama'])
            ->join('INNER JOIN', 'ruangan_m', 'soaprj_t.ruangan_id = ruangan_m.ruangan_id')
            ->where(['pasien_id' => $pasien_id])->asArray()->all();

        $data = ArrayHelper::map($data, 'ruangan_id', 'ruangan_nama');
        return $data;
    }

    private function getPengkajian($data)
    {
        $subject = isset($data['subject']) && !empty($data['subject']) ? $data['subject'] : '';
        $object = isset($data['object']) && !empty($data['object']) ? $data['object'] : '';
        $planning = isset($data['planning']) && !empty($data['planning']) ? $data['planning'] : '';
        $catatan = isset($data['catatan_dokter']) && !empty($data['catatan_dokter']) ? $data['catatan_dokter'] : '';
        $a_diag_utama = isset($data['a_diag_utama']) && !empty($data['a_diag_utama']) ? $data['a_diag_utama'] : '';
        $a_diag_penyerta = isset($data['a_diag_penyerta']) && !empty($data['a_diag_penyerta']) ? $data['a_diag_penyerta'] : '';
        $html = '';
        $html = '<ul>';

        if (!empty($subject)) {
            $html .= '<li> <b>S :</b> ' . $subject . ' </li>';
        }

        if (!empty($object)) {
            $html .= '<li> <b>O :</b> ' . $object . ' </li>';
        }
        $diagUtama = '';
        $diagPenyerta = '';

        if (!empty($a_diag_utama)) {
            $diag_utama = json_decode($a_diag_utama, true);
            $diagUtama = '<br><li> <b>Diagnosa Utama :</b> <br> - ' . @$diag_utama['text'];
        }
        if (!empty($a_diag_penyerta)) {
            $diagnosaPenyerta = json_decode($a_diag_penyerta, true);
            if ($diagnosaPenyerta != '') {
                $counter = 0;
                foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                    $text = @$valueDiagnosaPenyerta['text'];
                    if ($counter == 0) {
                        $diagPenyerta = '<li> <b>Diagnosa Penyerta :</b> <br> - ' . $text;
                    } else {
                        $diagPenyerta .= '<br> - ';
                        $diagPenyerta .= $text;
                    }
                    $counter++;
                }
            }
        }
        $space = '<br>';
        if (!empty($diagUtama) && !empty($diagPenyerta)) {
            $asesmen = $diagUtama . $space . $diagPenyerta;
        } else {
            if (empty($diagPenyerta)) {
                $asesmen = $diagUtama;
            }
            if (empty($diagUtama)) {
                $asesmen = $diagPenyerta;
            }
        }
        $html .= '<li> <b>A :</b> ' . ($asesmen) . ' </li>';

        if (!empty($planning)) {
            $html .= '<li> <b>P :</b> ' . $planning . ' </li>';
        }

        if (!empty($catatan)) {
            $html .= '<li> <b>Catatan :</b> ' . $catatan . ' </li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * This function will return existing SOAP by pendaftaran_id
     *
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSoap($pendaftaran_id, $pegawai_id)
    {
        $recordSoap = SoapRj::find()->select(['soaprj_id', 'subject', 'object', 'planning', 'a_diag_utama', 'a_diag_penyerta', 'tgl_soaprj', 'catatan_dokter', 'final','last_modified_date'])->where(compact('pendaftaran_id', 'pegawai_id'))->andWhere([
            'BETWEEN', 'tgl_soaprj', date("Y-m-d H:i:s", strtotime('-24 hours', time())), date('Y-m-d H:i:00')
        ])->andWhere([
            'final' => 0
        ])->orderBy(['soaprj_id' => SORT_DESC])->asArray()->one();
        $asmed = PemeriksaanFisik::find()->select([
        'created_date',
        'last_modified_date'
        ])->where([
            'pendaftaran_id' => compact('pendaftaran_id')
        ])->asArray()->one();
        $tgl_asmed = $tgl_soaprj = null;
        if(isset($asmed)){
            $tgl_asmed  = !empty($asmed['last_modified_date']) ? $asmed['last_modified_date'] : $asmed['created_date'];
        }
        if(isset($recordSoap)){
            $tgl_soaprj = !empty($recordSoap['last_modified_date']) ? $recordSoap['last_modified_date'] : $recordSoap['tgl_soaprj'];
        }
        $tgl_asmed_more_than_tgl_soaprj = !is_null($tgl_asmed) && !is_null($tgl_asmed) ? (($tgl_asmed > $tgl_soaprj) ? true : false) : false;
        if (empty($recordSoap) || $tgl_asmed_more_than_tgl_soaprj) {
            $recordSoap = [
                'object' => '',
                'planning' => '',
            ];
            $getAsmed = PemeriksaanFisik::find()->select([
                'pemeriksaanfisik_id',
                'tekanandarah',
                'detaknadi',
                'suhutubuh',
                'beratbadan_kg',
                'tinggibadan_cm',
                'pernapasan',
                'gcs_hasil_metode'
            ])->where([
                'pendaftaran_id' => compact('pendaftaran_id')
            ])->asArray()->one();
            $getAskep = Anamnesa::find()->select([
                'td',
                'nadi',
                'suhu',
                'tinggi_badan',
                'berat_badan',
                'rr',
                'keluhan_utama'
            ])->where([
                'pendaftaran_id' => compact('pendaftaran_id')
            ])->asArray()->one();
            if (!empty($getAsmed)) {
                $hasil_gcs = !empty($getAsmed['gcs_hasil_metode']) ? "\nHasil GCS: " . $getAsmed['gcs_hasil_metode'] : '';
                $recordSoap = [
                    'object' => "Tekanan Darah: " . $getAsmed['tekanandarah'] . "\nNadi: " . $getAsmed['detaknadi'] . "\nPernapasan: " . $getAsmed['pernapasan'] . "\nSuhu: " . $getAsmed['suhutubuh'] . "\nTinggi Badan: " . $getAsmed['tinggibadan_cm'] . "\nBerat Badan: " . $getAsmed['beratbadan_kg'] . $hasil_gcs,
                ];
            } else if (!empty($getAskep)) {
                $recordSoap = [
                    'object' => "Tekanan Darah: " . $getAskep['td'] . "\nNadi: " . $getAskep['nadi'] . "\nPernapasan: " . $getAskep['rr'] . "\nSuhu: " . $getAskep['suhu'] . "\nTinggi Badan: " . $getAskep['tinggi_badan'] . "\nBerat Badan: " . $getAskep['berat_badan'],
                ];
            }

            $employeeRecord = Pegawai::find(true)->select(['pegawai_id', 'kelompokpegawai_id'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->asArray()->one();
            if(!empty($employeeRecord) && $employeeRecord['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
                $recordSoap = [
                    'subject' => ArrayHelper::getValue($getAskep,'keluhan_utama'),
                    'object' => "Tekanan Darah: " . $getAskep['td'] . "\nNadi: " . $getAskep['nadi'] . "\nPernapasan: " . $getAskep['rr'] . "\nSuhu: " . $getAskep['suhu'] . "\nTinggi Badan: " . $getAskep['tinggi_badan'] . "\nBerat Badan: " . $getAskep['berat_badan'],
                ];
            }

            if (!empty($getAsmed['pemeriksaanfisik_id'])) {
                $areaTubuhDanTerapi = PeriksaTubuh::find()
                    ->select([
                        'periksatubuh_id',
                        'asesmenmedis_id',
                        'catatan_tubuh',
                        'bagiantubuh_m.namabagtubuh as area_tubuh',
                        'bagiantubuhdetail_m.nama_bagiantubuh as spesifik_area_tubuh'
                    ])
                    ->join('LEFT JOIN', 'bagiantubuh_m', 'bagiantubuh_m.bagiantubuh_id=periksatubuh_t.bagiantubuh_id')
                    ->join('LEFT JOIN', 'bagiantubuhdetail_m', 'bagiantubuhdetail_m.bagiantubuhdetail_id=periksatubuh_t.bagiantubuhdetail_id')
                    ->andWhere([
                        'pemeriksaanfisik_id' => $getAsmed['pemeriksaanfisik_id']
                    ])
                    ->asArray()
                    ->all();
                $terapi = '';
                if (!empty($areaTubuhDanTerapi)) {
                    foreach ($areaTubuhDanTerapi as $areaTubuh) {
                        $terapi .= $areaTubuh['area_tubuh'] . ' : ' . $areaTubuh['catatan_tubuh'] . "\n";
                    }
                }
                if (!empty($terapi)) {
                    $recordSoap['planning'] = $terapi;
                }
            }
        }
        $recordSoap['planning'] = (isset($recordSoap['planning']) ? $recordSoap['planning'] : '');// . $this->getPlanningSuggestion($pendaftaran_id);
        $recordSoap['total_hasil_radiologi'] = self::getTotalHasilRadiologi($pendaftaran_id, null);

        return $this->responseJson(200, 'Data berhasil diambil', !empty($recordSoap) ? $recordSoap : (object) null);
    }

    public function actionGetLatestCppt()
    {
        $request = Yii::$app->request;

        try {
            $last_cppt = SoapRjView::find()
                    ->select(['soaprj_id', 'subject', 'object', 'planning', 'a_diag_utama', 'a_diag_penyerta', 'tgl_soaprj']);

            if($request->get('pasien_id', null) == true){
                $last_cppt = $last_cppt->andWhere(['pasien_id' => $request->get('pasien_id')]);
            }

            if($request->get('pendaftaran_id', null) == true){
                $last_cppt = $last_cppt->andWhere(['pendaftaran_id' => $request->get('pendaftaran_id')]);
            }

            if($request->get('is_dokter', null) == true){
                $last_cppt = $last_cppt->andWhere(['kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER]);
            }

            if($request->get('is_nurse', null) == true){
                $last_cppt = $last_cppt->andWhere(['kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_PERAWAT]);
            }

            if($this->konfigCpptKosong == TRUE) {
                $last_cppt->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
            }
            if(!$this->konfigEditCpptCoret) {
                $last_cppt->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
            }

            $last_cppt = $last_cppt->andWhere(['grouping_tipe' => $this->_groupingTipeTindakanBMHP ])->orderBy(['tgl_soaprj' => SORT_DESC])->asArray()->one();

            return $this->responseJson(200, 'Data berhasil diambil', $last_cppt);


        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }

    private function getInstruksiDpjp($data)
    {
        $html = '';
        foreach( $data as $key => $item) {
            if ( $key == 'Tindakan & BMHP') {
                $tindakanBmhpArray = [];
                foreach( $data[$key] as $tindakanBmhpKey => $tindakanBmhpItem ) {
                    if ( !in_array($tindakanBmhpItem['kelompoktindakan_id'], [DocoConstants::VAR_KEL_KRCS, DocoConstants::KELOMPOK_MAKANAN]) ) {
                        if ($tindakanBmhpItem['jenis'] == 'TINDAKAN') {
                            $tindakanBmhpArray['tindakan'][] = $tindakanBmhpItem;
                        } else {
                            $tindakanBmhpArray['bmhp'][] = $tindakanBmhpItem;
                        }
                    }
                }
                if ( $tindakanBmhpArray ) {
                    $html .= ' <b>'.$key.'</b> <br />';
                    foreach ( $tindakanBmhpArray as $tindakanBmhpKey => $tindakanBmhpItems) {
                        $html .= '<b>'. ucfirst($tindakanBmhpKey) .'</b>';
                        $html .= '<ul>';
                        foreach( $tindakanBmhpItems as $index => $unit) {
                            $html .= '<li>'.$unit['instruksi']. ' - ' . $unit['qty'].'</li>';
                        }
                        $html .= '</ul>';
                    }
                    $html .= '<br/>';
                }
            } else if ($key == 'Reseptur' ) {
                $html .= ' <b>'.$key.'</b> <br />';
                $resepturArray = [];
                foreach ( $data[$key] as $resepturDataKey => $resepturDataItem) {
                    $resepturArray[$resepturDataItem['jenis']][] = $resepturDataItem;
                }
                foreach( $resepturArray as $resepturKey => $resepturType) {
                    $html .= '<b>'. $resepturKey .'</b>';
                    $html .= '<ul>';
                    foreach ($resepturType as $resepturTypeKey => $resepturTypeItem) {
                        $html .= '<li>'.$resepturTypeItem['instruksi'].' - '. $resepturTypeItem['qty'].'</li>';
                    }
                    $html .= '</ul>';
                }
                $html .= '<br/>';
            } else if ( $key == 'Penunjang' ) {
                $html .= ' <b>'.$key.'</b> <br />';
                $penunjangArr = [];
                foreach( $data[$key] as $penunjangDataKey => $penunjangDataItem ) {
                    $penunjangArr[$penunjangDataItem['ruangan_penunjang_nama'].' - '.$penunjangDataItem['instalasi_penunjang_nama']][] = $penunjangDataItem;
                }

                foreach( $penunjangArr as $penunjangItemKey => $penunjangItemData ) {
                    $html .= '<b>'. $penunjangItemKey .'</b>';
                    $html .= '<ul>';
                    foreach( $penunjangItemData as $penunjangItemDataKey => $unit) {
                        $html .= '<li>'.$unit['instruksi'].' - '.$unit['qty'].'</li>';
                    }
                    $html .= '</ul>';
                }
            }
        }
        return $html;
    }

    public function actionGetFilterCppt($pasien_id, $term, $type)
    {
        switch ($type) {
            case 'ruangan':
                $dataGroup = SoapRjView::find()
                    ->select([
                        'soaprj_v.ruangan_id as id',
                        'ruangan_m.ruangan_nama as text',
                    ])
                    ->join('LEFT JOIN', 'ruangan_m', 'soaprj_v.ruangan_id = ruangan_m.ruangan_id')
                    ->where(['pasien_id' => $pasien_id])
                    ->andWhere(['LIKE', 'LOWER(ruangan_m.ruangan_nama)', strtolower($term)]);
                break;

            default: // dokter defaultnya
                $dataGroup = SoapRjView::find()
                    ->select([
                        'soaprj_v.pegawai_id as id',
                        'pegawai_m.nama_pegawai as text',
                    ])
                    ->join('LEFT JOIN', 'pegawai_m', 'soaprj_v.pegawai_id = pegawai_m.pegawai_id')
                    ->where(['pasien_id' => $pasien_id])
                    ->andWhere(['LIKE', 'LOWER(pegawai_m.nama_pegawai)', strtolower($term)]);
                break;
        }

        if($this->konfigCpptKosong == TRUE) {
            $dataGroup->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
        }

        return $dataGroup->distinct()->asArray()->all();
    }

    private function getPlanningSuggestion($pendaftaran_id) {
        $lastDate = SoapRj::find()->select(new Expression('max(tgl_soaprj)'))
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(new Expression("COALESCE((additional_data::json->>'via_soap')::boolean, false) = true"))
            ->andWhere(['is_active' => true])
            ->scalar();
        $query = TerapiRjView::find();
        $query->select(['tgl_tindakan', 'jenis', 'grouping_tipe', 'instalasi_penunjang_id', 'tindakan_nama', 'qty', 'satuankecil_nama']);
        $query->andWhere(['pendaftaran_id' => $pendaftaran_id]);

        $query->orderBy(['tgl_tindakan' => SORT_ASC]);

        if(!empty($lastDate)) {
            $query->andWhere(['>', 'tgl_tindakan', $lastDate]);
        }

        $listInstruksi = $query->asArray()->distinct()->all();

        $planning = '';

        foreach ($listInstruksi as $instruksi) {
            if($instruksi['grouping_tipe'] == 'Reseptur') {
                $planning .= "Resep - " . $instruksi['tindakan_nama'] . "(" . $instruksi['qty'] . " " . $instruksi['satuankecil_nama'] . ")\n";
            } else if($instruksi['grouping_tipe'] == 'Tindakan & BMHP' ||
                ($instruksi['grouping_tipe'] == 'Penunjang' && in_array($instruksi['instalasi_penunjang_id'], [DocoConstants::INST_ID_LAB, DocoConstants::INST_ID_RAD,]))) {
                $planning .= "Pemeriksaan - " . $instruksi['tindakan_nama'] . "\n";
            } else if($instruksi['grouping_tipe'] == 'Penunjang' && $instruksi['instalasi_penunjang_id'] == DocoConstants::INST_ID_BEDAH) {
                $planning .= "Rencana Operasi - " . $instruksi['tindakan_nama'] . "\n";
            } else if($instruksi['grouping_tipe'] == 'SOAP FISIOTERAPI' || $instruksi['grouping_tipe'] == 'SOAP FISIOTERAPI RJ') {
            }
        }


        $query = (new \yii\db\Query())
            ->select(['konsulpoli_t.catatan_dokter_konsul', 'pegawai_m.nama_pegawai'])
            ->from('konsulpoli_t')
            ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = konsulpoli_t.pegawai_id')
            ->andWhere(['konsulpoli_t.pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['konsulpoli_t.created_date' => SORT_DESC]);
        if(!empty($lastDate)) {
            $query->andWhere(['>', 'konsulpoli_t.created_date', $lastDate]);
        }
        $listKonsultasi = $query->all();
        foreach ($listKonsultasi as $konsultasi) {
            $planning .= "Konsultasi - " . $konsultasi['nama_pegawai'] . " " . $konsultasi['catatan_dokter_konsul'] . "\n";
        }

        $query = (new \yii\db\Query())
            ->select(['catatan_diet'])
            ->from('permintaanmakan_t')
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['created_date' => SORT_DESC]);
        if(!empty($lastDate)) {
            $query->andWhere(['>', 'created_date', $lastDate]);
        }
        $listDiet = $query->all();
        foreach ($listDiet as $diet) {
            $planning .= "Diet - " . $diet['catatan_diet'] . "\n";
        }

        $query = (new \yii\db\Query())
            ->select(['terapi_nama', 'frekuensi'])
            ->from('programfisioterapi_v')
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tgl_rujukan' => SORT_DESC]);
        if(!empty($lastDate)) {
            $query->andWhere(['>', 'tgl_rujukan', $lastDate]);
        }
        $listTerapi = $query->all();
        foreach ($listTerapi as $terapi) {
            $planning .= "Penjadwalan fisioterapi - " . $terapi['terapi_nama'] . " (" . $terapi['frekuensi'] . ")\n";
        }

        return $planning;
    }

    public function actionGetSoapRehabMedic()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $orderBy = $request->get('order', ['tgl_soaprj' => SORT_DESC]);
            $ruangan_id = $request->get('ruangan_id', null);
            $kelompokpegawai_id = $request->get('kelompokpegawai_id');

            $querySoap = SoapRjView::find()
                ->select([
                    'a_diag_utama',
                    'instruksi',
                    'a_diag_penyerta',
                    'is_deleted_soap',
                ])
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id,
                    'jenis' => DocoConstants::JENIS_SOAP,
                    'grouping_tipe' => DocoConstants::GROUPING_TIPE
                ]);

            if (!empty($ruangan_id)) {
                $querySoap->andWhere([
                    'ruangan_id' => $ruangan_id
                ]);
            }
            if (!empty($kelompokpegawai_id)) {
                $querySoap->andWhere([
                    'kelompokpegawai_id' => $kelompokpegawai_id
                ]);
            }

            if($this->konfigCpptKosong == TRUE) {
                $querySoap->andWhere("((a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
            }

            if(!$this->konfigEditCpptCoret) {
                $querySoap->andWhere(['is_deleted_soap' => $this->konfigEditCpptCoret]);
            }

            $querySoap->orderBy($orderBy);
            $data = $querySoap->one();
            return [
                'data' => $data,
            ];
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDeleteCppt() {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id');
            $cppt_id = $request->post('cppt_id');
            $user_id = $request->post('user_id');

            $deleteCppt = SoapRj::updateAll(
                [
                    'is_deleted' => true,
                    'deleted_date' => date('Y-m-d H:i:s'),
                    'deleted_by' => $user_id
                ], 'pendaftaran_id = '.$pendaftaran_id.' AND soaprj_id = '.$cppt_id.'');

            if($deleteCppt) {
                return [
                    'status' => 200,
                    'message' => 'SOAP berhasil dihapus',
                ];
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'SOAP gagal dihapus',
                ];
            }
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
