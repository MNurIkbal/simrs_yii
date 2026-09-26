<?php
use app\modules\v1\services\Contracts\PanggilAntrianInterface;

class PanggilAntrianPoliklinikServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $service;
    
    protected function _before()
    {
        $this->service = Yii::$container->get(PanggilAntrianInterface::class);
    }

    protected function _after()
    {
    }

    public function testPanggilAntrian()
    {
        $data = [
            'pendaftaran_id' => 8158
        ];

        $test = $this->service->panggilAntrian($data);
        $this->assertArrayHasKey('panggil_antrian_poliklinik_with_dokter', $test);
    }

    public function testPanggilAntrianDataNotFound()
    {
        $data = [
            'pendaftaran_id' => 1
        ];

        $test = $this->service->panggilAntrian($data);
        $this->assertArrayNotHasKey('panggil_antrian_poliklinik_with_dokter', $test);
    }
}