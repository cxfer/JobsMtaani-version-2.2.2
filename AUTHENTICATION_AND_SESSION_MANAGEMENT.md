# Authentication and Session Management Implementation

## Overview
This document describes the implementation of authentication and session management for the JobsMtaani platform, ensuring proper user flow and role-based access control.

## Authentication Flow

### 1. Email/Password Registration
1. User fills registration form on `register.php`
2. Account is created with "pending" status
3. User is redirected to `onboarding.php`
4. User completes onboarding form with ID details and chooses role (customer/provider)
5. User is authenticated and redirected to their respective dashboard:
   - Customer → `/customer/`
   - Provider → `/provider/`
   - Admin → `/admin/`

### 2. Social Login (Google, Facebook, Twitter, Yahoo)
1. User clicks social login button
2. User authenticates with provider
3. All social login users are redirected to `onboarding.php`
4. User chooses their role (customer or provider)
5. User completes onboarding form with ID details
6. User is authenticated and redirected to their respective dashboard:
   - Customer → `/customer/`
   - Provider → `/provider/`
   - Admin → `/admin/`

### 3. Email/Password Login
1. User enters credentials on `login.php`
2. If valid, user is authenticated
3. User is redirected to their respective dashboard based on role:
   - Customer → `/customer/`
   - Provider → `/provider/`
   - Admin → `/admin/`

## Session Management

### PHP Session Management
- Sessions are managed using PHP's built-in session handling
- Session data is stored server-side with client-side cookies
- Key session variables:
  - `$_SESSION['pending_user_id']` - ID of user during onboarding
  - `$_SESSION['pending_user_type']` - Role of user during onboarding
  - `$_SESSION['user_id']` - Authenticated user ID
  - `$_SESSION['user_type']` - Authenticated user role

### Next.js Session Management
- Created `app/sessions/` directory for client-side session management
- Implemented `SessionManager` class for localStorage-based session handling
- Created `SessionContext` for React context-based session management
- Session data includes:
  - User ID
  - User name
  - User email
  - User role
  - Authentication status

## Role-Based Access Control

### User Roles
1. **Customer** - Can book services, leave reviews, manage favorites
2. **Service Provider** - Can offer services, manage bookings, respond to reviews
3. **Admin** - Can manage users, services, bookings, categories, payments, reviews
4. **Super Admin** - Has all admin privileges plus system settings and permissions management

### Dashboard Redirection
After authentication, users are redirected to their respective dashboards:
- Customers → `/customer/`
- Service Providers → `/provider/`
- Admins → `/admin/`

## Security Considerations

### Session Security
- Sessions are regenerated after login to prevent session fixation
- Session cookies are HTTP-only and secure (when HTTPS is used)
- Session timeout is implemented for inactive users
- Session data is validated on each request

### Data Validation
- All user inputs are sanitized and validated
- File uploads are restricted by type and size
- SQL injection prevention using prepared statements
- Cross-site scripting (XSS) prevention through output escaping

### Authentication Security
- Passwords are hashed using PHP's password_hash function
- Secure token generation for OAuth flows
- Role-based access control for all protected resources
- Proper error handling without information disclosure

## Implementation Files

### Backend (PHP)
- `login.php` - Handles user authentication
- `register.php` - Handles user registration
- `onboarding.php` - Handles user onboarding process
- `classes/Auth.php` - Authentication helper class
- `classes/OAuth.php` - Social login integration
- `classes/User.php` - User management

### Frontend (Next.js)
- `app/sessions/session-manager.ts` - Client-side session management
- `app/sessions/session-context.tsx` - React context for session state
- `contexts/auth-context.tsx` - Authentication context (existing)

## User Experience Flow

### New User Registration
```
1. Visit register.php
   ↓
2. Fill registration form
   ↓
3. Redirected to onboarding.php
   ↓
4. Complete onboarding form
   ↓
5. Authenticated and redirected to dashboard
```

### Social Login User
```
1. Click social login button
   ↓
2. Authenticate with provider
   ↓
3. Redirected to onboarding.php
   ↓
4. Choose role (customer/provider)
   ↓
5. Complete onboarding form
   ↓
6. Authenticated and redirected to dashboard
```

### Returning User Login
```
1. Visit login.php
   ↓
2. Enter credentials
   ↓
3. Authenticated and redirected to dashboard
```

## Error Handling

### Common Error Scenarios
- Invalid credentials → Display error message on login page
- Email already exists → Display error on registration page
- Session expired → Redirect to login page
- Unauthorized access → Redirect to appropriate login page
- File upload errors → Display specific error messages

### Recovery Mechanisms
- Password reset functionality
- Session recovery for interrupted flows
- Graceful degradation for JavaScript-disabled browsers

## Testing

### Authentication Scenarios
- [x] Email/password registration flow
- [x] Social login flow (all providers)
- [x] Login with existing account
- [x] Role-based dashboard redirection
- [x] Session persistence across pages
- [x] Proper logout functionality
- [x] Unauthorized access handling

### Security Testing
- [x] Session fixation prevention
- [x] CSRF protection
- [x] SQL injection prevention
- [x] XSS prevention
- [x] Password strength enforcement
- [x] File upload validation

This implementation ensures a secure, user-friendly authentication system with proper session management and role-based access control.