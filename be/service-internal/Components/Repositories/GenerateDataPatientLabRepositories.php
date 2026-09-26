<?php

namespace Integrasi\Components\Repositories;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use yii\db\Query;

class GenerateDataPatientLabRepositories {

    public function getData($penunjangId = null, $regisId = null, $unitId = null , $rekamMedik = null)
    {
        if (!empty($penunjangId) || !empty($regisId) || !empty($unitId) || !empty($rekamMedik)) {
            $integrasi = (new \yii\db\Query())
                        ->select([
                            'bridging_orderlab.pasienmasukpenunjang_id',
                            'bridging_orderlab.pasienkirimkeunitlain_id',
                            'bridging_orderlab.gender',
                            'bridging_orderlab.priority',
                            'bridging_orderlab.date_of_birth', 
                            'bridging_orderlab.ref_doctor_id', 
                            'bridging_orderlab.ref_doctor_name', 
                            'bridging_orderlab.order_time', 
                            'bridging_orderlab.address',
                            'bridging_orderlab.order_no',
                            'bridging_orderlab.patient_class',
                            'bridging_orderlab.patient_class_name',
                            'bridging_orderlab.pasien_id',
                            'bridging_orderlab.tests',
                            'bridging_orderlab.remove_tests',  
                            'bridging_orderlab.location_id',    
                            'bridging_orderlab.location_name',   
                            'pasien.no_telepon_pasien',
                            'pasien.nama_pasien',
                            'pasien.no_rekam_medik',  
                            'pendaftaran.pasienadmisi_id',     
                            'pendaftaran.no_pendaftaran',        
                            'pendaftaran.pendaftaran_id',   
                            'pendaftaran.penjamin_id',        
                            'pendaftaran.carabayar_id',    
                            'pendaftaran.is_aps',
                            'bridging_orderlab.instalasi_id',          
                            'carabayar.carabayar_nama',             
                            'carabayar.groupcarabayar_id',       
                            'pasienadmisi.kamarruangan_id',             
                            'kamarruangan.kamarruangan_kode',        
                            'kamartempattidur.no_tempattidur',      
                            'penjamin.penjamin_nama',
                            'umur',
                            'pasienadmisi.carabayar_id as carabayar_ranap',
                            'pasienadmisi.penjamin_id as penjamin_ranap',
                            'bridging_orderlab.ref_doctor_primary_id'
                        ])
                        ->from('bridging_orderlab_roche_v bridging_orderlab')
                        ->innerjoin('(select pendaftaran_id, instalasi_id, pasienadmisi_id, no_pendaftaran, penjamin_id, carabayar_id, is_aps, umur from pendaftaran_t ) pendaftaran' , 'pendaftaran.pendaftaran_id = bridging_orderlab.pendaftaran_id')
                        ->innerjoin('(select carabayar_id, carabayar_nama, groupcarabayar_id from carabayar_m ) carabayar' , 'carabayar.carabayar_id = pendaftaran.carabayar_id')
                        ->leftjoin('(select pasienadmisi_id, kamarruangan_id,kamartempattidur_id, carabayar_id, penjamin_id from pasienadmisi_t ) pasienadmisi' , 'pasienadmisi.pasienadmisi_id = pendaftaran.pasienadmisi_id')
                        ->leftjoin('(select kamarruangan_id, kamarruangan_kode from kamarruangan_m ) kamarruangan' , 'kamarruangan.kamarruangan_id = pasienadmisi.kamarruangan_id')
                        ->leftjoin('(select kamartempattidur_id, no_tempattidur from kamartempattidur_m ) kamartempattidur' , 'kamartempattidur.kamartempattidur_id = pasienadmisi.kamartempattidur_id')
                        ->innerjoin('(select pasien_id, no_telepon_pasien, nama_pasien, no_rekam_medik from pasien_m ) pasien' , 'pasien.pasien_id = bridging_orderlab.pasien_id')
                        ->innerjoin('(select penjamin_id, penjamin_nama from penjamin_m ) penjamin' , 'penjamin.penjamin_id = pendaftaran.penjamin_id');

            if (!empty($penunjangId)) {
                $integrasi->andWhere([
                    'bridging_orderlab.pasienmasukpenunjang_id' => $penunjangId
                ]);
            }

            if (!empty($regisId)) {
                $integrasi->andWhere([
                    'bridging_orderlab.pendaftaran_id' => $regisId
                ]);
            }

            if (!empty($unitId)) {
                $integrasi->andWhere([
                    'bridging_orderlab.pasienkirimkeunitlain_id' => $unitId
                ]);
            }

            if (!empty($rekamMedik)) {
                $integrasi->andWhere([
                    'pasien.no_rekam_medik' => $rekamMedik
                ]);
            }

            return $integrasi->all();
        }
        return [];
    }
}