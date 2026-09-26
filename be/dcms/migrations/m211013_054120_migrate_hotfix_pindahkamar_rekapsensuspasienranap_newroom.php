<?php

use yii\db\Migration;

/**
 * Class m211013_054120_migrate_hotfix_pindahkamar_rekapsensuspasienranap_newroom
 */
class m211013_054120_migrate_hotfix_pindahkamar_rekapsensuspasienranap_newroom extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
                
                -- set pasien akhir + 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_pindahan := 1;
                vpasien_akhir := vpasien_awal + 1;
                
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
                
                -- set pasien akhir + 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_pindahan := 1;
                vpasien_akhir := vpasien_awal + 1;
                
                -- update sensus
                UPDATE sensuspasienranap_r SET
                        pasien_pindahan = pasien_pindahan + vpasien_pindahan,
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
        echo "m211013_054120_migrate_hotfix_pindahkamar_rekapsensuspasienranap_newroom cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211013_054120_migrate_hotfix_pindahkamar_rekapsensuspasienranap_newroom cannot be reverted.\n";

        return false;
    }
    */
}
