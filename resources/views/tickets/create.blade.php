<x-admin-layout title="Nuevo Ticket | Simify" :breadcrumbs="[
    [
      'name' => 'Dashboard',
      'href' => route('dashboard'),
    ],
    [
      'name' => 'Soporte',
      'href' => route('tickets.index'),
    ],
    [
      'name' => 'Nuevo Ticket',
    ],
  ]">

  <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 max-w-full">
    <h2 class="text-xl font-semibold text-gray-900 mb-1">Reportar un problema</h2>
    <p class="text-gray-500 text-sm mb-6">Describe tu problema o duda y nuestro equipo de soporte se pondrá en contacto
      contigo.</p>

    <form action="{{ route('tickets.store') }}" method="POST">
      @csrf

      <div class="mb-6">
        <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Título del problema</label>
        <input type="text" id="title" name="title"
          class="bg-white border border-blue-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
          required>
      </div>

      <div class="mb-6">
        <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Descripción detallada</label>
        <textarea id="description" name="description" rows="5"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
          required></textarea>
      </div>

      <div class="flex justify-end gap-3 mt-8">
        <a href="{{ route('tickets.index') }}"
          class="text-gray-700 bg-white border border-gray-200 focus:outline-none hover:bg-gray-50 hover:text-gray-900 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors shadow-sm">Cancelar</a>
        <button type="submit"
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-2.5 focus:outline-none transition-colors shadow-sm">Enviar
          Ticket</button>
      </div>
    </form>
  </div>

</x-admin-layout>