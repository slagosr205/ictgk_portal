# 📋 Portal ICTGK - Sistema de Gestión de Candidatos

<div align="center">

![Estado](https://img.shields.io/badge/Estado-Activo-success)
![Versión](https://img.shields.io/badge/Versión-2.0-blue)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-purple)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)

**Sistema de Gestión de Candidatos, Ingresos y Egresos para ALTIA Business Park**

[📖 Documentación](#-tabla-de-contenidos) • [🚀 Instalación](#-instalación-y-configuración) • [👥 Manual de Usuario](#-manual-de-usuario) • [📞 Soporte](#-soporte-y-contacto)

</div>

---

## 📑 Tabla de Contenidos

- [¿Qué es el Portal ICTGK?](#-qué-es-el-portal-ictgk)
- [Características Principales](#-características-principales)
- [Arquitectura del Sistema](#-arquitectura-del-sistema)
- [Instalación y Configuración](#-instalación-y-configuración)
- [Manual de Usuario](#-manual-de-usuario)
  - [Roles y Permisos](#roles-y-permisos)
  - [Gestión de Candidatos](#gestión-de-candidatos)
  - [Gestión de Ingresos](#gestión-de-ingresos)
  - [Gestión de Egresos](#gestión-de-egresos)
  - [Consulta de Fichas](#consulta-de-fichas)
  - [Reportes e Informes](#reportes-e-informes)
- [Flujos de Trabajo](#-flujos-de-trabajo)
- [Base de Datos](#-estructura-de-base-de-datos)
- [Solución de Problemas](#-solución-de-problemas)
- [Guía de Cambios Recientes](#-cambios-recientes)
- [Soporte y Contacto](#-soporte-y-contacto)

---

## 🎯 ¿Qué es el Portal ICTGK?

El **Portal ICTGK** es un sistema web diseñado para gestionar el ciclo completo de vida laboral de los candidatos y colaboradores en las empresas de ALTIA Business Park. Permite controlar desde el momento en que un candidato es registrado, pasando por su ingreso a una empresa, hasta su eventual egreso, manteniendo un historial completo y detallado.

### 🎨 Vista General del Sistema

```
┌─────────────────────────────────────────────────────────────────┐
│                     PORTAL ICTGK                                │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐        │
│  │              │  │              │  │              │        │
│  │  CANDIDATOS  │→ │   INGRESOS   │→ │   EGRESOS    │        │
│  │              │  │              │  │              │        │
│  └──────────────┘  └──────────────┘  └──────────────┘        │
│         │                  │                  │               │
│         ↓                  ↓                  ↓               │
│  ┌─────────────────────────────────────────────────┐         │
│  │           HISTORIAL COMPLETO                     │         │
│  │      • Datos personales                          │         │
│  │      • Historial laboral                         │         │
│  │      • Recomendaciones                           │         │
│  │      • Bloqueos                                  │         │
│  └─────────────────────────────────────────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

---

## ✨ Características Principales

### 🔐 Gestión de Usuarios y Permisos
- Sistema de roles personalizable
- Permisos granulares por módulo
- Autenticación segura
- Multi-empresa

### 👥 Gestión de Candidatos
- Registro completo de información personal
- Importación masiva vía CSV
- Búsqueda avanzada por múltiples criterios
- Sistema de bloqueos y recomendaciones

### 📊 Control de Ingresos y Egresos
- Registro de ingresos con validación automática
- Control de egresos con motivos
- Validación de recontrataciones
- Alertas de candidatos bloqueados

### 📈 Reportes y Estadísticas
- Reportes por empresa
- Estadísticas de rotación
- Historial completo por candidato
- Exportación a Excel

### 🔍 Consulta Inteligente
- Búsqueda por identidad
- Vista unificada de ficha personal
- Detección automática de inconsistencias
- Mensajes de error claros y útiles

---

## 🏗️ Arquitectura del Sistema

### Diagrama de Arquitectura General

```mermaid
graph TB
    subgraph "Frontend - Navegador"
        A[Usuario]
        B[Interfaz Web<br/>HTML + CSS + JavaScript]
    end

    subgraph "Backend - Laravel"
        C[Controladores]
        D[Modelos Eloquent]
        E[Vistas Blade]
        F[Middleware de Auth]
    end

    subgraph "Base de Datos - MySQL"
        G[(Candidatos)]
        H[(Ingresos/Egresos)]
        I[(Empresas)]
        J[(Usuarios)]
    end

    A -->|Solicitudes HTTP| B
    B -->|Rutas Laravel| F
    F -->|Autenticación| C
    C -->|Lógica de Negocio| D
    D -->|Consultas SQL| G
    D -->|Consultas SQL| H
    D -->|Consultas SQL| I
    D -->|Consultas SQL| J
    C -->|Datos| E
    E -->|HTML Renderizado| B
    B -->|Respuesta| A

    style A fill:#e1f5ff
    style B fill:#fff4e1
    style C fill:#ffe1e1
    style D fill:#e1ffe1
    style G fill:#f0e1ff
    style H fill:#f0e1ff
    style I fill:#f0e1ff
    style J fill:#f0e1ff
```

### Stack Tecnológico

| Capa | Tecnología | Versión | Propósito |
|------|------------|---------|-----------|
| **Backend** | Laravel Framework | 10.x | Framework PHP principal |
| **Frontend** | Blade Templates | - | Motor de plantillas |
| **Base de Datos** | MySQL | 8.0+ | Almacenamiento de datos |
| **Estilos** | Bootstrap | 5.3 | Framework CSS |
| **Iconos** | RemixIcon | 3.5 | Biblioteca de iconos |
| **Autenticación** | Laravel Auth | - | Sistema de autenticación |
| **Validación** | Laravel Validation | - | Validación de formularios |

---

## 🚀 Instalación y Configuración

### Requisitos Previos

```
✅ PHP 8.1 o superior
✅ MySQL 8.0 o superior
✅ Composer 2.x
✅ Node.js 16.x o superior (para assets)
✅ Servidor web (Apache/Nginx)
```

### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/tu-organizacion/ictgk_portal.git
cd ictgk_portal
```

### Paso 2: Instalar Dependencias

```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node
npm install
```

### Paso 3: Configurar el Entorno

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### Paso 4: Configurar Base de Datos

Editar el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictgk_portal
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### Paso 5: Ejecutar Migraciones

```bash
# Crear las tablas
php artisan migrate

# Cargar datos iniciales (opcional)
php artisan db:seed
```

### Paso 6: Aplicar Cambios de Base de Datos

```bash
# Ejecutar script de mejoras
mysql -u root -p ictgk_portal < database/scripts/aplicar_cambios_completos.sql
```

### Paso 7: Compilar Assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### Paso 8: Iniciar el Servidor

```bash
# Servidor de desarrollo
php artisan serve

# El sistema estará disponible en: http://localhost:8000
```

### 🔧 Configuración Adicional

#### Permisos de Directorios

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### Configurar Email (Opcional)

Editar `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=tu-servidor-smtp
MAIL_PORT=587
MAIL_USERNAME=tu-email
MAIL_PASSWORD=tu-contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="Portal ICTGK"
```

---

## 👥 Manual de Usuario

### Roles y Permisos

El sistema cuenta con diferentes roles, cada uno con permisos específicos:

```mermaid
graph LR
    A[Super Admin] -->|Control Total| B[Todas las Funciones]
    C[Admin Empresa] -->|Gestión| D[Su Empresa]
    E[Usuario RH] -->|Consulta| F[Candidatos e Ingresos]
    G[Usuario Consulta] -->|Solo Lectura| H[Fichas]

    style A fill:#ff6b6b
    style C fill:#4ecdc4
    style E fill:#95e1d3
    style G fill:#f9ca24
```

#### Descripción de Roles

| Rol | Permisos | Casos de Uso |
|-----|----------|--------------|
| **Super Admin** | • Acceso total<br>• Gestionar empresas<br>• Gestionar usuarios<br>• Bloquear candidatos | Administración del sistema |
| **Admin Empresa** | • Gestionar candidatos<br>• Ingresos/Egresos<br>• Ver reportes<br>• Importar datos | Recursos Humanos de empresa |
| **Usuario RH** | • Consultar candidatos<br>• Ver fichas<br>• Reportes básicos | Personal de RH |
| **Usuario Consulta** | • Ver fichas<br>• Consultar información | Supervisores, Gerentes |

---

## 📋 Gestión de Candidatos

### ➕ Agregar Nuevo Candidato

```mermaid
graph TD
    A[Inicio] --> B[Clic en 'Nuevo Candidato']
    B --> C[Llenar Formulario]
    C --> D{¿Datos Válidos?}
    D -->|No| E[Mostrar Errores]
    E --> C
    D -->|Sí| F[Verificar si Existe]
    F --> G{¿Ya Existe?}
    G -->|Sí| H[Mostrar Alerta]
    G -->|No| I[Guardar en BD]
    I --> J[Candidato Creado ✓]
    J --> K[Fin]

    style A fill:#e3f2fd
    style K fill:#c8e6c9
    style H fill:#ffcdd2
```

#### Pasos Detallados:

1. **Acceder al módulo de Candidatos**
   ```
   Dashboard → Candidatos → Nuevo Candidato
   ```

2. **Llenar el formulario**

| Campo | Descripción | Ejemplo | Requerido |
|-------|-------------|---------|-----------|
| Identidad | Número de identificación (sin guiones) | 0501199200350 | ✅ Sí |
| Nombre | Nombre(s) del candidato | JUAN CARLOS | ✅ Sí |
| Apellido | Apellido(s) | PÉREZ LÓPEZ | ✅ Sí |
| Teléfono | Número de contacto | 9999-9999 | ✅ Sí |
| Correo | Email de contacto | juan@email.com | ✅ Sí |
| Dirección | Dirección completa | Col. Centro, Tegucigalpa | ✅ Sí |
| Género | M o F | M | ✅ Sí |
| Fecha Nacimiento | Fecha en formato YYYY-MM-DD | 1992-03-15 | ✅ Sí |

3. **Hacer clic en "Guardar"**

4. **El sistema validará:**
   - ✅ Que no exista el candidato
   - ✅ Formato de identidad correcto
   - ✅ Formato de email válido
   - ✅ Fecha de nacimiento válida

### 📥 Importación Masiva de Candidatos

Para registrar múltiples candidatos a la vez:

```mermaid
sequenceDiagram
    participant U as Usuario
    participant S as Sistema
    participant V as Validador
    participant BD as Base de Datos

    U->>S: Subir archivo CSV
    S->>V: Validar formato
    V->>V: Verificar columnas
    V->>V: Validar datos
    alt Archivo válido
        V->>BD: Insertar registros
        BD->>S: Confirmar
        S->>U: Reporte de éxito
    else Errores encontrados
        V->>S: Lista de errores
        S->>U: Mostrar errores detallados
    end
```

#### Formato del Archivo CSV

**Columnas requeridas** (en este orden):

```csv
id_empresa,fechaIngreso,area,id_puesto,identidad,nombre,apellido,telefono,correo,direccion,generoM_F,fecha_nacimiento
1,2024-01-15,Producción,5,0501199200350,JUAN,PEREZ,9999-9999,juan@email.com,Tegucigalpa,M,1992-03-15
1,2024-01-20,Administración,8,0501199200351,MARIA,LOPEZ,8888-8888,maria@email.com,San Pedro Sula,F,1990-05-20
```

**Notas importantes:**
- ⚠️ No incluir guiones en la identidad
- 📅 Fechas en formato: YYYY-MM-DD o DD/MM/YYYY
- 🔤 Género: M o F (una letra)
- 📧 Email válido y único
- 📝 Todas las columnas son obligatorias

#### Pasos para Importar:

1. Preparar archivo CSV con el formato correcto
2. `Candidatos → Importar → Seleccionar Archivo`
3. El sistema mostrará un resumen:
   ```
   ✅ Registros válidos: 45
   ⚠️ Registros con errores: 3
   ℹ️ Registros duplicados: 2
   ```
4. Revisar errores si existen
5. Confirmar importación

### 🔍 Buscar Candidatos

```mermaid
graph LR
    A[Buscar por] --> B[Identidad]
    A --> C[Nombre]
    A --> D[Apellido]
    A --> E[Estado]

    B --> F[Resultados]
    C --> F
    D --> F
    E --> F

    F --> G[Ver Ficha]
    F --> H[Editar]
    F --> I[Historial]

    style A fill:#fff3cd
    style F fill:#d1ecf1
```

**Filtros disponibles:**

| Filtro | Descripción | Ejemplo |
|--------|-------------|---------|
| 🆔 Identidad | Búsqueda exacta | 0501199200350 |
| 👤 Nombre | Búsqueda parcial | Juan |
| 📝 Apellido | Búsqueda parcial | Pérez |
| 🏢 Empresa Actual | Filtrar por empresa | ALTIA Manufacturing |
| ✅ Estado | Disponible / Trabajando / Bloqueado | Disponible |

---

## 📊 Gestión de Ingresos

### ➕ Registrar Nuevo Ingreso

```mermaid
graph TD
    A[Buscar Candidato] --> B{¿Existe?}
    B -->|No| C[Crear Candidato Primero]
    C --> D[Registrar Ingreso]
    B -->|Sí| E{¿Estado?}
    E -->|Disponible| D
    E -->|Trabajando| F[Alerta: Ya tiene ingreso activo]
    E -->|Bloqueado| G[Solicitar Autorización RH]
    D --> H[Seleccionar Empresa]
    H --> I[Seleccionar Puesto]
    I --> J{¿Puesto válido?}
    J -->|Sí| K[Seleccionar Fecha Ingreso]
    J -->|No| L[Error: Puesto no pertenece a empresa]
    L --> I
    K --> M[Agregar Comentarios]
    M --> N[Guardar]
    N --> O[Ingreso Registrado ✅]

    style A fill:#e3f2fd
    style O fill:#c8e6c9
    style F fill:#fff3cd
    style G fill:#ffcdd2
    style L fill:#ffcdd2
```

#### Flujo Detallado de Ingreso:

**1. Validaciones Automáticas del Sistema:**

| Validación | Descripción | Acción |
|------------|-------------|--------|
| ✅ Candidato existe | Verifica que el candidato esté registrado | Si no existe, debe crearse primero |
| ✅ No tiene ingreso activo | Verifica que no esté trabajando en otra empresa | Si está activo, muestra alerta |
| ✅ No está bloqueado | Verifica bloqueos o recomendaciones negativas | Si está bloqueado, solicita autorización |
| ✅ Puesto-Empresa | Valida que el puesto pertenezca a la empresa | Error automático si no coincide |

**2. Datos del Formulario de Ingreso:**

```
┌─────────────────────────────────────┐
│   FORMULARIO DE INGRESO             │
├─────────────────────────────────────┤
│ 🆔 Identidad: [0501199200350    ]  │
│ 👤 Nombre: JUAN CARLOS PÉREZ        │
│ 🏢 Empresa: [Seleccionar ▼]        │
│ 🏭 Área: [Producción           ]   │
│ 💼 Puesto: [Seleccionar ▼]         │
│ 📅 Fecha Ingreso: [YYYY-MM-DD]     │
│ 📝 Comentarios: [____________]      │
│                                     │
│  [Cancelar]  [💾 Guardar Ingreso] │
└─────────────────────────────────────┘
```

**3. Casos Especiales:**

```mermaid
graph TD
    A[Intentar Ingreso] --> B{¿Es Recontratación?}
    B -->|Sí| C[Sistema detecta ingreso anterior inactivo]
    C --> D[Marcar como 'Recontratado']
    D --> E[Crear nuevo ingreso]

    B -->|No| F{¿Tiene bloqueo?}
    F -->|Sí| G[Mostrar información de bloqueo]
    G --> H[Botón: Solicitar Autorización]
    H --> I[Enviar email a RH ALTIA]

    F -->|No| E
    E --> J[Ingreso completado ✅]

    style J fill:#c8e6c9
    style G fill:#ffcdd2
```

### 📥 Importación Masiva de Ingresos

Permite importar múltiples ingresos simultáneamente.

**Formato CSV:**

```csv
id_empresa,fechaIngreso,area,id_puesto,identidad,nombre,apellido,telefono,correo,direccion,generoM_F,fecha_nacimiento
1,2024-02-01,Producción,5,0501199200350,JUAN,PEREZ,9999-9999,juan@email.com,Tegucigalpa,M,1992-03-15
```

**Proceso de importación:**

```
1. Subir archivo → 2. Validación → 3. Pre-visualización → 4. Confirmar → 5. Resultado
     📄              ✅ ⚠️ ❌           📊                   ☑️            ✅ 45/50
```

**Resultado de la Importación:**

| Estado | Descripción | Icono |
|--------|-------------|-------|
| ✅ Registro nuevo | Candidato e ingreso creados exitosamente | 🟢 |
| ⚠️ Ya existe en misma empresa | El candidato ya tiene ingreso activo | 🟡 |
| 🔄 Recontratado | Candidato tuvo ingreso anterior, reingreso | 🔵 |
| ❌ Bloqueado | Candidato tiene bloqueo, requiere autorización | 🔴 |
| ⚠️ En otra empresa | Ya está trabajando en otra empresa | 🟠 |

---

## 📤 Gestión de Egresos

### ➕ Registrar Egreso

```mermaid
graph TD
    A[Buscar Candidato Activo] --> B{¿Tiene ingreso activo?}
    B -->|No| C[Error: No tiene ingreso activo]
    B -->|Sí| D[Mostrar Datos del Ingreso]
    D --> E[Seleccionar Fecha Egreso]
    E --> F[Seleccionar Tipo de Egreso]
    F --> G[Seleccionar Forma de Egreso]
    G --> H{¿Es Recomendado?}
    H -->|Sí| I[Marcar como Recomendado]
    H -->|No| J[Marcar como No Recomendado]
    I --> K[Agregar Comentarios]
    J --> K
    K --> L[Guardar Egreso]
    L --> M[Actualizar Estado Candidato]
    M --> N[Egreso Completado ✅]

    style A fill:#e3f2fd
    style N fill:#c8e6c9
    style C fill:#ffcdd2
```

#### Formulario de Egreso

```
┌──────────────────────────────────────────┐
│   REGISTRO DE EGRESO                     │
├──────────────────────────────────────────┤
│ 🆔 Identidad: 0501199200350              │
│ 👤 Nombre: JUAN CARLOS PÉREZ              │
│ 🏢 Empresa: ALTIA Manufacturing           │
│ 💼 Puesto: Operador de Producción        │
│ 📅 Fecha Ingreso: 2024-01-15             │
│                                          │
│ ─────────────────────────────────────   │
│                                          │
│ 📅 Fecha Egreso: [YYYY-MM-DD]           │
│                                          │
│ 🏷️ Tipo de Egreso:                       │
│    ○ Renuncia Voluntaria                 │
│    ○ Despido                             │
│    ○ Fin de Contrato                     │
│    ○ Abandono de Trabajo                 │
│    ○ Jubilación                          │
│    ○ Otro: [________]                    │
│                                          │
│ 📝 Forma de Egreso:                      │
│    ○ Con Preaviso                        │
│    ○ Sin Preaviso                        │
│    ○ Mutuo Acuerdo                       │
│                                          │
│ ⭐ ¿Es Recomendado?                      │
│    ○ Sí, lo recomendaría                 │
│    ○ No, no lo recomendaría              │
│                                          │
│ 🔄 ¿Lo recontrataría?                    │
│    ○ Sí                                  │
│    ○ No                                  │
│                                          │
│ 💬 Comentarios:                          │
│ [___________________________]            │
│ [___________________________]            │
│ [___________________________]            │
│                                          │
│  [Cancelar]  [💾 Guardar Egreso]        │
└──────────────────────────────────────────┘
```

#### Tipos de Egreso

| Tipo | Descripción | Impacto |
|------|-------------|---------|
| 🚪 Renuncia Voluntaria | El empleado decide retirarse | Neutral |
| ⚠️ Despido | Terminación por parte de la empresa | Posible bloqueo |
| 📄 Fin de Contrato | Contrato temporal finalizado | Neutral |
| 🏃 Abandono de Trabajo | Empleado dejó de asistir | Probable bloqueo |
| 🎂 Jubilación | Retiro por edad | Positivo |
| ❓ Otro | Otros motivos | Según caso |

### 🔒 Bloqueo de Candidatos

Si un candidato tiene problemas graves, puede ser bloqueado:

```mermaid
sequenceDiagram
    participant U as Usuario RH
    participant S as Sistema
    participant BD as Base de Datos
    participant E as Email

    U->>S: Solicitar bloqueo de candidato
    S->>U: Mostrar formulario de bloqueo
    U->>S: Justificación del bloqueo
    S->>BD: Marcar candidato como 'x'
    BD->>S: Confirmación
    S->>E: Notificar a RH ALTIA
    E->>U: Confirmación de bloqueo
    S->>U: Candidato bloqueado
```

**Motivos de Bloqueo:**
- 🚫 Robo
- ⚠️ Conducta inapropiada grave
- 📉 Bajo desempeño recurrente
- 🚨 Violencia o acoso
- 📋 Falsificación de documentos
- 🏃 Abandono sin justificación

**Efectos del Bloqueo:**
- ❌ No se puede ingresar en ninguna empresa
- ⚠️ Aparece alerta al buscar
- 📧 Requiere autorización de RH ALTIA para desbloquearse

---

## 🔍 Consulta de Fichas

### Ver Ficha Personal

La ficha personal muestra toda la información del candidato en una vista unificada:

```
┌─────────────────────────────────────────────────────────────────┐
│                    FICHA PERSONAL                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌────────┐    JUAN CARLOS PÉREZ LÓPEZ                         │
│  │  👤    │    🆔 0501199200350                                 │
│  │  Foto  │    🎂 15/03/1992 (32 años)                         │
│  └────────┘    ♂️ Masculino                                     │
│                                                                  │
│  📞 Contacto              🏢 Empresa Actual                     │
│  ├─ Tel: 9999-9999        ├─ ALTIA Manufacturing                │
│  ├─ Email: juan@email.com ├─ Puesto: Operador                  │
│  └─ Dir: Tegucigalpa      └─ Ingreso: 15/01/2024               │
│                                                                  │
│  📊 Estado: ⚪ Trabajando                                       │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                    HISTORIAL LABORAL                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  🏢 ALTIA Manufacturing                                          │
│  ├─ Puesto: Operador de Producción                             │
│  ├─ Ingreso: 15/01/2024                                         │
│  ├─ Estado: ✅ Activo                                           │
│  └─ Área: Producción                                            │
│                                                                  │
│  🏢 ALTIA Logistics (Anterior)                                  │
│  ├─ Puesto: Auxiliar de Bodega                                 │
│  ├─ Ingreso: 10/03/2022                                         │
│  ├─ Egreso: 30/12/2023                                          │
│  ├─ Duración: 1 año 9 meses                                    │
│  ├─ Recomendado: ✅ Sí                                          │
│  └─ Motivo egreso: Renuncia voluntaria                         │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Búsqueda Rápida

```mermaid
graph LR
    A[🔍 Buscar] --> B[Ingresar Identidad]
    B --> C{¿Encontrado?}
    C -->|Sí| D[Mostrar Ficha]
    C -->|No| E[Candidato no encontrado]

    D --> F[Ver Historial]
    D --> G[Hacer Ingreso]
    D --> H[Hacer Egreso]
    D --> I[Editar Datos]

    style D fill:#c8e6c9
    style E fill:#ffcdd2
```

### Estados del Candidato

```
┌─────────────────────────────────────────────┐
│  Estados Posibles del Candidato             │
├─────────────────────────────────────────────┤
│                                             │
│  🟢 Disponible (s)                          │
│  └─ Puede ser ingresado a cualquier empresa│
│                                             │
│  🔵 Trabajando (n)                          │
│  └─ Tiene ingreso activo en una empresa    │
│                                             │
│  🔴 Bloqueado (x)                           │
│  └─ Requiere autorización para ingresar    │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 📊 Reportes e Informes

### Tipos de Reportes Disponibles

```mermaid
graph TD
    A[Reportes] --> B[Por Empresa]
    A --> C[Por Candidato]
    A --> D[Por Período]
    A --> E[Estadísticas]

    B --> B1[Ingresos del mes]
    B --> B2[Egresos del mes]
    B --> B3[Personal activo]

    C --> C1[Historial completo]
    C --> C2[Recomendaciones]

    D --> D1[Rotación mensual]
    D --> D2[Tendencias]

    E --> E1[Tasa de retención]
    E --> E2[Motivos de egreso]

    style A fill:#fff3cd
```

### Usar Stored Procedures para Reportes

El sistema incluye consultas optimizadas:

```sql
-- Listar candidatos de una empresa
CALL sp_listar_candidatos_ingresos(1);

-- Ver historial de un candidato
CALL sp_historial_candidato('0501199200350');
```

### Exportar a Excel

1. Seleccionar los filtros deseados
2. Clic en **"Exportar a Excel"**
3. El archivo se descargará automáticamente
4. Formato `.xlsx` compatible con Excel y LibreOffice

---

## 🔄 Flujos de Trabajo

### Flujo Completo: Desde Candidato hasta Egreso

```mermaid
graph TD
    A[👤 Nuevo Candidato] -->|Registro| B[📝 Base de Candidatos]
    B -->|Selección| C[📊 Proceso de Selección]
    C -->|Aprobado| D[✅ Candidato Disponible]
    D -->|Asignación| E[📥 Ingreso a Empresa]
    E --> F[💼 Colaborador Activo]
    F -->|Tiempo| G{¿Continúa?}
    G -->|Sí| F
    G -->|No| H[📤 Proceso de Egreso]
    H --> I{¿Recomendado?}
    I -->|Sí| J[✅ Candidato Disponible]
    I -->|No| K[⚠️ Candidato con Observaciones]
    J --> D
    K --> L{¿Requiere Bloqueo?}
    L -->|Sí| M[🔒 Candidato Bloqueado]
    L -->|No| D

    style A fill:#e3f2fd
    style F fill:#c8e6c9
    style M fill:#ffcdd2
```

### Flujo de Recontratación

```mermaid
sequenceDiagram
    participant C as Candidato
    participant S as Sistema
    participant RH as RRHH
    participant E as Empresa

    Note over C: Tiene egreso anterior
    RH->>S: Buscar candidato
    S->>S: Verificar historial
    S->>RH: Mostrar egresos anteriores
    RH->>RH: Revisar recomendaciones

    alt Si fue recomendado
        RH->>S: Solicitar reingreso
        S->>S: Validar disponibilidad
        S->>E: Crear nuevo ingreso
        E->>S: Confirmar
        S->>RH: Recontratación exitosa ✅
        Note over C: Estado: Recontratado
    else Si no fue recomendado
        RH->>S: Solicitar autorización
        S->>E: Enviar solicitud a RH ALTIA
        E->>RH: Evaluar caso
        alt Autorizado
            RH->>S: Aprobar reingreso
            S->>RH: Recontratación autorizada ✅
        else Rechazado
            RH->>S: Rechazar
            S->>RH: Recontratación denegada ❌
        end
    end
```

---

## 🗄️ Estructura de Base de Datos

### Diagrama Entidad-Relación

```mermaid
erDiagram
    CANDIDATOS ||--o{ EGRESOS_INGRESOS : tiene
    EMPRESAS ||--o{ DEPARTAMENTOS : contiene
    EMPRESAS ||--o{ EGRESOS_INGRESOS : registra
    DEPARTAMENTOS ||--o{ PUESTOS : tiene
    PUESTOS ||--o{ EGRESOS_INGRESOS : asigna
    USUARIOS }o--|| EMPRESAS : pertenece
    USUARIOS }o--|| PERFILES : tiene

    CANDIDATOS {
        int id PK
        string identidad UK
        string nombre
        string apellido
        string telefono
        string correo
        string direccion
        char generoM_F
        date fecha_nacimiento
        char activo
        json comentarios
    }

    EGRESOS_INGRESOS {
        int id PK
        string identidad FK
        int id_empresa FK
        date fechaIngreso
        string area
        int id_puesto FK
        char activo
        date fechaEgreso
        string tipo_egreso
        string forma_egreso
        string Comentario
        char recomendado
        char prohibirIngreso
    }

    EMPRESAS {
        int id PK
        string nombre
        string descripcion
    }

    DEPARTAMENTOS {
        int id PK
        string nombredepartamento
        int empresa_id FK
    }

    PUESTOS {
        int id PK
        string nombrepuesto
        int departamento_id FK
    }

    USUARIOS {
        int id PK
        string name
        string email
        int empresa_id FK
        int perfil_id FK
    }
```

### Relaciones Clave

```
Candidatos
    ↓
    ├─→ puede tener múltiples → Ingresos/Egresos
    │
Empresas
    ↓
    ├─→ tiene múltiples → Departamentos
    │                        ↓
    │                        └─→ tiene múltiples → Puestos
    │
    └─→ registra múltiples → Ingresos/Egresos
```

### Índices Implementados (Optimización)

| Tabla | Índice | Columnas | Propósito |
|-------|--------|----------|-----------|
| candidatos | idx_candidatos_identidad | identidad | Búsqueda rápida |
| egresos_ingresos | idx_egresos_identidad_activo | identidad, activo | Búsqueda de activos |
| egresos_ingresos | idx_egresos_id_puesto | id_puesto | Joins optimizados |
| puestos | idx_puestos_departamento | departamento_id | Relaciones |
| departamentos | idx_departamentos_empresa | empresa_id | Relaciones |

### Constraints y Validaciones

```mermaid
graph LR
    A[egresos_ingresos] -->|FK| B[puestos]
    B -->|FK| C[departamentos]
    C -->|FK| D[empresas]

    A -->|Trigger| E{Validar<br/>Puesto-Empresa}
    E -->|✅ Válido| F[Permitir INSERT/UPDATE]
    E -->|❌ Inválido| G[Rechazar Operación]

    style F fill:#c8e6c9
    style G fill:#ffcdd2
```

---

## 🔧 Solución de Problemas

### Problemas Comunes y Soluciones

#### 1. Error: "Datos Inconsistentes"

**Problema:** Al buscar un candidato aparece mensaje de datos inconsistentes.

**Causa:** Existen ingresos sin candidato asociado en la base de datos.

**Solución:**

```bash
# 1. Ejecutar script de diagnóstico
mysql -u root -p ictgk_portal < database/scripts/identificar_ingresos_sin_candidatos.sql

# 2. Corregir datos manualmente o contactar a RH ALTIA
```

**Vista del Error:**

```
┌─────────────────────────────────────────┐
│  ⚠️  Datos Inconsistentes                │
├─────────────────────────────────────────┤
│                                         │
│  Se encontraron registros de ingresos  │
│  sin información del candidato          │
│                                         │
│  Identidad: 0501199200350               │
│  Registros afectados: 2                 │
│                                         │
│  [⬅️ Volver]  [📧 Reportar a RRHH]     │
└─────────────────────────────────────────┘
```

#### 2. Error: "El puesto no pertenece a la empresa"

**Problema:** Al intentar hacer un ingreso, el sistema rechaza el puesto seleccionado.

**Causa:** El puesto que se está intentando asignar no pertenece a la empresa seleccionada.

**Solución:**
1. Verificar que la empresa sea correcta
2. Seleccionar un puesto que sí pertenezca a esa empresa
3. Si el puesto es correcto, contactar al administrador para verificar la configuración

#### 3. Error: "Ya existe en la misma empresa"

**Problema:** No se puede ingresar a un candidato que ya tiene un ingreso activo.

**Solución:**
1. Verificar el estado actual del candidato
2. Si debe ser reingresado, primero hacer el egreso del ingreso anterior
3. Luego proceder con el nuevo ingreso

#### 4. Candidato no aparece en búsqueda

**Problema:** Un candidato que fue registrado no aparece al buscar.

**Posibles causas y soluciones:**

| Causa | Solución |
|-------|----------|
| Identidad incorrecta | Verificar formato de identidad (sin guiones) |
| Candidato eliminado | Verificar con administrador |
| Error de importación | Revisar logs de importación |
| Problema de base de datos | Contactar soporte técnico |

#### 5. Importación CSV falla

**Problema:** El archivo CSV no se puede importar.

**Checklist de verificación:**

```
✅ Formato de archivo es .csv
✅ Todas las columnas requeridas están presentes
✅ Las fechas están en formato correcto (YYYY-MM-DD)
✅ Las identidades no tienen guiones
✅ Los emails son válidos
✅ El archivo está codificado en UTF-8
✅ No hay líneas vacías al final del archivo
```

---

## 🆕 Cambios Recientes

### Versión 2.0 (Febrero 2026)

#### 🔐 Mejoras de Seguridad y Validación

**Integridad de Datos:**
- ✅ Implementadas Foreign Keys para garantizar relaciones válidas
- ✅ Triggers para validar que el puesto pertenezca a la empresa
- ✅ Validación automática de datos antes de insertar

**Rendimiento:**
- ⚡ 5 nuevos índices para consultas 6-10x más rápidas
- ⚡ Stored Procedures optimizados para reportes
- ⚡ Caché de consultas frecuentes

**Experiencia de Usuario:**
- 🎨 Nueva vista de error amigable para datos inconsistentes
- 📝 Mensajes de error claros y específicos
- 🔍 Logging automático de problemas para debugging
- 💾 Protección contra datos nulos en las vistas

#### 📚 Nueva Documentación

- 📖 Guía completa de cambios de base de datos
- ✅ Checklist de implementación
- 🔧 Scripts de diagnóstico y corrección
- 📊 Manual de usuario actualizado (este README)

#### 🛠️ Herramientas Nuevas

```bash
# Script de diagnóstico
database/scripts/identificar_ingresos_sin_candidatos.sql

# Script de implementación completa
database/scripts/aplicar_cambios_completos.sql

# Ejemplos de uso
database/scripts/ejemplos_uso_sp.sql
```

### Para Actualizar a Versión 2.0

```bash
# 1. Hacer backup
mysqldump -u root -p ictgk_portal > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Aplicar cambios
mysql -u root -p ictgk_portal < database/scripts/aplicar_cambios_completos.sql

# 3. Verificar
mysql -u root -p ictgk_portal < database/scripts/ejemplos_uso_sp.sql
```

**Documentación completa:** Ver [GUIA_CAMBIOS_BASE_DATOS.md](GUIA_CAMBIOS_BASE_DATOS.md)

---

## 📞 Soporte y Contacto

### 🆘 ¿Necesitas Ayuda?

#### Soporte Técnico

| Tipo de Problema | Contacto | Respuesta |
|------------------|----------|-----------|
| 🐛 **Errores del Sistema** | portal.reclutamiento@altiabusinesspark.com | 24-48 horas |
| 🔐 **Problemas de Acceso** | Administrador de tu empresa | Inmediato |
| 📊 **Dudas sobre Reportes** | portal.reclutamiento@altiabusinesspark.com | 24 horas |
| 💡 **Sugerencias** | GitHub Issues o email | Variable |
| 🚨 **Urgencias** | Llamar directamente a RH ALTIA | Inmediato |

### 📚 Recursos Adicionales

| Recurso | Ubicación | Descripción |
|---------|-----------|-------------|
| **Guía de Cambios BD** | [GUIA_CAMBIOS_BASE_DATOS.md](GUIA_CAMBIOS_BASE_DATOS.md) | Cambios técnicos de base de datos |
| **Checklist Rápido** | [CHECKLIST_RAPIDO.md](CHECKLIST_RAPIDO.md) | Pasos de implementación |
| **Cambios de Código** | [CAMBIOS_FICHA_PERSONAL.md](CAMBIOS_FICHA_PERSONAL.md) | Detalles de modificaciones |
| **Scripts SQL** | `/database/scripts/` | Scripts de mantenimiento |

### 🎓 Capacitación

Para solicitar capacitación sobre el uso del sistema:

1. 📧 Enviar email a: portal.reclutamiento@altiabusinesspark.com
2. 📋 Incluir:
   - Nombre de la empresa
   - Número de usuarios a capacitar
   - Temas específicos de interés
   - Disponibilidad de horario

---

## 📄 Licencia y Derechos

**Sistema Propietario** - ALTIA Business Park
- © 2024-2026 ALTIA Business Park
- Todos los derechos reservados
- Uso exclusivo para empresas del grupo ALTIA

---

## 🙏 Agradecimientos

Desarrollado con ❤️ por el equipo de TI de ALTIA Business Park

**Tecnologías utilizadas:**
- [Laravel Framework](https://laravel.com) - Framework PHP
- [Bootstrap](https://getbootstrap.com) - Framework CSS
- [RemixIcon](https://remixicon.com) - Iconos
- [MySQL](https://www.mysql.com) - Base de datos
- [Mermaid](https://mermaid.js.org) - Diagramas

---

<div align="center">

**Portal ICTGK v2.0**

[⬆️ Volver al inicio](#-portal-ictgk---sistema-de-gestión-de-candidatos)

</div>
