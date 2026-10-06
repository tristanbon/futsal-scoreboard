<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Futsal Scoreboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-5">

    <div class="text-center">

        <h1>Futsal Match Management</h1>

        <a
            href="<?= base_url('scoreboard/create') ?>"
            class="btn btn-primary"
        >
            Create Match
        </a>

        <a
            href="<?= base_url('scoreboard/history') ?>"
            class="btn btn-outline-secondary"
        >
            Match History
        </a>

    </div>

</div>

</body>

</html>