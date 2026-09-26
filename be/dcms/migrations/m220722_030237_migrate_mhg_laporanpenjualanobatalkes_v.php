<?php

use yii\db\Migration;

/**
 * Class m220722_030237_migrate_mhg_laporanpenjualanobatalkes_v
 */
class m220722_030237_migrate_mhg_laporanpenjualanobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanpenjualanobatalkes_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpenjualanobatalkes_v\" AS  SELECT a.jenis,
    a.noresep,
    a.nama_dokter,
    a.nama_pasien,
    a.no_pendaftaran,
    a.nama_obat,
    a.jumlah_obat,
    a.satuan,
    a.totalhargajual,
    a.totaltagihan,
    a.carabayar_nama,
    a.penjamin_nama,
    a.penjualanresep_id,
    a.tgltransaksi,
    a.nama_pembeli,
    a.totaltarifservice,
    a.biayaadministrasi,
    a.biayakonseling,
    a.pembulatanharga,
    a.jasadokterresep,
    a.jenispenjualan,
    a.jenis_resep,
    a.carabayar_id,
    a.supplier,
    a.kode_obat,
    NULL::text AS principle,
    a.\"user\",
    a.rke,
    a.no_rekammedik,
    a.is_formularium,
    a.status_reseptur,
    a.status_reseptur_nama,
    a.ruangan_id,
    a.ruangan_nama,
    a.is_psycothropica,
    a.is_narcotic
   FROM ( SELECT 'RESEP'::text AS jenis,
            penjualanresep_t.noresep,
            pegawai_m.nama_pegawai AS nama_dokter,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN pasien_m.nama_pasien
                    WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN karyawan.nama_pegawai
                    ELSE penjualanresep_t.nama_pembeli
                END AS nama_pasien,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN pendaftaran_t.no_pendaftaran
                    ELSE penjualanresep_t.noresep
                END AS no_pendaftaran,
            obatalkes_m.obatalkes_nama AS nama_obat,
            obatalkespasien_t.qty_oa AS jumlah_obat,
            COALESCE(obatalkespasien_t.additional_data::json ->> 'satuan_input'::text, satuanunit_m.satuanunit_nama::text) AS satuan,
            penjualanresep_t.totalhargajual,
            obatalkespasien_t.hargajual_oa AS totaltagihan,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            penjualanresep_t.penjualanresep_id,
            penjualanresep_t.tglpenjualan AS tgltransaksi,
            penjualanresep_t.nama_pembeli,
            penjualanresep_t.totaltarifservice,
            penjualanresep_t.biayaadministrasi,
            penjualanresep_t.biayakonseling,
            penjualanresep_t.pembulatanharga,
            penjualanresep_t.jasadokterresep,
            penjualanresep_t.jenispenjualan,
            jenispenjualan.lookup_name AS jenis_resep,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN pendaftaran_t.carabayar_id
                    ELSE penjualanresep_t.carabayar_id
                END AS carabayar_id,
            supplier_m.supplier_nama AS supplier,
            obatalkes_m.obatalkes_kode AS kode_obat,
            NULL::text AS principle,
            penjualanresep_t.additional_data AS \"user\",
                CASE
                    WHEN obatalkespasien_t.rke IS NULL THEN '-'::character varying
                    ELSE obatalkespasien_t.rke::character varying
                END AS rke,
            pasien_m.no_rekam_medik AS no_rekammedik,
            obatalkes_m.is_formularium,
            penjualanresep_t.status_reseptur,
            status_reseptur.lookup_name AS status_reseptur_nama,
            obatalkespasien_t.ruangan_id,
            ruangan_m.ruangan_nama,
            obatalkes_m.is_psycothropica,
            obatalkes_m.is_narcotic
           FROM penjualanresep_t
             LEFT JOIN ( SELECT a_1.pasien_id,
                    a_1.nama_pasien,
                    a_1.no_rekam_medik
                   FROM pasien_m a_1) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a_1.pendaftaran_id,
                    a_1.carabayar_id,
                    a_1.no_pendaftaran
                   FROM pendaftaran_t a_1) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai
                   FROM pegawai_m a_1) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a_1.carabayar_id,
                    a_1.carabayar_nama
                   FROM carabayar_m a_1) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a_1.penjamin_id,
                    a_1.penjamin_nama
                   FROM penjamin_m a_1) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai
                   FROM pegawai_m a_1) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
             LEFT JOIN ( SELECT a_1.penjualanresep_id,
                    a_1.obatalkes_id,
                    a_1.ruangan_id,
                    a_1.qty_oa,
                    a_1.satuankecil_id,
                    a_1.additional_data,
                    a_1.hargajual_oa,
                    a_1.rke
                   FROM obatalkespasien_t a_1
                  WHERE a_1.is_deleted = false) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
             LEFT JOIN ( SELECT a_1.obatalkes_id,
                    a_1.obatalkes_nama,
                    a_1.obatalkes_kode,
                    a_1.supplier_id,
                    a_1.is_formularium,
                    COALESCE(a_1.is_psycothropica, false) AS is_psycothropica,
                    COALESCE(a_1.is_narcotic, false) AS is_narcotic
                   FROM obatalkes_m a_1) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT a_1.satuanunit_id,
                    a_1.satuanunit_nama
                   FROM satuanunit_m a_1) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
             LEFT JOIN ( SELECT a_1.supplier_id,
                    a_1.supplier_nama
                   FROM supplier_m a_1) supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
             LEFT JOIN lookup_m jenispenjualan ON penjualanresep_t.jenispenjualan::integer = jenispenjualan.lookup_id
             LEFT JOIN lookup_m status_reseptur ON penjualanresep_t.status_reseptur::integer = status_reseptur.lookup_id
             LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
                    ruangan_m_1.ruangan_nama
                   FROM ruangan_m ruangan_m_1) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
          WHERE penjualanresep_t.is_active = true
        UNION ALL
         SELECT 'BMHP'::text AS jenis,
            NULL::text AS noresep,
                CASE
                    WHEN obatalkespasien_t.tindakanpelayanan_id IS NOT NULL AND tindakanpelayanan_t.dokterpenanggungjawab_id IS NOT NULL THEN dokterpenanggungjawab.nama_pegawai
                    WHEN obatalkespasien_t.tindakanpelayanan_id IS NOT NULL AND tindakanpelayanan_t.dokterpelaksana_id IS NULL THEN pegawai_pelaksana.nama_pegawai
                    WHEN obatalkespasien_t.tindakanpelayanan_id IS NULL THEN dokterintruksi.nama_pegawai
                    ELSE NULL::character varying
                END AS nama_dokter,
            pasien_m.nama_pasien,
            pendaftaran_t.no_pendaftaran,
            obatalkes_m.obatalkes_nama AS nama_obat,
            obatalkespasien_t.qty_oa AS jumlah_obat,
            COALESCE(obatalkespasien_t.additional_data::json ->> 'satuan_input'::text, satuanunit_m.satuanunit_nama::text) AS satuan,
            NULL::double precision AS totalhargajual,
            obatalkespasien_t.hargajual_oa AS totaltagihan,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::integer AS penjualanresep_id,
            obatalkespasien_t.tglpelayanan AS tgltransaksi,
            NULL::text AS nama_pembeli,
            NULL::integer AS totaltarifservice,
            NULL::integer AS biayaadministrasi,
            NULL::integer AS biayakonseling,
            NULL::integer AS pembulatanharga,
            NULL::integer AS jasadokterresep,
            'BMHP'::text AS jenispenjualan,
            'Penjualan BMHP'::text AS jenis_resep,
            carabayar_m.carabayar_id,
            supplier_m.supplier_nama AS supplier,
            obatalkes_m.obatalkes_kode AS kode_obat,
            NULL::text AS principle,
            NULL::text AS \"user\",
                CASE
                    WHEN obatalkespasien_t.rke IS NULL THEN '-'::character varying
                    ELSE obatalkespasien_t.rke::character varying
                END AS rke,
            pasien_m.no_rekam_medik AS no_rekammedik,
            obatalkes_m.is_formularium,
                CASE
                    WHEN obatalkespasien_t.is_deleted = false AND obatalkespasien_t.status_bmhp IS NOT NULL THEN obatalkespasien_t.status_bmhp::integer
                    WHEN obatalkespasien_t.is_deleted = true AND obatalkespasien_t.status_bmhp = 680 THEN 111
                    ELSE NULL::integer
                END AS status_reseptur,
                CASE
                    WHEN obatalkespasien_t.is_deleted = false AND obatalkespasien_t.status_bmhp IS NOT NULL THEN lookup_status.lookup_name
                    WHEN obatalkespasien_t.is_deleted = true AND obatalkespasien_t.status_bmhp = 680 THEN 'Batal'::text::character varying
                    ELSE NULL::character varying
                END AS status_reseptur_nama,
            obatalkespasien_t.ruangan_id,
            ruangan_m.ruangan_nama,
            obatalkes_m.is_psycothropica,
            obatalkes_m.is_narcotic
           FROM obatalkespasien_t
             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasien_id,
                    pendaftaran_t_1.no_pendaftaran
                   FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pasien_m_1.pasien_id,
                    pasien_m_1.no_rekam_medik,
                    pasien_m_1.nama_pasien
                   FROM pasien_m pasien_m_1) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a_1.obatalkes_id,
                    a_1.obatalkes_nama,
                    a_1.obatalkes_kode,
                    a_1.supplier_id,
                    a_1.is_formularium,
                    COALESCE(a_1.is_psycothropica, false) AS is_psycothropica,
                    COALESCE(a_1.is_narcotic, false) AS is_narcotic
                   FROM obatalkes_m a_1) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT a_1.satuanunit_id,
                    a_1.satuanunit_nama
                   FROM satuanunit_m a_1) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
             LEFT JOIN ( SELECT a_1.carabayar_id,
                    a_1.carabayar_nama
                   FROM carabayar_m a_1) carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a_1.penjamin_id,
                    a_1.penjamin_nama
                   FROM penjamin_m a_1) penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a_1.supplier_id,
                    a_1.supplier_nama
                   FROM supplier_m a_1) supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
                    ruangan_m_1.ruangan_nama
                   FROM ruangan_m ruangan_m_1) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT tindakanpelayanan_t_1.tindakanpelayanan_id,
                    tindakanpelayanan_t_1.dokterpenanggungjawab_id,
                    tindakanpelayanan_t_1.dokterpelaksana_id
                   FROM tindakanpelayanan_t tindakanpelayanan_t_1) tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
             LEFT JOIN ( SELECT instruksitindakanbmhp_t_1.instruksitindakanbmhp_id,
                    instruksitindakanbmhp_t_1.dokter_id
                   FROM instruksitindakanbmhp_t instruksitindakanbmhp_t_1) instruksitindakanbmhp_t ON obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai
                   FROM pegawai_m a_1) dokterpenanggungjawab ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokterpenanggungjawab.pegawai_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai
                   FROM pegawai_m a_1) pegawai_pelaksana ON tindakanpelayanan_t.dokterpelaksana_id = dokterpenanggungjawab.pegawai_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai
                   FROM pegawai_m a_1) dokterintruksi ON instruksitindakanbmhp_t.dokter_id = dokterintruksi.pegawai_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) lookup_status ON obatalkespasien_t.status_bmhp = lookup_status.lookup_id
          WHERE obatalkespasien_t.penjualanresep_id IS NULL) a;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220722_030237_migrate_mhg_laporanpenjualanobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220722_030237_migrate_mhg_laporanpenjualanobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
