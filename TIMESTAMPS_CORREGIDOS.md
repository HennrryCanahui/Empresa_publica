# Corrección de Timestamps en Modelos
**Fecha**: 25 de Octubre de 2025

## Problema Identificado
Error al crear solicitud:
```
ORA-00904: "CREATED_AT": identificador no válido
```

Laravel intentaba insertar `created_at` y `updated_at` en tablas que no tienen estas columnas.

---

## Modelos Corregidos

### 1. ✅ Solicitud
**Tabla**: `SOLICITUD`  
**Columnas timestamps en BD**: Solo `updated_at`  
**Antes**: `public $timestamps = true;`  
**Después**: `public $timestamps = false;`

**Nota**: La tabla tiene `updated_at` pero NO `created_at`. Se maneja `updated_at` manualmente.

---

### 2. ✅ Presupuesto
**Tabla**: `PRESUPUESTO`  
**Columnas timestamps en BD**: Ninguna  
**Estado**: Ya estaba corregido en revisión anterior  
**Config**: `public $timestamps = false;`

---

### 3. ✅ Aprobacion
**Tabla**: `APROBACION`  
**Columnas timestamps en BD**: Ninguna  
**Antes**: `public $timestamps = true;`  
**Después**: `public $timestamps = false;`

**Agregados casts**:
```php
protected $casts = [
    'fecha_aprobacion' => 'datetime',
    'monto_aprobado' => 'decimal:2'
];
```

---

### 4. ✅ Documento_adjunto
**Tabla**: `DOCUMENTO_ADJUNTO`  
**Columnas timestamps en BD**: Solo `created_at`  
**Antes**: `public $timestamps = true;`  
**Después**: 
```php
public $timestamps = false;
const CREATED_AT = 'created_at';
const UPDATED_AT = null;
```

---

## Modelos que SÍ tienen timestamps completos (NO modificados)

### ✅ User (USUARIO)
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

### ✅ Unidad
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

### ✅ Categoria_producto
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

### ✅ Catalogo_producto
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

### ✅ Proveedor
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

### ✅ Detalle_solicitud
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

### ✅ Cotizacion
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

### ✅ Adquisicion
- Tiene `created_at` y `updated_at` en BD
- `public $timestamps = true;` ✓ Correcto

---

## Modelos sin timestamps (ya correctos)

### ✅ Historial_estados
- NO tiene `created_at` ni `updated_at` (usa `fecha_cambio`)
- Verificar que tenga `public $timestamps = false;`

### ✅ Auditoria
- NO tiene `created_at` ni `updated_at` (usa `fecha_accion`)
- Verificar que tenga `public $timestamps = false;`

### ✅ Detalle_Cotizacion
- NO tiene timestamps
- Verificar configuración

---

## Resumen según SQL Oracle

```sql
-- TABLAS CON created_at Y updated_at:
- UNIDAD
- USUARIO
- CATEGORIA_PRODUCTO
- CATALOGO_PRODUCTO
- PROVEEDOR
- DETALLE_SOLICITUD
- COTIZACION
- ADQUISICION

-- TABLAS CON SOLO updated_at:
- SOLICITUD

-- TABLAS CON SOLO created_at:
- DOCUMENTO_ADJUNTO

-- TABLAS SIN TIMESTAMPS:
- PRESUPUESTO (usa fecha_revision)
- APROBACION (usa fecha_aprobacion)
- HISTORIAL_ESTADOS (usa fecha_cambio)
- DETALLE_COTIZACION
- AUDITORIA (usa fecha_accion)
```

---

## Resultado

El formulario de crear solicitud ahora debería funcionar sin el error `ORA-00904`.

**Archivos modificados**:
1. `app/Models/Solicitud.php`
2. `app/Models/Aprobacion.php`
3. `app/Models/Documento_adjunto.php`

**Caché limpiada**: ✅

---

## Próximo Paso

Probar crear una solicitud nuevamente desde el formulario del rol Solicitante.
