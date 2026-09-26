<?php

use app\modules\v1\models\PemakaianBarangDetail;

class PemakaianBarangDetailTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before() {
        $this->model = new PemakaianBarangDetail();
    }

    protected function _after() {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'pemakaianbarangdetail_t');
    }

    public function testTipeDataPositif() {
        $this->model->attributes = $this->setPayloadTipeData(true);
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif() {
        $this->model->attributes = $this->setPayloadTipeData(false);
        $this->assertFalse($this->model->validate());
    }

    public function testMaxStringCatatanPositif() {
        $nomutasi_barang = strlen('Untuk keperluan ruangan');
        $this->assertLessThanOrEqual(200, $nomutasi_barang);
    }

    public function testMaxStringCatatanNegatif() {
        $nomutasi_barang = strlen('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed laoreet dapibus quam, eget commodo leo auctor sit amet. Nulla imperdiet ac ante ut rutrum. Donec accumsan maximus ligula sit amet luctus. Cras sed lorem pulvinar, scelerisque leo quis, eleifend velit. In a condimentum nulla. Morbi lacus sapien, commodo quis ultricies eu, vehicula sit amet justo. Nulla aliquet risus at libero pharetra accumsan. Etiam in tortor pretium, pharetra quam ut, consequat tortor.
        Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec ullamcorper lacinia dui nec tristique. Cras vestibulum malesuada bibendum. Curabitur nec sollicitudin urna. Fusce maximus felis metus, eu maximus metus malesuada at. Curabitur malesuada orci metus, sed eleifend mi tincidunt at. Duis consectetur mauris sit amet quam pharetra tincidunt. Maecenas tempor lorem arcu, id viverra nibh hendrerit eget. Integer a tellus et dolor facilisis ultricies. Mauris eleifend ut nunc ac volutpat. Curabitur luctus non tortor a vehicula. Nullam ac arcu fringilla, sagittis ante at, sodales ipsum. Quisque purus neque, tempor ac felis a, sollicitudin ultrices nunc.
        Mauris consequat quam massa, a scelerisque est vulputate et. Pellentesque ultrices dolor et elit tincidunt consectetur. Morbi tincidunt pharetra blandit. Nam vitae vehicula augue. Nunc vestibulum finibus risus. Sed pharetra orci eget euismod consequat. Praesent.');
        $this->assertGreaterThan(200, $nomutasi_barang);
    }

    public function testRequiredPositif() {
        $this->model->attributes = [
            'pemakaianbarang_id' => 1,
            'barang_id' => 22,
            'jumlah_pakai' => 5,
            'harga_netto' => 1200,
            'ppn' => 11,
            'harga_jual' => 1000
        ];
        $this->assertTrue($this->model->validate());
    }

    public function testRequiredNegatif() {
        $this->model->attributes = [
            'pemakaianbarang_id' => null,
            'barang_id' => null,
            'jumlah_pakai' => null,
            'harga_netto' => null,
            'ppn' => null,
            'harga_jual' => null
        ];
        $this->assertFalse($this->model->validate());
    }

    private function setPayloadTipeData($isPositif = true) {
        return [
            'pemakaianbarang_id' => $isPositif ? 1 : '1',
            'barang_id' => $isPositif ? 22 : '22',
            'jumlah_pakai' => $isPositif ? 5 : '5',
            'harga_netto' => $isPositif ? 1200 : '1200',
            'ppn' => $isPositif ? 11 : '11%',
            'harga_jual' => $isPositif ? 1000 : '1000',
            'hpp' => $isPositif ? 1000 : '1000',
            'additional_data' => $isPositif ? '[{"key": "attr"}]' : 9999,
            'catatan_barang' => $isPositif ? 'Untuk pemakaian ruangan' : 123
        ];
    }
}