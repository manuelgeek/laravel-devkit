<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/1/19
 * Time: 9:31 PM
 */
namespace App\Alab\modules\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        return "working nicely";
    }
}