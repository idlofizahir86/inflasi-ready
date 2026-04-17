# 🚀 Quick Start Guide - ARTHADATA Platform

## 📌 System Requirements

- **PHP**: 8.2+
- **Node.js**: 18+
- **npm**: 9+
- **Laravel**: 11.x
- **Database**: SQLite (default) atau MySQL

---

## 🔧 Installation & Setup

### 1. Clone/Navigate to Project
```bash
cd d:\_IZR\__only_only_dopi\dopi_only\ARTHADATA
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Setup Environment
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed
```

### 4. Build Frontend Assets
```bash
# Development mode (with hot reload)
npm run dev

# Production build
npm run build
```

### 5. Start Development Server
```bash
# Terminal 1: Start Vite dev server
npm run dev

# Terminal 2: Start Laravel development server
php artisan serve
```

**Access application**: `http://localhost:8000`

---

## 👤 Demo Account Credentials

- **Email**: `akun-demo@pidi.id`
- **Password**: `123456`
- **Auto-Login**: ✅ Enabled on first dashboard visit
- **Auto-Fill**: ✅ Enabled after logout

---

## 📱 Dashboard Overview

### Navigation Structure
```
Sidebar (Left):
├── 🏠 Dashboard          → Overview & Regional Heatmap
├── 📊 Data Center        → Data Washing & Normalization
├── 📈 Simulation         → Inflation Impact Analysis
├── 💻 API Settings       → Developer Tools
├── 👤 Profile            → User Settings
└── 🚪 Logout (Bottom)    → Session Termination

Top Navbar:
├── 🔍 Search Bar         → Commodity Search (Ready)
├── 🔔 Notifications      → System Alerts
└── 👤 User Menu          → Profile Quick Links
```

---

## 🎯 Feature Quick Access

### 1. Dashboard Page
```
Click "View Detailed Map" button
→ Opens Regional Heatmap Modal
  - Click any region for details
  - See MoM inflation & volatility
  - View active markets & data freshness

Click "Verify Market Data" button (FAB)
→ Opens Blockchain Verification Modal
  - Browse recent verified entries
  - Click entry to see details
  - View transaction hash & block number
```

### 2. Data Center Page
```
Top Buttons:
- "Filter Stream" → Advanced Multi-Criteria Filtering
  - Select commodities, regions
  - Set price & volatility ranges
  - Choose data quality level
  
- "Run Batch Clean" → Z-Score Data Washing
  - Watch real-time progress
  - See records processed count
  - View outliers corrected
  - Get success summary

Bottom Actions:
- "Export CSV" → Download filtered data
  - Includes all selected columns
  - Proper object serialization (region fix)
  
- "Logs" → Audit Trail Viewer
  - Filter by action type
  - Click entry for detail view
  - See blockchain transaction info
```

### 3. Price Trend Chart
```
Toggle "Daily" / "Weekly"
→ Chart updates with:
  - Daily view: individual day prices
  - Weekly view: 7-day aggregated data
  - Responsive legend with commodities
```

---

## 📊 Data Sources

### Sample Data Included
```javascript
Commodities:
- Cabai Merah (Red Chili)
- Beras Medium (Medium Rice)
- Minyak Goreng (Cooking Oil)
- Bawang Merah (Red Onion)
- Daging Ayam (Chicken Meat)
- Telur Ayam (Chicken Eggs)

Regions:
- DKI Jakarta
- Jawa Timur
- Sumatera Utara
```

### Mock Data Statistics
```
- Total Records: 142,809
- Outliers Detected: 1,402 (~1%)
- Data Integrity: 99.8%
- Processing Time: <3 seconds
```

---

## 🔐 Security Features

### Built-in Protections
- ✅ CSRF token validation
- ✅ Session management
- ✅ User authentication
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Rate limiting ready

### Audit Trail
- Every action logged (CREATE/UPDATE/DELETE/VERIFY)
- User & IP tracking
- Blockchain-backed immutable records
- Searchable by action type

---

## 🐛 Troubleshooting

### Issue: "404 Page Not Found"
**Solution**: Run `php artisan route:list` to verify routes are registered

### Issue: "CSRF Token Mismatch"
**Solution**: Clear browser cache & refresh page

