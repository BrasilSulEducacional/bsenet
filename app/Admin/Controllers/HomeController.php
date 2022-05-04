<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Informações úteis internas...')
            ->body(view('dashboard'));
    }

    public function all(Request $request)
    {
        // $q = $request->get('q');
        // ->paginate(null, ['id', 'name as text']);
        return Administrator::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });
    }
}
