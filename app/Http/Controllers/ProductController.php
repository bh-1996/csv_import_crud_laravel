<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->simplePaginate('10');
        return view('product.index')->with(['products'=>$products]);
    }


    // public function exportCsv(Request $request)
    // {
    //     ini_set('max_execution_time', 0);
    //     /*set_time_limit(0);*/
    //     ini_set('post_max_size', '500000000000M');
    //     ini_set('upload_max_filesize', '500000000000M');

    //     $fileName = 'products.csv';

    //     $products = Product::all();

    //     $headers = array(
    //         "Content-type" => "text/csv",
    //         "Content-Disposition" => "attachment; filename=$fileName",
    //         "Pragma" => "no-cache",
    //         "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
    //         "Expires" => "0",
    //     );

    //     $columns = array('Title', 'Price', 'Description');
    //     $callback = function () use ($products, $columns) {
    //         $file = fopen('php://output', 'w');
    //         fputcsv($file, $columns);

    //         foreach ($products as $product) {
    //             $row['Title'] = $product->title;
    //             $row['Price'] = $product->price;
    //             $row['Description'] = $product->description;
    //             // print_r($row);die;
    //             fputcsv($file, array($row['Title'], $row['Price'], $row['Description']));
    //         }

    //         fclose($file);
    //     };
    //     back()->with('msg', 'CSV Exported successfully!');
    //     return response()->stream($callback, 200, $headers);
    // }

    public function exportCsv(Request $request)
{
    try {
        // Set the maximum execution time and upload file size limits
        ini_set('max_execution_time', 0);
        ini_set('post_max_size', '500000000000M');
        ini_set('upload_max_filesize', '500000000000M');

        // File name for the exported CSV
        $fileName = 'products.csv';

        // Retrieve all products from the database
        $products = Product::all();

        // Define headers for the CSV file
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        // Define the columns for the CSV file
        $columns = array('Title', 'Price', 'Description');

        // Callback function to generate CSV content
        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                $row['Title'] = $product->title;
                $row['Price'] = $product->price;
                $row['Description'] = $product->description;

                fputcsv($file, array($row['Title'], $row['Price'], $row['Description']));
            }

            fclose($file);
        };

        // Stream the CSV file to the response
        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        // Handle any exceptions during the export
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}

public function importProducts(Request $request)
{
    try {
        ini_set('max_execution_time', 0);
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

                $validator = Validator::make($product, [
                    '0' => 'required',  // Assuming 'title' is in the first column
                    '1' => 'required|numeric',  // Assuming 'price' is in the second column and should be a numeric value
                    '2' => 'nullable',  // Assuming 'description' is in the third column and can be nullable
                ]);

                if ($validator->fails()) {
                    return Redirect::to('/product/import')->with("message", $validator->errors()->first());
                }

                $records = [
                    'title' => $product[0],
                    'price' => $product[1],
                    'description' => $product[2] ?? null,
                ];

                try {
                    // Create the product
                    $productModel = Product::create($records);
                    $icheck++;
                } catch (\Exception $e) {
                    return Redirect::to('/product/import')->with("message", "Error inserting product at row $icheck: " . $e->getMessage());
                }
            }

            return Redirect::to('/')->with('msg', 'CSV Imported successfully!');
        }
    } catch (\Exception $e) {
        return Redirect::to('/product/import')->with("message", "Error during CSV import: " . $e->getMessage());
    }
}

    // public function importProducts(Request $request)
    // {
    //     ini_set('max_execution_time', 0);
    //     /*set_time_limit(0);*/
    //     ini_set('post_max_size', '500000000000M');
    //     ini_set('upload_max_filesize', '500000000000M');

    //     $postData = $request->all();

    //     $validateData = Validator::make($postData, [
    //         'csv_file' => 'required|mimes:csv,txt',
    //     ]);

    //     if ($validateData->fails()) {
    //         return Redirect::to('/product/import')->with("message", $validateData->errors()->first());
    //     }

    //     if ($request->hasFile('csv_file')) {
    //         $path = $request->file('csv_file')->getRealPath();
    //         $data = array_map('str_getcsv', file($path));

    //         $products = array_slice($data, 1);
    //         $totalProducts = count($products);

            
    //         $icheck = 0;
    //         for ($i = 0; $i < $totalProducts; $i++) {
    //             $product = $products[$i];
    //             // $records =  DB::table('products')->insertOrIgnore([
    //             //                 'title' => $product[0] ?? '',
    //             //                 'price' => $product[1] ?? '',
    //             //                 'description' => $product[2] ?? '',
    //             //         ]);
    //             $records = [
    //                 'title' => $product[0] ?? '',
    //                 'price' => $product[1] ?? '',
    //                 'description' => $product[2] ?? '',
    //             ];
    //             // dd($records);
    //             $products = Product::create($records);
                
    //             $icheck++;
    //         }
    //          //dd($icheck);
    //         return Redirect::to('/');
    //     }
        
    // }

    public function productEdit($id)
    {
       try {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->back()->withErrors(['error' => 'Product not found!']);
            }
                return view("product.edit", ['product' => $product]);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors(['error' => $message]);
        }
        
    }


    public function productUpdate(Request $request)
    {
        try {
            $product = Product::find($request->id);
            if (!$product) {
                return redirect()->back()->withErrors(['error'=>'Product not found']);
            }
            $product->title = $request->title;
            $product->description = $request->description;
            $product->price = $request->price;
                if($product->update());
                {
                return redirect()->route('index')->with('msg', 'Product updated successfully!');
                }
            return redirect()->back()->withErrors(['error'=> 'Something went wrong!']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors(['error' => $message]);
        }
    }

    public function productDelete(Request $request)
    {
        try {
            $product = Product::find($request->id);
            if (!$product) {
                return redirect()->back()->withErrors(['error'=>'Product not found']);
            }
            $product->delete();
            return redirect()->back()->with('msg', 'Product deleted successfully!');
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors(['error' => $message]);
        }
    }
    

}
