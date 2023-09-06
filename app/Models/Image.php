<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Image
 * @package App\Models
 *
 * @property int $id
 * @property string $path
 * @property string $name
 * @property string $extension
 */
class Image extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $fillable = [
        'path',
        'name',
        'extension'
    ];

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return url($this->getFullPath());
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->path . $this->name . "." . $this->extension;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }
}
