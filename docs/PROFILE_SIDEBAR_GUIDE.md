# Profile Sidebar with "Become an Owner" CTA

## Overview

ProfileSidebar adalah komponen sidebar yang tampil di halaman profile dengan fitur unggulan **"Become an Owner" CTA** yang memungkinkan tenant untuk upgrade menjadi owner dengan satu klik.

## Features

### 🎯 Core Features

1. **User Profile Display**
   - Avatar dengan initials otomatis
   - Nama dan email user
   - Role badge dengan warna berbeda per role

2. **Become Owner CTA** (Tenant Only)
   - Attractive gradient card dengan purple theme
   - Highlight benefits: kelola properti, terima pembayaran, analytics
   - Checklist fitur yang akan didapatkan
   - Dialog konfirmasi dengan detail lengkap

3. **Quick Links Navigation**
   - Dashboard link (sesuai role)
   - Role-specific links (Properties, Invoices, Contracts)
   - Settings link
   - Sign out button

4. **Responsive Design**
   - Sticky positioning di desktop
   - Mobile-friendly layout
   - Smooth transitions

---

## Component Structure

### ProfileSidebar.tsx

```typescript
interface ProfileSidebarProps {
  profile: {
    id: string;
    email: string;
    name: string;
    role: string;
    phone?: string;
    address?: string;
  };
  onNavigate: (path: string) => void;
  onRoleUpdate?: () => void;
}
```

**Key Props:**
- `profile`: User profile data
- `onNavigate`: Navigation handler
- `onRoleUpdate`: Callback dipanggil setelah upgrade sukses (refresh profile)

---

## Role-Based Display

### Tenant Role

**Shows:**
- ✅ "Become an Owner" CTA card (purple gradient)
- ✅ Benefits checklist (3 items)
- ✅ Quick links: Dashboard, Tagihan Saya, Kontrak Saya
- ✅ Settings & Logout

**CTA Benefits:**
1. Kelola properti kos Anda
2. Terima pembayaran online
3. Monitor pendapatan real-time

### Owner Role

**Shows:**
- ✅ Crown badge on role
- ✅ Quick links: Dashboard, Properti Saya, Tagihan
- ✅ Settings & Logout
- ❌ No "Become Owner" CTA (already owner)

### Admin Role

**Shows:**
- ✅ Shield badge on role
- ✅ Admin-specific quick links
- ✅ Settings & Logout
- ❌ No "Become Owner" CTA (admin can't upgrade)

---

## Backend Integration

### Endpoint: `/auth/upgrade-to-owner`

**Method:** POST

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: application/json
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Berhasil upgrade ke Owner",
  "user": {
    "id": "user-id",
    "email": "user@email.com",
    "name": "User Name",
    "role": "owner"
  }
}
```

**Response Error (400):**
```json
{
  "error": "Anda sudah terdaftar sebagai Owner"
}
```

**Response Error (403):**
```json
{
  "error": "Admin tidak dapat upgrade ke Owner"
}
```

### Backend Logic

```typescript
// 1. Verify user authentication
const { data: { user } } = await supabase.auth.getUser(accessToken);

// 2. Check current role
if (currentRole === "owner") return error;
if (currentRole === "admin") return error;

// 3. Update user metadata
await supabase.auth.admin.updateUserById(user.id, {
  user_metadata: {
    ...user.user_metadata,
    role: "owner",
  },
});

// 4. Store upgrade info in KV
await kv.set(`owner-upgrade:${user.id}`, {
  userId: user.id,
  previousRole: currentRole,
  newRole: "owner",
  upgradedAt: new Date().toISOString(),
});

// 5. Return success
return { success: true };
```

---

## User Flow

### Upgrade to Owner Flow

1. **User clicks "Daftar Sebagai Owner" button**
   - Purple CTA button in sidebar

2. **Confirmation dialog appears**
   - Shows benefits with checkmarks
   - 3 key features highlighted
   - Note: "Anda masih dapat menggunakan fitur tenant"

3. **User confirms upgrade**
   - Backend updates user metadata
   - KV store records upgrade info
   - Success toast notification

4. **Auto-redirect to owner dashboard**
   - 1.5 second delay to show success message
   - Navigate to `/owner`
   - Profile refreshed with new role

### Dialog Content

**Title:** "Daftar Sebagai Owner?"

**Benefits Shown:**
1. ✅ **Kelola Properti** - Tambah dan kelola properti kos Anda dengan mudah
2. ✅ **Terima Pembayaran** - Pembayaran QRIS otomatis dari penyewa
3. ✅ **Dashboard Analytics** - Monitor okupansi dan pendapatan real-time

**Note:** Anda masih dapat menggunakan fitur tenant setelah upgrade ke owner.

**Actions:**
- Cancel button (grey)
- "Ya, Daftar Sekarang" button (purple with Crown icon)

---

## UI Components Used

### shadcn/ui Components

- `Card`, `CardHeader`, `CardContent` - Container & sections
- `Avatar`, `AvatarFallback` - Profile picture with initials
- `Badge` - Role indicators
- `Button` - CTA & navigation buttons
- `AlertDialog` - Confirmation dialog
- `Separator` - Visual dividers
- `toast` from sonner - Notifications

### Lucide Icons

- `Crown` - Owner badge & upgrade CTA
- `User` - Tenant badge
- `Shield` - Admin badge
- `CheckCircle` - Benefits checklist
- `Sparkles` - Upgrade button decoration
- `Home`, `Building2`, `FileText` - Quick links
- `Settings`, `LogOut` - Settings & logout

---

## Styling & Theme

### Color Scheme

**Tenant CTA (Purple Theme):**
```css
bg-gradient-to-br from-purple-50 to-blue-50
border-purple-200
text-purple-900
bg-purple-600 (button)
```

**Role Badges:**
- Tenant: Blue (bg-blue-50, text-blue-700, border-blue-300)
- Owner: Purple (bg-purple-50, text-purple-700, border-purple-300)
- Admin: Red (bg-red-50, text-red-700, border-red-300)

**Avatar Gradient:**
```css
bg-gradient-to-br from-blue-500 to-purple-600
```

### Responsive Behavior

**Desktop (lg+):**
- Sticky positioning: `sticky top-24`
- Full sidebar width
- All content visible

**Mobile:**
- Not sticky (normal flow)
- Full width on small screens
- Compact spacing

---

## Integration with ProfilePage

### Usage in ProfilePage.tsx

```tsx
import { ProfileSidebar } from "./ProfileSidebar";

