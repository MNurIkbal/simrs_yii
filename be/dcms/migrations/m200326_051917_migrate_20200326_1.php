<?php

use yii\db\Migration;

/**
 * Class m200326_051917_migrate_20200326_1
 */
class m200326_051917_migrate_20200326_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER if exists trigger_update_kamartempattidur_m ON public.kamartempattidur_m;');

        $this->execute('DROP FUNCTION if exists public.kamartempattidur_m_update();');

        $this->execute("
            CREATE FUNCTION public.kamartempattidur_m_update()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS \$BODY\$
-- @created by ikbal 15 Februari 2019

DECLARE
    vtgl_tthistory TIMESTAMP;
    vruangan_id int4;
    vkamarruangan_id int4;
    vkamartempattidur_id int4;
    vno_tempattidur VARCHAR;
    vketerangan VARCHAR;
    vcreated_by int4;

BEGIN
  
    vtgl_tthistory := CURRENT_TIMESTAMP;
    vkamarruangan_id := NEW.kamarruangan_id;
    vkamartempattidur_id := NEW.kamartempattidur_id;
    vno_tempattidur := NEW.no_tempattidur;
    vcreated_by := NEW.created_by;
    
    
    SELECT  ruangan_id
    INTO        vruangan_id
    FROM kamarruangan_m
    WHERE kamarruangan_id = vkamarruangan_id;
    
    IF (NEW.is_deleted <> OLD.is_deleted)
    THEN
        IF (NEW.is_deleted = TRUE)
        THEN
            -- vketerangan := 'Penghapusan';
            vketerangan := '614';
            
            INSERT INTO kamartempattidur_r(
                            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
                            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
                            last_modified_by)
            VALUES (vtgl_tthistory, vruangan_id, vkamarruangan_id, vkamartempattidur_id, 
                            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                            vcreated_by);
        END IF;
    ELSEIF(NEW.is_active <> OLD.is_active)
    THEN
        IF (NEW.is_active = FALSE)
        THEN
            --vketerangan := 'Pengnon-Aktifan';
            vketerangan := '616';
        ELSE
            vketerangan := '615';
            --vketerangan := 'Peng-Aktifan';
        END IF;
        
        INSERT INTO kamartempattidur_r(
            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
            last_modified_by)
    VALUES (vtgl_tthistory, vruangan_id, vkamarruangan_id, vkamartempattidur_id, 
            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                        vcreated_by);
    ELSEIF(NEW.kamarruangan_id <> OLD.kamarruangan_id)
    THEN
        vketerangan := '618';
        
        SELECT  ruangan_id
        INTO        vruangan_id
        FROM kamarruangan_m
        WHERE kamarruangan_id = OLD.kamarruangan_id;
        
        INSERT INTO kamartempattidur_r(
            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
            last_modified_by)
    VALUES (vtgl_tthistory, vruangan_id, OLD.kamarruangan_id, vkamartempattidur_id, 
            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                        vcreated_by);
        
        vketerangan := '617';
        
        SELECT  ruangan_id
        INTO        vruangan_id
        FROM kamarruangan_m
        WHERE kamarruangan_id = NEW.kamarruangan_id;
        
        INSERT INTO kamartempattidur_r(
            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
            last_modified_by)
    VALUES (vtgl_tthistory, vruangan_id, NEW.kamarruangan_id, vkamartempattidur_id, 
            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                        vcreated_by);
    END IF;
    
RETURN NEW;

END;\$BODY\$;");

        $this->execute('ALTER FUNCTION public.kamartempattidur_m_update()
    OWNER TO postgres;');

        $this->execute('CREATE TRIGGER "trigger_update_kamartempattidur_m" AFTER UPDATE OF "kamarruangan_id", "is_deleted", "is_active" ON "public"."kamartempattidur_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."kamartempattidur_m_update"();');

        $this->execute('DROP TRIGGER if exists "tigger_update_pesanbarangdetail_t" ON "public"."pesanbarangdetail_t";');

        $this->execute('DROP FUNCTION if exists public.pesanbarangdetail_t_update();');

        $this->execute("
            CREATE FUNCTION public.pesanbarangdetail_t_update()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS \$BODY\$
DECLARE
vpesanbarangdetail_id int4;
vpesanbarang_id int4;
vbarang_id_new int4;
vbarang_id_old int4;
vqty_pesan_new int4;
vqty_pesan_old int4;
vqty_pesan_sisa int4;
vruangan_id int4;
vcreated_by int4;

BEGIN
    vpesanbarangdetail_id := NEW.pesanbarangdetail_id;
    vpesanbarang_id := NEW.pesanbarang_id;
    vbarang_id_new := NEW.barang_id;
    vbarang_id_old := OLD.barang_id;
    vqty_pesan_new := NEW.qty_pesan;
    vqty_pesan_old := OLD.qty_pesan;
    vcreated_by := NEW.created_by;
    
    SELECT 
        pesanbarang_t.ruangantujuan_id
    INTO
        vruangan_id
    FROM
        pesanbarang_t
    WHERE pesanbarang_t.pesanbarang_id = vpesanbarang_id;
    
    IF vbarang_id_new != vbarang_id_old
    THEN
        IF EXISTS(
            SELECT *
            FROM stokbarang_r
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id
            LIMIT 1
        )
        THEN
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan + vqty_pesan_new,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id;
            
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan - vqty_pesan_old,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_old
            AND ruangan_id = vruangan_id;
            
        ELSE
            UPDATE stokbarang_r 
            SET qty_dipesan = qty_dipesan - vqty_pesan_old
            WHERE ruangan_id = vruangan_id
            AND barang_id = vbarang_id_old;
            
            INSERT INTO stokbarang_r (
                ruangan_id, barang_id, qty_awal, qty_masuk, qty_keluar, qty_sisa, qty_tersedia,
                qty_dipesan, created_date, created_by, is_deleted, is_active
            )VALUES(
                vruangan_id, vbarang_id_new, 0, 0, 0, 0, 0, 
                vqty_pesan_new, CURRENT_TIMESTAMP, vcreated_by, 'f', 't'
            );
        END IF;
    ELSE
        IF vqty_pesan_new >= vqty_pesan_old
        THEN
            vqty_pesan_sisa := vqty_pesan_new - vqty_pesan_old;
            
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan + vqty_pesan_sisa,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id;
        
        ELSIF vqty_pesan_new <= vqty_pesan_old
        THEN
            vqty_pesan_sisa :=  vqty_pesan_old - vqty_pesan_new;
            
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan - vqty_pesan_sisa,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id;
        ELSE
            RETURN NEW;
        END IF;
    END IF;
    
    RETURN NEW;
END;
\$BODY\$;");
        
        $this->execute('ALTER FUNCTION public.pesanbarangdetail_t_update()
    OWNER TO postgres;');
        
        $this->execute('CREATE TRIGGER "tigger_update_pesanbarangdetail_t" AFTER UPDATE OF "barang_id", "qty_pesan" ON "public"."pesanbarangdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pesanbarangdetail_t_update"();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200326_051917_migrate_20200326_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200326_051917_migrate_20200326_1 cannot be reverted.\n";

        return false;
    }
    */
}
