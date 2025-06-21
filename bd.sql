-- =============================================
-- TABLA: TIPOS DE DOTACION
-- =============================================
CREATE TABLE mrml_tipo_dotacion (
    tipo_dotacion_id SERIAL PRIMARY KEY,
    tipo_dotacion_nombre VARCHAR(50) NOT NULL,
    tipo_dotacion_descripcion VARCHAR(200),
    tipo_dotacion_situacion INTEGER DEFAULT 1,
    tipo_dotacion_fecha_registro DATE DEFAULT TODAY
);

-- Insertar tipos básicos
INSERT INTO mrml_tipo_dotacion (tipo_dotacion_nombre, tipo_dotacion_descripcion) VALUES 
('BOTAS', 'Tipo Vulcano, Industria Militar');
INSERT INTO mrml_tipo_dotacion (tipo_dotacion_nombre, tipo_dotacion_descripcion) VALUES 
('CAMISAS', 'Uniforme No. 4');
INSERT INTO mrml_tipo_dotacion (tipo_dotacion_nombre, tipo_dotacion_descripcion) VALUES
('PANTALONES', 'Uniform No. 4');

-- =============================================
-- TABLA: TALLAS
-- =============================================
CREATE TABLE talla (
    talla_id SERIAL PRIMARY KEY,
    talla_codigo VARCHAR(10) NOT NULL,
    talla_descripcion VARCHAR(50),
    talla_situacion INTEGER DEFAULT 1
);

-- Insertar tallas
-- Tallas para calzado
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('35', 'Talla 35');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('36', 'Talla 36');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('37', 'Talla 37');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('38', 'Talla 38');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('39', 'Talla 39');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('40', 'Talla 40');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('41', 'Talla 41');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('42', 'Talla 42');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('43', 'Talla 43');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('44', 'Talla 44');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('45', 'Talla 45');

-- Tallas para ropa
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('XS', 'Extra Small');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('S', 'Small');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('M', 'Medium');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('L', 'Large');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('XL', 'Extra Large');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('XXL', 'Extra Extra Large');
INSERT INTO talla (talla_codigo, talla_descripcion) VALUES ('XXXL', 'Triple Extra Large');

-- =============================================
-- TABLA: INVENTARIO DOTACION
-- =============================================
CREATE TABLE dotacion_inventario (
    dotacion_inv_id SERIAL PRIMARY KEY,
    dotacion_inv_codigo VARCHAR(20) UNIQUE NOT NULL,
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
    
    FOREIGN KEY (tipo_dotacion_id) REFERENCES tipo_dotacion(tipo_dotacion_id),
    FOREIGN KEY (talla_id) REFERENCES talla(talla_id)
);

