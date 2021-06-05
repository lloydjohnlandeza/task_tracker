<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Carbon;

class ForceDeleteTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:force_delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete 30 days old task ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $task = Task::withTrashed()->where('deleted_at', '<=', Carbon::now()->subDays(30)->toDateTimeString())->get();
        $task->forceDelete();
        $this->info('30 days old task had been deleted');
    }
}
