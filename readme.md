Web Application – Installation & Configuration
-

Requirements
-

- PHP 7.3.33 with SQLite3
- Composer
- Node.js 22
- npm

PHP Dependencies Installation
-
Run the following command in the project root directory:

```
composer install --no-interaction
```

Vue Application Installation
-

Install Dependencies. In the vue-project directory run:

```
npm install
```

Run the Application
The project was developed using Node.js 22.

Development

```
npm run dev
```

Production

```
npm run build
```

Application Configuration
-
The application configuration is stored in the file:

```
app-config.json
```

Available configuration profiles:

- DEV
- STAGE
- PROD

Configuration Options

*dbFilename* — Path to the SQLite3 database, currently used to store the offer number.
It is recommended to place the database outside the source code directory to prevent data loss or accidental overwrites.

*isProductionSmtp* — If set to true, SMTP credentials from app-config.json are used.
If set to false, localhost is used.

*isProductionVue* — Must be set to true for production environments.

*prefix: mail* — SMTP server configuration (host, port, username, password, encryption).

*prefix: reCaptcha* — Google reCAPTCHA v2 (Checkbox) configuration.

Notes
-

Before deploying to production, verify that SMTP and reCAPTCHA settings are correctly configured.
Ensure that the Vue application build has been successfully generated before deployment (in this repo there is prepared
a production build).