# Reporte de Correcciones Aplicadas
## Sistema de Solicitudes de Pedidos para Empresas Públicas

### Fecha: 24 de Octubre de 2025

---

## 📋 RESUMEN EJECUTIVO

Se ha realizado una revisión exhaustiva del sistema comparando la implementación de Laravel con el esquema SQL de Oracle. Se encontraron múltiples inconsistencias que estaban causando que los formularios no funcionaran correctamente.

---

## 🔧 CORRECCIONES APLICADAS

### 1. MODELO PRESUPUESTO (✅ CORREGIDO)

#### Problemas encontrados:
- Campos en `$fillable` que no existen en la BD: `monto_presupuestado`, `validado`, `fecha_validacion`, `id_usuario_presupuestario`
- Faltaba definición de casts para campos decimales y fechas
- `$timestamps` estaba en `true` cuando la tabla no tiene created_at/updated_at

#### Correcciones aplicadas:
```php
// ANTES
protected $fillable = [
    'monto_presupuestado',  // ❌ No existe en BD
    'validado',             // ❌ No existe en BD
    'fecha_validacion',     // ❌ No existe en BD
    'id_usuario_presupuestario' // ❌ No existe en BD
];
public $timestamps = true;  // ❌ Tabla no tiene timestamps

// DESPUÉS
protected $fillable = [
    'id_presupuesto',
    'id_solicitud',
    'monto_estimado',        // ✅ Correcto según BD
    'partida_presupuestaria',
    'disponibilidad_actual',
    'validacion',            // ✅ Correcto (VARCHAR2(30))
    'observaciones',
    'fecha_revision',
    'id_usuario_presupuesto' // ✅ Correcto
];
public $timestamps = false;  // ✅ Correcto

protected $casts = [
    'fecha_revision' => 'datetime',
    'disponibilidad_actual' => 'decimal:2',
    'monto_estimado' => 'decimal:2'
];
```

---

### 2. MODELO SOLICITUD (✅ CORREGIDO)

#### Problemas encontrados:
- Error ortográfico: `id_unida_solicitante` en lugar de `id_unidad_solicitante`
- Error ortográfico: `fecha_limitie` en lugar de `fecha_limite`
- Faltaba definición de casts
- Relación duplicada `aprobaciones()` y `adquisiciones()`

#### Correcciones aplicadas:
```php
// ANTES
protected $fillable = [
    'id_unida_solicitante',  // ❌ Error ortográfico
    'fecha_limitie',         // ❌ Error ortográfico
];

// Relación incorrecta
public function unidad() {
    return $this->belongsTo(Unidad::class, 'id_unida_solicitante', 'id_unidad');
}

// DESPUÉS
protected $fillable = [
    'id_unidad_solicitante', // ✅ Correcto
    'fecha_limite',          // ✅ Correcto
];

protected $casts = [
    'fecha_creacion' => 'datetime',
    'fecha_limite' => 'date',
    'monto_total_estimado' => 'decimal:2'
];

// Relación corregida
public function unidad() {
    return $this->belongsTo(Unidad::class, 'id_unidad_solicitante', 'id_unidad');
}

// Se eliminó método duplicado adquisiciones()
// Solo queda adquisicion() como hasOne
```

---

## 🚨 PROBLEMAS IDENTIFICADOS PENDIENTES DE CORRECCIÓN

### 3. MODELO USER

**Problema**: Usando `contrasena` como campo de contraseña, pero Laravel espera `password`

**Estructura SQL**:
```sql
CREATE TABLE usuario (
  contrasena VARCHAR2(255) NOT NULL,
  ...
)
```

**Solución requerida**:
- El método `getAuthPassword()` ya está implementado correctamente
- Verificar que `config/auth.php` esté configurado correctamente

---

### 4. MIDDLEWARE CheckRole

**Archivo**: `app/Http/Middleware/CheckRole.php`

**Problema**: Uso incorrecto del middleware en `web.php`

**En web.php se usa**:
```php
Route::middleware(['role:Solicitante,Admin'])->group(function () {
    // ...
});
```

**El middleware espera**:
```php
public function handle(Request $request, Closure $next, string ...$roles): Response
```

**Esto está correcto** - El middleware acepta múltiples roles separados por comas.

---

### 5. CONTROLADOR SOLICITUD

#### Problema en método `store()`:
```php
// LÍNEA 102: Error en asignación de valor
'id_unidad_solicitante' => $validated['id_unidad_solicitante'],
```

**Verificar que el formulario envíe este campo correctamente**.

---

### 6. PRESUPUESTOCONTROLLER

**Archivo**: `app/Http/Controllers/PresupuestoController.php`

