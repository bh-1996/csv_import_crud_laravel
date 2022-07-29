<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Validator;
use DB;
use Hash;
use Redirect;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->simplePaginate('10');
        return view('product.index')->with(['products'=>$products]);
    }


    public function exportCsv(Request $request)
    {
        ini_set('max_execution_time', 0);
        /*set_time_limit(0);*/
        ini_set('post_max_size', '500000000000M');
        ini_set('upload_max_filesize', '500000000000M');

        $fileName = 'products.csv';

        $products = Product::all();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $columns = array('Title', 'Price', 'Description');
        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                $row['Title'] = $product->title;
                $row['Price'] = $product->price;
                $row['Description'] = $product->description;
                // print_r($row);die;
                fputcsv($file, array($row['Title'], $row['Price'], $row['Description']));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function importProducts(Request $request)
    {
        ini_set('max_execution_time', 0);
        /*set_time_limit(0);*/
        ini_set('post_max_size', '500000000000M');
        ini_set('upload_max_filesize', '500000000000M');

        $postData = $request->all();

        $validateData = Validator::make($postData, [
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        if ($validateData->fails()) {
            return Redirect::to('/product/import')->with("message", $validateData->errors()->first());
        }

        if ($request->hasFile('csv_file')) {
            $path = $request->file('csv_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));

            $products = array_slice($data, 1);
            $totalProducts = count($products);

            
            $icheck = 0;
            for ($i = 0; $i < $totalProducts; $i++) {
                $product = $products[$i];
                $records =  DB::table('products')->insertOrIgnore([
                                'title' => $product[0] ?? '',
                                'price' => $product[1] ?? '',
                                'description' => $product[2] ?? '',
                        ]);
                
                $icheck++;
            }
             //dd($icheck);
            return Redirect::to('/');
        }
        
    }

    public function productEdit($id)
    {
        $product = Product::find($id);
        if ($product) {
            return view("product.edit", ['product' => $product]);
        }
        return Redirect::back();
        
    }


    public function productUpdate(Request $request)
    {
        $product = Product::find($request->id);
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
            if($product->update());
            {
            return redirect()->route('index');
            }
            return redirect()->back();
    }

}
