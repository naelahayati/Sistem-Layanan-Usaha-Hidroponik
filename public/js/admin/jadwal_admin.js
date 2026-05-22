document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    if (!calendarEl) return;

    // Hapus kalender lama jika script berjalan dua kali (mencegah bug duplikat/bertumpuk)
    calendarEl.innerHTML = "";

    let calendar;

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "id",
        firstDay: 0,
        displayEventTime: false,
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        buttonText: {
            today: "Hari Ini",
        },
        events: "/admin/jadwal/events",
        selectable: true,
        select: function (info) {
            // Cek apakah ada event libur di tanggal ini
            const events = calendar.getEvents();
            const holidayEvent = events.find(
                (e) =>
                    e.extendedProps.kategori === "libur" &&
                    e.startStr === info.startStr,
            );

            if (holidayEvent) {
                showDetailPanel(holidayEvent);
            } else {
                resetForm();
                document.getElementById("jadwalStart").value = info.startStr;

                let endDate = new Date(info.endStr);
                endDate.setDate(endDate.getDate() - 1);
                let endStr = endDate.toISOString().split("T")[0];

                if (info.startStr !== endStr) {
                    document.getElementById("jadwalEnd").value = endStr;
                } else {
                    document.getElementById("jadwalEnd").value = "";
                }
                showFormPanel();
            }
        },
        eventClick: function (info) {
            showDetailPanel(info.event);
        },
        eventDidMount: function (info) {
            if (info.event.extendedProps.kategori === "libur") {
                const cell = info.el.closest(".fc-daygrid-day");
                if (cell) {
                    cell.style.setProperty("background-color", "#fff1f1", "important");
                    const number = cell.querySelector(".fc-daygrid-day-number");
                    if (number) {
                        number.style.setProperty("color", "#d9534f", "important");
                        number.style.setProperty("font-weight", "700", "important");
                    }
                }
            }
        },
    });

    calendar.render();

    // Observe container size changes (Sidebar toggle support)
    const resizeObserver = new ResizeObserver(() => {
        calendar.updateSize();
    });
    resizeObserver.observe(calendarEl);
    const detailPanel = document.getElementById("detailPanel");
    const jadwalForm = document.getElementById("jadwalForm");
    const btnCancelEdit = document.getElementById("btnCancelEdit");
    const btnEditJadwal = document.getElementById("btnEditJadwal");
    const btnDeleteJadwal = document.getElementById("btnDeleteJadwal");
    const closeDetailBtn = document.getElementById("closeDetailBtn");
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;

    let currentEventId = null;

    function resetForm() {
        jadwalForm.reset();
        document.getElementById("jadwalId").value = "";
        document.getElementById("formTitle").innerHTML =
            '<i class="fas fa-plus-circle mr-2 text-success"></i> Kelola Libur';
        btnCancelEdit.classList.add("d-none");
        document.getElementById("btnSaveJadwal").textContent =
            "Simpan Hari Libur";
    }

    function showFormPanel() {
        detailPanel.classList.add("d-none");
        formPanel.classList.remove("d-none");
        formPanel.style.opacity = "0";
        setTimeout(() => {
            formPanel.style.transition = "opacity 0.3s";
            formPanel.style.opacity = "1";
        }, 10);
    }

    function showDetailPanel(event) {
        currentEventId = event.id;

        const kat = event.extendedProps.kategori;
        const badgeEl = document.getElementById("detailKategori");
        let badgeClass = "badge-secondary";
        let katName = kat;
        let title = event.title;

        if (kat === "libur") {
            badgeClass = "badge-danger";
            katName = "HARI LIBUR";
        } else if (kat === "kunjungan") {
            badgeClass = "badge-success";
            katName = "KUNJUNGAN";
            title = "Daftar Kunjungan";
        } else if (kat === "magang") {
            const tipe = event.extendedProps.tipe || "";
            badgeClass = tipe === "PKL" ? "badge-primary" : "badge-info";
            katName = "MAGANG " + tipe;
            title = "Peserta Magang " + tipe;
        }

        document.getElementById("detailTitle").textContent = title;
        badgeEl.className = `badge ${badgeClass} kategori-badge`;
        badgeEl.textContent = katName;

        let dateStr = event.startStr;
        if (event.end) {
            let endD = new Date(event.end);
            // FullCalendar end is exclusive
            if (kat === "libur" || kat === "magang")
                endD.setDate(endD.getDate() - 1);
            let endStr = endD.toISOString().split("T")[0];
            if (dateStr !== endStr) dateStr += " s/d " + endStr;
        }
        document.getElementById("detailDate").textContent = dateStr;
        document.getElementById("detailDeskripsi").textContent =
            event.extendedProps.deskripsi || "- Tidak ada info tambahan -";

        // Sembunyikan Tombol Edit/Hapus jika event otomatis
        if (
            currentEventId.toString().startsWith("mag-") ||
            currentEventId.toString().startsWith("kun-")
        ) {
            btnEditJadwal.classList.add("d-none");
            btnDeleteJadwal.classList.add("d-none");
        } else {
            btnEditJadwal.classList.remove("d-none");
            btnDeleteJadwal.classList.remove("d-none");
        }

        detailPanel.classList.remove("d-none");
        detailPanel.style.opacity = "0";
        setTimeout(() => {
            detailPanel.style.transition = "opacity 0.3s";
            detailPanel.style.opacity = "1";
        }, 10);
    }

    jadwalForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const id = document.getElementById("jadwalId").value;
        // Bersihkan ID dari prefix 'libur-' jika sedang edit
        const cleanId = id.toString().replace("libur-", "");

        const formData = new FormData(jadwalForm);
        const url = cleanId
            ? `/admin/jadwal/edit/${cleanId}`
            : `/admin/jadwal/add`;

        fetch(url, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            body: formData,
        })
            .then((res) => res.json())
            .then((result) => {
                if (result.success) {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: result.message,
                        showConfirmButton: false,
                        timer: 3000,
                    });
                    resetForm();
                    calendar.refetchEvents();
                } else {
                    Swal.fire({
                        title: "Gagal!",
                        html: result.message.replace(/\n/g, '<br>'),
                        icon: "error"
                    });
                }
            });
    });

    btnEditJadwal.addEventListener("click", function () {
        const match = currentEventId.toString().match(/libur-(\d+)/);
        const cleanId = match ? match[1] : null;
        if (!cleanId) return;

        fetch(`/admin/jadwal/get/${cleanId}`)
            .then((res) => res.json())
            .then((data) => {
                document.getElementById("jadwalId").value = data.id;
                document.getElementById("jadwalTitle").value = data.title;
                document.getElementById("jadwalKategori").value = "libur";
                document.getElementById("jadwalDeskripsi").value =
                    data.deskripsi;
                document.getElementById("jadwalStart").value =
                    data.start_date.split(" ")[0];
                if (data.end_date)
                    document.getElementById("jadwalEnd").value =
                        data.end_date.split(" ")[0];

                document.getElementById("formTitle").innerHTML =
                    '<i class="fas fa-edit mr-2 text-warning"></i> Ubah Libur';
                document.getElementById("btnSaveJadwal").textContent =
                    "Simpan Perubahan";
                btnCancelEdit.classList.remove("d-none");
                showFormPanel();
            });
    });

    btnDeleteJadwal.addEventListener("click", function () {
        const match = currentEventId.toString().match(/libur-(\d+)/);
        const cleanId = match ? match[1] : null;
        if (!cleanId) return;

        Swal.fire({
            title: "Hapus Libur?",
            text: "Data hari libur ini akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/jadwal/delete/${cleanId}`, {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                    .then((res) => res.json())
                    .then((result) => {
                        if (result.success) {
                            detailPanel.classList.add("d-none");

                            // Reset SEMUA sel kalender secara manual agar tanda merah hilang total
                            document.querySelectorAll('.fc-daygrid-day').forEach(cell => {
                                cell.style.removeProperty('background-color');
                                const number = cell.querySelector(".fc-daygrid-day-number");
                                if (number) {
                                    number.style.removeProperty('color');
                                    number.style.removeProperty('font-weight');
                                }
                            });

                            calendar.refetchEvents();
                            Swal.fire("Terhapus!", result.message, "success");
                        }
                    });
            }
        });
    });

    btnCancelEdit.addEventListener("click", resetForm);
    closeDetailBtn.addEventListener("click", () =>
        detailPanel.classList.add("d-none"),
    );
});
