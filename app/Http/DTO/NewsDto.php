<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 00:29
 */

namespace App\Http\DTO;

/**
 * Class NewsDto
 * @package App\Http\DTO
 *
 * @property int     $id
 * @property string  $title
 * @property string  $description
 * @property string  $full_text
 * @property boolean $status
 */
class NewsDto
{
    /** @var int $image_id */
    protected int $image_id;

    /** @var string $title */
    protected string $title;

    /** @var string $description */
    protected string $description;

    /** @var string $full_text */
    protected string $full_text;

    /** @var bool $status */
    protected bool $status;

    public function __construct(
        int    $image_id,
        string $title,
        string $description,
        string $full_text,
        bool   $status,
    )
    {
        $this->image_id = $image_id;
        $this->title = $title;
        $this->description = $description;
        $this->full_text = $full_text;
        $this->status = $status;
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
    public function getStatus(): bool
    {
        return $this->status;
    }
}
