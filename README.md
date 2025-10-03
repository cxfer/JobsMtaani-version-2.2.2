# JobsMtaani Service Platform

A comprehensive service marketplace platform connecting customers with local service providers in Kenya. Built with Next.js frontend and PHP backend.

## 🚀 Features

### For Customers
- **Service Discovery**: Browse and search local services by category
- **Easy Booking**: Book services with preferred dates and times
- **Secure Payments**: Multiple payment options including M-Pesa integration
- **Reviews & Ratings**: Rate and review service providers
- **Real-time Messaging**: Communicate directly with service providers
- **Booking Management**: Track booking status and history

### For Service Providers
- **Service Management**: Create and manage service listings
- **Booking Management**: Accept, decline, and manage bookings
- **Earnings Dashboard**: Track revenue and commission details
- **Customer Communication**: Built-in messaging system
- **Performance Analytics**: View ratings, reviews, and booking statistics
- **Subscription Plans**: Choose from Basic, Professional, or Business plans

### For Administrators
- **User Management**: Manage customers, providers, and admin accounts
- **Service Oversight**: Approve, edit, and manage all services
- **Booking Monitoring**: Track all platform bookings and statuses
- **Payment Management**: Monitor transactions and handle refunds
- **Analytics Dashboard**: Comprehensive platform analytics and reporting
- **System Settings**: Configure platform settings and appearance

## 🛠 Technology Stack

### Frontend
- **Next.js 14** - React framework with App Router
- **TypeScript** - Type-safe development
- **Tailwind CSS v4** - Modern utility-first CSS framework
- **Shadcn/ui** - High-quality React components
- **Recharts** - Data visualization and analytics charts

### Backend
- **PHP 8.2** - Server-side logic and API endpoints
- **MySQL 8.0** - Primary database
- **Apache** - Web server
- **JWT** - Authentication and session management

### Development & Deployment
- **Docker** - Containerization for easy deployment
- **Jest** - Frontend testing framework
- **PHPUnit** - Backend testing framework
- **pnpm** - Fast, disk space efficient package manager

## 📦 Installation

### Prerequisites
- Node.js 18+ and pnpm
- PHP 8.2+ with MySQL extension
- MySQL 8.0+
- Apache/Nginx web server

### Quick Start with Docker

1. **Clone the repository**
\`\`\`bash
git clone <repository-url>
cd jobsmtaani-platform
\`\`\`

2. **Start with Docker Compose**
\`\`\`bash
docker-compose up -d
\`\`\`

3. **Access the application**
- Frontend: http://localhost:3000
- Backend API: http://localhost:8080
- phpMyAdmin: http://localhost:8081

### Manual Installation

1. **Install dependencies**
\`\`\`bash
# Frontend dependencies
pnpm install

# Backend setup
composer install (if using Composer)
\`\`\`

2. **Environment Configuration**
\`\`\`bash
# Copy environment file
cp .env.example .env.local

# Update database credentials and other settings
\`\`\`

3. **Database Setup**
\`\`\`bash
# Run database setup script
php setup/database_setup.php

# Or run migrations manually
php database/migrations/migrate.php
\`\`\`

4. **Start Development Servers**
\`\`\`bash
# Frontend (Next.js)
pnpm dev

# Backend (PHP with Apache/Nginx)
# Configure your web server to serve the PHP files
\`\`\`

## 🔧 Configuration

### Environment Variables

Create a `.env.local` file with the following variables:

\`\`\`env
# Database
DB_HOST=localhost
DB_NAME=jobsmtaani
DB_USER=root
DB_PASSWORD=your_password

# Application
APP_NAME=JobsMtaani
APP_URL=http://localhost:3000
APP_ENV=development

# Security
JWT_SECRET=your-jwt-secret
PASSWORD_SALT=your-password-salt

# Payment Gateway (M-Pesa)
MPESA_CONSUMER_KEY=your-consumer-key
MPESA_CONSUMER_SECRET=your-consumer-secret
MPESA_SHORTCODE=your-shortcode
MPESA_PASSKEY=your-passkey
MPESA_ENVIRONMENT=sandbox
\`\`\`

### Database Configuration

The platform uses MySQL with the following key tables:
- `users` - User accounts (customers, providers, admins)
- `services` - Service listings
- `bookings` - Service bookings
- `payments` - Payment transactions
- `reviews` - User reviews and ratings
- `notifications` - System notifications

## 🧪 Testing

### Frontend Tests
\`\`\`bash
# Run Jest tests
pnpm test

# Run tests in watch mode
pnpm test:watch

# Generate coverage report
pnpm test:coverage
\`\`\`

### Backend Tests
\`\`\`bash
# Run PHPUnit tests
php vendor/bin/phpunit tests/

# Run specific test file
php vendor/bin/phpunit tests/UserTest.php
\`\`\`

## 📱 API Documentation

### Authentication Endpoints
- `POST /api/auth.php?action=login` - User login
- `POST /api/auth.php?action=register` - User registration
- `POST /api/auth.php?action=logout` - User logout

### Service Endpoints
- `GET /api/services.php` - List all services
- `POST /api/services.php` - Create new service
- `PUT /api/services.php?id={id}` - Update service
- `DELETE /api/services.php?id={id}` - Delete service

### Booking Endpoints
- `GET /api/bookings.php` - List user bookings
- `POST /api/bookings.php` - Create new booking
- `PUT /api/bookings.php?id={id}` - Update booking status

### Payment Endpoints
- `POST /api/payments.php` - Process payment
- `GET /api/payments.php?booking_id={id}` - Get payment status

## 🚀 Deployment

### Production Deployment

1. **Build the application**
\`\`\`bash
pnpm build
\`\`\`

2. **Configure production environment**
\`\`\`bash
cp .env.example .env.production
# Update with production values
\`\`\`

3. **Deploy with Docker**
\`\`\`bash
docker-compose -f docker-compose.prod.yml up -d
\`\`\`

### Vercel Deployment (Frontend Only)
\`\`\`bash
# Deploy to Vercel
vercel --prod
\`\`\`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Email: support@jobsmtaani.com
- Documentation: [docs.jobsmtaani.com](https://docs.jobsmtaani.com)
- Issues: [GitHub Issues](https://github.com/jobsmtaani/platform/issues)

## 🙏 Acknowledgments

- Built with [Next.js](https://nextjs.org/)
- UI components from [Shadcn/ui](https://ui.shadcn.com/)
- Icons from [Lucide React](https://lucide.dev/)
- Charts powered by [Recharts](https://recharts.org/)

---

**JobsMtaani** - Connecting communities through trusted local services.
