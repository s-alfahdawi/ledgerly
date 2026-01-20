# Ledgerly

**Your Personal Financial Command Center**

Ledgerly is a modern, mobile-first personal finance management application built with Laravel 12 and Blade views. Take complete control of your finances by tracking income and expenses, managing multiple income sources, organizing expense types, visualizing spending patterns with beautiful charts, and collaborating with family or team members through secure role-based permissions.

**Free Forever • Open Source • Multi-Currency (IQD & USD)**

## Features

### Core Functionality

- **Multi-tenant Workspaces**: Share your workspace with family members or team members with role-based permissions (Owner, Admin, Member, Viewer)
- **Transaction Management**: Record income, expenses, and transfers between income sources with full transaction history
- **Income Source Management**: Track multiple income sources (cash, bank accounts, cards, etc.) with opening balances
- **Expense Type Management**: Organize transactions by expense types (rent, food, gas, utilities, etc.)
- **Financial Reports**: View monthly summaries, category breakdowns, wallet balances, and export PDF statements
- **Visual Analytics**: Interactive charts including:
  - Income vs Expense Donut Chart showing spending ratio
  - Monthly Income vs Expense Trend Line Chart
  - Expense Types Breakdown Pie Chart
  - Income Sources Breakdown Bar Chart
- **PDF Export**: Export monthly summaries and custom date-range statements
- **Email Invitations**: Invite unregistered users via email with secure token-based registration
- **Currency Support**: Choose between IQD (Iraqi Dinar) or USD (US Dollar) at registration and change it anytime from settings
- **API Access**: RESTful API with Sanctum token authentication (v1)

### Role-Based Permissions

- **Owner**: Full access to all features including settings and member management
- **Admin**: Can manage transactions, income sources, expense types, reports, and members (but not settings)
- **Member**: Can only view and create/update transactions and view reports (no access to income sources, expense types, settings, or members)
- **Viewer**: Read-only access to transactions and reports

### User Experience

- **Modern UI**: Built with Minia admin template for dashboard and Invoza template for landing page
- **Mobile-First Design**: Fully responsive interface optimized for mobile devices
- **Search & Filtering**: Advanced filtering and search capabilities on all list pages
- **Sorting & Pagination**: Sortable columns and paginated results for better data management
- **Currency Formatting**: All amounts display with proper currency formatting (e.g., 1,000.00 IQD, 500.00 USD)

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Blade templates with Bootstrap 5
- **Charts**: ApexCharts for data visualization
- **Icons**: Feather Icons
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Breeze (Session) + Sanctum (API)
- **Permissions**: Custom role-based permissions via `account_user.role` pivot and `config/permissions.php`
- **PDF Export**: DomPDF (barryvdh/laravel-dompdf)
- **Build Tool**: Vite
- **Email**: SMTP support with invitation system

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x & npm
- Database (MySQL, PostgreSQL, or SQLite)
- Mail server (for email invitations) - optional, can use 'log' driver for development

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd ledgerly
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node dependencies:
```bash
npm install
```

4. Copy environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ledgerly
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Configure mail settings (for email invitations):
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=465
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD="your_password"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your_email@example.com
MAIL_FROM_NAME="Ledgerly"
```

**Note**: Port 465 requires `MAIL_ENCRYPTION=ssl`, while port 587 requires `MAIL_ENCRYPTION=tls`.

8. Run migrations and seeders:
```bash
php artisan migrate --seed
```

9. Build frontend assets:
```bash
npm run build
```

10. Start the development server:
```bash
php artisan serve
```

11. Visit `http://localhost:8000` in your browser.

## Development

For development with hot-reloading:

```bash
# Terminal 1: Start Vite dev server
npm run dev
```

```bash
# Terminal 2: Start Laravel server
php artisan serve
```

The Vite dev server runs on `http://127.0.0.1:5173` and proxies to Laravel.

## Default Timezone & Currency

