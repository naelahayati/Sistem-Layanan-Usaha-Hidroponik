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
    <div class="pendaftaran-page-wrapper">
        <header class="pelatihan-header-form">
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
                        <div id="calendar-pendaftaran"></div>
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
                        <label>Nama Instansi / Kelompok</label>
                        <input type="text" name="instansi" class="@error('instansi') is-invalid @enderror"
                            value="{{ old('instansi') }}" required>
                        @error('instansi')
                            <div class="invalid-feedback"
                                style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}
                            </div>
                        @enderror
                    </div>

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
                    <div id="section-konfirmasi-admin" style="margin-top: 15px;">
                        <div class="alert alert-warning" style="border-radius: 10px; padding: 12px 15px; background: #fffde7; color: #856404; border: 1px solid #ffeeba; font-size: 0.9rem;">
                            <i class="fas fa-clock mr-2"></i> Pembayaran dapat dilakukan setelah Admin mengonfirmasi pendaftaran Anda.
                        </div>
                        <input type="hidden" name="metode_pembayaran" value="tunai">
                    </div>
                    @endif

                    {{-- 3. Skenario BERBAYAR + LANGSUNG BAYAR (NON-KONFIRMASI) --}}
                    @if(!$isFree && !$needsConfirm)
                    <div id="section-bayar-langsung" class="pesanan-section" style="margin-top: 15px;">
                        <h3 class="section-title" style="font-size: 1rem; margin-bottom: 15px;"><i class="fas fa-wallet"></i> Metode Pembayaran</h3>
                        <label class="radio-card active" style="display: flex; align-items: center; padding: 15px; border: 1px solid #2d5a27; border-radius: 12px; background: #fcfdfc; cursor: pointer;">
                            <input type="radio" name="metode_pembayaran" value="qris" checked style="margin-right: 12px; width: 18px; height: 18px;">
                            <div>
                                <strong style="display: block; color: #1b3a1a; font-size: 0.95rem;">QRIS</strong>
                                <div style="font-size: 0.85rem; color: #666;">Bayar instan via e-Wallet/M-Banking.</div>
                            </div>
                        </label>
                        <div id="qris-info" style="margin-top: 12px; font-size: 0.85rem; color: #555;">
                            <i class="fas fa-info-circle mr-1" style="color: #2d5a27;"></i> Anda akan diarahkan ke halaman pembayaran QRIS setelah klik daftar.
                        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hargaDasar = {{ $magang->price }};
            const inputPeserta = document.getElementById('jumlah_peserta');
            const textTotal = document.getElementById('total_bayar_text');
            const inputHiddenTotal = document.getElementById('total_harga_input');
            const calendarEl = document.getElementById('calendar-pendaftaran');
            const inputTanggal = document.getElementById('tanggal_magang_input');

            // Inisialisasi Kalender
            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    firstDay: 0,
                    selectable: true,
                    unselectAuto: false,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    events: "{{ route('nazfram.pelatihan.events') }}?type=magang",
                    selectAllow: function (selectInfo) {
                        const day = moment(selectInfo.start).day();
                        if (day === 0 || day === 6) return false;
                        const minDate = moment().add(3, 'days').startOf('day');
                        return moment(selectInfo.start).isSameOrAfter(minDate);
                    },
                    select: function (info) {
                        inputTanggal.value = info.startStr;
                    }
                });
                calendar.render();
            }

            window.hitungTotal = function() {
                let jumlah = parseInt(inputPeserta.value) || 0;
                let total = jumlah * hargaDasar;

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