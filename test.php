<?php
$conn = new PDO('mysql:host=localhost;dbname=moiz_care_pegawai', 'root', '');
$conn->exec("ALTER TABLE employee_payroll_components MODIFY component_id INT(11) UNSIGNED NULL");
$conn->exec("ALTER TABLE employee_payroll_components ADD name VARCHAR(255) NULL AFTER component_id");
$conn->exec("ALTER TABLE employee_payroll_components ADD type ENUM('allowance', 'deduction') NULL AFTER name");
$conn->exec("ALTER TABLE employee_payroll_components ADD calculation_basis VARCHAR(50) NULL AFTER type");
echo "DB ALTERED\n";
