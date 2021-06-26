<?php

namespace Modules\Translation\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Modules\Translation\Services\TranslationsService;

class AllTranslationApiController extends Controller
{
    private $translationsService;

    public function __construct(TranslationsService $translationsService)
    {
        $this->translationsService = $translationsService;
    }

    public function __invoke()
    {
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

        return response()->json($returnedTranslations);
    }
}
