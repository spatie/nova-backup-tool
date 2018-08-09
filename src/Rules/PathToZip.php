<?php

namespace Spatie\BackupTool\Rules;

use Illuminate\Contracts\Validation\Rule;

class PathToZip implements Rule
{
    public function passes($attribute, $value)
    {
        return ends_with($value, '.zip');
    }

    public function message()
    {
        return 'The given value must be a path to a zip file.';
    }
}
