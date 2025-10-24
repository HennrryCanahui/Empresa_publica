# 🔧 GUÍA DE CORRECCIONES FINALES
## Sistema de Solicitudes de Pedidos - Empresa Pública

**Fecha**: 24 de Octubre de 2025  
**Versión**: 2.0 Final

---

## ✅ CORRECCIONES YA APLICADAS

### 1. Modelo Presupuesto (✅ COMPLETO)
- Eliminados campos inexistentes: `monto_presupuestado`, `validado`, `fecha_validacion`, `id_usuario_presupuestario`
- Agregados casts para campos decimales y fechas
- Corregido `$timestamps = false`

### 2. Modelo Solicitud (✅ COMPLETO)
- Corregido error ortográfico: `id_unida_solicitante` → `id_unidad_solicitante`
- Corregido error ortográfico: `fecha_limitie` → `fecha_limite`
- Agregados casts para campos
- Eliminada relación duplicada `adquisiciones()`
- Cambiado `aprobaciones()` a `aprobacion()` (hasOne)

### 3. PresupuestoController (✅ VERIFICADO)
- Ya está correctamente implementado
- Usa los campos correctos de la BD
- Maneja estados correctamente

### 4. Vista validar.blade.php (✅ VERIFICADO)
- Formulario usa campos correctos
- Validación con valores: 'Válido', 'Requiere_Ajuste', 'Rechazado'

### 5. Vista create.blade.php (Solicitud) (✅ VERIFICADO)
- Usa `id_unidad_solicitante` correctamente
- Campos coinciden con modelo

---

## ⚠️ CORRECCIONES PENDIENTES CRÍTICAS

### 1. Middleware CheckRole - VERIFICAR EN web.php

**Archivo**: `routes/web.php`  
**Línea**: ~50

**Revisar que use**:
```php
Route::middleware(['role:Solicitante,Admin'])->group(function () {
```

**NO**:
```php
Route::middleware(['auth', 'role:Solicitante'])->group(function () {
```

El middleware `auth` ya está incluido en el grupo padre.

---

### 2. CotizacionController - Método seleccionar()

**Archivo**: `app/Http/Controllers/CotizacionController.php`

**Problema**: Cuando se selecciona una cotización, debe:
1. Actualizar cotización seleccionada a `estado = 'Seleccionada'`
2. Actualizar las demás del mismo `id_solicitud` a `estado = 'Descartada'`
3. Actualizar solicitud a `estado = 'Cotizada'`

**Código correcto**:
```php
public function seleccionar(Request $request, $id_cotizacion)
{
    $cotizacion = Cotizacion::findOrFail($id_cotizacion);
    
    DB::beginTransaction();
    try {
        // Descartar las demás cotizaciones de la misma solicitud
        Cotizacion::where('id_solicitud', $cotizacion->id_solicitud)
            ->where('id_cotizacion', '!=', $id_cotizacion)
            ->where('estado', 'Activa')
            ->update(['estado' => 'Descartada']);
        
        // Seleccionar esta cotización
        $cotizacion->update(['estado' => 'Seleccionada']);
        
        // Actualizar estado de solicitud
        $cotizacion->solicitud->update(['estado' => 'Cotizada']);
        
        DB::commit();
        return redirect()->route('cotizaciones.comparar', $cotizacion->id_solicitud)
            ->with('success', 'Cotización seleccionada exitosamente.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
```

---

### 3. CotizacionController - Método enviarAAprobacion()

**Crear nuevo método** (si no existe):

```php
public function enviarAAprobacion($id_solicitud)
{
    $solicitud = Solicitud::findOrFail($id_solicitud);
    
    // Verificar que haya una cotización seleccionada
    $cotizacionSeleccionada = Cotizacion::where('id_solicitud', $id_solicitud)
        ->where('estado', 'Seleccionada')
        ->first();
    
    if (!$cotizacionSeleccionada) {
        return back()->with('error', 'Debe seleccionar una cotización antes de enviar a aprobación.');
    }
    
    // Verificar que esté en estado correcto
    if ($solicitud->estado != 'Cotizada') {
        return back()->with('error', 'La solicitud no está en estado para enviar a aprobación.');
    }
    
    DB::beginTransaction();
    try {
        $solicitud->update(['estado' => 'En_Aprobacion']);
        
        DB::commit();
        return redirect()->route('cotizaciones.index')
            ->with('success', 'Solicitud enviada a aprobación exitosamente.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
```

---

### 4. AprobacionController - Método procesar()

**Archivo**: `app/Http/Controllers/AprobacionController.php`

**Verificar campo `decision`**:

```php
$validated = $request->validate([
    'decision' => 'required|in:Aprobada,Rechazada,Requiere_Revision',  // ✅ Con 'a' final
    'observaciones' => 'required|string|min:10|max:4000',
    'monto_aprobado' => 'nullable|numeric|min:0',
    'condiciones_aprobacion' => 'nullable|string|max:4000'
]);
```

**Actualizar estado según decisión**:

