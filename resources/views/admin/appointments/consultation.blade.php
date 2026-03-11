<x-admin-layout title="Consulta | Simify" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Citas',
        'href' => route('admin.appointments.index'),
    ],
    [
        'name' => 'Consulta',
    ],
]">

    @livewire('admin.consultations.manage-consultation', ['appointment' => $appointment])

</x-admin-layout>
