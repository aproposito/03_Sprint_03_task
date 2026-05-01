# Tasca S3.03 — TODO App (Sprint 3: Full Stack PHP 2025–2026)

## Descripción

Aplicación web de gestión de tareas personales construida en PHP con arquitectura MVC sobre el framework `phpInitialDemo`. Cada usuario ve únicamente sus propias tareas. La aplicación soporta dos capas de persistencia intercambiables: JSON (Nivel 1) y MySQL/PDO (Nivel 2).

## Stack técnico

- PHP 8.2 — arquitectura MVC (framework `phpInitialDemo`)
- MySQL / PDO — persistencia relacional (Nivel 2)
- JSON — persistencia en fichero (Nivel 1)
- Tailwind CSS (sin framework frontend)
- Git + Gitflow

## Funcionalidades

- Registro, login y logout de usuarios
- Dashboard con lista de tareas propias ordenadas por estado
- Filtros por nombre, estado y etiqueta
- CRUD completo de tareas con fechas automáticas
- CRUD completo de etiquetas (globales y privadas por usuario)
- Asignación de etiquetas a tareas
- Editar perfil y eliminar cuenta (con borrado en cascada)
- Alternancia entre persistencia JSON y MySQL mediante una constante

## Estructura

```
app/
├─ controllers/
│  ├─ TasksController.php
│  ├─ UserController.php
│  └─ TagsController.php
├─ models/
│  ├─ Task.php / TaskMysql.php
│  ├─ User.php / UserMysql.php
│  └─ Tag.php / TagMysql.php
└─ views/
   └─ scripts/
      ├─ tasks/
      ├─ user/
      └─ tags/
config/
├─ routes.php
├─ environment.inc.php
├─ settings.ini.example
└─ Todo_Model_SQL.sql
data/
├─ tasks.json
├─ users.json
├─ tags.json
└─ task_tags.json
web/
└─ index.php
```

## Instalación y ejecución

### Nivel 1 — Persistencia JSON

1. Clonar el repositorio y situarse en la rama `develop`:
   ```bash
   git clone https://github.com/aproposito/03_Sprint_03_task.git
   git checkout develop
   ```
2. Configurar un Virtual Host apuntando a `/web`, o usar XAMPP con la ruta completa.
3. En `config/environment.inc.php`, establecer:
   ```php
   define('PERSISTENCE', 'json');
   ```
4. Acceder a `/user/register` para crear una cuenta.

### Nivel 2 — Persistencia MySQL

1. Ejecutar `config/Todo_Model_SQL.sql` en MySQL para crear el esquema.
2. Copiar `config/settings.ini.example` a `config/settings.ini` y rellenar las credenciales:
   ```ini
   [database]
   driver   = mysql
   host     = 127.0.0.1
   dbname   = todo_db
   user     = root
   password = your_password
   ```
3. En `config/environment.inc.php`, establecer:
   ```php
   define('PERSISTENCE', 'mysql');
   ```

**URL local:** `http://todo.local` (con Virtual Host) o `http://localhost/[ruta-proyecto]/web`

## Decisiones de diseño

- Modelos paralelos JSON/MySQL con la misma interfaz pública — la constante `PERSISTENCE` en `environment.inc.php` controla qué capa se instancia.
- `$_SESSION['user']` almacena el objeto usuario completo tras el login.
- Borrado en cascada implementado manualmente en MySQL: `task_tags → tasks/tags → users`.
- `ApplicationController::init()` inyecta `baseUrl` en todas las vistas para compatibilidad con y sin Virtual Host.

## Diagrama de base de datos (Nivel 2)

<img width="722" height="564" alt="mer_diagram" src="https://github.com/user-attachments/assets/f4710341-94bc-4a20-a51e-9695ef53223a" />

