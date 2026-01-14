<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'default_values',
        'layout',
        'is_active',
        'is_system',
        'can_delete',
        'priority',
        'usage_count',
        'last_used_at',
        'version',
        'parent_id',
        'metadata',
        'description',
    ];

    protected $casts = [
        'variables' => 'array',
        'default_values' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'can_delete' => 'boolean',
        'usage_count' => 'integer',
        'priority' => 'integer',
        'version' => 'integer',
        'last_used_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_system' => false,
        'can_delete' => true,
        'priority' => 1,
        'usage_count' => 0,
        'version' => 1,
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(EmailTemplate::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(EmailTemplate::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%")
              ->orWhere('subject', 'like', "%{$search}%");
        });
    }

    // Methods
    public function incrementUsage()
    {
        $this->update([
            'usage_count' => $this->usage_count + 1,
            'last_used_at' => now(),
        ]);
    }

    public function render($data = [])
    {
        $html = $this->html_content;
        $text = $this->text_content;

        // Merge default values with provided data
        $mergedData = array_merge(
            $this->default_values ?? [],
            $data
        );

        // Add year if not provided
        if (!isset($mergedData['year'])) {
            $mergedData['year'] = date('Y');
        }

        // Replace variables in content
        foreach ($mergedData as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $html = str_replace($placeholder, $value, $html);

            if ($text) {
                $text = str_replace($placeholder, $value, $text);
            }
        }

        return [
            'html' => $html,
            'text' => $text,
            'subject' => $this->replaceVariables($this->subject, $mergedData),
        ];
    }

    private function replaceVariables($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    public function getVariablesListAttribute()
    {
        return $this->variables ?? [];
    }

    public function createVersion($data)
    {
        $version = $this->replicate();
        $version->parent_id = $this->id;
        $version->version = $this->version + 1;
        $version->is_active = false;

        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $version->$key = $value;
            }
        }

        $version->save();

        return $version;
    }

    public function activate()
    {
        // Deactivate other versions
        EmailTemplate::where('slug', $this->slug)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        $this->update(['is_active' => true]);
    }
}
