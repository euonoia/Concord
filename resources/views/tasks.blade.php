<h1>My Tasks</h1>

<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    <input type="text" name="title" placeholder="New Task..." required>
    <button type="submit">Add</button>
</form>

<hr>

<ul>
    @foreach($tasks as $task)
        <li>
            {{ $task->title }}
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </li>
    @endforeach
</ul>