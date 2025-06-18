-- TABLAS BASE DE DATOS SISTEMA DE DOTACIONES - INFORMIX
-- Sin campos código (el SERIAL es el código)


-- TABLA: TIPOS DE DOTACION

CREATE TABLE mrml_tipo_dotacion (
    tipo_dotacion_id SERIAL PRIMARY KEY,
    tipo_dotacion_nombre VARCHAR(50) NOT NULL,
    tipo_dotacion_descripcion VARCHAR(200),
    tipo_dotacion_situacion INTEGER DEFAULT 1,
    tipo_dotacion_fecha_registro DATE DEFAULT TODAY
);

-- Insertar tipos básicos
INSERT INTO mrml_tipo_dotacion (tipo_dotacion_nombre, tipo_dotacion_descripcion) VALUES ('BOTAS', 'Tipo Vulcano, Industria Militar');
INSERT INTO mrml_tipo_dotacion (tipo_dotacion_nombre, tipo_dotacion_descripcion) VALUES ('CAMISAS', 'Uniforme No. 4');
INSERT INTO mrml_tipo_dotacion (tipo_dotacion_nombre, tipo_dotacion_descripcion) VALUES ('PANTALONES', 'Uniforme No. 4');


-- TABLA: TALLAS

CREATE TABLE mrml_talla (
    talla_id SERIAL PRIMARY KEY,
    talla_nombre VARCHAR(10) NOT NULL, -- Directamente '35', 'XS', etc.
    talla_descripcion VARCHAR(50),
    talla_tipo VARCHAR(20) NOT NULL, -- 'CALZADO' o 'ROPA'
    talla_situacion INTEGER DEFAULT 1
);

