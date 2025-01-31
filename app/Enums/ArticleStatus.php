<?php
/**
 * enums for article
 * @author Hojjat koochak zadeh
 */
namespace App\Enums;

enum ArticleStatus: int
{
    case DRAFT = 1;
    case SCHEDULED = 2;
    case PUBLISHED = 3;
    case ARCHIVED = 4;
}
