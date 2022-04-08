<?php

namespace App\Http\Resources;

use App\Exceptions\LocaleException;
use App\Locale\PoshubLocale;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class EditHistoryResource extends JsonResource
{
    protected PoshubLocale $locale;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->locale = new PoshubLocale();
    }

    public function decorateWithId(array $array): array
    {
        return array_merge(
            ['id' => $this->id],
            $array
        );
    }

    /**
     * @param  array           $array
     * @return array
     * @throws LocaleException
     */
    public function decorateWithEditHistory(array $array): array
    {
        return $this->decorateWithTimeHistory(
            $this->decorateWithUserHistory($array)
        );
    }

    public function decorateWithUserHistory(array $array): array
    {
        return array_merge(
            $array,
            [
                'created_by' => new UserCollection($this->createdBy),
                'updated_by' => new UserCollection($this->updatedBy)
            ]
        );
    }

    /**
     * @param  array           $array
     * @return array
     * @throws LocaleException
     */
    public function decorateWithTimeHistory(array $array): array
    {
        return array_merge(
            $array,
            [
                'created_at' => empty($this->created_at)
                    ? $this->created_at
                    : $this->locale->getStringFromSystemToLocaleTzToDtfSystem($this->created_at),
                'updated_at' => empty($this->updated_at)
                    ? $this->updated_at
                    : $this->locale->getStringFromSystemToLocaleTzToDtfSystem($this->updated_at)
            ]
        );
    }
}
