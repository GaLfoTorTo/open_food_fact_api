<?php

namespace App\Domain\Products\Enums;

enum ProductStatusEnum: string
{
    case DRAFT = 'draft';
    case TRASH = 'trash';
    case PUBLISHED = 'published';
}