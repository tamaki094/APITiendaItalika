# 🛍️ API Tienda Italika

## 📋 Descripción del Proyecto

API RESTful desarrollada en Laravel 12 para la gestión de una tienda en línea. El sistema implementa funcionalidades avanzadas de seguridad como idempotencia, rate limiting, control de stock atómico, y auditoría de transacciones.

## ✅ Requisitos del Sistema

### Tecnologías Base
- **PHP** >= 8.2
- **Laravel** 12.x
- **MySQL** 8.0+
- **Composer** 2.x
- **Node.js** >= 18.x
- **NPM** o **Yarn**

### Dependencias Principales
- **Laravel Sanctum** - Autenticación con tokens personales
- **Laravel Queues** - Procesamiento en segundo plano
- **Rate Limiting** - Control de peticiones por minuto
- **Transacciones DB** - Operaciones atómicas

---

## 🚀 Instalación y Configuración

### 1. Clonar y configurar el proyecto

```bash
# Clonar el repositorio
git clone https://github.com/tamaki094/APITiendaItalika.git
cd APITiendaItalika

# Instalar dependencias
composer install
npm install

# Copiar y configurar variables de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 2. Configurar Base de Datos MySQL

```sql
-- Crear base de datos
CREATE DATABASE tienda_italika CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario
CREATE USER 'tienda_user'@'localhost' IDENTIFIED BY 'SuperPass123!';
GRANT ALL PRIVILEGES ON tienda_italika.* TO 'tienda_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Configurar archivo .env

```env
APP_NAME="API Tienda Italika"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tienda_italika
DB_USERNAME=tienda_user
DB_PASSWORD=SuperPass123!

QUEUE_CONNECTION=database
CACHE_DRIVER=database
SESSION_DRIVER=database
```

### 4. Ejecutar migraciones y seeders

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos de prueba)
php artisan db:seed

# Iniciar servidor
php artisan serve
```

### 5. Script automatizado (opcional)

```bash
# Usar script predefinido en composer.json
composer run setup
```

---

## 📡 Documentación de API

### Base URL
```
http://localhost:8000/api
```

## 🏥 Health Check

### **GET** `/health`
Verificar estado del servicio.

**Headers:** Ninguno requerido

**Respuesta:**
```json
{
  "status": "ok",
  "service": "tienda-italika", 
  "version": "0.1.0"
}
```

**Códigos de estado:**
- `200` - Servicio funcionando

---

## 🔐 Autenticación (`AuthController`)

### **POST** `/auth/login`
Iniciar sesión y obtener token de acceso.

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Respuesta exitosa:**
```json
{
  "token": "1|abc123def456...",
  "token_type": "Bearer"
}
```

**Códigos de estado:**
- `200` - Login exitoso
- `401` - Credenciales inválidas
- `422` - Errores de validación

### **POST** `/auth/logout`
Cerrar sesión y revocar token.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Respuesta:**
```json
{
  "message": "Sesión cerrada"
}
```

**Códigos de estado:**
- `200` - Logout exitoso
- `401` - Token inválido

---

## 🛍️ Productos (`ProductController`)

### **GET** `/products`
Listar productos con filtros y paginación.

**Headers:** Ninguno requerido

**Query Parameters:**
- `q` (string) - Búsqueda por nombre
- `min_price` (float) - Precio mínimo
- `max_price` (float) - Precio máximo
- `sortBy` (string) - Ordenar por: `id`, `name`, `price` (default: `id`)
- `sortDir` (string) - Dirección: `asc`, `desc` (default: `asc`)
- `per_page` (int) - Elementos por página: 1-100 (default: 10)
- `page` (int) - Número de página

**Ejemplo:**
```
GET /api/products?q=laptop&min_price=500&sortBy=price&sortDir=asc&per_page=20
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Laptop Gaming",
      "price": 799.99,
      "stock": 15,
      "description": "Laptop para gaming...",
      "created_at": "2026-03-20T10:00:00.000000Z"
    }
  ],
  "meta": {
    "total": 50,
    "per_page": 20,
    "current_page": 1,
    "last_page": 3,
    "from": 1,
    "to": 20,
    "sortBy": "price",
    "sortDir": "asc",
    "filters": {
      "q": "laptop",
      "min_price": 500,
      "max_price": null
    }
  }
}
```

### **GET** `/products/{id}`
Obtener detalle de un producto específico.

**Headers:** Ninguno requerido

**Respuesta:**
```json
{
  "id": 1,
  "name": "Laptop Gaming",
  "price": 799.99,
  "stock": 15,
  "description": "Laptop para gaming de alta gama",
  "created_at": "2026-03-20T10:00:00.000000Z",
  "updated_at": "2026-03-20T10:00:00.000000Z"
}
```

**Códigos de estado:**
- `200` - Producto encontrado
- `404` - Producto no encontrado

---

## 🛒 Órdenes (`OrderController`)

> **⚠️ Importante:** Todos los endpoints de órdenes requieren autenticación con `Authorization: Bearer {token}`

### **POST** `/orders`
Crear nueva orden (checkout).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Idempotency-Key: {uuid} (recomendado para evitar duplicados)
```