- **Default Timezone**: Asia/Baghdad (set automatically, not user-editable)
- **Supported Currencies**: IQD (Iraqi Dinar) and USD (US Dollar)
- Currency is selected during registration and can be changed from Settings (for owners/admins only)

## Registration & Invitations

### New User Registration

1. Users register with name, email, password, and preferred currency (IQD or USD)
2. A default account is automatically created upon registration
3. User becomes the owner of their account

### Inviting Members

1. Owners and Admins can invite members via email
2. If the email is already registered: User is added immediately
3. If the email is not registered: 
   - An invitation token is generated (valid for 7 days)
   - Email is sent with registration link containing the token
   - User registers and automatically joins the workspace with assigned role

## API Usage

### Authentication

Create a personal access token via API:

```bash
POST /api/v1/tokens
Headers:
  Authorization: Bearer {token}
  Accept: application/json
  Content-Type: application/json
```

### Endpoints

**Transactions:**
- `GET /api/v1/transactions` - List transactions (with filtering and pagination)
- `POST /api/v1/transactions` - Create transaction
- `GET /api/v1/transactions/{id}` - Get transaction
- `PUT /api/v1/transactions/{id}` - Update transaction
- `DELETE /api/v1/transactions/{id}` - Delete transaction

**Income Sources (Wallets):**
- `GET /api/v1/wallets` - List income sources
- `POST /api/v1/wallets` - Create income source
- `PUT /api/v1/wallets/{id}` - Update income source
- `DELETE /api/v1/wallets/{id}` - Delete income source

**Expense Types (Categories):**
- `GET /api/v1/categories` - List expense types
- `POST /api/v1/categories` - Create expense type
- `PUT /api/v1/categories/{id}` - Update expense type
- `DELETE /api/v1/categories/{id}` - Delete expense type

**Reports:**
- `GET /api/v1/reports/monthly-summary?month=1&year=2024` - Get monthly summary
- `GET /api/v1/reports/category-breakdown?start_date=2024-01-01&end_date=2024-01-31` - Get category breakdown
- `GET /api/v1/reports/wallet-breakdown?start_date=2024-01-01&end_date=2024-01-31` - Get wallet breakdown

**Note**: All API endpoints require authentication and are scoped to the current account context.

## Project Structure

```
app/
├── Enums/
│   └── Currency.php          # Currency enum (IQD, USD)
├── Helpers/
│   └── CurrencyHelper.php    # Currency formatting utilities
├── Http/
│   ├── Controllers/
│   │   ├── Web/              # Web controllers (dashboard, transactions, etc.)
│   │   ├── Api/V1/           # API controllers (versioned)
│   │   └── Auth/             # Authentication controllers
│   ├── Middleware/           # Custom middleware (AccountContext)
│   ├── Requests/             # Form request validation
│   └── Mail/                 # Mailable classes (invitations)
├── Models/
│   ├── Account.php           # Workspace/tenant model
│   ├── AccountInvitation.php # Invitation model
│   ├── AccountUser.php       # Pivot model for account-user relationship
│   ├── Category.php          # Expense type model
│   ├── Transaction.php       # Transaction model
│   ├── User.php              # User model with permission helpers
│   └── Wallet.php            # Income source model
├── Policies/                 # Authorization policies
├── Services/                 # Business logic services
│   ├── AccountContext.php    # Current account context management
│   ├── PdfExportService.php  # PDF generation
│   ├── ReportService.php     # Report calculations
│   └── TransactionService.php # Transaction business logic
└── Support/
    └── ReportPeriod.php      # Value object for date ranges

database/
├── migrations/               # Database migrations
└── seeders/                 # Database seeders

resources/
├── views/
│   ├── components/           # Reusable Blade components
│   │   ├── app-layout.blade.php
│   │   ├── page-header.blade.php
│   │   └── ...
│   ├── layouts/              # Layout templates
│   ├── emails/               # Email templates
│   └── ...                   # Page views
└── css/                      # SCSS files
```

