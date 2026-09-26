<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-05 11:42:14
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-08-13 13:37:40
 */

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoConstants;
use Doco\models\PegawaiSubSpesialis;

/**
 * This is the model class for table "cppt_t".
 *
 * @property int $cppt_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $ruangan_id
 * @property string $kamar_tempattidur formatnya : [no_kamar]-[no_tempattidur]
 * @property string $tgl_cppt
 * @property string $subject
 * @property string $object
 * @property int $a_diag_utama
 * @property string $a_diag_penyerta
 * @property string $planning
 * @property bool $is_instruksi_pulang
 * @property int $pegawai_id
 * @property string $catatan_dokter
 * @property string $catatan_perawat
 * @property string $instruksi
 * @property int $pemberi_instruksi_id
 * @property bool $is_verifikasi
 * @property int $pegawai_verifikasi_id
 * @property string $tgl_verifikasi
 * @property int $kamarruangan_id
 * @property int $kamartempattidur_id
 * @property bool $is_icd_x
 */
class Cppt extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'cppt_t';
	}

    public function scenarios()
    {
        return [
            'soap' => ['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama','is_active', 'catatan_dokter', 'catatan_perawat', 'instruksi'],
            'auto' => ['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama','is_active', 'catatan_dokter', 'catatan_perawat', 'instruksi']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama'], 'required','on' => 'soap'],
            [['pasien_id', 'ruangan_id', 'pegawai_id', 'ruangan_id', 'instruksi', 'pemberi_instruksi_id'], 'required','on' => 'verbalorder'],	
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'a_diag_utama', 'pegawai_id', 'pemberi_instruksi_id', 'pegawai_verifikasi_id', 'kamarruangan_id', 'kamartempattidur_id'], 'default', 'value' => null],
            [['cppt_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'pegawai_id', 'pemberi_instruksi_id', 'pegawai_verifikasi_id', 'kamarruangan_id', 'kamartempattidur_id'], 'integer'],
            [['tgl_cppt', 'tgl_verifikasi', 'referred_id', 'catatan_dokter', 'instruksi', 'is_icd_x'], 'safe'],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'ruangan_id', 'tgl_cppt', 'pegawai_id', 'subject', 'object', 'planning', 'a_diag_utama','is_active', 'catatan_dokter', 'instruksi'],'safe','on'=>'auto'],
            [['subject', 'object', 'planning', 'catatan_dokter', 'catatan_perawat', 'instruksi'], 'string'],
            [['is_instruksi_pulang', 'is_verifikasi','is_icd_x'], 'boolean'],
            [['kamar_tempattidur'], 'string', 'max' => 255],
            [['cppt_id'], 'unique'],
        ];
    }

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'cppt_id' => 'Cppt ID',
			'pendaftaran_id' => 'Pendaftaran ID',
			'pasienadmisi_id' => 'Pasienadmisi ID',
			'pasien_id' => 'Pasien ID',
			'ruangan_id' => 'Ruangan ID',
			'kamar_tempattidur' => 'Kamar Tempattidur',
			'tgl_cppt' => 'Tgl Cppt',
			'subject' => 'Subject',
			'object' => 'Object',
			'a_diag_utama' => 'A Diag Utama',
			'a_diag_penyerta' => 'A Diag Penyerta',
			'planning' => 'Planning',
			'is_instruksi_pulang' => 'Is Instruksi Pulang',
			'pegawai_id' => 'Pegawai ID',
			'catatan_dokter' => 'Catatan Dokter',
			'catatan_perawat' => 'Catatan Perawat',
			'instruksi' => 'Instruksi',
			'pemberi_instruksi_id' => 'Pemberi Instruksi ID',
			'is_verifikasi' => 'Is Verifikasi',
			'pegawai_verifikasi_id' => 'Pegawai Verifikasi ID',
			'tgl_verifikasi' => 'Tgl Verifikasi',
			'kamarruangan_id' => 'Kamarruangan ID',
			'kamartempattidur_id' => 'Kamar tempat tidur ID',
            'is_icd_x' => 'is icd x',
		];
	}

	/**
     * Function for integrate visite dokter to billing
     * Array $payload
     * Required params:
     * - pendaftaran_id
     * - pasienadmisi_id
     * - pegawai_id
     * - tgl_cppt
     * 
     * Conditions:
     * 1. pegawai input cppt adalah dokter
     * 2. belum ada tindakan Visit Dokter Spesialis yg tercatat di billing pada tanggal cppt yg terinput
     * 3. data pasien tersedia di view infopasienri_v, pasien belum dipulangkan, pasien belum stop akomodasi
     * 
     * Usage:
     * - CpptController@soapCreateOrUpdate
     * - AllowController@actionCreateSoap
     */
    public function addVisiteDokter($payload)
    {

        $dataPasien = (new \app\modules\v1\models\InfoPasienRanap)->find()->select([
            'pasienadmisi_id',
            'no_pendaftaran',
            'kelaspelayanan_id',
            'penjamin_id',
            'is_stopakomodasi',
            'pasienpulang_id',
            'ruangan_id'
        ])
        ->andWhere([
            'pendaftaran_id' => $payload['pendaftaran_id'],
            'pasienadmisi_id' => $payload['pasienadmisi_id'],
        ])->asArray()->one();

        $spesialis_id = Pegawai::find()->select([
            'spesialis_id'
        ])->where([
            'pegawai_id' => $payload['pegawai_id']
        ])->scalar();
        
        // $visitDokterSlug = DocoConstants::VISIT_DOKTER_SLUG;
        // if ( is_null($spesialis_id) ) {
        //     $visitDokterSlug = DocoConstants::VISIT_DOKTER_JAGA_SLUG;
        // }
        if(empty($dataPasien) || !is_null($dataPasien['pasienpulang_id']) || $dataPasien['is_stopakomodasi']){
            Yii::error([
                'msg' => 'maaf data pasien kosong, atau pasienpulang dah ada, atau is_stopakomodasi nya true',
                'data' => compact('dataPasien')
            ]);
            return true;
        }
        $visite_kode = (new \Doco\models\ConstantsId)->find()->select([
            'kode_id'
        ])->where([
            'kode_transaksi' => 'visite'
        ])->scalar();
        // if(empty($spesialis_id)) {
            // $tarif = (new TarifTotalRs([
            //     'extParam' => [
            //         $dataPasien['ruangan_id'],
            //         $dataPasien['penjamin_id'],
            //         $dataPasien['kelaspelayanan_id'],
            //         'pelayanan'
            //     ]
            // ]));
        // } else {
        //     $tarif = (new TarifTotalRs([
        //         'extParam' => [
        //             $dataPasien['ruangan_id'],
        //             $dataPasien['penjamin_id'],
        //             $dataPasien['kelaspelayanan_id'],
        //             'pelayanan',
        //             $spesialis_id,
        //         ]
        //     ]));
        // }
        $tindakanVisite = [];
        $tindakanVisite = TarifTindakan::find()
            ->select(['tariftindakan_m.daftartindakan_id'])
            ->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tariftindakan_m.daftartindakan_id')
            ->where([
                'tariftindakan_m.dokter_id' => $payload['pegawai_id'],
                'tariftindakan_m.penjamin_id' =>  $dataPasien['penjamin_id'],
                'tariftindakan_m.kelaspelayanan_id' =>  $dataPasien['kelaspelayanan_id'],
                'daftartindakan_m.kelompoktindakan_id' => $visite_kode,
            ])->distinct()->asArray()->one();
        
        $getTindakanPelayanan = null;

        // get daftar tindakan id  when tindakan visite null
        $get_visit_dokter_Bylookup = (new \Doco\models\ConstantsId)->find()->select([
            'additional_value'
        ])->where([
            'kode_transaksi' => 'get_visit_dokter_Bylookup'
        ])->asArray()->one();


        // kalau tarif tindakan dokter blm di mappingkan, menggunakan tarif berdasarkan tindakan dari lookup
        if(isset($get_visit_dokter_Bylookup['additional_value'])){
            $get_visit_dokter_Bylookup = json_decode($get_visit_dokter_Bylookup['additional_value'], true);

            // dokter yang memiliki spesialis id tetapi dia bukan termasuk dokter spesialis 
            $shadow_spesialis = (new \Doco\models\ConstantsId)->find()->select([
                'additional_value'
            ])->where([
                'kode_transaksi' => 'shadow_spesialis'
            ])->asArray()->one();
            
            $shadow_spesialis = json_decode($shadow_spesialis['additional_value'], true);

            // get kode_id lookuptransaksi VisitDokterSpesialis
            $tindakanVisiteSpesialis = (new \Doco\models\ConstantsId)->find()->select([
                'kode_id'
            ])->where([
                'kode_transaksi' => 'VisitDokterSpesialis'
            ])->scalar();

            // get kode_id lookuptransaksi VisitDokterSubSpesialis
            $tindakanVisiteSubSpesialis = (new \Doco\models\ConstantsId)->find()->select([
                'kode_id'
            ])->where([
                'kode_transaksi' => 'VisitSubDokterSpesialis'
            ])->scalar();

            // get kode_id lookuptransaksi VisitDokterJaga
            $tindakanVisiteJaga = (new \Doco\models\ConstantsId)->find()->select([
                'kode_id'
            ])->where([
                'kode_transaksi' => 'VisitDokterJaga'
            ])->scalar();
    
            // kondisi pengambilan lookup by dokter
            if (empty($tindakanVisite)){
                if(!empty($spesialis_id)) {
                    if(in_array($spesialis_id,$shadow_spesialis)){
                        $tindakanVisite['daftartindakan_id'] = $tindakanVisiteJaga;
                    }else{
                        $subSpesialis = PegawaiSubSpesialis::find()
                        ->where([
                            'pegawai_id' => $payload['pegawai_id']
                        ])->asArray()->one();

                        if(!empty($subSpesialis)){
                            $tindakanVisite['daftartindakan_id'] = $tindakanVisiteSubSpesialis;
                        }else{
                            $tindakanVisite['daftartindakan_id'] = $tindakanVisiteSpesialis;
                        }

                    }
                  
                }else{
                    $tindakanVisite['daftartindakan_id'] = $tindakanVisiteJaga;
                }
            }
        }
       
        if(!empty($tindakanVisite)) {
            $getTindakanPelayanan = \app\modules\v1\models\TindakanPelayanan::find()->andWhere([
                'daftartindakan_id' => $tindakanVisite['daftartindakan_id'],
                'pendaftaran_id' => $payload['pendaftaran_id'],
                'pasienadmisi_id' => $payload['pasienadmisi_id'],
                'dokterpenanggungjawab_id' => $payload['pegawai_id'],
            ])
            ->andWhere([
                'between', 'tgl_tindakan', date('Y-m-d 00:00:00', strtotime($payload['tgl_cppt'])), date('Y-m-d 23:59:59', strtotime($payload['tgl_cppt']))
            ])->asArray()->one();            
        }

        if( empty($getTindakanPelayanan) && !empty($tindakanVisite)){
            Yii::error([
                'msg' => 'nah kalo masuk sini, berarti tindakan pelayanan nya belom ada buat hari ini',
                'data' => compact('dataPasien','tindakanVisite', 'spesialis_id')
            ]);
            $params['registrationData'] = $dataPasien;
            $params['registrationData']['tgl_transaksi'] = date('Y-m-d H:i:s', strtotime($payload['tgl_cppt']));
            $params['registrationData']['is_soap'] = true;

            (new \Doco\Services\KasirService)->tagihan($params['registrationData'], [
                [
                    'daftartindakan_id' => $tindakanVisite['daftartindakan_id'],
                    'qty' => 1,
                    'dokter_id' => (int) $payload['pegawai_id'],
                ]
            ]);
        }
        return true;
    }
}
?>