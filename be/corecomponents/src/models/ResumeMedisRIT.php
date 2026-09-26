<?php

namespace Doco\models;

use app\modules\v1\models\PermintaanKonsulView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use app\modules\v1\models\InfoKunjunganRi;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\AsesmenAwal;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\InfoResepturDetailView;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "resumemedisri_t".
 *
 * @property int $resumemedisri_id
 * @property int $registrationId
 * @property int $pasienadmisi_id
 * @property string $tgl_masuk
 * @property string $tgl_keluar
 * @property string $diag_masuk
 * @property string $diag_utama
 * @property string $diag_penyerta
 * @property string $a_f_bermakna
 * @property string $prosedur_diag
 * @property string $tatalaksana_obat
 * @property bool $is_rotd
 * @property string $obat_rotd
 * @property int $kondisipulang_id
 * @property string $kondisi_lain
 * @property string $obat_pulang
 * @property int $kontrol_ke
 * @property string $tgl_kontrol
 * @property string $rencana_tindaklanjut
 * @property bool $is_print
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $anamnesa
 * @property string $pemeriksaan_fisik
 * @property string $prosedur
 * @property string $konsultasi
 * @property string $obat_rs
 * @property string $lain_lainnya
 */
class ResumeMedisRIT extends \Doco\components\DocoActiveRecord
{
    public $registrationId;
    public $pasienadmisiId;

