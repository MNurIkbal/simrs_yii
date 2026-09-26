<?php
use app\modules\v1\models\Antrian;

class AntrianTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new Antrian();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'ruangan_id' => null, 
            'tgl_antrian' =>null, 
            'no_antrian' => null
        ];
        $this->model->attributes = $payload;
        var_dump($this->model->validate(), $this->model->getErrors());
        ob_flush();
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'ruangan_id' => null, 
            'tgl_antrian' => null, 
            'no_antrian' => null
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'ruangan_id' => 1, 
            'tgl_antrian' => '2022-01-01', 
            'no_antrian' => 'NO1',
            'pendaftaran_id' => 1, 
            'layarantrian_id' => 1, 
            'loket_id' => 1, 
            'created_by' => 2, 
            'modified_count' => 3, 
            'last_modified_by' => 1, 
            'deleted_by' => 2, 
            'pasien_id' => 3, 
            'penjamin_id' => 5, 
            'pegawai_id' => 6, 
            'status_antrian' => 7, 
            'status_pasien' => 8, 
            'slot_sequence' => 9,
            'additional_data' => 'TESSSS'
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'ruangan_id' => null, 
            'tgl_antrian' => null, 
            'no_antrian' => null,
            'pendaftaran_id' =>  'TESSSS', 
            'layarantrian_id' =>  'TESSSS', 
            'loket_id' =>  'TESSSS', 
            'created_by' =>  'TESSSS', 
            'modified_count' =>  'TESSSS', 
            'last_modified_by' =>  'TESSSS', 
            'deleted_by' =>  'TESSSS', 
            'pasien_id' =>  'TESSSS', 
            'penjamin_id' =>  'TESSSS', 
            'pegawai_id' =>  'TESSSS', 
            'status_antrian' =>  'TESSSS', 
            'status_pasien' =>  'TESSSS', 
            'slot_sequence' =>  'TESSSS',
            'additional_data' => 'TESSSS'
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}