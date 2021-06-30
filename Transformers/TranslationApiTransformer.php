<?php

namespace Modules\Translation\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationApiTransformer extends JsonResource
{
  public $preserveKeys = true;
}