**Body:**
```json
{
  "currency": "MXN",
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 5,
      "quantity": 1
    }
  ]
}
```

**Respuesta exitosa:**
```json
{
  "message": "Orden creada exitosamente.",
  "order": {
    "id": 123,
    "user_id": 1,
    "status": "pending",
    "subtotal": 1599.98,
    "tax": 0,
    "total": 1599.98,
    "currency": "MXN",
    "idempotency_key": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "unit_price": 799.99,
        "line_total": 1599.98,
        "product": {
          "id": 1,
          "name": "Laptop Gaming",
          "price": 799.99
        }
      }
    ]
  }
}
```

**Códigos de estado:**
- `201` - Orden creada
- `409` - Orden duplicada (mismo idempotency key) o stock insuficiente
- `422` - Producto no encontrado o errores de validación
- `401` - No autenticado

### **GET** `/orders`
Listar órdenes del usuario autenticado.

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 123,
      "status": "pending",
      "total": 1599.98,
      "currency": "MXN",
      "items_count": 2,
      "created_at": "2026-03-20T15:30:00.000000Z"
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 15,
    "current_page": 1
  }
}
```

### **GET** `/orders/{id}`
Obtener detalle de una orden específica.

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "id": 123,
  "user_id": 1,
  "status": "pending",
  "subtotal": 1599.98,
  "tax": 0,
  "total": 1599.98,
  "currency": "MXN",
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 2,
      "unit_price": 799.99,
      "line_total": 1599.98,
      "product": {
        "id": 1,
        "name": "Laptop Gaming",
        "price": 799.99,
        "stock": 13
      }
    }
  ],
  "created_at": "2026-03-20T15:30:00.000000Z"
}
```

**Códigos de estado:**
- `200` - Orden encontrada
- `404` - Orden no encontrada o no pertenece al usuario

### **POST** `/orders/{id}/pay`
Marcar orden como pagada (simulación).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Rate Limit:** 10 requests por minuto por usuario

**Respuesta:**
```json
{
  "message": "Orden marcada como pagada exitosamente.",
  "order": {
    "id": 123,
    "status": "paid",
    "total": 1599.98,
    "updated_at": "2026-03-20T16:00:00.000000Z"
  }
}
```

**Códigos de estado:**
- `200` - Pago exitoso
- `404` - Orden no encontrada
- `409` - Orden ya pagada o estado inválido
- `429` - Rate limit excedido

### **POST** `/orders/{id}/cancel`
Cancelar orden y restaurar stock.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Rate Limit:** 5 requests por minuto por usuario

**Respuesta:**
```json
{
  "message": "Orden cancelada exitosamente.",
  "order": {
    "id": 123,
    "status": "cancelled",
    "updated_at": "2026-03-20T16:00:00.000000Z"
  }
}
```

**Códigos de estado:**
- `200` - Cancelación exitosa
- `404` - Orden no encontrada
- `409` - No se puede cancelar (estado inválido)
- `429` - Rate limit excedido

