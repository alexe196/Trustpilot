<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <a class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </a>
            </div>

            <div class="bg-white  p-4">
                @if(!empty($link))
                    <div class="text-center">
                        <a class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900"
                            href="{{ asset('storage/csv/reviews.csv') }}?t={{ \Illuminate\Support\Carbon::now()->timestamp }}">
                            Завантажити CSV
                        </a>
                    </div>
                @else

                <form action="{{route('dashboard')}}" method="POST" class="max-w-sm mx-auto">
                    @csrf
                    <input type="hidden" value="1" name="csv">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Експортувати CSV</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
