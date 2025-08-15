<?php

// app/Models/ResumePrompt.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumePrompt extends Model
{
    protected $fillable = ['job_id', 'title', 'prompt', 'is_active', 'task_file_path',
                                'task_file_name','task_file_mime','task_file_size', 'task_link',];

     protected $casts = [
        'is_active' => 'boolean',
        'task_file_path'  => 'array',   // will be array when valid JSON
        'task_file_name'  => 'array',
        'task_file_mime'  => 'array',
        'task_file_size'  => 'array',
      
    ];
     # ---------- Convenience helpers ----------

      /** Normalize any stored value to an array (JSON array, JSON string, CSV, single string, or null). */
    private function normalizeToArray($value): array
    {
        if (empty($value)) return [];

        // If cast already gave us an array, use it.
        if (is_array($value)) return array_values($value);

        // Try JSON decode
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // JSON string or JSON array -> normalize to array of strings
                return array_values(is_array($decoded) ? $decoded : [$decoded]);
            }
            // Try pipe or comma CSV
            if (str_contains($value, '|')) return array_values(array_filter(explode('|', $value), 'strlen'));
            if (str_contains($value, ',')) return array_values(array_filter(explode(',', $value), 'strlen'));

            // Fallback: single value string
            return [$value];
        }

        return [];
    }
    /**
     * Return normalized file items for display.
     * Each item: ['name' => string, 'url' => string, 'mime' => ?string, 'size' => ?int]
     */
    /** Build display items safely, even with legacy/partial data. */
    public function fileItems(): array
    {
        $paths = $this->normalizeToArray($this->task_file_path);
        $names = $this->normalizeToArray($this->task_file_name);
        $mimes = $this->normalizeToArray($this->task_file_mime);
        $sizes = $this->normalizeToArray($this->task_file_size);

        // Use the longest length among the arrays
        $count = max(count($paths), count($names), count($mimes), count($sizes));
        if ($count === 0) return [];

        // Pad arrays so indexes exist
        $paths = array_pad($paths, $count, null);
        $names = array_pad($names, $count, null);
        $mimes = array_pad($mimes, $count, null);
        $sizes = array_pad($sizes, $count, null);

        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $p = $paths[$i] ?? '';
            // Normalize slashes for URLs (Windows storage paths)
            $p = str_replace('\\', '/', (string) $p);

            $items[] = [
                'name' => $names[$i] ?: basename($p),
                'url'  => $p ? asset('storage/' . ltrim($p, '/')) : '#',
                'mime' => $mimes[$i] ?: null,
                'size' => is_numeric($sizes[$i] ?? null) ? (int) $sizes[$i] : null,
            ];
        }
        // Filter out items that somehow have no path nor name
        return array_values(array_filter($items, fn($it) => $it['url'] !== '#' || $it['name']));
    }
      public function filesCount(): int
    {
        return count($this->fileItems());
    }
}
