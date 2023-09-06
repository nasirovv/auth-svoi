<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class News
 * @package App\Models
 *
 * @property int     $id
 * @property int     image_id
 * @property string  $title
 * @property string  $description
 * @property string  $full_text
 * @property boolean $status
 *
 * @property Image   $image
 */
class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $fillable = [
        'image_id',
        'title',
        'description',
        'full_text',
        'status'
    ];

    /**
     * @return HasOne
     */
    public function image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->image_id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFullText(): string
    {
        return $this->full_text;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }
}
