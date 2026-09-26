<?php
use app\modules\v1\models\Reseptur;

class ResepturTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new Reseptur();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'reseptur_t');
    }
        
    public function testValidateRequiredPositif()
    {
        $payload = [
            'ruanganreseptur_id' => 1, 
            'tglreseptur' => '2022-07-08',
            'pendaftaran_id' => 1,
            'pegawai_id' => 1,
            'pasien_id' => 1,
            'ruangan_id' => 1,
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'ruanganreseptur_id' => null, 
            'tglreseptur' => null,
            'pendaftaran_id' => null,
            'pegawai_id' => null,
            'pasien_id' => null,
            'ruangan_id' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'reseptur_id' => 1,
            'pasienadmisi_id' => 1,
            'ruangan_id' => 1,
            'pasien_id' => 1,
            'pegawai_id' => 1,
            'pendaftaran_id' => 1,
            'penjualanresep_id' => 1,
            'tglreseptur' => '2022-07-08',
            'noresep' => 'tes',
            'ruanganreseptur_id' => 1,
            'status_reseptur' => 1,
            'antrian_id' => 1,
            'additional_data' => 'tes',
            'created_date' => '2022-07-08',
            'created_by' => 1,
            'modified_count' => 1,
            'last_modified_date' => '2022-07-08',
            'last_modified_by' => 1,
            'is_deleted' => false,
            'is_active' => true,
            'deleted_date' => '2022-07-08',
            'deleted_by' => 1,
            'instruksi_id' => 1,
            'is_hamil' => true,
            'berat_badan' => 43,
            'tinggi_badan' => 158,
            'luas_tubuh' => 'tes',
            'diagnosa_id' => 1,
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'reseptur_id' => "test",
            'pasienadmisi_id' => "test",
            'ruangan_id' => "test",
            'pasien_id' => "test",
            'pegawai_id' => "test",
            'pendaftaran_id' => "test",
            'penjualanresep_id' => "test",
            'tglreseptur' => 1,
            'noresep' => 1,
            'ruanganreseptur_id' => "tess",
            'status_reseptur' => "tess",
            'antrian_id' => "tess",
            'additional_data' => 1,
            'created_date' => 1,
            'created_by' => "tess",
            'modified_count' => "tess",
            'last_modified_date' => 1,
            'last_modified_by' => "tessss",
            'is_deleted' => "tess",
            'is_active' => "tess",
            'deleted_date' => 1,
            'deleted_by' => "tesss",
            'instruksi_id' => "tesss",
            'is_hamil' => 1,
            'berat_badan' => "tess",
            'tinggi_badan' => "tess",
            'luas_tubuh' => 1,
            'diagnosa_id' => "tes",
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}