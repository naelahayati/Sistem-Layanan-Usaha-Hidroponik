@extends('master')

@section('konten')
<!-- FullCalendar Dependencies -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
{{-- Menggunakan CSS pelatihan untuk menyamakan tampilan form --}}
<link rel="stylesheet" href="{{ asset('css/stylekunjungan.css') }}?v={{ time() }}">

<style>
    /* Mengembalikan Banner Kunjungan */
    .kunjungan-header-form {
        position: relative;
        width: 100%;
        height: 300px;
        background-image:
            linear-gradient(to bottom, rgba(255, 255, 255, 0) 65%, rgba(255, 255, 255, 1) 100%),
            linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.35)),
            url("{{ asset('image/kunjungan.webp') }}") !important;
        background-size: cover !important;
        background-position: center 40% !important;
        background-repeat: no-repeat;
        display: flex;
        padding-top: 80px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .kunjungan-header-form h1 {
        font-family: "Playfair Display", serif;
        font-size: 4.2rem;
        color: #ffffff;
        letter-spacing: 12px;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    /* Tetapkan hari Sabtu dan Minggu berwarna merah */
    #calendar-reservasi .fc-day-sun,
    #calendar-reservasi .fc-day-sat {
        background-color: #fff1f1 !important;
    }
    #calendar-reservasi .fc-day-sun .fc-daygrid-day-number,
    #calendar-reservasi .fc-day-sat .fc-daygrid-day-number {
        color: #d9534f !important;
        font-weight: 700 !important;
    }

    /* Warna hijau saat tanggal dipilih (diklik) dihapus agar mengikuti default soft FullCalendar seperti di Magang */
</style>

