<?php

use yii\db\Migration;

/**
 * Class m211116_130742_migrate_US1754_mobilejkn_konfigkuota_percarabayar
 */
class m211116_130742_migrate_US1754_mobilejkn_konfigkuota_percarabayar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
            ADD COLUMN IF NOT EXISTS "is_pisah_cabar" bool DEFAULT false;
        ');

        $this->execute('DROP VIEW if exists public.infojadwaldokter_v;');
        $this->execute('DROP VIEW if exists public.antrianjkn_v;');
        $this->execute('DROP VIEW if exists public.infokuotadokter_v;');

        $this->execute('ALTER TABLE "public"."jadwaldokter_m" 
          DROP COLUMN IF EXISTS "kuota_bpjs_total",
          DROP COLUMN IF EXISTS "kuota_nonbpjs_total",
          DROP COLUMN IF EXISTS "kuota_bpjs_offline",
          DROP COLUMN IF EXISTS "kuota_nonbpjs_offline",
          DROP COLUMN IF EXISTS "kuota_bpjs_online",
          DROP COLUMN IF EXISTS "kuota_nonbpjs_online";
        ');

        $this->execute('ALTER TABLE "public"."kuotadokter_r" 
          DROP COLUMN IF EXISTS "kuota_bpjs_offline",
          DROP COLUMN IF EXISTS "kuota_nonbpjs_offline",
          DROP COLUMN IF EXISTS "kuota_bpjs_online",
          DROP COLUMN IF EXISTS "kuota_nonbpjs_online",
          DROP COLUMN IF EXISTS "kuota_out_bpjs",
          DROP COLUMN IF EXISTS "kuota_out_nonbpjs";
        ');

        $this->execute('ALTER TABLE "public"."stokkuotadokter_t" 
          DROP COLUMN IF EXISTS "kuota_bpjs_offline",
          DROP COLUMN IF EXISTS "kuota_nonbpjs_offline",
          DROP COLUMN IF EXISTS "kuota_bpjs_online",
          DROP COLUMN IF EXISTS "kuota_nonbpjs_online",
          DROP COLUMN IF EXISTS "kuota_out_bpjs",
          DROP COLUMN IF EXISTS "kuota_out_nonbpjs";
        ');

        $this->execute('ALTER TABLE "public"."jadwaldokter_m" 
          ADD COLUMN IF NOT EXISTS "kuota_bpjs_total" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_nonbpjs_total" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_bpjs_offline" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_nonbpjs_offline" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_bpjs_online" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_nonbpjs_online" float4 DEFAULT 0;
        ');

        $this->execute('ALTER TABLE "public"."kuotadokter_r" 
          ADD COLUMN IF NOT EXISTS "kuota_bpjs_offline" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_nonbpjs_offline" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_bpjs_online" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_nonbpjs_online" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_out_bpjs" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_out_nonbpjs" float4 DEFAULT 0;
        ');

        $this->execute('ALTER TABLE "public"."stokkuotadokter_t" 
          ADD COLUMN IF NOT EXISTS "kuota_bpjs_offline" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_nonbpjs_offline" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_bpjs_online" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_nonbpjs_online" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_out_bpjs" float4 DEFAULT 0,
          ADD COLUMN IF NOT EXISTS "kuota_out_nonbpjs" float4 DEFAULT 0;
        ');

        $this->execute('ALTER TABLE "public"."pegawai_m" 
          ADD COLUMN IF NOT EXISTS "kode_dokter_bpjs" varchar(10) COLLATE "pg_catalog"."default";
        ');

        $this->execute('ALTER TABLE "public"."ruangan_m" 
          ADD COLUMN IF NOT EXISTS "kode_ruangan_bpjs" varchar(10) COLLATE "pg_catalog"."default";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."antrianjkn_r" (
          "antrianjkn_id" serial4,
          "pendaftaranol_id" int4,
          "antrian_id" int4,
          "tanggal_periksa" timestamp(0),
          "nomorkartu" varchar(50) COLLATE "pg_catalog"."default",
          "jenis_cara_bayar" int4,
          "jeniskunjungan" int4,
          "nomorreferensi" varchar(100) COLLATE "pg_catalog"."default",
          "keterangan" varchar(100) COLLATE "pg_catalog"."default",
          "is_checkin" bool DEFAULT false,
          "is_selesai_periksa" bool DEFAULT false,
          "is_batal_periksa" bool DEFAULT false,
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool DEFAULT false,
          "is_active" bool DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "no_rekam_medik" varchar(100) COLLATE "pg_catalog"."default",
          CONSTRAINT "antrianjkn_r_pkey" PRIMARY KEY ("antrianjkn_id")
          );
        ');

        $this->execute('ALTER TABLE "public"."antrianjkn_r" 
          OWNER TO "postgres";
        ');

        $this->execute('COMMENT ON COLUMN "public"."antrianjkn_r"."jenis_cara_bayar" IS \'pakai lookup_type jenispasienjkn\';
        ');

        $this->execute('COMMENT ON COLUMN "public"."antrianjkn_r"."jeniskunjungan" IS \'pakai lookup_type jeniskunjungan\';
        ');


        $this->execute("
            CREATE VIEW \"public\".\"infojadwaldokter_v\" AS
            SELECT jadwaldokter_m.jadwaldokter_id,
            jadwaldokter_m.ruangan_id,
            jadwaldokter_m.instalasi_id,
            jadwaldokter_m.pegawai_id,
            ruangan_m.ruangan_nama,
            pegawai_m.nama_pegawai,
            jadwaldokter_m.jadwaldokter_hari,
            concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS \"Waktu\",
            jadwaldokter_m.maximumantrian AS kuota,
            jadwaldokter_m.jadwaldokter_mulai AS waktu_mulai,
            jadwaldokter_m.jadwaldokter_tutup AS waktu_selesai,
            jadwaldoktertambahan_m.kuota_penambahan,
            ((jadwaldokter_m.maximumantrian)::double precision + (jadwaldoktertambahan_m.kuota_penambahan)::double precision) AS total_kuota,
            fgetnamalookup(jadwalbukapoli_m.hari) AS hari,
            jadwalbukapoli_m.hari AS hari_jadwalbuka,
            jadwaldokter_m.kuota_online,
            jadwaldokter_m.jadwaldokter_tgl,
            pegawai_m.dokter_id,
            ruangan_m.poliklinik_id,
            CASE
            WHEN (jadwalbukapoli_m.shift_id IS NULL) THEN 0
            ELSE jadwalbukapoli_m.shift_id
            END AS shift_id,
            shift_m.shift1_id,
            jadwaldokter_m.notifikasi_id,
            notifikasi_m.judul_temp,
            notifikasi_m.notifikasi,
            COALESCE(kuotadokter_r.kuota_tersedia, (0)::real) AS kuota_tersedia,
            jadwaldokter_m.is_active,
            kuotadokter_r.kuotadokter_id,
            jadwaldokter_m.jadwalbukapoli_id,
            jadwaldokter_m.kuota_total,
            pegawai_m.kode_dokter_bpjs,
            ruangan_m.kode_ruangan_bpjs,
            jadwaldokter_m.kuota_bpjs_online,
            jadwaldokter_m.kuota_nonbpjs_online
            FROM ((((((((jadwaldokter_m
            JOIN ruangan_m ON ((jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pegawai_m ON ((jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN jadwaldoktertambahan_m ON ((jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id)))
            JOIN jadwalbukapoli_m ON (((jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id) AND (jadwalbukapoli_m.is_deleted = false))))
            LEFT JOIN shift_m ON ((jadwalbukapoli_m.shift_id = shift_m.shift_id)))
            LEFT JOIN notifikasi_m ON ((jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id)))
            LEFT JOIN kuotadokter_r ON (((jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id) AND kuotadokter_r.is_online)))
            WHERE ((jadwaldokter_m.is_deleted = false) AND (jadwaldokter_m.is_active = true) AND (pegawai_m.is_deleted = false) AND (pegawai_m.is_active = true))
            ;");
            $this->execute('
                ALTER TABLE public.infojadwaldokter_v OWNER TO postgres;
            ');


            $this->execute("
                CREATE VIEW \"public\".\"antrianjkn_v\" AS
                SELECT pendaftaranol_t.pendaftaranol_id,
                antrian_t.pendaftaran_id,
                pendaftaranol_t.no_pendaftaranol AS kodebooking,
                antrianjkn_r.jenis_cara_bayar,
                fgetnamalookup(antrianjkn_r.jenis_cara_bayar) AS jenispasien,
                antrianjkn_r.nomorkartu,
                pendaftaranol_t.jenisidentitas,
                fgetnamalookup((pendaftaranol_t.jenisidentitas)::integer) AS jenisidentitas_nama,
                pendaftaranol_t.no_identitas_pasien,
                pendaftaranol_t.no_telepon_pasien,
                ruangan_m.kode_ruangan_bpjs AS kodepoli,
                ruangan_m.ruangan_nama AS namapoli,
                CASE
                WHEN (pendaftaranol_t.status_pasien = 311) THEN 0
                ELSE 1
                END AS status_pasien,
                fgetnamalookup(pendaftaranol_t.status_pasien) AS status_pasien_nama,
                antrianjkn_r.no_rekam_medik,
                antrianjkn_r.tanggal_periksa,
                pegawai_m.kode_dokter_bpjs AS kodedokter,
                pegawai_m.nama_pegawai AS namadokter,
                concat(to_char((jadwaldokter_m.jadwaldokter_mulai)::interval, 'HH24:MI'::text), ' - ', to_char((jadwaldokter_m.jadwaldokter_tutup)::interval, 'HH24:MI'::text)) AS jampraktek,
                fgetnamalookup(antrianjkn_r.jeniskunjungan) AS jeniskunjungan,
                antrianjkn_r.nomorreferensi,
                antrian_t.no_antrian AS nomorantrean,
                antrian_t.temp_urutan AS angkaantrean,
                jadwaldokter_m.kuota_bpjs_online AS kuotajkn,
                jadwaldokter_m.kuota_nonbpjs_online AS kuotanonjkn,
                kuotadokter_r.kuota_bpjs_online AS sisakuotajkn,
                kuotadokter_r.kuota_nonbpjs_online AS sisakuotanonjkn,
                antrianjkn_r.keterangan,
                jadwaldokter_m.jumlah_loaddokter AS estimasidilayani,
                antrian_t.antrian_id,
                jadwaldokter_m.jadwaldokter_id
                FROM ((((((pendaftaranol_t
                JOIN antrian_t ON ((pendaftaranol_t.antrian_id = antrian_t.antrian_id)))
                JOIN jadwaldokter_m ON ((antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id)))
                JOIN ruangan_m ON ((jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id)))
                JOIN pegawai_m ON ((jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id)))
                JOIN kuotadokter_r ON (((jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id) AND (kuotadokter_r.is_online = true))))
                JOIN antrianjkn_r ON ((pendaftaranol_t.pendaftaranol_id = antrianjkn_r.pendaftaranol_id)))
                ;");
            $this->execute('
                ALTER TABLE public.antrianjkn_v OWNER TO postgres;
                ');

            $this->execute("
                CREATE VIEW \"public\".\"infokuotadokter_v\" AS
                SELECT kuotadokter_r.kuotadokter_id,
                kuotadokter_r.jadwaldokter_id,
                NULL::integer AS jadwalbukapoli_id,
                jadwaldokter_m.pegawai_id,
                jadwaldokter_m.instalasi_id,
                jadwaldokter_m.ruangan_id,
                jadwaldokter_m.shift_id,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                pegawai_m.nama_pegawai,
                jadwalbukapoli_m.hari,
                fgetnamalookup(jadwalbukapoli_m.hari) AS nama_hari,
                jadwaldokter_m.jadwaldokter_mulai,
                jadwaldokter_m.jadwaldokter_tutup,
                kuotadokter_r.kuota_real,
                kuotadokter_r.kuota_masuk,
                kuotadokter_r.kuota_keluar,
                kuotadokter_r.kuota_tersedia,
                kuotadokter_r.is_online,
                jadwaldokter_m.is_active,
                kuotadokter_r.kuota_bpjs_offline,
                kuotadokter_r.kuota_nonbpjs_offline,
                kuotadokter_r.kuota_bpjs_online,
                kuotadokter_r.kuota_nonbpjs_online,
                kuotadokter_r.kuota_out_bpjs,
                kuotadokter_r.kuota_out_nonbpjs
                FROM ((((((kuotadokter_r
                JOIN jadwaldokter_m ON (((kuotadokter_r.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id) AND (jadwaldokter_m.is_deleted = false))))
                JOIN ruangan_m ON ((jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id)))
                JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                LEFT JOIN pegawai_m ON ((jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id)))
                LEFT JOIN jadwaldoktertambahan_m ON ((jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id)))
                LEFT JOIN jadwalbukapoli_m ON (((jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id) AND (jadwalbukapoli_m.is_deleted = false))))
                UNION ALL
                SELECT kuotadokter_r.kuotadokter_id,
                NULL::integer AS jadwaldokter_id,
                kuotadokter_r.jadwalbukapoli_id,
                NULL::integer AS pegawai_id,
                instalasi_m.instalasi_id,
                jadwalbukapoli_m.ruangan_id,
                jadwalbukapoli_m.shift_id,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                NULL::character varying AS nama_pegawai,
                jadwalbukapoli_m.hari,
                fgetnamalookup(jadwalbukapoli_m.hari) AS nama_hari,
                jadwalbukapoli_m.jam_mulai AS jadwaldokter_mulai,
                jadwalbukapoli_m.jam_tutup AS jadwaldokter_tutup,
                kuotadokter_r.kuota_real,
                kuotadokter_r.kuota_masuk,
                kuotadokter_r.kuota_keluar,
                kuotadokter_r.kuota_tersedia,
                kuotadokter_r.is_online,
                jadwalbukapoli_m.is_active,
                kuotadokter_r.kuota_bpjs_offline,
                kuotadokter_r.kuota_nonbpjs_offline,
                kuotadokter_r.kuota_bpjs_online,
                kuotadokter_r.kuota_nonbpjs_online,
                kuotadokter_r.kuota_out_bpjs,
                kuotadokter_r.kuota_out_nonbpjs
                FROM (((kuotadokter_r
                JOIN jadwalbukapoli_m ON (((kuotadokter_r.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id) AND (jadwalbukapoli_m.is_deleted = false))))
                JOIN ruangan_m ON ((jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id)))
                JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                ;");
            $this->execute('
                ALTER TABLE public.infokuotadokter_v OWNER TO postgres;
                ');

        $this->execute("
CREATE OR REPLACE FUNCTION \"public\".\"upd_stokkuotadokter_from_jadwal\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
                DECLARE
                    vJadwalId integer;
                    vMasuk float;
                    vKeluar float;
                    vJadwalExist integer;
                BEGIN
                    IF (TG_OP = 'INSERT') THEN
                        vJadwalId = NEW.jadwaldokter_id;
                        IF (NEW.maximumantrian >= 0) THEN
                                INSERT INTO stokkuotadokter_t(jadwaldokter_id,flag,tgltransaksi_in,kuota_in,is_deleted,is_active,is_online,kuota_bpjs_offline,kuota_nonbpjs_offline)
                                VALUES(vJadwalId,true,now(),NEW.maximumantrian,NEW.is_deleted,NEW.is_active,false,NEW.kuota_bpjs_offline,NEW.kuota_nonbpjs_offline);
                        END IF;
                        IF (NEW.kuota_online >= 0) THEN
                                INSERT INTO stokkuotadokter_t(jadwaldokter_id,flag,tgltransaksi_in,kuota_in,is_deleted,is_active,is_online,kuota_bpjs_online,kuota_nonbpjs_online)
                                VALUES(vJadwalId,true,now(),NEW.kuota_online,NEW.is_deleted,NEW.is_active,true,NEW.kuota_bpjs_online,NEW.kuota_nonbpjs_online);
                        END IF;
                        RETURN NEW;
                    ELSIF (TG_OP = 'UPDATE') THEN
                        vJadwalId = NEW.jadwaldokter_id;
                        SELECT jadwaldokter_id
                        INTO vJadwalExist
                        FROM stokkuotadokter_t
                        WHERE jadwaldokter_id = vJadwalId
                        LIMIT 1;
                        IF (vJadwalExist IS NULL) THEN
                            IF (NEW.maximumantrian >= 0) THEN
                                INSERT INTO stokkuotadokter_t(jadwaldokter_id,flag,tgltransaksi_in,kuota_in,is_deleted,is_active,is_online,kuota_bpjs_offline,kuota_nonbpjs_offline)
                                VALUES(vJadwalId,true,now(),NEW.maximumantrian,NEW.is_deleted,NEW.is_active,false,NEW.kuota_bpjs_offline,NEW.kuota_nonbpjs_offline);
                            END IF;
                            IF (NEW.kuota_online >= 0) THEN
                                    INSERT INTO stokkuotadokter_t(jadwaldokter_id,flag,tgltransaksi_in,kuota_in,is_deleted,is_active,is_online,kuota_bpjs_online,kuota_nonbpjs_online)
                                    VALUES(vJadwalId,true,now(),NEW.kuota_online,NEW.is_deleted,NEW.is_active,true,NEW.kuota_bpjs_online,NEW.kuota_nonbpjs_online);
                            END IF;
                            RETURN NULL;
                        ELSE
                                                --kondisi ketika kuota poliklinik
                                UPDATE stokkuotadokter_t SET
                                kuota_in = NEW.maximumantrian,
                                tgltransaksi_in = now(),
                                flag = true,
                                is_deleted = NEW.is_deleted,
                                is_active = NEW.is_active
                                WHERE jadwaldokter_id = vJadwalId AND is_online = false;
                        
                                UPDATE stokkuotadokter_t SET
                                kuota_in = NEW.kuota_online,
                                tgltransaksi_in = now(),
                                flag = true,
                                is_deleted = NEW.is_deleted,
                                is_active = NEW.is_active
                                WHERE jadwaldokter_id = vJadwalId AND is_online = true;
                --          UPDATE stokkuotadokter_t SET
                --          kuota_in = NEW.maximumantrian,
                --          tgltransaksi_in = now(),
                --          flag = true,
                --          is_deleted = NEW.is_deleted,
                --          is_active = NEW.is_active
                --          WHERE jadwaldokter_id = vJadwalId;
                            RETURN NULL;
                        END IF;
                        RETURN NULL;
                    ELSIF (TG_OP = 'DELETE') THEN
                        DELETE FROM stokkuotadokter_t WHERE jadwaldokter_id = vJadwalId;
                    END IF;
                END
                \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");

        $this->execute("
CREATE OR REPLACE FUNCTION \"public\".\"upd_stokkuotadokter_from_antrian_try\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
vAntrianId integer;
vPegawaiId integer;
vJadwalId integer;
vJadwalPoliId integer;
vJadwalExist integer;
vMaxAntrian integer;
vAsalStok integer;
vAsalStokOnline integer;
idKonsulPoli INTEGER;
isBpjs BOOLEAN;
vGroupCarabayar INTEGER;



BEGIN
        IF(TG_OP = 'DELETE') THEN
                DELETE FROM stokkuotadokter_t WHERE antrian_id = OLD.antrian_id;
                RETURN NULL;
        END IF;

        vAntrianId = NEW.antrian_id;
        vPegawaiId = NEW.pegawai_id;
        vJadwalId = NEW.jadwaldokter_id;
        vJadwalPoliId = NEW.jadwalbukapoli_id;
        vGroupCarabayar = NEW.groupcarabayar_id;
        isBpjs = FALSE;

        IF (vPegawaiId IS NOT NULL AND vJadwalId IS NOT NULL) THEN
                SELECT stokkuotadokter_id
                INTO vAsalStok
                FROM stokkuotadokter_t
                WHERE jadwaldokter_id = vJadwalId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0;
                
                IF (vGroupCarabayar IS NOT NULL) THEN
                    IF (vGroupCarabayar = '418') THEN
                    isBpjs = TRUE;
                    ELSE
                    isBpjs = FALSE;
                    END IF;
                END IF;
                
                IF (TG_OP = 'INSERT') THEN
                        IF (NEW.jenisantrian_id = '312') THEN
                                SELECT stokkuotadokter_id
                                INTO vAsalStokOnline
                                FROM stokkuotadokter_t
                                WHERE jadwaldokter_id = vJadwalId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0 AND is_online = TRUE;
                                
                                IF (NEW.is_online) THEN
                                IF (isBpjs = TRUE) THEN
                                    INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                        VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online,1);
                                ELSE
                                    INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                        VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online,1);
                                END IF;
--                                      INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
--                                      VALUES (vAntrianId,true,vJadwalId,vAsalStokOnline,now(),1,NEW.is_online);
                                END IF;
                                RETURN NEW;
                        END IF;
                        /*SELECT jadwaldokter_id
                        INTO vJadwalExist
                        FROM stokkuotadokter_t
                        WHERE jadwaldokter_id = vJadwalId
                        LIMIT 1;*/
                        IF (vAsalStok IS NULL) THEN
                                SELECT maximumantrian
                                INTO vMaxAntrian
                                FROM jadwaldokter_m
                                WHERE jadwaldokter_id = vJadwalId;
                                INSERT INTO stokkuotadokter_t (jadwaldokter_id,flag,tgltransaksi_in,kuota_in,kuota_out)
                                VALUES (vJadwalId,true,now(),vMaxAntrian,0);
                        END IF;
            
                        IF (NEW.is_konsulpoli = TRUE) THEN
                        IF (isBpjs = TRUE ) THEN
                            SELECT konsulpoli_id INTO idKonsulPoli FROM konsulpoli_t  WHERE antrian_id = vAntrianId;
                                INSERT INTO stokkuotadokter_t(konsulpoli_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online, kuota_out_bpjs)
                                VALUES (idKonsulPoli,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                        ELSE
                            SELECT konsulpoli_id INTO idKonsulPoli FROM konsulpoli_t  WHERE antrian_id = vAntrianId;
                                INSERT INTO stokkuotadokter_t(konsulpoli_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online, kuota_out_nonbpjs)
                                VALUES (idKonsulPoli,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                        END IF;
--                              SELECT konsulpoli_id INTO idKonsulPoli FROM konsulpoli_t  WHERE antrian_id = vAntrianId;
--                              INSERT INTO stokkuotadokter_t(konsulpoli_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
--                              VALUES (idKonsulPoli,true,vJadwalId,vAsalStok,now(),1,NEW.is_online);
                        ELSE
                        IF (isBpjs = TRUE ) THEN
                            INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_bpjs)
                                VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                            ELSE 
                            INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online,kuota_out_nonbpjs)
                                VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online,1);
                        END IF;
--                              INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwaldokter_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
--                              VALUES (vAntrianId,true,vJadwalId,vAsalStok,now(),1,NEW.is_online);
                        END IF;

                        RETURN NEW;
                ELSIF (TG_OP = 'UPDATE') THEN
                        IF(NEW.status_antrian = 4) THEN
                                RETURN NEW;
                        END IF;

                        RETURN NEW;
                END IF;
        ELSE
                SELECT stokkuotadokter_id
                INTO vAsalStok
                FROM stokkuotadokter_t
                WHERE jadwalbukapoli_id = vJadwalPoliId AND is_online = FALSE AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0;
        
                IF (TG_OP = 'INSERT') THEN
                        IF (NEW.jenisantrian_id = '312') THEN
                                SELECT stokkuotadokter_id
                                INTO vAsalStokOnline
                                FROM stokkuotadokter_t
                                WHERE jadwalbukapoli_id = vJadwalPoliId AND flag = TRUE AND is_deleted = FALSE AND is_active = TRUE AND kuota_in != 0 AND is_online = TRUE;
                                
                                IF (NEW.is_online) THEN
                                        INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwalbukapoli_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
                                        VALUES (vAntrianId,true,vJadwalPoliId,vAsalStokOnline,now(),1,NEW.is_online);
                                END IF;
                                RETURN NEW;
                        END IF;

                        IF (vAsalStok IS NULL) THEN
                                SELECT maxantrian_poli
                                INTO vMaxAntrian
                                FROM jadwalbukapoli_m
                                WHERE jadwalbukapoli_id = vJadwalPoliId;

                                INSERT INTO stokkuotadokter_t (jadwalbukapoli_id, flag, tgltransaksi_in, kuota_in, kuota_out)
                                VALUES (vJadwalPoliId, true, now(), vMaxAntrian, 0);
                        ELSE
                                INSERT INTO stokkuotadokter_t(antrian_id,flag,jadwalbukapoli_id,kuotaasal_id,tgltransaksi_out,kuota_out,is_online)
                                VALUES (vAntrianId,false,vJadwalPoliId,vAsalStok,now(),1,NEW.is_online);
                        END IF;

                        RETURN NEW;
                ELSIF (TG_OP = 'UPDATE') THEN
                        IF(NEW.status_antrian = 4) THEN
                                RETURN NEW;
                        END IF;
                        RETURN NEW;
                END IF;
        END IF;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");

        $this->execute("
CREATE OR REPLACE FUNCTION \"public\".\"upd_kuotadokter_r_try\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    DECLARE
vIdJadwal integer;
vIdJ integer;
vNewId integer;
vJadwalExist integer;
vMasuk integer;
vMasukOnline integer;
vKeluar integer;
vKeluarOnline integer;

vMasukBpjsOffline INTEGER;
vMasukNonBpjsOffline INTEGER;
vMasukBpjsOnline INTEGER;
vMasukNonBpjsOnline INTEGER;

vKeluarBpjsOffline INTEGER;
vKeluarNonBpjsOffline INTEGER;
vKeluarBpjsOnline INTEGER;
vKeluarNonBpjsOnline INTEGER;

qtyIn INTEGER;
qtyInOnline INTEGER;
qtyOut INTEGER;
qtyOutOnline INTEGER;
qtyMasuk INTEGER;
qtyMasukOnline INTEGER;

qtyInBpjsOffline INTEGER;
qtyInNonBpjsOffline INTEGER;
qtyInBpjsOnline INTEGER;
qtyInNonBpjsOnline INTEGER;

qtyOutBpjsOffline INTEGER;
qtyOutNonBpjsOffline INTEGER;
qtyOutBpjsOnline INTEGER;
qtyOutNonBpjsOnline INTEGER;

kuotaKeluar INTEGER;
kuotaTersedia INTEGER;
kuotaKeluarOnline INTEGER;
kuotaTersediaOnline INTEGER;

kuotaBpjsOffline INTEGER;
kuotaBpjsOnline INTEGER;
kuotaNonBpjsOnline INTEGER;
kuotaNonBpjsOffline INTEGER;

kuotaOutBpjsOffline INTEGER;
kuotaOutNonBpjsOffline INTEGER;
kuotaOutBpjsOnline INTEGER;
kuotaOutNonBpjsOnline INTEGER;

BEGIN
        qtyIn := 0;
        qtyInOnline := 0;
        qtyOut := 0;
        qtyOutOnline := 0;
        
        qtyInBpjsOffline := 0;
        qtyInNonBpjsOffline := 0;
        qtyInBpjsOnline := 0;
        qtyInNonBpjsOnline := 0;
        
        qtyOutBpjsOffline := 0;
        qtyOutNonBpjsOffline := 0;
        qtyOutBpjsOnline := 0;
        qtyOutNonBpjsOnline := 0;

        IF (NEW.tgltransaksi_in IS NOT NULL  AND NEW.tgltransaksi_out IS NULL AND NEW.is_online = false) THEN 
                qtyIn := NEW.kuota_in;
                qtyInBpjsOffline := NEW.kuota_bpjs_offline;
                qtyInNonBpjsOffline := NEW.kuota_nonbpjs_offline;
        ELSEIF (NEW.tgltransaksi_in IS NOT NULL  AND NEW.tgltransaksi_out IS NULL AND NEW.is_online = true) THEN
                qtyInOnline := NEW.kuota_in;
                qtyInBpjsOnline := NEW.kuota_bpjs_online;
                qtyInNonBpjsOnline := NEW.kuota_nonbpjs_online;
        ELSEIF (NEW.tgltransaksi_in IS NULL AND NEW.tgltransaksi_out IS NOT NULL AND NEW.is_online = false) THEN
                qtyOut := NEW.kuota_out;
                qtyOutBpjsOffline := NEW.kuota_out_bpjs;
                qtyOutNonBpjsOffline := NEW.kuota_out_nonbpjs;
        ELSEIF (NEW.tgltransaksi_in IS NULL AND NEW.tgltransaksi_out IS NOT NULL AND NEW.is_online = true) THEN
                qtyOutOnline := NEW.kuota_out;
                qtyOutBpjsOnline := NEW.kuota_out_bpjs;
                qtyOutNonBpjsOnline := NEW.kuota_out_nonbpjs;
        END IF;

        IF (NEW.jadwaldokter_id IS NOT NULL) THEN
                -- Offline kuotadokter_r
                SELECT 
                        jadwaldokter_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_offline,
                        kuota_nonbpjs_offline,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExist,
                        kuotaKeluar,
                        kuotaTersedia,
                        qtyMasuk,
                        kuotaBpjsOffline,
                        kuotaNonBpjsOffline,
                        kuotaOutBpjsOffline,
                        kuotaOutNonBpjsOffline
                FROM kuotadokter_r
                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = false;
                
                -- Online kuotadokter_r
                SELECT 
                        jadwaldokter_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_online,
                        kuota_nonbpjs_online,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExist,
                        kuotaKeluarOnline,
                        kuotaTersediaOnline,
                        qtyMasukOnline,
                        kuotaBpjsOnline,
                        kuotaNonBpjsOnline,
                        kuotaOutBpjsOnline,
                        kuotaOutNonBpjsOnline
                FROM kuotadokter_r
                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = true;
        ELSE
                -- Offline kuotadokter_r --kuotapoli
                SELECT 
                        jadwalbukapoli_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_offline,
                        kuota_nonbpjs_offline,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExist,
                        kuotaKeluar,
                        kuotaTersedia,
                        qtyMasuk,
                        kuotaBpjsOffline,
                        kuotaNonBpjsOffline,
                        kuotaOutBpjsOffline,
                        kuotaOutNonBpjsOffline
                FROM kuotadokter_r
                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = false;
                
                -- Online kuotadokter_r
                SELECT 
                        jadwalbukapoli_id,
                        kuota_keluar,
                        kuota_tersedia,
                        kuota_masuk,
                        kuota_bpjs_online,
                        kuota_nonbpjs_online,
                        kuota_out_bpjs,
                        kuota_out_nonbpjs
                INTO 
                        vJadwalExist,
                        kuotaKeluarOnline,
                        kuotaTersediaOnline,
                        qtyMasukOnline,
                        kuotaBpjsOnline,
                        kuotaNonBpjsOnline,
                        kuotaOutBpjsOnline,
                        kuotaOutNonBpjsOnline
                FROM kuotadokter_r
                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = true;
        END IF;
 
        IF (NEW.batalkonsolpoli_id IS NOT NULL OR NEW.bataljanjipoli_id IS NOT NULL OR NEW.batalantrian_id IS NOT NULL) THEN
                vMasuk := kuotaTersedia + qtyIn;
                vKeluar := kuotaKeluar - qtyIn;
                
                vMasukBpjsOffline := kuotaBpjsOffline + qtyOutBpjsOffline;
                vMasukNonBpjsOffline := kuotaNonBpjsOffline + qtyOutNonBpjsOffline;
                vMasukBpjsOnline := kuotaBpjsOnline + qtyOutBpjsOnline;
                vMasukNonBpjsOnline := kuotaNonBpjsOnline + qtyOutNonBpjsOnline;
                
                vKeluarBpjsOffline :=  kuotaOutBpjsOffline - qtyOutBpjsOffline;
                vKeluarNonBpjsOffline :=  kuotaOutNonBpjsOffline - qtyOutNonBpjsOffline;
                vKeluarBpjsOnline :=  kuotaOutBpjsOnline - qtyOutBpjsOnline;
                vKeluarNonBpjsOnline :=  kuotaOutNonBpjsOnline - qtyOutNonBpjsOnline;
        ELSEIF (NEW.jadwaldoktertambahan_id IS NOT NULL) THEN
                vMasuk := kuotaTersedia + qtyIn;
                qtyMasuk := qtyMasuk + qtyIn;
                vKeluar := kuotaKeluar;
        ELSE
                vMasuk := kuotaTersedia - qtyOut;
                vMasukOnline := kuotaTersediaOnline - qtyOutOnline;
                vKeluar := kuotaKeluar + qtyOut;
                vKeluarOnline := kuotaKeluarOnline + qtyOutOnline;
                
                vMasukBpjsOffline := kuotaBpjsOffline - qtyOutBpjsOffline;
                vMasukNonBpjsOffline := kuotaNonBpjsOffline - qtyOutNonBpjsOffline;
                vMasukBpjsOnline := kuotaBpjsOnline - qtyOutBpjsOnline;
                vMasukNonBpjsOnline := kuotaNonBpjsOnline - qtyOutNonBpjsOnline;
                
                vKeluarBpjsOffline :=  kuotaOutBpjsOffline + qtyOutBpjsOffline;
                vKeluarNonBpjsOffline :=  kuotaOutNonBpjsOffline + qtyOutNonBpjsOffline;
                vKeluarBpjsOnline :=  kuotaOutBpjsOnline + qtyOutBpjsOnline;
                vKeluarNonBpjsOnline :=  kuotaOutNonBpjsOnline + qtyOutNonBpjsOnline;
        END IF;

        IF (vJadwalExist IS NOT NULL) THEN
                IF (NEW.jadwaldokter_id IS NOT NULL) THEN
                        IF (NEW.is_online = false) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasuk,
                                        kuota_tersedia = vMasuk,
                                        kuota_keluar = vKeluar,
                                        kuota_bpjs_offline = vMasukBpjsOffline,
                                        kuota_nonbpjs_offline = vMasukNonBpjsOffline,
                                        kuota_out_bpjs = vKeluarBpjsOffline,
                                        kuota_out_nonbpjs = vKeluarNonBpjsOffline
                                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = false;
                        END IF;

                        IF (NEW.is_online = true) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasukOnline,
                                        kuota_tersedia = vMasukOnline,
                                        kuota_keluar = vKeluarOnline,
                                        kuota_bpjs_online = vMasukBpjsOnline,
                                        kuota_nonbpjs_online = vMasukNonBpjsOnline,
                                        kuota_out_bpjs = vKeluarBpjsOnline,
                                        kuota_out_nonbpjs = vKeluarNonBpjsOnline
                                WHERE jadwaldokter_id = NEW.jadwaldokter_id AND is_online = true;
                        END IF;
                ELSE
                -- poliklinik
                        IF (NEW.is_online = false) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasuk,
                                        kuota_tersedia = vMasuk,
                                        kuota_keluar = vKeluar
                                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = false;
                        END IF;

                        IF (NEW.is_online = true) THEN
                                UPDATE kuotadokter_r
                                SET
                                        kuota_masuk = qtyMasukOnline,
                                        kuota_tersedia = vMasukOnline,
                                        kuota_keluar = vKeluarOnline
                                WHERE jadwalbukapoli_id = NEW.jadwalbukapoli_id AND is_online = true;
                        END IF;
                END IF;

                RETURN NULL;
        END IF;

        IF (NEW.jadwaldokter_id IS NOT NULL) THEN
                IF (NEW.is_online = false) THEN
                        INSERT INTO kuotadokter_r(jadwaldokter_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online,kuota_bpjs_offline,kuota_nonbpjs_offline, kuota_out_bpjs , kuota_out_nonbpjs)
                        VALUES(NEW.jadwaldokter_id,NEW.kuota_in,NEW.kuota_in,0,false,NEW.kuota_bpjs_offline,NEW.kuota_nonbpjs_offline, new.kuota_out_bpjs , new.kuota_out_nonbpjs);
                END IF;

                IF (NEW.is_online = true) THEN
                        INSERT INTO kuotadokter_r(jadwaldokter_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online,kuota_bpjs_online,kuota_nonbpjs_online,  kuota_out_bpjs , kuota_out_nonbpjs)
                        VALUES(NEW.jadwaldokter_id,NEW.kuota_in,NEW.kuota_in,0,true,NEW.kuota_bpjs_online,NEW.kuota_nonbpjs_online, new.kuota_out_bpjs , new.kuota_out_nonbpjs);
                END IF;
        ELSE
        -- kuota poliklinik
                IF (NEW.is_online = false) THEN
                        INSERT INTO kuotadokter_r(jadwalbukapoli_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online)
                        VALUES(NEW.jadwalbukapoli_id,NEW.kuota_in,NEW.kuota_in,0,false);
                END IF;

                IF (NEW.is_online = true) THEN
                        INSERT INTO kuotadokter_r(jadwalbukapoli_id,kuota_masuk,kuota_tersedia,kuota_keluar,is_online)
                        VALUES(NEW.jadwalbukapoli_id,NEW.kuota_in,NEW.kuota_in,0,true);
                END IF;
        END IF;

        RETURN NULL;

        -- IF (NEW.jadwaldokter_id IS NOT NULL AND NEW.jadwaldoktertambahan_id IS NULL) THEN
        -- vNewId = NEW.jadwaldokter_id;
        -- ELSIF (NEW.jadwaldokter_id IS NOT NULL AND NEW.jadwaldoktertambahan_id IS NOT NULL) THEN
        -- vNewId = NEW.jadwaldoktertambahan_id;
        -- ELSIF (NEW.jadwaldokter_id IS NULL AND NEW.kuotaasal_id IS NOT NULL) THEN
        -- vNewId = NEW.kuotaasal_id;
        -- ELSE
        -- RAISE EXCEPTION 'Gagal';
        -- END IF;

        -- SELECT id_dokter,k_masuk,k_keluar
        -- FROM(
        -- SELECT 
        -- CASE    WHEN jadwaldokter_id IS NULL THEN kuotaasal_id
        -- WHEN jadwaldokter_id IS NOT NULL THEN jadwaldokter_id
        -- END AS jid_dokter,
        -- SUM(kuota_in) as k_masuk,
        -- SUM(kuota_out) as k_keluar
        -- INTO 
        -- vIdJ,
        -- vMasuk,
        -- vKeluar
        -- FROM stokkuotadokter_t
        -- WHERE flag IS TRUE AND is_deleted IS FALSE AND is_active IS TRUE
        -- GROUP BY(jid_dokter)
        -- ) t1
        -- WHERE jid_dokter = vNewId;
                        
        -- SELECT jadwaldokter_id
        -- INTO vJadwalExist
        -- FROM kuotadokter_r
        -- WHERE jadwaldokter_id = vNewId;

        -- IF (vJadwalExist IS NOT NULL) THEN
        -- UPDATE kuotadokter_r SET
        -- uota_masuk = vMasuk,
        -- kuota_keluar = vKeluar
        -- WHERE jadwaldokter_id = vNewId;
        -- RETURN NULL;
        -- END IF;
                        
        -- INSERT INTO kuotadokter_r(jadwaldokter_id,kuota_masuk,kuota_keluar)
        -- VALUES(vNewId,vMasuk,vKeluar);
        -- RETURN NULL;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211116_130742_migrate_US1754_mobilejkn_konfigkuota_percarabayar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_130742_migrate_US1754_mobilejkn_konfigkuota_percarabayar cannot be reverted.\n";

        return false;
    }
    */
}
