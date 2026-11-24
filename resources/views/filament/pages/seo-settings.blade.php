<x-filament-panels::page>
    <form wire:submit="save" style="display:grid;gap:24px">
        {{ $this->form }}
        <div><x-filament::button type="submit" wire:loading.attr="disabled">Guardar configuración</x-filament::button></div>
    </form>
    <x-filament::section>
        <x-slot name="heading">Al publicar el sitio</x-slot>
        <div style="display:grid;gap:12px;font-size:14px;line-height:1.7">
            <p>En el servidor configure <code>APP_ENV=production</code>, <code>APP_DEBUG=false</code> y <code>APP_URL</code> con el mismo dominio HTTPS de arriba. Solo así se permite la indexación.</p>
            <p>Después dé de alta el dominio en <strong>Google Search Console</strong> y <strong>Bing Webmaster Tools</strong>, pegue aquí sus códigos de verificación y envíe el sitemap: <code>{{ url('/sitemap.xml') }}</code>.</p>
            <p>El sitio publica automáticamente <code>/robots.txt</code>, <code>/sitemap.xml</code> y <code>/llms.txt</code>. Los artículos marcados como no indexables y las páginas con indexación apagada no aparecen en el sitemap.</p>
            <p><a href="{{ \App\Filament\Pages\SeoDashboard::getUrl() }}">Volver al dashboard SEO →</a></p>
        </div>
    </x-filament::section>
</x-filament-panels::page>
