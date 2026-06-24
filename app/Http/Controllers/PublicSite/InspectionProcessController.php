<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InspectionProcessController extends Controller
{
    public function __invoke(): View
    {
        return view('public.inspection-process');
    }
}
