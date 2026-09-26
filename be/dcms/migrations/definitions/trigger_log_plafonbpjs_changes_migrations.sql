CREATE OR REPLACE FUNCTION public.log_plafonbpjs_changes()
RETURNS trigger AS $$
DECLARE
    v_action TEXT;
    v_old_data JSONB := NULL;
    v_new_data JSONB := NULL;
    v_row RECORD;
BEGIN
    -- Tentukan aksi & data
    IF (TG_OP = 'INSERT') THEN
        v_action := 'INSERT';
        v_new_data := to_jsonb(NEW);
        v_row := NEW;

    ELSIF (TG_OP = 'UPDATE') THEN
        v_action := CASE
                      WHEN NEW.is_deleted = true AND OLD.is_deleted = false THEN 'DELETE'
                      ELSE 'UPDATE'
                   END;
        v_old_data := to_jsonb(OLD);
        v_new_data := to_jsonb(NEW);
        v_row := NEW;

    ELSIF (TG_OP = 'DELETE') THEN
        v_action := 'DELETE';
        v_old_data := to_jsonb(OLD);
        v_row := OLD;
    END IF;

    -- Masukkan ke histori
    INSERT INTO historiplafonbpjs_r (
        plafonbpjs_id,
        action,
        old_data,
        new_data,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by
    ) VALUES (
        v_row.plafonbpjs_id,
        v_action,
        v_old_data,
        v_new_data,
        v_row.additional_data,
        NOW(),
        v_row.created_by,
        v_row.modified_count,
        v_row.last_modified_date,
        v_row.last_modified_by,
        v_row.is_deleted,
        v_row.is_active,
        v_row.deleted_date,
        v_row.deleted_by
    );

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;