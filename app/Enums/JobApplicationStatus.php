<?php

namespace App\Enums;

enum JobApplicationStatus: string{
  case read = "Read";
  case unqualified = "Unqualified";
  case unread = "Unread";
  case archived = "Archived";
  case interview = "Interview";
  case accepted = "Accepted";
  case user_retracted = "User Retracted";
}