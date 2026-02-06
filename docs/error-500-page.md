# Documentación: Página de Error 500 Personalizada

## Descripción General

Se creó una vista personalizada para el error HTTP 500 (Internal Server Error) de Laravel, alineada con la identidad visual del Portal HHRR.

**Archivo creado:** `resources/views/errors/500.blade.php`

---

## Funcionamiento

Laravel detecta automáticamente las vistas dentro de `resources/views/errors/` y las utiliza cuando ocurre el error HTTP correspondiente. Al existir `500.blade.php`, Laravel la renderiza en lugar de la página de error genérica cuando se produce una excepción no controlada en el servidor.

No se requiere configuración adicional en rutas, controladores ni en el `Handler.php` de excepciones.

---

## Paleta de Colores Utilizada

| Elemento              | Color                          | Código                     |
|-----------------------|--------------------------------|----------------------------|
| Fondo principal       | Navy oscuro (gradiente)        | `#072132` → `#0a2e45`     |
| Acento primario       | Verde institucional            | `#32C36C`                  |
| Acento secundario     | Verde oscuro                   | `#28a85a`                  |
| Texto principal       | Blanco                         | `#FFFFFF` (90% opacidad)   |
| Texto secundario      | Blanco tenue                   | `#FFFFFF` (50% opacidad)   |
| Card background       | Blanco translúcido             | `rgba(255,255,255, 0.05)`  |
| Borde card            | Blanco translúcido             | `rgba(255,255,255, 0.08)`  |

---

## Componentes Visuales

### Logo
- Se muestra el logo blanco de Altia (`storage/altialogoblanco.png`) en la parte superior de la tarjeta.

### Icono
- Icono de servidor (`ri-server-line`) de la librería RemixIcon.
- Contenido en un círculo con gradiente verde y animación de rebote (`iconBounce`).

### Código de Error
- "500" mostrado en tipografía grande (72px, peso 800).
- El "0" central resaltado en verde `#32C36C` para dar énfasis visual.

### Mensaje
- **Título:** "Error interno del servidor"
- **Descripción:** Mensaje amigable informando que el equipo fue notificado.

### Botones de Acción
| Botón         | Acción                  | Estilo                          |
|---------------|-------------------------|---------------------------------|
| Ir al inicio  | Redirige a `url('/')`  | Verde primario con gradiente    |
| Volver        | `window.history.back()` | Transparente con borde blanco  |

---

## Animaciones CSS

| Animación    | Descripción                                     | Duración |
|--------------|------------------------------------------------|----------|
| `float`      | Movimiento flotante de partículas decorativas  | 6-8s     |
| `slideUp`    | Entrada de la tarjeta desde abajo              | 0.8s     |
| `iconBounce` | Rebote sutil del icono del servidor            | 3s       |
| `pulse`      | Escala pulsante (disponible para uso futuro)   | -        |

---

## Diseño Responsivo

La vista incluye media queries para pantallas menores a 576px:

- Padding de la tarjeta reducido (`35px 25px`)
- Código de error reducido a `56px`
- Título reducido a `18px`
- Botones apilados verticalmente

---

## Dependencias Externas

| Recurso                | Versión | Propósito                |
|------------------------|---------|--------------------------|
| Google Fonts (Nunito)  | -       | Tipografía de respaldo   |
| RemixIcon              | 3.5.0   | Iconografía              |

> **Nota:** La vista es autónoma (no extiende el layout principal) para garantizar que se renderice correctamente incluso cuando el error ocurre durante la carga del layout.

---

## Estructura de Archivos

```
resources/views/errors/
└── 500.blade.php    ← Página de error 500 personalizada
```

---

## Consideraciones Técnicas

1. **Vista standalone:** No utiliza `@extends('layouts.app')` intencionalmente, ya que un error 500 podría originarse en el propio layout, causando un bucle infinito.
2. **CSS inline:** Los estilos están embebidos directamente en la vista para evitar dependencias de Vite/assets que podrían no estar disponibles durante un error del servidor.
3. **Glassmorphism:** Se usa `backdrop-filter: blur(20px)` para el efecto de cristal esmerilado en la tarjeta.
4. **Partículas decorativas:** Implementadas con pseudo-elementos `::before` y `::after` del body, sin JavaScript adicional.
