<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 06.09.2023 / 23:19
 */

namespace App\Services;

use App\Http\DTO\NewsDto;
use App\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NewsService
 * @package App\Services
 */
class NewsService
{
    /**
     * @return static
     */
    public static function getInstance(): static
    {
        return new static();
    }

    public function getList(string $role): Collection
    {
        $builder = News::query()->with('image');

        if (!in_array($role, ['admin', 'moderator'])) {
            $builder->where('status', '=', 1);
        }

        return $builder->get();
    }

    /**
     * @param int    $id
     * @param string $role
     * @return Builder|Model|object|null
     */
    public function getById(int $id, string $role)
    {
        $builder = News::query()->whereKey($id)->with('image');

        if (!in_array($role, ['admin', 'moderator'])) {
            $builder->where('status', '=', 1);
        }

        return $builder->first();
    }

    /**
     * @param NewsDto $dto
     * @return void
     */
    public function store(NewsDto $dto): void
    {
        News::query()->create(
            [
                'title'       => $dto->getTitle(),
                'description' => $dto->getDescription(),
                'full_text'   => $dto->getFullText(),
                'status'      => $dto->getStatus(),
                'image_id'    => $dto->getImageId()
            ]
        );
    }

    /**
     * @param News    $news
     * @param NewsDto $dto
     * @return void
     */
    public function update(News $news, NewsDto $dto): void
    {
        $news->update(
            [
                'title'       => $dto->getTitle(),
                'description' => $dto->getDescription(),
                'full_text'   => $dto->getFullText(),
                'status'      => $dto->getStatus(),
                'image_id'    => $dto->getImageId()
            ]
        );
    }
}