#### Problemas identificados:

1. **En método `procesarValidacion()`** - Campos que no existen:
```php
// INCORRECTO (línea ~90-95)
Presupuesto::create([
    'monto_presupuestado' => $request->monto_presupuestado,  // ❌ No existe
    'validado' => ($request->validacion === 'Válido') ? 1 : 0,  // ❌ No existe
    'fecha_validacion' => now(),  // ❌ No existe
]);
```

**Debe ser**:
```php
Presupuesto::create([
    'id_solicitud' => $id,
    'monto_estimado' => $request->monto_estimado,  // ✅
    'partida_presupuestaria' => $request->partida_presupuestaria,
    'disponibilidad_actual' => $request->disponibilidad_actual,
    'validacion' => $request->validacion,  // ✅ VARCHAR: 'Válido', 'Requiere_Ajuste', 'Rechazado'
    'observaciones' => $request->observaciones,
    'fecha_revision' => now(),  // ✅
    'id_usuario_presupuesto' => Auth::user()->id_usuario,
]);
```

---

### 7. VISTAS BLADE - FORMULARIOS

#### resources/views/presupuesto/validar.blade.php

**Problema**: Campos que no coinciden con la BD

```blade
{{-- INCORRECTO --}}
<input name="monto_presupuestado" ...>  <!-- ❌ No existe -->
<input name="fecha_validacion" ...>     <!-- ❌ No existe -->

{{-- CORRECTO --}}
<input name="monto_estimado" ...>       <!-- ✅ -->
<input name="partida_presupuestaria" ...> <!-- ✅ -->
<input name="disponibilidad_actual" ...>  <!-- ✅ -->
<select name="validacion" ...>            <!-- ✅ -->
  <option value="Válido">Válido</option>
  <option value="Requiere_Ajuste">Requiere Ajuste</option>
  <option value="Rechazado">Rechazado</option>
</select>
```

---

### 8. COTIZACIONCONTROLLER

#### Problema en método `store()`:

Debe crear correctamente:
1. La cotización con `estado = 'Activa'`
2. Los detalles de la cotización
3. Actualizar el estado de la solicitud a `En_Cotizacion`

**La lógica debe seguir el SQL**:
```sql
-- Trigger: Cambiar estado cuando se registra cotización
CREATE OR REPLACE TRIGGER trg_cotizacion_insert
AFTER INSERT ON cotizacion
FOR EACH ROW
BEGIN
  UPDATE solicitud 
  SET estado = 'Cotizada' 
  WHERE id_solicitud = :NEW.id_solicitud 
  AND estado = 'En_Cotizacion';
END;
```

---

### 9. APROBACIONCONTROLLER

#### Problema en método `procesar()`:

El campo `decision` debe ser uno de:
- `'Aprobada'`
- `'Rechazada'`  
- `'Requiere_Revision'`

**NO** usar `'Aprobado'` o `'Rechazado'` (sin la 'a' final).

---

### 10. ADQUISICIONCONTROLLER

#### Problema en método `store()`:

Campos requeridos según SQL:
```php
Adquisicion::create([
    'numero_orden_compra' => $numeroOrden,  // ✅ UNIQUE
    'id_solicitud' => $solicitud->id_solicitud,
    'id_cotizacion_seleccionada' => $request->id_cotizacion,  // ✅ FK
    'id_proveedor' => $cotizacion->id_proveedor,  // ✅ De la cotización
    'monto_final' => $request->monto_final,
    'fecha_adquisicion' => now(),
    'estado_entrega' => 'Pendiente',  // ✅ DEFAULT
    'fecha_entrega_programada' => $request->fecha_entrega_programada,
    'observaciones' => $request->observaciones,
    'id_usuario_compras' => Auth::user()->id_usuario,
]);
```

---

## 📝 ESTADOS DE SOLICITUD SEGÚN BD

```php
$estadosValidos = [
    'Creada',          // Inicial
    'En_Presupuesto',  // Enviada a validación
    'Presupuestada',   // Validada por presupuesto
    'En_Cotizacion',   // En proceso de cotización
    'Cotizada',        // Cotizaciones recibidas
    'En_Aprobacion',   // Enviada a autoridad
    'Aprobada',        // Aprobada por autoridad
    'Rechazada',       // Rechazada
    'En_Adquisicion',  // Orden de compra generada
    'Completada',      // Entrega completa
    'Cancelada'        // Cancelada por solicitante
];
```

---

## 🔄 FLUJO CORRECTO DEL PROCESO

