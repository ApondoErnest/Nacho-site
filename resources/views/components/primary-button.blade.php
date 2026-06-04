<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-md border border-transparent bg-nacho-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition ease-in-out duration-150 hover:bg-nacho-primary-dark focus:bg-nacho-primary-dark focus:outline-none focus:ring-2 focus:ring-nacho-primary focus:ring-offset-2 active:bg-nacho-primary-dark']) }}>
    {{ $slot }}
</button>
