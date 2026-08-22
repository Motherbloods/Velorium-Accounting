@if (session('status'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
        class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
        class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-700 text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
