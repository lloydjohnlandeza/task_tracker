
<tr>
  <td>{{ $task->task }}</td>
  <td style="background-color: {{isset($status_color[$task->status]) ? $status_color[$task->status] : $status_color['custom']}}">{{ ucfirst($task->status) }}</td>
</tr>
@if (count($task->deep_sub_tasks) > 0)
    @foreach($task->deep_sub_tasks as $subtask)
      @include('exports.task_list', ['task' => $subtask])
    @endforeach
@endif