<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'group' => 'brand',
                'key' => 'brand_name',
                'value' => 'Clarahipoteca',
                'type' => 'text',
                'label' => 'Nombre de marca',
                'sort_order' => 1,
            ],
            [
                'group' => 'brand',
                'key' => 'brand_tagline',
                'value' => 'Estudio hipotecario claro, rápido y sin compromiso',
                'type' => 'text',
                'label' => 'Eslogan',
                'sort_order' => 2,
            ],
            [
                'group' => 'hero',
                'key' => 'hero_headline',
                'value' => 'Descubre qué hipoteca encaja contigo en minutos',
                'type' => 'text',
                'label' => 'Titular del hero',
                'sort_order' => 10,
            ],
            [
                'group' => 'hero',
                'key' => 'hero_subheadline',
                'value' => 'Te ayudamos a comparar opciones reales según tu perfil, tu vivienda y tu capacidad de pago. Completa el estudio inicial y un especialista te contactará.',
                'type' => 'textarea',
                'label' => 'Subtítulo del hero',
                'sort_order' => 11,
            ],
            [
                'group' => 'hero',
                'key' => 'hero_cta',
                'value' => 'Empezar estudio gratuito',
                'type' => 'text',
                'label' => 'Texto del CTA',
                'sort_order' => 12,
            ],
            [
                'group' => 'hero',
                'key' => 'hero_image_url',
                'value' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1800&q=80',
                'type' => 'url',
                'label' => 'URL imagen hero',
                'help' => 'Imagen a pantalla completa del hero.',
                'sort_order' => 13,
            ],
            [
                'group' => 'value',
                'key' => 'value_title',
                'value' => 'Una hipoteca no se elige a ciegas',
                'type' => 'text',
                'label' => 'Título propuesta de valor',
                'sort_order' => 20,
            ],
            [
                'group' => 'value',
                'key' => 'value_body',
                'value' => 'Analizamos tu caso con datos reales para estimar viabilidad, cuota orientativa y siguientes pasos. Sin letra pequeña y sin presión comercial.',
                'type' => 'textarea',
                'label' => 'Texto propuesta de valor',
                'sort_order' => 21,
            ],
            [
                'group' => 'benefits',
                'key' => 'benefits_title',
                'value' => 'Ventajas de estudiar tu hipoteca con nosotros',
                'type' => 'text',
                'label' => 'Título ventajas',
                'sort_order' => 30,
            ],
            [
                'group' => 'benefits',
                'key' => 'benefits_items',
                'value' => [
                    ['title' => 'Estudio personalizado', 'body' => 'Valoramos ingresos, deudas, ahorros y perfil laboral para una recomendación realista.'],
                    ['title' => 'Comparativa transparente', 'body' => 'Te explicamos opciones con lenguaje claro: tipos, plazos, gastos y condiciones.'],
                    ['title' => 'Acompañamiento extremo a extremo', 'body' => 'Desde la primera estimación hasta la firma, con seguimiento continuo.'],
                    ['title' => 'Sin coste por el estudio inicial', 'body' => 'Completas el formulario y recibes orientación sin compromiso de contratación.'],
                ],
                'type' => 'json',
                'label' => 'Listado de ventajas (JSON)',
                'sort_order' => 31,
            ],
            [
                'group' => 'process',
                'key' => 'process_title',
                'value' => 'Así de sencillo es el proceso',
                'type' => 'text',
                'label' => 'Título proceso',
                'sort_order' => 40,
            ],
            [
                'group' => 'process',
                'key' => 'process_steps',
                'value' => [
                    ['title' => 'Completa el estudio', 'body' => 'Cuéntanos sobre la vivienda, la financiación y tu situación económica.'],
                    ['title' => 'Revisión por un especialista', 'body' => 'Analizamos tu perfil y preparamos una propuesta orientativa.'],
                    ['title' => 'Te contactamos', 'body' => 'Recibes una llamada o email con los siguientes pasos y documentación.'],
                    ['title' => 'Avanzas con claridad', 'body' => 'Si encaja, te acompañamos en la negociación y la formalización.'],
                ],
                'type' => 'json',
                'label' => 'Pasos del proceso (JSON)',
                'sort_order' => 41,
            ],
            [
                'group' => 'testimonials',
                'key' => 'testimonials_title',
                'value' => 'Lo que dicen quienes ya dieron el paso',
                'type' => 'text',
                'label' => 'Título testimonios',
                'sort_order' => 50,
            ],
            [
                'group' => 'testimonials',
                'key' => 'testimonials_items',
                'value' => [
                    ['name' => 'Laura M.', 'role' => 'Primera vivienda · Madrid', 'quote' => 'En menos de una semana entendí mi capacidad real de compra y qué bancos encajaban con mi perfil.'],
                    ['name' => 'Carlos y Ana', 'role' => 'Cambio de hipoteca · Valencia', 'quote' => 'Nos ayudaron a bajar la cuota sin líos. El formulario fue claro y el seguimiento excelente.'],
                    ['name' => 'Miguel R.', 'role' => 'Autónomo · Sevilla', 'quote' => 'Pensaba que por ser autónomo sería imposible. Me orientaron con opciones viables y documentación concreta.'],
                ],
                'type' => 'json',
                'label' => 'Testimonios (JSON)',
                'sort_order' => 51,
            ],
            [
                'group' => 'faq',
                'key' => 'faq_title',
                'value' => 'Preguntas frecuentes',
                'type' => 'text',
                'label' => 'Título FAQ',
                'sort_order' => 60,
            ],
            [
                'group' => 'faq',
                'key' => 'faq_items',
                'value' => [
                    ['q' => '¿El estudio inicial tiene coste?', 'a' => 'No. El estudio inicial es gratuito y sin compromiso.'],
                    ['q' => '¿Cuánto tardan en contactarme?', 'a' => 'Normalmente en menos de 24 horas laborables, según volumen y franja horaria.'],
                    ['q' => '¿Necesito documentación desde el principio?', 'a' => 'Para el estudio inicial no. Si avanzamos, te indicaremos exactamente qué documentos aportar.'],
                    ['q' => '¿Trabajáis en toda España?', 'a' => 'Sí, atendemos solicitudes de todas las provincias.'],
                    ['q' => '¿Mis datos están seguros?', 'a' => 'Sí. Usamos tus datos únicamente para gestionar tu solicitud y según la política de privacidad.'],
                ],
                'type' => 'json',
                'label' => 'FAQ (JSON)',
                'sort_order' => 61,
            ],
            [
                'group' => 'footer',
                'key' => 'footer_legal',
                'value' => 'Servicio informativo de orientación hipotecaria. Condiciones sujetas a estudio y aprobación bancaria.',
                'type' => 'textarea',
                'label' => 'Aviso legal pie',
                'sort_order' => 70,
            ],
            [
                'group' => 'contact',
                'key' => 'privacy_url',
                'value' => '/privacidad',
                'type' => 'url',
                'label' => 'URL política de privacidad',
                'sort_order' => 80,
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