## Currency & Amount Formatting

### Currency Handling

- **Supported Currencies**: IQD (Iraqi Dinar) and USD (US Dollar)
- **Currency Selection**: Users select currency during registration (IQD or USD)
- **Currency Change**: Can be changed from Settings (owners/admins only)
- **Storage**: All amounts are stored in the account's `currency_code` (no conversion)
- **Display**: All amounts are formatted with currency suffix (e.g., "1,000.00 IQD", "500.00 USD")
- **Format**: Amounts display with thousands separators and 2 decimal places (e.g., 1,000.00, 100,000.00)

### Amount Display

All monetary values throughout the application are formatted using `CurrencyHelper::format()` which:
- Formats numbers with 2 decimal places
- Adds thousands separators (commas)
- Appends currency code as suffix
- Example: `1000` becomes `1,000.00 IQD`

## Permissions & Roles

### Permission System

The application uses a custom role-based permission system (not Spatie) that is tenant-aware:

- Permissions are defined in `config/permissions.php`
- Permissions are checked via `User::hasPermissionInAccount($accountId, $permission)`
- Policies enforce permissions at the controller level
- Views hide UI elements based on permissions

### Role Permissions

**Owner** (Full Access):
- All transaction permissions (view, create, update, delete)
- All income source permissions (view, create, update, delete)
- All expense type permissions (view, create, update, delete)
- View reports and export PDFs
- View and manage members
- View and manage settings

**Admin** (Management):
- All transaction permissions (view, create, update, delete)
- All income source permissions (view, create, update, delete)
- All expense type permissions (view, create, update, delete)
- View reports and export PDFs
- View and manage members
- ❌ No settings access

**Member** (Limited):
- View, create, and update transactions
- View reports
- ❌ No income sources access
- ❌ No expense types access
- ❌ No settings access
- ❌ No members access

**Viewer** (Read-Only):
- View transactions
- View reports
- ❌ All other permissions denied

## Key Features & Enhancements

### Dashboard

- **Financial Overview Cards**: Income, Expense, Net Balance, Income Sources count
- **Income vs Expense Donut Chart**: Visual representation of spending ratio with percentage
- **Monthly Trend Line Chart**: 12-month income vs expense trend showing long-term financial patterns
- **Top Expense Types Table**: Shows top 5 expense types for current month
- **Income Source Balances Table**: Shows balance for each income source
- **Quick Actions**: Context-aware action buttons (hidden for members)
- **Charts**: Expense Types Pie Chart and Income Sources Bar Chart

### Transactions

- **Filtering**: Filter by type (income/expense/transfer), income source, expense type, date range
- **Search**: Search by note, income source name, or expense type name
- **Sorting**: Sortable columns (Date, Type, Amount)
- **Pagination**: 20 transactions per page
- **Transfer Support**: Transfers are represented as two linked transactions (expense from source, income to destination)

### Income Sources (Wallets)

- **Types**: Cash, Bank, Card, Other
- **Balance Calculation**: Opening balance + income - expense
- **Balance Display**: Shows calculated balance with currency formatting
- **Sorting**: Sortable by name, type
- **Search**: Search by name

### Expense Types (Categories)

- **Types**: Income, Expense, Both
- **Balance Display**: Shows net balance (income - expense) for each category
- **Sorting**: Sortable by name, type
- **Search**: Search by name
- **Color Coding**: Optional color assignment

### Reports

- **Date Range Filtering**: Filter reports by start and end date
- **Transaction Type Filter**: Show only income or only expenses
- **Category Breakdown**: Shows total spending per expense type
- **Wallet Breakdown**: Shows balance per income source
- **PDF Export**: Export statements as PDF

### Email Invitations