-- Insertar tallas para calzado (botas)
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('35', 'Talla 35', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('36', 'Talla 36', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('37', 'Talla 37', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('38', 'Talla 38', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('39', 'Talla 39', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('40', 'Talla 40', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('41', 'Talla 41', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('42', 'Talla 42', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('43', 'Talla 43', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('44', 'Talla 44', 'CALZADO');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('45', 'Talla 45', 'CALZADO');
-- Insertar tallas para ropa (camisas y pantalones)
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('XS', 'Extra Small', 'ROPA');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('S', 'Small', 'ROPA');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('M', 'Medium', 'ROPA');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('L', 'Large', 'ROPA');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('XL', 'Extra Large', 'ROPA');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('XXL', 'Extra Extra Large', 'ROPA');
INSERT INTO mrml_talla (talla_nombre, talla_descripcion, talla_tipo) VALUES ('XXXL', 'Triple Extra Large', 'ROPA');


-- TABLA: INVENTARIO DOTACION

CREATE TABLE mrml_dotacion_inventario (
    dotacion_inv_id SERIAL PRIMARY KEY,
    tipo_dotacion_id INTEGER NOT NULL,
    talla_id INTEGER NOT NULL,
    dotacion_inv_marca VARCHAR(50),
    dotacion_inv_modelo VARCHAR(50),
    dotacion_inv_color VARCHAR(30),
    dotacion_inv_material VARCHAR(100),
    dotacion_inv_cantidad_inicial INTEGER NOT NULL DEFAULT 0,
    dotacion_inv_cantidad_actual INTEGER NOT NULL DEFAULT 0,
    dotacion_inv_cantidad_minima INTEGER DEFAULT 5,
    dotacion_inv_precio_unitario DECIMAL(10,2),
    dotacion_inv_proveedor VARCHAR(100),
    dotacion_inv_fecha_ingreso DATE DEFAULT TODAY,
    dotacion_inv_fecha_vencimiento DATE,
    dotacion_inv_observaciones TEXT,
    dotacion_inv_situacion INTEGER DEFAULT 1,
    dotacion_inv_fecha_registro DATE DEFAULT TODAY,
    
    FOREIGN KEY (tipo_dotacion_id) REFERENCES mrml_tipo_dotacion(tipo_dotacion_id),
    FOREIGN KEY (talla_id) REFERENCES mrml_talla(talla_id)
);


-- TABLA: EMPLEADOS

CREATE TABLE mrml_empleado (
    empleado_id SERIAL PRIMARY KEY,
    empleado_nombres VARCHAR(100) NOT NULL,
    empleado_apellidos VARCHAR(100) NOT NULL,
    empleado_dpi VARCHAR(15),
    empleado_puesto VARCHAR(50),
    empleado_departamento VARCHAR(50),
    empleado_fecha_ingreso DATE,
    empleado_telefono VARCHAR(15),
    empleado_correo VARCHAR(100),
    empleado_direccion TEXT,
    empleado_situacion INTEGER DEFAULT 1,
    empleado_fecha_registro DATE DEFAULT TODAY
);


-- TABLA: SOLICITUDES DE DOTACION

CREATE TABLE mrml_dotacion_solicitud (
    solicitud_id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL,
    solicitud_fecha DATE DEFAULT TODAY,
    solicitud_estado VARCHAR(20) DEFAULT 'PENDIENTE',
    solicitud_observaciones TEXT,
    solicitud_fecha_aprobacion DATE,
    solicitud_aprobado_por INTEGER,
    solicitud_situacion INTEGER DEFAULT 1,
    solicitud_fecha_registro DATE DEFAULT TODAY,
    
    FOREIGN KEY (empleado_id) REFERENCES mrml_empleado(empleado_id)
);


-- TABLA: DETALLE SOLICITUD DOTACION

CREATE TABLE mrml_dotacion_solicitud_detalle (
    solicitud_detalle_id SERIAL PRIMARY KEY,
    solicitud_id INTEGER NOT NULL,
    tipo_dotacion_id INTEGER NOT NULL,
    talla_id INTEGER NOT NULL,
    solicitud_det_cantidad INTEGER NOT NULL DEFAULT 1,
    solicitud_det_observaciones VARCHAR(200),
    solicitud_det_situacion INTEGER DEFAULT 1,
    
    FOREIGN KEY (solicitud_id) REFERENCES mrml_dotacion_solicitud(solicitud_id),
    FOREIGN KEY (tipo_dotacion_id) REFERENCES mrml_tipo_dotacion(tipo_dotacion_id),
    FOREIGN KEY (talla_id) REFERENCES mrml_talla(talla_id)
);


-- TABLA: ENTREGAS DE DOTACION

CREATE TABLE mrml_dotacion_entrega (
    entrega_id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL,
    solicitud_id INTEGER,
    entrega_fecha DATE DEFAULT TODAY,
    entrega_año INTEGER DEFAULT YEAR(TODAY),
    entrega_observaciones TEXT,
    entrega_entregado_por VARCHAR(100),
    entrega_recibido_por VARCHAR(100),
    entrega_situacion INTEGER DEFAULT 1,
    entrega_fecha_registro DATE DEFAULT TODAY,
    
    FOREIGN KEY (empleado_id) REFERENCES mrml_empleado(empleado_id),
    FOREIGN KEY (solicitud_id) REFERENCES mrml_dotacion_solicitud(solicitud_id)
);


-- TABLA: DETALLE ENTREGA DOTACION

CREATE TABLE mrml_dotacion_entrega_detalle (
    entrega_detalle_id SERIAL PRIMARY KEY,
    entrega_id INTEGER NOT NULL,
    dotacion_inv_id INTEGER NOT NULL,
    entrega_det_cantidad INTEGER NOT NULL DEFAULT 1,
    entrega_det_precio_unitario DECIMAL(10,2),
    entrega_det_observaciones VARCHAR(200),
    entrega_det_situacion INTEGER DEFAULT 1,
    
    FOREIGN KEY (entrega_id) REFERENCES mrml_dotacion_entrega(entrega_id),
    FOREIGN KEY (dotacion_inv_id) REFERENCES mrml_dotacion_inventario(dotacion_inv_id)
);


-- TABLA: CONTROL ANUAL ENTREGAS

CREATE TABLE mrml_dotacion_control_anual (
    control_id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL,
    control_año INTEGER NOT NULL,
    control_entregas_realizadas INTEGER DEFAULT 0,
    control_entregas_maximas INTEGER DEFAULT 3,
    control_fecha_primera_entrega DATE,
    control_fecha_ultima_entrega DATE,
    control_situacion INTEGER DEFAULT 1,
    
    FOREIGN KEY (empleado_id) REFERENCES mrml_empleado(empleado_id),
    UNIQUE (empleado_id, control_año)
);