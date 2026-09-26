#!/bin/bash

# Argumen: sumber dan tujuan
SOURCE_DIR="$1"
TARGET_DIR="$2"

# Validasi argumen
if [ -z "$SOURCE_DIR" ] || [ -z "$TARGET_DIR" ]; then
    echo "Penggunaan: $0 <folder_sumber> <folder_tujuan>"
    exit 1
fi

if [ ! -d "$SOURCE_DIR" ]; then
    echo "Folder sumber tidak ditemukan: $SOURCE_DIR"
    exit 1
fi

# Buat folder tujuan jika belum ada
mkdir -p "$TARGET_DIR"

echo "Menduplikasi folder '$SOURCE_DIR' ke '$TARGET_DIR' sebanyak 100 kali..."

for i in {1..1000}; do
    # Generate nama acak (8 karakter)
    RANDOM_NAME=$(tr -dc 'a-zA-Z0-9' </dev/urandom | head -c 8)
    DEST_DIR="$TARGET_DIR/$RANDOM_NAME"

    # Salin folder
    cp -r "$SOURCE_DIR" "$DEST_DIR"

    echo "[$i] -> $DEST_DIR"
done

echo "Selesai menduplikasi ke: $TARGET_DIR"
