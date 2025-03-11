<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, SoftDeletes, Sortable;

    protected $fillable = ['author_id', 'photo_id', 'title', 'content', 'slug', 'is_published'];

    public $sortable = ['title', 'content', 'created_at', 'updated_at'];

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function photo() {
        return $this->belongsTo(Photo::class);
    }

    public function categories() {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function keywords() {
        return $this->morphToMany(Keyword::class, 'keywordable');
    }

    /* filters (scopes) */
    public function scopeFilter($query, $searchterm) {
        if(!empty($searchterm)) {
            $query->where(function($q) use ($searchterm) {
                $q->where('title', 'like', "%{$searchterm}%")
                ->orWhere('content', 'like', "%{$searchterm}%");
            });
        }
        return $query;
    }

    // scope: alleen gepubliceerde posts
    public function scopePublished($query) {
        return $query->where('is_published', 1);
    }

    // scope: filter op posts op basis van categorieen (polymorfe relatie)
    // Dit gaat na of een post in ALLE geselecteerde categorieen zit.

    // $gefilterdePosts = POST::inCategories([1, 2, 3])->get();
    public function scopeInCategories($query, $categoryIds) {
        if(!empty($categoryIds)) {
            foreach($categoryIds as $categoryId) {
                $query->whereHas('categories', function($q) use ($categoryId) {
                    $q->where('categories.id', '=', $categoryId);
                });
            }
        }
        return $query;
    }
}