### **GET** `/orders/stats`
Obtener estadísticas del usuario.

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "kpis": {
    "orders_count": 5,
    "total_spent": 3299.95
  },
  "last_orders": [
    {
      "id": 125,
      "status": "paid",
      "total": 899.99,
      "items_count": 1,
      "created_at": "2026-03-20T14:00:00.000000Z"
    }
  ]
}
```

### **POST** `/webhooks/payments`
Webhook para procesar notificaciones de pago (simulado).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Rate Limit:** 30 requests por minuto por IP

**Body:**
```json
{
  "order_id": 123,
  "status": "paid"
}
```

**Respuesta:**
```json
{
  "message": "Webhook procesado, orden marcada como pagada.",
  "order": {
    "id": 123,
    "status": "paid"
  }
}
```

---

## 🛡️ Características de Seguridad

### **Idempotencia**
- Header `Idempotency-Key` para prevenir órdenes duplicadas
- Generado por el cliente (UUID recomendado)

**Ejemplo JavaScript:**
```javascript
const idempotencyKey = crypto.randomUUID();

fetch('/api/orders', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json',
    'Idempotency-Key': idempotencyKey
  },
  body: JSON.stringify({
    currency: 'MXN',
    items: [{ product_id: 1, quantity: 2 }]
  })
});
```

### **Rate Limiting**
- `cancel-order`: 5 requests/min por usuario
- `pay-order`: 10 requests/min por usuario  
- `payment-webhook`: 30 requests/min por IP

### **Control de Stock Atómico**
- Uso de `SELECT ... FOR UPDATE` 
- Transacciones DB para consistencia
- Reversión automática en cancelaciones

### **Auditoría**
- Tabla `order_status_history` registra todos los cambios
- Tracking de usuario que realizó cada cambio

---

## 🚦 Comandos de Desarrollo

```bash
# Desarrollo completo con queue y logs
composer run dev

# Solo servidor
php artisan serve

# Procesar trabajos en cola
php artisan queue:work

# Limpiar caché
php artisan optimize:clear

# Ver rutas
php artisan route:list

# Ver logs en tiempo real  
php artisan pail
```

---

## 🧪 Testing con cURL

### Obtener token
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password"}'
```

### Listar productos
```bash
curl -X GET "http://localhost:8000/api/products?per_page=5&sortBy=price"
```

### Crear orden
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer 1|your-token-here" \
  -H "Content-Type: application/json" \
  -H "Idempotency-Key: $(uuidgen)" \
  -d '{
    "currency": "MXN",
    "items": [
      {"product_id": 1, "quantity": 2}
    ]
  }'
```

### Pagar orden
```bash
curl -X POST http://localhost:8000/api/orders/1/pay \
  -H "Authorization: Bearer 1|your-token-here" \
  -H "Content-Type: application/json"
```

---

## 📊 Estructura de Base de Datos

### Tablas principales:
- **users** - Usuarios del sistema
- **products** - Catálogo de productos
- **orders** - Órdenes de compra
- **order_items** - Items de cada orden
- **order_status_history** - Auditoría de cambios de estado
- **personal_access_tokens** - Tokens de Sanctum

### Estados de orden:
- `pending` - Orden creada, esperando pago
- `paid` - Orden pagada exitosamente
- `cancelled` - Orden cancelada por usuario

---

## 🐛 Solución de Problemas

### Error: "Tabla no existe"
```bash
php artisan migrate:fresh --seed
```

### Error: "Token inválido"
- Verificar que el header `Authorization` tenga formato: `Bearer {token}`
- El token expira, generar uno nuevo con `/auth/login`

### Error: "Stock insuficiente"
- Verificar disponibilidad con `GET /products/{id}`
- El stock se actualiza en tiempo real

### Rate limit excedido (HTTP 429)
- Esperar 1 minuto antes de reintentar
- Los límites son por usuario/IP según el endpoint

---

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama feature: `git checkout -b feature/nueva-funcionalidad`
3. Commit cambios: `git commit -am 'Agregar funcionalidad'`
4. Push: `git push origin feature/nueva-funcionalidad`
5. Crear Pull Request

---

## 📄 Licencia

MIT License - Ver archivo `LICENSE` para detalles.

---

**Desarrollado con ❤️ para Tienda Italika**
