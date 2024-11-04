<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Foundation\Application;

class ProductController extends Controller
{
    public function __invoke(Product $product): Factory|View|Application
    {
        $product->load(['optionValues.option']);

        $alsoIds = session('also', []);
        $also = Product::query()
            ->where(function ($q) use ($product, $alsoIds) {
                $q->whereIn('id', $alsoIds)
                    ->where('id', '!=', $product->id);
            })
            ->get();

        $options = $product->optionValues->mapToGroups(function ($item) {
             return [$item->option->title => $item];
        });


        session()->put('also.' . $product->id, $product->id);

        return view('product.shared.show', [
            'product' => $product,
            'options' => $options,
            'also' => $also
        ]);
    }
}
