<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminTableHelper
{
    /**
     * Apply whitelist-safe sorting from request query params.
     *
     * @param  array<string, string>  $allowed  map of request key => db column / expression
     */
    public static function applySort(
        Builder $query,
        Request $request,
        array $allowed,
        string $defaultSort = 'created_at',
        string $defaultDirection = 'desc'
    ): Builder {
        $sort = $request->get('sort', $defaultSort);
        $direction = strtolower((string) $request->get('direction', $defaultDirection)) === 'asc' ? 'asc' : 'desc';

        if (!array_key_exists($sort, $allowed)) {
            $sort = $defaultSort;
            $direction = $defaultDirection;
        }

        $column = $allowed[$sort] ?? $allowed[$defaultSort] ?? $defaultSort;

        return $query->orderBy($column, $direction);
    }

    public static function sortUrl(string $column, string $defaultDirection = 'asc'): string
    {
        $currentSort = request('sort');
        $currentDirection = strtolower((string) request('direction', 'asc'));

        $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';

        if ($currentSort !== $column) {
            $direction = $defaultDirection;
        }

        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $direction,
            'page' => null,
        ]);
    }

    public static function isSortedBy(string $column): bool
    {
        return request('sort') === $column;
    }

    public static function sortDirection(string $column): ?string
    {
        if (!self::isSortedBy($column)) {
            return null;
        }

        return strtolower((string) request('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
    }
}
