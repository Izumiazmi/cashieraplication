// =================================================================
// SCRIPT.JS - VERSI FINAL DAN LENGKAP
// =================================================================
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM Loaded. Script utama berjalan.");

    // =================================================================
    // BAGIAN 1: FUNGSI GLOBAL (Berjalan di SEMUA Halaman)
    // =================================================================
    const body = document.body;
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const themeToggle = document.getElementById("themeToggle");
    const mainContent = document.querySelector(".main-content");
    document.body.setAttribute("data-theme", "dark");

    // --- Logika Tema Gelap/Terang ---
    function toggleTheme() {
        const currentTheme = document.body.getAttribute("data-theme");
        const newTheme = currentTheme === "dark" ? "light" : "dark";
        document.body.setAttribute("data-theme", newTheme);
        localStorage.setItem("theme", newTheme);
    }

    // Check saved preference
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
        document.body.setAttribute("data-theme", savedTheme);
    }

    // --- Logika Buka/Tutup Sidebar ---
    const closeSidebar = () => {
        sidebar?.classList.remove("active");
        overlay?.classList.remove("active");
        sidebarToggle?.classList.remove("shifted");
        mainContent?.classList.remove("shifted");
    };
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", () => {
            sidebar?.classList.toggle("active");
            overlay?.classList.toggle("active");
            sidebarToggle?.classList.toggle("shifted");
            mainContent?.classList.toggle("shifted");
        });
    }
    if (overlay) {
        overlay.addEventListener("click", closeSidebar);
    }
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeSidebar();
    });

    // =================================================================
    // BAGIAN 2: FUNGSI KHUSUS HALAMAN
    // =================================================================

    // Fungsionalitas Modal To Do List
    const addTodoBtn = document.getElementById("addTodoBtn");
    const modalOverlay = document.getElementById("todoModalOverlay");
    const closeModalBtn = document.getElementById("closeModalBtn");

    if (addTodoBtn && modalOverlay && closeModalBtn) {
        addTodoBtn.addEventListener("click", () => {
            modalOverlay.style.display = "flex";
        });

        const closeModal = () => {
            modalOverlay.style.display = "none";
        };

        closeModalBtn.addEventListener("click", closeModal);

        // Menutup modal jika klik di luar area konten modal
        modalOverlay.addEventListener("click", (event) => {
            if (event.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // ===============================================
    // BAGIAN FUNGSI FILTER STATISTIK (YANG DIPERBAIKI)
    // ===============================================

    // 1. Pertama, kita cari elemen UTAMA dari fitur statistik ini.
    const percentIndicatorEl = document.querySelector(".percentage-indicator");

    // 2. GUNAKAN "IF" UNTUK MEMBUNGKUS SEMUA KODE STATISTIK.
    // Kode di dalam 'if' ini HANYA akan berjalan jika elemen '.percentage-indicator' DITEMUKAN.
    if (percentIndicatorEl) {
        // Data sementara untuk demonstrasi
        const dummyData = {
            "Hari ini": {
                amount: "Rp 1.250.000",
                percent: "5.2%",
                status: "up",
            },
            "Minggu ini": {
                amount: "Rp 8.750.000",
                percent: "1.8%",
                status: "up",
            },
            "Bulan ini": {
                amount: "Rp 35.100.000",
                percent: "12.5%",
                status: "up",
            },
        };

        // Semua variabel ini sekarang aman untuk dicari karena kita sudah di dalam 'if'
        const filterButtons = document.querySelectorAll(".filter-btn");
        const totalAmountEl = document.querySelector(".total-amount");
        // `percentIndicatorEl` sudah kita temukan di atas, jadi kita bisa langsung pakai
        const percentArrowEl = percentIndicatorEl.querySelector(".arrow");
        const percentValueEl =
            percentIndicatorEl.querySelector(".percent-value");

        // Event listener untuk setiap tombol filter
        filterButtons.forEach((button) => {
            button.addEventListener("click", () => {
                // Hapus kelas 'active' dari semua tombol
                filterButtons.forEach((btn) => btn.classList.remove("active"));
                // Tambahkan kelas 'active' ke tombol yang diklik
                button.classList.add("active");

                const filterKey = button.textContent;
                const data = dummyData[filterKey];

                if (data) {
                    totalAmountEl.textContent = data.amount;
                    percentValueEl.textContent = data.percent;
                    percentIndicatorEl.classList.remove("up", "down");
                    percentIndicatorEl.classList.add(data.status);
                    percentArrowEl.textContent =
                        data.status === "up" ? "▲" : "▼";
                }
            });
        });

        // Tambahan: Secara default, klik tombol filter pertama saat halaman dimuat
        if (filterButtons.length > 0) {
            filterButtons[0].click();
        }
    }

    // --- BAGIAN KHUSUS HALAMAN KASIR ---
    const kasirContainer = document.querySelector(".pos-layout");

    // Semua kode kasir hanya berjalan jika kita berada di halaman kasir
    if (kasirContainer) {
        const menuGridContainer = document.querySelector(".left-content");
        const orderList = document.getElementById("orderList");
        const totalPriceEl = document.getElementById("totalPrice");
        const saveOrderBtn = document.getElementById("saveOrderBtn");

        let currentOrder = {};

        // Pastikan semua elemen penting ada sebelum melanjutkan
        if (
            !menuGridContainer ||
            !orderList ||
            !totalPriceEl ||
            !saveOrderBtn
        ) {
            console.error(
                "Satu atau lebih elemen penting di halaman kasir tidak ditemukan!"
            );
            return; // Hentikan eksekusi jika ada elemen yang hilang
        }

        function updateOrderDisplay() {
            orderList.innerHTML = "";
            let total = 0;
            Object.values(currentOrder).forEach((item) => {
                total += item.harga * item.jumlah;
                const orderItemHTML = `
                    <div class="widget" data-id="${item.id}">
                        <h4>${item.nama}</h4>
                        <div class="number-input">
                            <button class="decrement" type="button">−</button>
                            <input type="number" value="${item.jumlah}" min="1" readonly />
                            <button class="increment" type="button">+</button>
                        </div>
                    </div>`;
                orderList.insertAdjacentHTML("beforeend", orderItemHTML);
            });
            totalPriceEl.textContent =
                "Total : Rp " + new Intl.NumberFormat("de-DE").format(total);
        }

        menuGridContainer.addEventListener("click", function (event) {
            const menuItem = event.target.closest(".btn-menu-item");
            if (!menuItem) return;
            const id = menuItem.dataset.id;
            const nama = menuItem.dataset.nama;
            const harga = parseInt(menuItem.dataset.harga);
            if (currentOrder[id]) {
                currentOrder[id].jumlah++;
            } else {
                currentOrder[id] = { id, nama, harga, jumlah: 1 };
            }
            updateOrderDisplay();
        });

        orderList.addEventListener("click", function (event) {
            const widget = event.target.closest(".widget");
            if (!widget) return;
            const id = widget.dataset.id;
            if (event.target.classList.contains("increment")) {
                currentOrder[id].jumlah++;
            } else if (event.target.classList.contains("decrement")) {
                currentOrder[id].jumlah--;
                if (currentOrder[id].jumlah <= 0) {
                    delete currentOrder[id];
                }
            }
            updateOrderDisplay();
        });

        saveOrderBtn.addEventListener("click", function () {
            const orderItems = Object.values(currentOrder);
            if (orderItems.length === 0) {
                alert("Tidak ada item dalam pesanan.");
                return;
            }

            const totalHarga = orderItems.reduce(
                (sum, item) => sum + item.harga * item.jumlah,
                0
            );
            const csrfTokenEl = document.querySelector(
                'meta[name="csrf-token"]'
            );

            // Pengecekan keamanan: pastikan CSRF token ada
            if (!csrfTokenEl) {
                alert("ERROR: CSRF Token Meta Tag tidak ditemukan!");
                return;
            }
            const csrfToken = csrfTokenEl.getAttribute("content");
            const actionUrl = saveOrderBtn.dataset.url;

            fetch(actionUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ order: orderItems, total: totalHarga }),
            })
                .then((response) => {
                    if (!response.ok)
                        return response.json().then((err) => {
                            throw err;
                        });
                    return response.json();
                })
                .then((data) => {
                    Swal.fire({
                        title: "Berhasil!",
                        text: data.message, // Pesan dari controller
                        icon: "success", // Ikon centang hijau
                        timer: 2000, // Notifikasi hilang setelah 2 detik
                        showConfirmButton: false, // Sembunyikan tombol "OK"
                    }).then(() => {
                        if (data.user_role === "pro") {
                            // 2. Jika ya, panggil fungsi cetak dengan data dari controller
                            printReceipt(data.transaction, data.anggota);
                        }

                        // 3. Refresh halaman (ini akan berjalan setelah cetak selesai)
                        window.location.reload();
                    });
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert(
                        "Gagal menyimpan transaksi: " +
                            (error.message || "Error tidak diketahui")
                    );
                });
        });
    }

    // --- BAGIAN KHUSUS HALAMAN TABEL (Manajemen Anggota / Menu) ---
    const tablePage = document.querySelector(".table-page-layout");
    if (tablePage) {
        console.log("Menjalankan skrip untuk Halaman Tabel...");

        // --- Logika untuk Modal Tambah/Edit ---
        const formModal = document.getElementById("formModal");
        if (formModal) {
            const addBtn = document.getElementById("addBtn");
            const dataForm = document.getElementById("dataForm");
            const modalTitle = document.getElementById("modalTitle");
            const tableBody = document.querySelector(".data-table tbody");
            const closeBtn = formModal.querySelector(".close-btn");
            let formMethodContainer = dataForm.querySelector("#formMethod");
            if (!formMethodContainer) {
                formMethodContainer = document.createElement("div");
                formMethodContainer.id = "formMethod";
                dataForm.prepend(formMethodContainer);
            }
            const openModal = () => (formModal.style.display = "flex");
            const closeModal = () => (formModal.style.display = "none");
            if (closeBtn) closeBtn.addEventListener("click", closeModal);
            if (addBtn) {
                addBtn.addEventListener("click", () => {
                    dataForm.reset();
                    modalTitle.textContent = "Tambah Data Baru";
                    dataForm.action = addBtn.dataset.storeUrl;
                    formMethodContainer.innerHTML = "";
                    openModal();
                });
            }
            if (tableBody) {
                tableBody.addEventListener("click", function (event) {
                    const editButton = event.target.closest(".edit-btn");
                    if (editButton) {
                        document.getElementById("namaInput").value =
                            editButton.dataset.nama;
                        document.getElementById("hargaInput").value =
                            editButton.dataset.harga;
                        document.getElementById("jenisInput").value =
                            editButton.dataset.jenis;
                        modalTitle.textContent = "Edit Menu";
                        dataForm.action = editButton.dataset.updateUrl;
                        formMethodContainer.innerHTML =
                            '<input type="hidden" name="_method" value="PUT">';

                        openModal();
                    }
                });
            }
        }

        // --- Logika untuk Modal Hapus ---
        const deleteModal = document.getElementById("deleteModal");

        // Kode ini hanya berjalan jika modal hapus ada di halaman
        if (deleteModal) {
            const confirmDeleteBtn =
                document.getElementById("confirmDeleteBtn");
            const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
            const deleteForm = document.getElementById("deleteForm");
            let urlToDelete = ""; // Variabel untuk menyimpan URL yang akan dihapus

            // 1. Dengarkan klik di seluruh dokumen. Ini cara paling andal.
            document.addEventListener("click", function (event) {
                // Cek apakah yang diklik (atau induknya) adalah sebuah tombol .delete-btn
                const deleteButton = event.target.closest(".delete-btn");

                // Jika tombol .delete-btn ditemukan:
                if (deleteButton) {
                    event.preventDefault(); // Mencegah aksi default jika ada
                    // Ambil URL dari atribut data-url dan simpan
                    urlToDelete = deleteButton.dataset.url;
                    // Tampilkan modal
                    deleteModal.style.display = "flex";
                }
            });

            // 2. Jika tombol "Ya, Hapus" di modal diklik
            if (confirmDeleteBtn && deleteForm) {
                confirmDeleteBtn.addEventListener("click", () => {
                    if (urlToDelete) {
                        deleteForm.setAttribute("action", urlToDelete);
                        deleteForm.submit();
                    }
                });
            }

            // 3. Jika tombol "Batal" di modal diklik
            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener("click", () => {
                    deleteModal.style.display = "none";
                });
            }
        }
    }

    // --- BAGIAN KHUSUS HALAMAN PROFIL ANGGOTA ---
    const profileForm = document.getElementById("profileUpdateForm");
    if (profileForm) {
        console.log("Menjalankan skrip untuk Profil Anggota...");
        const editBtn = profileForm.querySelector(".btn-edit");
        const cancelBtn = profileForm.querySelector(".btn-cancel");
        const cards = profileForm.querySelectorAll(".card");
        const saveTriggerBtn = profileForm.querySelector(".btn-save"); // Tombol Simpan di form

        // Modal konfirmasi untuk SIMPAN
        const saveConfirmModal = document.getElementById("saveConfirmModal");

        if (editBtn && cancelBtn) {
            editBtn.addEventListener("click", (e) => {
                e.preventDefault();
                cards.forEach((c) => c.classList.add("editing"));
            });
            cancelBtn.addEventListener("click", (e) => {
                e.preventDefault();
                cards.forEach((c) => c.classList.remove("editing"));
                profileForm.reset();
            });
        }

        // Tampilkan modal konfirmasi saat tombol 'Simpan Perubahan' diklik
        if (saveTriggerBtn && saveConfirmModal) {
            // Kita hentikan submit asli dari tombol Simpan
            saveTriggerBtn.addEventListener("click", function (event) {
                event.preventDefault();
                saveConfirmModal.style.display = "flex";
            });

            // Tombol 'Batal' di dalam modal
            const cancelSaveBtn =
                saveConfirmModal.querySelector("#cancelSaveBtn");
            cancelSaveBtn.addEventListener("click", () => {
                saveConfirmModal.style.display = "none";
            });

            // Tombol 'Ya, Simpan' di dalam modal akan men-submit form utama
            const confirmSaveBtn =
                saveConfirmModal.querySelector("#confirmSaveBtn");
            confirmSaveBtn.addEventListener("click", () => {
                profileForm.submit(); // Lanjutkan submit form
            });
        }
    }

    // --- BAGIAN KHUSUS UNTUK MODAL HAPUS (Generik/Umum) ---
    const deleteModal = document.getElementById("deleteModal");
    if (deleteModal) {
        console.log("Menjalankan skrip untuk Modal Hapus...");
        const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
        const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
        const deleteForm = document.getElementById("deleteForm");
        let deleteUrl = "";

        // Dengarkan klik di seluruh dokumen. Ini lebih aman.
        document.addEventListener("click", function (event) {
            const targetBtn = event.target.closest(".delete-btn");
            if (targetBtn && deleteForm) {
                // Pastikan tombol & form ada
                deleteUrl = targetBtn.dataset.url;
                deleteModal.style.display = "flex";
            }
        });

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener("click", () => {
                deleteModal.style.display = "none";
            });
        }

        if (confirmDeleteBtn && deleteForm) {
            confirmDeleteBtn.addEventListener("click", () => {
                if (deleteUrl) {
                    deleteForm.setAttribute("action", deleteUrl);
                    deleteForm.submit();
                }
            });
        }
    }

    // --- Logika untuk Halaman TABEL ---
    const tableBody = document.querySelector(
        "body.table-page-layout .data-table tbody"
    );
    if (tableBody) {
        const formModal = document.getElementById("formModal");
        const deleteModal = document.getElementById("deleteModal");
        const addBtn = document.getElementById("addBtn");
        let rowToDelete = null;

        const openModal = (modal) => {
            if (modal) modal.style.display = "flex";
        };
        const closeModal = (modal) => {
            if (modal) modal.style.display = "none";
        };

        if (addBtn) {
            addBtn.addEventListener("click", () => {
                document.getElementById("modalTitle").textContent =
                    "Tambah Data Baru";
                document.getElementById("dataForm").reset();
                openModal(formModal);
            });
        }

        tableBody.addEventListener("click", (event) => {
            const target = event.target;
            const row = target.closest("tr");
            if (!row) return;

            if (target.classList.contains("edit-btn")) {
                const nama = row.children[1].textContent;
                const harga = row.children[2].textContent.replace(
                    /[^0-9]/g,
                    ""
                );
                const jenis = row.children[3].textContent;
                document.getElementById("modalTitle").textContent = "Edit Data";
                document.getElementById("namaInput").value = nama;
                document.getElementById("hargaInput").value = harga;
                document
                    .getElementById("hargaInput")
                    .dispatchEvent(new Event("input"));
                document.getElementById("jenisInput").value = jenis;
                openModal(formModal);
            }

            if (target.classList.contains("delete-btn")) {
                rowToDelete = row;
                openModal(deleteModal);
            }
        });

        document.querySelectorAll(".modal-overlay").forEach((modal) => {
            const closeBtn = modal.querySelector(".close-btn");
            if (closeBtn)
                closeBtn.addEventListener("click", () => closeModal(modal));
            modal.addEventListener("click", (e) => {
                if (e.target === modal) closeModal(modal);
            });
        });

        const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
        const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
        if (confirmDeleteBtn)
            confirmDeleteBtn.addEventListener("click", () => {
                if (rowToDelete) rowToDelete.remove();
                closeModal(deleteModal);
                rowToDelete = null;
            });
        if (cancelDeleteBtn)
            cancelDeleteBtn.addEventListener("click", () => {
                closeModal(deleteModal);
                rowToDelete = null;
            });

        const hargaInput = document.getElementById("hargaInput");
        if (hargaInput) {
            hargaInput.addEventListener("input", function (e) {
                let rawValue = e.target.value.replace(/[^0-9]/g, "");
                e.target.value = rawValue
                    ? new Intl.NumberFormat("id-ID").format(rawValue)
                    : "";
            });
        }
    }

    // === Elemen-elemen Modal dan Form ===
    const formModal = document.getElementById("formModal");
    const addBtn = document.getElementById("addBtn");
    const closeBtn = formModal.querySelector(".close-btn");
    const modalTitle = document.getElementById("modalTitle");
    const dataForm = document.getElementById("dataForm");

    // Panggil fungsi ini saat halaman pertama kali dimuat
    updateStatusIndicators();

    // === Fungsi untuk membuka modal ===
    function openModal() {
        formModal.style.display = "flex";
    }

    // === Fungsi untuk menutup modal ===
    function closeModal() {
        formModal.style.display = "none";
        dataForm.reset(); // Bersihkan form
        dataForm.removeAttribute("data-editing-row-index"); // Hapus status edit
    }

    // === Event Listener untuk Tombol "Tambah Data" (+) ===
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            modalTitle.textContent = "Tambah Data Baru";
            dataForm.reset();
            openModal();
        });
    }

    // === Event Listener untuk Tombol "Edit" dan "Hapus" pada Tabel ===
    tableBody.addEventListener("click", function (event) {
        const target = event.target;
        const row = target.closest("tr");
        if (!row) return;

        // --- Logika untuk Tombol EDIT ---
        if (target.classList.contains("edit-btn")) {
            // 1. Ambil data dari sel-sel tabel di baris tersebut
            const ownerName = row.cells[1].textContent;
            const shopName = row.cells[2].textContent;
            const whatsApp = row.cells[3].textContent;
            const address = row.cells[4].textContent;
            const status = row.cells[5].textContent;

            // 2. Isi form dengan data yang sudah diambil
            document.getElementById("namaInput").value = ownerName;
            document.getElementById("namaToko").value = shopName;
            document.getElementById("noWa").value = whatsApp;
            document.getElementById("alamat").value = address;
            document.getElementById("jenisInput").value = status;

            // 3. Ubah judul modal dan simpan indeks baris yang diedit
            modalTitle.textContent = "Edit Data";
            dataForm.setAttribute("data-editing-row-index", row.rowIndex);

            // 4. Tampilkan modal
            openModal();
        }

        // --- Logika untuk Tombol HAPUS (Contoh) ---
        if (target.classList.contains("delete-btn")) {
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                row.remove();
                // Di sini Anda biasanya akan memanggil API untuk menghapus data dari server
            }
        }
    });

    // === Event Listener untuk Menyimpan Data (Submit Form) ===
    dataForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Mencegah form reload halaman

        const editingRowIndex = dataForm.getAttribute("data-editing-row-index");

        if (editingRowIndex) {
            // --- MODE EDIT: Perbarui baris yang ada ---
            // (CATATAN: Logika ini hanya mengubah tampilan, belum menyimpan ke database. Lihat bonus di bawah)
            const newOwnerName = document.getElementById("namaInput").value;
            const newShopName = document.getElementById("namaToko").value;
            // ... ambil nilai lainnya ...

            const rowToUpdate = tableBody.rows[parseInt(editingRowIndex) - 1];
            rowToUpdate.cells[1].textContent = newOwnerName;
            rowToUpdate.cells[2].textContent = newShopName;
            // ... perbarui sel lainnya ...

            updateStatusIndicators();
            closeModal(); // Pindahkan closeModal() ke SINI
        } else {
            // --- MODE TAMBAH: Kirim data ke server ---
            const formData = new FormData(this);
            const actionUrl = this.action;

            fetch(actionUrl, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then((errorData) => {
                            throw errorData;
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    // Jika SUKSES, tampilkan pesan dan reload halaman
                    alert(data.message || "Anggota berhasil ditambahkan!");
                    window.location.reload(); // Reload akan otomatis menutup modal
                })
                .catch((error) => {
                    // Jika GAGAL, tampilkan pesan error, modal JANGAN ditutup
                    console.error("Error:", error);
                    let errorMessages = "Gagal menyimpan data:\n";
                    if (error.errors) {
                        for (const key in error.errors) {
                            errorMessages += `- ${error.errors[key][0]}\n`;
                        }
                    } else {
                        errorMessages +=
                            error.message ||
                            "Terjadi kesalahan tidak diketahui.";
                    }
                    alert(errorMessages);
                });
        }
        // HAPUS closeModal() dan updateStatusIndicators() DARI SINI
    });

    // === Event Listener untuk menutup modal (Tombol X dan klik di luar) ===
    if (closeBtn) {
        closeBtn.addEventListener("click", closeModal);
    }

    formModal.addEventListener("click", function (event) {
        if (event.target === formModal) {
            closeModal();
        }
    });
});

