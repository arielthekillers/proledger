# ProLedger 🚀
**Collaborative Project Financial Management System**

ProLedger is a robust, lightweight, and secure web-based application designed to manage financial records for collaborative projects involving multiple stakeholders (Investors/Pemodal and Companies/Perusahaan). It provides real-time transparency into project funds, expenses, and profit distribution.

![ProLedger Dashboard](https://via.placeholder.com/800x400?text=ProLedger+Dashboard+Preview)

## ✨ Key Features

### 1. 📊 Interactive Dashboard
- **Real-time Statistics**: View total active projects, registered investors, total capital, and net profit/loss at a glance.
- **Cash Flow Overview**: Visual summary of total income vs. expenses.
- **Quick Actions**: One-click access to create projects, transactions, or investments (Admin only).

### 2. 💼 Project Management (Paket Kegiatan)
- **Centralized Data**: Manage all project details including Pagu (Ceiling), RAB, and Status.
- **Automated Calculations**: Automatically calculates "Modal Pasal" (13% of Pagu) and "Modal Belanja".
- **Status Tracking**: Track project lifecycle (Draft, Running, Finished).

### 3. 👥 Multi-Stakeholder Investment
- **Investor Management**: Register and manage "Pemodal" (Investors).
- **Pooled Capital**: Support for multiple investors per single project.
- **Automatic Share Calculation**: System calculates each investor's share percentage based on their contribution.

### 4. 💸 Financial Tracking
- **Granular Transactions**: Record every inflow and outflow with precision.
- **Categories**: Organized by "Pasal", "Modal Belanja", "Operasional", etc.
- **Transparency**: Every transaction is linked to a specific project and timestamped.

### 5. 🛡️ Role-Based Access Control (RBAC)
- **Admin**: Full access to all features (Create/Edit/Delete).
- **Perusahaan (Company)**: View-only access to their specific assigned projects.
- **Pemodal (Investor)**: Read-only access restricted strictly to their own investment portfolios and reports.

### 6. 📈 Reporting (Rekap Keuangan)
- **Project Recap**: Detailed financial health check per project.
- **Investor Reports**: Personalized reports showing individual capital vs. current value.
- **Profit/Loss Analysis**: Automatic calculation of current balance and financial performance.

---

## 🛠️ Technology Stack
- **Backend**: Native PHP 8.2+ (Custom Lightweight MVC Architecture)
- **Database**: MySQL 8.0
- **Frontend**: HTML5, Tailwind CSS (Modern UI)
- **Icons**: Ionicons
- **Security**: 
    - CSRF Protection
    - Password Hashing (Bcrypt)
    - Session-based Authentication
    - Environment Variable Configuration (.env)

---

## 🚀 Installation & Setup

### Prerequisites
- PHP >= 8.0
- MySQL
- Web Server (Apache/Nginx/IIS)

### Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/yourusername/proledger.git
    cd proledger
    ```

2.  **Configure Environment**
    Create a `.env` file from the example:
    ```bash
    cp .env.example .env
    ```
    Open `.env` and configure your database usage:
    ```ini
    APP_ENV=local
    DB_DATABASE=proledger
    DB_USERNAME=root
    DB_PASSWORD=your_password
    ```

3.  **Setup Database**
    - Create a new MySQL database named `proledger`.
    - Import the database schema from `database/proledger.sql` (if available) or run migrations.

4.  **Run the Application**
    If using PHP built-in server:
    ```bash
    php -S localhost:8000 -t public
    ```
    Access the app at `http://localhost:8000`.

---

## 🔒 Security Note
This project uses a custom authentication system. Ensure `APP_ENV` is set to `production` when deploying live to disable debug overlays and error reporting.

---

## 📄 License
Private / Proprietary.
