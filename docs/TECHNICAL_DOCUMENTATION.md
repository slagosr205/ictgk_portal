# Documentación técnica del proyecto

## 1. Resumen del proyecto
Este repositorio contiene una aplicación web basada en **Laravel 10** (backend) con **Inertia.js + React** (frontend) y un pipeline de assets gestionado por **Vite**. La UI combina **Material Dashboard**, **Bootstrap 5** y **MUI** para construir interfaces con componentes prearmados y estilos modernos. También incluye módulos de data tables, gráficos y selección de fechas. Para la capa de datos, se usa Eloquent ORM, migraciones y soporte para exportación/importación con Excel/CSV. Autenticación y autorización están soportadas mediante **Sanctum** y **spatie/laravel-permission**.

---

## 2. Arquitectura (alto nivel)

### 2.1 Capas principales
- **Backend (Laravel 10)**
  - Rutas HTTP y controladores.
  - Servicios y lógica de negocio.
  - Acceso a datos con Eloquent ORM.
  - Autenticación con Sanctum.
  - Autorización basada en roles/permisos (spatie/laravel-permission).
- **Frontend (Inertia.js + React)**
  - Vistas server-driven renderizadas en React.
  - Componentes UI con MUI y Bootstrap/Material Dashboard.
  - Manejo de formularios con React Hook Form.
- **Build & Assets**
  - Vite para compilación y bundling.
  - Tailwind CSS (utilizable junto a Bootstrap/MUI).

### 2.2 Diagrama de arquitectura
```mermaid
flowchart LR
  subgraph Browser[Cliente Web]
    UI[React + Inertia Views]
    MUI[MUI / Material Dashboard]
  end

  subgraph Backend[Servidor Laravel]
    Routes[Routes / Controllers]
    Services[Servicios / Casos de uso]
    Auth[Sanctum + spatie/laravel-permission]
    Models[Eloquent Models]
  end

  subgraph Data[Datos]
    DB[(Base de datos SQL)]
    Files[(CSV / Excel)]
  end

  UI --> Routes
  Routes --> Services
  Services --> Models
  Models --> DB
  Services --> Files
  Auth --> Routes
```

---

## 3. Flujo de petición (request lifecycle)
```mermaid
sequenceDiagram
  participant U as Usuario
  participant B as Browser (Inertia + React)
  participant L as Laravel (Routes/Controllers)
  participant S as Servicios
  participant M as Eloquent
  participant DB as Base de datos

  U->>B: Navega o envía formulario
  B->>L: Request HTTP (Inertia)
  L->>S: Ejecuta lógica de negocio
  S->>M: Consulta/actualiza modelos
  M->>DB: Operación SQL
  DB-->>M: Respuesta
  M-->>S: Datos
  S-->>L: Respuesta
  L-->>B: Página/props Inertia
  B-->>U: Render UI
```

---

## 4. Dependencias principales

### 4.1 Backend (Composer)
| Librería | Propósito | Versión | Comentarios |
|---|---|---|---|
| laravel/framework | Framework principal | ^10.10 | Núcleo MVC y herramientas. |
| laravel/sanctum | Auth por tokens/sesiones | ^3.3 | Autenticación API/SPA. |
| spatie/laravel-permission | Roles/permisos | ^6.3 | Control de acceso. |
| inertiajs/inertia-laravel | Inertia bridge | 2.0 | Conecta Laravel + React. |
| laravel-frontend-presets/material-dashboard | UI preset | ^2.0 | Plantilla Material Dashboard. |
| maatwebsite/excel | Import/Export Excel | ^3.1 | Procesamiento de archivos. |
| league/csv | CSV | 9.0 | Parsing/serialización CSV. |
| jenssegers/date | Fechas i18n | ^4.0 | Utilidades de fecha. |

### 4.2 Frontend (NPM)
| Librería | Propósito | Versión | Comentarios |
|---|---|---|---|
| react / react-dom | UI | ^19.1.1 | Framework de frontend. |
| @inertiajs/react | Inertia adapter | 2.0 | React + Inertia. |
| @mui/material | UI components | ^7.2.0 | Componentes Material. |
| bootstrap / react-bootstrap | UI | ^5.3.7 / ^2.10.10 | Componentes y estilos. |
| material-dashboard | UI kit | ^3.0.9 | Diseño Material Dashboard. |
| chart.js | Gráficas | ^4.4.1 | Visualización de datos. |
| datatables.net-* | Tablas | varias | DataTables con Bootstrap. |
| sweetalert2 | Alertas | ^11.6.13 | Notificaciones. |
| vite | Build | ^5.0.0 | Bundler y dev server. |
| tailwindcss | Utilidades CSS | ^4.1.18 | Clases utilitarias. |

---

