# Nexora - Estructura de la base de datos

## Tabla: Rol
| Rol |
|---------|
| id_rol |
| nombre |

## Tabla: Usuario
| Usuario |
|---------|
| id_usuario |
| nombre |
| apellido |
| correo |
| contrasena |
| id_rol |
| id-sucursal |

## Tabla: Sucursal
| Sucursal |
|---------|
| id_sucursal |
| nombre |
| direccion |
| contacto |
| estado |
| id_gerente |

## Tabla: Categoria
| Categoria | 
|---------|
| id_categoria |
| nombre |
| estado |

## Tabla: Producto
| Producto |
|---------|
| id_producto |
| nombre |
| precio |
| codigo_barras |
| estado |
| id_categoria |

## Tabla: Inventario
| Inventario |
|---------|
| id_inventario |
| id_sucursal |
| id_producto |
| existencias |
| stock_minimo |
| fecha_actualizacion |

## Tabla: Venta
| Venta |
|---------|
| id_venta |
| fecha_hora |
| subtotal |
| total |
| id_sucursal |
| id_cajero |
| id_metodo_pago |

## Tabla: Metodo_pago
| Metodo_pago |
|---------|
| id_metodo_pago |
| nombre |

## Tabla: Detalle_venta
| Detalle_venta |
|---------|
| id_detalle |
| id_venta |
| id_producto |
| cantidad |
| precio_unitario |
| subtotal |

## Tabla: Caja
| Caja |
|---------|
| id_caja |
| numero_caja |
| estado |
| id_sucursal |

## Tabla: Corte_caja
| Corte_caja |
|---------|
| id_corte |
| fecha_apertura |
| fecha_cierre |
| monto_inicial |
| monto_final |
| id_caja |
| id_usuario |

## Relaciones

ROL 1:N USUARIO

SUCURSAL 1:N USUARIO

SUCURSAL 1:N INVENTARIO

PRODUCTO 1:N INVENTARIO

CATEGORIA 1:N PRODUCTO

SUCURSAL N:N PRODUCTO

SUCURSAL 1:N VENTA

CAJERO 1:N VENTA

METODO_PAGO 1:N VENTA

VENTA 1:N DETALLE_VENTA

PRODUCTO 1:N DETALLE_VENTA

SUCURSAL 1:N VENTA

SUCURSAL 1:N CAJA 

USUARIO 1:N VENTA

CAJA 1:N VENTA

CAJA 1:N CORTE_CAJA

USUARIO 1:N CORTE_CAJA