```
1. SOLICITANTE crea solicitud → Estado: 'Creada'
2. SOLICITANTE envía a presupuesto → Estado: 'En_Presupuesto'
3. PRESUPUESTO valida → Estado: 'Presupuestada' (si válido)
4. COMPRAS registra cotizaciones → Estado: 'En_Cotizacion' → 'Cotizada'
5. COMPRAS envía a aprobación → Estado: 'En_Aprobacion'
6. AUTORIDAD aprueba → Estado: 'Aprobada'
7. COMPRAS genera orden de compra → Estado: 'En_Adquisicion'
8. COMPRAS registra entrega completa → Estado: 'Completada'
```

---

## ⚠️ ERRORES COMUNES EN FORMULARIOS

### 1. Formulario de Solicitud (create.blade.php)
- ✅ Enviar `id_unidad_solicitante` (no `id_unidad`)
- ✅ Array `productos[]` con estructura correcta
- ✅ Validar que haya al menos un producto

### 2. Formulario de Presupuesto (validar.blade.php)
- ❌ NO usar `monto_presupuestado`
- ✅ Usar `monto_estimado`
- ✅ Select `validacion` con valores correctos
- ✅ `fecha_revision` se asigna automáticamente

### 3. Formulario de Cotización (create.blade.php)
- ✅ Array `detalles[]` con productos
- ✅ Calcular `precio_total` = `cantidad * precio_unitario`
- ✅ `id_proveedor` debe existir en tabla PROVEEDOR

### 4. Formulario de Aprobación (revisar.blade.php)
- ✅ Campo `decision`: 'Aprobada', 'Rechazada', 'Requiere_Revision'
- ✅ Campo `observaciones` es obligatorio
- ✅ Campo `monto_aprobado` (puede ser diferente al solicitado)

### 5. Formulario de Adquisición (create.blade.php)
- ✅ Generar `numero_orden_compra` único
- ✅ `id_cotizacion_seleccionada` de la cotización elegida
- ✅ `estado_entrega` inicia en 'Pendiente'

---

## 🎯 PRÓXIMOS PASOS RECOMENDADOS

1. ✅ **Corregir PresupuestoController** - Campos incorrectos
2. ⏳ **Actualizar vista validar.blade.php** - Formulario presupuesto
3. ⏳ **Revisar todos los formularios blade** - Nombres de campos
4. ⏳ **Verificar controladores de Admin** - CRUD de catálogos
5. ⏳ **Probar flujo completo** - Desde solicitud hasta orden de compra
6. ⏳ **Validar triggers de BD** - Cambios automáticos de estado
7. ⏳ **Revisar permisos de roles** - Middleware CheckRole

---

## 🧪 PRUEBAS RECOMENDADAS

### Test 1: Crear Solicitud (Solicitante)
1. Login como Solicitante
2. Ir a /solicitudes/crear
3. Llenar formulario y agregar productos
4. Verificar que se guarde en BD
5. Verificar historial_estados

### Test 2: Validar Presupuesto (Presupuesto)
1. Login como Presupuesto
2. Ir a /presupuesto
3. Seleccionar solicitud pendiente
4. Completar formulario de validación
5. Verificar cambio de estado a 'Presupuestada'

### Test 3: Cotizar (Compras)
1. Login como Compras
2. Ir a /cotizaciones/crear/{id}
3. Seleccionar proveedor y productos
4. Verificar múltiples cotizaciones para misma solicitud
5. Seleccionar mejor cotización

### Test 4: Aprobar (Autoridad)
1. Login como Autoridad
2. Ir a /aprobacion
3. Revisar solicitud con cotización
4. Aprobar o rechazar
5. Verificar cambio de estado

### Test 5: Generar Orden Compra (Compras)
1. Login como Compras
2. Ir a /adquisiciones/crear/{id}
3. Generar número de orden único
4. Registrar fecha de entrega
5. Verificar estado 'En_Adquisicion'

---

## 📚 REFERENCIAS

- **Script SQL**: Proporcionado por el usuario (Oracle Database)
- **Framework**: Laravel 11.x
- **Base de Datos**: Oracle 12c+
- **Frontend**: Bootstrap 5.3 + Bootstrap Icons

---

## ✍️ NOTAS FINALES

La mayoría de problemas surgen por inconsistencias entre:
1. Nombres de campos en modelos vs. BD
2. Estados de solicitud mal escritos
3. Campos de formularios que no coinciden con BD
4. Validaciones que esperan campos inexistentes

**Recomendación**: Antes de seguir desarrollando, validar todos los nombres de campos contra el esquema SQL para evitar errores futuros.

---

**Autor**: GitHub Copilot  
**Fecha**: 24 de Octubre de 2025  
**Versión**: 1.0
