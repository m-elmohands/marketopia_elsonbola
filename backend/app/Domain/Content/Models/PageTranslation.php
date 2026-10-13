<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['title', 'intro', 'body', 'meta_title', 'meta_description', 'locale'];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
