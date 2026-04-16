# Project Technical Documentation (SOPs)

## Purpose
This document serves as the centralized technical reference for the project. It outlines:
- Implemented features and modules
- Standard operating procedures for development
- Known limitations and items out of scope
- Contributor guidelines

## Index
- [1. Feature Overview](#1-feature-overview)
- [2. Standard Operating Procedures (SOPs)](#2-standard-operating-procedures-sops)
- [3. Not in Scope](#3-not-in-scope)
- [4. Contributor Guidelines](#4-contributor-guidelines)
- [5. Notes & References](#5-notes--references)

---

## 1. Feature Overview

### Super Admin
- Login / Forgot Password / Logout
- CRUD Sub-Admin
- Business Listings
- Dashboard Metrics

### Business Admin
- Work Order Management
- Technician Scheduling (Calendar-based)
- Subscription & Billing (Stripe)
- Client CRUD
- Checklist Templates

### Technician App
- View Assigned Jobs
- Offline Job Completion
- Chemical Logs, Photos, Signature Upload
- Profile Update

---

## 2. Standard Operating Procedures (SOPs)

### API Versioning
- All APIs must use `/api/v1/` prefix.
- Future upgrades will version APIs without breaking previous clients.

### REST Guidelines
- Use correct HTTP verbs:  
  - `GET` for fetch  
  - `POST` for creation  
  - `PUT`/`PATCH` for updates  
  - `DELETE` for deletion  
- Avoid verbs in URL paths (`/update`, `/create`)

### Job Scheduling
- Manual drag-and-drop based scheduling via calendar UI.
- No auto-assignment logic in this phase.

### File Uploads
- All media files uploaded to AWS S3 via backend.
- Max file size: 5MB, accepted formats: JPG, PNG.

### Offline Mode
- Technician data saved locally.
- Sync to server when internet is available.

---

## 3. Not in Scope (Phase 1)
- Push notifications / SMS integration
- Automatic job scheduling / AI-based job suggestion
- Role-based access management for sub-admins
- Localization / Multi-language support
- Downgrade logic for subscriptions

---

## 4. Contributor Guidelines

- All updates should be logged in the changelog section at bottom.
- Use markdown formatting.
- Keep sections modular and readable.
- Confirm feature alignment with the latest BDD and SDD.

> _Note: All team members have edit access to this file. Please sync with leads before making major changes._

---

## 5. Notes & References
- BDD Files Location: `/features/`
- API Postman Collection: `/docs/api/postman_collection.json`
- Infra Architecture: See `/docs/infrastructure_diagram.png`
