<?php

use yii\db\Migration;

/**
 * Class m221204_075219_migrate_GB_75_persalinan_t_improve_column_using
 */
class m221204_075219_migrate_GB_75_persalinan_t_improve_column_using extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."partograf_v";
        ');
		
		
        $this->execute('
			ALTER TABLE "public"."persalinan_t" 
			  ALTER COLUMN "penolong" TYPE int4 USING "penolong"::int4;
        ');
		
        $this->execute('
            CREATE VIEW "public"."partograf_v" AS SELECT pendaftaran_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS kasus_penyakit,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas,
    pasienadmisi.no_kamar,
    pasienadmisi.no_bed,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin,
    persalinan_t.tgl_persalinan,
    persalinan_t.penolong,
    persalinan_t.tempat_persalinan,
    fgetnamalookupkeperawatan(persalinan_t.rujuk_kala::integer) AS rujuk_kala,
    persalinan_t.alasan_merujuk,
    persalinan_t.tempat_rujukan,
    fgetnamalookupkeperawatan(persalinan_t.pendamping::integer) AS pendamping,
    fgetnamalookupkeperawatan(persalinan_t.masalah_persalinan::integer) AS masalah_persalinan,
    persalinan_t.k1_gariswaspada,
    persalinan_t.k1_masalah,
    persalinan_t.k1_pelaksanaanmasalah,
    persalinan_t.k1_hasil,
    persalinan_t.k2_episitomi,
    persalinan_t.k2_indikasi,
    fgetnamalookupkeperawatan(persalinan_t.k2_pendamping::integer) AS k2_pendamping,
    persalinan_t.k2_gawatjanin,
    persalinan_t.k2_tindakanjanin,
    persalinan_t.k2_hasil,
    persalinan_t.k2_distosiabahu,
    persalinan_t.k2_tindakandistosia,
    persalinan_t.k2_masalah,
    persalinan_t.k3,
    persalinan_t.k4_keadaanumum,
    persalinan_t.k4_td_systolic,
    persalinan_t.k4_td_diastolic,
    persalinan_t.k4_detaknadi,
    persalinan_t.k4_pernapasan,
    persalinan_t.k4_masalah
   FROM pendaftaran_t
     JOIN persalinan_t ON pendaftaran_t.pendaftaran_id = persalinan_t.pendaftaran_id AND persalinan_t.is_deleted = false
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
            kamarruangan_m.kamarruangan_nokamar AS no_kamar,
            kamartempattidur_m.no_tempattidur AS no_bed
           FROM pasienadmisi_t
             LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id;
        ');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221204_075219_migrate_GB_75_persalinan_t_improve_column_using cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221204_075219_migrate_GB_75_persalinan_t_improve_column_using cannot be reverted.\n";

        return false;
    }
    */
}
