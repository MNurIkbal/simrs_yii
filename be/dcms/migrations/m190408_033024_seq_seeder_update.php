<?php

use yii\db\Migration;

/**
 * Class m190408_033024_seq_seeder_update
 */
class m190408_033024_seq_seeder_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("SELECT setval('public.instalasi_m_instalasi_id_seq', (SELECT COALESCE(MAX(instalasi_id) ,1)+1 FROM instalasi_m), false);");
        $this->execute("SELECT setval('public.ruangan_m_ruangan_id_seq', (SELECT COALESCE(MAX(ruangan_id) ,1)+1 FROM ruangan_m), false);");
        $this->execute("SELECT setval('public.modul_k_modul_id_seq', (SELECT COALESCE(MAX(modul_id) ,1)+1 FROM modul_k), false);");
        $this->execute("SELECT setval('public.kelompokmenu_m_seq', (SELECT COALESCE(MAX(kelmenu_id) ,1)+1 FROM kelompokmenu_k), false);");
        $this->execute("SELECT setval('public.menumodul_k_menu_id_seq', (SELECT COALESCE(MAX(menu_id) ,1)+1 FROM menumodul_k), false);");
        $this->execute("SELECT setval('public.pegawai_m_pegawai_id_seq', (SELECT COALESCE(MAX(pegawai_id) ,1)+1 FROM pegawai_m), false);");
        $this->execute("SELECT setval('public.loginpemakai_k_loginpemakai_id_seq', (SELECT COALESCE(MAX(loginpemakai_id) ,1)+1 FROM loginpemakai_k), false);");
        $this->execute("SELECT setval('public.peranpengguna_t_peranpengguna_id_seq', (SELECT COALESCE(MAX(peranpengguna_id) ,1)+1 FROM peranpengguna_k), false);");
        $this->execute("SELECT setval('public.aksespengguna_k_aksespengguna_id_seq', (SELECT COALESCE(MAX(aksespengguna_id) ,1)+1 FROM aksespengguna_k), false);");
        $this->execute("SELECT setval('public.carabayar_m_carabayar_id_seq', (SELECT COALESCE(MAX(carabayar_id) ,1)+1 FROM carabayar_m), false);");
        $this->execute("SELECT setval('public.penjamin_m_penjamin_id_seq', (SELECT COALESCE(MAX(penjamin_id) ,1)+1 FROM penjamin_m), false);");
        $this->execute("SELECT setval('public.jeniskasuspenyakit_m_jeniskasuspenyakit_id_seq', (SELECT COALESCE(MAX(jeniskasuspenyakit_id) ,1)+1 FROM jeniskasuspenyakit_m), false);");
        $this->execute("SELECT setval('public.kelaspelayanan_m_kelaspelayanan_id_seq', (SELECT COALESCE(MAX(kelaspelayanan_id) ,1)+1 FROM kelaspelayanan_m), false);");
        $this->execute("SELECT setval('public.golonganumur_m_golonganumur_id_seq', (SELECT COALESCE(MAX(golonganumur_id) ,1)+1 FROM golonganumur_m), false);");
        $this->execute("SELECT setval('public.jenisobatalkes_m_jenisobatalkes_id_seq', (SELECT COALESCE(MAX(jenisobatalkes_id) ,1)+1 FROM jenisobatalkes_m), false);");
        $this->execute("SELECT setval('public.groupinacbg_m_groupinacbg_id_seq', (SELECT COALESCE(MAX(groupinacbg_id) ,1)+1 FROM groupinacbg_m), false);");
        $this->execute("SELECT setval('public.pajak_m_pajak_id_seq', (SELECT COALESCE(MAX(pajak_id) ,1)+1 FROM pajak_m), false);");
        $this->execute("SELECT setval('public.payterm_m_payterm_id_seq', (SELECT COALESCE(MAX(payterm_id) ,1)+1 FROM payterm_m), false);");
        $this->execute("SELECT setval('public.konfigfarmasi_k_konfigfarmasi_id_seq', (SELECT COALESCE(MAX(konfigfarmasi_id) ,1)+1 FROM konfigfarmasi_k), false);");
        $this->execute("SELECT setval('public.komponentarif_m_komponentarif_id_seq', (SELECT COALESCE(MAX(komponentarif_id) ,1)+1 FROM komponentarif_m), false);");
        $this->execute("SELECT setval('public.kategoritindakan_m_kategoritindakan_id_seq', (SELECT COALESCE(MAX(kategoritindakan_id) ,1)+1 FROM kategoritindakan_m), false);");
        $this->execute("SELECT setval('public.kelompoktindakan_m_kelompoktindakan_id_seq', (SELECT COALESCE(MAX(kelompoktindakan_id) ,1)+1 FROM kelompoktindakan_m), false);");
        $this->execute("SELECT setval('public.konfigsystem_k_konfigsystem_id_seq', (SELECT COALESCE(MAX(konfigsystem_id) ,1)+1 FROM konfigsystem_k), false);");
        $this->execute("SELECT setval('public.pemeriksaanlab_m_pemeriksaanlab_id_seq', (SELECT COALESCE(MAX(pemeriksaanlab_id) ,1)+1 FROM pemeriksaanlab_m), false);");
        $this->execute("SELECT setval('public.golonganumurlab_m_golonganumurlab_id_seq', (SELECT COALESCE(MAX(golonganumurlab_id) ,1)+1 FROM golonganumurlab_m), false);");
        $this->execute("SELECT setval('public.carakeluar_m_carakeluar_id_seq', (SELECT COALESCE(MAX(carakeluar_id) ,1)+1 FROM carakeluar_m), false);");
        $this->execute("SELECT setval('public.bodymassindex_m_bodymassindex_id_seq', (SELECT COALESCE(MAX(bodymassindex_id) ,1)+1 FROM bodymassindex_m), false);");
        $this->execute("SELECT setval('public.kelompokpegawai_m_kelompokpegawai_id_seq', (SELECT COALESCE(MAX(kelompokpegawai_id) ,1)+1 FROM kelompokpegawai_m), false);");
        $this->execute("SELECT setval('public.docfooter_k_docfooter_id_seq', (SELECT COALESCE(MAX(docfooter_id) ,1)+1 FROM docfooter_k), false);");
        $this->execute("SELECT setval('public.docmapping_seq', (SELECT COALESCE(MAX(docmapping_id) ,1)+1 FROM docmapping_k), false);");
        $this->execute("SELECT setval('public.gcs_m_gcs_id_seq', (SELECT COALESCE(MAX(gcs_id) ,1)+1 FROM gcs_m), false);");
        $this->execute("SELECT setval('public.warnadokrm_m_warnadokrm_id_seq', (SELECT COALESCE(MAX(warnadokrm_id) ,1)+1 FROM warnadokrekammedik_m), false);");
        $this->execute("SELECT setval('public.kelompokdiagnosa_m_kelompokdiagnosa_id_seq', (SELECT COALESCE(MAX(kelompokdiagnosa_id) ,1)+1 FROM kelompokdiagnosa_m), false);");
        $this->execute("SELECT setval('public.kondisikeluar_m_kondisikeluar_id_seq', (SELECT COALESCE(MAX(kondisikeluar_id) ,1)+1 FROM kondisikeluar_m), false);");
        $this->execute("SELECT setval('public.kelompokmenugroup_k_kelompokmenugroup_id_seq', (SELECT COALESCE(MAX(kelompokmenu_id) ,1)+1 FROM kelompokmenugroup_k), false);");
        $this->execute("SELECT setval('public.metodegcs_m_metodegcs_id_seq', (SELECT COALESCE(MAX(metodegcs_id) ,1)+1 FROM metodegcs_m), false);");
        $this->execute("SELECT setval('public.konfiggudang_k_konfiggudang_id_seq', (SELECT COALESCE(MAX(konfiggudang_id) ,1)+1 FROM konfiggudang_k), false);");
        $this->execute("SELECT setval('public.konfigkonten_k_konfigkonten_id_seq', (SELECT COALESCE(MAX(konfigkonten_id) ,1)+1 FROM konfigkonten_k), false);");
        $this->execute("SELECT setval('public.loginmobile_t_loginmobile_id_seq', (SELECT COALESCE(MAX(loginmobile_id) ,1)+1 FROM loginmobile_k), false);");
        $this->execute("SELECT setval('public.jabatan_m_jabatan_id_seq', (SELECT COALESCE(MAX(jabatan_id) ,1)+1 FROM jabatan_m), false);");
        $this->execute("SELECT setval('public.asalrujukan_m_asalrujukan_id_seq', (SELECT COALESCE(MAX(asalrujukan_id) ,1)+1 FROM asalrujukan_m), false);");
        $this->execute("SELECT setval('public.bagiantubuh_m_bagiantubuh_id_seq', (SELECT COALESCE(MAX(bagiantubuh_id) ,1)+1 FROM bagiantubuh_m), false);");
        $this->execute("SELECT setval('public.bagiantubuhdetail_m_bagiantubuhdetail_id_seq', (SELECT COALESCE(MAX(bagiantubuhdetail_id) ,1)+1 FROM bagiantubuhdetail_m), false);");
        $this->execute("SELECT setval('public.bank_m_bank_id_seq', (SELECT COALESCE(MAX(bank_id) ,1)+1 FROM bank_m), false);");
        $this->execute("SELECT setval('public.caramasuk_m_caramasuk_id_seq', (SELECT COALESCE(MAX(caramasuk_id) ,1)+1 FROM caramasuk_m), false);");
        $this->execute("SELECT setval('public.gelarbelakang_m_gelarbelakang_id_seq', (SELECT COALESCE(MAX(gelarbelakang_id) ,1)+1 FROM gelarbelakang_m), false);");
        $this->execute("SELECT setval('public.golonganpegawai_m_golonganpegawai_id_seq', (SELECT COALESCE(MAX(golonganpegawai_id) ,1)+1 FROM golonganpegawai_m), false);");
        $this->execute("SELECT setval('public.jenisjabatan_m_jenisjabatan_id_seq', (SELECT COALESCE(MAX(jenisjabatan_id) ,1)+1 FROM jenisjabatan_m), false);");
        $this->execute("SELECT setval('public.jeniskelas_m_jeniskelas_id_seq', (SELECT COALESCE(MAX(jeniskelas_id) ,1)+1 FROM jeniskelas_m), false);");
        $this->execute("SELECT setval('public.jenispasien_m_jenispasien_id_seq', (SELECT COALESCE(MAX(jenispasien_id) ,1)+1 FROM jenispasien_m), false);");
        $this->execute("SELECT setval('public.jenistarif_m_jenistarif_id_seq', (SELECT COALESCE(MAX(jenistarif_id) ,1)+1 FROM jenistarif_m), false);");
        $this->execute("SELECT setval('public.subkelompokbarang_m_subkelompokbarang_id_seq', (SELECT COALESCE(MAX(kelompokbarang_id) ,1)+1 FROM kelompokbarang_m), false);");
        $this->execute("SELECT setval('public.perujuk_m_perujuk_id_seq', (SELECT COALESCE(MAX(perujuk_id) ,1)+1 FROM perujuk_m), false);");
        $this->execute("SELECT setval('public.racikan_m_racikan_id_seq', (SELECT COALESCE(MAX(racikan_id) ,1)+1 FROM racikan_m), false);");
        $this->execute("SELECT setval('public.rujukandari_m_rujukandari_id_seq', (SELECT COALESCE(MAX(rujukandari_id) ,1)+1 FROM rujukandari_m), false);");
        $this->execute("SELECT setval('public.rujukankeluar_m_rujukankeluar_id_seq', (SELECT COALESCE(MAX(rujukankeluar_id) ,1)+1 FROM rujukankeluar_m), false);");
        $this->execute("SELECT setval('public.signaobat_m_signaobat_id_seq', (SELECT COALESCE(MAX(signa_id) ,1)+1 FROM signaobat_m), false);");
        $this->execute("SELECT setval('public.subkelompokbarang_m_subkelompokbarang_id_seq', (SELECT COALESCE(MAX(subkelompokbarang_id) ,1)+1 FROM subkelompokbarang_m), false);");
        $this->execute("SELECT setval('public.unitkerja_m_unitkerja_id_seq', (SELECT COALESCE(MAX(unitkerja_id) ,1)+1 FROM unitkerja_m), false);");
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190408_033024_seq_seeder_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_033024_seq_seeder_update cannot be reverted.\n";

        return false;
    }
    */
}
