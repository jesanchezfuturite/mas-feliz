# Iniciativa +Feliz 💛

Plataforma oficial de la **Iniciativa +Feliz**, un programa estatal enfocado en el diagnóstico y promoción del bienestar y felicidad ciudadana.

## 🚀 Características Principales

- **Diagnóstico Estatal**: Sistema de encuestas y recolección de datos para medir el índice de felicidad.
- **Panel Administrativo**: Desarrollado con [Filament PHP](https://filamentphp.com/) para una gestión robusta e intuitiva.
- **Gestión Dinámica de Contenido**:
  - **Configurador de Landing**: Permite a los administradores actualizar dinámicamente logos de socios o programas institucionales (ej. *Coahuila Pa' Delante*) desde el panel de control, reflejándose inmediatamente tanto en la página de inicio como en el cuestionario de diagnóstico.

## 🛠 Stack Tecnológico

Este proyecto está construido utilizando:
- **[Laravel](https://laravel.com/)** (PHP Framework)
- **[Livewire](https://livewire.laravel.com/)** (Componentes dinámicos full-stack)
- **[Filament v3](https://filamentphp.com/)** (Panel de administración)
- **[Tailwind CSS](https://tailwindcss.com/)** (Diseño y utilidades CSS)
- **[Alpine.js](https://alpinejs.dev/)** (Interactividad Frontend)

## ⚙️ Requisitos y Configuración Local

El proyecto está diseñado para funcionar en contenedores de **Docker**.

1. Clonar el repositorio.
2. Copiar el archivo `.env.example` a `.env` y configurar variables (asegurarse de que `APP_URL` incluya el puerto local si se usa Docker, ej. `http://localhost:8080`).
3. Instalar dependencias de PHP: `composer install`.
4. Instalar dependencias de Node: `npm install` y compilar assets `npm run build`.
5. Ejecutar migraciones: `php artisan migrate`.
6. **Importante**: Crear el enlace simbólico del storage para que las imágenes dinámicas carguen correctamente:
   ```bash
   php artisan storage:link
   ```
7. Asegurarse de que el directorio `storage/` tenga los permisos correctos (ej. `775` y propietario `www-data` en entornos Linux/Docker).

## 🧑‍💻 Gestión del Logo Dinámico (Partner Logo)

Para actualizar el logo del programa estatal o de socios:
1. Iniciar sesión en el panel de administrador (`/admin`).
2. Navegar a la pestaña **"Configurador Landing"**.
3. Subir una nueva imagen (máx 300kb).
4. Dar clic en Guardar. El cambio se reflejará automáticamente en la cabecera del sitio principal y del cuestionario.

---
*Construido para esparcir bienestar.*
