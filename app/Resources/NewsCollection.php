<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 06.09.2023 / 23:23
 */

namespace App\Resources;

use App\Models\News;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class NewsCollection
 * @package App\Resources
 */
class NewsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function (NewsResource $news) {
            return $news;
        });
    }
}
