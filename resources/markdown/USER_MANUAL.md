# 📚 USER MANUAL - ARTHADATA Platform v1.0.0

**Version**: 1.0.0  
**Last Updated**: 24 Maret 2026  
**Target Users**: UMKM, Petani, Pemerintah, Publik  

---

## 📖 DAFTAR ISI

1. [Overview Aplikasi](#overview-aplikasi)
2. [Panduan Cepat (Quick Start)](#panduan-cepat)
3. [Fitur-Fitur Utama](#fitur-fitur-utama)
4. [Panduan Per Halaman](#panduan-per-halaman)
5. [FAQ & Troubleshooting](#faq--troubleshooting)
6. [Kontak Support](#kontak-support)

---

## 🎯 OVERVIEW APLIKASI

### Apa itu ARTHADATA?

**ARTHADATA** adalah platform web yang menyediakan **data harga komoditas pangan real-time** yang bersih, terstandardisasi, dan siap pakai. Platform ini dirancang khusus untuk membantu:

- 👨‍🌾 **Petani** - Memantau harga pasar untuk optimasi jual-beli
- 🏪 **UMKM Pangan** - Perencanaan inventory & strategi pricing
- 📊 **Pembuat Kebijakan** - Monitoring inflasi untuk analisis ekonomi
- 👥 **Publik** - Transparansi harga komoditas pangan

### Masalah yang Dipecahkan

```
SEBELUM (Tanpa ARTHADATA):
❌ Data harga tersebar di berbagai sumber
❌ Sulit akses & tidak real-time
❌ Data mentah, tidak terstandar
❌ Anomali & error data
❌ Sulit buat keputusan bisnis

SESUDAH (Dengan ARTHADATA):
✅ Data terpusat & accessible
✅ Real-time updates
✅ Clean & standardized
✅ Auto-corrected & verified
✅ Siap untuk decision-making
```

### Fitur Utama

| Fitur | Deskripsi | Untuk Siapa |
|-------|-----------|------------|
| **Dashboard** | Monitoring harga nasional & regional real-time | Semua pengguna |
| **Data Center** | Filter & bersihkan data komoditas | Analyst & Data Team |
| **Simulator** | Hitung dampak harga untuk bisnis Anda | UMKM & Petani |
| **API Settings** | Integrasi data ke sistem lain | Developer & IT Team |

---

## 🚀 PANDUAN CEPAT

### Instalasi & Setup (5 menit)

#### 1. **Login ke Platform**

```
URL: http://localhost:8000
```

**Demo Account (untuk testing):**
```
Email:    akun-demo@pidi.id
Password: 123456
```

✅ Sistem akan **auto-login** saat pertama kali akses dashboard!

#### 2. **Navigasi Awal**

Setelah login, Anda akan melihat:
- **Sidebar Kiri** - Menu navigasi (Dashboard, Data Center, Simulator, API Settings, Profile)
- **Top Header** - Search bar & user menu
- **Main Content** - Konten halaman aktif

#### 3. **Panduan Navigasi**

```
🏠 Dashboard
   └─ Pantau inflasi & harga nasional/regional

📊 Data Center
   └─ Filter, bersihkan, verifikasi data komoditas

🎯 Simulator
   └─ Hitung dampak harga terhadap bisnis Anda

⚙️ API Settings
   └─ Kelola integrasi API & dokumentasi

👤 Profile
   └─ Kelola akun & preferensi
```

---

## 📱 FITUR-FITUR UTAMA

### 1. Dashboard - Monitoring Inflasi Nasional 📊

**Tujuan**: Melihat overview harga komoditas real-time

#### Cara Menggunakan:

**Step 1: Pilih Wilayah Pantauan**
```
Di bagian atas halaman ada dropdown "Wilayah Pantauan"

Pilihan:
- 🇮🇩 Nasional (Seluruh Indonesia) ← Default
- Jawa Barat
- DKI Jakarta
- Sumatera Utara
- (dan wilayah lainnya)

Setiap pemilihan akan update data secara otomatis
```

**Step 2: Baca Card Statistik**
```
Card 1 - Inflation Rate
├─ Menunjukkan persentase inflasi saat ini
├─ Contoh: 3.2%
└─ Trend indicator: ↑ atau ↓

Card 2 - Top Rising Commodity
├─ Komoditas dengan kenaikan harga tertinggi
├─ Contoh: Beras Rp 15.000/kg (+5.2%)
└─ Status: Animated pulse (urgent)

Card 3 - AI Accuracy
├─ Akurasi data cleaning sistem
├─ Contoh: 99.4%
└─ Menunjukkan kualitas data
```

**Step 3: Analisis Price Trend Chart**
```
📈 Chart menampilkan:
- Horizontal Axis: Tanggal (15 hari terakhir)
- Vertical Axis: Harga dalam Rupiah
- Multiple Lines: Berbagai komoditas

Fitur:
✅ Hover untuk detail
✅ Daily/Weekly toggle di atas chart
✅ Moving average calculation
✅ Trend visualization
```

**Step 4: Review Region Hotspots**
```
📍 Wilayah dengan fluktuasi harga tertinggi

Ditampilkan:
- Status badge (CRITICAL/MODERATE/STABLE)
- Region name
- Volatility percentage
- Market data feed (real-time updates)

⚠️ Red zone = Perlu perhatian khusus
```

#### Advanced: Blockchain Verification Modal

**Cara buka:**
```
Di dashboard, klik tombol "🔍 Verify Data" 
(jika tersedia di region yang dipilih)
```

**Di dalam modal:**
```
1. Lihat verified market data entries
2. Check blockchain verification status
3. Lihat transaction hash & block number
4. Understand Merkle tree validation
5. Download verification certificate
```

**Gunakan untuk:**
- Validasi authenticity data
- Audit trail tracking
- Compliance documentation

---

### 2. Data Center - Filter & Bersihkan Data 🧹

**Tujuan**: Manage kualitas data komoditas dengan advanced filtering & cleaning

#### Cara Menggunakan:

**Step 1: Filter Stream (Multi-Criteria Filtering)**

```
Klik tombol "🔽 Filter Stream" di bagian kanan atas
```

**Modal yang muncul memiliki:**

| Filter Type | Options | Contoh |
|------------|---------|--------|
| **Commodity** | Multi-select | Beras, Telur, Sayur |
| **Region** | Multi-select | Jawa Barat, Jakarta, etc |
| **Price Range** | Slider | Rp 5.000 - Rp 50.000 |
| **Volatility** | Slider | 0% - 20% |
| **Quality** | Radio | All / Verified / Pending |
| **Time Range** | Buttons | 24h / 7d / 30d / 90d |

**Cara pakai filter:**
```
1. Centang komoditas yang ingin dilihat
2. Pilih region (1 atau lebih)
3. Atur price range dengan slider
4. Set volatility threshold
5. Pilih data quality filter
6. Select time range
7. Klik "Apply Filters"
```

**Hasil:**
- Tabel diupdate sesuai filter
- Jumlah records berubah
- Data lebih relevant & focused

---

**Step 2: Run Batch Clean (Pembersihan Data Otomatis)**

```
Klik tombol "🧹 Run Batch Clean" (hijau)
```

**Proses yang terjadi:**
```
1. Modal muncul dengan progress bar
2. System scan semua data untuk anomali
3. Z-Score algorithm detect outliers (σ > 3.0)
4. Corrected values ditampilkan
5. Hasil ditampilkan dalam stats

Progress tracking:
├─ Records Processed: X
├─ Outliers Corrected: Y
├─ Processing Time: Z seconds
└─ Throughput: ~50K+ records/sec
```

**Gunakan jika:**
- Data terlihat ada yang aneh
- Ada spike harga yang suspicious
- Setelah input data manual dari field
- Regular maintenance (weekly)

---

**Step 3: Lihat Data Records (Tabel)**

```
📋 Tabel menampilkan:

Columns:
├─ Commodity: Nama komoditas
├─ Category: Kategori (Beras, Telur, dll)
├─ Price: Harga dalam Rp (formatted)
├─ Region: Wilayah data
└─ Status: ✓ Verified / ⏳ Pending

Features:
✅ Search bar di atas tabel
✅ Pagination (10 items per halaman)
✅ Sort by column header
✅ Hover untuk highlight row
```

---

**Step 4: Export Data (CSV)**

```
Klik tombol "📥 Export CSV" (di kanan bawah tabel)
```

**File yang dihasilkan:**
```
- Format: CSV (Excel-compatible)
- Nama: commodity-data.csv
- Isi: Semua data + metadata
- Gunakan untuk: Analysis di Excel, integrasi sistem lain

Kolom dalam CSV:
├─ Commodity Name
├─ Category
├─ Price (Rp)
├─ Region
└─ Status
```

---

**Step 5: Lihat Audit Logs (Blockchain Trail)**

```
Klik tombol "📜 Logs" (di kanan bawah tabel)
```

**Di dalam modal:**
```
📊 Audit Trail menampilkan:

Setiap entry:
├─ Action: CREATE / UPDATE / DELETE / VERIFY
├─ Commodity: Nama komoditas
├─ User: Siapa yang perform action
├─ Timestamp: Kapan terjadi
├─ IP Address: Dari mana
└─ Blockchain: Transaction hash & status

Filter options:
✅ By Action (CREATE, UPDATE, DELETE, VERIFY)
✅ By Date Range
✅ By User

Gunakan untuk:
- Track perubahan data
- Compliance & audit
- Identify anomalous changes
- User accountability
```

---

### 3. Simulator - Hitung Dampak Harga 🎯

**Tujuan**: Simulasikan dampak kenaikan/penurunan harga komoditas terhadap bisnis Anda

#### Cara Menggunakan:

**Step 1: Pahami Interface**

```
Halaman terbagi 2 bagian:

LEFT SIDE (Simulator Controls):
├─ Sliders untuk setiap komoditas
├─ Current market price
├─ Simulated price
└─ % Change calculation

RIGHT SIDE (Results Panel):
├─ Current Inflation Index
├─ Simulated Inflation Index
├─ Impact Analysis
└─ Report buttons
```

---

**Step 2: Adjust Slider untuk Simulasi**

```
Untuk setiap komoditas (Beras, Telur, Sayur, dll):

📊 Slider Scale:
0                    50                    100
|-----|-----|-----|-----|-----|-----|-----|
← Deflation     Normal    Critical Spike →

Contoh use case:

Scenario 1: Kenaikan Beras
├─ Geser slider Beras ke 60
├─ Harga: Rp 15.000 → Rp 18.000
├─ Change: +20%
└─ Impact: Inflation naik ke 3.8%

Scenario 2: Penurunan Telur
├─ Geser slider Telur ke 35
├─ Harga: Rp 30.000 → Rp 21.000
├─ Change: -30%
└─ Impact: Inflation turun ke 2.9%

Scenario 3: Krise Multi-Komoditas
├─ Naikkan semua slider ke 70+
├─ Hitung dampak total
└─ Understand worst-case scenario
```

---

**Step 3: Real-Time Impact Analysis**

```
Sistem OTOMATIS menghitung:

1. Per-Commodity Impact
   ├─ Change percentage
   ├─ New simulated price
   └─ Market comparison

2. Overall Inflation Impact
   ├─ Base inflation: 3.2%
   ├─ Simulated inflation: 3.8% (contoh)
   └─ Change: +0.6%

3. UMKM Impact
   ├─ COGS impact
   ├─ Margin impact
   └─ Revenue impact (jika ada integration)
```

---

**Step 4: Export & Save Report**

```
📤 Opsi Export:
├─ Export as PDF
├─ Export as CSV
└─ Export as JSON

💾 Opsi Save:
├─ Save Scenario
├─ Give it a name (contoh: "Scenario Beras Krisis")
├─ Set visibility (Public/Private)
└─ Reuse later
```

---

**Step 5: Reset & Try Again**

```
Untuk clear semua slider:
Klik tombol "🔄 Reset Simulation"

Semua slider kembali ke posisi tengah (50)
Impact metrics reset ke baseline
Siap untuk scenario baru
```

#### Contoh Skenario Nyata untuk UMKM

**Contoh 1: Toko Roti**
```
Produk: Roti gandum (harga Rp 5.000)
Bahan: 
- Tepung (40% dari COGS)
- Telur (20% dari COGS)
- Minyak (15% dari COGS)

Simulasi:
1. Geser Tepung slider ke 70 (+40%)
2. Geser Telur slider ke 65 (+30%)
3. Geser Minyak slider ke 75 (+50%)

Hasil:
- COGS naik dari Rp 2.500 → Rp 3.200
- Margin turun dari Rp 2.500 → Rp 1.800
- Decision: Kenaikan harga atau batch kecil?
```

**Contoh 2: Pedagang Sayur**
```
Produk: Sayur segar (mix bundle)
Bahan:
- Kangkung, Bayam, Sawi

Simulasi:
1. Monitor trend per jenis sayur
2. Geser slider sesuai market prediction
3. Calculate retail price untuk profit target

Benefit:
- Tahu kapan harga akan naik
- Plan inventory accordingly
- Adjust pricing strategy
```

---

### 4. API Settings - Integrasi Data 🔌

**Tujuan**: Kelola integrasi API untuk sistem pihak ketiga

#### Cara Menggunakan:

**Step 1: Lihat API Documentation**

```
Klik tab "📖 Documentation"
```

**Dokumentasi yang disediakan:**
```
1. Endpoint 1: GET /api/commodities
   ├─ Deskripsi: Ambil daftar komoditas
   ├─ Method: GET
   ├─ Parameters: region_id, category
   ├─ Response: JSON array
   └─ Example:
      curl -H "Authorization: Bearer TOKEN" \
           https://api.ARTHADATA.id/v1/commodities

2. Endpoint 2: POST /api/sync-price
   ├─ Deskripsi: Sync harga baru
   ├─ Method: POST
   ├─ Required: price, commodity_id, region_id
   ├─ Auth: API Key
   └─ Example:
      curl -X POST https://api.ARTHADATA.id/v1/sync-price \
           -H "X-API-Key: YOUR_KEY" \
           -d '{"price": 15000, "commodity_id": 1}'

3. Endpoint 3: GET /api/trends
   ├─ Deskripsi: Ambil price trends
   ├─ Method: GET
   ├─ Parameters: days, commodity_id
   ├─ Response: Historical data
   └─ Example:
      curl https://api.ARTHADATA.id/v1/trends?days=30
```

**Copy button:**
```
Setiap code example ada tombol "📋 Copy"
Klik untuk copy ke clipboard
```

---

**Step 2: Monitor API Usage**

```
Klik tab "📊 Usage Log"
```

**Metrics yang ditampilkan:**
```
📈 API Statistics:
├─ Total Calls: 1,234
├─ Success Rate: 99.8%
├─ Avg Response Time: 45ms
├─ Rate Limit: 1,000 calls/hour

📋 Recent Requests:
ID    | Endpoint           | Status | Time    | Response
------|-------------------|--------|---------|--------
1234  | GET /commodities  | 200    | 2:45 PM | 234ms
1233  | GET /trends       | 200    | 2:40 PM | 156ms
1232  | POST /sync-price  | 201    | 2:35 PM | 89ms

Filter options:
✅ By Date Range
✅ By Status Code
✅ By Endpoint
✅ Search by IP
```

---

**Step 3: Manage API Keys**

```
(Fitur untuk admin/developer)

Kelola:
├─ Generate new API key
├─ Revoke existing key
├─ Set rate limits
├─ View key usage stats
└─ Regenerate if compromised
```

---

## 📖 PANDUAN PER HALAMAN

### Dashboard Page

```
┌─────────────────────────────────────────┐
│ 🏠 DASHBOARD - Nasional Overview        │
├─────────────────────────────────────────┤
│                                         │
│ Header:                                 │
│ ├─ Title: "Nasional Overview"           │
│ ├─ Region Selector Dropdown             │
│ └─ Filter buttons                       │
│                                         │
│ Section 1: Key Statistics Cards         │
│ ├─ Inflation Rate (3.2%)                │
│ ├─ Top Rising (Beras +5.2%)             │
│ └─ AI Accuracy (99.4%)                  │
│                                         │
│ Section 2: Price Trend Chart            │
│ ├─ 15-day historical data               │
│ ├─ Daily/Weekly toggle                  │
│ └─ Multiple commodity lines             │
│                                         │
│ Section 3: Region Hotspots              │
│ ├─ Critical regions list                │
│ ├─ Status badges                        │
│ └─ Market data feed                     │
│                                         │
│ Actions:                                │
│ ├─ Click region untuk drill-down        │
│ ├─ Hover chart untuk detail             │
│ └─ Export dashboard data                │
└─────────────────────────────────────────┘
```

**Tips:**
- Cek setiap hari untuk monitoring
- Red zones = action required
- Use chart untuk identify trends
- Cross-reference dengan news

---

### Data Center Page

```
┌─────────────────────────────────────────┐
│ 📊 DATA CENTER - Washing & Normalization│
├─────────────────────────────────────────┤
│                                         │
│ Header + Action Buttons:                │
│ ├─ 🔽 Filter Stream                     │
│ ├─ 🧹 Run Batch Clean                   │
│ └─ Stats cards (Records, Outliers, etc) │
│                                         │
│ Search & Filter:                        │
│ ├─ Search bar (by commodity/category)   │
│ ├─ Filter results real-time             │
│ └─ Show matching records count          │
│                                         │
│ Data Table:                             │
│ ├─ Commodity | Category | Price | etc   │
│ ├─ Paginated (10 per page)              │
│ ├─ Sortable columns                     │
│ └─ Verified status indicator            │
│                                         │
│ Bottom Actions:                         │
│ ├─ Pagination controls                  │
│ ├─ 📥 Export CSV                        │
│ └─ 📜 View Logs (Audit trail)           │
│                                         │
│ Status Indicators:                      │
│ ├─ Green dot: Verified ✓                │
│ ├─ Yellow dot: Pending ⏳               │
│ └─ Red dot: Needs Review ⚠️             │
└─────────────────────────────────────────┘
```

**Workflow:**
1. Filter untuk data yang relevan
2. Run batch clean jika ada anomali
3. Export untuk analysis
4. Check audit logs untuk compliance

---

### Simulation Page

```
┌──────────────────────────────────────────┐
│ 🎯 INFLATION SIMULATOR                   │
├──────────────────────────────────────────┤
│                                          │
│ LEFT (Controls):               RIGHT     │
│ ┌────────────────┐            (Results) │
│ │ Commodity 1    │                      │
│ │ [0──•────100]  │            Current:  │
│ │ Price: Rp 15K  │            3.2%      │
│ │ Sim: Rp 18K    │                      │
│ │ Change: +20%   │            Simulated:│
│ │                │            3.8%      │
│ │ Commodity 2    │                      │
│ │ [0────•──100]  │            Buttons:  │
│ │ Price: Rp 30K  │            📤 Export │
│ │ Sim: Rp 21K    │            💾 Save   │
│ │ Change: -30%   │            🔄 Reset  │
│ │                │                      │
│ │ ... (more)     │                      │
│ └────────────────┘                      │
└──────────────────────────────────────────┘
```

**Workflow:**
1. Read current market prices (left column)
2. Adjust sliders untuk scenario
3. Watch impact calculation (right side)
4. Export report untuk presentation
5. Save scenario untuk future reference

---

### Profile Page

```
┌──────────────────────────────────────────┐
│ 👤 MY PROFILE                            │
├──────────────────────────────────────────┤
│                                          │
│ Account Information:                     │
│ ├─ Name: [Display Name]                  │
│ ├─ Email: [user@email.com]               │
│ ├─ Role: [User/Admin/Analyst]            │
│ └─ Account Status: Active                │
│                                          │
│ Preferences:                             │
│ ├─ Language: Bahasa Indonesia            │
│ ├─ Timezone: Asia/Jakarta                │
│ ├─ Data Export Format: CSV               │
│ └─ Notifications: Enabled                │
│                                          │
│ Account Actions:                         │
│ ├─ Edit Profile                          │
│ ├─ Change Password                       │
│ ├─ Download Data                         │
│ └─ Delete Account                        │
│                                          │
│ Security:                                │
│ ├─ Last Login: [date/time]               │
│ ├─ Active Sessions: 1                    │
│ ├─ 2FA Status: Disabled                  │
│ └─ Change Password                       │
│                                          │
│ Bottom:                                  │
│ └─ 🚪 Logout Button                      │
└──────────────────────────────────────────┘
```

---

## ❓ FAQ & TROUBLESHOOTING

### Q1: Bagaimana cara login?

**A:**
```
1. Buka http://localhost:8000
2. Masukkan email: akun-demo@pidi.id
3. Masukkan password: 123456
4. Klik "Sign In"
5. Otomatis redirect ke Dashboard

Jika dashboard langsung terload, berarti auto-login berhasil!
```

---

### Q2: Lupa password, bagaimana?

**A:**
```
Ada 3 opsi:

Option 1: Password Reset (jika email terverifikasi)
├─ Klik "Forgot Password?" di login page
├─ Masukkan email
├─ Check email untuk reset link
└─ Set password baru

Option 2: Contact Admin
├─ Email ke: support@ARTHADATA.id
├─ Sebutkan email account
├─ Tim support akan reset

Option 3: Emergency Access
├─ Hubungi super admin
├─ Verifikasi identitas
└─ Password sementara akan dikirim
```

---

### Q3: Mengapa data saya tidak tampil di filter?

**A:**
```
Kemungkinan penyebab & solusi:

❌ Masalah 1: Filter terlalu ketat
   ✅ Solusi: Reset filter, ubah range, coba lagi

❌ Masalah 2: Data belum di-seed ke database
   ✅ Solusi: Hub admin untuk jalankan seeder

❌ Masalah 3: Region tidak ada data
   ✅ Solusi: Pilih region lain atau Nasional (all)

❌ Masalah 4: Browser cache
   ✅ Solusi: Clear cache (Ctrl+Shift+Delete), reload page
```

---

### Q4: Bagaimana cara export data besar?

**A:**
```
Jika data > 10.000 rows:

1. Gunakan Filter Stream untuk subset data
   ├─ Pilih region tertentu
   ├─ Set date range
   └─ Focus pada komoditas spesifik

2. Klik Export CSV
   ├─ File akan didownload
   ├─ Bisa dibuka di Excel
   └─ Format sudah clean & standardized

3. Alternative: Gunakan API
   ├─ Lebih scalable untuk data besar
   ├─ Batch download support
   └─ Real-time access
```

---

### Q5: Bagaimana cara gunakan simulator untuk UMKM?

**A:**
```
Workflow Rekomendasi:

1. RESEARCH PHASE
   ├─ Kunjungi Dashboard
   ├─ Monitor current prices
   └─ Identify trend

2. SCENARIO PLANNING
   ├─ Buka Simulator
   ├─ Input komoditas yang jadi bahan baku
   ├─ Geser slider sesuai prediction
   └─ Monitor impact

3. DECISION MAKING
   ├─ Export report dengan simulasi
   ├─ Presentasikan ke management
   ├─ Decide: adjust pricing vs volume
   └─ Implement strategy

4. MONITORING
   ├─ Kembali ke Dashboard
   ├─ Compare simulasi vs actual
   ├─ Adjust strategy
   └─ Save lesson learned
```

---

### Q6: Apakah data real-time atau delayed?

**A:**
```
Data Status:

Real-time:
✅ Dashboard charts - Updated daily
✅ Statistics cards - Updated hourly
✅ Regional data - Near real-time

Near Real-time (5-15 min delay):
⏱️ Price feeds dari partners
⏱️ API sync dari external sources

Batch Updated:
📅 Government data - Weekly
📅 Market reports - Daily

Untuk critical operations:
- Pair dengan manual field verification
- Use blockchain verification untuk validate
- Monitor audit logs untuk data integrity
```

---

### Q7: Bagaimana jaminan akurasi data?

**A:**
```
Multi-Layer Verification:

Layer 1: Input Validation
├─ Type checking (must be number)
├─ Range checking (price > 0)
└─ Format validation (currency format)

Layer 2: Automated Cleaning
├─ Z-Score outlier detection (σ > 3.0)
├─ Duplicate removal
└─ Timestamp validation

Layer 3: Blockchain Verification
├─ Hash validation
├─ Merkle tree check
└─ Immutable audit trail

Layer 4: Manual Review
├─ Analyst verification
├─ Cross-check dengan manual data
└─ Approve untuk publish

Accuracy Metrics:
✅ System accuracy: 99.4%
✅ Outlier detection: 1.4% margin error
✅ Data integrity: 99.8%
```

---

### Q8: Bagaimana cara share data dengan team?

**A:**
```
3 Cara Share:

Option 1: Export CSV
├─ Klik Export button
├─ Kirim file via email
├─ Tim buka di Excel
└─ Simple tapi manual update

Option 2: Public Link
├─ Save scenario as Public
├─ Generate shareable link
├─ Tim access via link
└─ Real-time data (jika terupdate)

Option 3: API Integration
├─ Generate API key
├─ Share ke developer
├─ Integrate ke sistem mereka
└─ Automated & always up-to-date
```

---

### Q9: Bagaimana cara offline access?

**A:**
```
Offline Options:

Option 1: Download Data
├─ Export CSV dari Data Center
├─ Simpan ke drive lokal
├─ Buka offline di Excel
└─ Data static (tidak update)

Option 2: PDF Report
├─ Generate report di Simulator
├─ Export as PDF
├─ Print untuk field use
└─ Portable & shareable

Option 3: PWA (Future)
├─ (Coming soon)
├─ Download app ke device
├─ Work offline dengan sync otomatis
└─ Best untuk field teams

Current Recommendation:
→ Download CSV + PDF untuk field work
→ Sync ulang saat online
```

---

### Q10: Error "Data tidak muncul" - Bagaimana solusi?

**A:**
```
Troubleshooting Checklist:

Step 1: Check Browser
├─ Clear cache (Ctrl+Shift+Del)
├─ Hard refresh (Ctrl+F5)
└─ Try different browser

Step 2: Check Network
├─ Pastikan internet connected
├─ Check network tab di browser DevTools
├─ Lihat apakah API call error

Step 3: Check Filter
├─ Reset semua filter
├─ Try filter satu satu
├─ Expand date range

Step 4: Check Database
├─ Hubungi admin
├─ Check kalau seeder sudah jalan
├─ Verify data di database

Step 5: Contact Support
├─ Email error screenshot ke: support@ARTHADATA.id
├─ Include: browser, device, steps to reproduce
└─ Tim akan bantu investigate
```

---

## 💬 KONTAK SUPPORT

### Hubungi Tim Support

**Email**: support@ARTHADATA.id  
**Phone**: +62-21-XXXX-XXXX  
**Support Hours**: Monday-Friday, 09:00-17:00 WIB  

### Response Time
```
🟢 Critical Issues: < 1 hour
🟡 High Priority: < 4 hours
🟠 Medium Priority: < 24 hours
🔵 Low Priority: < 48 hours
```

### Jenis Support

| Kategori | Contoh | Waktu Respon |
|----------|--------|--------------|
| **Critical** | Server down, data loss | < 1 jam |
| **High** | Login error, data tidak akurat | < 4 jam |
| **Medium** | UI issue, feature request | < 24 jam |
| **Low** | Documentation, general questions | < 48 jam |

### Reporting a Bug

```
Kirim email dengan format:

Subject: [BUG] Nama Issue
────────────────────────────────

Bug Description:
Jelaskan apa yang terjadi

Steps to Reproduce:
1. Do this
2. Then this
3. Finally this

Expected Result:
Apa yang seharusnya terjadi

Actual Result:
Apa yang terjadi sebenarnya

Environment:
- Browser: Chrome v120
- Device: Windows 10
- Network: WiFi
- Account: [email]

Screenshots/Logs:
[Attach file jika ada]
```

---

## 📚 Recursos Tambahan

### Documentation
- 📖 [Dokumentasi Lengkap](./DOCUMENTATION_INDEX.md)
- 🔧 [Panduan Teknis](./FILE_INDEX.md)
- 🚀 [Deployment Guide](./DEPLOYMENT_GUIDE.md)

### Video Tutorials
- 🎥 Dashboard Overview (5 min) - Coming soon
- 🎥 Data Center Tutorial (8 min) - Coming soon
- 🎥 Simulator Walkthrough (7 min) - Coming soon

### API Documentation
- 📡 [API Reference](./ApiSettings.vue)
- 🔑 [Authentication Guide](./LOGIN_FIX_REPORT.md)
- ⚡ [Quick Start Guide](./QUICK_START.md)

### Community
- 💬 [Forum Komunitas](https://forum.ARTHADATA.id)
- 📱 [Telegram Group](https://t.me/ARTHADATA-id)
- 🐦 [Twitter Updates](https://twitter.com/ARTHADATA)

---

## ✅ Checklists & Quick Reference

### Daily Monitoring Checklist

```
✓ Morning (08:00 WIB)
  ├─ Login ke platform
  ├─ Check Dashboard untuk overview
  ├─ Lihat price changes vs kemarin
  └─ Identifikasi anomali

✓ Midday (12:00 WIB)
  ├─ Update inventory based on prices
  ├─ Quick simulator check untuk noon decisions
  └─ Note any critical changes

✓ Afternoon (16:00 WIB)
  ├─ Export daily report
  ├─ Backup data locally
  └─ Plan next day strategies
```

### Weekly Analysis Checklist

```
✓ Setiap Senin pagi
  ├─ Export last week's data
  ├─ Analyze trends
  ├─ Run simulator untuk week ahead
  ├─ Share insights dengan team
  └─ Update procurement strategy
```

### Monthly Review Checklist

```
✓ Akhir bulan
  ├─ Generate monthly report
  ├─ Compare simulations vs actual
  ├─ Calculate savings/losses
  ├─ Identify improvement areas
  └─ Plan next month strategy
```

---

**Document Version**: 1.0  
**Last Updated**: 24 Maret 2026  
**Valid Until**: 24 Maret 2027

---

Untuk update atau feedback tentang manual ini, hubungi: support@ARTHADATA.id
