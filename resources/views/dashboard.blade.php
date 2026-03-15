<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Эквис - Дашборд</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Навигация (можно потом вынести) -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold">Эквис</h1>
                    </div>
                   
                </div>
            </div>
        </nav>

        <!-- Основной контент -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold mb-6">Диспетчерский щит</h2>
                
                <!-- Карточки с метриками -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 uppercase">Всего зарядок</div>
                        <div class="text-3xl font-bold">{{ $totalChargers }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 uppercase">Активных зарядок</div>
                        <div class="text-3xl font-bold">{{ $activeChargers }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 uppercase">Активных сессий</div>
                        <div class="text-3xl font-bold">{{ $activeSessions }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 uppercase">Трансформаторов</div>
                        <div class="text-3xl font-bold">{{ $transformers->count() }}</div>
                    </div>
                </div>

                <!-- Список трансформаторов -->
                <h3 class="text-xl font-semibold mb-4">Трансформаторы</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                    @foreach($transformers as $transformer)
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-start">
                                <h4 class="text-lg font-bold">{{ $transformer->name }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($transformer->statusColor() == 'red') bg-red-100 text-red-800
                                    @elseif($transformer->statusColor() == 'yellow') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $transformer->status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mb-3">{{ $transformer->address }}</p>
                            
                            <!-- Прогресс-бар загрузки -->
                            <div class="mb-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Загрузка</span>
                                    <span class="font-semibold">{{ $transformer->loadPercentage() }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full 
                                        @if($transformer->statusColor() == 'red') bg-red-600
                                        @elseif($transformer->statusColor() == 'yellow') bg-yellow-500
                                        @else bg-green-600
                                        @endif"
                                        style="width: {{ $transformer->loadPercentage() }}%">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2 text-sm mt-3">
                                <div>
                                    <span class="text-gray-500">Мощность:</span>
                                    <span class="font-medium">{{ $transformer->current_load }}/{{ $transformer->capacity }} кВт</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Температура:</span>
                                    <span class="font-medium">{{ $transformer->temperature }}°C</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Активные сессии -->
                <h3 class="text-xl font-semibold mb-4">Активные сессии</h3>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Трансформатор</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Зарядка</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Пользователь</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Мощность</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Режим</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Начало</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($sessions as $session)
                                <tr>
                                    <td class="px-6 py-4">{{ $session->charger->transformer->name ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $session->charger->name ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $session->user->name ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $session->power }} кВт</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($session->mode == 'turbo') bg-purple-100 text-purple-800
                                            @elseif($session->mode == 'smart') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ $session->mode }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $session->started_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Нет активных сессий
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>