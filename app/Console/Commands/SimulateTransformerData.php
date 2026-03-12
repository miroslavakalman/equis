<?php

namespace App\Console\Commands;

use App\Models\Transformer;
use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\User;
use Illuminate\Console\Command;

class SimulateTransformerData extends Command
{
    protected $signature = 'simulate:transformer';
    protected $description = 'Генерирует тестовые данные для трансформаторов';

    public function handle()
    {
        $this->info('Симулятор запущен! Нажми Ctrl+C для остановки.');

        // Создаем тестового пользователя, если нет
        $user = User::firstOrCreate(
            ['email' => 'driver@test.com'],
            [
                'name' => 'Тестовый Водитель',
                'password' => bcrypt('password')
            ]
        );

        // Создаем тестовый трансформатор, если нет
        $transformer = Transformer::firstOrCreate(
            ['name' => 'ТП-7'],
            [
                'address' => 'ул. Ленина, 42',
                'capacity' => 500,
                'current_load' => 200,
                'temperature' => 40,
                'status' => 'normal'
            ]
        );

        // Создаем тестовые зарядки, если их мало
        $chargerCount = $transformer->chargers()->count();
        if ($chargerCount < 5) {
            for ($i = $chargerCount + 1; $i <= 5; $i++) {
                $transformer->chargers()->create([
                    'name' => "Зарядка {$i}",
                    'max_power' => 22,
                    'status' => rand(0, 1) ? 'free' : 'busy'
                ]);
            }
        }

        $this->info('Начальные данные созданы. Начинаю симуляцию...');

        // Бесконечный цикл обновления
        while (true) {
            $this->updateTransformerData($transformer);
            $this->updateChargerStatuses($transformer, $user);
            
            $this->info("Данные обновлены: " . now()->format('H:i:s'));
            sleep(5); // Обновление каждые 5 секунд
        }
    }

    private function updateTransformerData(Transformer $transformer)
    {
        $hour = now()->hour;
        
        // Пик в 19:00
        if ($hour == 19) {
            $load = rand(420, 490);
            $temp = 65 + rand(0, 10);
        } 
        // Ночью минимум
        elseif ($hour >= 0 && $hour <= 5) {
            $load = rand(100, 200);
            $temp = 35 + rand(0, 8);
        } 
        // Днем среднее
        else {
            $load = rand(200, 380);
            $temp = 45 + rand(0, 15);
        }

        // Определяем статус
        $status = 'normal';
        if ($load > 450) $status = 'overload';
        elseif ($load > 400) $status = 'warning';

        $transformer->update([
            'current_load' => $load,
            'temperature' => $temp,
            'status' => $status
        ]);
    }

    private function updateChargerStatuses(Transformer $transformer, User $user)
    {
        foreach ($transformer->chargers as $charger) {
            // 30% шанс изменить статус
            if (rand(1, 100) > 70) { 
                $newStatus = $charger->status === 'free' ? 'busy' : 'free';
                $charger->update(['status' => $newStatus]);
                
                // Если зарядка стала занятой - создаем сессию
                if ($newStatus === 'busy' && !$charger->activeSession()) {
                    $power = rand(7, 22);
                    ChargingSession::create([
                        'charger_id' => $charger->id,
                        'user_id' => $user->id,
                        'power' => $power,
                        'mode' => ['turbo', 'smart', 'eco'][rand(0, 2)],
                        'started_at' => now()->subMinutes(rand(10, 120)),
                        'ended_at' => null
                    ]);
                    
                    // Обновляем нагрузку трансформатора (добавляем мощность сессии)
                    $transformer->increment('current_load', $power);
                }
                
                // Если зарядка стала свободной - завершаем сессию
                if ($newStatus === 'free') {
                    $activeSession = $charger->activeSession();
                    if ($activeSession) {
                        // Вычитаем мощность из нагрузки
                        $transformer->decrement('current_load', $activeSession->power);
                        
                        $activeSession->update(['ended_at' => now()]);
                    }
                }
            }
        }
    }
}