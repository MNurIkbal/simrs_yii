<?php
use app\modules\v1\models\AsuransiPasien;

class AsuransiTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new AsuransiPasien();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'pasien_id' => 1,
            'penjamin_id' => 2,
            'carabayar_id' => 3,
            'nokartuasuransi' => '21312312333'
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'pasien_id' => null,
            'penjamin_id' => null,
            'carabayar_id' => null,
            'nokartuasuransi' => null
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'pasien_id' => 1,
            'jenispeserta_id' => 1,
            'kelastanggunganasuransi_id' => 2,
            'penjamin_id' => 1,
            'carabayar_id' => 1,
            'created_by' => 2, 
            'modified_count' => 1, 
            'last_modified_by' => 1, 
            'deleted_by' => 1,
            'nokartuasuransi' => 'NO11222222',
            'nopeserta' => 'NO11222222', 
            'namapemilikasuransi' => 'Udin', 
            'kodefeskestk1' => 'YY1', 
            'kodefeskesgigi' => 'YY2', 
            'namaperusahaan' => 'TES', 
            'nomorpokokperusahaan' => 'GGGGG', 
            'status_konfirmasi' => 'YA', 
            'nama_asuransi' => 'AXA'
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'pasien_id' => null,
            'penjamin_id' => null,
            'carabayar_id' => null,
            'nokartuasuransi' => null,
            'jenispeserta_id' => 'YES',
            'kelastanggunganasuransi_id' => 'YES',
            'created_by' => 'YES', 
            'modified_count' => 'YES', 
            'last_modified_by' => 'YES', 
            'deleted_by' => 'YES',
            'nopeserta' => 1, 
            'namapemilikasuransi' => 2, 
            'kodefeskestk1' => 3, 
            'kodefeskesgigi' => 4, 
            'namaperusahaan' => 5, 
            'nomorpokokperusahaan' => 6, 
            'status_konfirmasi' => 7, 
            'nama_asuransi' => 8
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}