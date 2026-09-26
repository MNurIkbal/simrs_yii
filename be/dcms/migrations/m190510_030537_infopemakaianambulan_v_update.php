<?php

use yii\db\Migration;

/**
 * Class m190510_030537_infopemakaianambulan_v_update
 */
class m190510_030537_infopemakaianambulan_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW infopemakaianambulan_v;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW infopemakaianambulan_v AS 
 SELECT pemakaianambulan_t.pemakaianambulan_id,
    ambulan_m.ambulan_id,
    ambulan_m.no_polisi,
    pesanambulan_t.tgl_pesanambulan,
    pemakaianambulan_t.tgl_pemakaiandari,
    pemakaianambulan_t.tgl_pemakaiansampai,
    pesanambulan_t.no_pesanambulan,
    pemakaianambulan_t.durasi_pemakaian,
    pemakaianambulan_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN pesanambulan_t.pemesan
            ELSE pasien_m.nama_pasien
        END AS nama_pemesan,
        CASE
            WHEN pesanambulan_t.pasien_id IS NULL THEN jk_pemesan.lookup_name
            ELSE jk_pasien.lookup_name
        END AS jns_kelamin,
        CASE
            WHEN ambulan_m.is_emergency IS TRUE THEN \'EMERGENCY\'::text
            ELSE \'NON EMERGENCY\'::text
        END AS jenis_ambulan,
    pesanambulan_t.status_ambulan,
    pesanambulan_t.asal_pasien,
    pesanambulan_t.keluhan,
    pelayanan_ambulan.lookup_name AS pelayanan,
    COALESCE(pemakaianambulan_t.km_awal, 0) AS km_awal,
    COALESCE(pemakaianambulan_t.km_akhir, 0) AS km_akhir,
    COALESCE(pemakaianambulan_t.km_akhir, 0) - COALESCE(pemakaianambulan_t.km_awal, 0) AS jarak_pemakian,
    COALESCE(pemakaianambulan_t.total_biaya, 0::double precision) AS nominal_tagihan,
    fgetnamalookup(pesanambulan_t.status_ambulan) AS status_ambulan_nama,
    pegawai.nama_pegawai::character varying AS supir,
    pesanambulan_t.umur,
    pesanambulan_t.is_sadar,
    pesanambulan_t.is_nafas,
    pesanambulan_t.is_nadi,
    pesanambulan_t.nama_pj,
    pesanambulan_t.kontak_pj,
    pemakaianambulan_t.created_date,
    pemakaianambulan_t.biaya_pemakaian,
    pemakaianambulan_t.tgl_realisasikembali,
    pemakaianambulan_t.lama_pemakaian,
    pesanambulan_t.status_pesan
   FROM pemakaianambulan_t
     JOIN pesanambulan_t ON pemakaianambulan_t.pemakaianambulan_id = pesanambulan_t.pemakaianambulan_id
     LEFT JOIN pasien_m ON pesanambulan_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN lookup_m jk_pemesan ON pesanambulan_t.jenis_kelamin = jk_pemesan.lookup_id
     LEFT JOIN lookup_m jk_pasien ON pasien_m.jeniskelamin::integer = jk_pasien.lookup_id
     JOIN ambulan_m ON pesanambulan_t.ambulan_id = ambulan_m.ambulan_id
     JOIN lookup_m stat_ambulan ON ambulan_m.status_ambulan = stat_ambulan.lookup_id
     JOIN lookup_m pelayanan_ambulan ON pemakaianambulan_t.pelayanan_ambulan = pelayanan_ambulan.lookup_id
     LEFT JOIN ( SELECT pemakaianambulandetail_t.pemakaianambulan_id,
            string_agg(pegawai_m.nama_pegawai::text, \' ,\'::text) AS nama_pegawai
           FROM pemakaianambulandetail_t
             JOIN pegawai_m ON pemakaianambulandetail_t.petugas_id = pegawai_m.pegawai_id AND pegawai_m.jabatan_id = 38
          GROUP BY pemakaianambulandetail_t.pemakaianambulan_id) pegawai ON pemakaianambulan_t.pemakaianambulan_id = pegawai.pemakaianambulan_id;

        ');

        $this->execute('
ALTER TABLE infopemakaianambulan_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190510_030537_infopemakaianambulan_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_030537_infopemakaianambulan_v_update cannot be reverted.\n";

        return false;
    }
    */
}
