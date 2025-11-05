# 🚀 Quick Start - Browse Kost Feature

## Akses Cepat

### 1️⃣ Untuk Guest (Belum Login)
```
1. Buka aplikasi → Lihat navbar
2. Klik "Cari Kos" di navbar
   ATAU
3. Di homepage, klik tombol "Cari Kos" di hero section
   ATAU
4. Scroll ke "Kos Terbaru" → Klik "Lihat Semua"
```

### 2️⃣ Untuk Authenticated Users (Sudah Login)
```
1. Login sebagai Tenant/Owner/Admin
2. Klik "Cari Kos" di navbar (available untuk semua role)
3. Mulai browsing dan filtering
```

## 🎯 Testing Checklist

### Basic Navigation
- [ ] Guest bisa akses `/browse-kost`
- [ ] Tenant bisa akses browse kost
- [ ] Owner bisa akses browse kost
- [ ] Admin bisa akses browse kost
- [ ] Link di navbar berfungsi
- [ ] Button di homepage berfungsi

### Search & Filter
- [ ] Search by nama kos
- [ ] Search by lokasi
- [ ] Filter by city (dropdown)
- [ ] Filter by price range (slider)
- [ ] Filter by type (Putra/Putri/Campur)
- [ ] Filter by facilities (checkboxes)
- [ ] Multiple filters bersamaan

### Sorting
- [ ] Sort by Paling Relevan
- [ ] Sort by Harga Terendah
- [ ] Sort by Harga Tertinggi
- [ ] Sort by Rating Tertinggi
- [ ] Sort by Paling Banyak Tersedia

### UI/UX
- [ ] Active filters ditampilkan dengan badge
- [ ] Quick remove individual filter (X button)
- [ ] Clear all filters button
- [ ] Filter count badge di filter button
- [ ] Empty state saat no results
- [ ] Property cards clickable
- [ ] Mobile responsive
- [ ] Filter sheet di mobile

### Profile System
- [ ] Profile button di navbar berfungsi
- [ ] Dropdown menu muncul
- [ ] Link ke profile page
- [ ] Logout berfungsi
- [ ] Tenant bisa lihat CTA upgrade to owner

## 🔍 Test Scenarios

### Scenario 1: Guest User Browsing
```
1. Buka app tanpa login
2. Klik "Cari Kos" di navbar
3. Search "Melati"
4. Verify: 1 hasil (Kos Melati Residence)
5. Clear search
6. Filter: Type = "Putri"
7. Verify: 2 hasil (Mawar Indah Putri, Dahlia Executive)
```

### Scenario 2: Price Range Filter
```
1. Buka /browse-kost
2. Buka filter panel (mobile: sheet, desktop: inline)
3. Set price range: 1,000,000 - 1,500,000
4. Verify: 3 properties dalam range tersebut
5. Clear filter
6. Set price range: 1,800,000 - 2,000,000
7. Verify: 2 properties (Anggrek Premium, Dahlia Executive)
```

### Scenario 3: Multiple Filters
```
1. Buka /browse-kost
2. Select city: "Bogor"
3. Select type: "Campur"
4. Select facility: "AC"
5. Set price max: 1,500,000
6. Verify: Results sesuai semua criteria
7. Check active filters badges
8. Remove type filter dengan X
9. Verify: Results updated
```

### Scenario 4: Sorting Test
```
1. Buka /browse-kost
2. Sort by "Harga Terendah"
3. Verify: Teratai Strategis (1M) di posisi pertama
4. Sort by "Harga Tertinggi"
5. Verify: Dahlia Executive (2M) di posisi pertama
6. Sort by "Rating Tertinggi"
7. Verify: Dahlia Executive (5.0) di posisi pertama
```

### Scenario 5: Mobile Experience
```
1. Switch to mobile view (< 768px)
2. Klik "Filter" button
3. Verify: Sheet slides from right
4. Apply filters
5. Close sheet
6. Verify: Active filters visible
7. Test search input
8. Test city selector
```

### Scenario 6: Empty State
```
1. Buka /browse-kost
2. Search "XYZ123NonExistent"
3. Verify: Empty state message
4. Verify: "Hapus Semua Filter" button visible
5. Click button
6. Verify: All properties shown again
```

### Scenario 7: Navigation from Browse to Detail
```
1. Buka /browse-kost
2. Click any property card
3. Verify: Navigates to /property/{id}
4. Click back button
5. Verify: Returns to browse page
```

### Scenario 8: Profile & Logout
```
1. Login as Tenant
2. Click profile button di navbar
3. Verify: Dropdown menu muncul
4. Click "Profil"
5. Verify: Navigate to profile page
6. Verify: ProfileSidebar shows upgrade to owner CTA
7. Click "Keluar"
8. Verify: Logged out & redirected
```

## 📊 Expected Results Summary

### Mock Data (6 Properties):
1. **Kos Melati Residence** - Rp 1,200,000 - Campur - 4.5★
2. **Kos Mawar Indah Putri** - Rp 1,500,000 - Putri - 4.8★
3. **Kos Anggrek Premium** - Rp 1,800,000 - Campur - 4.9★
4. **Kos Teratai Strategis Putra** - Rp 1,000,000 - Putra - 4.3★
5. **Kos Kenanga Modern** - Rp 1,350,000 - Campur - 4.6★
6. **Kos Dahlia Executive** - Rp 2,000,000 - Putri - 5.0★

### Filter Combinations Examples:
- Type "Putri": 2 results (Mawar, Dahlia)
- Type "Putra": 1 result (Teratai)
- Type "Campur": 3 results (Melati, Anggrek, Kenanga)
- Price < 1,500,000: 4 results
- Price > 1,500,000: 2 results
- City "Bogor": 6 results (all)
- Has "AC": 5 results
- Has "Kamar Mandi Dalam": 4 results

## 🐛 Known Limitations (Current Version)

1. **Mock Data Only** - Belum connect ke backend
2. **No Pagination** - All results shown at once
3. **No Saved State** - Filters reset on page refresh
4. **No Favorites** - Can't save favorite properties yet
5. **No Map View** - Text-based location only
6. **No Image Gallery** - Single image per property

## 🔗 Related Documentation

- **Full Guide**: `/BROWSE_KOST_GUIDE.md`
- **Changelog**: `/CHANGELOG_BROWSE_KOST.md`
- **Profile Guide**: `/PROFILE_SIDEBAR_GUIDE.md`

## 💡 Tips

1. **Mobile Testing**: Use browser DevTools untuk test responsive
2. **Filter Logic**: Filters are cumulative (AND operation)
3. **Search**: Case-insensitive, searches name/address/district
4. **Sort**: Applies after filtering
5. **Performance**: Current implementation handles small datasets well

---

**Happy Testing! 🎉**
