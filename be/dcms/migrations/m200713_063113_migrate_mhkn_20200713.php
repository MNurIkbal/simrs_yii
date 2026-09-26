<?php

use yii\db\Migration;

/**
 * Class m200713_063113_migrate_mhkn_20200713
 */
class m200713_063113_migrate_mhkn_20200713 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "is_rekammedik_aps" bool DEFAULT true;');
         
         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pasien_m\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
     vId integer; 
  vPrefix varchar;
  vLast VARCHAR;
  vNumber VARCHAR;
  vIsAps BOOLEAN;
  vdigit_rm INT;
    vprefix_rm VARCHAR;
    vis_rekammedik_aps BOOLEAN;
BEGIN
    vIsAps := NEW.is_aps;               
    
    SELECT digit_rekammedik , is_rekammedik_aps
    INTO vdigit_rm, vis_rekammedik_aps
    FROM konfigsystem_k
    WHERE konfigsystem_id = 1;
    
    
    
    SELECT COALESCE(prefix,'') INTO vprefix_rm
    FROM penomoran_k
    WHERE penomoran_id = 57;
    
    IF(vis_rekammedik_aps IS TRUE )
    THEN
        IF(vIsAps) THEN
                vId := 128;
                SELECT 
                     prefix,
                        ( SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no 
                                FROM penomoran_k where penomoran_id = vId
                        )
                    INTO 
                         vPrefix,
                         vNumber
                    FROM penomoran_k where penomoran_id = vId;

                vLast := vPrefix || vNumber;
                UPDATE penomoran_k SET
                    last_number = vNumber,
                    last_generate = vLast
                WHERE penomoran_id = vId;
                
                NEW.no_rekam_medik := vLast;

                RETURN NEW;
        ELSE
                    vId := 26;
                    SELECT 
                                CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 8) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no
                    INTO 
                                vPrefix
                    FROM penomoran_k where penomoran_id = vId;
                    
                    vPrefix := CONCAT(vprefix_rm,vPrefix);
                    
                    UPDATE penomoran_k SET
                                last_number = vPrefix,
                                last_generate = vPrefix
                    WHERE penomoran_id = vId;
                
                    NEW.no_rekam_medik := vPrefix;

                    RETURN NEW;
        END IF;
    ELSE
        vId := 26;
        SELECT 
                    CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 8) AS INT), 0) + 1 AS VARCHAR(20)), vdigit_rm, '0')) last_no
        INTO 
                    vPrefix
        FROM penomoran_k where penomoran_id = vId;
        
        vPrefix := CONCAT(vprefix_rm,vPrefix);
        
        UPDATE penomoran_k SET
                    last_number = vPrefix,
                    last_generate = vPrefix
        WHERE penomoran_id = vId;
    
        NEW.no_rekam_medik := vPrefix;

        RETURN NEW;
    END IF;
    
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('DROP VIEW if exists "public"."rincianpasien_v";');

         $this->execute("
            CREATE VIEW \"public\".\"rincianpasien_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_admisi.carabayar_nama AS carabayar_admisi,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_admisi.penjamin_nama AS penjamin_admisi,
    pendaftaran_t.kelaspelayanan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN kelas_admisi.kelaspelayanan_nama
            ELSE kelaspelayanan_m.kelaspelayanan_nama
        END AS kelaspelayanan_nama,
    kelas_admisi.kelaspelayanan_nama AS kelas_admisi,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_lunas,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN (pendaftaran_t.is_stopakomodasi <> true) THEN false
            WHEN (pendaftaran_t.is_stopakomodasi = true) THEN true
            ELSE NULL::boolean
        END AS is_pulang,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)
        END AS tgl_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'HH24:MI:SS'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'HH24:MI:SS'::text)
        END AS jam_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END AS tgl_keluar,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END AS jam_keluar
   FROM ((((((((((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     LEFT JOIN bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
     LEFT JOIN pasienpulang_t pulang_pendaftaran ON ((pendaftaran_t.pasienpulang_id = pulang_pendaftaran.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_admisi ON ((pasienadmisi_t.pasienpulang_id = pulang_admisi.pasienpulang_id)))
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasienadmisi_t.tgl_admisi, pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, pendaftaran_t.ruangan_id, r_pendaftaran.ruangan_nama, pasienadmisi_t.ruangan_id, r_admisi.ruangan_nama, pasienadmisi_t.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, pasienadmisi_t.kamartempattidur_id, kamartempattidur_m.no_tempattidur, pendaftaran_t.status_bayar, (fgetnamalookup(pendaftaran_t.status_bayar)), penjamin_admisi.penjamin_nama, carabayar_admisi.carabayar_nama, kelas_admisi.kelaspelayanan_nama, pasien_m.namadepan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END;");


         $this->execute('select public.deps_save_and_drop_dependencies(\'public\', \'penjamin_m\');');
        
         $this->execute('ALTER TABLE "public"."penjamin_m" 
                          ALTER COLUMN "penjamin_nama" TYPE varchar(200) COLLATE "pg_catalog"."default",
                          ALTER COLUMN "penjamin_namalainnya" TYPE varchar(200) COLLATE "pg_catalog"."default";');
        
         $this->execute('select public.deps_restore_dependencies(\'public\', \'penjamin_m\');');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200713_063113_migrate_mhkn_20200713 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200713_063113_migrate_mhkn_20200713 cannot be reverted.\n";

        return false;
    }
    */
}
