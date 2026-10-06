<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Futsal Match</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <h2 class="mb-4">
                Create Futsal Match
            </h2>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form
                action="<?= base_url('scoreboard/store') ?>"
                method="post"
                enctype="multipart/form-data"
            >

                <div class="mb-3">

                    <label class="form-label">
                        Team A
                    </label>

                    <input
                        type="text"
                        name="team_a"
                        class="form-control"
                        placeholder="Enter Team A"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Team A Logo
                    </label>

                    <input
                        type="file"
                        name="logo_a"
                        class="form-control"
                        accept="image/png,image/jpeg,image/webp,image/gif"
                    >

                    <div class="form-text">
                        PNG, JPG, WEBP or GIF, up to 2MB (optional)
                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Team B
                    </label>

                    <input
                        type="text"
                        name="team_b"
                        class="form-control"
                        placeholder="Enter Team B"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Team B Logo
                    </label>

                    <input
                        type="file"
                        name="logo_b"
                        class="form-control"
                        accept="image/png,image/jpeg,image/webp,image/gif"
                    >

                    <div class="form-text">
                        PNG, JPG, WEBP or GIF, up to 2MB (optional)
                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Time per Half
                    </label>

                    <select
                        id="halfPreset"
                        class="form-select"
                    >
                        <option value="10">10 minutes</option>
                        <option value="15">15 minutes</option>
                        <option value="20" selected>20 minutes</option>
                        <option value="custom">Custom...</option>
                    </select>

                    <input
                        type="number"
                        id="halfCustom"
                        class="form-control mt-2 d-none"
                        min="1"
                        max="90"
                        placeholder="Minutes per half (1-90)"
                    >

                    <input
                        type="hidden"
                        name="half_minutes"
                        id="halfMinutes"
                        value="20"
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-success"
                >
                    Start Match
                </button>

                <a
                    href="<?= base_url('scoreboard') ?>"
                    class="btn btn-secondary"
                >
                    Back
                </a>

            </form>

        </div>

    </div>

</div>

<script>

const preset = document.getElementById('halfPreset');
const custom = document.getElementById('halfCustom');
const hidden = document.getElementById('halfMinutes');

function syncHalf()
{
    const isCustom = preset.value === 'custom';

    custom.classList.toggle('d-none', !isCustom);
    custom.required = isCustom;

    hidden.value = isCustom ? custom.value : preset.value;
}

preset.addEventListener('change', syncHalf);
custom.addEventListener('input', syncHalf);

syncHalf();

</script>

</body>

</html>