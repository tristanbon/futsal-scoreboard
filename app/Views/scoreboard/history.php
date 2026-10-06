<?php

$logo = function ($path) {
    return !empty($path) ? base_url($path) : null;
};

$initials = function ($name) {
    return mb_strtoupper(mb_substr(trim((string) $name), 0, 2));
};

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Match History</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        .team-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 50%;
        }

        .team-fallback {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dee2e6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .score {
            font-size: 1.4rem;
            font-weight: 700;
            white-space: nowrap;
        }
    </style>

</head>

<body>

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <h2 class="mb-0">Match History</h2>

        <div>
            <a href="<?= base_url('scoreboard/create') ?>" class="btn btn-primary">
                + New Match
            </a>

            <a href="<?= base_url('scoreboard') ?>" class="btn btn-secondary">
                Back
            </a>
        </div>

    </div>

    <?php if (empty($matches)): ?>

        <div class="alert alert-info">
            No matches yet. Create your first match to see it here.
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Team A</th>
                        <th class="text-center">Score</th>
                        <th>Team B</th>
                        <th class="text-center">Half-time</th>
                        <th class="text-center">Per half</th>
                        <th class="text-center">Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($matches as $m): ?>

                    <?php
                        $sa = (int) ($m['score_a'] ?? 0);
                        $sb = (int) ($m['score_b'] ?? 0);

                        $finished = ($m['status'] ?? 'live') === 'finished';

                        $hasHt = isset($m['score_a_ht'], $m['score_b_ht']);

                        $logoA = $logo($m['logo_a'] ?? null);
                        $logoB = $logo($m['logo_b'] ?? null);
                    ?>

                    <tr>

                        <td class="text-nowrap">
                            <?= !empty($m['created_at']) ? esc(date('M d, Y h:i A', strtotime($m['created_at']))) : '-' ?>
                        </td>

                        <td class="text-end">
                            <span class="<?= $finished && $sa > $sb ? 'fw-bold' : '' ?>">
                                <?= esc($m['team_a']) ?>
                            </span>

                            <?php if ($logoA): ?>
                                <img src="<?= esc($logoA, 'attr') ?>" class="team-logo ms-2" alt="">
                            <?php else: ?>
                                <span class="team-fallback ms-2"><?= esc($initials($m['team_a'])) ?></span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center score">
                            <?= $sa ?> - <?= $sb ?>
                        </td>

                        <td>
                            <?php if ($logoB): ?>
                                <img src="<?= esc($logoB, 'attr') ?>" class="team-logo me-2" alt="">
                            <?php else: ?>
                                <span class="team-fallback me-2"><?= esc($initials($m['team_b'])) ?></span>
                            <?php endif; ?>

                            <span class="<?= $finished && $sb > $sa ? 'fw-bold' : '' ?>">
                                <?= esc($m['team_b']) ?>
                            </span>
                        </td>

                        <td class="text-center">
                            <?= $hasHt ? (int) $m['score_a_ht'] . ' - ' . (int) $m['score_b_ht'] : '-' ?>
                        </td>

                        <td class="text-center">
                            <?= (int) ($m['half_minutes'] ?? 20) ?> min
                        </td>

                        <td class="text-center">
                            <?php if ($finished): ?>
                                <span class="badge text-bg-success">
                                    <?= $sa === $sb ? 'Draw' : 'Finished' ?>
                                </span>
                            <?php else: ?>
                                <span class="badge text-bg-warning">Live</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-end">
                            <a
                                href="<?= base_url('scoreboard/match/' . (int) $m['id']) ?>"
                                class="btn btn-sm <?= $finished ? 'btn-outline-primary' : 'btn-warning' ?>"
                            >
                                <?= $finished ? 'View' : 'Resume' ?>
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

</body>

</html>
