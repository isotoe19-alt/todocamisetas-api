# 👕 TodoCamisetas API - B2B Inventory Management

Sólida solución Backend RESTful desarrollada en **PHP puro** bajo el patrón de arquitectura **MVC (Modelo-Vista-Controlador)**. El sistema expone endpoints dinámicos orientados a la automatización de inventario y la asignación inteligente de tarifas segmentadas según el perfil de cliente corporativo (B2B).

---

## 🚀 Características del Proyecto
* **Arquitectura MVC Limpia:** Desacoplamiento estricto de responsabilidades sin dependencias externas ni frameworks.
* **Enrutamiento Dinámico:** Sistema centralizado mediante expresiones regulares (`preg_match`) en `index.php`.
* **Capa de Persistencia Segura:** Uso de **PDO** (PHP Data Objects) con consultas preparadas para mitigar inyecciones SQL.
* **Integración Relacional Compleja:** Gestión robusta de relaciones *Muchos a Muchos* (Camisetas $\leftrightarrow$ Tallas) respaldada por **transacciones SQL** (`beginTransaction`/`commit`/`rollBack`) para salvaguardar la consistencia.
* **Reglas de Negocio Automatizadas:** Cálculo dinámico en tiempo de ejecución del campo `precio_final` basándose en la categorización del cliente (Preferencial con precio oferta vs. Regular con precio base).

---

## 🛠️ Stack Tecnológico
* **Backend:** PHP 8.x
* **Base de Datos:** MySQL / MariaDB via XAMPP
* **Pruebas de API:** Postman

---

## 📁 Estructura del Directorio
```text
todocamisetas-api/
├── config/
│   └── Database.php      # Conexión Singleton/PDO a la Base de Datos
├── controllers/
│   ├── ClienteController.php
│   ├── CamisetaController.php
│   └── TallaController.php
├── models/
│   ├── Cliente.php
│   ├── Camiseta.php
│   └── Talla.php
├── .htaccess             # Reescriptura de URLs limpias
├── index.php             # Enrutador central e inyección de dependencias
└── TodoCamisetas API.postman_collection.json  # Suite de pruebas exportada