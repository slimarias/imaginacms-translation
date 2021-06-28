<?php

namespace Modules\Translation\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Translation\Services\TranslationsService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Translation\Transformers\TranslationApiTransformer;

class AllTranslationApiController extends BaseApiController
{
    private $translationsService;

    public function __construct(TranslationsService $translationsService)
    {
        $this->translationsService = $translationsService;
    }

    public function index(Request $request)
    {
        $params = $this->getParamsRequest($request);
        $allModulesTrans = $this->translationsService->getFileAndDatabaseMergedTranslations();

        $translations = $allModulesTrans->all()->toArray();

        $returnedTranslations = [];

        foreach($translations as $key=>$translation){
            $returnedTranslation = [
              'key' =>  $key
            ];
            foreach($translation as $locale=>$value){
                $returnedTranslation[$locale] = ['value' => $value];
            }

            $returnedTranslations[] = $returnedTranslation;

        }

        if (isset($params->page) && $params->page) {
            $returnedTranslations = $this->paginate($returnedTranslations, $params->take);
        }

        $response = ['data' => TranslationApiTransformer::collection($returnedTranslations)];

        $params->page ? $response["meta"] = ["page" => $this->pageTransformer($returnedTranslations)] : false;

        return response()->json($response,200);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