- **Token-Based**: Secure UUID tokens for invitations
- **Expiration**: Invitations expire after 7 days
- **Email Template**: Professional HTML email with registration link
- **Auto-Join**: Users automatically join workspace upon registration with invitation token
- **Fallback**: If email sending fails, invitation token is provided for manual sharing

## Security Features

- **Account Scoping**: All queries are scoped by `account_id` to ensure data isolation
- **Role-Based Permissions**: Custom permission system that is tenant-aware
- **CSRF Protection**: All forms protected with CSRF tokens
- **SQL Injection Protection**: Eloquent ORM prevents SQL injection
- **XSS Protection**: Blade automatically escapes output
- **Soft Deletes**: Financial records are soft-deleted for audit trails
- **Immutable Transactions**: Transactions older than 30 days (configurable) cannot be edited, only reversed
- **Password Hashing**: Passwords are hashed using bcrypt
- **Session Security**: Secure session handling
- **Token Authentication**: API uses Sanctum tokens

## Configuration

### Immutable Transactions

Transactions older than a certain number of days cannot be edited. Configure this in `.env`:

```env
IMMUTABLE_TRANSACTIONS_DAYS=30
```

### Account Context

The current account is managed via session storage and automatically resolved for authenticated users. Users can switch between accounts if they belong to multiple workspaces.

## Templates & Assets

### Dashboard Template

- **Minia Admin Template** v2.4.0
- Location: `public/assets/minia/`
- Includes: Bootstrap 5, ApexCharts, Feather Icons, custom styling
- Used for: Dashboard, transactions, income sources, expense types, reports, settings

### Landing Page Template

- **Invoza Template** v1.3.0
- Location: `public/assets/invoza/`
- Used for: Landing page, login, registration
- Features: Modern, responsive design with smooth animations

## Development Guidelines

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add docblocks for public methods
- Keep controllers thin - business logic in services
- Use policies for authorization
- Always scope queries by account_id

### Database

- Always use `->forAccount($accountId)` scope instead of global scopes
- Use DB transactions for operations that modify multiple records
- Include proper indexes on foreign keys and frequently queried columns
- Use soft deletes for audit trails

### Services

- Services contain business logic
- Services interact with models and other services
- Controllers only call services, never directly interact with models for business logic
- Services are reusable between web and API controllers

## Testing

Run the test suite:

```bash
php artisan test
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes following the code style guidelines
4. Write tests for new features
5. Commit your changes (`git commit -m 'Add some amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## Troubleshooting

### Mail Not Sending

If email invitations are not being sent:

1. Check `.env` mail configuration
2. Verify port and encryption match (465 = ssl, 587 = tls)
3. Check `storage/logs/laravel.log` for errors
4. If using 'log' driver, check `storage/logs/laravel.log` for email content

### Permission Issues

If you see 403 errors:

1. Check user's role in the account
2. Verify permissions in `config/permissions.php`
3. Clear config cache: `php artisan config:clear`

### View Issues

If views don't update:

1. Clear view cache: `php artisan view:clear`
2. Clear all caches: `php artisan optimize:clear`
3. Rebuild assets: `npm run build`

## About Ledgerly

Ledgerly is designed to be your complete financial management solution. Whether you're tracking personal expenses, managing household finances, or collaborating with a team, Ledgerly provides the tools you need to stay organized and make informed financial decisions.

### Why Ledgerly?

- **Free Forever**: No subscriptions, no hidden costs, no limits
- **Privacy First**: Your data is yours. Account-scoped tenancy ensures complete data isolation
- **Multi-Currency**: Support for IQD and USD with easy currency switching
- **Team Ready**: Invite family members or team members with granular role-based permissions
- **Visual Insights**: Beautiful charts and graphs help you understand your spending patterns
- **Mobile First**: Access your finances anywhere, on any device
- **Open Source**: Built with modern technologies, fully open source

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues, questions, or contributions, please open an issue on GitHub.

**Website**: [www.ledgerly.com](https://www.ledgerly.com)  
**Email**: support@ledgerly.com
