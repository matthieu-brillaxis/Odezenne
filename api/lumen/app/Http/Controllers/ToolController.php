<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings;

class ToolController extends Controller
{
  public function Analytics() {
    $api_key = Settings::where('label', 'analytics_api_key')->limit(1)->pluck('value')[0];

    return response($api_key);
  }
}

?>
