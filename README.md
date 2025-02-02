# BengkelCash (Backend)

The backend system for the **BengkelCash** application, a platform designed to manage financial operations for automotive workshops. This backend provides efficient and secure APIs to handle income, expenses, customers, and financial reports.

## Key Features
- APIs for recording income and expenses.
- Financial reports available in JSON/CSV format.
- User authentication using JSON Web Token (JWT).
- Multi-branch support for workshops with multiple locations.

## Technology Stack
- **Framework**: Laravel 11
- **Authentication**: JSON Web Token (JWT)
- **Database**: MySQL (or customize as needed)
- **Asset Management**: Vite

## Installation

Follow the steps below to set up the project locally:

1. **Clone the Repository**
   ```bash
   git clone https://github.com/username/bengkelcash-backend.git
   cd bengkelcash-backend
   ```

2. **Install Dependencies**
   Ensure you have [Composer](https://getcomposer.org/) and [Node.js](https://nodejs.org/) installed.
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**
   Copy the `.env.example` file to `.env` and configure the database, JWT, and other settings:
   ```bash
   cp .env.example .env
   ```

   Example database configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bengkelcash
   DB_USERNAME=root
   DB_PASSWORD=yourpassword

   JWT_SECRET=your_jwt_secret_key
   ```

4. **Generate App Key and JWT Secret**
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```

5. **Run Database Migrations**
   Execute the migrations to create necessary tables in the database:
   ```bash
   php artisan migrate
   ```

6. **Start the Local Server**
   Run the development server:
   ```bash
   php artisan serve
   ```
   The server will be accessible at `http://127.0.0.1:8000` by default.

## API Documentation

Once the server is running, API documentation can be accessed at the following endpoints (if Swagger or Postman Collection is used):
- **Swagger**: `http://127.0.0.1:8000/api/documentation` (optional)
- **Postman Collection**: [Download here](#) (replace with your Postman collection link).

## NPM Scripts

Use the following scripts to manage assets with Vite:
- **Build for Production**:
  ```bash
  npm run build
  ```
- **Development**:
  ```bash
  npm run dev
  ```

## Contribution

Contributions are welcome! To contribute, follow these steps:
1. Fork this repository.
2. Create a new branch:
   ```bash
   git checkout -b new-feature
   ```
3. Make your changes and commit them:
   ```bash
   git commit -m "Add new feature"
   ```
4. Push to your branch:
   ```bash
   git push origin new-feature
   ```
5. Open a Pull Request.

## License

This project is licensed under the [MIT License](LICENSE).

---

Support the development of **BengkelCash** and make financial management for workshops easier! 😊
