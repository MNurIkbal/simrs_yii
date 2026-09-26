<?php

use app\modules\v1\payload\BpjsNewForm;

class BpjsNewFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;

    protected function _before()
    {
        $this->model = new BpjsNewForm;
    }

    protected function _after()
    {
    }

    // tests
    public function testBpjsNewFormScenario()
    {
        
        $payload_false = [
            // Date format berpengaruh
            'asal_rujukan' => '123123', 
            'jenis_pelayanan' => 123123, 
            'no_kartu' => 123123, 
            'tanggal_sep' => '2022-02-22', 
            'tanggal_rujukan' => '2022-02-22', 
            'diagnosa_awal' => '123123',
            'no_telp' => '123123', 
            'kasus_kecelakaan' => '123123', 
        ];
        $this->model->attributes = $payload_false;
        $this->assertTrue($this->model->validate());

        $payload_true = [
            'asal_rujukan' => '123123', 
            'jenis_pelayanan' => 123123, 
            'no_kartu' => 123123, 
            'tanggal_sep' => '2022-02-22', 
            'tanggal_rujukan' => '2022-02-22', 
            'diagnosa_awal' => '123123',
            'no_telp' => '123123', 
            'kasus_kecelakaan' => '123123', 
            'no_surat_kontrol' => 1313131, 
            'kode_dpjp' => 131313, 
            'kode_dpjp_melayani' => 131313
        ];
        $this->model->attributes = $payload_true;
        $this->model->scenario = 'skdp';
        $this->assertTrue($this->model->validate());

    }
}