<div class="reservasi-page-wrapper">
    <header class="kunjungan-header-form">
        <h1>RESERVASI</h1>
    </header>

    <div class="action-bar-reservasi">
        <a href="{{ route('nazfram.kunjungan') }}" class="btn-back-modern">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="reservasi-form-frame">
        <div class="reservasi-card-modern">
            <h2 class="form-title-modern">{{ $kunjungan->name }}</h2>

            <form action="{{ route('nazfram.reservasi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_kunjungan" value="{{ $id_kunjungan }}">

                <!-- Kalender -->
                <div class="form-group-modern">
                    <label>Pilih Rencana Tanggal Kunjungan</label>
                    <p style="color: #555; font-size: 0.85rem; background: #fff8e1; padding: 10px 15px; border-left: 4px solid #ffc107; border-radius: 4px; margin-bottom: 15px; line-height: 1.6;">
                       Jadwal hanya dapat dipilih minimal H+3 dari tanggal pemesanan.
                    </p>
                    <div id="calendar-reservasi"></div>
                    {{-- Input tersembunyi untuk dikirim ke Controller --}}
                    <input type="hidden" name="tanggal_reservasi" id="tanggal_reservasi_input" value="{{ old('tanggal_reservasi') }}" required>
                    @error('tanggal_reservasi')
                        <div class="invalid-feedback" style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Peserta -->
                <div class="form-group-modern">
                    <label>Jumlah Peserta (Orang)</label>
                    <input type="number" name="jumlah_peserta" id="jumlah_peserta" class="@error('jumlah_peserta') is-invalid @enderror" placeholder="Masukkan jumlah peserta" min="{{ $kunjungan->min_people }}" max="{{ $kunjungan->max_people }}" value="{{ old('jumlah_peserta') }}" required>
                    <div id="peserta-error-msg" style="display: none; color: #dc3545; font-size: 0.85rem; margin-top: 5px; font-weight: 500;">Minimal peserta adalah {{ $kunjungan->min_people }} orang dan maksimal {{ $kunjungan->max_people }} orang.</div>
                    <small style="color: #888;">* Batas peserta: minimal {{ $kunjungan->min_people }} orang dan maksimal {{ $kunjungan->max_people }} orang.</small>
                    @error('jumlah_peserta')
                        <div class="invalid-feedback" style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Instansi -->
                <div class="form-group-modern">
                    <label>Nama Instansi / Kelompok</label>
                    <input type="text" id="instansi" name="instansi" class="@error('instansi') is-invalid @enderror" value="{{ old('instansi') }}" placeholder="Contoh: SMK Negeri 1 Subang" required>
                    @error('instansi')
                        <div class="invalid-feedback" style="display: block; color: #dc3545; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="total-container" style="background: #fcfdfc; border: 1px dashed #1b3a1a; border-radius: 15px; padding: 15px 20px; margin: 20px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: #1b3a1a; font-size: 1rem;">Total Pembayaran:</span>
                        <span id="total_bayar_text" style="font-size: 1.5rem; font-weight: 800; color: #1b3a1a;">Rp 0</span>
                    </div>
                    <input type="hidden" name="total_harga" id="total_harga_input">
                </div>

                    <!-- Metode Pembayaran -->
                <div class="pesanan-section">
                    <h3 class="section-title"><i class="fas fa-wallet"></i> Metode Pembayaran</h3>

                    <label class="radio-card active" id="card-qris">
                        <input type="radio" name="metode_pembayaran" id="radio-qris" value="qris" checked onchange="updateUI()">
                        <div>
                            <strong>QRIS</strong>
                            <div style="font-size: 0.85rem; color: #666;">Bayar instan via e-Wallet/M-Banking.</div>
                        </div>
                    </label>
                        <div id="qris-info" style="display: none;">
                           <p><strong>Info:</strong> Anda akan diarahkan ke halaman pembayaran QRIS setelah menekan tombol daftar.</p>
                        </div>
                </div>

                <div class="form-actions-modern" style="margin-top: 30px;">
                    <div style="margin-top: 40px;">
                        <button type="submit" id="btn-submit-reservasi" class="btn-Daftar" style="width: 100%; justify-content: center; font-size: 1.2rem; padding: 18px;">
                            Daftar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hargaDasar = {{ $kunjungan->price }};
        const inputPeserta = document.getElementById('jumlah_peserta');
        const inputInstansi = document.getElementById('instansi');
        const textTotal = document.getElementById('total_bayar_text');
        const qrisInfo = document.getElementById('qris-info');
        const calendarEl = document.getElementById('calendar-reservasi');
        const inputTanggal = document.getElementById('tanggal_reservasi_input');
        const errorMsg = document.getElementById('peserta-error-msg');
        const btnSubmit = document.getElementById('btn-submit-reservasi');
        const methodRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
        let calendar;

        // Inisialisasi Kalender
        if (calendarEl) {
            calendar = new FullCalendar.Calendar(calendarEl, {
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
                events: "{{ route('nazfram.pelatihan.events') }}?type=kunjungan", // Mengambil hari libur dan status sold out kunjungan
                selectAllow: function(selectInfo) {
                    const start = moment(selectInfo.start);
                    const day = start.day();

                    // 1. Hari Sabtu dan Minggu dilarang (Libur)
                    if (day === 0 || day === 6) return false;

                    // 2. Hari lampau dan minimal H+3 dilarang
                    const minDate = moment().add(3, 'days').startOf('day');
                    if (start.isBefore(minDate)) return false;

                    // 3. Cek apakah bertabrakan dengan hari libur (event background "TUTUP")
                    const dateStr = start.format('YYYY-MM-DD');
                    const overlapping = calendar.getEvents().some(event => {
                        if (event.display === 'background') {
                            const evStart = moment(event.start).format('YYYY-MM-DD');
                            const evEnd = event.end ? moment(event.end).format('YYYY-MM-DD') : moment(event.start).add(1, 'days').format('YYYY-MM-DD');
                            return moment(dateStr).isSameOrAfter(evStart) && moment(dateStr).isBefore(evEnd);
                        }
                        return false;
                    });

                    return !overlapping;
                },
                select: function(info) {
                    inputTanggal.value = info.startStr;
                    hitungTotal();
                }
            });
            calendar.render();
        }

        function updateUI() {
            const selected = document.querySelector('input[name="metode_pembayaran"]:checked');
            if (qrisInfo) {
                qrisInfo.style.display = (selected && selected.value === 'qris') ? 'block' : 'none';
            }
        }

        // Tambahkan event listener untuk memantau perubahan metode pembayaran
        methodRadios.forEach(radio => radio.addEventListener('change', updateUI));

        function hitungTotal() {
            let jumlah = parseInt(inputPeserta.value) || 0;
            const minPeserta = {{ $kunjungan->min_people }};
            const maxPeserta = {{ $kunjungan->max_people }};

            // Validasi Peserta Real-time
            if (inputPeserta.value !== "" && (jumlah < minPeserta || jumlah > maxPeserta)) {
                errorMsg.style.display = 'block';
                inputPeserta.style.borderColor = '#dc3545';
                btnSubmit.style.opacity = '0.5';
                btnSubmit.style.pointerEvents = 'none';
            } else {
                errorMsg.style.display = 'none';
                inputPeserta.style.borderColor = '#e0e6e0';
                btnSubmit.style.opacity = '1';
                btnSubmit.style.pointerEvents = 'auto';
            }

            let total = jumlah * hargaDasar;
            if (textTotal) {
                textTotal.textContent = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(total);
            }
        }

        inputPeserta.addEventListener('input', hitungTotal);
        inputInstansi.addEventListener('input', hitungTotal);
        updateUI();
        hitungTotal();
    });
</script>
@endsection
