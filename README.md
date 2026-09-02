<div align="center">

# 🛒 FeliShop

**Sistema de punto de venta multisucursal**

Centraliza ventas, inventario y personal de todas tus sucursales en una sola plataforma.

[![Estado](https://img.shields.io/badge/estado-en%20desarrollo-yellow?style=for-the-badge)](https://github.com/)
[![Licencia](https://img.shields.io/badge/licencia-MIT-blue?style=for-the-badge)](LICENSE.md)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/es/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/es/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/es/docs/Web/JavaScript)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

</div>

> ⚠️ **Proyecto en fase inicial.** Este repositorio está comenzando su desarrollo; por el momento este README describe el planteamiento del problema, el objetivo y las tecnologías que se utilizarán. El código y la documentación se irán actualizando conforme avance el proyecto.

---

## 📖 Descripción

**FeliShop** es un sistema de punto de venta (POS) pensado para negocios que operan con más de una sucursal —tiendas de conveniencia, minisúpers, negocios de comida, etc.—. Actualmente, este tipo de negocios suele gestionar sus ventas e inventario de forma independiente en cada sucursal, usando métodos manuales (cuadernos, hojas de cálculo) o sistemas de punto de venta aislados que no se comunican entre sí.

Esta desconexión genera problemas como:

- Falta de visibilidad en tiempo real de ventas e inventario por sucursal.
- Dificultad para tomar decisiones de reabastecimiento (ej. un producto agotado en una sucursal mientras sobra en otra).
- Ausencia de control centralizado de usuarios y roles, permitiendo que cualquier persona con acceso modifique precios o inventario sin un registro claro de quién lo hizo.
- Cortes de caja y control de compras a proveedores hechos manualmente, lo que aumenta el riesgo de errores o pérdidas no detectadas.

**FeliShop** busca resolver esto centralizando la operación de todas las sucursales bajo un mismo sistema: cada sucursal mantiene su propio inventario, mientras un administrador central tiene visibilidad y control total sobre sucursales, usuarios, proveedores e inventario.

---

## 🎯 Objetivo general

Desarrollar un sistema de punto de venta (FeliShop) que permita centralizar la administración de ventas, inventario, usuarios y proveedores de un negocio con múltiples sucursales, brindando al administrador visibilidad y control en tiempo real sobre la operación de cada punto de venta, y facilitando a cada sucursal la gestión de su propio inventario mediante roles de usuario diferenciados (administrador, gerente, cajero).

---

## 👥 Roles de usuario (planeados)

### 🛡️ Administrador
Control total del sistema: gestión de sucursales, usuarios, proveedores e inventario global.

### 🧑‍💼 Gerente de sucursal
Administra el inventario, ventas y personal de su propia sucursal.

### 🧾 Cajero
Realiza ventas y consulta inventario dentro de su sucursal.

---

## 🛠️ Tecnologías a utilizar

<p align="left">
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" width="50" height="50" title="HTML5"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" width="50" height="50" title="CSS3"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" width="50" height="50" title="JavaScript"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" width="50" height="50" title="PHP"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" width="50" height="50" title="MySQL"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg" width="50" height="50" title="Bootstrap"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/apache/apache-original.svg" width="50" height="50" title="Apache"/>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg" width="50" height="50" title="Git"/>
</p>

| Tecnología | Uso | Capa |
|---|---|---|
| **HTML5** | Estructura de las páginas: login, dashboard, inventario, ventas, etc. | Frontend |
| **CSS3** | Estilo visual: colores, tipografía, distribución y experiencia de usuario. | Frontend |
| **JavaScript** | Interactividad: validación de formularios, actualización dinámica de inventario, alertas de éxito/error. | Frontend |
| **Bootstrap** *(opcional)* | Framework CSS para interfaces responsivas sin diseñar todo desde cero. | Frontend |
| **PHP** | Lógica de negocio, manejo de sesiones y roles (admin, gerente, cajero), conexión con la base de datos. | Backend |
| **Servidor web** (Apache / Nginx) | Ejecuta PHP y sirve las páginas al navegador. | Backend |
| **SQL** | Gestión de tablas de usuarios, inventarios, ventas y sucursales; consultas, inserciones y actualizaciones. | Datos |
| **MySQL / MariaDB** | Almacenamiento de toda la información del sistema. | Datos |
| **Git + GitHub** | Control de versiones y trabajo colaborativo en equipo. | Colaboración |
| **XAMPP** *(opcional)* | Entorno local con Apache, PHP y MySQL para pruebas antes de desplegar. | Entorno local |

### Síntesis por capas

- **Frontend** → HTML, CSS, JavaScript, Bootstrap.
- **Backend** → PHP, servidor web.
- **Datos** → SQL, gestor de base de datos.
- **Colaboración** → Git / GitHub.
- **Entorno local** → XAMPP.

---

## 📁 Estructura del proyecto (propuesta)

```
FeliShop/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── config/
│   └── db.php
├── database/
│   └── felishop.sql
├── pages/
│   ├── login.php
│   ├── dashboard.php
│   ├── inventario.php
│   ├── ventas.php
│   ├── sucursales.php
│   └── proveedores.php
├── .gitignore
├── LICENSE.md
└── README.md
```

> Esta estructura es una propuesta inicial y podrá ajustarse conforme avance el desarrollo.

---

## 🚧 Estado actual

El proyecto se encuentra en su **etapa inicial**: se ha definido el problema, el objetivo general y las tecnologías a utilizar. Aún no se ha comenzado la implementación del código. Este README se irá actualizando con capturas de pantalla, instrucciones de instalación y avances conforme el desarrollo progrese.

---

## ✒️ Integrantes del equipo
| Nombre | GitHub |
|---|---|
| Kayleen Avendano Reguera | [@kayleen2807](https://github.com/kayleen2807) |
| Josue Felipe Cruz Espinosa | [@jcruz31-hue](https://github.com/jcruz31-hue) |
| Abril Azucena Diaz Ruelas | [@adiaz108](https://github.com/adiaz108) |
| Carolina Molina Pimentel | [@Carolina234184](https://github.com/Carolina234184) |
| Maximiliano Tejeda Figueroa | [@mtejeda4](https://github.com/mtejeda4) |
---

## 📄 Licencia

Este proyecto está bajo la Licencia [MIT](LICENSE.md). Puedes usarlo, modificarlo y distribuirlo libremente con atribución.

---

<div align="center">

Hecho con ❤️ por el equipo de FeliShop

</div>
