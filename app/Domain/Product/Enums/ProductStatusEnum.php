<?php

namespace App\Domain\Product\Enums;

enum ProductStatusEnum: string
{
    case DRAFT = 'draft';
    case TRASH = 'trash';
    case PUBLISHED = 'published';
}