<?php
// alta_hermanos.php

// Título de la página
$title = 'Alta de Hermanos';

// Includes de configuración y sesión
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';

// Calcular el siguiente número de hermano
try {
    $stmtNum = $conn->query('SELECT MAX(numero_hermano) AS max_num FROM hermanos');
    $rowNum = $stmtNum->fetch(PDO::FETCH_ASSOC);
    $nextNumero = $rowNum && $rowNum['max_num'] ? $rowNum['max_num'] + 1 : 1;
} catch (PDOException $e) {
    $nextNumero = '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Contenedor principal */
        .form-wrapper {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .form-wrapper h2 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #4a148c;
        }
        /* Grid del formulario */
        .form-wrapper form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        /* Flex para filas agrupadas */
        .row-flex {
            grid-column: 1 / -1;
            display: flex;
            gap: 20px;
        }
        .row-flex .form-group {
            flex: 1;
        }
        /* Para el campo Nº Hermano */
        .numero-group input {
            max-width: 120px;
            width: 100%;
        }
        /* Para elementos que ocupan toda la fila */
        .full-width {
            grid-column: 1 / -1;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
            display: flex;
            align-items: center;
        }
        .form-group label i {
            margin-right: 8px;
            color: #4a148c;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
            background: rgba(255,255,255,0.9);
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #7b1fa2;
            box-shadow: 0 0 8px rgba(123,31,162,0.2);
        }
        .btn-submit {
            grid-column: 1 / -1;
            padding: 14px;
            background: linear-gradient(135deg, #4a148c, #7b1fa2);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-submit i {
            margin-right: 8px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #6a1b9a, #8e24aa);
        }
        .btn-cancel {
            padding: 14px 20px;
            background: #ddd;
            color: #333;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-cancel:hover {
            background: #ccc;
        }
        .btn-cancel i {
            margin-right: 8px;
            color: #dc3545;
        }
        /* Ocultar inicialmente detalles bancarios */
        .bank-details {
            display: none;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <main class="form-wrapper">
        <h2><i class="fa fa-user-plus"></i> <?php echo htmlspecialchars($title); ?></h2>
        <form action="process_alta_hermanos.php" method="POST" enctype="multipart/form-data">

            <!-- Nº Hermano -->
            <div class="form-group numero-group">
                <label for="numero_hermano"><i class="fa fa-hashtag"></i> Nº Hermano</label>
                <input type="text" id="numero_hermano" name="numero_hermano" value="<?php echo htmlspecialchars($nextNumero); ?>" readonly>
            </div>

            <!-- Nombre y Apellidos -->
            <div class="row-flex">
                <div class="form-group">
                    <label for="nombre"><i class="fa fa-user"></i> Nombre</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej. Juan">
                </div>
                <div class="form-group">
                    <label for="apellidos"><i class="fa fa-user"></i> Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required placeholder="Ej. Pérez García">
                </div>
            </div>

            <!-- DNI, Fecha y Sexo -->
            <div class="row-flex">
                <div class="form-group">
                    <label for="dni"><i class="fa fa-id-card"></i> DNI</label>
                    <input type="text" id="dni" name="dni" required placeholder="12345678A">
                </div>
                <div class="form-group">
                    <label for="fecha_nac"><i class="fa fa-calendar-alt"></i> Fecha de nacimiento</label>
                    <input type="date" id="fecha_nac" name="fecha_nac" required>
                </div>
                <div class="form-group">
                    <label for="sexo"><i class="fa fa-venus-mars"></i> Sexo</label>
                    <select id="sexo" name="sexo" required>
                        <option value="">Seleccione</option>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                    </select>
                </div>
            </div>

            <!-- Teléfono y Email -->
            <div class="row-flex">
                <div class="form-group">
                    <label for="telefono"><i class="fa fa-phone"></i> Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="600123456">
                </div>
                <div class="form-group">
                    <label for="email"><i class="fa fa-envelope"></i> Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="ejemplo@correo.com">
                </div>
            </div>

            <!-- Método de pago cuotas -->
            <div class="form-group full-width">
                <label for="metodo_pago"><i class="fa fa-credit-card"></i> Pago de cuotas</label>
                <select id="metodo_pago" name="metodo_pago" required>
                    <option value="">Seleccione</option>
                    <option value="cobrador">Cobrador</option>
                    <option value="domiciliacion">Domiciliación bancaria</option>
                </select>
            </div>

            <!-- Detalles domiciliación -->
            <div class="bank-details">
                <div class="row-flex">
                    <div class="form-group">
                        <label for="iban"><i class="fa fa-university"></i> IBAN</label>
                        <input type="text" id="iban" name="iban" placeholder="ES00 0000 0000 0000 0000 0000">
                    </div>
                    <div class="form-group">
                        <label for="titular_cuenta"><i class="fa fa-user-circle"></i> Titular de la cuenta</label>
                        <input type="text" id="titular_cuenta" name="titular_cuenta" placeholder="Nombre del titular">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="form-group">
                        <label for="cuota"><i class="fa fa-money-bill-wave"></i> Cuota (€)</label>
                        <input type="number" id="cuota" name="cuota" step="0.01" value="3.00">
                    </div>
                    <div class="form-group">
                        <label for="periodicidad"><i class="fa fa-calendar-check"></i> Periodicidad</label>
                        <select id="periodicidad" name="periodicidad">
                            <option value="mensual" selected>Mensual</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="semestral">Semestral</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group full-width">
                <label for="observaciones"><i class="fa fa-comment"></i> Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="4" placeholder="Añadir notas..."></textarea>
            </div>

            <!-- Foto carnet -->
            <div class="form-group full-width">
                <label for="foto"><i class="fa fa-camera"></i> Foto tamaño carnet</label>
                <input type="file" id="foto" name="foto" accept="image/*">
            </div>

            <!-- Botones de acción -->
            <div class="full-width" style="display:flex; gap:10px; justify-content:flex-end">
                <button type="button" class="btn-cancel" onclick="window.location.href='../gestion_hermanos.html'">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-submit">
                    <i class="fa fa-check-circle"></i> Registrar Hermano
                </button>
            </div>
        </form>
    </main>

    <script>
        // Mostrar/ocultar detalles de domiciliación
        document.getElementById('metodo_pago').addEventListener('change', function() {
            const bank = document.querySelector('.bank-details');
            bank.style.display = this.value === 'domiciliacion' ? 'block' : 'none';
        });
    </script>
</body>
</html>
