<?php

/*
|--------------------------------------------------------------------------
| Helpers for the view
|--------------------------------------------------------------------------
*/

$logoA = !empty($match['logo_a']) ? base_url($match['logo_a']) : null;
$logoB = !empty($match['logo_b']) ? base_url($match['logo_b']) : null;

$initials = function ($name) {
    $name = trim((string) $name);

    return mb_strtoupper(mb_substr($name, 0, 2));
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

    <title>
        <?= esc($match['team_a']) ?>
        vs
        <?= esc($match['team_b']) ?>
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #070f20;
            color: #fff;
            font-family: 'Oswald', 'Arial Narrow', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 28px;
            padding: 24px 12px;
        }

        /* ------------------------------------------------------------
           SCOREBOARD
        ------------------------------------------------------------ */

        .board {
            width: min(1000px, 100%);
            border: 4px solid #6f9be8;
            border-radius: 16px;
            overflow: hidden;
            background: #0c2155;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .6);
            text-align: center;
        }

        /* top blue area: period + timer */

        .board-top {
            background: linear-gradient(180deg, #2b5fc2 0%, #1b448f 100%);
            padding: clamp(10px, 2vw, 22px) 12px clamp(8px, 1.6vw, 18px);
        }

        .period {
            display: inline-block;
            min-width: clamp(44px, 7vw, 70px);
            padding: 0 14px;
            border-radius: 8px;
            background: #0c2155;
            border: 2px solid #8fb2f0;
            color: #ffd84a;
            font-size: clamp(22px, 4vw, 40px);
            font-weight: 700;
            line-height: 1.3;
        }

        .timer {
            margin-top: clamp(2px, 1vw, 10px);
            font-size: clamp(64px, 17vw, 170px);
            font-weight: 700;
            line-height: 1.05;
            letter-spacing: 2px;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 4px 0 rgba(0, 0, 0, .35);
        }

        .timer.running {
            color: #fff;
        }

        .timer.stopped {
            color: #dbe6ff;
        }

        /* bottom dark area: logos, scores, fouls, time-outs */

        .board-bottom {
            display: grid;
            grid-template-columns: 1fr 1.5fr 1fr;
            align-items: center;
            gap: clamp(6px, 2vw, 24px);
            padding: clamp(14px, 3vw, 34px) clamp(10px, 2.5vw, 30px);
            background: radial-gradient(ellipse at center, #123070 0%, #0a1b46 100%);
        }

        .team {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: clamp(6px, 1.2vw, 14px);
            min-width: 0;
        }

        .logo {
            width: clamp(70px, 17vw, 190px);
            height: clamp(48px, 11.5vw, 128px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 6px;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, .5));
        }

        .logo .fallback {
            width: clamp(48px, 11.5vw, 128px);
            height: clamp(48px, 11.5vw, 128px);
            border-radius: 50%;
            background: #fff;
            color: #0c2155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(20px, 5vw, 54px);
            font-weight: 700;
        }

        .team-name {
            max-width: 100%;
            font-size: clamp(12px, 3.4vw, 44px);
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.1;
            overflow-wrap: break-word;
        }

        .middle {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: clamp(8px, 1.6vw, 18px);
        }

        .scores {
            display: flex;
            justify-content: center;
            gap: clamp(30px, 9vw, 110px);
            font-size: clamp(64px, 16vw, 170px);
            font-weight: 700;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .scores span {
            min-width: 1ch;
            text-shadow: 0 4px 0 rgba(0, 0, 0, .4);
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(10px, 2.4vw, 28px);
            font-size: clamp(16px, 3vw, 34px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .row b {
            min-width: 1ch;
            font-variant-numeric: tabular-nums;
        }

        .dot {
            width: clamp(12px, 2vw, 22px);
            height: clamp(12px, 2vw, 22px);
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #0a1b46;
            transition: background .2s, box-shadow .2s;
        }

        .dot.available {
            background: #2fe05a;
            box-shadow: 0 0 10px #2fe05a;
        }

        .dot.used {
            background: #3a4a6e;
            box-shadow: none;
        }

        /* ------------------------------------------------------------
           CONTROL PANEL (below the board)
        ------------------------------------------------------------ */

        .panel {
            width: min(1000px, 100%);
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .panel .box {
            background: #121c33;
            border: 1px solid #25355c;
            border-radius: 12px;
            padding: 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            align-content: start;
        }

        .panel .box h3 {
            grid-column: 1 / -1;
            margin: 0 0 2px;
            font-size: 18px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #9db4e6;
            text-align: center;
        }

        .panel .wide {
            grid-column: 1 / -1;
        }

        button {
            border: none;
            padding: 12px 10px;
            font-family: inherit;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: .5px;
            border-radius: 8px;
            cursor: pointer;
            color: #fff;
            background: #2b3a5e;
        }

        button:hover {
            filter: brightness(1.15);
        }

        button:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .set-time {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .set-time input {
            width: 100%;
            padding: 10px;
            font-size: 17px;
            font-weight: 700;
            text-align: center;
            color: #fff;
            background: #0b1426;
            border: 1px solid #25355c;
            border-radius: 8px;
        }

        .finish { background: #b91c1c; }

        .final-bar {
            width: min(1000px, 100%);
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            padding: 12px;
            background: #3b1d1d;
            border: 1px solid #b91c1c;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .final-bar[hidden] { display: none; }

        .final-bar a {
            color: #9db4e6;
        }

        .goal  { background: #198754; }
        .foul  { background: #c2410c; }
        .start { background: #0d6efd; }
        .pause { background: #e0a800; color: #111; }
        .timeout { background: #6d28d9; }
        .timeout.used { background: #3a4a6e; }

        @media (max-width: 700px) {

            .panel {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<!-- ============================================================
     SCOREBOARD
============================================================ -->

<div class="board">

    <div class="board-top">

        <div class="period" id="period">1</div>

        <div class="timer stopped" id="timer">00:00</div>

    </div>

    <div class="board-bottom">

        <!-- TEAM A -->

        <div class="team">

            <div class="logo">
                <?php if ($logoA): ?>
                    <img src="<?= esc($logoA, 'attr') ?>" alt="<?= esc($match['team_a'], 'attr') ?>">
                <?php else: ?>
                    <div class="fallback"><?= esc($initials($match['team_a'])) ?></div>
                <?php endif; ?>
            </div>

            <div class="team-name"><?= esc($match['team_a']) ?></div>

        </div>

        <!-- MIDDLE: score / fouls / time-outs -->

        <div class="middle">

            <div class="scores">
                <span id="scoreA">0</span>
                <span id="scoreB">0</span>
            </div>

            <div class="row">
                <b id="foulsA">0</b>
                <span>Fouls</span>
                <b id="foulsB">0</b>
            </div>

            <div class="row">
                <i class="dot available" id="dotA"></i>
                <span>Time-out</span>
                <i class="dot available" id="dotB"></i>
            </div>

        </div>

        <!-- TEAM B -->

        <div class="team">

            <div class="logo">
                <?php if ($logoB): ?>
                    <img src="<?= esc($logoB, 'attr') ?>" alt="<?= esc($match['team_b'], 'attr') ?>">
                <?php else: ?>
                    <div class="fallback"><?= esc($initials($match['team_b'])) ?></div>
                <?php endif; ?>
            </div>

            <div class="team-name"><?= esc($match['team_b']) ?></div>

        </div>

    </div>

</div>


<!-- ============================================================
     CONTROLS
============================================================ -->

<div class="final-bar" id="finalBar" hidden>
    <span>🏁 FULL TIME – RESULT SAVED</span>
    <button onclick="reopenMatch()">↩ REOPEN</button>
    <a href="<?= base_url('scoreboard/history') ?>">Match history</a>
</div>

<div class="panel">

    <div class="box">
        <h3><?= esc($match['team_a']) ?></h3>
        <button class="goal" onclick="changeStat('score_a', 1)">+ GOAL</button>
        <button onclick="changeStat('score_a', -1)">− GOAL</button>
        <button class="foul" onclick="changeStat('fouls_a', 1)">+ FOUL</button>
        <button onclick="changeStat('fouls_a', -1)">− FOUL</button>
        <button class="timeout wide" id="btnTimeoutA" onclick="toggleTimeout('a')">TIME-OUT</button>
    </div>

    <div class="box">
        <h3>Clock</h3>
        <button class="start" onclick="startTimer()">▶ START</button>
        <button class="pause" onclick="pauseTimer()">⏸ PAUSE</button>
        <div class="set-time">
            <input type="number" id="halfInput" min="1" max="90" placeholder="Min per half">
            <button onclick="setHalfLength()">⏱ SET TIME</button>
        </div>
        <button class="wide" id="btnNextHalf" onclick="nextHalf()">⏭ START 2ND HALF</button>
        <button class="wide finish" id="btnFinish" onclick="finishMatch()">🏁 FINISH &amp; SAVE RESULT</button>
    </div>

    <div class="box">
        <h3><?= esc($match['team_b']) ?></h3>
        <button class="goal" onclick="changeStat('score_b', 1)">+ GOAL</button>
        <button onclick="changeStat('score_b', -1)">− GOAL</button>
        <button class="foul" onclick="changeStat('fouls_b', 1)">+ FOUL</button>
        <button onclick="changeStat('fouls_b', -1)">− FOUL</button>
        <button class="timeout wide" id="btnTimeoutB" onclick="toggleTimeout('b')">TIME-OUT</button>
    </div>

</div>


<script>

// minutes per half, chosen when the match was created (can be changed with SET TIME)
let halfSeconds = <?= (int) (($match['half_minutes'] ?? 20) ?: 20) * 60 ?>;

const HISTORY_URL = "<?= base_url('scoreboard/history') ?>";

const UPDATE_URL = "<?= base_url('scoreboard/update/' . (int) $match['id']) ?>";

/*
|--------------------------------------------------------------------------
| State (loaded from the matches table)
|--------------------------------------------------------------------------
*/

const state = {
    score_a:   <?= (int) ($match['score_a'] ?? 0) ?>,
    score_b:   <?= (int) ($match['score_b'] ?? 0) ?>,
    score_a_ht: <?= isset($match['score_a_ht']) ? (int) $match['score_a_ht'] : 'null' ?>,
    score_b_ht: <?= isset($match['score_b_ht']) ? (int) $match['score_b_ht'] : 'null' ?>,
    status:    <?= json_encode(($match['status'] ?? 'live') === 'finished' ? 'finished' : 'live') ?>,
    fouls_a:   <?= (int) ($match['fouls_a'] ?? 0) ?>,
    fouls_b:   <?= (int) ($match['fouls_b'] ?? 0) ?>,
    timeout_a: <?= (int) ($match['timeout_a'] ?? 0) ?>,
    timeout_b: <?= (int) ($match['timeout_b'] ?? 0) ?>,
    time_left: <?= (int) ($match['time_left'] ?? 1200) ?>,
    half_minutes: <?= (int) (($match['half_minutes'] ?? 20) ?: 20) ?>,
    period:    Math.min(2, Math.max(1, <?= (int) ($match['period'] ?? 1) ?>))
};

let timerInterval = null;
let endAt = 0;
let lastSavedSecond = null;


/*
|--------------------------------------------------------------------------
| Save to database
|--------------------------------------------------------------------------
*/

function post(fields)
{
    const body = new URLSearchParams();

    fields.forEach(function (key) {
        body.append(key, state[key]);
    });

    return fetch(UPDATE_URL, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: body,
        keepalive: true
    });
}

function save(fields)
{
    post(fields).catch(function () {
        /* offline: the next action will try again */
    });
}


/*
|--------------------------------------------------------------------------
| Render
|--------------------------------------------------------------------------
*/

function pad(n)
{
    return String(n).padStart(2, '0');
}

function render()
{
    document.getElementById('scoreA').innerText = state.score_a;
    document.getElementById('scoreB').innerText = state.score_b;

    document.getElementById('foulsA').innerText = state.fouls_a;
    document.getElementById('foulsB').innerText = state.fouls_b;

    document.getElementById('period').innerText = state.period;

    document.getElementById('timer').innerText =
        pad(Math.floor(state.time_left / 60)) + ':' + pad(state.time_left % 60);

    document.getElementById('timer').className =
        'timer ' + (timerInterval ? 'running' : 'stopped');

    ['a', 'b'].forEach(function (t) {

        const used = state['timeout_' + t] === 1;

        document.getElementById('dot' + t.toUpperCase()).className =
            'dot ' + (used ? 'used' : 'available');

        const btn = document.getElementById('btnTimeout' + t.toUpperCase());

        btn.className = 'timeout wide' + (used ? ' used' : '');

        btn.innerText = used ? 'TIME-OUT USED (UNDO)' : 'TIME-OUT';
    });

    document.getElementById('halfInput').placeholder = 'Now ' + state.half_minutes + ' min';

    const finished = state.status === 'finished';

    document.querySelectorAll('.panel button, .panel input').forEach(function (el) {
        el.disabled = finished;
    });

    document.getElementById('finalBar').hidden = !finished;

    document.getElementById('btnNextHalf').disabled = finished || state.period >= 2;
}


/*
|--------------------------------------------------------------------------
| Goals and fouls
|--------------------------------------------------------------------------
*/

function changeStat(field, amount)
{
    state[field] = Math.max(0, state[field] + amount);

    render();

    save([field]);
}


/*
|--------------------------------------------------------------------------
| Time-outs (one per team, per half). Using one pauses the clock.
|--------------------------------------------------------------------------
*/

function toggleTimeout(team)
{
    const field = 'timeout_' + team;

    if (state[field] === 0) {
        state[field] = 1;
        pauseTimer();
    } else {
        state[field] = 0;
    }

    render();

    save([field]);
}


/*
|--------------------------------------------------------------------------
| Timer
|--------------------------------------------------------------------------
*/

function startTimer()
{
    if (timerInterval !== null || state.time_left <= 0 || state.status === 'finished') {
        return;
    }

    endAt = Date.now() + state.time_left * 1000;

    timerInterval = setInterval(tick, 250);

    render();
}

function tick()
{
    const left = Math.max(0, Math.ceil((endAt - Date.now()) / 1000));

    if (left !== state.time_left) {

        state.time_left = left;

        render();

        // save every 5 seconds while running
        if (left % 5 === 0 && left !== lastSavedSecond) {
            lastSavedSecond = left;
            save(['time_left']);
        }
    }

    if (left <= 0) {
        pauseTimer();
    }
}

function pauseTimer()
{
    if (timerInterval === null) {
        return;
    }

    clearInterval(timerInterval);

    timerInterval = null;

    render();

    save(['time_left']);
}


/*
|--------------------------------------------------------------------------
| Set time per half (e.g. 10, 15, 20). Sets the clock to the new length.
|--------------------------------------------------------------------------
*/

function setHalfLength()
{
    const input   = document.getElementById('halfInput');
    const minutes = parseInt(input.value, 10);

    if (!Number.isInteger(minutes) || minutes < 1 || minutes > 90) {
        alert('Enter minutes per half between 1 and 90.');
        return;
    }

    if (timerInterval !== null) {
        alert('Pause the clock first, then set the time.');
        return;
    }

    if (state.time_left !== halfSeconds
        && !confirm('Set ' + minutes + ' minutes? The current clock will be reset to ' + pad(minutes) + ':00.')) {
        return;
    }

    state.half_minutes = minutes;
    halfSeconds        = minutes * 60;
    state.time_left    = halfSeconds;

    input.value = '';

    render();

    save(['half_minutes', 'time_left']);
}


/*
|--------------------------------------------------------------------------
| 2nd half: clock back to the full half length, fouls and time-outs reset, score kept
|--------------------------------------------------------------------------
*/

function nextHalf()
{
    if (state.period >= 2) {
        return;
    }

    if (!confirm('Start the 2nd half? The clock resets to ' + pad(state.half_minutes) + ':00 and fouls and time-outs reset.')) {
        return;
    }

    pauseTimer();

    // remember the half-time score for the match history
    state.score_a_ht = state.score_a;
    state.score_b_ht = state.score_b;

    state.period    = 2;
    state.time_left = halfSeconds;
    state.fouls_a   = 0;
    state.fouls_b   = 0;
    state.timeout_a = 0;
    state.timeout_b = 0;

    render();

    save(['period', 'time_left', 'fouls_a', 'fouls_b', 'timeout_a', 'timeout_b', 'score_a_ht', 'score_b_ht']);
}


/*
|--------------------------------------------------------------------------
| Finish the match: saves the final result and opens the match history
|--------------------------------------------------------------------------
*/

function finishMatch()
{
    const msg = state.period < 2
        ? 'The 2nd half has not started. Finish the match now and save ' + state.score_a + ' - ' + state.score_b + ' as the result?'
        : 'Finish the match and save ' + state.score_a + ' - ' + state.score_b + ' as the final result?';

    if (!confirm(msg)) {
        return;
    }

    pauseTimer();

    state.status = 'finished';

    render();

    post(['score_a', 'score_b', 'fouls_a', 'fouls_b', 'time_left', 'status'])
        .then(function (res) {
            if (!res.ok) { throw new Error('save failed'); }
            window.location.href = HISTORY_URL;
        })
        .catch(function () {
            state.status = 'live';
            render();
            alert('Could not save the result. Check the connection and try again.');
        });
}

function reopenMatch()
{
    if (!confirm('Reopen this match so it can be edited again?')) {
        return;
    }

    state.status = 'live';

    render();

    save(['status']);
}


render();

</script>

</body>

</html>
