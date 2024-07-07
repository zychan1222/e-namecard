<?php
namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    public function getModelClass(): string
    {
        return Employee::class;
    }

    public function create(array $data): ?Employee
    {
        return parent::create($data);
    }

    public function findById($id): ?Employee
    {
        return Employee::find($id);
    }

    public function findByEmail(string $email): ?Employee
    {
        return Employee::where('email', $email)->first();
    }

    public function getAll(array $filters = []): Collection
    {
        
    }

    public function getAllPaginated(array $filters = []): LengthAwarePaginator
    {

    }
}

