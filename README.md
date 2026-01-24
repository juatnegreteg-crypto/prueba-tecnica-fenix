# Sistema de Gestión de Órdenes

Descripción
-----------
Esta es una aplicación web desarrollada en **Laravel 10** diseñada para gestionar el historial de órdenes de trading conectándose con la API de **Bitfinex**. La aplicación demuestra capacidades de integración con servicios externos, seguridad mediante firmas criptográficas y gestión CRUD eficiente.

 Funcionalidades clave
------------------------
- Autenticación HMAC SHA384: Seguridad avanzada para la comunicación con Bitfinex mediante encabezados firmados (`bfx-signature`, `bfx-nonce`).
- Sincronización inteligente: Algoritmo que procesa el historial externo y evita duplicados en la base de datos comparando `bitfinex_id`.
- Arquitectura de servicios: Desacoplamiento de la lógica de negocio mediante `BitfinexService`.
- Gestión completa (CRUD): Listar, buscar por ID de Bitfinex, editar (`status`, `price`, `amount`) y eliminar registros.
- Interfaz de usuario: `resources/views/orders.blade.php` como SPA con JavaScript nativo.

 Requisitos
-------------
- PHP >= 8.1
- Composer
- MySQL / SQLite (u otra base de datos soportada)
- Node.js & NPM
- Git

 Instalación
--------------
1. Clona el repositorio:
```bash
git clone https://github.com/juatnegreteg-crypto/prueba-tecnica-fenix.git
cd prueba-tecnica-fenix
```

2. Instala dependencias PHP:
```bash
composer install
```

3. Configura el entorno:
```bash
cp .env.example .env
```
Edita `.env` y ajusta las credenciales de tu DB y las llaves de Bitfinex.

4. Genera la clave de aplicación:
```bash
php artisan key:generate
```

5. Ejecuta migraciones:
```bash
php artisan migrate
```

6. Instala dependencias de frontend y compila (si aplica):
```bash
npm install
npm run dev
# Para producción:
npm run build
```

7. Inicia el servidor:
```bash
php artisan serve
```
Accede a: `http://127.0.0.1:8000`

 Documentación de la API (endpoints)
--------------------------------------

1) Sincronizar historial (Bitfinex -> Local)
- Método: POST
- Ruta: `/api/orders/sync`
- Descripción: Llama a la API de Bitfinex, firma la petición y guarda órdenes nuevas evitando duplicados por `bitfinex_id`.

Ejemplo cURL:
```bash
curl -X POST http://127.0.0.1:8000/api/orders/sync -H "Accept: application/json"
```

2) Listar todas las órdenes
- Método: GET
- Ruta: `/api/orders`

Ejemplo cURL:
```bash
curl -X GET http://127.0.0.1:8000/api/orders
```

3) Buscar orden por ID de Bitfinex
- Método: GET
- Ruta: `/api/orders/{id_bitfinex}`

Ejemplo cURL:
```bash
curl -X GET http://127.0.0.1:8000/api/orders/123456789
```

4) Actualizar orden
- Método: PUT
- Ruta: `/api/orders/{id}`
- Campos permitidos: `status`, `price`, `amount`

Ejemplo cURL:
```bash
curl -X PUT http://127.0.0.1:8000/api/orders/1 \
     -H "Content-Type: application/json" \
     -d '{"status":"CANCELED", "price":"60000.50"}'
```

5) Eliminar orden
- Método: DELETE
- Ruta: `/api/orders/{id}`

Ejemplo cURL:
```bash
curl -X DELETE http://127.0.0.1:8000/api/orders/1
```

 Estructura del proyecto (resumen)
------------------------------------
- `app/Services/BitfinexService.php` — Lógica central de autenticación HMAC y conexión externa.  
- `app/Http/Controllers/OrderController.php` — Controlador RESTful para la gestión de la data.  
- `app/Models/Order.php` — Modelo con asignación masiva protegida.  
- `resources/views/orders.blade.php` — Interfaz de usuario (Single Page Interface) con JavaScript nativo.  
- `routes/api.php` — Definición de los endpoints requeridos.  
- `database/` — Migraciones y seeders.  
- `public/` — Punto de entrada público (assets, index.php).  
- `tests/` — Pruebas automatizadas (si aplican).

 Buenas prácticas aplicadas
-----------------------------
- Seguridad: Uso de variables de entorno para API Keys (no subir `.env` al repo).  
- Validación: Manejo de respuestas vacías y errores HTTP de la API externa.  
- Integridad: Uso de `decimal(18,8)` para precisión financiera en cripto.  
- Scannability: Código comentado y nombres de métodos descriptivos.  
- Sincronización: Detección y evitación de duplicados por `bitfinex_id`.

 Notas técnicas destacadas
----------------------------
- Autenticación HMAC SHA384: la firma se calcula con la API secret y se envía en el encabezado `bfx-signature`, junto con `bfx-nonce` y `bfx-apikey`.
- BitfinexService encapsula:
  - Construcción de payloads y cabeceras firmadas.
  - Llamadas HTTP a Bitfinex.
  - Transformación y normalización de la respuesta antes de persistir.
- La sincronización compara `bitfinex_id` para evitar insertar órdenes duplicadas.

🌐 Despliegue
-------------
Se utiliza la plataforma Render para desplegar la aplicacion, se utlizo la base de datos sqlite que proporciona la plataforma.
El proyecto se encuentra desplegado para pruebas en vivo aquí: 👉 [https://prueba-tecnica-fenix.onrender.com/]

Visuales
<img width="1121" height="554" alt="image" src="https://github.com/user-attachments/assets/4315e959-c606-430d-9ad9-931ec701b013" />
<img width="440" height="592" alt="image" src="https://github.com/user-attachments/assets/7c341667-4a0f-48ff-9393-418db0d5d902" />
<img width="437" height="395" alt="image" src="https://github.com/user-attachments/assets/bc47b190-864b-4eff-811c-a9e3478101c2" />
# Imagen de Base de datos local MySQl
<img width="1080" height="330" alt="image" src="https://github.com/user-attachments/assets/7af71e74-07e4-4bcf-acb6-d81d095cc998" />



Recomendaciones de despliegue:
```bash
# Compilar assets
npm run build

# Optimizar config en Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache



👨‍💻 Autor
----------
- Nombre: juatnegreteg-crypto  
- Repositorio: https://github.com/juatnegreteg-crypto/prueba-tecnica-fenix
