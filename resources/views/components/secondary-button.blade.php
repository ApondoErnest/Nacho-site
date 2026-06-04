<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-md border border-nacho-dark/20 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-nacho-dark shadow-sm transition ease-in-out duration-150 hover:bg-nacho-cream focus:outline-none focus:ring-2 focus:ring-nacho-primary focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
