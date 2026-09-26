<?php 
/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use Doco\components\DocoMessages;
use Doco\models\Pendaftaran;
use Doco\models\RiwayatPenyakitTrans;

class RiwayatPenyakitProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $dataRiwayat;
    protected $pendaftaran_id;
    protected $data_pendaftaran;
    protected $additional_data;

    /**
     * @return void
     * @throws Doco\exceptions\ValidationException
     */
    protected function validation()
    {
        $data_riwayat = $this->_requestData->post();
        $pendaftaran_id = $this->_requestData->post('pendaftaran_id', null);
        $this->pendaftaran_id = $pendaftaran_id;
        $data_pendaftaran = $this->getDataPendaftaran();
		    
        if (empty($data_pendaftaran)) {
            throw new \yii\base\Exception("Error Processing Request", 1);
        }
		
        $this->dataRiwayat = $data_riwayat;
    }

    /**
     * @return void
     */
    protected function save()
    {
    	$data = $this->dataRiwayat;
      $model = new RiwayatPenyakitTrans;
    	$cekData = RiwayatPenyakitTrans::find()
          ->where(['pendaftaran_id' => $this->pendaftaran_id])
          ->one();

      $model = !empty($cekData) ? $cekData : new RiwayatPenyakitTrans;

      $config = $this->checkPrimaConfig();
      if($config == 'prima'){
        $this->setAdditionalPrima();
      } else {
        $this->setAdditional();
      }

    	$model->additional_data = json_encode($this->additional_data);
      $model->attributes = $data;
    	if ($model->validate()) {
      	 $model->save();
    	} else {
      	$errors = DocoHelpers::parseError($model->errors, 'RiwayatPenyakitForm');
      	return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
        		'data' => $errors
      	]);
    	}
    }

	protected function getDataPendaftaran()
	{
		$model = Pendaftaran::findOne($this->pendaftaran_id);

		return $model;
	}

  protected function getDataRiwayat()
  {
    $model = RiwayatPenyakitTrans::find()
      ->where(['pendaftaran_id' => $this->pendaftaran_id])
      ->one();

    return $model;
  }

  protected function setAdditional()
  {
    $data = $this->dataRiwayat;
    $additional_data = [
      'keluhan' => $data['keluhan'],
      'riwayat_penyakit_terdahulu' => [
        'riwayat_diderita' => ArrayHelper::getValue($data,'riwayat_diderita'),
        'riwayat_diderita_catatan' => ArrayHelper::getValue($data, 'riwayat_diderita_catatan'),
        'riwayat_alergi' => ArrayHelper::getValue($data, 'riwayat_alergi'),
        'riwayat_alergi_catatan' => ArrayHelper::getValue($data, 'riwayat_alergi_catatan'),
        'riwayat_dirawat_rs' => ArrayHelper::getValue($data, 'riwayat_dirawat_rs'),
        'riwayat_dirawat_rs_catatan' => ArrayHelper::getValue($data, 'riwayat_dirawat_rs_catatan'),
        'riwayat_operasi' => ArrayHelper::getValue($data, 'riwayat_operasi'),
        'riwayat_operasi_catatan' => ArrayHelper::getValue($data, 'riwayat_operasi_catatan'),
        'riwayat_imunisasi' => ArrayHelper::getValue($data, 'riwayat_imunisasi'),
        'riwayat_imunisasi_catatan' => ArrayHelper::getValue($data, 'riwayat_imunisasi_catatan'),
      ],
      'khusus_perempuan' => [
        'menstruasi' => ArrayHelper::getValue($data,'menstruasi'),
        'riwayat_kontrasepsi' => ArrayHelper::getValue($data,'riwayat_kontrasepsi'),
        'riwayat_melahirkan' => ArrayHelper::getValue($data,'riwayat_melahirkan'),
        'riwayat_keguguran' => ArrayHelper::getValue($data,'riwayat_keguguran'),
        'sedang_hamil' => ArrayHelper::getValue($data,'sedang_hamil'),
        'riwayat_pap_smear' => ArrayHelper::getValue($data,'riwayat_pap_smear'),
        'riwayat_penyakit_keluarga' => ArrayHelper::getValue($data,'riwayat_penyakit_keluarga'),
      ],
      'kebiasaan' => [
        'rokok' => ArrayHelper::getValue($data, 'rokok'),
        'alkohol' => ArrayHelper::getValue($data, 'alkohol'),
        'kopi' => ArrayHelper::getValue($data, 'kopi'),
        'olahraga' => ArrayHelper::getValue($data, 'olahraga'),
        'diet' => ArrayHelper::getValue($data, 'diet'),
        'tidur' => ArrayHelper::getValue($data, 'tidur'),
        'obat_rutin' => ArrayHelper::getValue($data, 'obat_rutin'),
      ],
    ];

    $this->additional_data = $additional_data;
  }

  /**
   * Setting payload untuk RS Prima.
   */
  private function setAdditionalPrima()
  {
    $data = $this->dataRiwayat;
    $additional_data = [
      'pasien_phr' => [
        'pekerjaan' => ArrayHelper::getValue($data, 'pekerjaan'),
        'lokasi_kerja' => ArrayHelper::getValue($data, 'lokasi_kerja'),
        'matriks_pemeriksaan' => ArrayHelper::getValue($data, 'matriks_pemeriksaan'),
        'nama_perusaahan' => ArrayHelper::getValue($data, 'nama_perusaahan'),
        'tipe_pekerja' => ArrayHelper::getValue($data, 'tipe_pekerja'),
        'prosedur_pemeriksaan' => ArrayHelper::getValue($data, 'prosedur_pemeriksaan'),
        'prosedur_pemeriksaan_text' => ArrayHelper::getValue($data, 'prosedur_pemeriksaan_text'),
      ],
      'riwayat_pekerjaan' => [
        'tahun_mulai' => ArrayHelper::getValue($data,'tahun_mulai'),
        'tahun_selesai' => ArrayHelper::getValue($data,'tahun_selesai'),
        'perusahaan' => ArrayHelper::getValue($data,'perusahaan'),
        'jabatan' => ArrayHelper::getValue($data,'jabatan'),
        'uraian_singkat' => ArrayHelper::getValue($data,'uraian_singkat'),
        'paparan_tidak_ada' => ArrayHelper::getValue($data,'paparan_tidak_ada'),
        'paparan_bising' => ArrayHelper::getValue($data,'paparan_bising'),
        'paparan_kimia' => ArrayHelper::getValue($data,'paparan_kimia'),
        'paparan_radiasi' => ArrayHelper::getValue($data,'paparan_radiasi'),
        'paparan_stress' => ArrayHelper::getValue($data,'paparan_stress'),
        'paparan_ergonomis' => ArrayHelper::getValue($data,'paparan_ergonomis'),
        'paparan_lainnya' => ArrayHelper::getValue($data,'paparan_lainnya'),
        'paparan_secara_singkat' => ArrayHelper::getValue($data,'paparan_secara_singkat'),
        'resiko_confined_space' => ArrayHelper::getValue($data,'resiko_confined_space'),
        'resiko_operator_berat' => ArrayHelper::getValue($data,'resiko_operator_berat'),
        'resiko_tangki_penyelam' => ArrayHelper::getValue($data,'resiko_tangki_penyelam'),
        'resiko_security' => ArrayHelper::getValue($data,'resiko_security'),
        'resiko_bekerja_ketinggian' => ArrayHelper::getValue($data,'resiko_bekerja_ketinggian'),
        'resiko_awak_mobil' => ArrayHelper::getValue($data,'resiko_awak_mobil'),
        'resiko_pengemudi' => ArrayHelper::getValue($data,'resiko_pengemudi'),
        'resiko_fire_brigade' => ArrayHelper::getValue($data,'resiko_fire_brigade'),
        'resiko_lain' => ArrayHelper::getValue($data,'resiko_lain'),
        'resiko_lain_text' => ArrayHelper::getValue($data,'resiko_lain_text'),
      ],
      'tanda_vital' => [
        'vital_tekanan_darah' => ArrayHelper::getValue($data, 'vital_tekanan_darah'),
        'vital_nadi' => ArrayHelper::getValue($data, 'vital_nadi'),
        'vital_suhu' => ArrayHelper::getValue($data, 'vital_suhu'),
        'vital_respirasi' => ArrayHelper::getValue($data, 'vital_respirasi'),
      ],
      'keluhan_saatini' => [
        'keluhan_saat_ini' => ArrayHelper::getValue($data, 'keluhan_saat_ini'),
        'keluhan_migrain' => ArrayHelper::getValue($data, 'keluhan_migrain'),
        'keluhan_migrain_text' => ArrayHelper::getValue($data, 'keluhan_migrain_text'),
        'keluhan_epilepsi' => ArrayHelper::getValue($data, 'keluhan_epilepsi'),
        'keluhan_epilepsi_text' => ArrayHelper::getValue($data, 'keluhan_epilepsi_text'),
        'keluhan_gangguan_pengelihatan' => ArrayHelper::getValue($data, 'keluhan_gangguan_pengelihatan'),
        'keluhan_gangguan_pengelihatan_text' => ArrayHelper::getValue($data, 'keluhan_gangguan_pengelihatan_text'),
        'keluhan_gangguan_pendengaran' => ArrayHelper::getValue($data, 'keluhan_gangguan_pendengaran'),
        'keluhan_gangguan_pendengaran_text' => ArrayHelper::getValue($data, 'keluhan_gangguan_pendengaran_text'),
        'keluhan_masalah_hidung' => ArrayHelper::getValue($data, 'keluhan_masalah_hidung'),
        'keluhan_masalah_hidung_text' => ArrayHelper::getValue($data, 'keluhan_masalah_hidung_text'),
        'keluhan_tbc' => ArrayHelper::getValue($data, 'keluhan_tbc'),
        'keluhan_tbc_text' => ArrayHelper::getValue($data, 'keluhan_tbc_text'),
        'keluhan_pneumonia' => ArrayHelper::getValue($data, 'keluhan_pneumonia'),
        'keluhan_pneumonia_text' => ArrayHelper::getValue($data, 'keluhan_pneumonia_text'),
        'keluhan_asma' => ArrayHelper::getValue($data, 'keluhan_asma'),
        'keluhan_asma_text' => ArrayHelper::getValue($data, 'keluhan_asma_text'),
        'keluhan_gangguang_saluran' => ArrayHelper::getValue($data, 'keluhan_gangguang_saluran'),
        'keluhan_gangguang_saluran_text' => ArrayHelper::getValue($data, 'keluhan_gangguang_saluran_text'),
        'keluhan_hernia' => ArrayHelper::getValue($data, 'keluhan_hernia'),
        'keluhan_hernia_text' => ArrayHelper::getValue($data, 'keluhan_hernia_text'),
        'keluhan_nyeri_dada' => ArrayHelper::getValue($data, 'keluhan_nyeri_dada'),
        'keluhan_nyeri_dada_text' => ArrayHelper::getValue($data, 'keluhan_nyeri_dada_text'),
        'keluhan_penyakit_ginjal' => ArrayHelper::getValue($data, 'keluhan_penyakit_ginjal'),
        'keluhan_penyakit_ginjal_text' => ArrayHelper::getValue($data, 'keluhan_penyakit_ginjal_text'),
        'keluhan_batu_ginjal' => ArrayHelper::getValue($data, 'keluhan_batu_ginjal'),
        'keluhan_batu_ginjal_text' => ArrayHelper::getValue($data, 'keluhan_batu_ginjal_text'),
        'keluhan_penyakit_kulit' => ArrayHelper::getValue($data, 'keluhan_penyakit_kulit'),
        'keluhan_penyakit_kulit_text' => ArrayHelper::getValue($data, 'keluhan_penyakit_kulit_text'),
        'keluhan_riwayat_kecelakaan' => ArrayHelper::getValue($data, 'keluhan_riwayat_kecelakaan'),
        'keluhan_riwayat_kecelakaan_text' => ArrayHelper::getValue($data, 'keluhan_riwayat_kecelakaan_text'),
        'keluhan_riwayat_inap_rs' => ArrayHelper::getValue($data, 'keluhan_riwayat_inap_rs'),
        'keluhan_riwayat_inap_rs_text' => ArrayHelper::getValue($data, 'keluhan_riwayat_inap_rs_text'),
        'keluhan_riwayat_operasi' => ArrayHelper::getValue($data, 'keluhan_riwayat_operasi'),
        'keluhan_riwayat_operasi_text' => ArrayHelper::getValue($data, 'keluhan_riwayat_operasi_text'),
        'keluhan_alergi' => ArrayHelper::getValue($data, 'keluhan_alergi'),
        'keluhan_alergi_text' => ArrayHelper::getValue($data, 'keluhan_alergi_text'),
        'keluhan_demam_reumatik' => ArrayHelper::getValue($data, 'keluhan_demam_reumatik'),
        'keluhan_demam_reumatik_text' => ArrayHelper::getValue($data, 'keluhan_demam_reumatik_text'),
        'keluhan_demam_typhoid' => ArrayHelper::getValue($data, 'keluhan_demam_typhoid'),
        'keluhan_demam_typhoid_text' => ArrayHelper::getValue($data, 'keluhan_demam_typhoid_text'),
        'keluhan_demam_berdarah' => ArrayHelper::getValue($data, 'keluhan_demam_berdarah'),
        'keluhan_demam_berdarah_text' => ArrayHelper::getValue($data, 'keluhan_demam_berdarah_text'),
        'keluhan_malaria' => ArrayHelper::getValue($data, 'keluhan_malaria'),
        'keluhan_malaria_text' => ArrayHelper::getValue($data, 'keluhan_malaria_text'),
        'keluhan_hepatitis' => ArrayHelper::getValue($data, 'keluhan_hepatitis'),
        'keluhan_hepatitis_text' => ArrayHelper::getValue($data, 'keluhan_hepatitis_text'),
        'keluhan_diabetes' => ArrayHelper::getValue($data, 'keluhan_diabetes'),
        'keluhan_diabetes_text' => ArrayHelper::getValue($data, 'keluhan_diabetes_text'),
        'keluhan_nyeri_sendi' => ArrayHelper::getValue($data, 'keluhan_nyeri_sendi'),
        'keluhan_nyeri_sendi_text' => ArrayHelper::getValue($data, 'keluhan_nyeri_sendi_text'),
        'keluhan_nyeri_punggung' => ArrayHelper::getValue($data, 'keluhan_nyeri_punggung'),
        'keluhan_nyeri_punggung_text' => ArrayHelper::getValue($data, 'keluhan_nyeri_punggung_text'),
        'keluhan_varises' => ArrayHelper::getValue($data, 'keluhan_varises'),
        'keluhan_varises_text' => ArrayHelper::getValue($data, 'keluhan_varises_text'),
        'keluhan_kanker' => ArrayHelper::getValue($data, 'keluhan_kanker'),
        'keluhan_kanker_text' => ArrayHelper::getValue($data, 'keluhan_kanker_text'),
        'keluhan_psikiatrik' => ArrayHelper::getValue($data, 'keluhan_psikiatrik'),
        'keluhan_psikiatrik_text' => ArrayHelper::getValue($data, 'keluhan_psikiatrik_text'),
        'keluhan_penyakit_kelamin' => ArrayHelper::getValue($data, 'keluhan_penyakit_kelamin'),
        'keluhan_penyakit_kelamin_text' => ArrayHelper::getValue($data, 'keluhan_penyakit_kelamin_text'),
        'keluhan_masalah_kebidanan' => ArrayHelper::getValue($data, 'keluhan_masalah_kebidanan'),
        'keluhan_hpht' => ArrayHelper::getValue($data, 'keluhan_hpht'),
        'keluhan_menarche' => ArrayHelper::getValue($data, 'keluhan_menarche'),
        'keluhan_keteranganobat' => ArrayHelper::getValue($data, 'keluhan_keteranganobat'),
        'keluhan_keteranganobat_text' => ArrayHelper::getValue($data, 'keluhan_keteranganobat_text'),
        'keluhan_keteranganobat_text' => ArrayHelper::getValue($data, 'keluhan_keteranganobat_text'),
        'keluhan_perubahanbb' => ArrayHelper::getValue($data, 'keluhan_perubahanbb'),
        'keluhan_perubahanbb_type' => ArrayHelper::getValue($data, 'keluhan_perubahanbb_type'),
        'keluhan_perubahanbb_kg' => ArrayHelper::getValue($data, 'keluhan_perubahanbb_kg'),
        'keluhan_perubahanbb_napsumakan' => ArrayHelper::getValue($data, 'keluhan_perubahanbb_napsumakan'),
        'keluhan_merokok' => ArrayHelper::getValue($data, 'keluhan_merokok'),
        'keluhan_merokok_jenis' => ArrayHelper::getValue($data, 'keluhan_merokok_jenis'),
        'keluhan_merokok_jumlah' => ArrayHelper::getValue($data, 'keluhan_merokok_jumlah'),
        'keluhan_merokok_sejak' => ArrayHelper::getValue($data, 'keluhan_merokok_sejak'),
        'keluhan_alkohol' => ArrayHelper::getValue($data, 'keluhan_alkohol'),
        'keluhan_perubahanbb_kg' => ArrayHelper::getValue($data, 'keluhan_perubahanbb_kg'),
        'keluhan_alkohol_jenis' => ArrayHelper::getValue($data, 'keluhan_alkohol_jenis'),
        'keluhan_alkohol_jumlah' => ArrayHelper::getValue($data, 'keluhan_alkohol_jumlah'),
        'keluhan_alkohol_sejak' => ArrayHelper::getValue($data, 'keluhan_alkohol_sejak'),
        'keluhan_lainnya' => ArrayHelper::getValue($data, 'keluhan_lainnya'),
        'keluhan_lainnya_text' => ArrayHelper::getValue($data, 'keluhan_lainnya_text'),
      ],
      'kebiasaan_olahraga' => [
        'tingkat_kebiasaan_olahraga' => ArrayHelper::getValue($data, 'tingkat_kebiasaan_olahraga'),
        'jenis_olahraga' => ArrayHelper::getValue($data, 'jenis_olahraga')
      ],
      'penyakit_keluarga' => [
        'penyakit_jantung_stroke' => ArrayHelper::getValue($data, 'penyakit_jantung_stroke'),
        'penyakit_kanker_tumor' => ArrayHelper::getValue($data, 'penyakit_kanker_tumor'),
        'penyakit_riwayat_saudara' => ArrayHelper::getValue($data, 'penyakit_riwayat_saudara'),
      ]
    ];

    $this->additional_data = $additional_data;
  }

  private function checkPrimaConfig()
  {
    $mcuConfig = (new DocoConstansId)->actionGetAdditional('format_mcu');
    return $mcuConfig;
  }

  protected function processFlow()
  {
    $this->validation();
    $this->startDBTransaction();
    $this->getDataRiwayat();
    $this->save();
    $this->commitDBTransaction();
    return [
      'message' => 'Data Berhasil di simpan', 
    ];
  }
}