## 5. Estructura de carpetas (referencia rápida)
- `app/` → Lógica de la aplicación (models, controllers, services).
- `routes/` → Definición de rutas web/API.
- `resources/` → Frontend (views, JS/React, CSS/SASS).
- `database/` → Migraciones, seeders y factories.
- `public/` → Assets compilados y archivos públicos.
- `config/` → Configuración del framework.

---

## 6. Integración de vistas con controladores
La navegación se define en `routes/web.php`, donde cada ruta apunta a un controlador y método. Los controladores devuelven vistas Blade mediante `return view('...')`, y el nombre de la vista se resuelve en `resources/views/<ruta>.blade.php`.【F:routes/web.php†L46-L170】

### 6.1 Ejemplos clave de rutas → controladores → vistas
- `/home` → `HomeController@index` → `resources/views/home.blade.php`.【F:routes/web.php†L63-L67】【F:app/Http/Controllers/HomeController.php†L24-L28】
- `/dmtables` → `AdminController@index` → `resources/views/Table-inf.blade.php` (o `components/dmtables` en AJAX).【F:routes/web.php†L67-L69】【F:app/Http/Controllers/AdminController.php†L109-L122】
- `/egresos` → `EgresoController@index` → `resources/views/egresos/index.blade.php`.【F:routes/web.php†L162-L166】【F:app/Http/Controllers/EgresoController.php†L31-L50】
- `/validador-importacion` → `ValidadorImportacionController@index` → `resources/views/validador-importacion/index.blade.php`.【F:routes/web.php†L141-L152】【F:app/Http/Controllers/ValidadorImportacionController.php†L38-L52】
- `/puestos` → `PuestosController@index` → `resources/views/puestos.blade.php`.【F:routes/web.php†L103-L106】【F:app/Http/Controllers/PuestosController.php†L14-L62】
- `/perfiles` → `PerfilController@show` → `resources/views/perfiles.blade.php`.【F:routes/web.php†L93-L99】【F:app/Http/Controllers/PerfilController.php†L27-L33】
- `/empresas` → `EmpresasController@index` → `resources/views/empresas.blade.php`.【F:routes/web.php†L100-L103】【F:app/Http/Controllers/EmpresasController.php†L14-L41】
- `/departamentos` → `DepartamentosController@index` → `resources/views/departamentv.blade.php`.【F:routes/web.php†L106-L110】【F:app/Http/Controllers/DepartamentosController.php†L12-L49】
- `/infopersonal/{dni}` → `CandidatosController@GetIndividualInfo` → `resources/views/consultaficha.blade.php`.【F:routes/web.php†L71-L73】【F:app/Http/Controllers/CandidatosController.php†L660-L695】
- `/informes` → `InformesController@GetInformes` → `resources/views/informes.blade.php`.【F:routes/web.php†L70-L72】【F:app/Http/Controllers/InformesController.php†L14-L32】
- `/` (root) → vista directa `resources/views/home.blade.php` o `resources/views/auth/login.blade.php` según autenticación.【F:routes/web.php†L46-L61】

---

## 7. Guía de instalación (local)
1. Clonar el repositorio.
2. Instalar dependencias de PHP: `composer install`.
3. Instalar dependencias de frontend: `npm install`.
4. Copiar el archivo de entorno: `cp .env.example .env`.
5. Generar la clave de la app: `php artisan key:generate`.
6. Configurar la base de datos en `.env` (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD).
7. Ejecutar migraciones: `php artisan migrate`.
8. En desarrollo, levantar el frontend con Vite: `npm run dev`.

---

## 8. Creación del ambiente de desarrollo con XAMPP
1. Instalar XAMPP con PHP **8.1+** y MySQL.
2. Colocar el proyecto dentro de `htdocs` (por ejemplo `C:\\xampp\\htdocs\\ictgk_portal`).
3. Iniciar **Apache** y **MySQL** desde el panel de XAMPP.
4. Crear la base de datos desde phpMyAdmin.
5. Configurar `.env` con los valores locales (ejemplo típico):
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=` (vacío, según configuración de XAMPP)
6. Ejecutar en terminal (usando el PHP de XAMPP):
   - `composer install`
   - `php artisan key:generate`
   - `php artisan migrate`
7. Levantar la app:
   - Opción A: `php artisan serve` y abrir `http://127.0.0.1:8000`.
   - Opción B: crear un VirtualHost apuntando a `public/`.
8. Para frontend en caliente: `npm run dev`.

---

## 9. Instalación en un servidor nuevo (producción)
1. Requisitos: PHP **8.1+**, Composer, Node.js/NPM, servidor web (Apache/Nginx) y base de datos.
2. Clonar o copiar el código en el servidor (por ejemplo `/var/www/ictgk_portal`).
3. Instalar dependencias de backend en modo producción:
   - `composer install --no-dev --optimize-autoloader`
4. Instalar dependencias de frontend y compilar:
   - `npm install`
   - `npm run build`
