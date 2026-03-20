
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


## Creacion de Base de datos

```sql

CREATE DATABASE tienda_italika CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tienda_user'@'localhost' IDENTIFIED BY 'SuperPass123!';
GRANT ALL PRIVILEGES ON tienda_italika.* TO 'tienda_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

**Desarrollado con ❤️ para Tienda Italika**





## 🗺️ Ruta de aprendizaje (en 6 etapas)

Cada etapa tiene: qué aprender, tareas, validación y por qué importa en una API de tienda.

0) Preparación (10–20 min)

Qué: Revisa .env, conexión a MySQL 8, y ejecuta el servidor.
Tareas

Configura DB en .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
php artisan migrate (debe crear users, cache, jobs).
php artisan serve.


Validación: consola sin errores, tablas creadas.
Por qué: base estable para no depurar fallas de entorno mientras aprendes.


Tip anti-piloto automático: no aceptes sugerencias largas de VS Copilot; pídele líneas cortas, y cuando te proponga bloques, escribe tú primero los esqueletos.


1) Diseño de la API (contratos primero)

Qué: Define el contrato de la API antes de codear (endpoints, payloads, estados).
Tareas

Crea routes/api.php y anota endpoints vacíos (GET /health, POST /auth/login, GET /products, POST /orders).
Crea Controladores vacíos: AuthController, ProductController, OrderController.
Define códigos de estado esperados y errores (400/401/409/422/500).


Validación: php artisan route:list muestra los endpoints.
Por qué: separa el qué del cómo; facilita pruebas y evita re-trabajo.


Estructura actual del proyecto ya incluye routes/ y app/Http/Controllers listos para esto.


2) Autenticación con Sanctum (tokens personales) + Roles/Permisos

Qué: Login con email/password → genera token; middleware auth:sanctum; roles básicos (admin, customer).
Tareas

Instala y configura Sanctum (migraciones, middleware).
En AuthController@login, valida credenciales y crea token.
Middleware RolePermission (simple): verifica user->role.


Validación:

POST /auth/login devuelve {token}.
Un GET /products protegido devuelve 401 sin token y 200 con token.


Por qué: toda compra y carrito requieren identidad y permisos.


3) Dominio de Productos e Inventario (modelo de datos mínimo)

Qué: Tablas y modelos Product, InventoryMovement para existencias.
Tareas

Migraciones:

products: sku único, name, price_decimal, stock_cached.
inventory_movements: product_id, delta, reason, created_at.


Seeders con 5–10 productos de prueba.
ProductController@index/show (solo lectura).


Validación: GET /products lista con paginación; GET /products/{id} devuelve detalle.
Por qué: inventario consistente es la base del checkout.


4) Compras seguras: idempotencia + “doble clic” + locks

Qué: Endpoint POST /orders con token idempotente y control de stock atómico.
Tareas

Crea tabla orders (estado: pending|paid|cancelled, total, idempotency_key único).
Middleware TokenIdempotency: lee header Idempotency-Key, busca orden previa con ese key, reutiliza respuesta si existe; si no, marca un lock corto (Redis o DB).
CheckoutService:

Revalida stock con SELECT ... FOR UPDATE o Redis lock.
Descuenta stock (escribe inventory_movements, actualiza stock_cached).
Registra order en pending y simula pago (por ahora).




Validación: dos POST consecutivos con el mismo Idempotency-Key → misma respuesta (HTTP 200/201) sin crear duplicados; sin key → 400.
Por qué: esto elimina cargos duplicados y órdenes fantasma cuando el usuario presiona dos veces.


5) Reintentos y Jobs (colas)

Qué: Mueve tareas pesadas a queue: confirmación de pago, envío de email.
Tareas

Configura queue (database/redis).
Crea Job ProcessPayment con retryUntil() y backoff exponencial.
OrderController@checkout → encola Job, devuelve 202 + order_id.


Validación: php artisan queue:work procesa y actualiza orders.status a paid.
Por qué: mejora resiliencia y UX sin bloquear la petición.


6) Auditoría y Seguridad defensiva

Qué: audit_logs para rastrear acciones; rate limiting y validaciones.
Tareas

Middleware que registra usuario, endpoint, payload resumido, resultado.
RateLimiter por route sensible (login, checkout).
Validaciones FormRequest estrictas (tipos, tamaños, listas blancas).


Validación: logs consistentes y límites aplicados (429 cuando corresponde).
Por qué: forense y protección ante abuso.


## ✅ Orden de trabajo propuesto para hoy (bloques de 25–40 min)

Crear contratos de API y rutas vacías (routes/api.php) con controladores vacíos.
Sanctum básico: login → token → proteger /products.
Migraciones/seeders de productos + GET /products.

Con eso ya tendrás autenticación + catálogo funcionando. Mañana atacamos checkout idempotente.

## 📌 Reglas de juego con VS Copilot (para que no te “haga todo”)

Primero escribe tú el comentario o la firma del método; luego acepta pequeñas sugerencias (1–3 líneas).
Si te propone 50 líneas: Escápate (Esc) y pide “Suggest a shorter snippet” o escribe tú la estructura y deja que complete param names, no la lógica.
Cuando dudes, pega aquí lo que te sugirió y lo refactorizamos juntos.


## 🧪 Checklist de comprensión (no sigas sin poder contestar esto)

¿Qué diferencia hay entre autenticación (quién eres) y autorización (qué puedes hacer)?
¿Qué garantiza un token idempotente frente a un simple request_id?
¿Cuál es la diferencia entre lock optimista y pesimista en tu flujo de stock?
¿Qué endpoints deberían tener rate limiting y por qué?


## ¿Por dónde empezamos ya?
Dime si quieres que arranquemos con el Bloque 1 (contratos + rutas + controladores vacíos).
Si me dices “va”, te paso:

el esqueleto de routes/api.php (sin lógica, solo endpoints y comentarios de intención),
los stubs de controladores con métodos vacíos y docblocks de lo que hará cada uno,
y un mini plan de pruebas con curl/httpie para validar que existen y responden 200/401 según corresponda.