// In component
<div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {/* Sidebar - Left Column */}
  <div className="lg:col-span-1">
    {profile && (
      <ProfileSidebar 
        profile={profile} 
        onNavigate={onNavigate}
        onRoleUpdate={fetchProfile}
      />
    )}
  </div>

  {/* Main Content - Right Column */}
  <div className="lg:col-span-2 space-y-6">
    {/* Profile tabs, forms, etc */}
  </div>
</div>
```

**Layout:**
- 3-column grid on desktop (1 col sidebar + 2 col content)
- Single column on mobile
- Gap between columns

---

## Testing

### Quick Test

1. **Visit:** `/showcase-profile-sidebar`
2. **Switch roles:** Use tabs to preview tenant, owner, admin
3. **Click CTA:** Test "Daftar Sebagai Owner" button (demo mode)
4. **Check dialog:** Verify all benefits and styling

### Live Test with Real Data

1. Login as tenant: `tenant@demo.com` / `demo123`
2. Navigate to `/profile`
3. See ProfileSidebar on the left
4. Click "Daftar Sebagai Owner"
5. Confirm upgrade
6. Should redirect to `/owner` dashboard
7. Check that role badge changed to "Owner"

### Expected Behavior

**Before Upgrade:**
- Role badge: "Tenant" (blue)
- CTA card visible
- Tenant quick links shown

**After Upgrade:**
- Role badge: "Owner" (purple with Crown)
- CTA card hidden
- Owner quick links shown (Properti Saya, etc)
- User can still access tenant features

---

## Error Handling

### Already an Owner

**Scenario:** User clicks upgrade when already owner

**Response:**
```json
{ "error": "Anda sudah terdaftar sebagai Owner" }
```

**UI:** Toast error message, dialog stays closed

### Admin Trying to Upgrade

**Scenario:** Admin tries to become owner

**Response:**
```json
{ "error": "Admin tidak dapat upgrade ke Owner" }
```

**UI:** Toast error message

### Network Error

**Scenario:** API request fails

**UI:**
- Toast error: "Gagal upgrade ke Owner"
- Button re-enabled
- User can retry

---

## Accessibility

### ARIA & Keyboard Support

- Dialog accessible with keyboard (AlertDialog)
- Button focus states
- Proper heading hierarchy
- Alt text for icons (via aria-label)

### Screen Reader Support

- Role badges announced properly
- Button purposes clear
- Dialog content readable
- Success messages announced

---

## Performance

### Optimization

- Lazy state updates (useState)
- Minimal re-renders
- Efficient navigation
- Cached profile data

### Loading States

- Button shows spinner during upgrade: "Memproses..."
- Disabled state during API call
- No double submissions

---

## Customization

### Easy to Modify

**Change CTA Color:**
```tsx
// Purple theme
className="bg-purple-600 hover:bg-purple-700"

// Change to green
className="bg-green-600 hover:bg-green-700"
```

**Add More Benefits:**
```tsx
<div className="flex items-center gap-2">
  <CheckCircle className="h-3 w-3" />
  <span>Your new benefit here</span>
</div>
```

**Change Icon:**
```tsx
// Replace Crown with another icon
<Star className="h-5 w-5 text-purple-600" />
```

---

## Security Considerations

### Backend Validation

✅ **Token verification:** Only authenticated users can upgrade
✅ **Role checking:** Prevent admin → owner, owner → owner
✅ **Audit trail:** Store upgrade info in KV for tracking
✅ **User metadata:** Use Supabase Auth metadata (secure)

### Frontend Protection

✅ **Conditional rendering:** CTA only shown to eligible users
✅ **Loading states:** Prevent double submission
✅ **Error handling:** Graceful failure messages
✅ **Session refresh:** Auto-update after role change

---

## Future Enhancements

Possible improvements:

- [ ] Email notification on upgrade
- [ ] Welcome tour for new owners
- [ ] Owner verification process
- [ ] Payment/subscription for premium owner features
- [ ] Analytics tracking of upgrade conversions
- [ ] A/B testing different CTA copy
- [ ] Social proof (e.g., "Join 1000+ owners")

---

## File Locations

```
/components/ProfileSidebar.tsx          - Main component
/components/ProfilePage.tsx             - Integration example
/components/ProfileSidebarShowcase.tsx  - Demo/testing page
/supabase/functions/server/index.tsx    - Backend endpoint
/PROFILE_SIDEBAR_GUIDE.md              - This documentation
```

---

## Quick Reference

### API Endpoint
```
POST /make-server-dbd6b95a/auth/upgrade-to-owner
Authorization: Bearer {token}
```

### Test URL
```
/showcase-profile-sidebar
```

### Demo Account
```
Email: tenant@demo.com
Password: demo123
```

---

**Created:** October 31, 2025
**Version:** 1.0.0
**Status:** ✅ Production Ready
