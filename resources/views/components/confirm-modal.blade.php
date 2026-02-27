@props([
    'id' => 'confirmModal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Delete',
    'confirmClass' => 'btn-danger',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="{{ $id }}-message">{{ $message }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="{{ $id }}-form" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn {{ $confirmClass }}">{{ $confirmText }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('{{ $id }}');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const trigger = event.relatedTarget;
            if (trigger) {
                const action = trigger.getAttribute('data-action');
                const message = trigger.getAttribute('data-message');
                const method = trigger.getAttribute('data-method') || 'DELETE';

                if (action) {
                    document.getElementById('{{ $id }}-form').action = action;
                }
                if (message) {
                    document.getElementById('{{ $id }}-message').textContent = message;
                }

                // Update the method field
                const methodField = document.querySelector('#{{ $id }}-form input[name="_method"]');
                if (methodField) {
                    methodField.value = method;
                }
            }
        });
    }
});
</script>
