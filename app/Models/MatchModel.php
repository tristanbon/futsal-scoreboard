<?php

namespace App\Models;

use CodeIgniter\Model;

class MatchModel extends Model
{
    protected $table = 'matches';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'team_a',
        'team_b',
        'logo_a',
        'logo_b',
        'score_a',
        'score_b',
        'score_a_ht',
        'score_b_ht',
        'fouls_a',
        'fouls_b',
        'timeout_a',
        'timeout_b',
        'time_left',
        'half_minutes',
        'period',
        'match_time',
        'status',
        'finished_at',
        'scheduled_at'
    ];

    protected $useTimestamps = true;
}
