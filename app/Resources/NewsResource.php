<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 06.09.2023 / 23:45
 */

namespace App\Resources;

use App\Models\News;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NewsResource
 * @package App\Resources
 */
class NewsResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var News $news */
        $news = $this->resource;

        return [
            'id'          => $news->getId(),
            'title'       => $news->getTitle(),
            'description' => $news->getDescription(),
            'full_text'   => $news->getFullText(),
            'image'       => $news->image->getUrl(),
        ];
    }
}
