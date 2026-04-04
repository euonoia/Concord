# Concord – AI & Security Upgrade Plan

## Project Overview

**Concord** is a Hospital Management System (HMS) built with **Laravel, TiDB, and TailwindCSS**.  
It currently manages authentication, HR workflows, onboarding, training, and succession planning.  

**Upgrade Goal:** Enhance Concord to demonstrate **AI capabilities** (IBM AI Fundamentals badge) and **security best practices** (Cisco Security & Connectivity Support badge) to make it **portfolio-ready for internships**.

---

## AI Upgrade Plan

### Feature 1 – AI-Powered Employee Insights
- **Objective:** Predict employee training success or competency gaps using historical HR2 data.
- **Input:** Employee training scores, competency completion records, onboarding assessment data.
- **Output:** Probability scores and recommendations displayed on HR2 dashboard.
- **Implementation:**
  - Python ML model (scikit-learn or similar)
  - Laravel endpoint `/api/hr/predict` to serve predictions
  - Admin dashboard integration for visual display

---

### Feature 2 – AI Chat Assistant
- **Objective:** Provide staff or patients with an AI assistant for HR or hospital-related queries.
- **Input:** User text queries
- **Output:** Context-aware responses
- **Implementation:**
  - Integrate LLM API (e.g., OpenAI GPT)
  - Laravel endpoint `/api/chatbot` for secure communication
  - Authenticate users and log queries for auditing
  - Display responses on dashboard or patient portal

---

### Feature 3 – AI-Based Recommendations
- **Objective:** Suggest learning modules or career paths to employees based on their competency and training history.
- **Input:** Employee competency and training history
- **Output:** Recommended courses/modules
- **Implementation:** Simple AI model or rule-based system integrated into HR2 dashboard

---

## Security Upgrade Plan

### 1. Full Two-Factor Authentication (2FA)
- Enforce OTP-based authentication for high-privilege roles
- Limit OTP attempts (max 5)
- Expire sessions after inactivity

### 2. Role-Based Access Control
- Verify role permissions for all endpoints
- Ensure users can only access allowed dashboards and actions

### 3. Audit Logs
- Log critical actions such as login, training verification, ESS approvals
- Display logs on admin dashboard for review

### 4. Data Encryption
- Encrypt sensitive fields such as patient MRNs, emails, and passwords
- Use Laravel’s built-in encryption features

### 5. Anomaly Detection (Optional)
- Monitor unusual login attempts or ESS requests
- Flag anomalies for admin review

---

## Integration Plan

| Feature | Target Files / Modules | Notes |
|---------|----------------------|-------|
| AI Employee Insights | HR2 Dashboard, `/api/hr/predict`, Python ML scripts | Integrate ML model, expose API, display predictions |
| AI Chat Assistant | `/api/chatbot`, dashboard modules | Secure API integration, logging, dashboard display |
| AI Recommendations | HR2 Dashboard | Optional, simple AI or rule-based |
| 2FA | AuthController, Middleware | Enforce for high-privilege roles |
| Audit Logs | HR2 Controllers | Store logs in DB, create dashboard view |
| Data Encryption | User, Patient models | Encrypt sensitive fields using Laravel `encrypt()` |

---

## Expected Outcome

After this upgrade, **Concord will demonstrate**:

- Practical application of **AI skills** in HR workflows  
- Strong **security implementation** including 2FA, role access, and encryption  
- A professional **portfolio-ready project** that aligns with IBM and Cisco credentials