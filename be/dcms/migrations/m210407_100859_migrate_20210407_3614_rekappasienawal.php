<?php

use yii\db\Migration;

/**
 * Class m210407_100859_migrate_20210407_3614_rekappasienawal
 */
class m210407_100859_migrate_20210407_3614_rekappasienawal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pasienadmisi_rekapsensuspasienranap\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
        vid INTEGER;
        vruangan_id INTEGER;
        vkelaspelayanan_id INTEGER;
    vpasien_awal INTEGER;
    vpasien_masuk INTEGER;
        vpasien_akhir INTEGER;
        vpasien_masuk_last INTEGER;
        vpasien_akhir_last INTEGER;
        
BEGIN
        vruangan_id := NEW.ruangan_id;
    vkelaspelayanan_id := NEW.kelaspelayanan_id;
        
        -- identifikasi untuk data insert/update = vid
        SELECT id INTO vid
        FROM sensuspasienranap_r
        WHERE tgl_sensus::DATE = CURRENT_DATE
        AND ruangan_id = vruangan_id
        AND kelaspelayanan_id = vkelaspelayanan_id;

        -- cek vid untuk insert/update
        IF (vid IS NULL) THEN
                -- cari last pasien akhir
                SELECT pasien_akhir INTO vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE tgl_sensus::DATE < CURRENT_DATE
                AND ruangan_id = vruangan_id
                AND kelaspelayanan_id = vkelaspelayanan_id
                ORDER BY id DESC
                LIMIT 1;
                
                -- cek last pasien akhir jika null isi 0
                IF (vpasien_akhir_last IS NULL) THEN
                        vpasien_akhir_last := 0;
                END IF;
                
                vpasien_awal := vpasien_akhir_last;
                vpasien_masuk := 1;
                vpasien_akhir := vpasien_akhir_last + 1;
                
                INSERT INTO sensuspasienranap_r (       
                        ruangan_id,
                        kelaspelayanan_id,
                        tgl_sensus,
                        pasien_awal,
                        pasien_masuk,
                        pasien_pindahan,
                        pasien_keluarhidup,
                        pasien_keluardipindahkan,
                        pasien_keluarmeninggalkur48,
                        pasien_keluarmeninggalleb48,
                        pasien_akhir
                ) VALUES (
                        vruangan_id,
                        vkelaspelayanan_id,
                        CURRENT_DATE,
                        vpasien_awal,
                        vpasien_masuk ,
                        0 ,
                        0 , 
                        0 ,
                        0 ,
                        0 ,
                        vpasien_akhir
                );
        ELSE
                -- cari last sensus
                SELECT pasien_masuk, pasien_akhir INTO vpasien_masuk_last, vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE tgl_sensus::DATE <= CURRENT_DATE
                AND ruangan_id = vruangan_id
                AND kelaspelayanan_id = vkelaspelayanan_id
                ORDER BY id DESC
                LIMIT 1;
                
                -- set pasien masuk dan pasien akhir + 1
                vpasien_masuk := vpasien_masuk_last + 1;
                vpasien_akhir := vpasien_akhir_last + 1;
                
                -- update sensus
                UPDATE sensuspasienranap_r SET
                        pasien_masuk = vpasien_masuk,
                        pasien_akhir = vpasien_akhir
                WHERE id = vid;
        END IF;
        
        RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"pasienpulang_rekapsensuspasienranap\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
        vid INTEGER;
        vruangan_id INTEGER;
        vkelaspelayanan_id INTEGER;
    vpasien_awal INTEGER;
    vpasien_keluarhidup INTEGER;
        vpasien_keluarmeninggalkur48 INTEGER;
        vpasien_keluarmeninggalleb48 INTEGER;
        vpasien_akhir INTEGER;
        vpasien_akhir_last INTEGER;
        
