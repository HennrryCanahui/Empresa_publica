# Corrección de Auto-Incremento en Modelos Laravel con Oracle

## Problema Identificado

**Error:** `ORA-01400: no se puede realizar una inserción NULL en ID_SOLICITUD`

### Causa Raíz
Todos los modelos tenían `public $incrementing = false`, lo que impedía que Laravel recuperara los IDs generados automáticamente por los triggers de Oracle después de una inserción.

## Modelos Corregidos

### 1. Solicitud.php
```php
// ANTES
public $incrementing = false;

// DESPUÉS
public $incrementing = true; // Permite recuperar ID generado por Oracle
```

### 2. Detalle_solicitud.php
```php
// ANTES
public $incrementing = false;
public $timestamps = true;

// DESPUÉS
public $incrementing = true; // Recuperar ID del trigger
public $timestamps = false;  // Tabla sin created_at/updated_at
```

### 3. Presupuesto.php
```php
public $incrementing = true;
public $timestamps = false;
```

### 4. Aprobacion.php
```php
public $incrementing = true;
public $timestamps = false;
```

### 5. Cotizacion.php
```php
public $incrementing = true;
```

### 6. Detalle_Cotizacion.php
```php
public $incrementing = true;
```

### 7. Adquisicion.php
```php
public $incrementing = true;
```

### 8. Documento_adjunto.php
```php
public $incrementing = true;
```

## Corrección en Vista create.blade.php

### Problema en JavaScript
Los eventos de cantidad no actualizaban en tiempo real.

```javascript
// ANTES: Un solo evento 'change' para todo
document.getElementById('productos-container').addEventListener('change', function(e) {
    // Manejaba tanto select como input juntos
});

// DESPUÉS: Eventos separados
// Evento 'change' para selects de producto
document.getElementById('productos-container').addEventListener('change', function(e) {
    if (e.target.classList.contains('producto-select')) {
        // Manejar cambio de producto
    }
});

// Evento 'input' para cantidades (actualiza en tiempo real)
document.getElementById('productos-container').addEventListener('input', function(e) {
    if (e.target.classList.contains('cantidad-input')) {
        calcularSubtotal(productoItem);
    }
});
```

## Flujo Corregido de Creación de Solicitud

1. **Usuario crea solicitud** → Formulario envía datos
2. **Solicitud::create()** → Oracle trigger genera `id_solicitud`
3. **Laravel recupera ID** (gracias a `incrementing = true`)
4. **Detalle_solicitud::create()** → Usa el `id_solicitud` recuperado
5. **Oracle trigger genera** `id_detalle`
6. **Laravel actualiza** `monto_total_estimado` con DB::table()

## Verificación

### Antes de la corrección:
```
Error: ID_SOLICITUD = NULL en Detalle_solicitud
```

### Después de la corrección:
```
✅ Solicitud creada con ID válido
✅ Detalles insertados correctamente
✅ Monto total actualizado
✅ JavaScript calcula subtotales en tiempo real
```

## Comandos Ejecutados

```bash
php artisan optimize:clear
```

## Pendiente: Otros Modelos

Los siguientes modelos también necesitan `incrementing = true`:

- User.php
- Catalogo_producto.php
- Unidad.php
- Categoria_producto.php
- Proveedor.php
- Notificacion.php
- Historial_estados.php
- Rechazo_solicitud.php
- Auditoria.php

**Recomendación:** Aplicar el mismo cambio si se usan inserciones desde Laravel Eloquent.

## Notas Técnicas

### Laravel + Oracle
- Oracle usa SEQUENCES y TRIGGERS para auto-incremento
- Laravel necesita `incrementing = true` para ejecutar `RETURNING id_campo INTO ?`
- Con `incrementing = false`, Laravel no recupera el ID generado
- Resultado: Variables siguientes reciben NULL

### Timestamps en Oracle
- Tablas SIN timestamps: `public $timestamps = false`
- Tablas con solo `updated_at`: Usar `DB::table()->update()` manualmente
- Evitar `$model->update()` en tablas sin `created_at`

---

**Fecha:** 25 de octubre de 2025  
**Archivos modificados:** 8 modelos + 1 vista  
**Estado:** ✅ CORREGIDO
