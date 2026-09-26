<?php

use yii\db\Migration;

/**
 * Class m200925_100243_migrate_20200925_laporanpenjualanresep
 */
class m200925_100243_migrate_20200925_laporanpenjualanresep extends Migration
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
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pasien_m.nama_pasien
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.nama_pegawai
            ELSE penjualanresep_t.nama_pembeli
        END AS nama_pasien,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pendaftaran_t.no_pendaftaran
            ELSE penjualanresep_t.noresep
        END AS no_pendaftaran,
    obatalkes_m.obatalkes_nama AS nama_obat,
    obatalkespasien_t.qty_oa AS jumlah_obat,
    COALESCE(((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text), (satuanunit_m.satuanunit_nama)::text) AS satuan,
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
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenis_resep,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pendaftaran_t.carabayar_id
            ELSE penjualanresep_t.carabayar_id
        END AS carabayar_id,
    supplier_m.supplier_nama AS supplier,
    obatalkes_m.obatalkes_kode AS kode_obat,
    NULL::text AS principle,
    penjualanresep_t.additional_data AS \"user\",
        CASE
            WHEN (obatalkespasien_t.rke IS NULL) THEN '-'::character varying
            ELSE (obatalkespasien_t.rke)::character varying
        END AS rke,
    pasien_m.no_rekam_medik AS no_rekammedik,
    obatalkes_m.is_formularium,
    penjualanresep_t.status_reseptur,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur_nama
   FROM ((((((((((penjualanresep_t
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
     LEFT JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN supplier_m ON ((obatalkes_m.supplier_id = supplier_m.supplier_id)))
  WHERE (penjualanresep_t.is_active = true);");
        
        $this->execute('ALTER TABLE "public"."laporanpenjualanresepdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_100243_migrate_20200925_laporanpenjualanresep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_100243_migrate_20200925_laporanpenjualanresep cannot be reverted.\n";

        return false;
    }
    */
}
