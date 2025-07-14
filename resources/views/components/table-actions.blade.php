<div class="d-flex gap-1 justify-content-center">
    <a href="{{ $editUrl }}" class="btn btn-sm btn-warning">
        <i class="bi bi-pencil-square"></i>
    </a>
    <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display:inline;">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger" type="submit">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
