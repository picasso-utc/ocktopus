<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PicStatus;
use Filament\Widgets\Widget;

class PicStatusWidget extends Widget
{
    protected static ?int $sort = 1;
    
    protected static string $view = 'filament.admin.widgets.pic-status';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public bool $open;

    public function mount(): void
    {
        $this->open = PicStatus::current()->open;
    }

    public function toggle(): void
    {
        $status = PicStatus::current();
        $status->open = !$this->open;
        $status->save();

        $this->open = $status->open;
    }
}
