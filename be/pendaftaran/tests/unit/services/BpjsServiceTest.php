<?php
use app\modules\v1\services\Contracts\BpjsInterface;

class BpjsServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $service;
    
    protected function _before()
    {
        $this->service = Yii::$container->get(BpjsInterface::class);
    }

    public function testHistoryPelayananDataKartuNotFound()
    {
        $noka = "00012618";
        $message = "Data Tidak Ada";

        $bpjs = $this->service->historyPelayanan($noka);
        $this->assertEquals(201,$bpjs['metaData']['code']);
        $this->assertEquals($message,$bpjs['metaData']['message']);
    }

    public function testHistoryPelayananSukses()
    {
        $noka = "0001261832477";
        // $noka = "00012618324770";
        $bpjs = $this->service->historyPelayanan($noka);
        $this->assertEquals(200,$bpjs['metaData']['code']);
    }

    public function testHistoryPelayananSuksesWithData()
    {
        $noka = "00012618324770";
        $response = false;
        $bpjs = $this->service->historyPelayanan($noka);
        $this->assertEquals(200,$bpjs['metaData']['code']);
        $this->assertArrayHasKey('histori', $bpjs['response']);
    }

    public function testCariPasienBpjsByNamaParams()
    {
        $peserta = [];

        $unit = $this->service->cariPasienBpjsByNama($peserta);
        $this->assertEquals(422,$unit['status']);
    }

    public function testCariPasienBpjsByNamaExsistRecord()
    {
        // this is sample response from bpjs 
        $peserta = [
            'nama' => 'sad boy ariya',
            'tglLahir' => '1999-11-11',
            'sex' => 'L'
        ];

        $unit = $this->service->cariPasienBpjsByNama($peserta);
        $this->assertArrayHasKey('nama_pasien', $unit);
        $this->assertNotNull($unit);
        $this->assertEquals($peserta['nama'], $unit['nama_pasien']);
    }

    public function testCariPasienBpjsByNamaNotFound()
    {
        // this is sample response from bpjs 
        $peserta = [
            'nama' => 'sad boy ariya',
            'tglLahir' => '1999-11-11',
            'sex' => 'P'
        ];

        $unit = $this->service->cariPasienBpjsByNama($peserta);
        $this->assertNull($unit);
    }

    public function testCariPasienBpjsByRmParams()
    {
        $peserta = [];

        $unit = $this->service->cariPasienBpjsByRm($peserta);
        $this->assertEquals(422,$unit['status']);
    }

    public function testCariPasienBpjsByRmExistsRecord()
    {
        // this is sample response from bpjs 
        $peserta = [
            'mr' => '000643'
        ];

        $unit = $this->service->cariPasienBpjsByRm($peserta);
        $this->assertNotNull($unit);
        $this->assertArrayHasKey('nama_pasien', $unit);
    }

    public function testCariPasienBpjsByRmNotFound()
    {
        // this is sample response from bpjs 
        $peserta = [
            'mr' => '0'
        ];

        $unit = $this->service->cariPasienBpjsByRm($peserta);
        $this->assertNull($unit);
    }

    public function testCariPasienBpjsByNokartu()
    {
        $peserta = [
            'noKartu' => '0000053201215',
        ];

        $unit = $this->service->cariPasienBpjsByNokartu($peserta);
        $this->assertArrayHasKey('nama_pasien', $unit);
        $this->assertArrayHasKey('no_rekam_medik', $unit);
        $this->assertNotNull($unit);
    }

    public function testCariPasienBpjsByNokartuNotFound()
    {
        $peserta = [
            'noKartu' => '0000053201214',
        ];

        $unit = $this->service->cariPasienBpjsByNokartu($peserta);
        $this->assertFalse($unit);
    }

    public function testCariPasienBpjsByNik()
    {
        
        $peserta = [
            'nik' => '41001036371',
        ];

        $unit = $this->service->CariPasienBpjsByNik($peserta);
        $this->assertArrayHasKey('nama_pasien', $unit);
        $this->assertArrayHasKey('no_rekam_medik', $unit);
        $this->assertNotNull($unit);
    }

    public function testCariPasienBpjsByNikValidation()
    {
        
        $peserta = [
            'nikk' => '41001036371',
        ];

        $unit = $this->service->CariPasienBpjsByNik($peserta);
        $this->assertEquals(422,$unit['status']);
    }

    public function testCariPasienBpjsByNikNotFound()
    {
        $peserta = [
            'nik' => '099020219',
        ];

        $unit = $this->service->CariPasienBpjsByNik($peserta);
        $this->assertNull($unit);
    }

    public function testCariRujukanByNokartu()
    {
        // kalau jadi failed berarti rujukan nya sudah expired ganti nokartu nya
        $noka = '0002082092422'; 
        $type = 1;
        $unit = $this->service->cariRujukanByNoKartu($noka, $type);
        $this->assertEquals(200,$unit['metaData']['code']);
        $this->assertArrayHasKey('response', $unit);
        $this->assertArrayHasKey('rujukan', $unit['response']);
    }

    public function testCariRujukanByNokartuEmptyResponse()
    {
        $noka = '0000053201215';
        $type = 1;
        $unit = $this->service->cariRujukanByNoKartu($noka, $type);
        $this->assertEquals(201,$unit['metaData']['code']);
        $this->assertNull($unit['response']);
    }

    public function testCariListRujukanByNokartu()
    {
        // kalau jadi failed berarti rujukan nya sudah expired ganti nokartu nya
        $noka = '0002082092422';
        $type = 1;
        $unit = $this->service->cariListRujukanByNoKartu($noka, $type);
        $this->assertEquals(200,$unit['metaData']['code']);
        $this->assertArrayHasKey('response', $unit);
        $this->assertArrayHasKey('rujukan', $unit['response']);
        $this->assertInternalType('array', $unit['response']['rujukan']);
    }

    public function testCariListRujukanByNokartuEmptyResponse()
    {
        $noka = '0000053201215';
        $type = 1;
        $unit = $this->service->cariListRujukanByNoKartu($noka, $type);
        $this->assertEquals(201,$unit['metaData']['code']);
        $this->assertNull($unit['response']);
    }
}
