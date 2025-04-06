import os
import subprocess
import time
import json
import requests

def get_domain_list(whm_username, whm_api_token, whm_host):
    """Mendapatkan daftar domain dari WHM menggunakan API"""
    try:
        api_url = f"https://{whm_host}:2087/json-api/listaccts?api.version=1"
        headers = {"Authorization": f"whm {whm_username}:{whm_api_token}"}
        response = requests.get(api_url, headers=headers, verify=False)
        response.raise_for_status()
        data = response.json()
        domains = [account['domain'] for account in data['data']['acct']]
        return domains
    except requests.exceptions.RequestException as e:
        print(f"Gagal mengambil daftar domain: {str(e)}")
        return []
    except Exception as e:
        print(f"Error saat memproses daftar domain: {str(e)}")
        return []

def ensure_target_path(domain, target_path=None):
    """Memastikan target_path ada, jika tidak buat otomatis"""
    base_path = f"/home/{domain}/public_html"
    
    if not target_path:
        target_path = "uploads/tmp/samplefile.zip"
    
    full_target_path = os.path.join(base_path, target_path)
    target_dir = os.path.dirname(full_target_path)
    
    try:
        if not os.path.exists(target_dir):
            subprocess.run(f"mkdir -p {target_dir}", shell=True, check=True)
            print(f"Membuat direktori: {target_dir}")
        
        return full_target_path
    except subprocess.CalledProcessError as e:
        print(f"Gagal membuat direktori {target_dir}: {str(e)}")
        return None

def save_to_done_file(domain, full_target_path):
    """Menyimpan domain dan path ke file done.txt"""
    try:
        with open("done.txt", "a") as f:
            f.write(f"{domain} - {full_target_path}\n")
    except Exception as e:
        print(f"Gagal menyimpan ke done.txt: {str(e)}")

def upload_file_to_domain(domain, file_url, target_path=None):
    """Mengunggah file ke domain menggunakan wget"""
    try:
        full_target_path = ensure_target_path(domain, target_path)
        if not full_target_path:
            return False
        
        wget_command = f"wget -O {full_target_path} {file_url}"
        result = subprocess.run(wget_command, shell=True, capture_output=True, text=True)
        
        if result.returncode == 0:
            print(f"Berhasil mengunggah file ke {domain} di {full_target_path}")
            save_to_done_file(domain, full_target_path)  # Simpan ke done.txt
            return True
        else:
            print(f"Gagal mengunggah ke {domain}: {result.stderr}")
            return False
            
    except Exception as e:
        print(f"Error pada {domain}: {str(e)}")
        return False

def main():
    # Konfigurasi WHM API
    whm_username = "your_whm_username"    # Ganti dengan username WHM Anda
    whm_api_token = "your_api_token"      # Ganti dengan API token WHM Anda
    whm_host = "your_server_hostname"     # Ganti dengan hostname server Anda
    
    # Konfigurasi upload
    file_url = "http://example.com/samplefile.zip"  # URL file yang akan di-download
    target_path = None                             # Biarkan None untuk path otomatis
    delay_seconds = 5                              # Delay antar upload
    
    # Dapatkan daftar domain dari WHM
    domains = get_domain_list(whm_username, whm_api_token, whm_host)
    
    if not domains:
        print("Tidak ada domain yang ditemukan atau gagal mengambil daftar domain.")
        return
    
    print(f"Total domain yang akan diupload: {len(domains)}")
    
    # Pastikan file done.txt kosong di awal (opsional)
    if os.path.exists("done.txt"):
        os.remove("done.txt")
    
    # Proses upload untuk setiap domain
    success_count = 0
    for domain in domains:
        print(f"\nMemproses {domain}...")
        
        if upload_file_to_domain(domain, file_url, target_path):
            success_count += 1
        
        time.sleep(delay_seconds)
    
    # Ringkasan
    print("\n=== Ringkasan ===")
    print(f"Total domain: {len(domains)}")
    print(f"Berhasil: {success_count}")
    print(f"Gagal: {len(domains) - success_count}")
    print("Hasil upload tersimpan di done.txt")

if __name__ == "__main__":
    main()