    public static $encodedFields = ['diag_utama', 'diag_penyerta', 'tindakan', 'order_laboratorium', 'order_radiologi', 'konsul', 'obat_pulang'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resumemedisri_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['diag_masuk', 'diag_utama', 'diag_penyerta', 'prosedur_diag', 'pendaftaran_id', 'pasienadmisi_id', 'kondisipulang_id', 'kontrol_ke', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'kondisipulang_id', 'kontrol_ke', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_masuk', 'tgl_keluar', 'tgl_kontrol', 'created_date', 'last_modified_date', 'deleted_date', 'keluhan_utama', 'berat_badan', 'nadi', 'tinggi_badan', 'rr', 'td', 'suhu', 'skala', 'alergi_obat', 'obat_diberikan', 'riwayat_penyakit_dahulu', 'metod_asmennyeri', 'tindakan', 'order_laboratorium', 'order_radiologi', 'konsul', 'obat', 'doktor_rawat_bersama', 'obat_dibawa_pulang', 'obat_dibawa_pulang_text', 'indikasi_pasien_dirawat', 'instruksi', 'alergi_makanan', 'alergi_lainnya', 'reaksi_alergi_obat', 'kondisi_pulang', 'diag_awal', 'anamnesa', 'pemeriksaan_fisik', 'prosedur', 'konsultasi', 'obat_rs', 'lain_lainnya', 'catatan_diet'], 'safe'],
            [['a_f_bermakna', 'tatalaksana_obat', 'obat_rotd', 'kondisi_lain', 'rencana_tindaklanjut', 'additional_data'], 'string'],
            [['is_rotd', 'is_print', 'is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'resumemedisri_id' => 'Resumemedisri ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_masuk' => 'Tgl Masuk',
            'tgl_keluar' => 'Tgl Keluar',
            'diag_masuk' => 'Diag Masuk',
            'diag_utama' => 'Diag Utama',
            'diag_penyerta' => 'Diag Penyerta',
            'a_f_bermakna' => 'A F Bermakna',
            'prosedur_diag' => 'Prosedur Diag',
            'tatalaksana_obat' => 'Tatalaksana Obat',
            'is_rotd' => 'Is Rotd',
            'obat_rotd' => 'Obat Rotd',
            'kondisipulang_id' => 'Kondisipulang ID',
            'kondisi_lain' => 'Kondisi Lain',
            'obat_pulang' => 'Obat Pulang',
            'kontrol_ke' => 'Kontrol Ke',
            'tgl_kontrol' => 'Tgl Kontrol',
            'rencana_tindaklanjut' => 'Rencana Tindaklanjut',
            'is_print' => 'Is Print',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'anamnesa' => 'Anamnesa',
            'pemeriksaan_fisik' => 'Pemeriksaan Fisik',
            'prosedur' => 'Prosedur',
            'konsultasi' => 'Konsultasi',
            'obat_rs' => 'Obat Rumah Sakit',
            'lain_lainnya' => 'Lain - Lain',
            'catatan_diet' => 'Catatan Diet'
        ];
    }

    /**
     * This function will return all of data resume medis
     *
     * @param String $registrationId
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function resumeByRegistrationId($registrationId, $pasienadmisi_id, $actualRecord = false)
    {
        $patientRecord = InfoKunjunganRi::find()
            ->select(['nama_pasien', 'no_pendaftaran', 'tanggal_lahir', 'pasien_id', 'pendaftaran_id', 'no_rekam_medik', 'alamat_pasien', 'ruangan_nama', 'umur', 'tgl_pendaftaran', 'jenis_kelamin', 'tgl_pulang as tglpasienpulang', 'agama_nama as agama', 'nama_pegawai as dokter_dpjp', 'pegawai_id as dokter_dpjp_id', 'carakeluar_nama', 'is_stopakomodasi', 'kamarruangan_nokamar as kamar', 'rujukan_dari as perujuk', 'tgl_admisi', 'tgl_stopakomodasi', 'additional_pasien', 'tempat_lahir', 'no_identitas_pasien'])
            ->andWhere(['pendaftaran_id' => $registrationId])
            ->asArray()
            ->one();

        if (!$actualRecord) {
            $resumeRecord = self::find()
                ->select([
                    'tgl_masuk', 'tgl_keluar', 'additional_data', 'keluhan_utama', 'berat_badan', 'diag_awal', 'diag_utama', 'diag_penyerta', 'nadi', 'tinggi_badan', 
                    'rr', 'td', 'suhu', 'skala', 'alergi_obat', 'obat_diberikan', 'riwayat_penyakit_dahulu', 'metod_asmennyeri', 'tindakan', 'order_laboratorium', 
                    'order_radiologi', 'konsul', 'obat', 'doktor_rawat_bersama', 'obat_dibawa_pulang', 'obat_dibawa_pulang_text', 'indikasi_pasien_dirawat', 'instruksi', 
                    'alergi_makanan', 'alergi_lainnya', 'reaksi_alergi_obat', 'kondisi_pulang', 'anamnesa', 'pemeriksaan_fisik', 'prosedur', 'konsultasi', 'obat_rs', 
                    'lain_lainnya', 'catatan_diet', 'diag_masuk', 'resumemedisri_id'
                ])
                ->andWhere(['pasienadmisi_id' => $pasienadmisi_id])
                ->one();
        } else {
            $resumeRecord = [];
        }
        
        // get cara keluar
        $caraKeluar = CaraKeluar::find()
        ->select(['carakeluar_id', 'carakeluar_namalain'])
        // ->where(['IN', 'carakeluar_id', [1,2,3,6]])
        ->orderBy(['carakeluar_id' => SORT_ASC])
        ->all();

        $resumeClass = new self();
        $resumeClass->registrationId = $registrationId;
        $resumeClass->pasienadmisiId = $pasienadmisi_id;
        $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');
        $lastCppt = self::getLastCppt($registrationId);
        $orderPenunjang = self::getOrderPenunjang($registrationId);
        $resep = self::getResep($registrationId);
        $objective = ArrayHelper::getValue($lastCppt, 'object');
        $asmed = $resumeClass->resumeData('asmed');
        $asesmenAwal = $resumeClass->resumeData('askep');
        
        if (!empty($resumeRecord)) {
            $additionalDiagnose = '<ol>';
            $cpptDetailRecord = [];
            if(isset($resumeRecord['diag_penyerta']) && !empty($resumeRecord['diag_penyerta']) ) {
                foreach ($resumeRecord['diag_penyerta'] as $keydetail => $valdetail) {
                    if (empty($valdetail) || !is_array($valdetail)) continue;
                    $diagPenyerta = self::recursiveArray(is_array($valdetail) ? $valdetail : json_decode($valdetail, true));

                    if ( isset($diagPenyerta['text']) ) {
                        $additionalDiagnose .= '<li>';
                        $additionalDiagnose .= isset($diagPenyerta['text']) ? $diagPenyerta['text'] : '-';
                        $additionalDiagnose .= '</li>';
                    }
                    if ($diagPenyerta != '-'){
                        $cpptDetailRecord[] = $diagPenyerta;
                    }
                }
            }
            $cpptDetailRecord = [
                [
                    'a_diag_penyerta' => json_encode($cpptDetailRecord)
                ]
            ];
            $additionalDiagnose .= '</ol>';
            // utk additional data
            $additional_data = json_decode($resumeRecord['additional_data'], true);
            if(!is_array($additional_data)) {
                $additional_data = [];
            }
            // if(!isset($additional_data['cara_keluar']) || empty($additional_data['cara_keluar'])) {
            // $additional_data['cara_keluar'] = $resumeClass->resumeData('pulang');
            // }
            // $resumeRecord['additional_data'] = json_encode($additional_data);
            $additional_data['cara_keluar'] = ArrayHelper::getValue($additional_data, 'cara_keluar', false);
            if(empty($resumeRecord['obat_dibawa_pulang'])) {
                $obat_dibawa_pulang_temp = $resumeClass->resumeData('obat-bawa-pulang');
                $obat_dibawa_pulang = [];
                $null_count = 0;
                foreach ($obat_dibawa_pulang_temp as $obat) {
                    $obat_dibawa_pulang[$obat['noresep']][] = $obat;
                }
                $resObatPulang = [];
                foreach ($obat_dibawa_pulang as $k => $v) {
                    if (!empty($v)) {
                        foreach ($v as $keyv => $vObat) {
                            $resObatPulang[$k][$vObat['rke']][] = $vObat;
                        }
                    }
                }
                if (!empty($resObatPulang) && count($resObatPulang) > 0) {
                    $resObatPulang = $resObatPulang[array_keys($resObatPulang)[0]];
                }

                $resumeRecord['obat_dibawa_pulang'] = $resObatPulang;
            }
            if(empty($resumeRecord['obat'])) {
                $resumeRecord['obat'] = $resumeClass->resumeData('obat');
            }

            return [
                'patientRecord' => array_merge($patientRecord, [
                    'carakeluar_nama' => $resumeRecord['kondisi_pulang']
                ]),
                'askepRecord' => [
                    'keluhan_utama'  => $resumeRecord['keluhan_utama'],
                    'pendaftaran_id'  => $resumeRecord['pendaftaran_id'],
                    'berat_badan'  => $resumeRecord['berat_badan'],
                    'nadi'  => $resumeRecord['nadi'],
                    'tinggi_badan'  => $resumeRecord['tinggi_badan'],
                    'rr'  => $resumeRecord['rr'],
                    'td'  => $resumeRecord['td'],
                    'suhu'  => $resumeRecord['suhu'],
                    'skala'  => $resumeRecord['skala'],
                    'alergi_obat'  => $resumeRecord['alergi_obat'],
                    'alergi_makanan'  => $resumeRecord['alergi_makanan'],
                    'alergi_lainnya'  => $resumeRecord['alergi_lainnya'],
                    'reaksi_alergi_obat'  => $resumeRecord['reaksi_alergi_obat'],
                    'obat_diberikan'  => $resumeRecord['obat_diberikan'],
                    'metod_asmennyeri'  => $resumeRecord['metod_asmennyeri'],
                    'diagnosa_rujukan' => $resumeRecord['diag_awal']
                ],
                'patientDiseaseHistory' => $resumeRecord['riwayat_penyakit_dahulu'],
                'diagAwal' => [
                    'diagnosa_masuk' => !empty($resumeRecord['diag_masuk']) ? $resumeRecord['diag_masuk'] : ArrayHelper::getValue($asmed, 'diagnosa_id'),
                    'diagAwal' => !empty($resumeRecord['diag_awal']) ? $resumeRecord['diag_awal'] : ArrayHelper::getValue($asmed, 'diagnosa_id'),                    
                ],
                'cpptRecord' => [
                    'a_diag_utama' => (!empty($resumeRecord['diag_utama'])) ? $resumeRecord['diag_utama'] : null,
                    'a_diag_penyerta' => (!empty($resumeRecord['diag_penyerta'])) ? $resumeRecord['diag_penyerta'] : null
                ],
                'cpptDetailRecord' => $cpptDetailRecord,
                'actRecord' => $resumeRecord['tindakan'],
                'labOrder' => $resumeRecord['order_laboratorium'],
                'radOrder' => $resumeRecord['order_radiologi'],
                'consuleRecord' => $resumeRecord['konsul'],
                'medicineRecord' => $resumeRecord['obat'],
                'medicineOnReturn' => $resumeRecord['obat_dibawa_pulang'],
                'doctorInpatientConsule' => $resumeClass->resumeData('doctorInpatient'),
                'instruction' => $resumeRecord['instruksi'],
                'additionalDiagnose' => $additionalDiagnose,
                'indication' => $resumeRecord['indikasi_pasien_dirawat'],
                'anamnesa' => $resumeRecord['anamnesa'],
                'pemeriksaan_fisik' => !empty($resumeRecord['pemeriksaan_fisik']) ? $resumeRecord['pemeriksaan_fisik'] : $objective,
                'prosedur' => $resumeRecord['prosedur'],
                'konsultasi' => $resumeRecord['konsultasi'],
                'obat_rs' => $resumeRecord['obat_rs'],
                'obat_dibawa_pulang' => $resumeRecord['obat_dibawa_pulang'],
                'obat_dibawa_pulang_text' => $resumeRecord['obat_dibawa_pulang_text'],
                'lain_lainnya' => $resumeRecord['lain_lainnya'],
                'catatan_diet' => $resumeRecord['catatan_diet'],
                'tgl_masuk' => $resumeRecord['tgl_masuk'],
                'tgl_keluar' => $resumeRecord['tgl_keluar'],
                'caraKeluar' => $caraKeluar,
                'additional_data' => $additional_data,
                'td' => $resumeRecord['td'],
                'suhu' => $resumeRecord['suhu'],
                'nadi' => $resumeRecord['nadi'],
                'enable_pulang' => $enable_pulang,
                'orderPenunjang' => $orderPenunjang,
                'resep' => $resep,
                'resumemedisri_id' => $resumeRecord['resumemedisri_id'],
                'suggestion' => null
            ];
        } else {
            $patientId = $patientRecord['pasien_id'];

            $resumeClass = new self();
            $resumeClass->registrationId = $registrationId;
            $askepRecord = $resumeClass->resumeData('asmed');
            $patientDiseaseHistory = isset($askepRecord['riwayat_penyakit_dahulu']) ? $askepRecord['riwayat_penyakit_dahulu'] : '';
            $td_systolic = isset($askepRecord['td_systolic']) && $askepRecord['td_systolic'] != 0 ? $askepRecord['td_systolic'] : '';
            $td_diastolic = isset($askepRecord['td_diastolic']) && $askepRecord['td_diastolic'] != 0 ? $askepRecord['td_diastolic'] : '';
            $askepRecord['td'] = $td_systolic . "/" . $td_diastolic;

            // SOAP RJ
            $diagAwal = $resumeClass->resumeData('diagnosaAwal');
            $cpptRecord = $resumeClass->resumeData('cppt');

            $cpptDetailRecord = $resumeClass->resumeData('penyerta');

            // tindakan
            $actRecord = $resumeClass->resumeData('tindakan');

            // laboratorium
            // $labOrder = $resumeClass->resumeData('laboratorium');
            $labOrder['text'] = '';

            // radiologi
            // $radOrder = $resumeClass->resumeData('radiologi');
            $radOrder['text'] = '';

            // Konsul poli
            $consuleRecord = $resumeClass->resumeData('konsul');

            // reseptur
            $medicineRecord = $resumeClass->resumeData('obat');
            $obat_dibawa_pulang_temp = $resumeClass->resumeData('obat-bawa-pulang');
            $obat_dibawa_pulang = [];
            foreach ($obat_dibawa_pulang_temp as $obat) {
                $obat_dibawa_pulang[$obat['noresep']][] = $obat;
            }
            $resObatPulang = [];
            foreach ($obat_dibawa_pulang as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $keyv => $vObat) {
                        $resObatPulang[$k][$vObat['rke']][] = $vObat;
                    }
                }
            }
            if (!empty($resObatPulang) && count($resObatPulang) > 0) {
                $resObatPulang = $resObatPulang[array_keys($resObatPulang)[0]];
            }

            $medicineOnReturn = $resObatPulang;

            $additionalDiagnose = '<ul>';
            foreach ($cpptDetailRecord as $key => $val) {
                if(isset($diagnosa['a_diag_penyerta']) && !empty($diagnosa['a_diag_penyerta'])){
                    if ( !is_array($diagnosa['a_diag_penyerta']) ) {
                        $aDiagPenyerta = json_decode($diagnosa['a_diag_penyerta'], true);
                    } else if ( is_array($diagnosa['a_diag_penyerta']) && isset($diagnosa['a_diag_penyerta']['text']) && $diagnosa['a_diag_penyerta']['text'] != '-') {
                        $aDiagPenyerta = $diagnosa['a_diag_penyerta'];
                    } else {
                        $aDiagPenyerta = [];
                    }
                    if( isset($aDiagPenyerta['text']) ){
                        $aDiagPenyerta = is_array($aDiagPenyerta) ? $aDiagPenyerta : json_encode($aDiagPenyerta, true) ;
                        $additionalDiagnose .= '<li>';
                        $additionalDiagnose .= isset($aDiagPenyerta['text']) ? $aDiagPenyerta['text'] : '-';
                        $additionalDiagnose .= '</li>';
                    } else {
                        $aDiagPenyerta = is_array($aDiagPenyerta) ? $aDiagPenyerta : json_encode($aDiagPenyerta, true) ;
                        $listItem = [];
                        foreach($aDiagPenyerta as $index => $item){
                            if (empty($item) || !is_array($item)) continue;
                            $item = self::recursiveArray( is_array($item) ? $item : json_decode($item, true) );
                            if (isset($item['text']) && $item['text'] == '-' ) continue;
                            $additionalDiagnose .= '<li>';
                            $additionalDiagnose .= isset($item['text']) ? $item['text'] : '-';
                            $additionalDiagnose .= '</li>';
                        }
                    }
                }
            }
            $additionalDiagnose .= '</ul>';
            // dokter rawat bersama
            $doctorInpatientConsule = $resumeClass->resumeData('doctorInpatient');

            // anamnesa
            $anamnesa = $resumeClass->resumeData('anamnesa');

            // diet
            $catatan_diet = $resumeClass->resumeData('diet');

            $additional_data = json_decode($resumeRecord['additional_data'], true);
            // if(!isset($additional_data['cara_keluar']) || empty($additional_data['cara_keluar'])) {
            $additional_data['cara_keluar'] = $resumeClass->resumeData('pulang');
            // }
            $resumeRecord['additional_data'] = json_encode($additional_data);
            $pemeriksaan_fisik = $objective;

            $suggestion = self::getSuggestion($askepRecord,$diagAwal);
            
            return compact(
                'patientRecord', 'patientId', 'patientDiseaseHistory', 'askepRecord', 'cpptRecord', 'cpptDetailRecord', 'actRecord', 'labOrder', 'radOrder', 
                'consuleRecord', 'medicineRecord', 'medicineOnReturn', 'registrationId', 'additionalDiagnose', 'cpptDetailRecord', 'additionalDiagnose', 'doctorInpatientConsule', 'diagAwal', 
                'anamnesa', 'catatan_diet', 'caraKeluar', 'enable_pulang', 'lastCppt', 'orderPenunjang', 'resep', 'pemeriksaan_fisik'
                ,'suggestion','asesmenAwal'
            );
        }
    }

    /**
     * This function will return resume source data by type
     *
     * @param String $type
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function resumeData($type)
    {
        $result = [];
        if (!empty($this->registrationId)) {
            switch ($type) {
                case 'asmed':
                    $result = AsesmenMedis::find()
                        ->select([
                            'keluhan_utama',
                            'pendaftaran_id',
                            'berat_badan',
                            'detak_nadi as nadi',
                            'tinggi_badan',
                            'pernapasan as rr',
                            'tekanan_darah as td',
                            'suhu_tubuh as suhu',
                            'skala',
                            'r_alergiobat as alergi_obat',
                            'r_makanan as alergi_makanan',
                            'obat_diberikan',
                            'metod_asmennyeri',
                            'r_penyakitdahulu',
                            'r_penyakitsekarang',
                            'diagnosa_id',
                            'pernapasan',
                            'td_systolic',
                            'td_diastolic',
                        ])
                        ->orderBy(['created_date' => SORT_DESC])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->asArray()
                        ->one();
                    
                    $patientDiseaseHistoryArray = !empty($result['r_penyakitdahulu']) ? json_decode($result['r_penyakitdahulu'], true) : [];
                    $patientDiseaseHistory = '';
                    $totalDisease = count($patientDiseaseHistoryArray);
                    foreach ($patientDiseaseHistoryArray as $indexHistory => $history) {
                        if($history['tahun'] != '' || $history['penyakit'] != '' || $history['terapi'] != ''){
                            $patientDiseaseHistory .= $history['tahun'] .' - '. $history['penyakit'] .' - '. $history['terapi'] ."\n";
                        }
                    }
                    $result['riwayat_penyakit_dahulu'] = $patientDiseaseHistory;
                    break;
                case 'cppt':
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.a_diag_utama',
                            'cppt_t.a_diag_penyerta',
                            'cppt_t.pasien_id',
                            'cppt_t.pendaftaran_id',
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'pegawai_m.kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER
                        ])
                        ->andWhere([
                            'IS NOT', 'cppt_t.a_diag_utama', null
                        ])
                        ->join('join', 'pegawai_m', 'pegawai_m.pegawai_id=cppt_t.pegawai_id')
                        ->orderBy(['cppt_t.tgl_cppt' => SORT_DESC])
                        ->one();
                    break;
                case 'penyerta':
                    $result = Cppt::find()
                        ->select([
                            'cppt_t.a_diag_penyerta',
                            'cppt_t.pasien_id',
                            'cppt_t.pendaftaran_id',
                        ])
                        ->andWhere([
                            'cppt_t.pendaftaran_id' => $this->registrationId,
                            'pegawai_m.kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER
                        ])
                        ->join('join', 'pegawai_m', 'pegawai_m.pegawai_id=cppt_t.pegawai_id')
                        ->andWhere([
                            'IS NOT', 'cppt_t.a_diag_penyerta', null
                        ])
                        ->orderBy(['cppt_t.tgl_cppt' => SORT_DESC])
                        ->all();
                    break;
                case 'tindakan':
                    $result = RiwayatInstruksiTindakanView::find()
                        ->select([
                            'tindakan',
                            'tindakan_paket_obat',
                            'qty',
                            'tipe'
                        ])
                        ->andWhere(['pendaftaran_id' => $this->registrationId])
                        // ->andWhere(['!=', 'tipe', 'BMHP'])
                        ->orderBy(['tipe' => SORT_DESC])
                        ->asArray()
                        ->all();
                    break;
                case 'laboratorium':
                    $result = InfoPasienLabDetailView::find()
                        ->select([
                            'pasien_id',
                            'pendaftaran_id',
                            'tgl_tindakan',
                            'jenis',
                            'daftartindakan_nama'
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->orderBy(['tgl_tindakan' => SORT_ASC])
                        ->asArray()
                        ->all();
                    break;
                case 'radiologi':
                    $result = InfoPasienRadDetailView::find()
                        ->select([
                            'pasien_id',
                            'pendaftaran_id',
                            'tgl_tindakan',
                            'jenis',
                            'daftartindakan_nama'
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->orderBy(['tgl_tindakan' => SORT_ASC])
                        ->asArray()
                        ->all();
                    break;
                case 'konsul':
                    $result = PermintaanKonsulView::find()
                        ->select([
                            'pendaftaran_id',
                            'dok_konsul as dok_mengkonsul',
                            'ket_konsul as catatan_dokter_konsul',
                            'jawaban_konsul',
                            'waktu_permintaan as tgl_konsulpoli',
                            'waktu_persetujuan as tgl_selesaikonsul',
                        ])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId,
                            'status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU
                        ])
                        ->asArray()
                        ->all();
                    break;
                case 'obat':
                    $result = InfoResepturDetailView::find()
                        ->select([
                            'racikan_nama',
                            'rke',
                            'obatalkes_nama',
                            'satuan_kecil',
                            'signa_nama',
                            'signa',
                            'qty_reseptur',
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId,
                            // 'is_bayar' => true,
                            'status_reseptur_id' => DocoConstants::RESEPTUR_DISERAHKAN,
                        ])
                        ->orderBy(['racikan_nama' => SORT_ASC])
                        ->asArray()
                        ->all();
                    break;
                case 'obat-bawa-pulang':
                    $result = InfoResepturDetailView::find()
                        ->select([
                            'racikan_nama',
                            'rke',
                            'obatalkes_nama',
                            'satuan_kecil',
                            'signa_nama',
                            'signa',
                            'qty_reseptur',
                            'etiket',
                            'noresep'
                        ])
                        ->where([
                            'pendaftaran_id' => $this->registrationId,
                            // 'is_bayar' => true,
                            // 'status_reseptur_id' => [DocoConstants::RESEPTUR_SUDAH_DIPROSES, DocoConstants::RESEPTUR_BELUM_DIPROSES, DocoConstants::RESEPTUR_DISERAHKAN],
                        ])
                        ->orderBy([
                            'noresep' => SORT_DESC,
                            'racikan_nama' => SORT_ASC,
                            'rke' => SORT_ASC
                        ])
                        ->asArray()
                        ->all();
                    break;
                case 'diagnosaAwal':
                    $result = AsesmenAwal::find()
                        ->select([
                            'diagnosa_masuk',
                            'pendaftaran_id',
                            'r_alergi',
                            'nama_alergi',
                            'additional_data'
                        ])
                        ->where(['pendaftaran_id' => $this->registrationId])
                        ->one();
                    break;
                case 'doctorInpatient':
                    $doctorInpatientConsule = PermintaanKonsulView::find()
                        ->select(['dok_konsul as dokter'])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->andWhere([
                            'jenis_konsul' => DocoConstants::JNS_KNSL_RB,
                            'status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU
                        ])
                        ->asArray()
                        ->one();
                    if (!empty($doctorInpatientConsule)) {
                        $result = $doctorInpatientConsule['dokter'];
                    } else {
                        $result = '-';
                    }
                    break;
                case 'anamnesa':
                    $result = Anamnesa::find()
                        ->select([
                            'keluhan_utama',
                            'keluhan_tambahan'
                        ])
                        ->andWhere([
                            'pendaftaran_id' => $this->registrationId
                        ])
                        ->asArray()
                        ->one();
                    break;
                case 'diet':
                    $catatanDiet = PermintaanMakan::find()
                        ->select(['catatan_diet'])
                        ->where(['pendaftaran_id' => $this->registrationId])
                        ->orderBy(['tgl_permintaanmakan' => SORT_DESC])
                        ->asArray()
                        ->one();
                    $result = !empty($catatanDiet) ? $catatanDiet['catatan_diet'] : '-';
                    break;
                case 'pulang':
                    $result = PasienPulang::find()
                        ->select(['carakeluar_id'])
                        ->where(['pendaftaran_id' => $this->registrationId])
                        ->scalar();
                case 'askep':
                    $result = AsesmenAwal::find()
                        ->select([
                            'r_alergi',
                            'additional_data'
                        ])
                        ->where(['pasienadmisi_id' => $this->pasienadmisiId])
                        ->one();
                default:
                    break;
            }
        }
        return $result;
    }

    /**
     * This function will update data resume medis by it type
     *
     * @param String $registrationId
     * @param String $type
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function updateResume($registrationId, $type)
    {
        $recordResumeMedis = self::find()
            ->andWhere(['pendaftaran_id' => $registrationId])
            ->one();
        if (empty($recordResumeMedis)) {
            $recordResumeMedis = new self();
            $recordResumeMedis->pendaftaran_id = $registrationId;
        }
        if (!empty($recordResumeMedis)) {
            $classResume = new self();
            $classResume->registrationId = $registrationId;
            switch ($type) {
                case 'diagnosaAwal':
                    $recordAskep = $classResume->resumeData('diagnosaAwal');
                    
                    //dimatikan karena merubah data diag awal setelah asmed
                    // $recordResumeMedis->diag_awal = isset($recordAskep['diagnosa_masuk']) ? $recordAskep['diagnosa_masuk'] : null;
                    break;
                case 'asmed':
                    $record = $classResume->resumeData('asmed');
                    $recordResumeMedis->keluhan_utama = $record['keluhan_utama'];
                    $recordResumeMedis->pendaftaran_id = $record['pendaftaran_id'];
                    $recordResumeMedis->berat_badan = $record['berat_badan'];
                    $recordResumeMedis->nadi = $record['nadi'];
                    $recordResumeMedis->tinggi_badan = $record['tinggi_badan'];
                    $recordResumeMedis->rr = $record['rr'];
                    $recordResumeMedis->td = $record['td'];
                    $recordResumeMedis->suhu = $record['suhu'];
                    $recordResumeMedis->skala = $record['skala'];
                    $recordResumeMedis->alergi_obat = $record['alergi_obat'];
                    $recordResumeMedis->alergi_makanan = $record['alergi_makanan'];
                    $recordResumeMedis->obat_diberikan = $record['obat_diberikan'];
                    $recordResumeMedis->metod_asmennyeri = $record['metod_asmennyeri'];
                    $recordResumeMedis->riwayat_penyakit_dahulu = $record['riwayat_penyakit_dahulu'];
                    $recordResumeMedis->riwayat_penyakit_dahulu = $record['riwayat_penyakit_dahulu'];
                    $recordResumeMedis->diag_masuk = json_decode($record['diagnosa_id'], true);
                    $recordResumeMedis->diag_awal = json_decode($record['diagnosa_id'], true);

                    $additional_data = json_decode($recordResumeMedis->additional_data, true);
                    $additional_data['frekuensi_nafas'] = $record['pernapasan'];
                    $recordResumeMedis->additional_data = json_encode($additional_data);
                    break;
                case 'cppt':
                    // update diag masuk and diag penyerta
                    $recordCppt = $classResume->resumeData('cppt');
                    $recordPenyerta = $classResume->resumeData('penyerta');
                    $recordResumeMedis->diag_utama = isset($recordCppt['a_diag_utama']) ? $recordCppt['a_diag_utama'] : null;
                    $arrayDiagnose = [];
                    foreach ($recordPenyerta as $diagnose) {
                        $arrayDiagnose = array_merge( $arrayDiagnose, $diagnose['a_diag_penyerta'] );
                    }
                    $recordResumeMedis->diag_penyerta = $arrayDiagnose;
                    break;
                case 'tindakan':
                    $record = $classResume->resumeData('tindakan');
                    $recordResumeMedis->tindakan = $record;
                    break;
                case 'laboratorium':
                    break;
                    $record = $classResume->resumeData('laboratorium');
                    $recordResumeMedis->laboratorium = $record;
                case 'radiologi':
                    $record = $classResume->resumeData('radiologi');
                    $recordResumeMedis->radiologi = $record;
                    break;
                case 'konsul':
                    $record = $classResume->resumeData('konsul');
                    $recordResumeMedis->konsul = $record;
                    break;
                case 'obat':
                    $record = $classResume->resumeData('obat');
                    $recordResumeMedis->obat = $record;
                    break;
                case 'diet':
                    $record = $classResume->resumeData('diet');
                    $recordResumeMedis->catatan_diet = $record;
                    break;
            }
            $result = $recordResumeMedis->save();
            if (!$result) {
                Yii::error([
                    "ErrorResumeMedis" => $type,
                    "Bucket" => $recordResumeMedis->errors
                ]);
            }
            return $result;
        } else {
            return false;
        }
    }

    private function recursiveArray( $arr )
    {
        $isArray = false;
        foreach ($arr as $v) {
            if (is_array($v)) $isArray = true;
        }

        if ( $isArray ) {
            return $arr[0];
        }
        return $arr;
    }

    private function getLastCppt($id)
    {
        return Yii::$app->db->createCommand("
            SELECT * FROM soaprs_v WHERE pendaftaran_id = {$id} ORDER BY tgl_soaprj DESC LIMIT 1
        ")->queryOne();
    }

    private function getOrderPenunjang($id)
    {
        return Yii::$app->db->createCommand("
            SELECT pemeriksaan_nama FROM orderpenunjang_v WHERE pendaftaran_id = {$id}
        ")->queryAll();
    }

    private function getResep($id)
    {
        $statusDiserahkan = DocoConstants::RESEPTUR_DISERAHKAN;
        return Yii::$app->db->createCommand("
            SELECT obatalkespasien_id,obatalkes_nama,qty_transaksi,satuan_kecil,signa_nama FROM inforesepdetail_v WHERE pendaftaran_id = {$id} 
            AND obatalkespasien_id IS NOT NULL AND penjualanresep_id IS NOT NULL AND status_reseptur_id = {$statusDiserahkan}
        ")->queryAll();
    }

    private function getSuggestion($asmed = null,$askep = null)
    {
        $diag_awal = null;

        $diag_awal = ArrayHelper::getValue($asmed,'diagnosa_id');
        if(!is_null($diag_awal)){
            $diag_awal = json_decode($diag_awal,true);
        }

        return [
            'diag_awal' => $diag_awal
        ];
    }
}
