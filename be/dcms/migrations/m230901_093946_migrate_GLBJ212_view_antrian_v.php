<?php

use yii\db\Migration;

/**
 * Class m230901_093946_migrate_GLBJ212_view_antrian_v
 */
class m230901_093946_migrate_GLBJ212_view_antrian_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."antrian_v";
        '); 

        $this->execute('
            CREATE VIEW "public"."antrian_v" AS  SELECT antrian_t.antrian_id,
    antrian_t.no_antrian,
    antrian_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.no_telepon_pasien,
    antrian_t.ruangan_id,
    ruangan_m.ruangan_nama,
    antrian_t.carabayar_id,
    carabayar_m.carabayar_nama,
    antrian_t.penjamin_id,
    penjamin_m.penjamin_nama,
    antrian_t.pendaftaran_id,
    antrian_t.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    antrian_t.loket_id,
    loket_m.loket_nama,
    antrian_t.panggilan_ke,
    antrian_t.tgl_antrian,
    antrian_t.status_antrian,
        CASE
            WHEN antrian_t.status_antrian = 0 THEN \'Belum Panggil\'::text
            WHEN antrian_t.status_antrian = 1 THEN \'Panggil\'::text
            WHEN antrian_t.status_antrian = 2 THEN \'Lewati\'::text
            ELSE \'Batal\'::text
        END AS stat_antrian,
    antrian_t.status_pasien,
    fgetnamalookup(antrian_t.status_pasien) AS stat_pasien,
    COALESCE(resep.racikan_id, antrian_t.racikan_id) AS racikan_id,
    racikan_m.racikan_nama,
    pegawai_m.nama_pegawai,
    concat(fgetnamalookup(pegawai_m.gelardepan::integer), \' \', pegawai_m.nama_pegawai, \' \', gelarbelakang.gelarbelakang_nama) AS nama_pegawai_lengkap,
    fgetnamalookup(antrian_t.groupcarabayar_id) AS namagroupcarabayar,
    antrian_t.jenisantrian_id,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
    jadwalbukapoli_m.shift_id,
    antrian_t.is_online,
    antrian_t.fungsiantrian_id,
    fgetnamalookup(antrian_t.fungsiantrian_id) AS fungsi_nama,
    instalasi.instalasi_nama,
    antrian_t.antrian_farmasi,
        CASE
            WHEN fgetnamalookup(antrian_t.antrian_farmasi) IS NULL THEN \'Belum Proses\'::text::character varying
            ELSE fgetnamalookup(antrian_t.antrian_farmasi)
        END AS stat_antrian_farmasi,
        CASE
            WHEN antrian_t.antrian_farmasi = 584 THEN \'Siap Ambil\'::text
            WHEN antrian_t.antrian_farmasi = 585 THEN \'Selesai\'::text
            WHEN antrian_t.antrian_farmasi = 586 THEN \'Selesai\'::text
            ELSE \'Proses\'::text
        END AS stat_proses_antrian_farmasi,
    antrian_t.is_appointment,
    pendaftaran_t.no_pendaftaran,
    pendaftaranol_t.tgl_pendaftaranol,
    antrian_t.panggil_flag,
    pegawai_m.pegawai_id,
    ruangan_m.ruangan_urutan,
    jenisantriandetail_m.nama AS lantai,
    antrian_t.jenisantriandetail_id,
    resep.status_reseptur
   FROM antrian_t
     LEFT JOIN ruangan_m ON antrian_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN carabayar_m ON antrian_t.antrian_id = carabayar_m.carabayar_id
     LEFT JOIN layarantrian_m ON antrian_t.layarantrian_id = layarantrian_m.layarantrian_id
     LEFT JOIN loket_m ON antrian_t.loket_id = loket_m.loket_id
     LEFT JOIN pasien_m ON antrian_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN penjamin_m ON antrian_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pegawai_m ON antrian_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN jadwaldokter_m ON antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id AND jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true
     LEFT JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
     LEFT JOIN instalasi_m instalasi ON antrian_t.instalasi_id = instalasi.instalasi_id
     LEFT JOIN pendaftaran_t ON antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pendaftaranol_t ON antrian_t.antrian_id = pendaftaranol_t.antrian_id
     LEFT JOIN jenisantriandetail_m ON antrian_t.jenisantriandetail_id = jenisantriandetail_m.jenisantriandetail_id
     LEFT JOIN ( SELECT DISTINCT reseptur_t.antrian_id,
                CASE 
                    WHEN penjualanresep_t.penjualanresep_id IS NULL THEN \'Belum Proses\'::character varying
                    WHEN penjualanresep_t.status_reseptur = 347 THEN \'Dalam Proses\'::character varying
                    ELSE lookup_m.lookup_name
                END AS status_reseptur,
                CASE
                    WHEN resepturracikan_t.ct_racikan >= 1 THEN 1
                    ELSE resepturdetail_t.racikan_id
                END AS racikan_id,
            penjualanresep_t.penjualanresep_id
           FROM reseptur_t
             LEFT JOIN ( SELECT DISTINCT b.penjualanresep_id,
                    b.status_reseptur
                   FROM penjualanresep_t b) penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT a_1.reseptur_id,
                        CASE
                            WHEN grup_resep.qty > 1 THEN 1
                            ELSE a_1.racikan_id
                        END AS racikan_id
                   FROM resepturdetail_t a_1
                     JOIN ( SELECT count(*) AS qty,
                            x.reseptur_id
                           FROM ( SELECT a_2.racikan_id,
                                    a_2.reseptur_id
                                   FROM resepturdetail_t a_2
                                  GROUP BY a_2.racikan_id, a_2.reseptur_id) x
                          GROUP BY x.reseptur_id) grup_resep ON a_1.reseptur_id = grup_resep.reseptur_id
                  GROUP BY a_1.reseptur_id, (
                        CASE
                            WHEN grup_resep.qty > 1 THEN 1
                            ELSE a_1.racikan_id
                        END)) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
             LEFT JOIN ( SELECT count(*) AS ct_racikan,
                    a.reseptur_id
                   FROM resepturracikan_t a
                  GROUP BY a.reseptur_id) resepturracikan_t ON reseptur_t.reseptur_id = resepturracikan_t.reseptur_id
             LEFT JOIN lookup_m ON reseptur_t.status_reseptur = lookup_m.lookup_id
        UNION ALL
         SELECT DISTINCT penjualanresep_t.antrian_id,
                CASE
                    WHEN penjualanresep_t.penjualanresep_id IS NULL THEN \'Belum Proses\'::character varying
                    WHEN penjualanresep_t.status_reseptur = 347 THEN \'Dalam Proses\'::character varying
                    ELSE lookup_m.lookup_name
                END AS status_reseptur,
            obatalkespasien_t.racikan_id,
            penjualanresep_t.penjualanresep_id
           FROM penjualanresep_t
             LEFT JOIN ( SELECT obatalkespasien_t_1.penjualanresep_id,
                        CASE
                            WHEN grup_resep.qty > 1 THEN 1
                            ELSE obatalkespasien_t_1.racikan_id
                        END AS racikan_id
                   FROM obatalkespasien_t obatalkespasien_t_1
                     JOIN ( SELECT count(*) AS qty,
                            x.penjualanresep_id
                           FROM ( SELECT obatalkespasien_t_2.racikan_id,
                                    obatalkespasien_t_2.penjualanresep_id
                                   FROM obatalkespasien_t obatalkespasien_t_2
                                  GROUP BY obatalkespasien_t_2.penjualanresep_id, obatalkespasien_t_2.racikan_id) x
                          GROUP BY x.penjualanresep_id) grup_resep ON obatalkespasien_t_1.penjualanresep_id = grup_resep.penjualanresep_id
                  GROUP BY obatalkespasien_t_1.penjualanresep_id, (
                        CASE
                            WHEN grup_resep.qty > 1 THEN 1
                            ELSE obatalkespasien_t_1.racikan_id
                        END)) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
             LEFT JOIN lookup_m ON penjualanresep_t.status_reseptur = lookup_m.lookup_id) resep ON antrian_t.antrian_id = resep.antrian_id
     LEFT JOIN racikan_m ON COALESCE(resep.racikan_id, antrian_t.racikan_id) = racikan_m.racikan_id;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230901_093946_migrate_GLBJ212_view_antrian_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230901_093946_migrate_GLBJ212_view_antrian_v cannot be reverted.\n";

        return false;
    }
    */
}
