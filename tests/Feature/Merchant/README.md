# Merchant Dashboard Tests Summary

## Tenant Management Tests
- **Basic CRUD Operations**
  - View tenant index page
  - Create a tenant with validation
  - View tenant details
  - Update tenant information
  - Delete tenant (and cascade associated feeds)

- **Authorization Tests**
  - User can only view their own tenants
  - User cannot access other users' tenant details
  - User cannot update tenants they don't own
  - User cannot delete tenants they don't own
  - Tenant name is unique per user

## API Token Tests
- **Basic Token Management**
  - Generate an API token
  - Revoke an API token

- **API Token Security Tests**
  - Token has sufficient entropy and length
  - Regenerating a token changes its value
  - Tokens for inactive tenants cannot be generated/revoked
  - Admin users can manage tokens for any tenant
  - Tokens are protected in tenant listing but visible in detail view

## Feed Management Tests
- **Basic CRUD Operations**
  - View feeds index page
  - Create feed with validation
  - View feed details
  - Update feed information
  - Delete feed

- **Feed Validation Tests**
  - URL validation
  - Format validation (google_merchant, csv, xml, json)
  - Sync schedule validation (hourly, daily, weekly, monthly)

- **Feed Sync Tests**
  - Trigger manual feed sync
  - Filter feeds by tenant
  - Feed shows correct next sync time
  - User can't create or sync feeds for tenants they don't own

- **Feed Sync Edge Cases**
  - Sync updates timestamps correctly
  - Next sync calculated based on schedule
  - Error handling during sync
  - Inactive feeds/tenants can't be synced
  - Authorization for feed operations

## Merchant Dashboard Tests
- **Dashboard Statistics**
  - Shows summary statistics (tenants, feeds, active vs. total)
  - Unauthenticated users can't access dashboard
  - Dashboard links to tenant and feed management
  - Dashboard uses correct Inertia component

## Integration Tests
- Tenant deletion cascades to associated feeds
- Authorization across the entire system
- Validation rules are enforced

This test suite comprehensively covers all the functionality required for the Merchant Dashboard feature, ensuring robustness and security in tenant management, API token handling, and feed configuration and synchronization.
