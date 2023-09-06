<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 00:26
 */

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageService
{
    /**
     * @return ImageService
     */
    public static function getInstance(): ImageService
    {
        return new static();
    }

    public function upload(Request $request, string $catalog)
    {
        $extension = $request->file('image')->getClientOriginalExtension();
        $imageName = md5(time());

        $request->file('image')->storeAs("public/images/{$catalog}", $imageName . '.' . $extension);

        return Image::query()->create(
            [
                'path'      => "storage/images/{$catalog}/",
                'name'      => $imageName,
                'extension' => $extension
            ]
        );
    }
}