```php
if ($validated['decision'] === 'Aprobada') {
    $solicitud->update(['estado' => 'Aprobada']);
} elseif ($validated['decision'] === 'Rechazada') {
    $solicitud->update(['estado' => 'Rechazada']);
}
// Si es 'Requiere_Revision', se mantiene en 'En_Aprobacion'
```

---

### 5. AdquisicionController - Método store()

**Archivo**: `app/Http/Controllers/AdquisicionController.php`

**Generar número único de orden de compra**:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'id_solicitud' => 'required|exists:solicitud,id_solicitud',
        'id_cotizacion' => 'required|exists:cotizacion,id_cotizacion',
        'numero_factura' => 'nullable|string|max:100',
        'fecha_entrega_programada' => 'required|date|after:today',
        'observaciones' => 'nullable|string|max:4000'
    ]);

    $solicitud = Solicitud::findOrFail($validated['id_solicitud']);
    $cotizacion = Cotizacion::with('proveedor')->findOrFail($validated['id_cotizacion']);
    
    // Verificar estado correcto
    if ($solicitud->estado != 'Aprobada') {
        return back()->with('error', 'La solicitud no está aprobada.');
    }

    // Generar número único de orden de compra
    $year = date('Y');
    $count = Adquisicion::whereYear('fecha_adquisicion', $year)->count() + 1;
    $numeroOrden = 'OC-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

    DB::beginTransaction();
    try {
        $adquisicion = Adquisicion::create([
            'numero_orden_compra' => $numeroOrden,
            'id_solicitud' => $solicitud->id_solicitud,
            'id_cotizacion_seleccionada' => $cotizacion->id_cotizacion,
            'id_proveedor' => $cotizacion->id_proveedor,
            'numero_factura' => $validated['numero_factura'],
            'monto_final' => $cotizacion->monto_total,
            'fecha_adquisicion' => now(),
            'estado_entrega' => 'Pendiente',
            'fecha_entrega_programada' => $validated['fecha_entrega_programada'],
            'observaciones' => $validated['observaciones'],
            'id_usuario_compras' => Auth::user()->id_usuario
        ]);

        // Actualizar estado de solicitud
        $solicitud->update(['estado' => 'En_Adquisicion']);

        DB::commit();
        return redirect()->route('adquisiciones.ver', $adquisicion->id_adquisicion)
            ->with('success', 'Orden de compra generada exitosamente: ' . $numeroOrden);
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}
```

---

### 6. AdquisicionController - Método actualizarEntrega()

**Actualizar estado de entrega**:

```php
public function actualizarEntrega(Request $request, $id)
{
    $validated = $request->validate([
        'estado_entrega' => 'required|in:Parcial,Completa',
        'fecha_entrega_real' => 'required|date',
        'observaciones' => 'nullable|string|max:4000'
    ]);

    $adquisicion = Adquisicion::with('solicitud')->findOrFail($id);

    DB::beginTransaction();
    try {
        $adquisicion->update([
            'estado_entrega' => $validated['estado_entrega'],
            'fecha_entrega_real' => $validated['fecha_entrega_real'],
            'observaciones' => $validated['observaciones']
        ]);

        // Si la entrega está completa, actualizar solicitud
        if ($validated['estado_entrega'] === 'Completa') {
            $adquisicion->solicitud->update(['estado' => 'Completada']);
        }

        DB::commit();
        return back()->with('success', 'Estado de entrega actualizado exitosamente.');
        
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
```

---

## 🔍 VERIFICACIONES NECESARIAS

### Archivos a revisar manualmente:

#### 1. UsuarioController
- ✅ Método `store()` - Hash de contraseña
- ✅ Método `update()` - Validación de correo único
- ✅ Campo `contrasena` no `password`

#### 2. UnidadController
- ✅ CRUD básico funcional
- ✅ Validación de código único

#### 3. CategoriaProductoController
- ✅ CRUD básico funcional
- ✅ Validación de código único

#### 4. CatalogoProductoController
- ✅ CRUD básico funcional
- ✅ Relación con `id_categoria`
- ✅ Campo `precio_referencia` como decimal

---

## 📋 CHECKLIST DE PRUEBAS

### Flujo Completo ROL SOLICITANTE:

- [ ] 1. Login como Solicitante
- [ ] 2. Ir a `/solicitudes/crear`
- [ ] 3. Llenar formulario:
  - [ ] Seleccionar unidad solicitante
  - [ ] Establecer prioridad
  - [ ] Agregar descripción y justificación
  - [ ] Agregar al menos un producto
  - [ ] Verificar cálculo de monto total
- [ ] 4. Guardar solicitud
- [ ] 5. Verificar que aparezca en "Mis Solicitudes"
- [ ] 6. Ver detalle de solicitud
- [ ] 7. Enviar a presupuesto
- [ ] 8. Verificar cambio de estado a "En_Presupuesto"

### Flujo Completo ROL PRESUPUESTO:

- [ ] 1. Login como Presupuesto
- [ ] 2. Ir a `/presupuesto`
- [ ] 3. Verificar lista de solicitudes pendientes
- [ ] 4. Seleccionar una solicitud
- [ ] 5. Completar formulario de validación:
  - [ ] Monto estimado
  - [ ] Partida presupuestaria
  - [ ] Disponibilidad actual
  - [ ] Seleccionar validación
  - [ ] Observaciones
- [ ] 6. Procesar validación
- [ ] 7. Verificar cambio de estado según validación

### Flujo Completo ROL COMPRAS:

- [ ] 1. Login como Compras
- [ ] 2. Ir a `/cotizaciones`
- [ ] 3. Seleccionar solicitud presupuestada
- [ ] 4. Crear cotización:
  - [ ] Seleccionar proveedor
  - [ ] Agregar detalles de productos
  - [ ] Calcular precio total
- [ ] 5. Crear múltiples cotizaciones para comparar
- [ ] 6. Ir a comparar cotizaciones
- [ ] 7. Seleccionar la mejor cotización
- [ ] 8. Enviar a aprobación
- [ ] 9. Verificar estado "En_Aprobacion"

### Flujo Completo ROL AUTORIDAD:

- [ ] 1. Login como Autoridad
- [ ] 2. Ir a `/aprobacion`
- [ ] 3. Seleccionar solicitud pendiente
- [ ] 4. Revisar información completa
- [ ] 5. Tomar decisión:
  - [ ] Aprobar con observaciones
  - [ ] O Rechazar con motivo
  - [ ] O Solicitar revisión
- [ ] 6. Verificar cambio de estado

### Flujo Completo ORDEN DE COMPRA:

- [ ] 1. Login como Compras
- [ ] 2. Ir a `/adquisiciones`
- [ ] 3. Seleccionar solicitud aprobada
- [ ] 4. Generar orden de compra:
  - [ ] Número de orden único
  - [ ] Cotización seleccionada
  - [ ] Fecha de entrega programada
- [ ] 5. Registrar entrega:
  - [ ] Cambiar a "Completa"
  - [ ] Fecha de entrega real
- [ ] 6. Verificar estado final "Completada"

### Flujo Completo ROL ADMIN:

- [ ] 1. Login como Admin
- [ ] 2. Gestionar Usuarios:
  - [ ] Crear nuevo usuario
  - [ ] Asignar rol
  - [ ] Activar/desactivar
- [ ] 3. Gestionar Unidades
- [ ] 4. Gestionar Categorías
- [ ] 5. Gestionar Productos:
  - [ ] Crear producto
  - [ ] Asignar categoría
  - [ ] Precio de referencia
  - [ ] Especificaciones técnicas

---

## 🎯 PRIORIDADES DE CORRECCIÓN

### ALTA PRIORIDAD (Hacer ahora):
1. ✅ Modelo Presupuesto - COMPLETADO
2. ✅ Modelo Solicitud - COMPLETADO
3. ⏳ CotizacionController.seleccionar() - PENDIENTE
4. ⏳ CotizacionController.enviarAAprobacion() - PENDIENTE
5. ⏳ AdquisicionController.store() - PENDIENTE

### MEDIA PRIORIDAD:
6. AprobacionController.procesar() - Verificar
7. AdquisicionController.actualizarEntrega() - Verificar
8. Vistas de cotizaciones - Verificar formularios
9. Vistas de adquisiciones - Verificar formularios

### BAJA PRIORIDAD:
10. Controladores Admin - Funcionan pero mejorar validaciones
11. Dashboard - Estadísticas y gráficos
12. Reportes - Implementar exports

---

## 💡 RECOMENDACIONES FINALES

### 1. Testing
- Usar datos de ejemplo del SQL (ya proporcionados)
- Probar cada rol de forma independiente
- Verificar transiciones de estado

### 2. Validaciones
- Todos los formularios ya tienen validaciones básicas
- Agregar validaciones JavaScript para mejor UX

### 3. Seguridad
- Los middlewares CheckRole ya están implementados
- Verificar que cada ruta tenga el middleware correcto
- No permitir cambios de estado fuera de secuencia

### 4. Base de Datos
- Los triggers de Oracle manejan automáticamente:
  - Generación de IDs con secuencias
  - Cambios en historial_estados
  - Cálculos de totales
  - Actualizaciones de timestamps

### 5. Documentación
- Mantener el archivo CORRECCIONES_APLICADAS.md actualizado
- Documentar cada cambio importante
- Agregar comentarios en código complejo

---

## 📞 SOPORTE

Si encuentras algún error no documentado:
1. Verificar nombres de campos contra SQL
2. Revisar logs de Laravel: `storage/logs/laravel.log`
3. Verificar estructura de BD con Oracle SQL Developer
4. Consultar este documento primero

---

**Última actualización**: 24/10/2025  
**Estado**: Documentación Completa ✅  
**Modelos corregidos**: 2/2 ✅  
**Controladores pendientes**: 3 ⏳  
**Vistas verificadas**: 2 ✅
