<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;

readonly class SearchDemand
{
    public function __construct(private string $query, private int $page, private array $filters)
    {
    }

    public static function createFromRequest(Request $request): SearchDemand
    {
        $requestFilters = $request->query->all()['filters'] ?? [];
        $filters = [];
        if (!empty($requestFilters)) {
            foreach ($requestFilters as $filter => $value) {
                $filterMap = [
                    'Document Type' => 'manual_type',
                    'Language' => 'manual_language',
                    'Version' => 'major_versions',
                ];
                if (!\array_key_exists($filter, $filterMap)) {
                    continue;
                }
                foreach ($value as $name => $state) {
                    if ($state === 'true') {
                        $filters[$filterMap[$filter]][] = $name;
                    }
                }
            }
        }
        $page = (int)$request->query->get('page', '1');
        $query = $request->query->get('q', '');

        return new self($query, max($page, 1), $filters);
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
