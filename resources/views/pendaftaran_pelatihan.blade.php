@extends('master')

@section('konten')
    <!-- Import CSS yang sama dengan halaman lain -->
    <!-- FullCalendar Dependencies -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/stylepelatihan.css') }}?v={{ time() }}">
    <!-- Konten Form Pendaftaran -->
    <div class="pendaftaran-page-wrapper pelatihan-page">
        <header class="pelatihan-header-form page-header-sub">
            <h1>PENDAFTARAN</h1>
        </header>

        <div class="action-bar-pendaftaran">
            <a href="{{ route('nazfram.pelatihan') }}" class="btn-back-modern">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="pendaftaran-form-frame">
            <div class="pendaftaran-card-modern">
                <h2 class="form-title-modern">{{ $magang->name }}</h2>

                {{-- Notifikasi Global --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('nazfram.pelatihan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_pelatihan" value="{{ $id_pelatihan }}">

                    <div class="form-group-modern">
                        <label>Pilih Rencana Tanggal Mulai Magang</label>
                        <p
                            style="color: #555; font-size: 0.85rem; background: #fff8e1; padding: 10px 15px; border-left: 4px solid #ffc107; border-radius: 4px; margin-bottom: 15px; line-height: 1.6;">
                            Jadwal hanya dapat dipilih minimal H+3 dari tanggal pemesanan.
                        </p>

                        {{-- Tombol trigger khusus mobile --}}
                        <button type="button" class="cal-trigger-btn-pelatihan" id="btn-open-cal-pelatihan">
                            <i class="fas fa-calendar-alt"></i>
                            <span id="cal-trigger-label-pelatihan">Ketuk untuk pilih tanggal</span>
                        </button>

                        {{-- Kalender desktop --}}
                        <div class="cal-desktop-pelatihan">
                            <div id="calendar-pendaftaran"></div>
                        </div>

                        <!-- Input hidden untuk mengirim data ke controller -->
                        <input type="hidden" name="tanggal_magang" id="tanggal_magang_input"
                            value="{{ old('tanggal_magang') }}" required>

                        @error('tanggal_magang')
                            <div class="invalid-feedback"
                                style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}
                            </div>
                        @enderror
                    </div>

                    @php
                        $oldDurasi = old('jumlah_peserta');
                        $isCustom = $oldDurasi && !in_array($oldDurasi, [1, 2, 3, 4, 5, 6]);
                    @endphp
                    <div class="form-group-modern">
                        <label>Durasi (Bulan)</label>
                        <select id="select_durasi" class="@error('jumlah_peserta') is-invalid @enderror"
                            onchange="toggleCustomDurasi(this.value)" required
                            style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #ced4da; font-size: 1rem; color: #495057;">
                            <option value="" disabled {{ !$oldDurasi ? 'selected' : '' }}>Pilih durasi magang (Bulan)
                            </option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ $oldDurasi == $i ? 'selected' : '' }}>{{ $i }} Bulan</option>
                            @endfor
                            <option value="other" {{ $isCustom ? 'selected' : '' }}>Lainnya (Lebih dari 6 bulan)</option>
                        </select>

                        <input type="number" name="jumlah_peserta" id="jumlah_peserta"
                            class="@error('jumlah_peserta') is-invalid @enderror"
                            placeholder="Ketik jumlah bulan (misal: 7)" min="1"
                            style="display: {{ $isCustom ? 'block' : 'none' }}; margin-top: 10px; width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #ced4da; font-size: 1rem;"
                            value="{{ $oldDurasi }}">

                        @error('jumlah_peserta')
                            <div class="invalid-feedback"
                                style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label>Nama Instansi / Kelompok (Asal Sekolah/Kampus)</label>
                        <input type="text" name="instansi" class="@error('instansi') is-invalid @enderror"
                            value="{{ old('instansi') }}" placeholder="Contoh: SMKN 1 Bogor atau Universitas Indonesia" required>
                        @error('instansi')
                            <div class="invalid-feedback"
                                style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Fitur Daftar Kolektif (Khusus PKL) --}}
                    @php
                        $isPKL = str_contains(strtolower($magang->name), 'pkl');
                    @endphp

                    @if($isPKL)
                    <div class="collective-section">
                        <button type="button" class="collective-btn-toggle" id="btn-toggle-collective">
                            <i class="fas fa-users"></i> Daftar untuk Teman / Kolektif?
                        </button>

                        <div id="collective-content" style="display: none;">
                            <div class="collective-options">
                                <label class="collective-radio active" id="label-opsi-ikut">
                                    <input type="radio" name="is_pendaftar_ikut" value="1" checked>
                                    <i class="fas fa-user-check"></i>
                                    <span>Saya ikut magang dan mewakili teman-teman</span>
                                </label>
                                <label class="collective-radio" id="label-opsi-wakil">
                                    <input type="radio" name="is_pendaftar_ikut" value="0">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Saya hanya perwakilan (Saya tidak ikut magang)</span>
                                </label>
                            </div>

                            <label class="collective-list-label">
                                Daftar Nama Siswa/Mahasiswa yang Mengikuti PKL:
                            </label>
                            
                            <div id="collective-list-names" class="collective-list-container">
                                {{-- Textbox baru akan muncul di sini --}}
                                <div class="collective-input-wrapper">
                                    <input type="text" name="list_nama_peserta[]" placeholder="Masukkan nama lengkap siswa/mahasiswa">
                                    <button type="button" class="btn-remove-name" onclick="removeNameRow(this)" style="display:none;"><i class="fas fa-times"></i></button>
                                </div>
                            </div>

                            <button type="button" class="btn-add-name" id="btn-add-name">
                                <i class="fas fa-plus-circle"></i> Tambah Nama Lainnya
                            </button>
                        </div>
                    </div>
                    @endif

                    @php
                        $isPKL = str_contains(strtolower($magang->name), 'pkl');
                    @endphp

                    {{-- Bagian Deskripsi Kemampuan --}}
                    @if($magang->show_skill_description)
                        <div class="form-group-modern">
                            <label>Deskripsi Kemampuan</label>
                            <textarea name="deskripsi_kemampuan" class="@error('deskripsi_kemampuan') is-invalid @enderror"
                                placeholder="Ceritakan kemampuan atau pengalaman dasar Anda di bidang hidroponik/pertanian, contoh: Paham sistem NFT/DFT, pernah tanam selada & bayam hidroponik, bisa racik nutrisi AB Mix dan cek pH."
                                rows="4" required>{{ old('deskripsi_kemampuan') }}</textarea>
                            @error('deskripsi_kemampuan')
                                <div class="invalid-feedback"
                                    style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif

                    {{-- Total & Metode Pembayaran --}}
                    <div class="total-container"
                        style="background: #fcfdfc; border: 1px dashed #1b3a1a; border-radius: 15px; padding: 15px 20px; margin: 20px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; color: #1b3a1a; font-size: 1rem;">Total Pembayaran:</span>
                            <span id="total_bayar_text" style="font-size: 1.5rem; font-weight: 800; color: #1b3a1a;">Rp
                                0</span>
                        </div>
                        <input type="hidden" name="total_harga" id="total_harga_input">
                    </div>

                    {{-- Section Logika Dinamis --}}
                    @php
                        $isFree = $magang->price == 0;
                        $needsConfirm = $magang->is_wa_confirmation == 1;
                    @endphp

                    {{-- 1. Skenario GRATIS --}}
                    @if($isFree)
                    <div id="section-gratis" style="margin-top: 15px;">
                        <div class="alert alert-info" style="border-radius: 10px; padding: 12px 15px; background: #f0f7ff; color: #0056b3; border: 1px solid #cce5ff; font-size: 0.9rem;">
                            <i class="fas fa-info-circle mr-2"></i> Program magang ini <strong>Gratis</strong> (tidak dipungut biaya).
                        </div>
                        <input type="hidden" name="metode_pembayaran_gratis" value="gratis">
                    </div>
                    @endif

                    {{-- 2. Skenario BERBAYAR + KONFIRMASI WA --}}
                    @if(!$isFree && $needsConfirm)
                    @php
                        $adminPhone = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_admin', '6282240867746'));
                        if (str_starts_with($adminPhone, '0')) $adminPhone = '62' . substr($adminPhone, 1);
                        $pesanWA = "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\nHalo Admin Naz Hidrofarm,\n\nSaya ingin mendaftar program magang:\n\nNama    : " . auth()->user()->name . "\nProgram : " . $magang->name . "\n\nMohon kiranya pendaftaran saya dapat segera diproses.\n\nTerima kasih.";
                        $waLink = "https://wa.me/" . $adminPhone . "?text=" . urlencode($pesanWA);
                    @endphp
                    <div id="section-konfirmasi-admin" style="margin-top: 15px;">
                        <div class="alert alert-warning" style="border-radius: 10px; padding: 12px 15px; background: #fffde7; color: #856404; border: 1px solid #ffeeba; font-size: 0.9rem;">
                            <i class="fab fa-whatsapp mr-2"></i> Setelah klik <strong>Daftar Sekarang</strong>, Anda akan langsung diarahkan ke WhatsApp Admin untuk konfirmasi pendaftaran.
                        </div>
                        <input type="hidden" name="metode_pembayaran" value="tunai">
                        <input type="hidden" name="wa_link" value="{{ $waLink }}">
                    </div>
                    @endif

                    {{-- 3. Skenario BERBAYAR + LANGSUNG BAYAR (NON-KONFIRMASI) --}}
                    @if(!$isFree && !$needsConfirm)
                    <div id="section-bayar-langsung" class="pesanan-section" style="margin-top: 15px;">
                        <h3 class="section-title" style="font-size: 1rem; margin-bottom: 15px;"><i class="fas fa-wallet"></i> Metode Pembayaran</h3>
                        <div style="padding: 15px; background: #fff8e1; border-radius: 10px; border-left: 4px solid #ffc107; font-size: 0.9rem; color: #856404;">
                            <i class="fas fa-info-circle mr-1"></i> Pembayaran dapat dilakukan via <strong>Transfer Bank</strong> atau <strong>QRIS</strong> pada halaman selanjutnya.
                        </div>
                        <input type="hidden" name="metode_pembayaran" value="qris">
                    </div>
                    @endif

                    <div class="form-actions-modern" style="margin-top: 40px;">
                        <button type="submit" id="btn-submit-pendaftaran" class="btn-daftar-modern"
                            style="width: 100%; padding: 18px; border-radius: 15px; background: linear-gradient(135deg, #2d5a27 0%, #1b3a1a 100%); color: white; border: none; font-weight: 700; font-size: 1.2rem; cursor: pointer; transition: all 0.3s; box-shadow: 0 6px 20px rgba(27, 58, 26, 0.25);">
                            <i class="fas fa-paper-plane mr-2"></i> Daftar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Kalender Mobile --}}
    <div class="cal-modal-overlay-pelatihan" id="cal-modal-overlay-pelatihan">
        <div class="cal-modal-box-pelatihan">
            <div class="cal-modal-header-pelatihan">
                <span><i class="fas fa-calendar-alt" style="margin-right:6px;color:#1b3a1a;"></i> Pilih Tanggal Mulai Magang</span>
                <button type="button" class="cal-modal-close-pelatihan" id="btn-close-cal-pelatihan">&times;</button>
            </div>
            <div id="calendar-pendaftaran-modal"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hargaDasar = {{ $magang->price }};
            const inputPeserta = document.getElementById('jumlah_peserta');
            const textTotal = document.getElementById('total_bayar_text');
            const inputHiddenTotal = document.getElementById('total_harga_input');
            const inputTanggal = document.getElementById('tanggal_magang_input');
            const triggerBtn   = document.getElementById('btn-open-cal-pelatihan');
            const triggerLabel = document.getElementById('cal-trigger-label-pelatihan');
            const overlay      = document.getElementById('cal-modal-overlay-pelatihan');
            const btnClose     = document.getElementById('btn-close-cal-pelatihan');

            // ── Validasi tanggal ─────────────────────────────────────
            // Blokir: Sabtu/Minggu, sebelum H+3, dan hari libur dari admin (background events)
            function isDateAllowed(calInstance, start) {
                const day = start.day();
                if (day === 0 || day === 6) return false;
                const minDate = moment().add(3, 'days').startOf('day');
                if (moment(start).isBefore(minDate)) return false;
                if (!calInstance) return true;
                const dateStr = start.format('YYYY-MM-DD');
                return !calInstance.getEvents().some(event => {
                    if (event.display !== 'background') return false;
                    const evStart = moment(event.start).format('YYYY-MM-DD');
                    const evEnd   = event.end
                        ? moment(event.end).format('YYYY-MM-DD')
                        : moment(event.start).add(1, 'days').format('YYYY-MM-DD');
                    return moment(dateStr).isSameOrAfter(evStart) && moment(dateStr).isBefore(evEnd);
                });
            }

            // ── Setelah tanggal dipilih ──────────────────────────────
            function onDatePicked(dateStr) {
                inputTanggal.value = dateStr;
                const formatted = moment(dateStr).locale('id').format('dddd, D MMMM YYYY');
                if (triggerLabel) triggerLabel.textContent = formatted;
                if (typeof hitungTotal === 'function') hitungTotal();
                tutupModal();
            }

            // ── Konfigurasi kalender ─────────────────────────────────
            function buatKalender(elId, ref) {
                const cal = new FullCalendar.Calendar(document.getElementById(elId), {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    firstDay: 0,
                    selectable: true,
                    unselectAuto: false,
                    selectLongPressDelay: 0,
                    longPressDelay: 0,
                    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                    height: 'auto',
                    contentHeight: 'auto',
                    buttonText: { today: 'Hari ini' },
                    events: "{{ route('nazfram.pelatihan.events') }}?type=magang",
                    selectAllow: info => isDateAllowed(ref.cal, moment(info.start)),
                    select:    info => onDatePicked(moment(info.start).format('YYYY-MM-DD')),
                    dateClick: info => {
                        if (isDateAllowed(ref.cal, moment(info.date))) onDatePicked(moment(info.date).format('YYYY-MM-DD'));
                    }
                });
                ref.cal = cal;
                return cal;
            }

            // ── Kalender Desktop ─────────────────────────────────────
            const desktopRef = { cal: null };
            const calDesktopEl = document.getElementById('calendar-pendaftaran');
            if (calDesktopEl) {
                buatKalender('calendar-pendaftaran', desktopRef).render();
            }

            // ── Kalender Modal (mobile, lazy init) ──────────────────
            const modalRef = { cal: null };
            function bukaModal() {
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                if (!modalRef.cal) {
                    buatKalender('calendar-pendaftaran-modal', modalRef).render();
                }
            }
            function tutupModal() {
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (triggerBtn) triggerBtn.addEventListener('click', bukaModal);
            if (btnClose)   btnClose.addEventListener('click', tutupModal);
            if (overlay)    overlay.addEventListener('click', e => { if (e.target === overlay) tutupModal(); });

            // Restore label jika ada old value
            if (inputTanggal && inputTanggal.value && triggerLabel) {
                triggerLabel.textContent = moment(inputTanggal.value).locale('id').format('dddd, D MMMM YYYY');
            }

            // ── Logika Kolektif (PKL) ───────────────────────────────
            const btnToggleColl = document.getElementById('btn-toggle-collective');
            const collContent   = document.getElementById('collective-content');
            const listNames     = document.getElementById('collective-list-names');
            const btnAddName    = document.getElementById('btn-add-name');
            const radiosParticipate = document.querySelectorAll('input[name="is_pendaftar_ikut"]');

            if (btnToggleColl) {
                btnToggleColl.addEventListener('click', function() {
                    this.classList.toggle('active');
                    if (collContent.style.display === 'none') {
                        collContent.style.display = 'block';
                        this.innerHTML = '<i class="fas fa-times-circle"></i> Batalkan Daftar Kolektif';
                    } else {
                        collContent.style.display = 'none';
                        this.innerHTML = '<i class="fas fa-users"></i> Daftar untuk Teman / Kolektif?';
                        // Reset list if needed or keep it hidden
                    }
                });
            }

            if (btnAddName) {
                btnAddName.addEventListener('click', function() {
                    // Validasi: Cek apakah input terakhir sudah diisi
                    const allInputsLabels = listNames.querySelectorAll('input[name="list_nama_peserta[]"]');
                    const lastInput = allInputsLabels[allInputsLabels.length - 1];
                    
                    if (lastInput && lastInput.value.trim() === "") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Input Kosong',
                            text: 'Silakan isi nama siswa/mahasiswa sebelumnya terlebih dahulu sebelum menambah nama baru.',
                            confirmButtonColor: '#2d5a27'
                        });
                        lastInput.focus();
                        return;
                    }

                    const row = document.createElement('div');
                    row.className = 'collective-input-wrapper';
                    row.innerHTML = `
                        <input type="text" name="list_nama_peserta[]" placeholder="Masukkan nama lengkap siswa/mahasiswa">
                        <button type="button" class="btn-remove-name" onclick="removeNameRow(this)"><i class="fas fa-times"></i></button>
                    `;
                    listNames.appendChild(row);
                    updateRemoveButtons();
                    
                    // Focus ke input baru
                    row.querySelector('input').focus();
                });
            }

            window.removeNameRow = function(btn) {
                btn.closest('.collective-input-wrapper').remove();
                updateRemoveButtons();
            }

            function updateRemoveButtons() {
                const rows = listNames.querySelectorAll('.collective-input-wrapper');
                rows.forEach((row, index) => {
                    const btn = row.querySelector('.btn-remove-name');
                    if (rows.length > 1) {
                        btn.style.display = 'flex';
                    } else {
                        // Alih-alih display:none, kita bisa biarkan dia tetap ada tapi tidak terlihat 
                        // agar lebar input tetap konsisten, atau kita pastikan input tetap flex:1
                        btn.style.display = 'none';
                    }
                });
            }

            radiosParticipate.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.collective-radio').forEach(lbl => lbl.classList.remove('active'));
                    this.closest('.collective-radio').classList.add('active');
                });
            });

            window.hitungTotal = function() {
                let jumlah = parseInt(inputPeserta ? inputPeserta.value : 0) || 0;
                let total = jumlah * hargaDasar;
                
                // Jika ingin mendukung harga per orang di masa depan, logika perkalian bisa ditambah di sini.
                // Untuk sekarang PKL berharga Rp 0, jadi total tetap 0.

                if (textTotal) {
                    textTotal.textContent = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(total);
                }
                if (inputHiddenTotal) inputHiddenTotal.value = total;
            }

            window.toggleCustomDurasi = function (val) {
                const customInput = document.getElementById('jumlah_peserta');
                if (val === 'other') {
                    customInput.style.display = 'block';
                    customInput.value = '';
                    customInput.required = true;
                    customInput.focus();
                    hitungTotal();
                } else {
                    customInput.style.display = 'none';
                    customInput.value = val;
                    customInput.required = false;
                    hitungTotal();
                }
            }

            if (inputPeserta) {
                inputPeserta.addEventListener('input', hitungTotal);
                inputPeserta.addEventListener('change', hitungTotal);
            }

            // Jalankan saat pertama kali
            hitungTotal();
        });
    </script>
@endsection
