<?php

namespace App\Exports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SelectedCandidatesExport implements FromCollection, WithHeadings
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return collect($this->records)->map(function ($candidate) {
            return [
                'ID' => $candidate->candidate_id,
                'Name' => $candidate->name,
                'Email' => $candidate->email,
                'Phone' => $candidate->phone,
                'Applied For' => optional($candidate->job)->title,
                'Score' => $candidate->score ?? 'N/A',
                'Applied On' => $candidate->created_at->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone', 'Applied For', 'Score', 'Applied On'];
    }
}
