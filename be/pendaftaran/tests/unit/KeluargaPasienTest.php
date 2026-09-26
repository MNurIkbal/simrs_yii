<?php
use app\modules\v1\models\KeluargaPasien;

class KeluargaPasienTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new KeluargaPasien();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'keluarga_alamat' => 'china',
            'created_by' => 2, 
            'modified_count' => 3, 
            'last_modified_by' => 1, 
            'deleted_by' => 2, 
            'is_deleted' => true,
            'is_active' => false,
            'keluarga_nama' => 'tesss',
            'keluarga_hubungan' => 'baik baik saja',
            'keluarga_namadepan' => 'tn',
            'keluarga_jk' => 'laki laki',
            'keluarga_no_telepon' => '234234242'
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'keluarga_alamat' => 'china',
            'additional_data' => 1,
            'created_by' =>  'TESSSS', 
            'modified_count' =>  'TESSSS', 
            'last_modified_by' =>  'TESSSS', 
            'deleted_by' =>  'TESSSS', 
            'is_deleted' => 'salah',
            'is_active' => 'salah',
            'keluarga_nama' => 1,
            'keluarga_hubungan' => 2,
            'keluarga_namadepan' => 3,
            'keluarga_jk' => 4,
            'keluarga_no_telepon' => 5
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}