5. Configurar `.env` con credenciales reales y URL de la aplicación.
6. Ejecutar migraciones: `php artisan migrate --force`.
7. Generar el enlace de almacenamiento: `php artisan storage:link`.
8. Cachear configuración y rutas:
   - `php artisan config:cache`
   - `php artisan route:cache`
9. Dar permisos a `storage/` y `bootstrap/cache/` para el usuario del servidor web.
10. Apuntar el DocumentRoot del servidor a `public/`.

---

## 10. Migraciones (cómo se hacen y se ejecutan)
### 10.1 Crear una migración
```bash
php artisan make:migration create_nombre_tabla_table
```

### 10.2 Ejecutar migraciones
```bash
php artisan migrate
```

### 10.3 Revertir o refrescar
```bash
php artisan migrate:rollback
php artisan migrate:refresh
```

### 10.4 Ver estado de migraciones
```bash
php artisan migrate:status
```

---

## 11. Diccionario de campos (base de datos)
> Fuente: migraciones en `database/migrations/`.

### 11.1 Tabla `users`
- `id` (PK)
- `name`
- `email` (único)
- `email_verified_at` (nullable)
- `phone` (nullable)
- `location` (nullable)
- `about` (nullable)
- `password`
- `remember_token`
- `created_at`, `updated_at`

### 11.2 Tabla `password_reset_tokens`
- `email` (PK)
- `token`
- `created_at` (nullable)

### 11.3 Tabla `password_resets` (legacy)
- `email` (index)
- `token`
- `created_at` (nullable)

### 11.4 Tabla `failed_jobs`
- `id` (PK)
- `uuid` (único)
- `connection`
- `queue`
- `payload`
- `exception`
- `failed_at`

### 11.5 Tabla `personal_access_tokens`
- `id` (PK)
- `tokenable_type`, `tokenable_id` (morphs)
- `name`
- `token` (único)
- `abilities` (nullable)
- `last_used_at` (nullable)
- `expires_at` (nullable)
- `created_at`, `updated_at`

### 11.6 Tablas de permisos (Spatie)
Configuradas en `config/permission.php`:
- `roles`: `id`, `name`, `guard_name`, `created_at`, `updated_at`
- `permissions`: `id`, `name`, `guard_name`, `created_at`, `updated_at`
- `model_has_permissions`: `permission_id`, `model_type`, `model_id`
- `model_has_roles`: `role_id`, `model_type`, `model_id`
- `role_has_permissions`: `permission_id`, `role_id`
> Si `teams` se habilita en la configuración, se agrega `team_id` en tablas de pivote y `roles`.

### 11.7 Tabla `candidatos`
- `id` (PK)
- `identidad` (único)
- `nombre`
- `apellido`
- `telefono`
- `correo`
- `direccion`
- `generoM_F` (char 1)
- `fecha_nacimiento`
- `created_at`, `updated_at`

### 11.8 Tabla `egresos_ingresos`
- `id` (PK)
- `identidad`
- `id_empresa`
- `fechaIngreso`
- `area`
- `id_puesto`
- `activo` (char 1)
- `forma_egreso`
- `Comentario`
- `recomendado` (char 1)
- `recontrataria` (char 1)
- `prohibirIngreso` (char 1)
- `ComenProhibir`
- `created_at`, `updated_at`

### 11.9 Tabla `empresas`
- `id` (PK)
- `nombre`
- `direccion`
- `telefonos`
- `contacto`
- `pin`
- `puesto`
- `correo`
- `estado` (char 1)
- `logo`
- `created_at`, `updated_at`

### 11.10 Tabla `perfiles`
- `id` (PK)
- `perfilesdescrip`
- `ingreso` (tinyInteger)
- `egreso` (tinyInteger)
- `requisiciones` (tinyInteger)
- `calendarioentrevistas` (tinyInteger)
- `usuariosdb` (tinyInteger)
- `created_at`, `updated_at`

### 11.11 Tabla `departamentos`
- `id` (PK)
- `nombredepartamento`
- `empresa_id`
- `created_at`, `updated_at`

### 11.12 Tabla `puestos`
- `id` (PK)
- `nombrepuesto`
- `departamento_id`
- `created_at`, `updated_at`

### 11.13 Tabla `event_logs`
- `id` (PK)
- `user_id` (FK a `users`, nullable)
- `event_type`
- `event_data` (nullable)
- `created_at`, `updated_at`

---

## 12. Consideraciones técnicas
- Inertia evita una API REST separada: las páginas se sirven desde Laravel con props JSON.
- Roles/permisos y autenticación habilitan control de acceso a nivel de rutas y vistas.
- La combinación de MUI + Bootstrap + Material Dashboard requiere un manejo cuidadoso de estilos para evitar conflictos de CSS.
- Vite gestiona hot-reload y bundling rápido de los assets.
