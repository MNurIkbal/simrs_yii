<?php

use app\modules\v1\models\InformasiPemakaianBarang;

class InformasiPemakaianBarangTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before() {
        $this->model = new InformasiPemakaianBarang();
        $this->str520 = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi commodo justo et diam ultricies hendrerit. Cras eu diam lectus. Fusce egestas, velit non laoreet malesuada, mauris mauris tempor ipsum, imperdiet tempor ipsum turpis vel elit. Fusce sed ex non nunc placerat cursus. Ut luctus porttitor mauris. Vivamus blandit sapien augue, vitae sodales libero lacinia eu. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras elementum id velit bibendum semper. Mauris vitae urna neque. Curabitur elementum laoreet.';
    }

    protected function _after() {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'informasipemakaianbarang_v');
    }

    public function testTipeDataPositif() {
        $this->model->attributes = $this->setPayloadTipeData(true);
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif() {
        $this->model->attributes = $this->setPayloadTipeData(false);
        $this->assertFalse($this->model->validate());
    }

    public function testMaxStringRuanganPositif() {
        $ruangan_nama = strlen('Farmasi RJ');
        $this->assertLessThanOrEqual(50, $ruangan_nama);
    }

    public function testMaxStringRuanganNegatif() {
        $ruangan_nama = strlen($this->str520);
        $this->assertGreaterThan(50, $ruangan_nama);
    }

    public function testMaxStringInstalasiPositif() {
        $instalasi_nama = strlen('Farmasi RJ');
        $this->assertLessThanOrEqual(50, $instalasi_nama);
    }

    public function testMaxStringInstalasiNegatif() {
        $instalasi_nama = strlen($this->str520);
        $this->assertGreaterThan(50, $instalasi_nama);
    }

    public function testMaxStringPegawaiPositif() {
        $nama_pegawai = strlen('Farmasi RJ');
        $this->assertLessThanOrEqual(50, $nama_pegawai);
    }

    public function testMaxStringPegawaiNegatif() {
        $nama_pegawai = strlen($this->str520);
        $this->assertGreaterThan(50, $nama_pegawai);
    }

    public function testMaxStringNoPemakaianPositif() {
        $no_pemakaian = strlen('PMB202207260001');
        $this->assertLessThanOrEqual(50, $no_pemakaian);
    }

    public function testMaxStringNoPemakaianNegatif() {
        $no_pemakaian = strlen($this->str520);
        $this->assertGreaterThan(50, $no_pemakaian);
    }

    public function testMaxStringKeperluanPositif() {
        $keperluan = strlen('Untuk ruangan A');
        $this->assertLessThanOrEqual(50, $keperluan);
    }

    public function testMaxStringKeperluanNegatif() {
        $keperluan = strlen($this->str520);
        $this->assertGreaterThan(50, $keperluan);
    }

    public function testMaxStringNamaBarangPositif() {
        $barang_nama = strlen('Kertas HVS');
        $this->assertLessThanOrEqual(50, $barang_nama);
    }

    public function testMaxStringNamaBarangNegatif() {
        $barang_nama = strlen($this->str520);
        $this->assertGreaterThan(50, $barang_nama);
    }

    private function setPayloadTipeData($isPositif = true) {
        return [
            'pemakaianbarangdetail_id' => $isPositif ? 1 : '10',
            'pemakaianbarang_id' => $isPositif ? 1 : '10',
            'instalasi_id' => $isPositif ? 6 : '10',
            'ruangan_id' => $isPositif ? 134 : '10',
            'pegawai_id' => $isPositif ? 10 : '10',
            'jumlah_pakai' => $isPositif ? 5 : '10',
            'barang_id' => $isPositif ? 22 : '10',
            'tgl_pemakaianbarang' => $isPositif ? '2022-07-26' : null,
            'untuk_keperluan' => $isPositif ? 'Pemakaian ruangan' : null,
            'keteranganpakai' => $isPositif ? 'Untuk ruangan A' : 123,
            'catatan_barang' => $isPositif ? 'Untuk ruangan A' : 123,
            'no_pemakaianbarang' => $isPositif ? 'PMB202207260001' : 20220726,
            'ruangan_nama' => $isPositif ? 'Farmasi RJ' : 134, 
            'instalasi_nama' => $isPositif ? 'Farmasi' : 6, 
            'nama_pegawai' => $isPositif ? 'Sandi' : 2,
            'barang_nama' => $isPositif ? 'Kertas' : 22
        ];
    }
}