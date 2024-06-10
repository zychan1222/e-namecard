<?php

namespace App\Traits;

use App\Models\Internationalization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Trait Queryable
 * Place all common query methods in this trait
 */

trait Queryable
{
    protected array|string $with = [];

    protected array|string $withCount = [];

    protected array|string $orderBy = [];

    protected int $per_page = self::PAGINATION_PER_PAGE;

    protected function getQuery($filters = []): Builder
    {
        $model = resolve($this->getModelClass());

        if(!empty($filters['per_page'])) {
            $this->per_page = $filters['per_page'];
        }

        $translatable_field = $model->translatable ?? [];

        return $model->query()
            ->when(!empty($this->with), function (Builder $query) {
                $query->with($this->with);
            })
            ->when(!empty($this->withCount), function (Builder $query) {
                $query->withCount($this->withCount);
            })
            ->when(!empty($filters['order_by']), function (Builder $query) use ($filters, $translatable_field) {
                $order_by = $filters['order_by'];

                if(!is_array($order_by)) {
                    $order_by = [$order_by => 'asc'];
                }

                foreach($order_by as $key => $value) {
                    /**
                     * To handle non-associative array of order_by
                     * E.g. order_by = ['name', 'code']
                     */
                    if(is_int($key)) {
                        if(in_array($value, $translatable_field)) {
                            $value = $this->getTranslatableKey($value);
                        }
                        $query->orderBy($value);
                    } else {
                        /**
                         * To handle associative array of order_by
                         */

                        /**
                         * To handle associative array with locale specified
                         * E.g.
                         * $order_by = [
                         *   'name' => [
                         *      'en' => 'asc',
                         *      'zh' => 'desc',
                         *   ];
                         */
                        if(is_array($value)) {
                            foreach($value as $locale => $sort_direction) {
                                if(in_array($key, $translatable_field)) {
                                    $key = $this->getTranslatableKey($key, $locale);
                                }

                                $query->orderBy($key, $sort_direction);
                            }
                        } else {
                            /**
                             * To handle associative array without locale specified
                             * E.g.
                             * $order_by = [
                             *   'name' => 'asc',
                             * ]
                             */
                            if(in_array($key, $translatable_field)) {
                                $key = $this->getTranslatableKey($key);
                            }
                            $query->orderBy($key, $value);
                        }
                    }
                }
            }, function (Builder $query) {
                // Default order by latest records first
                $query->latest();
            });
    }

    public function with(array|string $with): self
    {
        $this->with = $with;

        return $this;
    }

    public function withCount(array|string $with_count): self
    {
        $this->withCount = $with_count;

        return $this;
    }

    public function orderBy($filters = []): self
    {
        $this->orderBy = $filters;

        return $this;
    }

    public function find($id): ?Model
    {
        return $this->getQuery()->findOrFail($id);
    }

    public function first(): ?Model
    {
        return $this->getQuery()->firstOrFail();
    }

    public function getTranslatableKey($key, $locale = null): string
    {
        if($locale) {
            return $key . '->' . $locale;
        }

        return $key . '->' . app()->getLocale();
    }

    abstract public function getAll(array $filters = []): Collection;
    abstract public function getAllPaginated(array $filters = []): LengthAwarePaginator;
}
