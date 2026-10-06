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
            'status'    => 'live'
        ]);

        $id = $this->matchModel->getInsertID();

        return redirect()->to('/scoreboard/match/' . $id);
    }

    /**
     * List of all matches, newest first.
     */
    public function history()
    {
        $matches = $this->matchModel->orderBy('id', 'DESC')->findAll();

        return view('scoreboard/history', [
            'matches' => $matches
        ]);
    }

    public function match($id)
    {
        $match = $this->matchModel->find($id);

        if (!$match) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
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
