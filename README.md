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

## Notas

- El archivo `.env` **no** se sube al repositorio (secretos locales).
- Este repo es el **código fuente** del demo. Para una URL pública en internet hace falta un hosting PHP (Laravel Cloud, VPS, etc.).
