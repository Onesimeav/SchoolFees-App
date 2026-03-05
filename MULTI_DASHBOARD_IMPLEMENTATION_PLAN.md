# Multi-User Dashboard System Implementation Plan

## System Architecture Overview

### User Types
1. **Admins** - Full system access
2. **Accountant** - Financial management
3. **Parents/Students** - Fee payments and tracking
4. **College Employee** - Employee functions
5. **Secretary** - Administrative tasks

### Dashboard Structure
1. **Admin Dashboard** (`/admin`) - For Admins only
2. **Portal Dashboard** (`/portal`) - For Parents/Students
3. **Staff Dashboard** (`/staff`) - Shared by Accountant, College Employee, and Secretary with role-based access control

---

## Phase 1: Database Schema & User Roles

### 1.1 Create Role & Permission System

**Option A: Use Spatie Laravel-Permission** (Recommended)
- Industry-standard package
- Well-integrated with Filament via `filament/spatie-laravel-permissions-plugin`
- Flexible role and permission management

**Option B: Custom Role System**
- Simple enum-based roles
- Lighter weight but less flexible

**Migration needed:**
```sql
- Add 'role' column to users table (enum or string)
- Optional: permissions table for granular access control
```

**User Model additions:**
```php
- role/type field
- Relationships to role/permission tables
- Helper methods: isAdmin(), isAccountant(), etc.
```

---

## Phase 2: Create Multiple Filament Panels

### 2.1 Admin Panel ✓ (Already exists)
- Path: `/admin`
- Users: Admins only
- Full access to all resources

### 2.2 Portal Panel (New)
- Path: `/portal`
- Users: Parents/Students
- Resources: View fees, make payments, download receipts

### 2.3 Staff Panel (New)
- Path: `/staff`
- Users: Accountant, College Employee, Secretary
- Resources: Based on role permissions
- Implement resource-level authorization

**Implementation:**
```bash
php artisan make:filament-panel portal
php artisan make:filament-panel staff
```

---

## Phase 3: Authentication & Authorization

### 3.1 Panel-Level Access Control

Each panel needs middleware/guards to:
- Restrict which users can access which panel
- Redirect users to their appropriate dashboard after login

**Implementation locations:**
- `app/Providers/Filament/AdminPanelProvider.php` - Add `->authGuard()` if needed
- `app/Providers/Filament/PortalPanelProvider.php` - Configure for parents/students
- `app/Providers/Filament/StaffPanelProvider.php` - Configure for staff roles

### 3.2 Custom Authentication Logic

Override the login process to redirect users based on role:
- Admin → `/admin`
- Parent/Student → `/portal`
- Accountant/Employee/Secretary → `/staff`

---

## Phase 4: Resource-Level Access Control (Staff Panel)

### 4.1 Implement Authorization Policies

For each resource in the Staff panel, define policies:
```php
- viewAny() - Can this user see this resource?
- view() - Can view individual records?
- create() - Can create new records?
- update() - Can edit records?
- delete() - Can delete records?
```

**Examples:**
- **Financial Reports Resource**: Only Accountant can access
- **Student Records Resource**: Secretary and Accountant can access
- **Employee Management**: Only Secretary can access

### 4.2 Navigation Control

Hide/show menu items based on user role:
```php
->visible(fn() => auth()->user()->role === 'accountant')
```

---

## Phase 5: Custom Dashboards

### 5.1 Admin Dashboard
- System overview widgets
- User management
- Global statistics

### 5.2 Portal Dashboard
- Student fee balance
- Payment history
- Upcoming payments

### 5.3 Staff Dashboard
- Role-specific widgets
- Conditional widget display based on permissions

---

## Phase 6: Testing & Security

### 6.1 Security Considerations
- Ensure users cannot access panels they don't have permission for
- Test direct URL access attempts
- Verify resource authorization works correctly
- Test cross-role data access

