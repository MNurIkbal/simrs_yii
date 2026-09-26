<?php

use app\modules\v1\models\LaporanPtmV;

class LaporanPtmVTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $model;

    protected function _before()
    {
        $this->model = new LaporanPtmV;
    }

    protected function _after()
    {
    }

    public function testLaporanPtmVFind()
    {
        $start_t   = date('Y-m-d 00:00:00');
        $end_t     = date('Y-m-d 23:59:59');
        $query_t = $this->model::find();
        $diagnosa_id_t     = 12;
        $instalasi_id_t     = 12;
        $query_t->andWhere(['diagnosa_id' => $diagnosa_id_t]);
        $query_t->andWhere(['instalasi_id'=> $instalasi_id_t]);
        $query_t->andWhere(['between', 'tgl_registrasi', $start_t, $end_t]);
        $query_t->andWhere(['not', ['diag_utama_kode' => null, 'diag_utama' => null]]);
        $query_t->orderBy(['tgl_registrasi' => 'desc']);
        $this->assertTrue(is_array($query_t->all()));

        // id tidak berpengaruh
        $start_t   = date('Y-m-d 00:00:00');
        $end_t     = date('Y-m-d 23:59:59');
        $diagnosa_id_f     = 'r12';
        $instalasi_id_f     = 'r12';
        $query_f = $this->model::find();
        $query_t->andWhere(['diagnosa_id' => $diagnosa_id_f]);
        $query_t->andWhere(['instalasi_id'=> $instalasi_id_f]);
        $query_f->andWhere(['between', 'tgl_registrasi', $start_t, $end_t]);
        $query_f->andWhere(['not', ['diag_utama_kode' => null, 'diag_utama' => null]]);
        $query_f->orderBy(['tgl_registrasi' => 'desc']);
        $this->assertTrue(is_array($query_f->all()));

        // datestyle berpengaruh pada pencarian Laporan PTM V
        $start_f   = date('d/m/Y 00:00:00');
        $end_f     = date('d/m/Y 23:59:59');
        $diagnosa_id_t     = 12;
        $instalasi_id_t     = 12;
        $query_f = $this->model::find();
        $query_t->andWhere(['diagnosa_id' => $diagnosa_id_t]);
        $query_t->andWhere(['instalasi_id'=> $instalasi_id_t]);
        $query_f->andWhere(['between', 'tgl_registrasi', $start_f, $end_f]);
        $query_f->andWhere(['not', ['diag_utama_kode' => null, 'diag_utama' => null]]);
        $query_f->orderBy(['tgl_registrasi' => 'desc']);
        $this->assertFalse(is_array($query_f->all()));
    }
}