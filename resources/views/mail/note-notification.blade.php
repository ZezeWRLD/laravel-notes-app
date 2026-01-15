<!DOCTYPE html>
<html>
<head>
    <title>Note Notification</title>
</head>
<body>
    <p>
        @if($action === 'created')
            Your note "{{ $note->title }}" has been created.
        @elseif($action === 'updated')
            Your note "{{ $note->title }}" has been updated.
        @elseif($action === 'deleted')
            Your note "{{ $note->title }}" has been deleted.
        @endif
    </p>

    @if($action !== 'deleted')
        <p>
            <a href="{{ url('/notes/' . $note->id) }}">
                View your note
            </a>
        </p>
    @endif
</body>
</html>
