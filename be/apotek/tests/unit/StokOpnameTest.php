<?php
use app\modules\v1\models\StokOpname;

class StokOpnameTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new StokOpname();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'stokopname_t');
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'ruangan_id' => 1, 
            'formulirstokopname_id' => 1, 
            'mengetahui_id' => 2,
            'petugas1_id' => 1, 
            'created_by' => 1, 
            'modified_count' => 1, 
            'last_modified_by' => 2, 
            'deleted_by' => 3, 
            'last_modified_by' => 1, 
            'deleted_by' => 2, 
            'nostokopname' => 'tes',  
            'jenisstokopname' => 'tes', 
            'keterangan_opname' => 'tes', 
            'additional_data' => 'aa', 
            'tglstokopname' => '2022-07-18', 
            'created_date' => '2022-07-18',
            'last_modified_date' => '2022-07-18',
            'deleted_date' => '2022-07-18',
            'totalharga_fisik' => 20000,
            'totalharga_sistem' => 20000,
            'is_verifikasi' => true,
            'tglverifikasi' => '2022-07-18',
            'pegawaiverifikasi_id' => 1,
            'ruanganinput_id' => 1,
            'tgl_implementasi' => '2022-07-18',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'ruangan_id' => 'tes', 
            'formulirstokopname_id' => 'tes', 
            'mengetahui_id' =>'tes', 
            'petugas1_id' =>'tes',  
            'created_by' =>'tes',  
            'modified_count' =>'tes',  
            'last_modified_by' =>'tes',  
            'deleted_by' =>'tes',  
            'last_modified_by' =>'tes',  
            'deleted_by' =>'tes',  
            'nostokopname' =>  1, 
            'jenisstokopname' =>   1,
            'keterangan_opname' => 1,
            'additional_data' => 1, 
            'tglstokopname' => 1, 
            'created_date' => 2,
            'last_modified_date' => 2,
            'deleted_date' => 2,
            'totalharga_fisik' => 'ss',
            'totalharga_sistem' => 'ss',
            'is_verifikasi' => 'aa',
            'tglverifikasi' => 2,
            'pegawaiverifikasi_id' => 'tes',
            'ruanganinput_id' => 'tes',
            'tgl_implementasi' => 2,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}