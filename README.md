
 # �️ API Tienda Italika

## 📋 Descripción del Proyecto

API RESTful desarrollada en Laravel para la gestión de una tienda en línea. El sistema está diseñado con altos estándares de seguridad, especialmente en el módulo de compras, y cuenta con funcionalidades robustas para el manejo de transacciones y autenticación.

## ✅ Requisitos del Sistema

### Tecnologías Base
- **PHP** >= 8.1
- **Laravel** 11.x
- **MySQL** 8.0+
- **Composer** 2.x
- **Node.js** >= 18.x
- **NPM** o **Yarn**

### Dependencias Principales
- Laravel Sanctum (Autenticación JWT)
- Laravel Queue (Manejo de trabajos en segundo plano)
- Laravel Cache (Sistema de caché)
- Middleware personalizado para seguridad

## 🎯 Funcionalidades Principales

### 🔐 Autenticación y Autorización
- Implementación de **JWT (JSON Web Tokens)** mediante Laravel Sanctum
- Sistema de roles y permisos
- Protección de endpoints sensibles
- Middleware de autenticación personalizado

### 🛒 Sistema de Compras Seguro
- **Protección contra doble clic**: Prevención de transacciones duplicadas
- Idempotencia en operaciones de compra
- Tokens únicos por transacción
- Validaciones de estado de inventario en tiempo real
- Sistema de locks para operaciones críticas

### 🔄 Manejo de Reintentos
- Implementación de políticas de retry automático
- Gestión de timeouts en peticiones
- Queue system para operaciones pesadas
- Logging detallado de fallos y recuperaciones

### 🗄️ Base de Datos
- **MySQL** como motor principal
- Migraciones versionadas
- Seeders para datos de prueba
- Índices optimizados para consultas frecuentes

### 🛡️ Seguridad
- Validación de datos de entrada
- Protección CSRF
- Rate limiting en endpoints
- Encriptación de datos sensibles
- Auditoría de transacciones

---

---

## ⚙️ Configuración del Entorno

### 1. Configuración de Base de Datos MySQL

Crear usuario administrador para la base de datos:

```sql
-- Crear usuario con permisos completos
CREATE USER 'edri_admin'@'localhost' IDENTIFIED BY 'PasswordFuerte#2026';
CREATE USER 'edri_admin'@'%' IDENTIFIED BY 'PasswordFuerte#2026';

-- Asignar privilegios
GRANT ALL PRIVILEGES ON *.* TO 'edri_admin'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'edri_admin'@'%' WITH GRANT OPTION;

-- Aplicar cambios
FLUSH PRIVILEGES;
```

### 2. Instalación del Proyecto

```bash
# Crear proyecto Laravel
composer create-project laravel/laravel APITiendaItalika

# Navegar al directorio
cd APITiendaItalika

# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install

# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 3. Configuración del Archivo .env

```env
APP_NAME="API Tienda Italika"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tienda_italika
DB_USERNAME=edri_admin
DB_PASSWORD=PasswordFuerte#2026

QUEUE_CONNECTION=database
CACHE_DRIVER=database
SESSION_DRIVER=database
```

## � Comandos de Desarrollo

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos de prueba)
php artisan db:seed

# Iniciar servidor de desarrollo
php artisan serve

# Ejecutar workers de queue
php artisan queue:work

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 📡 Endpoints Principales

### Autenticación
- `POST /api/auth/register` - Registro de usuario
- `POST /api/auth/login` - Inicio de sesión
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/profile` - Perfil del usuario

### Productos
- `GET /api/productos` - Listar productos
- `GET /api/productos/{id}` - Detalle de producto
- `POST /api/productos` - Crear producto (Admin)
- `PUT /api/productos/{id}` - Actualizar producto (Admin)
- `DELETE /api/productos/{id}` - Eliminar producto (Admin)

### Compras
- `POST /api/compras` - Realizar compra
- `GET /api/compras` - Historial de compras
- `GET /api/compras/{id}` - Detalle de compra
- `POST /api/compras/{id}/retry` - Reintentar compra fallida

### Carrito
- `GET /api/carrito` - Ver carrito
- `POST /api/carrito/agregar` - Agregar producto al carrito
- `PUT /api/carrito/{id}` - Actualizar cantidad
- `DELETE /api/carrito/{id}` - Eliminar producto del carrito

## 🧪 Testing

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar pruebas específicas
php artisan test --filter=CompraTest

# Ejecutar con coverage
php artisan test --coverage
```

## 📂 Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ProductoController.php
│   │   ├── CompraController.php
│   │   └── CarritoController.php
│   ├── Middleware/
│   │   ├── PreventDoubleSubmission.php
│   │   └── RateLimitMiddleware.php
│   └── Requests/
│       ├── CompraRequest.php
│       └── ProductoRequest.php
├── Models/
│   ├── User.php
│   ├── Producto.php
│   ├── Compra.php
│   ├── DetalleCompra.php
│   └── Carrito.php
├── Services/
│   ├── CompraService.php
│   ├── PagoService.php
│   └── NotificationService.php
└── Jobs/
    ├── ProcesarPago.php
    └── EnviarNotificacion.php
```

## 👥 Contribución

1. Fork el proyecto
2. Crear una rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit los cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

**Desarrollado con ❤️ para Tienda Italika**



