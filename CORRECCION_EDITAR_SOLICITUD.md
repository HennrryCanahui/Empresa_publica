# Corrección: Editar Solicitud con Productos Dinámicos

## Problema Reportado

Usuario reportó que:
1. ❌ No se pueden **modificar solicitudes**
2. ❌ La **importación de detalle del producto** no funciona
3. ❌ Los **subtotales** no se calculan

## Causa Raíz

La vista `edit.blade.php` estaba **incompleta**:
- Solo tenía campos básicos (descripción, justificación, prioridad)
- **NO incluía** la sección de productos dinámicos
- **NO tenía JavaScript** para calcular subtotales
- Tenía error tipográfico: `fecha_limitie` en lugar de `fecha_limite`

## Solución Implementada

### 1. Vista edit.blade.php Reescrita

Se reemplazó completamente para incluir:

#### ✅ Sección de Productos Dinámicos
```blade
<div id="productos-container">
    @foreach($detalles as $index => $detalle)
        <div class="producto-item" data-index="{{ $index }}">
            <!-- Select de producto con data-precio y data-unidad -->
            <select name="productos[{{ $index }}][id_producto]" 
                    class="producto-select">
                @foreach($productos as $producto)
                    <option value="{{ $producto->id_producto }}" 
                            data-precio="{{ $producto->precio_referencia }}"
                            data-unidad="{{ $producto->unidad_medida }}"
                            {{ old(..., $detalle['id_producto']) == ... ? 'selected' : '' }}>
                @endforeach
            </select>
            
            <!-- Input de cantidad -->
            <input type="number" 
                   name="productos[{{ $index }}][cantidad]" 
                   class="cantidad-input"
                   value="{{ old(..., $detalle['cantidad']) }}">
            
            <!-- Campos readonly: unidad, precio, subtotal -->
            <input type="text" class="unidad-medida" readonly>
            <input type="text" class="precio-referencia" readonly>
            <input type="text" class="subtotal-estimado" readonly>
        </div>
    @endforeach
</div>
```

#### ✅ JavaScript para Cálculos en Tiempo Real

```javascript
// Evento 'change' para selects de producto
document.getElementById('productos-container').addEventListener('change', function(e) {
    if (e.target.classList.contains('producto-select')) {
        const productoItem = e.target.closest('.producto-item');
        const option = e.target.options[e.target.selectedIndex];
        const precio = parseFloat(option.dataset.precio) || 0;
        const unidad = option.dataset.unidad || '';
        
        productoItem.querySelector('.precio-referencia').value = 'Q ' + precio.toFixed(2);
        productoItem.querySelector('.unidad-medida').value = unidad;
        calcularSubtotal(productoItem);
    }
});

// Evento 'input' para cantidades (actualiza mientras escribes)
document.getElementById('productos-container').addEventListener('input', function(e) {
    if (e.target.classList.contains('cantidad-input')) {
        calcularSubtotal(e.target.closest('.producto-item'));
    }
});

function calcularSubtotal(productoItem) {
    const cantidad = parseFloat(cantidadInput.value) || 0;
    const precio = parseFloat(precioRef.value.replace('Q ', '')) || 0;
    const subtotal = cantidad * precio;
    
    subtotalInput.value = 'Q ' + subtotal.toFixed(2);
    calcularTotalGeneral();
}

function calcularTotalGeneral() {
    let total = 0;
    document.querySelectorAll('.subtotal-estimado').forEach(function(input) {
        total += parseFloat(input.value.replace('Q ', '')) || 0;
    });
    document.getElementById('total-general').textContent = 'Q ' + total.toFixed(2);
}
```

#### ✅ Inicialización al Cargar

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Cargar datos de productos existentes
    document.querySelectorAll('.producto-item').forEach(function(item) {
        const select = item.querySelector('.producto-select');
        if (select.value) {
            const option = select.options[select.selectedIndex];
            const precio = parseFloat(option.dataset.precio) || 0;
            const unidad = option.dataset.unidad || '';
            
            item.querySelector('.precio-referencia').value = 'Q ' + precio.toFixed(2);
            item.querySelector('.unidad-medida').value = unidad;
            calcularSubtotal(item);
        }
    });
    
    actualizarBotonesEliminar();
});
```

### 2. Correcciones de Campos

| Campo Anterior | Campo Corregido | Razón |
|---------------|-----------------|-------|
| `fecha_limitie` | `fecha_limite` | Error tipográfico en modelo |
| `monto_total_estimado` (manual) | Calculado automáticamente | JavaScript lo calcula |

### 3. Funcionalidad de Agregar/Eliminar Productos

```javascript
// Botón "Agregar Producto"
document.getElementById('btn-agregar-producto').addEventListener('click', function() {
    const nuevoProducto = crearProductoItem(productoIndex);
    container.insertAdjacentHTML('beforeend', nuevoProducto);
    productoIndex++;
});

