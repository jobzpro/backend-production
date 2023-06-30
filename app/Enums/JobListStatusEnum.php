<?php 

namespace App\Enums;

enum JobListStatusEnum: string{
  case Draft = 'draft';
  case Published = 'published';
  case Archived = 'archived';
}