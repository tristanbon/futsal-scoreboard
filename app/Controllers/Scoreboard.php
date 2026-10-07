<?php

namespace App\Controllers;

use App\Models\MatchModel;

class Scoreboard extends BaseController
{
    protected $matchModel;

    public function __construct()
    {
        $this->matchModel = new MatchModel();
    }

    public function index()
    {
        return view('scoreboard/index');
    }

    public function create()
    {
        return view('scoreboard/create');
    }

    public function store()
    {
        // "now" starts the match right away, "later" saves it as scheduled
        $mode        = $this->request->getPost('mode') === 'later' ? 'later' : 'now';
        $scheduledAt = null;

        if ($mode === 'later') {
            $scheduledAt = $this->parseDateTime($this->request->getPost('scheduled_at'));

            if ($scheduledAt === null) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please choose a valid date and time for the scheduled match.');
            }
        }

        $logoA = $this->saveLogo('logo_a');
        $logoB = $this->saveLogo('logo_b');

        // false = a file was uploaded but it is not a valid image
        if ($logoA === false || $logoB === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Logos must be PNG, JPG, WEBP or GIF images up to 2MB.');
        }

        // minutes per half (10, 15, 20 or custom), limited to 1-90
        $halfMinutes = min(90, max(1, (int) ($this->request->getPost('half_minutes') ?: 20)));

        $this->matchModel->insert([
            'team_a'    => $this->request->getPost('team_a'),
            'team_b'    => $this->request->getPost('team_b'),
            'logo_a'    => $logoA,
            'logo_b'    => $logoB,
            'half_minutes' => $halfMinutes,
            'time_left' => $halfMinutes * 60,
            'status'    => $mode === 'later' ? 'scheduled' : 'live',
            'scheduled_at' => $scheduledAt,
        ]);

        $id = $this->matchModel->getInsertID();

        if ($mode === 'later') {
            return redirect()->to('/scoreboard/history')
                ->with('success', 'Match scheduled for ' . date('M d, Y h:i A', strtotime($scheduledAt)) . '.');
        }

        return redirect()->to('/scoreboard/match/' . $id);
    }

    /**
     * List of all matches, newest first.
     */
    public function history()
    {
        $from = $this->validDate($this->request->getGet('from'));
        $to   = $this->validDate($this->request->getGet('to'));

        // only accept known statuses; anything else means "all"
        $status = $this->request->getGet('status');
        $status = in_array($status, ['scheduled', 'live', 'finished'], true) ? $status : null;

        // backwards range: swap it
        if ($from && $to && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        if ($from) {
            $this->matchModel->where('COALESCE(scheduled_at, created_at) >=', $from . ' 00:00:00');
        }

        if ($to) {
            $this->matchModel->where('COALESCE(scheduled_at, created_at) <=', $to . ' 23:59:59');
        }

        if ($status) {
            $this->matchModel->where('status', $status);
        }

        $matches = $this->matchModel->orderBy('id', 'DESC')->findAll();

        return view('scoreboard/history', [
            'matches' => $matches,
            'from'    => $from,
            'to'      => $to,
            'status'  => $status,
        ]);
    }

    /**
     * Converts a datetime-local value (Y-m-dTH:i) to Y-m-d H:i:s, or null if invalid.
     */
    private function parseDateTime($value): ?string
    {
        $value = (string) $value;
        $dt    = \DateTime::createFromFormat('Y-m-d\TH:i', $value);

        return ($dt && $dt->format('Y-m-d\TH:i') === $value) ? $dt->format('Y-m-d H:i:s') : null;
    }

    /**
     * Returns the value if it is a real Y-m-d date, otherwise null.
     */
    private function validDate($value): ?string
    {
        $value = (string) $value;
        $dt    = \DateTime::createFromFormat('Y-m-d', $value);

        return ($dt && $dt->format('Y-m-d') === $value) ? $value : null;
    }

    /**
     * Scheduled -> live, then opens the scoreboard.
     */
    public function start($id)
    {
        $match = $this->matchModel->find($id);

        if (!$match) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (($match['status'] ?? '') === 'scheduled') {
            $this->matchModel->update($id, ['status' => 'live']);
        }

        return redirect()->to('/scoreboard/match/' . (int) $id);
    }

    /**
     * Deletes a match that has not started yet. Live and finished matches are kept.
     */
    public function cancel($id)
    {
        $match = $this->matchModel->find($id);

        if (!$match || ($match['status'] ?? '') !== 'scheduled') {
            return redirect()->to('/scoreboard/history')
                ->with('error', 'Only scheduled matches can be cancelled.');
        }

        foreach (['logo_a', 'logo_b'] as $field) {
            if (!empty($match[$field]) && is_file(FCPATH . $match[$field])) {
                @unlink(FCPATH . $match[$field]);
            }
        }

        $this->matchModel->delete($id);

        return redirect()->to('/scoreboard/history')->with('success', 'Scheduled match cancelled.');
    }

    public function match($id)
    {
        $match = $this->matchModel->find($id);

        if (!$match) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // a scheduled match has to be started first
        if (($match['status'] ?? '') === 'scheduled') {
            return redirect()->to('/scoreboard/history')
                ->with('error', 'This match is scheduled. Press Start when it is time to play.');
        }

        return view('scoreboard/match', [
            'match' => $match
        ]);
    }

    /**
     * Saves live scoreboard changes (goals, fouls, time-outs, clock, period).
     * Called with fetch() from the match page.
     */
    public function update($id)
    {
        $match = $this->matchModel->find($id);

        if (!$match) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false]);
        }

        if (($match['status'] ?? '') === 'scheduled') {
            return $this->response->setStatusCode(409)->setJSON(['ok' => false]);
        }

        $data = [];

        foreach (['score_a', 'score_b', 'score_a_ht', 'score_b_ht', 'fouls_a', 'fouls_b', 'time_left'] as $field) {
            $value = $this->request->getPost($field);

            if ($value !== null) {
                $data[$field] = max(0, (int) $value);
            }
        }

        foreach (['timeout_a', 'timeout_b'] as $field) {
            $value = $this->request->getPost($field);

            if ($value !== null) {
                $data[$field] = min(1, max(0, (int) $value));
            }
        }

        // finishing a match saves the result; reopening puts it back to live
        $status = $this->request->getPost('status');

        if (in_array($status, ['live', 'finished'], true)) {
            $data['status']      = $status;
            $data['finished_at'] = $status === 'finished' ? date('Y-m-d H:i:s') : null;
        }

        $halfMinutes = $this->request->getPost('half_minutes');

        if ($halfMinutes !== null) {
            $data['half_minutes'] = min(90, max(1, (int) $halfMinutes));
        }

        $period = $this->request->getPost('period');

        if ($period !== null) {
            $data['period'] = min(2, max(1, (int) $period));
        }

        if ($data) {
            $this->matchModel->update($id, $data);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    /**
     * Moves an uploaded logo to public/uploads/logos.
     *
     * @return string|null|false  relative path, null if no file, false if invalid
     */
    private function saveLogo(string $field)
    {
        $file = $this->request->getFile($field);

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $allowed = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];

        if (
            !$file->isValid()
            || $file->hasMoved()
            || $file->getSizeByUnit('kb') > 2048
            || !in_array($file->getMimeType(), $allowed, true)
        ) {
            return false;
        }

        $dir = FCPATH . 'uploads/logos';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = $file->getRandomName();

        $file->move($dir, $name);

        return 'uploads/logos/' . $name;
    }
}
