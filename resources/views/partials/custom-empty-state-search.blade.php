<div class="flex flex-col items-center justify-center space-y-4">
    <x-filament::icon name="heroicon-o-magnifying-glass" class="w-10 h-10 text-gray-400" />
    <h2 class="text-xl font-semibold text-gray-600">Please search for a customer</h2>
    <p class="text-gray-500">You can search by name, email, phone, or RSA ID.</p>

    <div class="w-full max-w-sm">
        <input
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:outline-none"
            placeholder="Enter RSA ID or Email..."
            onkeydown="if (event.key === 'Enter') {
                const url = new URL(window.location.href);
                url.searchParams.set('tableSearch', event.target.value);
                window.location.href = url.toString();
            }"
        />
    </div>
</div>