/**
 *
 * @param {object} transaction
 * @param {object} anggota
 */

function generateQRCode(data) {
    const dataBytes = new TextEncoder().encode(data);
    const dataLength = dataBytes.length + 3;
    const pL = dataLength % 256;
    const pH = Math.floor(dataLength / 256);

    let commands = "";

    commands += "\x1D\x28\x6B\x04\x00\x31\x41\x32\x00";

    commands += "\x1D\x28\x6B\x03\x00\x31\x43\x04";

    commands += "\x1D\x28\x6B\x03\x00\x31\x45\x31";

    commands += `\x1D\x28\x6B${String.fromCharCode(pL)}${String.fromCharCode(
        pH
    )}\x31\x50\x30`;
    commands += data;

    commands += "\x1D\x28\x6B\x03\x00\x31\x51\x30";

    return commands;
}

function printReceipt(transaction, anggota) {
    const formatRp = (number) => new Intl.NumberFormat("id-ID").format(number);

    const CMD_CENTER = "\x1B\x61\x01";
    const CMD_LEFT_ALIGN = "\x1B\x61\x00";
    const CMD_FONT_SMALL = "\x1B\x21\x01";
    const CMD_FONT_NORMAL = "\x1B\x21\x00";

    let receiptText = "";

    receiptText += `${CMD_CENTER}${anggota.nama_toko}\n`;
    receiptText += `${CMD_FONT_SMALL}`;
    receiptText += `${anggota.alamat}\n`;
    receiptText += `Telp: ${anggota.no_hp}\n`;
    receiptText += `${CMD_FONT_NORMAL}`;
    receiptText += "================================\n";

    const kodeTransaksi = `${anggota.username.substring(0, 3).toUpperCase()}-${
        transaction.id
    }`;
    receiptText += `Kode: ${kodeTransaksi}\n`;
    receiptText += `Tanggal: ${new Date(transaction.created_at).toLocaleString(
        "id-ID"
    )}\n`;
    receiptText += "================================\n";

    transaction.details.forEach((item) => {
        let itemName = item.menu.nama.padEnd(16, " ");
        let itemQty = item.jumlah.toString().padStart(3, " ");
        let itemPrice = formatRp(item.harga).padStart(10, " ");
        receiptText += `${itemName}${itemQty} x ${itemPrice}\n`;
    });

    receiptText += "================================\n";
    receiptText +=
        "Total".padEnd(21, " ") + `Rp ${formatRp(transaction.total_harga)}\n`;
    receiptText += "\n";
    receiptText += "Layanan Web Kasir\n";
    receiptText += "Telp/WA 081374195580\n";
    receiptText += "\n";

    const waLink =
        "https://dash.infinityfree.com/accounts/if0_39709336/domains/cashierapliaction.free.nf";

    const qrCodeCommands = generateQRCode(waLink);

    receiptText += `${CMD_CENTER}`;
    receiptText += qrCodeCommands;
    receiptText += "\n";
    receiptText += "Terima Kasih!\n";

    receiptText += `${CMD_LEFT_ALIGN}`;

    const encodedReceipt = encodeURIComponent(receiptText);
    const intentUrl = `intent:${encodedReceipt}#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;`;
    window.location.href = intentUrl;
}
