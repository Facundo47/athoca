# 🌿 ATHOCA - Sistema Web de Servicios

Proyecto académico de pasantía para la empresa **ATHOCA** (Catamarca, Argentina).

## 📋 Descripción

Sistema web simple que permite:
- **Visitantes** (sin registro): ver servicios y eventos publicados.
- **Vendedores** (con registro): publicar servicios con links a WhatsApp/redes.
- **Administrador**: gestionar usuarios, servicios, eventos y contraseñas.

## 🛠️ Tecnologías usadas

- **PHP** (backend)
- **MySQL** (base de datos)
- **HTML5 + CSS3** (frontend)
- **XAMPP** (servidor local)

## 🚀 Instalación paso a paso

### 1. Requisitos
- Instalar [XAMPP](https://www.apachefriends.org/es/index.html)
- Abrir el panel de XAMPP y encender **Apache** y **MySQL**

### 2. Copiar el proyecto
Colocar la carpeta `athoca/` dentro de `C:\xampp\htdocs\`

### 3. Crear la base de datos
1. Ir a `http://localhost/phpmyadmin`
2. Crear BD llamada `athoca_db`
3. Ejecutar el script SQL (ver `database.sql`)

### 4. Configurar conexión
Editar `config/conexion.php` con tus datos:
```php
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "athoca_db";