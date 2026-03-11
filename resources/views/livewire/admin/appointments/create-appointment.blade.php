<div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Buscar disponibilidad</h2>
        <p class="text-gray-500 mb-6 text-sm">Encuentra el horario perfecto para tu cita.</p>

        <form wire:submit.prevent="searchAvailability" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <x-wire-input type="date" wire:model="date" min="{{ now()->format('Y-m-d') }}" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                <x-wire-select wire:model="time" placeholder="Selecciona una hora" :options="[
                    ['name' => '08:00', 'id' => '08:00'], ['name' => '08:30', 'id' => '08:30'],
                    ['name' => '09:00', 'id' => '09:00'], ['name' => '09:30', 'id' => '09:30'],
                    ['name' => '10:00', 'id' => '10:00'], ['name' => '10:30', 'id' => '10:30'],
                    ['name' => '11:00', 'id' => '11:00'], ['name' => '11:30', 'id' => '11:30'],
                    ['name' => '12:00', 'id' => '12:00'], ['name' => '12:30', 'id' => '12:30'],
                    ['name' => '13:00', 'id' => '13:00'], ['name' => '13:30', 'id' => '13:30'],
                    ['name' => '14:00', 'id' => '14:00'], ['name' => '14:30', 'id' => '14:30'],
                    ['name' => '15:00', 'id' => '15:00'], ['name' => '15:30', 'id' => '15:30'],
                    ['name' => '16:00', 'id' => '16:00'], ['name' => '16:30', 'id' => '16:30'],
                    ['name' => '17:00', 'id' => '17:00'], ['name' => '17:30', 'id' => '17:30'],
                    ['name' => '18:00', 'id' => '18:00'], ['name' => '18:30', 'id' => '18:30'],
                    ['name' => '19:00', 'id' => '19:00'], ['name' => '19:30', 'id' => '19:30']
                ]" option-label="name" option-value="id" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad (opcional)</label>
                <x-wire-select wire:model="speciality_id" placeholder="Selecciona una especialidad"
                    :options="$specialities" option-label="name" option-value="id" />
            </div>

            <div>
                <x-wire-button type="submit" primary class="w-full">Buscar disponibilidad</x-wire-button>
            </div>
        </form>
    </div>

    {{-- Resultados de Doctores Disponibles --}}
    @if(count($availableDoctors) > 0)
        <div class="mt-8 space-y-6">
            <h3 class="text-lg font-semibold text-gray-800">Doctores Disponibles ({{ \Carbon\Carbon::parse($date)->format('d/m/Y') }})</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableDoctors as $doctor)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                {{ substr($doctor['user_name'], 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">{{ $doctor['user_name'] }}</h4>
                                <p class="text-sm text-gray-500">{{ $doctor['speciality_name'] }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-2 flex-grow">
                            <p class="text-xs text-gray-500 mb-2 uppercase tracking-wide font-semibold">Horarios Disponibles</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($doctor['available_slots'] as $slot)
                                    <button 
                                        wire:click="selectSlot({{ $doctor['id'] }}, '{{ $slot['start_time'] }}', '{{ $slot['end_time'] }}')"
                                        class="py-2 px-1 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-md transition-colors border border-blue-100 text-center">
                                        {{ \Carbon\Carbon::parse($slot['start_time'])->format('H:i') }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($date && empty($availableDoctors))
        <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No se encontraron doctores con disponibilidad para los criterios seleccionados. Intenta con otra fecha u hora.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Confirmación y Selección de Paciente --}}
    @if($isConfirmModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 transition-opacity">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Confirmar Cita Médica</h3>
                    <button wire:click="closeConfirmModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="mb-4 bg-blue-50 p-4 rounded-md border border-blue-100">
                        <div class="flex items-center text-blue-800 font-medium mb-1">
                            <i class="fa-regular fa-calendar-check mr-2"></i> 
                            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($selectedTimeSlot['start'])->format('H:i') }}
                        </div>
                    </div>

                    <form wire:submit.prevent="registerAppointment">
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selecciona al Paciente</label>
                            <x-wire-select wire:model="selectedPatientId" placeholder="Buscar paciente..." :searchable="true">
                                @foreach($availablePatients as $patient)
                                    <x-wire-select.user-option 
                                        src="https://ui-avatars.com/api/?name={{ urlencode($patient->user->name) }}&color=1D4ED8&background=DBEAFE" 
                                        label="{{ $patient->user->name }} {{ $patient->user->last_name }}" 
                                        value="{{ $patient->id }}" 
                                        description="{{ $patient->user->id_number }} - {{ $patient->user->email }}" 
                                    />
                                @endforeach
                            </x-wire-select>
                            @error('selectedPatientId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <x-wire-button wire:click="closeConfirmModal" secondary>Cancelar</x-wire-button>
                            <x-wire-button type="submit" primary>Confirmar y Agendar</x-wire-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
