<?php

use yii\db\Migration;

/**
 * Class m190408_033307_seq_demo_update
 */
class m190408_033307_seq_demo_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute("SELECT setval('public.suku_m_suku_id_seq', (SELECT COALESCE(MAX(suku_id) ,1)+1 FROM suku_m), false);");
        $this->execute("SELECT setval('public.satuanunit_m_satuanunit_id_seq', (SELECT COALESCE(MAX(satuanunit_id) ,1)+1 FROM satuanunit_m), false);");
        $this->execute("SELECT setval('public.satuankonversi_m_satuankonversi_id_seq', (SELECT COALESCE(MAX(satuankonversi_id) ,1)+1 FROM satuankonversi_m), false);");
        $this->execute("SELECT setval('public.konfigmargin_k_konfigmargin_id_seq', (SELECT COALESCE(MAX(konfigmargin_id) ,1)+1 FROM konfigmargin_k), false);");
        $this->execute("SELECT setval('public.konfigmargindetail_k_konfigmargindetail_id_seq', (SELECT COALESCE(MAX(konfigmargindetail_id) ,1)+1 FROM konfigmargindetail_k), false);");
        $this->execute("SELECT setval('public.daftartindakan_m_daftartindakan_id_seq', (SELECT COALESCE(MAX(daftartindakan_id) ,1)+1 FROM daftartindakan_m), false);");
        $this->execute("SELECT setval('public.jeniskegiatantindakan_m_jeniskegiatantindakan_id_seq', (SELECT COALESCE(MAX(jeniskegiatantindakan_id) ,1)+1 FROM jeniskegiatantindakan_m), false);");
        $this->execute("SELECT setval('public.perdatarif_m_perdatarif_id_seq', (SELECT COALESCE(MAX(perdatarif_id) ,1)+1 FROM perdatarif_m), false);");
        $this->execute("SELECT setval('public.jenispemeriksaanlab_m_jenispemeriksaanlab_id_seq', (SELECT COALESCE(MAX(jenispemeriksaanlab_id) ,1)+1 FROM jenispemeriksaanlab_m), false);");
        $this->execute("SELECT setval('public.kelompokpemeriksaanlab_m_kelompokpemeriksaanlab_id_seq', (SELECT COALESCE(MAX(kelompokpemeriksaanlab_id) ,1)+1 FROM kelompokpemeriksaanlab_m), false);");
        $this->execute("SELECT setval('public.satuan_lab_satuanlab_id', (SELECT COALESCE(MAX(satuanlab_id) ,1)+1 FROM satuanlab_m), false);");
        $this->execute("SELECT setval('public.nilairujukan_m_nilairujukan_id_seq', (SELECT COALESCE(MAX(nilairujukan_id) ,1)+1 FROM nilairujukan_m), false);");
        $this->execute("SELECT setval('public.shift_m_shift_id_seq', (SELECT COALESCE(MAX(shift_id) ,1)+1 FROM shift_m), false);");
        $this->execute("SELECT setval('public.loket_m_loket_id_seq', (SELECT COALESCE(MAX(loket_id) ,1)+1 FROM loket_m), false);");
        $this->execute("SELECT setval('public.kamarruangan_m_kamarruangan_id_seq', (SELECT COALESCE(MAX(kamarruangan_id) ,1)+1 FROM kamarruangan_m), false);");
        $this->execute("SELECT setval('public.kamartempattidur_m_kamartempattidur_id_seq', (SELECT COALESCE(MAX(kamartempattidur_id) ,1)+1 FROM kamartempattidur_m), false);");
        $this->execute("SELECT setval('public.pekerjaan_m_pekerjaan_id_seq', (SELECT COALESCE(MAX(pekerjaan_id) ,1)+1 FROM pekerjaan_m), false);");
        $this->execute("SELECT setval('public.kettempattidur_t_kettempattidur_id_seq', (SELECT COALESCE(MAX(kettempattidur_id) ,1)+1 FROM kettempattidur_m), false);");
        $this->execute("SELECT setval('public.pendidikan_m_pendidikan_id_seq', (SELECT COALESCE(MAX(pendidikan_id) ,1)+1 FROM pendidikan_m), false);");
        $this->execute("SELECT setval('public.pendidikankualifikasi_m_pendkualifikasi_id_seq', (SELECT COALESCE(MAX(pendkualifikasi_id) ,1)+1 FROM pendidikankualifikasi_m), false);");
        $this->execute("SELECT setval('public.expertise_m_expertise_id_seq', (SELECT COALESCE(MAX(expertise_id) ,1)+1 FROM expertise_m), false);");
        $this->execute("SELECT setval('public.jenispemeriksaanrad_m_jenispemeriksaanrad_id_seq', (SELECT COALESCE(MAX(pemeriksaanradiologi_id) ,1)+1 FROM pemeriksaanrad_m), false);");
        $this->execute("SELECT setval('public.golonganoperasi_m_golonganoperasi_id_seq', (SELECT COALESCE(MAX(golonganoperasi_id) ,1)+1 FROM golonganoperasi_m), false);");
        $this->execute("SELECT setval('public.indexing_m_indexing_id_seq', (SELECT COALESCE(MAX(indexing_id) ,1)+1 FROM indexing_m), false);");
        $this->execute("SELECT setval('public.jenisanastesi_m_jenisanastesi_id_seq', (SELECT COALESCE(MAX(jenisanastesi_id) ,1)+1 FROM jenisanastesi_m), false);");
        $this->execute("SELECT setval('public.jenisdarah_m_jenisdarah_id_seq', (SELECT COALESCE(MAX(jenisdarah_id) ,1)+1 FROM jenisdarah_m), false);");
        $this->execute("SELECT setval('public.jenisdiet_m_jenisdiet_id_seq', (SELECT COALESCE(MAX(jenisdiet_id) ,1)+1 FROM jenisdiet_m), false);");
        $this->execute("SELECT setval('public.jenispemeriksaanrad_m_jenispemeriksaanrad_id_seq', (SELECT COALESCE(MAX(jenispemeriksaanrad_id) ,1)+1 FROM jenispemeriksaanrad_m), false);");
        $this->execute("SELECT setval('public.kegiatanoperasi_m_kegiatanoperasi_id_seq', (SELECT COALESCE(MAX(kegiatanoperasi_id) ,1)+1 FROM kegiatanoperasi_m), false);");
        $this->execute("SELECT setval('public.kelompokpemeriksaanrad_m_kelompokpemeriksaanrad_id_seq', (SELECT COALESCE(MAX(kelompokpemeriksaanrad_id) ,1)+1 FROM kelompokpemeriksaanrad_m), false);");
        $this->execute("SELECT setval('public.klasifikasipasien_m_klasifikasipasien_id_seq', (SELECT COALESCE(MAX(klasifikasipasien_id) ,1)+1 FROM klasifikasipasien_m), false);");
        $this->execute("SELECT setval('public.klasifikasitekanadarah_m_klasifikasitekanadarah_id_seq', (SELECT COALESCE(MAX(klasifikasitekanadarah_id) ,1)+1 FROM klasifikasitekanandarah_m), false);");
        $this->execute("SELECT setval('public.seq_konfigantrian_m', (SELECT COALESCE(MAX(konfigantrian_id) ,1)+1 FROM konfigantrian_m), false);");
        $this->execute("SELECT setval('public.lokasirak_m_lokasirak_id_seq', (SELECT COALESCE(MAX(lokasirak_id) ,1)+1 FROM lokasirak_m), false);");
        $this->execute("SELECT setval('public.makanandiet_m_makanandiet_id_seq', (SELECT COALESCE(MAX(makanandiet_id) ,1)+1 FROM makanandiet_m), false);");
        $this->execute("SELECT setval('public.operasi_m_operasi_id_seq', (SELECT COALESCE(MAX(operasi_id) ,1)+1 FROM operasi_m), false);");
        $this->execute("SELECT setval('public.pangkat_m_pangkat_id_seq', (SELECT COALESCE(MAX(pangkat_id) ,1)+1 FROM pangkat_m), false);");
        $this->execute("SELECT setval('public.racikandetail_m_racikandetail_id_seq', (SELECT COALESCE(MAX(racikandetail_id) ,1)+1 FROM racikandetail_m), false);");
        $this->execute("SELECT setval('public.samplelab_m_samplelab_id_seq', (SELECT COALESCE(MAX(samplelab_id) ,1)+1 FROM samplelab_m), false);");
        $this->execute("SELECT setval('public.satuankonversibrg_m_satuankonversibrg_id_seq', (SELECT COALESCE(MAX(satuankonversibrg_id) ,1)+1 FROM satuankonversibrg_m), false);");
        $this->execute("SELECT setval('public.subrak_m_subrak_id_seq', (SELECT COALESCE(MAX(subrak_id) ,1)+1 FROM subrak_m), false);");
        $this->execute("SELECT setval('public.supplier_m_supplier_id_seq', (SELECT COALESCE(MAX(supplier_id) ,1)+1 FROM supplier_m), false);");
        $this->execute("SELECT setval('public.tariftindakan_m_tariftindakan_id_seq', (SELECT COALESCE(MAX(tariftindakan_id) ,1)+1 FROM tariftindakan_m), false);");
        $this->execute("SELECT setval('public.tipepaket_m_tipepaket_id_seq', (SELECT COALESCE(MAX(tipepaket_id) ,1)+1 FROM tipepaket_m), false);");
        $this->execute("SELECT setval('public.ambulan_m_ambulan_id_seq', (SELECT COALESCE(MAX(ambulan_id) ,1)+1 FROM ambulan_m), false);");
        $this->execute("SELECT setval('public.ambulandetail_m_ambulandetail_id_seq', (SELECT COALESCE(MAX(ambulandetail_id) ,1)+1 FROM ambulandetail_m), false);");
        $this->execute("SELECT setval('public.pbf_m_pbf_id_seq', (SELECT COALESCE(MAX(pbf_id) ,1)+1 FROM pbf_m), false);");
        $this->execute("SELECT setval('public.pasien_m_pasien_id_seq', (SELECT COALESCE(MAX(pasien_id) ,1)+1 FROM pasien_m), false);");
        $this->execute("SELECT setval('public.jadwaldokter_m_jadwaldokter_id_seq', (SELECT COALESCE(MAX(jadwaldokter_id) ,1)+1 FROM jadwaldokter_m), false);");
        $this->execute("SELECT setval('public.jadwalbukapoli_m_jadwalbukapoli_id_seq', (SELECT COALESCE(MAX(jadwalbukapoli_id) ,1)+1 FROM jadwalbukapoli_m), false);");
        $this->execute("SELECT setval('public.pegawai_m_pegawai_id_seq', (SELECT COALESCE(MAX(pegawai_id) ,1)+1 FROM pegawai_m), false);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190408_033307_seq_demo_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_033307_seq_demo_update cannot be reverted.\n";

        return false;
    }
    */
}
