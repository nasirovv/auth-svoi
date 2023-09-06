<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 06.09.2023 / 16:44
 */

namespace App\Http\Controllers;

use App\Http\DTO\NewsDto;
use App\Models\Image;
use App\Models\News;
use App\Resources\NewsCollection;
use App\Resources\NewsResource;
use App\Services\ImageService;
use App\Services\NewsService;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use RuntimeException;

/**
 * Class NewsController
 * @package App\Http\Controllers
 */
class NewsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            $list = NewsService::getInstance()->getList($request->user()->role->getName());

            return $this->render_json(new NewsCollection($list), 'Success');
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function detail(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            $news = NewsService::getInstance()->getById((int)$request->query('id'), $request->user()->role->getName());

            if ($news === null) {
                throw new RuntimeException('News not found', 404);
            }

            return $this->render_json(new NewsResource($news), 'Success');
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }

    public function validator(Request $request): ValidatorContract
    {
        return ValidatorFacade::make(
            $request->all(),
            [
                'title'       => 'required|string',
                'description' => 'required|string',
                'full_text'   => 'required|string',
                'status'      => 'required|bool',
                'image'       => 'required|mimes:jpeg,png,jpg,gif'
            ],
            [
                'required' => 'Поле, обязательное для заполнения',
            ]
        );
    }

    public function validatorForUpdate(Request $request): ValidatorContract
    {
        return ValidatorFacade::make(
            $request->all(),
            [
                'title'       => 'required|string',
                'description' => 'required|string',
                'full_text'   => 'required|string',
                'status'      => 'required|bool',
                'image'       => 'nullable|mimes:jpeg,png,jpg,gif'
            ],
            [
                'required' => 'Поле, обязательное для заполнения',
            ]
        );
    }

    public function create(Request $request)
    {
        $message = null;
        $code = 200;

        try {
            DB::beginTransaction();

            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->validator($request);

            if ($validator->fails()) {
                throw new RuntimeException($validator->errors()->first(), 400);
            }

            $data = $validator->getData();

            if (isset($data['image'])) {
                /** @var Image $image */
                $image = ImageService::getInstance()->upload($request, 'news');
            } else {
                throw new RuntimeException('Image not found');
            }

            NewsService::getInstance()->store(
                new NewsDto(
                    $image->getId(),
                    $data['title'],
                    $data['description'],
                    $data['full_text'],
                    $data['status'],
                )
            );

            DB::commit();

            return $this->render_json([], 'Successfully added');
        } catch (Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            $code = (int)$exception->getCode() === 0 ? 400 : (int)$exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }

    public function update(Request $request)
    {
        $message = null;
        $code = 200;

        try {
            DB::beginTransaction();

            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->validatorForUpdate($request);

            if ($validator->fails()) {
                throw new RuntimeException($validator->errors()->first(), 400);
            }

            $data = $validator->getData();

            /** @var News|null $news */
            $news = NewsService::getInstance()->getById((int)$request->query('id'), $request->user()->role->getName());

            if ($news === null) {
                throw new RuntimeException('News not found', 404);
            }

            $imageId = $news->image->getId();

            if (isset($data['image'])) {
                /** @var Image $image */
                $image = ImageService::getInstance()->upload($request, 'news');

                $imageId = $image->getId();
            }

            NewsService::getInstance()->update(
                $news,
                new NewsDto(
                    $imageId,
                    $data['title'],
                    $data['description'],
                    $data['full_text'],
                    $data['status'],
                )
            );

            DB::commit();

            return $this->render_json([], 'Successfully updated');
        } catch (Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }

    public function delete(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            DB::beginTransaction();

            /** @var News $news */
            $news = NewsService::getInstance()->getById((int)$request->query('id'), $request->user()->role->getName());

            if ($news === null) {
                throw new RuntimeException('News not found', 404);
            }

            $news->delete();
            $news->image->delete();

            DB::commit();

            return $this->render_json([], 'Success');
        } catch (Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }
}
