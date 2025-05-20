<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Head } from "@inertiajs/vue3";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import {
    mdiQrcodeScan,
    mdiCheckCircle,
    mdiAlertCircle,
    mdiRefresh,
} from "@mdi/js";
import axios from "axios";

const scanResult = ref(null);
const loading = ref(false);
const error = ref(null);
const scanner = ref(null);
const scanActive = ref(false);
const cameraList = ref([]);
const selectedCamera = ref("");

// Format datetime dengan timezone yang benar
function formatDateTime(datetime) {
    if (!datetime) return "";

    // Pastikan datetime diperlakukan sebagai UTC dan dikonversi ke waktu lokal
    const date = new Date(datetime + "Z"); // Tambahkan 'Z' untuk memastikan diinterpretasikan sebagai UTC

    // Konversi ke waktu lokal Indonesia (GMT+7)
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
        timeZone: "Asia/Jakarta",
    };

    return new Intl.DateTimeFormat("id-ID", options).format(date);
}

// Dapatkan daftar kamera yang tersedia
async function getCameras() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(
            (device) => device.kind === "videoinput"
        );

        cameraList.value = videoDevices.map((device) => ({
            id: device.deviceId,
            label: device.label || `Camera ${videoDevices.indexOf(device) + 1}`,
        }));

        if (cameraList.value.length > 0) {
            selectedCamera.value = cameraList.value[0].id;
        }
    } catch (err) {
        console.error("Error getting cameras:", err);
        error.value = "Tidak dapat mengakses daftar kamera: " + err.message;
    }
}

// Inisialisasi scanner QR code
async function initScanner() {
    if (scanActive.value) return;

    scanActive.value = true;
    loading.value = true;
    error.value = null;
    scanResult.value = null;

    try {
        const { Html5Qrcode } = await import("html5-qrcode");

        // Bersihkan scanner sebelumnya jika ada
        if (scanner.value) {
            await scanner.value.stop();
        }

        const html5QrCode = new Html5Qrcode("qr-reader");

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
        };

        const cameraId = selectedCamera.value || { facingMode: "environment" };

        await html5QrCode.start(
            cameraId,
            config,
            (decodedText) => {
                // QR code terdeteksi
                html5QrCode.stop().then(() => {
                    scanActive.value = false;
                    verifyQrCode(decodedText);
                });
            },
            (errorMessage) => {
                // Kesalahan saat scanning (tidak perlu menampilkan)
                console.log(errorMessage);
            }
        );

        scanner.value = html5QrCode;
        loading.value = false;
    } catch (err) {
        scanActive.value = false;
        loading.value = false;
        error.value = "Gagal memuat scanner: " + err.message;
        console.error("Scanner initialization error:", err);
    }
}

// Verifikasi QR code yang dipindai
async function verifyQrCode(qrData) {
    loading.value = true;
    error.value = null;

    try {
        // Coba dekripsi data QR (jika terenkripsi)
        let uuid = qrData;

        try {
            // Coba parse sebagai JSON jika data terenkripsi
            const decryptedData = JSON.parse(qrData);
            if (decryptedData && decryptedData.uuid) {
                uuid = decryptedData.uuid;
            }
        } catch (e) {
            // Jika bukan JSON, coba ekstrak UUID dari URL
            if (qrData.includes("/scan/verify/")) {
                uuid = qrData.split("/scan/verify/")[1];
            }
        }

        // Kirim request ke API untuk verifikasi
        const response = await axios.get(`/api/scan/verify/${uuid}`, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });

        if (response.data.success) {
            scanResult.value = response.data.data;
            // Lakukan check-in otomatis
            await checkIn(uuid);
        } else {
            error.value = response.data.message || "QR Code tidak valid";
            scanResult.value = null;
        }
    } catch (err) {
        console.error("QR verification error:", err);
        error.value =
            "Gagal memverifikasi QR code: " +
            (err.response?.data?.message || err.message);
        scanResult.value = null;
    } finally {
        loading.value = false;
    }
}

// Proses check-in pengunjung
async function checkIn(uuid) {
    try {
        const response = await axios.post(
            "/api/scan/check-in",
            { uuid },
            {
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    "Content-Type": "application/json",
                },
            }
        );

        if (response.data.success) {
            // Tambahkan informasi check-in ke hasil scan
            scanResult.value.check_in_time = response.data.data.check_in_time;
        } else {
            error.value = response.data.message || "Gagal melakukan check-in";
        }
    } catch (err) {
        console.error("Check-in error:", err);
        error.value =
            "Gagal melakukan check-in: " +
            (err.response?.data?.message || err.message);
    }
}

// Reset scanner
function resetScanner() {
    if (scanner.value && scanActive.value) {
        scanner.value
            .stop()
            .then(() => {
                scanActive.value = false;
                loading.value = false;
                error.value = null;
                scanResult.value = null;
            })
            .catch((err) => {
                console.error("Error stopping scanner:", err);
            });
    } else {
        scanActive.value = false;
        loading.value = false;
        error.value = null;
        scanResult.value = null;
    }
}

