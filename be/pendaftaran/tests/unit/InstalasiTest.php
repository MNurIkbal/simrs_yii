<?php
use app\modules\v1\models\Instalasi;

class InstalasiTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new Instalasi();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'instalasi_nama' => 'rajal',
            'instalasi_singkatan' => 'rj',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'instalasi_nama' => null,
            'instalasi_singkatan' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'instalasi_nama' => 'rajal',
            'instalasi_singkatan' => 'rj',
            'instalasi_namalainnya' => 'Raja',
            'additional_data' => 'additional_data',
            'profilers_id' => 2,
            'riwayatruangan_id' => 1,
            'created_by' => 2, 
            'modified_count' => 3, 
            'last_modified_by' => 1, 
            'deleted_by' => 2, 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'instalasi_nama' => 1,
            'instalasi_singkatan' => 2,
            'instalasi_namalainnya' => 3,
            'additional_data' => 1,
            'profilers_id' => 'Tes',
            'riwayatruangan_id' => 'TES',
            'created_by' =>  'TESSSS', 
            'modified_count' =>  'TESSSS', 
            'last_modified_by' =>  'TESSSS', 
            'deleted_by' =>  'TESSSS', 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}