<x-admin-layout title="Doctores | Simify" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Doctores',
        'href' => route('admin.doctors.index'),
    ],
    [
        'name' => 'Editar',
    ],
]">

    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- Encabezado con foto y acciones --}}
        <x-wire-card class="mb-8">
            <div class = "lg:flex lg:justify-between lg:items-center">
                <div class= "flex items-center">
                    <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                        class="h-20 w-20 rounded-full object-cover object-center">
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $doctor->user->name }}</p>
                        <p class="text-sm text-gray-500">Licencia: {{ $doctor->medical_license_number ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}" class="whitespace-nowrap">
                        Volver
                    </x-wire-button>
                    <x-wire-button type="submit" class="flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-check"></i>
                        <span>Guardar cambios</span>
                    </x-wire-button>
                </div>
            </div>

        </x-wire-card>

        <x-wire-card>
            <div class="space-y-4">
                <x-wire-native-select label="Especialidad" name="speciality_id">
                    <option value="">Selecciona una especialidad</option>
                    @foreach ($specialities as $speciality)
                        <option value="{{ $speciality->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                            {{ $speciality->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>

                <x-wire-input label="Número de licencia médica" name="medical_license_number"
                    value="{{ old('medical_license_number', $doctor->medical_license_number ?? 'N/A') }}" />

                <x-wire-textarea label="Biografía" name="biography">
                    {{ old('biography', $doctor->biography ?? 'N/A') }}
                </x-wire-textarea>
            </div>
        </x-wire-card>

    </form>

</x-admin-layout>
