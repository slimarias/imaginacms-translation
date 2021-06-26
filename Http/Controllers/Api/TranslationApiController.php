<?php

namespace Modules\Translation\Http\Controllers\Api;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Translation\Repositories\TranslationRepository;
use Modules\Translation\Services\TranslationRevisions;
use Modules\User\Traits\CanFindUserWithBearerToken;

class TranslationApiController extends BaseApiController
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
        return response()->json(['data' => 'Translation updated Successfully']);
    }

    public function clearCache()
    {
        $this->translation->clearCache();
        return response()->json(['data' => 'Translation Cache cleared Successfully']);
    }

    public function revisions(TranslationRevisions $revisions, Request $request)
    {
        return $revisions->get(
            $request->get('key'),
            $request->get('locale')
        );
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function delete($criteria, Request $request)
    {
        \DB::beginTransaction();
        try {
            $params = $this->getParamsRequest($request);
            //Delete data
            $this->translation->deleteBy($criteria, $params);

            //Response
            $response = ['data' => 'Translation deleted Successfully'];
            \DB::commit(); //Commit to Data Base
        } catch (\Exception $e) {
            \DB::rollback();//Rollback to Data Base
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response, $status ?? 200);
    }
}