### 6.2 User Experience
- Proper redirects after login
- Clear error messages for unauthorized access
- Logout functionality across all panels

---

## Detailed Step-by-Step Implementation

### Step 1: Install Spatie Permissions Package
```bash
composer require spatie/laravel-permission
composer require filament/spatie-laravel-permissions-plugin
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Step 2: Create Roles Migration
Add role column to users table or use Spatie's role system

### Step 3: Update User Model
Add HasRoles trait (Spatie) or custom role methods

### Step 4: Create Panel Providers
```bash
php artisan make:filament-panel portal
php artisan make:filament-panel staff
```

### Step 5: Configure Panel Access
In each PanelProvider:
- Set authentication middleware
- Configure authorization callbacks
- Set up custom login redirects

### Step 6: Create Resources
Create resources under appropriate namespaces:
- `app/Filament/Admin/Resources/` - Admin-only resources
- `app/Filament/Portal/Resources/` - Parent/Student resources
- `app/Filament/Staff/Resources/` - Staff resources with policies

### Step 7: Implement Authorization
- Create Laravel Policies for each resource
- Add authorization methods to resources
- Configure navigation visibility

### Step 8: Create Custom Dashboards
- Override default dashboard pages per panel
- Add role-specific widgets

### Step 9: Seed Database
Create seeder with:
- Default admin user
- Sample roles and permissions
- Test users for each role type

### Step 10: Testing
- Test login for each user type
- Verify dashboard access
- Test resource permissions
- Verify navigation visibility

---

## File Structure After Implementation

```
app/
├── Filament/
│   ├── Admin/
│   │   ├── Resources/     (Admin-only resources)
│   │   ├── Pages/
│   │   └── Widgets/
│   ├── Portal/
│   │   ├── Resources/     (Parent/Student resources)
│   │   ├── Pages/
│   │   └── Widgets/
│   └── Staff/
│       ├── Resources/     (Multi-role staff resources)
│       ├── Pages/
│       └── Widgets/
├── Models/
│   └── User.php          (with HasRoles trait)
├── Policies/             (Authorization policies)
│   ├── FinancialReportPolicy.php
│   └── ...
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php   ✓
        ├── PortalPanelProvider.php  (new)
        └── StaffPanelProvider.php   (new)

database/
├── migrations/
│   └── add_role_to_users_table.php
└── seeders/
    └── RoleAndPermissionSeeder.php
```

---

## Key Design Decisions to Confirm

1. **Permission Package**: Spatie Laravel-Permission or custom role enum?
2. **Staff Panel Resources**: Should all staff see the same resources with different permissions, or completely different resources?
3. **Parent/Student Distinction**: Are Parents and Students the same user type or separate?
4. **Multi-tenancy**: Do you need school/institution-level separation?
5. **Authentication**: Single login form that redirects based on role, or separate login pages per panel?

---

## Panel Configuration Summary

| Panel | Path | User Types | Access Level |
|-------|------|------------|--------------|
| Admin | `/admin` | Admin | Full access to all resources |
| Portal | `/portal` | Parents/Students | Limited to fee viewing and payments |
| Staff | `/staff` | Accountant, Employee, Secretary | Role-based access to specific resources |

---

## Authorization Matrix Example

| Resource | Admin | Accountant | Secretary | Employee | Parent/Student |
|----------|-------|------------|-----------|----------|----------------|
| User Management | Full | - | View | - | - |
| Financial Reports | Full | Full | View | - | - |
| Student Records | Full | View/Edit | Full | View | Own record only |
| Fee Payments | Full | Full | View | - | Own payments only |
| Employee Management | Full | - | Full | View | - |

---

## Next Steps

1. Review and confirm design decisions above
2. Choose between Spatie Permission or custom role system
3. Begin implementation starting with Phase 1
4. Create and run database migrations
5. Set up panel providers
6. Implement authorization logic
7. Create resources for each panel
8. Test thoroughly before deployment