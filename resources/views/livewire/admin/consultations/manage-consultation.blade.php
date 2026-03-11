<div>
    <form wire:submit="saveConsultation">
        {{-- Header Section --}}
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $appointment->patient->user->name }} {{ $appointment->patient->user->last_name }}</h2>
                <p class="text-sm text-gray-500 mt-1">DNI: {{ $appointment->patient->user->document_number ?? 'No registrado' }}</p>
            </div>
            <div class="flex space-x-3">
                <x-wire-button wire:click="$set('showHistoryModal', true)" outline gray icon="folder-open" type="button">
                    Ver Historia
                </x-wire-button>
                <x-wire-button wire:click="openPreviousConsultations" outline gray icon="clock" type="button">
                    Consultas Anteriores
                </x-wire-button>
            </div>
        </div>

        {{-- Tabs Card --}}
        <x-wire-card>
            <x-tabs :active="$activeTab">
                <x-slot name="header">
                    <x-tabs-link tab="consulta">
                        <i class="fa-solid fa-notes-medical me-2"></i>
                        Consulta
                    </x-tabs-link>
                    <x-tabs-link tab="receta">
                        <i class="fa-solid fa-prescription-bottle-medical me-2"></i>
                        Receta
                    </x-tabs-link>
                </x-slot>

                {{-- Tab 1: Consulta --}}
                <x-tab-content tab="consulta">
                    <div class="space-y-6">
                        <x-wire-textarea 
                            wire:model="diagnosis" 
                            label="Diagnóstico" 
                            placeholder="Describa el diagnóstico del paciente aquí..." 
                            rows="4" 
                        />
                        
                        <x-wire-textarea 
                            wire:model="treatment" 
                            label="Tratamiento" 
                            placeholder="Describa el tratamiento recomendado aquí..." 
                            rows="4" 
                        />
                        
                        <x-wire-textarea 
                            wire:model="notes" 
                            label="Notas" 
                            placeholder="Agregue notas adicionales sobre la consulta..." 
                            rows="4" 
                        />
                    </div>
                </x-tab-content>

                {{-- Tab 2: Receta --}}
                <x-tab-content tab="receta">
                    <div class="space-y-4">
                        @foreach ($prescriptionItems as $index => $item)
                        <div class="flex items-start gap-4 p-4 border rounded-lg bg-gray-50" wire:key="prescription-{{ $index }}">
                            <div class="flex-1">
                                <x-wire-input 
                                    wire:model="prescriptionItems.{{ $index }}.medication" 
                                    label="Medicamento" 
                                    placeholder="Ej: Amoxicilina 500mg" 
                                />
                            </div>
                            <div class="w-1/4">
                                <x-wire-input 
                                    wire:model="prescriptionItems.{{ $index }}.dose" 
                                    label="Dosis" 
                                    placeholder="Ej: 1 cada 8 horas" 
                                />
                            </div>
                            <div class="flex-1">
                                <x-wire-input 
                                    wire:model="prescriptionItems.{{ $index }}.frequency_duration" 
                                    label="Frecuencia / Duración" 
                                    placeholder="Ej: cada 8 horas por 7 días" 
                                />
                            </div>
                            <div class="pt-7">
                                <x-wire-button color="red" icon="trash" wire:click="removeMedication({{ $index }})" type="button">
                                </x-wire-button>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-4">
                            <x-wire-button outline gray icon="plus" wire:click="addMedication" type="button">
                                Añadir Medicamento
                            </x-wire-button>
                        </div>
                    </div>
                </x-tab-content>
            </x-tabs>

            {{-- Footer Save Button --}}
            <div class="mt-8 flex justify-end border-t border-gray-100 pt-4">
                <x-wire-button type="submit" primary class="w-full sm:w-auto">
                    <i class="fa-solid fa-lock me-2"></i> Guardar Consulta
                </x-wire-button>
            </div>
        </x-wire-card>
    </form>

    {{-- Modal Historia Médica --}}
    @if($showHistoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 transition-opacity">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Historia médica del paciente</h3>
                    <button wire:click="$set('showHistoryModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tipo de sangre:</p>
                            <p class="font-bold text-gray-900">{{ $appointment->patient->bloodType->name ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Alergias:</p>
                            <p class="font-bold text-gray-900">{{ $appointment->patient->allergies ?? 'No registradas' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Enfermedades crónicas:</p>
                            <p class="font-bold text-gray-900">{{ $appointment->patient->chronic_conditions ?? 'No registradas' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Antecedentes quirúrgicos:</p>
                            <p class="font-bold text-gray-900">{{ $appointment->patient->surgical_history ?? 'No registradas' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.patients.edit', $appointment->patient) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center gap-1">
                            Ver / Editar Historia Médica <i class="fa-solid fa-arrow-up-right-from-square text-xs ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Consultas Anteriores --}}
    @if($showPreviousConsultationsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 transition-opacity">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Consultas Anteriores</h3>
                    <button wire:click="$set('showPreviousConsultationsModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto bg-gray-50">
                    @forelse($previousConsultations as $prevApp)
                        <div class="bg-white p-5 rounded-lg border border-blue-100 shadow-sm mb-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="flex items-center text-blue-700 font-semibold">
                                        <i class="fa-regular fa-calendar-check mr-2"></i>
                                        {{ \Carbon\Carbon::parse($prevApp->date)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($prevApp->start_time)->format('H:i') }}
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Atendido por: Dr(a). {{ $prevApp->doctor->user->name }} {{ $prevApp->doctor->user->last_name }}</p>
                                </div>
                                <x-wire-button outline blue sm href="{{ route('admin.appointments.consultation', $prevApp) }}" target="_blank">
                                    Consultar Detalle
                                </x-wire-button>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded text-sm space-y-3 mt-3">
                                <div>
                                    <span class="font-bold text-gray-800">Diagnóstico:</span> 
                                    <span class="text-gray-700">{{ $prevApp->consultation->diagnosis ?? 'No registrado' }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800">Tratamiento:</span> 
                                    <span class="text-gray-700">{{ $prevApp->consultation->treatment ?? 'No registrado' }}</span>
                                </div>
                                @if($prevApp->consultation->notes)
                                <div>
                                    <span class="font-bold text-gray-800">Notas:</span> 
                                    <span class="text-gray-700">{{ $prevApp->consultation->notes }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fa-regular fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p>El paciente no tiene consultas previas registradas en el sistema.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