### Issue: "Database Connection Error"
**Solution**: 
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Issue: "Module Not Found (npm)"
**Solution**:
```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Issue: "Laravel Route Not Working"
**Solution**:
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

---

## 📚 API Endpoints Reference

### Dashboard
```
GET /dashboard
  Response: Dashboard stats, chart data, commodities
```

### Data Center
```
GET /datacenter
  Response: Commodity list, filter options
  
POST /api/clean-batch
  Body: { cleaningParams }
  Response: { recordsProcessed, outliersCorrected, timeTaken }
```

### Profile
```
GET /profile/edit
  Response: User profile form
  
PATCH /profile
  Body: { name, email, phone }
  Response: Updated profile
  
DELETE /profile
  Response: Account deletion confirmation
```

### Authentication
```
POST /login
  Body: { email, password, remember }
  Response: Authenticated session
  
POST /logout
  Response: Redirect to /login with demo credentials
```

---

## 🎨 Customization Guide

### Change Primary Color
**File**: `resources/css/app.css`
```css
:root {
  --color-primary: #004532; /* Change this to your color */
}
```

### Change Demo Credentials
**File**: `app/Http/Controllers/DashboardController.php`
```php
$demoEmail = 'your-email@example.com';
$demoPassword = 'your-password-here';
```

### Add New Commodity
**File**: `database/seeders/DatabaseSeeder.php`
```php
Commodity::create([
    'name' => 'New Commodity',
    'category' => 'Category',
    'unit' => 'kg',
]);
```

### Modify Chart Display
**File**: `resources/js/Components/PriceTrendChart.vue`
```javascript
// Adjust colors in commodityColors object
// Modify chart options in initChart() function
```

---

## 📈 Performance Tips

### For Better Responsiveness
1. Enable caching:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. Use production build:
```bash
npm run build
```

3. Enable gzip compression in server config

### For Development
Keep dev server running:
```bash
npm run dev
```

This enables:
- Hot module replacement
- Instant style updates
- Source maps for debugging

---

## 📞 Support Resources

### Documentation Files
- `README.md` - Project overview
- `IMPROVEMENTS.md` - Detailed features
- `IMPLEMENTATION_COMPLETE.md` - This session summary
- `CHANGELOG.md` - Version history

### Code Navigation
```
app/
├── Http/Controllers/     → Request handlers
├── Models/               → Data models
└── Middleware/           → Request filters

resources/
├── js/Components/        → Reusable Vue components
├── js/Pages/             → Page components
├── js/Layouts/           → Layout wrappers
└── css/                  → Stylesheets

routes/
├── web.php               → Web routes
├── api.php               → API routes
└── auth.php              → Auth routes
```

---

## 🚀 Deployment Checklist

### Before Production
- [ ] Run full test suite: `php artisan test`
- [ ] Check build size: `npm run build`
- [ ] Verify database backups
- [ ] Enable HTTPS/SSL
- [ ] Configure CDN for static assets
- [ ] Setup error monitoring (Sentry)
- [ ] Configure email notifications
- [ ] Test on multiple browsers
- [ ] Performance audit with Lighthouse

### Production Commands
```bash
# Build optimized bundle
npm run build

# Run migrations
php artisan migrate --force

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start application
php artisan serve --host=0.0.0.0 --port=80
```

---

## 💡 Tips & Tricks

### Keyboard Shortcuts
- `Ctrl+/` - Focus search bar
- `Ctrl+Enter` - Submit forms in modals
- `Esc` - Close open modals

### Pro Features
- **Batch Filter**: Select multiple options to narrow results
- **Real-time Search**: Results update as you type
- **Weekly Aggregation**: Better for trend analysis
- **Audit Trail**: Full transparency for compliance

### Data Export Best Practices
1. Apply filters first for targeted export
2. Export in batches for large datasets
3. Keep CSV for Excel analysis
4. Archive exports regularly

---

## 📧 Contact & Feedback

For issues, suggestions, or contributions:
- Check existing documentation
- Review CHANGELOG.md for recent changes
- Submit detailed bug reports with screenshots

---

**Version**: 1.0.0  
**Last Updated**: 2026-03-24  
**Status**: ✅ Production Ready

**Happy Monitoring! 🎉**
