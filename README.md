# Landing de captación hipotecaria (demo)

Prototipo de landing de alto rendimiento para captar leads hipotecarios, con **switch de marca** entre dos demos:

| Marca | Mercado | Moneda |
|-------|---------|--------|
| **Clarahipoteca** | España | EUR (€) |
| **Crediservicios** | Ecuador | USD ($) |

Ideal para mostrar a clientes (Workana, brokers, inmobiliarias) cómo quedaría su captura de leads.

## Qué incluye

- Landing responsive (hero, valor, ventajas, proceso, testimonios, FAQ)
- Formulario multipaso con validación, lógica condicional y honeypot anti-spam
- Captura de UTMs / gclid / fbclid
- Integración preparada con **Clientify** (job en cola)
- Analytics: GA4, GTM y Meta Pixel (vía variables de entorno)
- Emails de confirmación al lead y aviso al equipo
- Panel admin: listado de leads + editor de contenidos
- Temas visuales y provincias/moneda según la marca activa

## Stack

- PHP 8.4 · Laravel 13 · Livewire 4 · Flux · Fortify
- Tailwind CSS 4 · Vite
- Pest

## Demo de marcas

En la propia landing hay un switch **Demo marca**. También puedes forzar la marca por URL:

```
/?demo=clarahipoteca
/?demo=crediservicios
```

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env` (MySQL o SQLite) y luego:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Abre `http://localhost:8000`.

### Acceso admin (tras el seeder)

- URL: `/login`
- Email: `admin@clarahipoteca.test`
- Password: `password`

### Variables opcionales

En `.env.example` están placeholders para:

- `CLIENTIFY_API_TOKEN` — sync de leads al CRM
- `GTM_ID` / `GA4_MEASUREMENT_ID` / `META_PIXEL_ID`
- `LEADS_NOTIFY_EMAILS` — avisos internos (separados por coma)

Sin estas claves la landing y el formulario funcionan igual; solo quedan desactivadas esas integraciones.

## Desplegar demo en Railway

El repo ya incluye `railway.toml` y `railway/init-app.sh` (migrate + seed + caches).

### 1. Proyecto y base de datos

1. Entra en [railway.com](https://railway.com) e inicia sesión (con GitHub).
2. **New Project** → **Deploy from GitHub repo** → `RobertoGarc/landing-captacion-hipotecaria`.
3. En el mismo proyecto: **Add Service** → **Database** → **PostgreSQL**.

### 2. Variables del servicio de la app

En el servicio de la app → **Variables** → pega esto (ajusta si tu Postgres no se llama `Postgres`):

```env
APP_NAME=LandingHipotecaria
APP_ENV=production
APP_KEY=base64:PEGAR_AQUI_LA_KEY
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
LOG_CHANNEL=stderr
LOG_LEVEL=info
DB_CONNECTION=pgsql
DB_URL=${{Postgres.DATABASE_URL}}
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
MAIL_MAILER=log
MAIL_FROM_ADDRESS=demo@example.com
MAIL_FROM_NAME=LandingHipotecaria
LEADS_BRAND_NAME=Clarahipoteca
RAILPACK_PHP_EXTENSIONS=pgsql
RAILPACK_SKIP_MIGRATIONS=true
```

Genera `APP_KEY` en local con:

```bash
php artisan key:generate --show
```

Referencia completa: `.env.railway.example`.

### 3. Dominio público

En el servicio de la app → **Settings** → **Networking** → **Generate Domain**.

Redeploy si hace falta. La URL quedará tipo `https://….up.railway.app`.

### 4. Acceso demo

- Landing: la URL pública (usa el switch de marca Clarahipoteca / Crediservicios).
- Admin: `/login` → `admin@clarahipoteca.test` / `password`.

Cola en `sync` y mail en `log` para que el demo funcione sin worker ni SMTP. Clientify/analytics se pueden añadir después con variables opcionales.

## Notas

- El archivo `.env` **no** se sube al repositorio (secretos locales).
- Cada push a `main` redespliega el servicio si está conectado a GitHub.
