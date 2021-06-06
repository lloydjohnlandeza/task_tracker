<?php

namespace App\Exports;
use Maatwebsite\Excel\Excel;
use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;
class TaskExportCsv implements FromCollection
{
    use Exportable;
      
    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'tasks.csv';
    
    /**
    * Optional Writer Type
    */
    private $writerType = Excel::CSV;
    
    /**
    * Optional headers
    */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Task::where('parent_id', 0)
          ->where('user_id', Auth::user()->id)
          ->join('orders', 'orders.id', '=', 'tasks.order_id')
          ->orderBy('orders.order' , 'asc')
          ->orderBy('tasks.updated_at' , 'desc')
          ->with(['deep_sub_tasks' => function ($q) {
            $q->join('orders', 'orders.id', '=', 'tasks.order_id');
            $q->orderBy('orders.order' , 'asc');
            $q->orderBy('tasks.updated_at' , 'desc');
            $q->select('tasks.*');
          }])
          ->select('tasks.*')
          ->get();
    }
}
