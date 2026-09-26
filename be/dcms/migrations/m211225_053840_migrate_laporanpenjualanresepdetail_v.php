<?php

use yii\db\Migration;

/**
 * Class m211225_053840_migrate_laporanpenjualanresepdetail_v
 */
class m211225_053840_migrate_laporanpenjualanresepdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpenjualanresepdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpenjualanresepdetail_v\" AS  SELECT penjualanresep_t.noresep,
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
    penjualanresep_t.tglpenjualan,
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
    status_reseptur.lookup_name AS status_reseptur_nama
   FROM penjualanresep_t
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.carabayar_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN ( SELECT a.penjualanresep_id,
            a.obatalkes_id,
            a.qty_oa,
            a.satuankecil_id,
            a.additional_data,
            a.hargajual_oa,
            a.rke
           FROM obatalkespasien_t a
          WHERE a.is_deleted = false) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.obatalkes_kode,
            a.supplier_id,
            a.is_formularium
           FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
     LEFT JOIN lookup_m jenispenjualan ON penjualanresep_t.jenispenjualan::integer = jenispenjualan.lookup_id
     LEFT JOIN lookup_m status_reseptur ON penjualanresep_t.status_reseptur::integer = status_reseptur.lookup_id
  WHERE penjualanresep_t.is_active = true;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211225_053840_migrate_laporanpenjualanresepdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211225_053840_migrate_laporanpenjualanresepdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
