<div class="flex items-center space-x-2">
    {{-- TODO: Add action buttons (View, Edit status, Cancel) --}}
    <x-wire-button color="green" href="{{ route('admin.appointments.consultation', $appointment) }}" xs>
        Consultar
    </x-wire-button>

    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
</div>
