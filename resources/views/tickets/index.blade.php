<x-admin-layout title="Soporte | Simify" :breadcrumbs="[
    [
      'name' => 'Dashboard',
      'href' => route('dashboard'),
    ],
    [
      'name' => 'Soporte',
    ],
  ]">

  <x-slot name="action">
    <a href="{{ route('tickets.create') }}"
      class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
      Nuevo Ticket
    </a>
  </x-slot>

  <!-- Flowbite table -->
  <div class="relative overflow-x-auto shadow-sm sm:rounded-lg bg-white border border-gray-100">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 font-sans">
      <thead class="text-xs text-gray-600 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 tracking-wider">
        <tr>
          <th scope="col" class="px-6 py-4">ID</th>
          <th scope="col" class="px-6 py-4">USUARIO</th>
          <th scope="col" class="px-6 py-4">TÍTULO</th>
          <th scope="col" class="px-6 py-4">ESTADO</th>
          <th scope="col" class="px-6 py-4">FECHA</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($tickets as $ticket)
          <tr
            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            <td class="px-6 py-4 font-medium text-gray-500 whitespace-nowrap w-24">
              #{{ $ticket->id }}
            </td>
            <td class="px-6 py-4 text-gray-900 font-semibold w-64">
              {{ $ticket->user->name ?? 'Usuario' }}
            </td>
            <td class="px-6 py-4 text-gray-600">
              {{ $ticket->title }}
            </td>
            <td class="px-6 py-4 w-32">
              <span
                class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded shadow-sm">{{ $ticket->status }}</span>
            </td>
            <td class="px-6 py-4 w-48 text-gray-500">
              {{ $ticket->created_at->format('d/m/Y H:i') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
              No hay tickets de soporte registrados aún.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</x-admin-layout>