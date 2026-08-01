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

## Desplegar demo en Render (recomendado / gratis)

El repo incluye `render.yaml`, `Dockerfile` y `scripts/00-laravel-deploy.sh`.

### Pasos

1. Entra en [dashboard.render.com](https://dashboard.render.com) e inicia sesión con GitHub.
2. **New** → **Blueprint** → conecta el repo `RobertoGarc/landing-captacion-hipotecaria`.
3. Render leerá `render.yaml` y creará:
   - Web service (Docker, plan free)
   - PostgreSQL (plan free)
4. Cuando pida **APP_KEY**, pega el resultado de:

```bash
php artisan key:generate --show
```

(o usa esta de demo: `base64:OGoQG0IK2/pVGrW78fkYNzYdc2VKto3G/I/Pgj5pbkk=`)

5. Confirma el deploy. La URL será tipo `https://landing-hipotecaria.onrender.com`.

> El plan free **se duerme** tras ~15 min sin tráfico. La primera visita puede tardar 30–60 s en despertar.

### Acceso demo

- Landing: URL de Render (switch Clarahipoteca / Crediservicios).
- Admin: `/login` → `admin@clarahipoteca.test` / `password`.

### Alternativa: Railway

También hay `railway.toml` + `.env.railway.example` si prefieres Railway (créditos iniciales, luego de pago).

## Notas

- El archivo `.env` **no** se sube al repositorio.
- Cada push a `main` redespliega si el servicio está conectado a GitHub.
