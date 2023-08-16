<?php

namespace Laravel\Pulse\Livewire;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Contracts\ShouldNotReportUsage;
use Laravel\Pulse\Contracts\Storage;
use Laravel\Pulse\Contracts\SupportsSlowQueries;
use Laravel\Pulse\Livewire\Concerns\HasPeriod;
use Livewire\Component;
use RuntimeException;

class SlowQueries extends Component implements ShouldNotReportUsage
{
    use HasPeriod;

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        [$slowQueries, $time, $runAt] = $this->slowQueries();

        $this->dispatch('slow-queries:dataLoaded');

        return view('pulse::livewire.slow-queries', [
            'time' => $time,
            'runAt' => $runAt,
            'slowQueries' => $slowQueries,
        ]);
    }

    /**
     * Render the placeholder.
     */
    public function placeholder(): Renderable
    {
        return view('pulse::components.placeholder', ['class' => 'col-span-3']);
    }

    /**
     * The slow queries.
     */
    protected function slowQueries(): Collection
    {
        return Cache::remember("illuminate:pulse:slow-queries:{$this->period}", $this->periodCacheDuration(), function () use ($storage) {
            $now = new CarbonImmutable;

            $start = hrtime(true);

            if (app()->bound(SlowQueriesThing::class)) {
                $query = app(SlowQueriesThing::class);
            } else {
                match (config('pulse.'))
            }

            $slowQueries = $storage->slowQueries($this->periodAsInterval());

            $time = (int) ((hrtime(true) - $start) / 1000000);

            return [$slowQueries, $time, $now->toDateTimeString()];
        });
    }
}