# Multi-stage Dockerfile for JobsMtaani Platform
# Supports both PHP backend and Next.js frontend

# Stage 1: PHP Backend
FROM php:8.2-apache as backend

# Install PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy PHP application
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Stage 2: Node.js Frontend
FROM node:18-alpine as frontend

WORKDIR /app

# Copy package files
COPY package*.json ./
COPY pnpm-lock.yaml ./

# Install dependencies
RUN npm install -g pnpm
RUN pnpm install

# Copy source code
COPY . .

# Build the application
RUN pnpm build

# Stage 3: Production
FROM php:8.2-apache as production

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy PHP backend
COPY --from=backend /var/www/html /var/www/html

# Copy built frontend
COPY --from=frontend /app/.next /var/www/html/.next
COPY --from=frontend /app/public /var/www/html/public

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create uploads directory
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

EXPOSE 80

CMD ["apache2-foreground"]
