<?php

namespace Doco\Traits;
use Yii;

trait HistoryFisioTrait
{
    public function actionListHistoryFisio()
    {    	
    	$norm = Yii::$app->request->get('no_rekam_medik', null);
    	$pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
    	$pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
    	if(!empty($norm) && !empty($pendaftaran_id)) {
			return [
				'data_fisioterapi' => $this->getDataFisio($norm),
				'data_pasien' => $this->getDataPasien($pendaftaran_id, $pasienadmisi_id)
			];
    	} else {
            return [];
    	}
    }


    public function getDataPasien($pendaftaran_id, $pasienadmisi_id = null)
    {
        $data_pasien = (new \yii\db\Query())
            ->select([
                'pasien_id',
                'nama_pasien',
                'no_telepon_pasien',
                'alamat_pasien',
                'no_rekam_medik',
                'umur',
                'ruangan_nama',
                'no_pendaftaran',
                'jenis_kelamin',
                'tanggal_lahir',
                'carabayar_nama',
                'nama_pegawai as dokter_nama',
                'pegawai_id as dokter_id',
                'penjamin_nama',
                'pegawai_id as dokter_admisi_id',
                new \yii\db\Expression("CASE WHEN is_pasientitipan = true THEN kelas_ditagihkan_nama ELSE kelaspelayanan_nama END AS kelaspelayanan_nama"),
            ])
            ->from('infokunjunganrs_v')
            ->where(['pendaftaran_id' => $pendaftaran_id]);

        if ($pasienadmisi_id !== null) {
            $data_pasien->andWhere([
                'pasienadmisi_id' => $pasienadmisi_id,
            ]);
        }

        return $data_pasien->one();
    }

    public function getDataFisio($norm) {
        $data_fisioterapi = (new \yii\db\Query())
            ->select([
                'no_pendaftaran',
                'dokter_pemeriksa',
                'tgl_tindakan',
                'tindakan_obat',
            ])
            ->from('riwayattindakan_v')
            ->where(['no_rekam_medik' => $norm])
            ->andWhere("UPPER(ruangan_pelayanan) like '%FISIO%'")
            ->orderBy(['tgl_tindakan' => SORT_ASC])
            ->all();
        return $data_fisioterapi;
    }

    // public function getDataFisio($norm) {
    //     $data_fisioterapi = (new \yii\db\Query())
    //         ->select([
    //         	'pendaftaran_t.no_pendaftaran as no_pendaftaran',
    //         	'COALESCE(pegawai_m.nama_pegawai, \'-\') as dokter_pemeriksa',
    //         	'soapfisioterapi_t.tgl_soapfisioterapi as tgl_tindakan',
    //         	'daftartindakan_m.daftartindakan_nama as tindakan_obat',
    //         ])
    //         ->from('programterapidetail_t')
    //         ->leftJoin('programterapi_t', 'programterapi_t.programterapi_id = programterapidetail_t.programterapi_id')
    //         ->leftJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = programterapi_t.pasienmasukpenunjang_id')
    //         ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = programterapi_t.pendaftaran_id')
    //         ->leftJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
    //         ->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = programterapidetail_t.daftartindakan_id')
    //         ->leftJoin('soapfisioterapi_t', 'soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
    //         ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = soapfisioterapi_t.terapis_id')
    //         ->where(['pasien_m.no_rekam_medik' => $norm])
    //         ->orderBy(['soapfisioterapi_t.tgl_soapfisioterapi' => SORT_ASC])
    //         ->all();
    //     return $data_fisioterapi;
    // }
}