BEGIN       
        -- cek pasienadmisi_id
        IF (NEW.pasienadmisi_id IS NULL) THEN
                RETURN NEW;
        END IF;
        
        SELECT ruangan_id, kelaspelayanan_id INTO vruangan_id, vkelaspelayanan_id
        FROM pasienadmisi_t
        WHERE pasienadmisi_id = New.pasienadmisi_id;
        
        -- identifikasi untuk data insert/update = vid
        SELECT id INTO vid
        FROM sensuspasienranap_r
        WHERE tgl_sensus::DATE = CURRENT_DATE
        AND ruangan_id = vruangan_id
        AND kelaspelayanan_id = vkelaspelayanan_id;

        -- cek vid untuk insert/update
        IF (vid IS NULL) THEN
                -- cari last pasien akhir
                SELECT pasien_akhir INTO vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE tgl_sensus::DATE < CURRENT_DATE
                AND ruangan_id = vruangan_id
                AND kelaspelayanan_id = vkelaspelayanan_id
                ORDER BY id DESC
                LIMIT 1;
                
                -- cek last pasien akhir jika null isi 0
                IF (vpasien_akhir_last IS NULL) THEN
                        vpasien_akhir_last := 0;
                END IF;
                
                -- cek cara keluar
                IF (NEW.carakeluar_id = 4) THEN
                        IF (NEW.lama_rawat <= 2) THEN --meninggal < 48 Jam
                                vpasien_keluarmeninggalkur48 := 1;
                                vpasien_keluarmeninggalleb48 := 0;
                                vpasien_keluarhidup := 0;
                        ELSE
                                vpasien_keluarmeninggalleb48 := 1;
                                vpasien_keluarmeninggalkur48 := 0;
                                vpasien_keluarhidup := 0;
                        END IF;
                ELSE
                        vpasien_keluarhidup := 1;
                        vpasien_keluarmeninggalleb48 := 0;
                        vpasien_keluarmeninggalkur48 := 0;
                END IF;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_akhir := vpasien_awal - 1;
                
                INSERT INTO sensuspasienranap_r (       
                        ruangan_id,
                        kelaspelayanan_id,
                        tgl_sensus,
                        pasien_awal,
                        pasien_masuk,
                        pasien_pindahan,
                        pasien_keluarhidup,
                        pasien_keluardipindahkan,
                        pasien_keluarmeninggalkur48,
                        pasien_keluarmeninggalleb48,
                        pasien_akhir
                ) VALUES (
                        vruangan_id,
                        vkelaspelayanan_id,
                        CURRENT_DATE,
                        vpasien_awal,
                        0,
                        0,
                        vpasien_keluarhidup,
                        0,
                        vpasien_keluarmeninggalkur48,
                        vpasien_keluarmeninggalleb48,
                        vpasien_akhir
                );
        ELSE
                -- cari last sensus
                SELECT
                    pasien_keluarhidup, pasien_keluarmeninggalkur48, pasien_keluarmeninggalleb48, pasien_akhir INTO
                    vpasien_keluarhidup, vpasien_keluarmeninggalkur48, vpasien_keluarmeninggalleb48, vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE id = vid;
                
                -- cek cara keluar
                IF (NEW.carakeluar_id = 4) THEN
                        IF (NEW.lama_rawat > 2) THEN -- meninggal > 48 Jam
                                vpasien_keluarmeninggalleb48 := vpasien_keluarmeninggalleb48 + 1;
                        ELSE
                                vpasien_keluarmeninggalkur48 := vpasien_keluarmeninggalkur48 + 1;
                        END IF;
                ELSE
                        vpasien_keluarhidup := vpasien_keluarhidup + 1;
                END IF;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_akhir := vpasien_awal - 1;
                
                -- update sensus
                UPDATE sensuspasienranap_r SET
                        pasien_keluarhidup = vpasien_keluarhidup,
                        pasien_keluarmeninggalkur48 = vpasien_keluarmeninggalkur48,
                        pasien_keluarmeninggalleb48 = vpasien_keluarmeninggalleb48,
                        pasien_akhir = vpasien_akhir
                WHERE id = vid;
        END IF;
        
        RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");

        $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pindahkamar_rekapsensuspasienranap_lastroom\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
        vid INTEGER;
        vruangan_id INTEGER;
        vkelaspelayanan_id INTEGER;
    vpasien_awal INTEGER;
        vpasien_keluardipindahkan INTEGER;
        vpasien_akhir INTEGER;
        vpasien_akhir_last INTEGER;
        