-- =============================================
-- TABLA: EMPLEADOS (Si no existe)
-- =============================================
CREATE TABLE empleado (
    empleado_id SERIAL PRIMARY KEY,
    empleado_codigo VARCHAR(20) UNIQUE NOT NULL,
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

-- =============================================
-- TABLA: SOLICITUDES DE DOTACION
-- =============================================
CREATE TABLE dotacion_solicitud (
    solicitud_id SERIAL PRIMARY KEY,
    solicitud_codigo VARCHAR(20) UNIQUE NOT NULL,
    empleado_id INTEGER NOT NULL,
    solicitud_fecha DATE DEFAULT TODAY,
    solicitud_estado VARCHAR(20) DEFAULT 'PENDIENTE', -- PENDIENTE, APROBADA, RECHAZADA, ENTREGADA
    solicitud_observaciones TEXT,
    solicitud_fecha_aprobacion DATE,
    solicitud_aprobado_por INTEGER,
    solicitud_situacion INTEGER DEFAULT 1,
    solicitud_fecha_registro DATE DEFAULT TODAY,
    
    FOREIGN KEY (empleado_id) REFERENCES empleado(empleado_id)
);

-- =============================================
-- TABLA: DETALLE SOLICITUD DOTACION
-- =============================================
CREATE TABLE dotacion_solicitud_detalle (
    solicitud_detalle_id SERIAL PRIMARY KEY,
    solicitud_id INTEGER NOT NULL,
    tipo_dotacion_id INTEGER NOT NULL,
    talla_id INTEGER NOT NULL,
    solicitud_det_cantidad INTEGER NOT NULL DEFAULT 1,
    solicitud_det_observaciones VARCHAR(200),
    solicitud_det_situacion INTEGER DEFAULT 1,
    
    FOREIGN KEY (solicitud_id) REFERENCES dotacion_solicitud(solicitud_id),
    FOREIGN KEY (tipo_dotacion_id) REFERENCES tipo_dotacion(tipo_dotacion_id),
    FOREIGN KEY (talla_id) REFERENCES talla(talla_id)
);

-- =============================================
-- TABLA: ENTREGAS DE DOTACION
-- =============================================
CREATE TABLE dotacion_entrega (
    entrega_id SERIAL PRIMARY KEY,
    entrega_codigo VARCHAR(20) UNIQUE NOT NULL,
    empleado_id INTEGER NOT NULL,
    solicitud_id INTEGER,
    entrega_fecha DATE DEFAULT TODAY,
    entrega_año INTEGER DEFAULT YEAR(TODAY),
    entrega_observaciones TEXT,
    entrega_entregado_por VARCHAR(100),
    entrega_recibido_por VARCHAR(100),
    entrega_situacion INTEGER DEFAULT 1,
    entrega_fecha_registro DATE DEFAULT TODAY,
    
    FOREIGN KEY (empleado_id) REFERENCES empleado(empleado_id),
    FOREIGN KEY (solicitud_id) REFERENCES dotacion_solicitud(solicitud_id)
);

-- =============================================
-- TABLA: DETALLE ENTREGA DOTACION
-- =============================================
CREATE TABLE dotacion_entrega_detalle (
    entrega_detalle_id SERIAL PRIMARY KEY,
    entrega_id INTEGER NOT NULL,
    dotacion_inv_id INTEGER NOT NULL,
    entrega_det_cantidad INTEGER NOT NULL DEFAULT 1,
    entrega_det_precio_unitario DECIMAL(10,2),
    entrega_det_observaciones VARCHAR(200),
    entrega_det_situacion INTEGER DEFAULT 1,
    
    FOREIGN KEY (entrega_id) REFERENCES dotacion_entrega(entrega_id),
    FOREIGN KEY (dotacion_inv_id) REFERENCES dotacion_inventario(dotacion_inv_id)
);

-- =============================================
-- TABLA: CONTROL ANUAL ENTREGAS
-- =============================================
CREATE TABLE dotacion_control_anual (
    control_id SERIAL PRIMARY KEY,
    empleado_id INTEGER NOT NULL,
    control_año INTEGER NOT NULL,
    control_entregas_realizadas INTEGER DEFAULT 0,
    control_entregas_maximas INTEGER DEFAULT 3,
    control_fecha_primera_entrega DATE,
    control_fecha_ultima_entrega DATE,
    control_situacion INTEGER DEFAULT 1,
    
    FOREIGN KEY (empleado_id) REFERENCES empleado(empleado_id),
    UNIQUE (empleado_id, control_año)
);

-- =============================================
-- INDICES PARA OPTIMIZACIÓN
-- =============================================
CREATE INDEX idx_dotacion_inv_tipo ON dotacion_inventario(tipo_dotacion_id);
CREATE INDEX idx_dotacion_inv_talla ON dotacion_inventario(talla_id);
CREATE INDEX idx_dotacion_inv_codigo ON dotacion_inventario(dotacion_inv_codigo);
CREATE INDEX idx_solicitud_empleado ON dotacion_solicitud(empleado_id);
CREATE INDEX idx_solicitud_estado ON dotacion_solicitud(solicitud_estado);
CREATE INDEX idx_solicitud_fecha ON dotacion_solicitud(solicitud_fecha);
CREATE INDEX idx_entrega_empleado ON dotacion_entrega(empleado_id);
CREATE INDEX idx_entrega_año ON dotacion_entrega(entrega_año);
CREATE INDEX idx_control_empleado_año ON dotacion_control_anual(empleado_id, control_año);

-- =============================================
-- TRIGGERS PARA CONTROL AUTOMÁTICO
-- =============================================

-- Trigger para actualizar cantidad en inventario al entregar
CREATE TRIGGER tr_actualizar_inventario
    UPDATE OF entrega_det_cantidad ON dotacion_entrega_detalle
    REFERENCING OLD AS old_row NEW AS new_row
    FOR EACH ROW
    (
        UPDATE dotacion_inventario 
        SET dotacion_inv_cantidad_actual = dotacion_inv_cantidad_actual - new_row.entrega_det_cantidad
        WHERE dotacion_inv_id = new_row.dotacion_inv_id
    );

-- =============================================
-- PROCEDIMIENTOS ALMACENADOS
-- =============================================

-- Procedimiento para verificar límite anual de entregas
CREATE PROCEDURE sp_verificar_limite_anual(emp_id INTEGER, año INTEGER)
    RETURNS INTEGER;
    
    DEFINE entregas_año INTEGER;
    DEFINE limite_maximo INTEGER DEFAULT 3;
    
    SELECT COUNT(*) INTO entregas_año
    FROM dotacion_entrega 
    WHERE empleado_id = emp_id 
    AND entrega_año = año 
    AND entrega_situacion = 1;
    
    IF entregas_año >= limite_maximo THEN
        RETURN 0; -- No puede recibir más entregas
    ELSE
        RETURN 1; -- Puede recibir entregas
    END IF;
    
END PROCEDURE;

-- Procedimiento para obtener inventario disponible por tipo y talla
CREATE PROCEDURE sp_inventario_disponible(tipo_id INTEGER, talla_id INTEGER)
    RETURNS INTEGER;
    
    DEFINE cantidad_disponible INTEGER DEFAULT 0;
    
    SELECT SUM(dotacion_inv_cantidad_actual) INTO cantidad_disponible
    FROM dotacion_inventario 
    WHERE tipo_dotacion_id = tipo_id 
    AND talla_id = talla_id 
    AND dotacion_inv_situacion = 1
    AND dotacion_inv_cantidad_actual > 0;
    
    IF cantidad_disponible IS NULL THEN
        LET cantidad_disponible = 0;
    END IF;
    
    RETURN cantidad_disponible;
    
END PROCEDURE;

-- =============================================
-- VISTAS PARA REPORTES
-- =============================================

-- Vista resumen de inventario
CREATE VIEW v_inventario_resumen AS
SELECT 
    di.dotacion_inv_id,
    di.dotacion_inv_codigo,
    td.tipo_dotacion_nombre,
    t.talla_codigo,
    di.dotacion_inv_marca,
    di.dotacion_inv_modelo,
    di.dotacion_inv_color,
    di.dotacion_inv_cantidad_inicial,
    di.dotacion_inv_cantidad_actual,
    di.dotacion_inv_cantidad_minima,
    di.dotacion_inv_fecha_ingreso,
    CASE 
        WHEN di.dotacion_inv_cantidad_actual <= di.dotacion_inv_cantidad_minima 
        THEN 'STOCK_BAJO'
        WHEN di.dotacion_inv_cantidad_actual = 0 
        THEN 'SIN_STOCK'
        ELSE 'DISPONIBLE'
    END AS estado_stock
FROM dotacion_inventario di
JOIN tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
JOIN talla t ON di.talla_id = t.talla_id
WHERE di.dotacion_inv_situacion = 1;

-- Vista entregas por empleado
CREATE VIEW v_entregas_empleado AS
SELECT 
    e.empleado_id,
    e.empleado_nombres,
    e.empleado_apellidos,
    e.empleado_puesto,
    de.entrega_fecha,
    de.entrega_año,
    td.tipo_dotacion_nombre,
    t.talla_codigo,
    ded.entrega_det_cantidad,
    di.dotacion_inv_marca,
    di.dotacion_inv_modelo
FROM dotacion_entrega de
JOIN empleado e ON de.empleado_id = e.empleado_id
JOIN dotacion_entrega_detalle ded ON de.entrega_id = ded.entrega_id
JOIN dotacion_inventario di ON ded.dotacion_inv_id = di.dotacion_inv_id
JOIN tipo_dotacion td ON di.tipo_dotacion_id = td.tipo_dotacion_id
JOIN talla t ON di.talla_id = t.talla_id
WHERE de.entrega_situacion = 1;

-- Vista control anual
CREATE VIEW v_control_anual AS
SELECT 
    e.empleado_id,
    e.empleado_nombres,
    e.empleado_apellidos,
    e.empleado_puesto,
    dca.control_año,
    dca.control_entregas_realizadas,
    dca.control_entregas_maximas,
    (dca.control_entregas_maximas - dca.control_entregas_realizadas) AS entregas_disponibles,
    dca.control_fecha_primera_entrega,
    dca.control_fecha_ultima_entrega
FROM empleado e
LEFT JOIN dotacion_control_anual dca ON e.empleado_id = dca.empleado_id
WHERE e.empleado_situacion = 1;

