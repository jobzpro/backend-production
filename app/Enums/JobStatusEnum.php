<?php 

namespace App\Enums;

enum JobStatusEnum: string{
  case Draft = 'draft';
  case Published = 'published';
  case Archived = 'archived';
}