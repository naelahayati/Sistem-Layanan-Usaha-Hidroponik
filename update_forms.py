import re
import sys

with open("c:/xampp/htdocs/FIELA-TEAM/ALFI/new/Nazfram/resources/views/admin/transaksi_offline.blade.php", "r", encoding="utf8") as f:
    content = f.read()

# --- Produk Form ---
prod_old = """                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Nama Pembeli</label>
                                                <input type="text" name="nama_pembeli" class="form-control form-control-custom" placeholder="Masukkan nama pelanggan" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Metode Pembayaran</label>
                                                <select name="metode_pembayaran" class="form-control form-control-custom" required>
                                                    <option value="tunai" selected>Tunai (Cash)</option>
                                                    <option value="qris">QR (QRIS)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>"""

prod_new = """                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom d-block">Tipe Pembeli</label>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="tipe_online_prod" name="tipe_pembeli_prod" class="custom-control-input" value="online">
                                                    <label class="custom-control-label" for="tipe_online_prod">Punya Akun (Online)</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="tipe_offline_prod" name="tipe_pembeli_prod" class="custom-control-input" value="offline" checked>
                                                    <label class="custom-control-label" for="tipe_offline_prod">Belum Akun (Offline)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Metode Pembayaran</label>
                                                <select name="metode_pembayaran" class="form-control form-control-custom" required>
                                                    <option value="tunai" selected>Tunai (Cash)</option>
                                                    <option value="qris">QR (QRIS)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6" id="wrapper_online_prod" style="display:none;">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Cari Akun Online</label>
                                                <select class="form-control user-search-online" name="user_id_online_prod" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="col-md-6" id="wrapper_offline_prod">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Nama Pembeli Offline</label>
                                                <select class="form-control user-search-offline" name="nama_pembeli_offline_prod" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="col-md-6" id="wrapper_nohp_prod">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">No. WhatsApp (Opsional)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text input-group-text-custom"><i class="fab fa-whatsapp"></i></span>
                                                    </div>
                                                    <input type="text" name="no_hp_prod" id="nohp_prod" class="form-control form-control-custom" style="border-radius: 0 12px 12px 0;" placeholder="Contoh: 08123456789">
                                                </div>
                                            </div>
                                        </div>
                                    </div>"""

content = content.replace(prod_old, prod_new)

# --- Kunjungan Form ---
kun_old = """                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Nama Penanggung Jawab</label>
                                        <input type="text" name="nama_pengunjung" class="form-control form-control-custom" placeholder="Masukkan nama penanggung jawab rombongan" required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">No. WhatsApp</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text input-group-text-custom"><i class="fab fa-whatsapp"></i></span>
                                            </div>
                                            <input type="text" name="no_wa" class="form-control form-control-custom" style="border-radius: 0 12px 12px 0;" placeholder="Contoh: 08123456789" required>
                                        </div>
                                    </div>"""

kun_new = """                                    <div class="form-group mb-4">
                                        <label class="form-label-custom d-block">Tipe Pengunjung</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_online_kun" name="tipe_pembeli_kun" class="custom-control-input" value="online">
                                            <label class="custom-control-label" for="tipe_online_kun">Punya Akun (Online)</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_offline_kun" name="tipe_pembeli_kun" class="custom-control-input" value="offline" checked>
                                            <label class="custom-control-label" for="tipe_offline_kun">Belum Akun (Offline)</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12" id="wrapper_online_kun" style="display:none;">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Cari Akun Online</label>
                                                <select class="form-control user-search-online" name="user_id_online_kun" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="wrapper_offline_kun">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Nama Penanggung Jawab Offline</label>
                                                <select class="form-control user-search-offline" name="nama_pembeli_offline_kun" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="wrapper_nohp_kun">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">No. WhatsApp</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text input-group-text-custom"><i class="fab fa-whatsapp"></i></span>
                                                    </div>
                                                    <input type="text" name="no_wa" id="nohp_kun" class="form-control form-control-custom" style="border-radius: 0 12px 12px 0;" placeholder="Contoh: 08123456789">
                                                </div>
                                            </div>
                                        </div>
                                    </div>"""

content = content.replace(kun_old, kun_new)

with open("c:/xampp/htdocs/FIELA-TEAM/ALFI/new/Nazfram/resources/views/admin/transaksi_offline.blade.php", "w", encoding="utf8") as f:
    f.write(content)
print("Updated HTML UI forms.")
