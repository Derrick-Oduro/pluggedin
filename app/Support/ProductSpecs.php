<?php

namespace App\Support;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Reads the spec templates in config/product_specs.php and works out which
 * template a category uses, which fields are required, and how to display them.
 */
class ProductSpecs
{
    public static function templates(): array
    {
        return config('product_specs.templates', []);
    }

    public static function conditions(): array
    {
        return config('product_specs.conditions', []);
    }

    public static function conditionLabel(?string $condition): ?string
    {
        return $condition ? (self::conditions()[$condition] ?? Str::headline($condition)) : null;
    }

    /** Template options for the category admin select: key => label. */
    public static function templateOptions(): array
    {
        return collect(self::templates())->map(fn ($t) => $t['label'])->all();
    }

    public static function templateKeyFor(?Category $category): string
    {
        if (! $category) {
            return 'general';
        }

        $templates = self::templates();

        if ($category->spec_template && isset($templates[$category->spec_template])) {
            return $category->spec_template;
        }

        return self::guessTemplateKey($category->slug ?: Str::slug($category->name));
    }

    public static function guessTemplateKey(string $slug): string
    {
        $tokens = array_filter(explode('-', Str::lower($slug)));

        foreach (self::templates() as $key => $template) {
            foreach ($template['keywords'] ?? [] as $keyword) {
                foreach ($tokens as $token) {
                    if (Str::startsWith($token, $keyword)) {
                        return $key;
                    }
                }
            }
        }

        return 'general';
    }

    /** All fields for a template, flattened: key => definition. */
    public static function fields(string $templateKey): array
    {
        $template = self::templates()[$templateKey] ?? self::templates()['general'];

        return collect($template['groups'])->flatMap(fn ($fields) => $fields)->all();
    }

    /** Validation rules for the spec fields of a template. */
    public static function rules(string $templateKey): array
    {
        $rules = ['specs' => ['nullable', 'array']];

        foreach (self::fields($templateKey) as $key => $field) {
            $fieldRules = [! empty($field['required']) ? 'required' : 'nullable'];

            $fieldRules[] = match ($field['type']) {
                'number' => 'numeric',
                default => 'string',
            };

            if ($field['type'] === 'select') {
                $fieldRules[] = Rule::in($field['options']);
            } elseif ($field['type'] === 'textarea') {
                $fieldRules[] = 'max:1000';
            } elseif ($field['type'] !== 'number') {
                $fieldRules[] = 'max:255';
            }

            $rules["specs.$key"] = $fieldRules;
        }

        return $rules;
    }

    /** Readable attribute names so errors say "RAM is required" instead of "specs.ram size". */
    public static function attributes(string $templateKey): array
    {
        return collect(self::fields($templateKey))
            ->mapWithKeys(fn ($field, $key) => ["specs.$key" => $field['label']])
            ->all();
    }

    /** Keep only the fields belonging to the template, dropping empty values. */
    public static function clean(string $templateKey, array $input): array
    {
        return collect(self::fields($templateKey))
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => is_string($input[$key] ?? null) ? trim($input[$key]) : ($input[$key] ?? null)])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();
    }

    /**
     * Grouped, display-ready specs for a product page:
     * ['Performance' => [['label' => 'RAM', 'value' => '16 GB'], ...], ...]
     */
    public static function display(?Category $category, ?array $specs): array
    {
        if (empty($specs)) {
            return [];
        }

        $template = self::templates()[self::templateKeyFor($category)] ?? self::templates()['general'];
        $groups = [];
        $shown = [];

        foreach ($template['groups'] as $groupName => $fields) {
            foreach ($fields as $key => $field) {
                if (! isset($specs[$key]) || $specs[$key] === '') {
                    continue;
                }

                $value = (string) $specs[$key];
                if (! empty($field['suffix']) && ! Str::endsWith($value, $field['suffix'])) {
                    $value .= ($field['suffix'] === '%' ? '' : ' ').$field['suffix'];
                }

                $groups[$groupName][] = ['label' => $field['label'], 'value' => $value, 'long' => $field['type'] === 'textarea'];
                $shown[] = $key;
            }
        }

        // Specs saved under a previous category still show instead of silently vanishing.
        $leftover = array_diff_key($specs, array_flip($shown));
        foreach ($leftover as $key => $value) {
            if ($value !== null && $value !== '') {
                $groups['Other details'][] = ['label' => Str::headline($key), 'value' => (string) $value, 'long' => false];
            }
        }

        return $groups;
    }
}
