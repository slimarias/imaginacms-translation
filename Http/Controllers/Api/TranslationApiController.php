<?php

namespace Modules\Translation\Http\Controllers\Api;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Translation\Repositories\TranslationRepository;
use Modules\Translation\Services\TranslationRevisions;
use Modules\User\Traits\CanFindUserWithBearerToken;

class TranslationApiController extends Controller
{
    use CanFindUserWithBearerToken;
    /**
     * @var TranslationRepository
     */
    private $translation;

    public function __construct(TranslationRepository $translation)
    {
        $this->translation = $translation;
    }

    public function update(Request $request)
    {
        $data = $request->input('attributes') ?? [];//Get data

        $languages = \LaravelLocalization::getSupportedLocales();

        foreach ($languages as $lang => $value) {
            $this->translation->saveTranslationForLocaleAndKey(
                $lang,
                $data['key'],
                $data[$lang]['value'],
            );
        }
        return response()->json(['data' => 'Translation Successfull']);
    }

    public function clearCache()
    {
        $this->translation->clearCache();
    }

    public function revisions(TranslationRevisions $revisions, Request $request)
    {
        return $revisions->get(
            $request->get('key'),
            $request->get('locale')
        );
    }
}