BEGIN
        SELECT k.ruangan_id, k.kelaspelayanan_id INTO vruangan_id, vkelaspelayanan_id
        FROM masukkamar_t AS m
        LEFT JOIN pindahkamar_t AS p ON (
            (p.ruangan_id = m.ruangan_id) AND (p.pasienadmisi_id = m.pasienadmisi_id) AND (p.kelaspelayanan_id = m.kelaspelayanan_id)
        )
        LEFT JOIN masukkamar_t AS k ON (k.pindahkamar_id = p.pindahkamar_id)
        WHERE m.pasienadmisi_id = NEW.pasienadmisi_id
        ORDER BY m.masukkamar_id DESC
        LIMIT 1;
        
        -- identifikasi untuk data insert/update = vid
        SELECT id INTO vid
        FROM sensuspasienranap_r
        WHERE tgl_sensus::DATE = CURRENT_DATE
        AND ruangan_id = vruangan_id
        AND kelaspelayanan_id = vkelaspelayanan_id;

        -- cek vid untuk insert/update
        IF (vid IS NULL) THEN
                -- cari last pasien akhir
                SELECT pasien_akhir INTO vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE tgl_sensus::DATE < CURRENT_DATE
                AND ruangan_id = vruangan_id
                AND kelaspelayanan_id = vkelaspelayanan_id
                ORDER BY id DESC
                LIMIT 1;
                
                -- cek last pasien akhir jika null isi 0
                IF (vpasien_akhir_last IS NULL) THEN
                        vpasien_akhir_last := 0;
                END IF;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_keluardipindahkan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                INSERT INTO sensuspasienranap_r (       
                        ruangan_id,
                        kelaspelayanan_id,
                        tgl_sensus,
                        pasien_awal,
                        pasien_masuk,
                        pasien_pindahan,
                        pasien_keluarhidup,
                        pasien_keluardipindahkan,
                        pasien_keluarmeninggalkur48,
                        pasien_keluarmeninggalleb48,
                        pasien_akhir
                ) VALUES (
                        vruangan_id,
                        vkelaspelayanan_id,
                        CURRENT_DATE,
                        vpasien_awal,
                        0,
                        0,
                        0,
                        vpasien_keluardipindahkan,
                        0,
                        0,
                        vpasien_akhir
                );
        ELSE
                -- cari last sensus
                SELECT pasien_keluardipindahkan, pasien_akhir INTO vpasien_keluardipindahkan, vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE id = vid;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_keluardipindahkan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                -- update sensus
                UPDATE sensuspasienranap_r SET
                        pasien_keluardipindahkan = vpasien_keluardipindahkan,
                        pasien_akhir = vpasien_akhir
                WHERE id = vid;
        END IF;
        
        RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");

        $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pindahkamar_rekapsensuspasienranap_newroom\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
        vid INTEGER;
        vruangan_id INTEGER;
        vkelaspelayanan_id INTEGER;
    vpasien_awal INTEGER;
        vpasien_pindahan INTEGER;
        vpasien_akhir INTEGER;
        vpasien_akhir_last INTEGER;
        
BEGIN
        vruangan_id := NEW.ruangan_id;
        vkelaspelayanan_id := NEW.kelaspelayanan_id;
        
        -- identifikasi untuk data insert/update = vid
        SELECT id INTO vid
        FROM sensuspasienranap_r
        WHERE tgl_sensus::DATE = CURRENT_DATE
        AND ruangan_id = vruangan_id
        AND kelaspelayanan_id = vkelaspelayanan_id;

        -- cek vid untuk insert/update
        IF (vid IS NULL) THEN
                -- cari last pasien akhir
                SELECT pasien_akhir INTO vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE tgl_sensus::DATE < CURRENT_DATE
                AND ruangan_id = vruangan_id
                AND kelaspelayanan_id = vkelaspelayanan_id
                ORDER BY id DESC
                LIMIT 1;
                
                -- cek last pasien akhir jika null isi 0
                IF (vpasien_akhir_last IS NULL) THEN
                        vpasien_akhir_last := 0;
                END IF;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_pindahan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                INSERT INTO sensuspasienranap_r (       
                        ruangan_id,
                        kelaspelayanan_id,
                        tgl_sensus,
                        pasien_awal,
                        pasien_masuk,
                        pasien_pindahan,
                        pasien_keluarhidup,
                        pasien_keluardipindahkan,
                        pasien_keluarmeninggalkur48,
                        pasien_keluarmeninggalleb48,
                        pasien_akhir
                ) VALUES (
                        vruangan_id,
                        vkelaspelayanan_id,
                        CURRENT_DATE,
                        vpasien_awal,
                        0,
                        vpasien_pindahan,
                        0,
                        0,
                        0,
                        0,
                        vpasien_akhir
                );
        ELSE
                -- cari last sensus
                SELECT pasien_pindahan, pasien_akhir INTO vpasien_pindahan, vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE id = vid;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_pindahan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                -- update sensus
                UPDATE sensuspasienranap_r SET
                        pasien_pindahan = vpasien_pindahan,
                        pasien_akhir = vpasien_akhir
                WHERE id = vid;
        END IF;
        
        RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_100859_migrate_20210407_3614_rekappasienawal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_100859_migrate_20210407_3614_rekappasienawal cannot be reverted.\n";

        return false;
    }
    */
}
