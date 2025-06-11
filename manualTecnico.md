![Pantalla de inicio](./src/assets/img/manualUsu/portada.jpg)
# MANUAL TECNICO  
**WEB COFRADÍA BUENA MUERTE**  

**Autor:** Lidia Lopez Martin  
**Versión:** 1.0  
**Fecha:** 16/06/2025  

---

## Índice

1. [Introducción](#introducción)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Requisitos Técnicos](#requisitos-técnicos)
4. [Instalación y Puesta en Marcha](#instalación-y-puesta-en-marcha)
5. [Configuración de Docker y Base de Datos](#configuración-de-docker-y-base-de-datos)
6. [Descripción de la Arquitectura](#descripción-de-la-arquitectura)
7. [Descripción de los Componentes](#descripción-de-los-componentes)
8. [Principales Funcionalidades](#principales-funcionalidades)
9. [Seguridad](#seguridad)
10. [Pruebas y Validaciones](#pruebas-y-validaciones)
11. [Mantenimiento y Ampliación](#mantenimiento-y-ampliación)
12. [Contactar con el Autor](#contactar-con-el-autor)

---

## Introducción

Este manual técnico describe la arquitectura, instalación, configuración y funcionamiento interno de la aplicación web **Cofradía Buena Muerte**. El objetivo es facilitar el mantenimiento, despliegue y futuras ampliaciones del sistema.

---

## Estructura del Proyecto

## Estructura del Proyecto

```plaintext
proyectoFinCiclo/
│
├── src/
│   ├── assets/           # Imágenes, CSS, JS
│   ├── forms/            # Formularios PHP (contacto, etc.)
│   ├── php/              # Lógica de servidor (bibliotecaFunciones.php, accionesUsuario.php, etc.)
│   ├── js/               # Scripts JavaScript (controlUsuarios.js, controlRegistrar.js, etc.)
│   ├── index.html        # Página de inicio
│   ├── registro.php      # Página de registro
│   └── ...               # Otras páginas y recursos
│
├── db-init/
│   └── init.sql          # Script de inicialización de la base de datos
│
├── Dockerfile            # Imagen personalizada PHP+Apache
├── docker-compose.yml    # Orquestación de servicios (web, db)
├── php.ini               # Configuración personalizada de PHP
├── MANUAL_USUARIO.md     # Manual de usuario
├── manualTecnico.md      # Manual técnico (este documento)
└── README.md             # Descripción general del proyecto
```

---

## Personalización de la Plantilla

Para el diseño del sitio web se partió de una plantilla gratuita basada en Bootstrap 5. Esta plantilla ha sido profundamente modificada para ajustarse a la identidad y necesidades del sitio:

- **Paleta de colores personalizada:** Se cambiaron los estilos CSS para reflejar los colores institucionales de la Cofradía.
- **Imágenes:** Se reemplazaron todas las imágenes por material gráfico propio.
- **Contenido adaptado:** Se personalizaron las páginas con contenido textual e imágenes originales, además de reorganizar secciones.
- **Eliminación de secciones innecesarias:** Por ejemplo, se eliminó la página del blog y otros apartados no relevantes.
- **Cambios en la navegación:** Se ajustó el menú y enlaces según los apartados reales del proyecto.


---

## Requisitos Técnicos

- **Docker** y **Docker Compose** (para entorno de desarrollo y despliegue)
- **PHP 8.2** (con Apache)
- **MySQL** (servidor de base de datos)
- **Composer** (opcional, para gestión de dependencias PHP)
- Navegador web moderno

---

## Instalación y Puesta en Marcha

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/lidiacampanilla/proyectoFinCiclo.git
   cd proyectoFinCiclo
2. **Arrancar los servicios con Docker Compose:**

- **Docker Desktop**
- **Termial:** docker-compose up -build (desde la carpeta del proyecto)

3. **Acceder a la aplicacion**

- Navegar a **http://localhost:8080**

---

## 5. Configuración de Docker y Base de Datos

- **Dockerfile:** Define la imagen personalizada de PHP 8.2 con Apache, instala extensiones necesarias y copia el código fuente y la configuración personalizada.
- **docker-compose.yml:** Orquesta los servicios `web` (PHP+Apache) y `db` (MySQL), monta volúmenes para persistencia y scripts de inicialización.
- **db-init/init.sql:** Script que crea la base de datos `COFRADIA`, tablas y relaciones necesarias para la aplicación.

---

## 6. Descripción de la Arquitectura

- **Frontend:** HTML5, Bootstrap 5, CSS personalizado y JavaScript para validaciones y operaciones dinámicas.
- **Backend:** PHP para la lógica de negocio, validaciones, acceso a base de datos y envío de emails (pendiente de finalizar).
- **Base de datos:** MySQL, con tablas relacionales para usuarios, tipos, operaciones, etc.
- **Comunicación:** AJAX para operaciones dinámicas (modificación de usuarios, validaciones, etc.).

---

## 7. Descripción de los Componentes

### PHP
- `bibliotecaFunciones.php`: Funciones reutilizables para acceso a datos, generación de tablas, validaciones, etc.
- `accionesUsuario.php`: Controlador de acciones AJAX (modificar, borrar, insertar usuarios).
- `registroUsuarios.php`: Lógica de registro de nuevos usuarios.
- `forms/contact.php`: Procesamiento del formulario de contacto y envío de emails (pendiente de finalizar).

### JavaScript
- `controlUsuarios.js`: Validaciones y gestión dinámica de usuarios (modificar, borrar, etc.).
- `controlRegistrar.js`: Validaciones en el registro de usuarios.

### CSS
- `main.css`: Estilos personalizados sobre Bootstrap.

### Base de datos
- `init.sql`: Estructura y datos iniciales de la base de datos.

---

## 8. Principales Funcionalidades

- Registro y gestión de usuarios con diferentes tipos (nazareno, costalero, mantilla, otros, junta, administrador).
- Los tipos administrador y junta, seran gestionados por el administrador, no son de libre elección.
- Validación de campos (DNI, email, cuenta bancaria) tanto en frontend como en backend.
- Gestión de operaciones y tipos de usuario.
- Envío de mensajes de contacto a través del formulario (pendiente de finalizar).
- Panel de administración para gestión múltiple de usuarios.
- Seguridad en el almacenamiento de contraseñas (hashing).

---

## 9. Seguridad

- Contraseñas almacenadas con `password_hash` y verificadas con `password_verify`.
- Validación de datos en frontend y backend.
- Control de acceso según tipo de usuario.
- Protección contra inyección SQL mediante consultas preparadas (`PDO`).

---

## 10. Pruebas y Validaciones

- Pruebas manuales de registro, login, modificación y borrado de usuarios.
- Validación de formularios en cliente y servidor.
- Pruebas de envío de emails (en entorno real o con herramientas como MailHog en local).
- Pruebas de persistencia de datos tras reinicio de contenedores Docker.

---

## 11. Mantenimiento y Ampliación

- Para añadir nuevas funcionalidades, crea nuevos scripts PHP en `src/php/` y enlaza desde el frontend.
- Para modificar la estructura de la base de datos, actualiza `db-init/init.sql` y reinicia el contenedor `db` (ten en cuenta la persistencia de datos).
- Para cambiar la configuración de PHP, edita `php.ini` y reconstruye el contenedor `web`.
- Las ampliaciones mas necesarias:
    - Envio de emails en el apartado contacto.
    - Implementar código para que el administrador y la junta puedan añadir noticias e imagenes, en las partes de la web diseñadas para esto.

---

## 12. Contactar con el Autor

**Lidia Lopez Martin**  
Email: [tuemail@tudominio.com](mailto:tuemail@tudominio.com)

---