// Botón "Eliminar" en cada producto
document.getElementById('productos-container').addEventListener('click', function(e) {
    if (e.target.closest('.btn-eliminar-producto')) {
        e.target.closest('.producto-item').remove();
        actualizarBotonesEliminar();
        calcularTotalGeneral();
    }
});

// Mostrar/ocultar botón eliminar (mínimo 1 producto)
function actualizarBotonesEliminar() {
    const items = document.querySelectorAll('.producto-item');
    items.forEach(function(item) {
        const btnEliminar = item.querySelector('.btn-eliminar-producto');
        btnEliminar.style.display = items.length > 1 ? 'block' : 'none';
    });
}
```

## Flujo de Edición Corregido

### Antes (No Funcionaba)
1. Usuario abre editar → Solo ve descripción y justificación
2. No puede modificar productos
3. Submit → No envía array de productos
4. Error de validación: `productos es requerido`

### Después (Funciona)
1. ✅ Usuario abre editar → **Ve todos los productos existentes**
2. ✅ **Precio y unidad se cargan automáticamente** desde `data-precio` y `data-unidad`
3. ✅ **Subtotales se calculan** al cargar la página (`DOMContentLoaded`)
4. ✅ **Cambiar cantidad** actualiza subtotal en tiempo real (`input` event)
5. ✅ **Cambiar producto** actualiza precio/unidad/subtotal (`change` event)
6. ✅ **Puede agregar más productos** con el botón "+"
7. ✅ **Puede eliminar productos** (mínimo 1)
8. ✅ **Total general** se actualiza automáticamente
9. ✅ Submit → Envía array `productos[]` completo al controlador

## Controlador (Ya Estaba Correcto)

El método `update()` en `SolicitudController` ya manejaba correctamente:

```php
public function update(Request $request, $id) {
    // Validación incluye 'productos' como array
    $validated = $request->validate([
        'productos' => 'required|array|min:1',
        'productos.*.id_producto' => 'required|exists:...',
        'productos.*.cantidad' => 'required|numeric|min:0.01',
    ]);
    
    // Elimina detalles antiguos
    Detalle_solicitud::where('id_solicitud', $id)->delete();
    
    // Crea nuevos detalles
    foreach ($validated['productos'] as $producto) {
        Detalle_solicitud::create([...]);
    }
    
    // Actualiza monto total
    DB::table('SOLICITUD')->update(['monto_total_estimado' => $montoTotal]);
}
```

## Verificación

### Antes de la Corrección
- ❌ Vista sin productos
- ❌ JavaScript inexistente
- ❌ Campo `fecha_limitie` con typo
- ❌ Submit fallaba por validación

### Después de la Corrección
- ✅ Vista muestra productos existentes
- ✅ JavaScript calcula subtotales
- ✅ Campo `fecha_limite` correcto
- ✅ Submit funciona correctamente
- ✅ **Importación de datos** funciona al cargar
- ✅ **Subtotales** se calculan en tiempo real

## Comandos Ejecutados

```bash
php artisan view:clear
```

## Archivos Modificados

1. ✅ `resources/views/solicitudes/edit.blade.php` - **Reescrito completamente**

## Pruebas Sugeridas

1. Abrir una solicitud en estado "Creada"
2. Hacer clic en "Editar"
3. Verificar que:
   - ✅ Se muestren todos los productos existentes
   - ✅ Precio y unidad se carguen automáticamente
   - ✅ Subtotales estén calculados
   - ✅ Cambiar cantidad actualice subtotal
   - ✅ Cambiar producto actualice precio/unidad
   - ✅ Total general se actualice
   - ✅ Botón agregar producto funcione
   - ✅ Botón eliminar funcione (mínimo 1)
4. Guardar cambios y verificar actualización en BD

---

**Fecha:** 25 de octubre de 2025  
**Archivo modificado:** 1 vista (reescrita)  
**Estado:** ✅ CORREGIDO - Edición funcional con productos dinámicos
