<?php

use app\modules\v1\models\CaraKeluar;

class CaraKeluarTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new CaraKeluar;
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'carakeluar_nama' => 'Sembuh',
            'carakeluar_kode' => 'SMBH',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'carakeluar_nama' => null,
            'carakeluar_kode' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'carakeluar_nama' => 'Sembuh',
            'carakeluar_kode' => 'SMBH',
            'carakeluar_urutan' => 7, 
            'catatan' => 'Pasien Keluar Sembuh', 
            'additional_data' => 'Informasi tambahan', 
            'carakeluar_namalain' => 'Cured', 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'carakeluar_nama' => (int) 1234,
            'carakeluar_kode' => (int) 555,
            'carakeluar_urutan' => 'Tujuh', 
            'catatan' => (int) 9990, 
            'additional_data' => ['data' => 'info'], 
            'carakeluar_namalain' => (int) 884, 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}