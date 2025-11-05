# 🚀 Quick Start Guide for Developers

Panduan cepat untuk developers yang ingin memahami dan mengembangkan KostIn SuperWebApp.

## 📋 Table of Contents

1. [Setup Development Environment](#setup-development-environment)
2. [Project Architecture](#project-architecture)
3. [Component Structure](#component-structure)
4. [API Integration](#api-integration)
5. [Adding New Features](#adding-new-features)
6. [Best Practices](#best-practices)
7. [Common Tasks](#common-tasks)

---

## 🛠️ Setup Development Environment

### Prerequisites
```bash
- Node.js 18+ installed
- Git installed
- Supabase account
- Code editor (VSCode recommended)
```

### Initial Setup

```bash
# 1. Clone & install
git clone https://github.com/yourusername/kostin.git
cd kostin
npm install

# 2. Start dev server
npm run dev
# App akan berjalan di http://localhost:3000 (or port lain)
```

### Supabase Configuration

1. **Create Project**
   - Go to [supabase.com](https://supabase.com)
   - Create new project
   - Note: Project URL & Anon Key

2. **Update Config**
   ```typescript
   // utils/supabase/info.tsx
   export const projectId = "your-project-id";
   export const publicAnonKey = "your-anon-key";
   ```

3. **Setup Auth Providers**
   - Navigate to project settings
   - Enable Email/Password auth
   - Enable Google OAuth (optional)
   - Follow `/setup-oauth` guide

4. **Upload Secrets**
   ```bash
   # Via Supabase Dashboard > Settings > Edge Functions
   SUPABASE_URL=your-url
   SUPABASE_ANON_KEY=your-anon-key
   SUPABASE_SERVICE_ROLE_KEY=your-service-key
   MIDTRANS_SERVER_KEY=your-midtrans-key
   MIDTRANS_CLIENT_KEY=your-midtrans-client
   ```

---

## 🏗️ Project Architecture

### High-Level Overview

```
Frontend (React)
    ↓
App.tsx (Routing)
    ↓
Components
    ↓
Supabase Client
    ↓
Edge Functions (Backend)
    ↓
KV Store / Auth
```

### Directory Structure

```
/
├── App.tsx                    # Main app + routing logic
├── components/               
│   ├── [Feature]Page.tsx     # Full page components
│   ├── [Feature]*.tsx        # Feature-specific components
│   └── ui/                   # Reusable UI components (shadcn)
├── supabase/
│   └── functions/server/
│       ├── index.tsx         # All API endpoints
│       ├── payment.tsx       # Midtrans integration
│       └── kv_store.tsx      # KV utility (READ-ONLY)
├── utils/
│   └── supabase/
│       ├── client.tsx        # Supabase client factory
│       └── info.tsx          # Project config
└── styles/
    └── globals.css           # Global styles + design tokens
```

### Data Flow

```
User Action
    ↓
Component State Update
    ↓
API Call (fetch)
    ↓
Server Endpoint (/make-server-dbd6b95a/*)
    ↓
Authentication Check
    ↓
Business Logic
    ↓
KV Store / Database
    ↓
Response to Frontend
    ↓
UI Update + Toast Notification
```

---

## 🧩 Component Structure

### Standard Page Component Template

```typescript
import { useState, useEffect } from "react";
import { Button } from "./ui/button";
import { Card, CardContent } from "./ui/card";
import { toast } from "sonner@2.0.3";
import { projectId } from "../utils/supabase/info";
import { createClient } from "../utils/supabase/client";

interface [Feature]PageProps {
  onNavigate: (path: string) => void;
}

export function [Feature]Page({ onNavigate }: [Feature]PageProps) {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const supabase = createClient();

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const { data: { session } } = await supabase.auth.getSession();
      
      if (!session?.access_token) {
        toast.error("Silakan login terlebih dahulu");
        onNavigate("/login");
        return;
      }

      const response = await fetch(
        `https://${projectId}.supabase.co/functions/v1/make-server-dbd6b95a/endpoint`,
        {
          headers: {
            Authorization: `Bearer ${session.access_token}`,
          },
        }
      );

      const result = await response.json();

      if (response.ok) {
        setData(result.data);
      } else {
        throw new Error(result.error);
      }
    } catch (error: any) {
      console.error("Fetch error:", error);
      toast.error(error.message || "Gagal memuat data");
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-gray-600">Memuat...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container mx-auto px-4 py-8">
        <h1 className="mb-8">Page Title</h1>
        {/* Your content here */}
      </div>
    </div>
  );
}
```

### Key Patterns

#### 1. Authentication Check
```typescript
const { data: { session } } = await supabase.auth.getSession();

if (!session?.access_token) {
  toast.error("Silakan login terlebih dahulu");
  onNavigate("/login");
  return;
}
```

#### 2. API Call Pattern
```typescript
const response = await fetch(
  `https://${projectId}.supabase.co/functions/v1/make-server-dbd6b95a/endpoint`,
  {
    method: "GET", // or POST, PUT, DELETE
    headers: {
      "Content-Type": "application/json", // for POST/PUT
      Authorization: `Bearer ${session.access_token}`,
    },
    body: JSON.stringify(data), // for POST/PUT
  }
);

const result = await response.json();

if (!response.ok) {
  throw new Error(result.error);
}
```

#### 3. Error Handling
```typescript
try {
  // API call
} catch (error: any) {
  console.error("Operation error:", error);
  toast.error(error.message || "Gagal melakukan operasi");
}
```

#### 4. Loading States
```typescript
const [loading, setLoading] = useState(true);

// Before API call
setLoading(true);

// After API call (in finally block)
finally {
  setLoading(false);
}
```

---

## 🔌 API Integration

### Backend Endpoint Structure

```typescript
// supabase/functions/server/index.tsx

app.get("/make-server-dbd6b95a/endpoint", async (c) => {
  try {
    // 1. Get auth token
    const accessToken = c.req.header('Authorization')?.split(' ')[1];
    
    if (!accessToken) {
      return c.json({ error: "Missing authorization token" }, 401);
    }

    // 2. Verify user
    const { data: { user }, error: authError } = await supabase.auth.getUser(accessToken);

    if (authError || !user) {
      return c.json({ error: "Unauthorized" }, 401);
    }

    // 3. Check role if needed
    const userRole = user.user_metadata?.role;
    if (userRole !== 'admin') {
      return c.json({ error: "Forbidden" }, 403);
    }

    // 4. Business logic
    const data = await kv.get('some-key');

    // 5. Return response
    return c.json({
      success: true,
      data,
    });
  } catch (error: any) {
    console.error("Endpoint error:", error);
    return c.json({ error: error.message || "Internal server error" }, 500);
  }
});
```

### KV Store Operations

```typescript
// Read
const data = await kv.get('key');
const dataArray = await kv.getByPrefix('prefix:');

// Write
await kv.set('key', { data: 'value' });
await kv.mset(['key1', 'key2'], [data1, data2]);

// Delete
await kv.del('key');
await kv.mdel(['key1', 'key2']);
```

**⚠️ IMPORTANT**: Never modify `/supabase/functions/server/kv_store.tsx`!

---

## ➕ Adding New Features

### Step-by-Step Guide

#### 1. Plan the Feature
```
- Define user stories
- Design data model
- List required endpoints
- Sketch UI components
```

#### 2. Create Backend Endpoints

```typescript
// Add to /supabase/functions/server/index.tsx

// GET endpoint
app.get("/make-server-dbd6b95a/feature", async (c) => {
  // Implementation
});

// POST endpoint
app.post("/make-server-dbd6b95a/feature", async (c) => {
  // Implementation
});
```

#### 3. Create Frontend Component

```bash
# Create component file
touch components/FeaturePage.tsx
```

```typescript
// Implement component using template above
export function FeaturePage({ onNavigate }: FeaturePageProps) {
  // Your implementation
}
```

#### 4. Add Routing

```typescript
// App.tsx

// Import
import { FeaturePage } from "./components/FeaturePage";

// Add route in renderPage()
if (currentPath === '/feature') {
  return <FeaturePage onNavigate={handleNavigate} />;
}
```

#### 5. Add Navigation

```typescript
// Update Navbar.tsx or add button
<Button onClick={() => onNavigate('/feature')}>
  Feature
</Button>
```

#### 6. Test Feature

```bash
1. Test as guest (should redirect to login if protected)
2. Test as tenant
3. Test as owner
4. Test as admin
5. Test error scenarios
6. Test mobile responsive
```

---

## ✅ Best Practices

### Code Style

```typescript
// ✅ Good - Descriptive names
const fetchUserProperties = async () => { ... }

// ❌ Bad - Unclear names
const fetch = async () => { ... }

// ✅ Good - Early returns
if (!session) {
  toast.error("Login required");
  return;
}

// ❌ Bad - Deep nesting
if (session) {
  if (user) {
    if (role === 'admin') {
      // code
    }
  }
}
```

### Error Handling

```typescript
// ✅ Good - Specific error messages
catch (error: any) {
  console.error("Create property error:", error);
  toast.error(error.message || "Gagal membuat properti");
}

// ❌ Bad - Generic errors
catch (error) {
  toast.error("Error");
}
```

### State Management

```typescript
// ✅ Good - Separate concerns
const [properties, setProperties] = useState([]);
const [loading, setLoading] = useState(false);
const [error, setError] = useState("");

// ❌ Bad - Complex state object
const [state, setState] = useState({
  properties: [],
  loading: false,
  error: ""
});
```

### Performance

```typescript
// ✅ Good - Cleanup intervals
useEffect(() => {
  const interval = setInterval(fetchData, 3000);
  return () => clearInterval(interval);
}, []);

// ❌ Bad - No cleanup
useEffect(() => {
  setInterval(fetchData, 3000);
}, []);
```

---

## 🔧 Common Tasks

### Task 1: Add New Field to Property

**Backend:**
```typescript
// Property object automatically accepts new fields
const property = {
  ...existingFields,
  newField: value, // Just add it!
};

await kv.set(`property:${id}`, property);
```

**Frontend:**
```typescript
// Add to form state
const [formData, setFormData] = useState({
  ...existingFields,
  newField: "",
});

// Add input field
<Input
  value={formData.newField}
  onChange={(e) => setFormData({ ...formData, newField: e.target.value })}
/>
```

### Task 2: Add New Status to Ticket

**Backend:**
```typescript
// Just use the new status string
const ticket = {
  ...existingTicket,
  status: "new_status",
};
```

**Frontend:**
```typescript
// Add to status config
const statusConfig = {
  ...existingStatuses,
  new_status: {
    icon: Icon,
    label: "New Status",
    className: "bg-color text-color"
  }
};
```

### Task 3: Create New Role

**1. Update Type:**
```typescript
// App.tsx
type UserRole = 'guest' | 'tenant' | 'owner' | 'admin' | 'moderator';
```

**2. Add Nav Items:**
```typescript
// Navbar.tsx
const navItems = {
  ...existing,
  moderator: [
    { label: 'Dashboard', icon: Home, path: '/moderator' }
  ]
};
```

**3. Add Routes:**
```typescript
// App.tsx
if (currentPath === '/moderator' && user.role === 'moderator') {
  return <ModeratorDashboard onNavigate={handleNavigate} />;
}
```

**4. Add Backend Check:**
```typescript
// server/index.tsx
const userRole = user.user_metadata?.role;
if (userRole !== 'moderator' && userRole !== 'admin') {
  return c.json({ error: "Unauthorized" }, 403);
}
```

### Task 4: Add Toast Notification

```typescript
import { toast } from "sonner@2.0.3";

// Success
toast.success("Operation successful!");

// Error
toast.error("Operation failed!");

// Info
toast.info("FYI: Something happened");

// Warning
toast.warning("Be careful!");

// With duration
toast.success("Saved!", { duration: 5000 });
```

### Task 5: Add New shadcn Component

```bash
# List available components in /components/ui/
# Import and use:

import { NewComponent } from "./ui/new-component";

<NewComponent>Content</NewComponent>
```

---

## 🐛 Debugging Tips

### Debug Auth Issues

```typescript
// Check session
const { data: { session } } = await supabase.auth.getSession();
console.log("Session:", session);
console.log("User:", session?.user);
console.log("Role:", session?.user?.user_metadata?.role);
```

### Debug API Calls

```typescript
// Log request
console.log("Request URL:", url);
console.log("Request headers:", headers);
console.log("Request body:", body);

// Log response
console.log("Response status:", response.status);
console.log("Response data:", await response.json());
```

### Debug KV Store

```typescript
// Check what's stored
const allKeys = await kv.getByPrefix('');
console.log("All stored data:", allKeys);

// Check specific key
const data = await kv.get('specific-key');
console.log("Data for key:", data);
```

### Common Errors

**Error: "Missing authorization token"**
- Solution: Check if `Authorization` header is sent
- Verify session.access_token is valid

**Error: "Property not found"**
- Solution: Check property ID format
- Verify property exists in KV store
- Check property status (active vs pending)

**Error: "Cannot read property of undefined"**
- Solution: Add null checks
- Use optional chaining (`data?.property`)
- Provide default values

---

## 📚 Resources

### Official Docs
- [React Docs](https://react.dev)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [shadcn/ui](https://ui.shadcn.com)
- [Supabase Docs](https://supabase.com/docs)
- [Hono Docs](https://hono.dev)

### Project Docs
- [NEW_FEATURES_GUIDE.md](./NEW_FEATURES_GUIDE.md)
- [CHANGELOG_ALL_FEATURES.md](./CHANGELOG_ALL_FEATURES.md)
- [README.md](./README.md)

### Code Examples
- Check existing components for patterns
- All components follow similar structure
- Backend endpoints use consistent format

---

## 🎯 Next Steps

1. **Explore codebase**
   - Read through existing components
   - Understand data flow
   - Check backend endpoints

2. **Make small changes**
   - Update UI text
   - Add new button
   - Change colors

3. **Build new feature**
   - Follow "Adding New Features" guide
   - Start with simple CRUD
   - Add complexity gradually

4. **Optimize**
   - Improve performance
   - Enhance UX
   - Add features

---

## 💡 Pro Tips

- **Use TypeScript** - Type safety saves debugging time
- **Console.log liberally** - During development, log everything
- **Test incrementally** - Don't build everything before testing
- **Read error messages** - They usually tell you what's wrong
- **Check Network tab** - See actual API requests/responses
- **Use React DevTools** - Inspect component state & props
- **Keep components small** - Easier to understand & maintain
- **Reuse components** - DRY principle
- **Comment complex logic** - Help future you
- **Git commit often** - Small, focused commits

---

## 🤝 Getting Help

1. **Check documentation** first
2. **Search codebase** for similar implementations
3. **Console log** to understand what's happening
4. **Use React DevTools** to inspect state
5. **Check Network tab** for API issues
6. **Read error stack traces** carefully
7. **Google the error** - Someone likely had it before
8. **Ask the team** - Don't struggle alone

---

<div align="center">

**Happy Coding! 🚀**

Made with ❤️ by the KostIn Team

</div>
