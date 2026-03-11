<x-admin-layout title="Horario de {{ $doctor->user->name }} | Simify" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Doctores',
        'href' => route('admin.doctors.index'),
    ],
    [
        'name' => 'Gestor de horarios',
    ],
]">

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Gestor de horarios: {{ $doctor->user->name }} {{ $doctor->user->last_name }}</h2>
            <x-wire-button onclick="document.getElementById('schedule-form').submit()" primary>Guadar horario</x-wire-button>
        </div>

        @php
            $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            $hours = [
                '08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00',
                '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00', '19:00:00', '20:00:00'
            ];
        @endphp

        <form id="schedule-form" action="{{ route('admin.doctors.schedule.store', $doctor) }}" method="POST">
            @csrf
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium text-gray-500">DÍA/HORA</th>
                            @foreach ($days as $day)
                                <th scope="col" class="px-6 py-3 font-medium text-gray-500 text-center">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hours as $hour)
                            @php
                                $hourCarbon = \Carbon\Carbon::parse($hour);
                            @endphp
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $hourCarbon->format('H:i') }}
                                </td>
                                
                                @foreach ($days as $day)
                                    <td class="px-6 py-4 border-l border-gray-100">
                                        <div class="flex flex-col space-y-3 justify-center items-center">
                                            @php
                                                // Bloque 1: XX:00:00 - XX:30:00
                                                $start1 = clone $hourCarbon;
                                                $end1 = (clone $hourCarbon)->addMinutes(30);
                                                $val1 = $start1->format('H:i:s') . '-' . $end1->format('H:i:s');
                                                $isChecked1 = isset($schedules[$day][$val1]);

                                                // Bloque 2: XX:30:00 - (XX+1):00:00
                                                $start2 = $end1;
                                                $end2 = (clone $hourCarbon)->addHour();
                                                $val2 = $start2->format('H:i:s') . '-' . $end2->format('H:i:s');
                                                $isChecked2 = isset($schedules[$day][$val2]);
                                            @endphp

                                            <label class="flex items-center space-x-2 cursor-pointer text-gray-700 hover:text-blue-600 transition-colors">
                                                <input type="checkbox" name="schedules[{{ $day }}][]" value="{{ $val1 }}" 
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    {{ $isChecked1 ? 'checked' : '' }}>
                                                <span>{{ $start1->format('H:i') }} - {{ $end1->format('H:i') }}</span>
                                            </label>

                                            <label class="flex items-center space-x-2 cursor-pointer text-gray-700 hover:text-blue-600 transition-colors">
                                                <input type="checkbox" name="schedules[{{ $day }}][]" value="{{ $val2 }}" 
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    {{ $isChecked2 ? 'checked' : '' }}>
                                                <span>{{ $start2->format('H:i') }} - {{ $end2->format('H:i') }}</span>
                                            </label>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <x-wire-button onclick="document.getElementById('schedule-form').submit()" primary lg>Guardar horario</x-wire-button>
            </div>
        </form>
    </div>

</x-admin-layout>
