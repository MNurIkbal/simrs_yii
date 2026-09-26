<?php
use app\modules\v1\models\StokOpnameDetail;

class StokOpnameDetailTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new StokOpnameDetail();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'stokopnamedetail_t');
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'stokopnamedetail_id' => 1, 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'stokopnamedetail_id' => null, 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'stokopnamedetail_id' => 1, 
            'formstokopname_id' => 1, 
            'satuankecil_id' => 2,
            'sumberdana_id' => 1, 
            'stokopname_id' => 1, 
            'created_by' => 2, 
            'modified_count' => 3, 
            'last_modified_by' => 1, 
            'deleted_by' => 2, 
            'volume_fisik' => 30,  
            'volume_sistem' => 30, 
            'hargasatuan' => 30, 
            'jumlahharga' => 20000, 
            'harganetto' => 20000, 
            'jumlahnetto' => 20000,
            'jmlselisihstok' => 20000,
            'stok_akhir' => 20000,
            'selisih_akhir' => 20000,
            'revisi_stok' => 20000,
            'revisi_stok' => 60,
            'tglkadaluarsa' => '2022-07-18',
            'tglperiksafisik' => '2022-07-18',
            'created_date' => '2022-07-18',
            'last_modified_date' => '2022-07-18',
            'deleted_date' => '2022-07-18',
            'stokobatalkes_id' => 2,
            'kondisibarang' => 'tes',
            'additional_data' => 'tes',
            'is_deleted' => false,
            'is_active' => true,
            'is_newso' => true,
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'stokopnamedetail_id' => 'tes', 
            'formstokopname_id' => 'tes', 
            'satuankecil_id' => 'tes',
            'sumberdana_id' => 'tes', 
            'stokopname_id' => 'tes', 
            'obatalkes_id' => 'tes', 
            'created_by' => 'tes', 
            'modified_count' => 'tes', 
            'last_modified_by' =>'tes', 
            'deleted_by' => 'tes', 
            'volume_fisik' => 'tes',  
            'volume_sistem' => 'tes', 
            'hargasatuan' => 'tes', 
            'jumlahharga' => 'tes', 
            'harganetto' => 'tes', 
            'jumlahnetto' => 'tes',
            'jmlselisihstok' => 'tes',
            'stok_akhir' => 'tes',
            'selisih_akhir' => 'tes',
            'revisi_stok' => 'tes',
            'revisi_stok' => 'tes',
            'tglkadaluarsa' => 1,
            'tglperiksafisik' => 1,
            'created_date' => 1,
            'last_modified_date' => 1,
            'deleted_date' => 1,
            'stokobatalkes_id' => 'tes',
            'kondisibarang' => 2,
            'additional_data' => 2,
            'is_deleted' => 'tes',
            'is_active' => 'tes',
            'is_newso' => 'tes',
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}