// Inisialisasi komponen
onMounted(() => {
    getCameras();
});

// Bersihkan scanner saat komponen di-unmount
onUnmounted(() => {
    if (scanner.value && scanActive.value) {
        scanner.value.stop().catch((err) => {
            console.error("Error stopping scanner on unmount:", err);
        });
    }
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Scan QR Code" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiQrcodeScan"
                title="Scan QR Code"
                main
            />

            <CardBox class="mb-6">
                <div class="flex flex-col items-center">
                    <div v-if="!scanResult && !error" class="w-full max-w-md">
                        <!-- Pilihan kamera -->
                        <div v-if="cameraList.length > 1" class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Pilih Kamera:</label
                            >
                            <select
                                v-model="selectedCamera"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                :disabled="scanActive"
                            >
                                <option
                                    v-for="camera in cameraList"
                                    :key="camera.id"
                                    :value="camera.id"
                                >
                                    {{ camera.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Area scanner -->
                        <div
                            id="qr-reader"
                            class="w-full border rounded-lg overflow-hidden"
                        ></div>

                        <div class="mt-4 flex justify-center">
                            <BaseButton
                                v-if="!scanActive"
                                label="Mulai Scan"
                                color="info"
                                :icon="mdiQrcodeScan"
                                @click="initScanner"
                                :disabled="loading"
                                class="mr-2"
                            />
                            <BaseButton
                                v-else
                                label="Berhenti Scan"
                                color="danger"
                                @click="resetScanner"
                                :disabled="loading"
                            />
                        </div>

                        <div v-if="loading" class="mt-4 text-center">
                            <p>Memindai QR code...</p>
                            <div
                                class="mt-2 w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"
                            ></div>
                        </div>
                    </div>

                    <!-- Error message -->
                    <div v-if="error" class="mt-4 w-full max-w-md">
                        <div
                            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert"
                        >
                            <div class="flex items-center">
                                <svg
                                    :data-v-5bf6e625="mdiAlertCircle"
                                    width="24"
                                    height="24"
                                    class="mr-2"
                                ></svg>
                                <span>{{ error }}</span>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-center">
                            <BaseButton
                                label="Coba Lagi"
                                color="info"
                                :icon="mdiRefresh"
                                @click="resetScanner"
                            />
                        </div>
                    </div>

                    <!-- Scan result -->
                    <div v-if="scanResult" class="mt-4 w-full max-w-md">
                        <div
                            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert"
                        >
                            <div class="flex items-center">
                                <svg
                                    :data-v-5bf6e625="mdiCheckCircle"
                                    width="24"
                                    height="24"
                                    class="mr-2"
                                ></svg>
                                <span>QR Code Valid!</span>
                            </div>
                        </div>

                        <div class="mt-4 bg-white shadow-md rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-2">
                                Detail Kunjungan:
                            </h3>

                            <div class="mb-2">
                                <span class="font-medium">Tujuan:</span>
                                {{ scanResult.visit_purpose }}
                            </div>

                            <div class="mb-2">
                                <span class="font-medium">Waktu Mulai:</span>
                                {{ formatDateTime(scanResult.start_date) }}
                            </div>

                            <div class="mb-2">
                                <span class="font-medium">Waktu Selesai:</span>
                                {{ formatDateTime(scanResult.end_date) }}
                            </div>

                            <div class="mb-2">
                                <span class="font-medium">Pengunjung:</span>
                                <ul class="list-disc list-inside ml-2">
                                    <li
                                        v-for="(
                                            visitor, index
                                        ) in scanResult.visitors"
                                        :key="index"
                                    >
                                        {{ visitor.name }}
                                        {{ visitor.is_pic ? "(PIC)" : "" }} -
                                        {{ visitor.company || "N/A" }}
                                    </li>
                                </ul>
                            </div>

                            <div
                                v-if="scanResult.check_in_time"
                                class="mt-4 text-green-600 font-medium"
                            >
                                Check-in berhasil pada:
                                {{ scanResult.check_in_time }}
                            </div>
                        </div>

                        <div class="mt-4 flex justify-center">
                            <BaseButton
                                label="Scan Baru"
                                color="info"
                                :icon="mdiRefresh"
                                @click="resetScanner"
                            />
                        </div>
                    </div>
                </div>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>

<style scoped>
#qr-reader {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
}

#qr-reader img {
    object-fit: cover;
}

/* Hide unnecessary elements from html5-qrcode library */
#qr-reader__dashboard_section_csr button {
    color: white !important;
    background: #4f46e5 !important;
    border: none !important;
    border-radius: 0.375rem !important;
    padding: 0.5rem 1rem !important;
}

#qr-reader__dashboard_section_fsr {
    display: none !important;
}
</style>
