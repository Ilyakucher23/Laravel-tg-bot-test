<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Quiz Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Quiz Progress</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="container">
            <h1>User Quiz Progress</h1>

            @if ($quizProgress->isEmpty())
                <p>No quiz progress available.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Answers</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quizProgress as $progress)
                            <tr>
                                <td>{{ $progress->id }}</td>
                                <td>{{ $progress->user_id }}</td>
                                <td>
                                    @foreach (json_decode($progress->answers) as $answer)
                                        {{ $answer }}
                                    @endforeach
                                    {{-- {{ json_encode($progress->answers) }} --}}
                                </td>
                                <td>{{ $progress->created_at }}</td>
                                <td>{{ $progress->updated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>

</html>
