<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between no-print">
            <h2 class="font-serif text-2xl font-bold text-[#1D3557] tracking-tight">
                QR Code Portal Pendaftaran
            </h2>
            <span class="text-xs font-semibold text-[#457B9D] bg-white/80 px-3.5 py-1.5 rounded-full shadow-xs border border-[#E2ECF5]">
                RSUD Blambangan
            </span>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center">
        <!-- Premium Claymorphism Card -->
        <div id="printable-card" class="relative max-w-md w-full bg-gradient-to-br from-white via-[#F8FBFF] to-[#F0F5FA] backdrop-blur-2xl p-8 sm:p-10 rounded-[2.5rem] shadow-[0_20px_50px_rgba(29,53,87,0.12)] border border-white/90 text-center space-y-6 transition-all">
            
            <!-- Soft Glow Accents -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#A8DADC]/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-[#457B9D]/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-2">
                <span class="inline-block px-3.5 py-1 bg-[#F0F5FA] text-[#457B9D] font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-[#E2ECF5] shadow-2xs">
                    RSUD Blambangan
                </span>
                <h3 class="font-serif text-2xl font-bold text-[#1D3557] tracking-tight">QR Code Portal Pendaftaran</h3>
                <p class="text-xs text-slate-500 font-light max-w-xs mx-auto leading-relaxed">
                    Scan untuk membuka halaman pengajuan jadwal mandiri pasien.
                </p>
            </div>
            
            <!-- Sunken Clay QR Code Box -->
            <div class="relative z-10 inline-block p-5 bg-[#F8FBFF] rounded-[2rem] shadow-[inset_5px_5px_12px_rgba(155,177,200,0.25),inset_-5px_-5px_12px_rgba(255,255,255,0.95)] border border-[#E2ECF5]">
                <div id="qrcode" class="flex justify-center"></div>
                <!-- Logo di Tengah -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-14 h-14 bg-white rounded-2xl p-1.5 shadow-[0_8px_20px_rgba(29,53,87,0.15)] flex items-center justify-center border border-[#E2ECF5]">
                        <img src="{{ asset('images/IMGLOGO.png') }}" alt="Logo Cathlab" class="w-full h-full object-contain rounded-xl">
                    </div>
                </div>
            </div>

            <!-- URL Badge -->
            <div class="relative z-10 text-[11px] font-mono text-[#457B9D] bg-[#F0F5FA] p-3 rounded-2xl border border-[#E2ECF5] break-all shadow-2xs">
                https://cathlabbanyuwangi.my.id/portal-pasien/daftar-pasien
            </div>

            <!-- Action Button -->
            <div class="relative z-10 pt-2 no-print">
                <button onclick="window.print()" class="w-full py-3.5 bg-[#1D3557] hover:bg-[#457B9D] text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all cursor-pointer flex items-center justify-center space-x-2">
                    <span>🖨️ Cetak / Simpan QR Code</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Print Stylesheet (Clean layout isolation to prevent blank/white print pages) -->
    <style>
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            /* Sembunyikan elemen navigasi, sidebar, header dashboard, dan tombol */
            header, nav, aside, footer, .no-print {
                display: none !important;
            }
            /* Posisikan kartu di tengah kertas cetak */
            #printable-card {
                position: fixed !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 95mm !important;
                max-width: 95mm !important;
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                background: #ffffff !important;
                margin: 0 !important;
                padding: 24px !important;
            }
        }
    </style>

    <!-- qrcode.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "https://cathlabbanyuwangi.my.id/portal-pasien/daftar-pasien",
                width: 180,
                height: 180,
                colorDark: "#1D3557",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H 
            });
        });
    </script>
</x-app-layout>