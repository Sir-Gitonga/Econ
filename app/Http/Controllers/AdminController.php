<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use App\Models\Slide;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    /**
     * Return the current company id if a company has been identified by middleware.
     *
     * @return int|null
     */
    protected function currentCompanyId()
    {
        return app()->has('company') ? (app('company')->id ?? null) : null;
    }

    /**
     * Get the current subdomain slug for route generation.
     *
     * @return string
     */
    protected function currentSubdomain()
    {
        if (app()->has('company')) {
            return app('company')->slug;
        }

        // Fallback: extract from request
        $host = request()->getHost();
        $host = explode(':', $host)[0];
        $mainDomain = parse_url(config('app.url') ?? env('APP_URL', ''), PHP_URL_HOST) ?: 'localhost';

        if ($host !== $mainDomain) {
            return explode('.', $host)[0] ?? null;
        }

        return null;
    }

    protected function getTenantTable($base)
    {
        $companyId = $this->currentCompanyId();
        if ($companyId) {
            return 'company_' . $companyId . '_' . $base;
        }
        return $base;
    }
    public function index()
    {
        ini_set('memory_limit', '1024M');
        $user = Auth::user();

        // Get latest orders using raw query only - no Eloquent to avoid circular references and eager loading
        $ordersQuery = "SELECT o.id, o.user_id, o.name, o.phone, o.subtotal, o.tax, o.total, o.status, o.created_at, o.delivered_date, COUNT(oi.id) as items_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id";
        $bindings = [];
        if ($user && $user->company_id) {
            $ordersQuery .= " WHERE o.company_id = ?";
            $bindings[] = $user->company_id;
        }
        $ordersQuery .= " GROUP BY o.id, o.user_id, o.name, o.phone, o.subtotal, o.tax, o.total, o.status, o.created_at, o.delivered_date ORDER BY o.created_at DESC LIMIT 10";
        $orders = DB::select($ordersQuery, $bindings);

        // Aggregate dashboard data (global scope applies)
        $dashboardQuery = DB::table('orders');
        if ($user && $user->company_id) {
            $dashboardQuery->where('company_id', $user->company_id);
        }
        $dashboardDatas = $dashboardQuery
            ->selectRaw("
                COUNT(*) AS TotalOrders,
                SUM(total) AS TotalAmount,
                SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
                SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount,
                SUM(IF(status = 'ordered', 1, 0)) AS TotalOrdered,
                SUM(IF(status = 'delivered', 1, 0)) AS TotalDelivered,
                SUM(IF(status = 'canceled', 1, 0)) AS TotalCanceled
            ")
            ->first();

        // Monthly stats (global scope applies)
        $monthlyQuery = DB::table('orders')
            ->selectRaw("
                DATE_FORMAT(created_at, '%b') AS MonthName,
                MONTH(created_at) AS MonthNo,
                SUM(total) AS TotalAmount,
                SUM(IF(status='ordered', total, 0)) AS TotalOrderedAmount,
                SUM(IF(status='delivered', total, 0)) AS TotalDeliveredAmount,
                SUM(IF(status='canceled', total, 0)) AS TotalCanceledAmount
            ")
            ->whereYear('created_at', now()->year);
        if ($user && $user->company_id) {
            $monthlyQuery->where('company_id', $user->company_id);
        }
        $monthlyDatas = DB::table('month_names as M')
            ->leftJoinSub(
                $monthlyQuery
                    ->groupByRaw('YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, "%b")')
                    ->orderByRaw('MONTH(created_at)')
                , 'D', 'D.MonthNo', '=', 'M.id'
            )
            ->selectRaw("
                M.id AS MonthNo, M.name AS MonthName,
                IFNULL(D.TotalAmount, 0) AS TotalAmount,
                IFNULL(D.TotalOrderedAmount, 0) AS TotalOrderedAmount,
                IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
            ")
            ->get();

        // Prepare data for charts
        $AmountM = $monthlyDatas->pluck('TotalAmount')->implode(',');
        $OrderedAmountM = $monthlyDatas->pluck('TotalOrderedAmount')->implode(',');
        $DeliveredAmountM = $monthlyDatas->pluck('TotalDeliveredAmount')->implode(',');
        $CanceledAmountM = $monthlyDatas->pluck('TotalCanceledAmount')->implode(',');

        // Totals for chart summary
        $TotalAmount = $monthlyDatas->sum('TotalAmount');
        $TotalOrderedAmount = $monthlyDatas->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = $monthlyDatas->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = $monthlyDatas->sum('TotalCanceledAmount');

        return view('admin.dashboard', compact(
            'orders', 'dashboardDatas',
            'AmountM', 'OrderedAmountM', 'DeliveredAmountM', 'CanceledAmountM',
            'TotalAmount', 'TotalOrderedAmount', 'TotalDeliveredAmount', 'TotalCanceledAmount'
        ));
    }


public function brands()
{
    $brands = Brand::paginate(10);
    return view('admin.brands', compact('brands'));
}

    public function add_brand()
    {
        return view('admin.brand-add');
    }
    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug'  =>  'required|unique:brands,slug',
            'image'  =>  'mimes:jpeg,png,jpg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        // Set company_id for multi-tenant
        $user = Auth::user();
        if ($user && $user->company_id) {
            $brand->company_id = $user->company_id;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();
        return redirect()->route('admin.brands', ['subdomain' => $this->currentSubdomain()])->with('status', 'Brand added successfully!');

    }

    public function brand_edit($id)
    {
        $companyId = $this->currentCompanyId();

        if ($companyId) {
            // Company admin can only edit their own brands
            $brand = Brand::where('company_id', $companyId)->findOrFail($id);
        } else {
            // Super admin can edit any brand
            $brand = Brand::findOrFail($id);
        }

        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048'
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/brands/').'/'.$brand->image))
            {
                File::delete(public_path('uploads/brands/'.'/'.$brand->image));
            }
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image =$file_name;
        }

        $brand->save();
        return redirect()->route('admin.brands', ['subdomain' => $this->currentSubdomain()])->with('status', 'Brand updated successfully!');
    }
    protected function GenerateBrandThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $image = Image::read($image->path());

        // Create directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $image->cover(124, 124);
        $image->save($destinationPath . '/' . $imageName);
    }

    protected function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $image = Image::read($image->path());

        // Create directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $image->cover(124, 124);
        $image->save($destinationPath . '/' . $imageName);
    }

    public function brand_delete($id)
    {
       $brand = Brand::find($id);
       if(File::exists(public_path('uploads/brands').'/' . $brand->image))
       {
        File::delete(public_path('uploads/brands').'/' . $brand->image);
       }
       $brand->delete();
       return redirect()->route('admin.brands', ['subdomain' => $this->currentSubdomain()])->with('status', 'Brand has been deleted successfully!');
    }

    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }
    public function category_add()
    {
        return view('admin.category-add');
    }
    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug'  =>  'required|unique:categories,slug',
            'image'  =>  'mimes:jpeg,png,jpg|max:2048'
        ]);

        $category = new category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        // Set company_id for multi-tenant
        $user = Auth::user();
        if ($user && $user->company_id) {
            $category->company_id = $user->company_id;
        }

        $image = $request->file('image');
        if($image && $request->hasFile('image')) {
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image =$file_name;
        }
        $category->save();
        return redirect()->route('admin.categories', ['subdomain' => $this->currentSubdomain()])->with('status', 'category added successfully!');
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }
    public function category_update(Request $request)
    {
        $request->validate([
            'name'  =>  'required','string','max:255',
            'slug'  =>  'required|unique:categories,slug',$request->id,
            'image'  =>  'mimes:jpeg,png,jpg|max:2048'
        ]);

        $category = category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories/').'/'.$category->image))
            {
                File::delete(public_path('uploads/categories/'.'/'.$category->image));
            }
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image =$file_name;
        }

        $category->save();
        return redirect()->route('admin.categories', ['subdomain' => $this->currentSubdomain()])->with('status', 'category updated successfully!');
    }
    public function category_delete($id){
        $category = Category::find($id);
        if(File::exists(public_path('uploads/categories').'/' . $category->image))
        {
            File::delete(public_path('uploads/categories').'/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories', ['subdomain' => $this->currentSubdomain()])->with('status', 'category has been deleted successfully!');
    }
    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add',compact('categories', 'brands'));
    }
    public function product_store(Request $request)
    {
        $user = Auth::user();
        $companyId = $user && $user->company_id ? $user->company_id : null;

        // Generate slug from name first for validation
        $generatedSlug = Str::slug($request->name);

        $request->validate([
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'SKU' => 'required',
            'featured' => 'required|boolean',
            'quantity' => 'nullable|integer|min:0',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        // Validate generated slug for uniqueness within company
        $slugExists = Product::where('slug', $generatedSlug)
            ->where('company_id', $companyId)
            ->exists();

        if ($slugExists) {
            return redirect()->back()
                ->withErrors(['name' => 'A product with this name already exists for your company.'])
                ->withInput();
        }

        $product = new Product();
        if ($user && $user->company_id) {
            $product->company_id = $user->company_id;
        }
        $product->name = $request->name;
        $product->slug = $generatedSlug;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;

        // ✅ Stock status logic moved here
        $quantity = $request->quantity ?? 0;
        $product->quantity = max(0, $quantity); // no negatives
        $product->stock_status = $quantity > 0 ? 'instock' : 'outofstock';

        $product->featured = $request->featured;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imageName = $current_timestamp .'.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if($request->hasFile('images'))
        {
            $allowedFileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                if (in_array($gextension, $allowedFileExtion)) {
                    $gfileName = $current_timestamp . "_" . $counter . "." . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter++;
                }
            }

            $gallery_images = implode(',', $gallery_arr);
        }

        $product->images = $gallery_images;
        $product->save();

        return redirect()->route('admin.products', ['subdomain' => $this->currentSubdomain()])->with('status', 'Product has been added successfully!');
    }


    public function GenerateProductThumbnailImage($image,$imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $image = Image::read($image->path());

        // Generate main product image
        $mainImage = clone $image;
        $mainImage->cover(540, 689);
        $mainImage->save($destinationPath . '/' . $imageName);

        // Generate thumbnail
        $thumbnailImage = clone $image;
        $thumbnailImage->cover(104, 104);
        $thumbnailImage->save($destinationPathThumbnail. '/' . $imageName);
    }
    public function product_edit($id)
    {
        $companyId = $this->currentCompanyId();
        if ($companyId) {
            $product = Product::where('company_id', $companyId)->findOrFail($id);
        } else {
            $product = Product::findOrFail($id);
        }
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update(Request $request)
    {
        $companyId = $this->currentCompanyId();

        $request->validate([
            'name' => 'required|max:100',
            'category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'short_description' => 'required|max:255',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'SKU' => 'required|max:100',
            'quantity' => 'required|integer',
            'featured' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($companyId) {
            $product = Product::where('company_id', $companyId)->findOrFail($request->id);
        } else {
            $product = Product::findOrFail($request->id);
        }

        // Generate slug from name
        $generatedSlug = Str::slug($request->name);

        // Check if slug is unique (excluding current product)
        $slugExists = Product::where('slug', $generatedSlug)
            ->where('company_id', $companyId)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($slugExists) {
            return redirect()->back()
                ->withErrors(['name' => 'A product with this name already exists for your company.'])
                ->withInput();
        }

        $product->name = $request->name;
        $product->slug = $generatedSlug;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->quantity = $request->quantity;
        $product->featured = $request->featured;

        // Handle single image
        if ($request->hasFile('image')) {
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads/products'), $imageName);
        $product->image = $imageName;
    }

    // Handle multiple gallery images
    if ($request->hasFile('images')) {
        $gallery = [];
        foreach ($request->file('images') as $img) {
            $imageName = time().'_'.$img->getClientOriginalName();
            $img->move(public_path('uploads/products'), $imageName);
            $gallery[] = $imageName;
        }
        $product->images = implode(',', $gallery);
    }

    $product->save();

    return redirect()->route('admin.products', ['subdomain' => $this->currentSubdomain()])->with('success', 'Product updated successfully!');
}


    public function product_delete($id)
    {
        $companyId = $this->currentCompanyId();
        if ($companyId) {
            $product = Product::where('company_id', $companyId)->findOrFail($id);
        } else {
            $product = Product::findOrFail($id);
        }
        if(File::exists(public_path('uploads/products').'/'.$product->image))
        {
            File::delete(public_path('uploads/products').'/'.$product->image);
        }
        if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image))
        {
            File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
        }

        foreach(explode(',',$product->images) as $ofile)
        {
            if(File::exists(public_path('uploads/products').'/'.$ofile))
            {
                File::delete(public_path('uploads/products').'/'.$ofile);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile))
            {
                File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products', ['subdomain' => $this->currentSubdomain()])->with('status', 'Product has been deleted successfully!');
    }

    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }

    public function coupon_add()
    {
        return view('admin.coupon-add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' =>'required',
            'type' =>'required',
            'value' =>'required|numeric',
            'cart_value' =>'required|numeric',
            'expiry_date' =>'required|date',
        ]);

        $coupon = new Coupon();
        $user = Auth::user();
        if ($user && $user->company_id) {
            $coupon->company_id = $user->company_id;
        }
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons', ['subdomain' => $this->currentSubdomain()])->with('status', 'Coupon has been added successfully!');
    }

    public function coupon_edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon'));
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'code' =>'required',
            'type' =>'required',
            'value' =>'required|numeric',
            'cart_value' =>'required|numeric',
            'expiry_date' =>'required|date',
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons', ['subdomain' => $this->currentSubdomain()])->with('status', 'Coupon has been updated successfully!');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons', ['subdomain' => $this->currentSubdomain()])->with('status', 'Coupon has been deleted successfully!');
    }
    public function orders()
    {
        $companyId = $this->currentCompanyId();
        if ($companyId) {
            $orders = Order::whereHas('user', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $orders = Order::orderBy('created_at', 'DESC')->paginate(10);
        }
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        // Use Eloquent with global scope - CompanyScope will automatically filter by current company
        $order = Order::with('user')->findOrFail($order_id);

        // Get order items without product relationship to avoid N+1 and null issues
        $orderItems = OrderItem::where('order_id', $order_id)->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();

        return view('admin.order-details', compact('order', 'orderItems', 'transaction'));
    }
    public function update_order_status(Request $request)
    {
        $order = Order::with('orderItems.product', 'user')->findOrFail($request->order_id);

        $companyId = $this->currentCompanyId();
        if ($companyId && (!$order->user || $order->user->company_id != $companyId)) {
            abort(403, 'Unauthorized access to order');
        }
        $order->status = $request->order_status;

        if ($request->order_status == 'delivered') {
            $order->delivered_date = now();

            // ✅ Deduct stock only once verified as delivered
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                if ($product) {
                    // Prevent negative stock
                    $product->quantity = max(0, $product->quantity - $item->quantity);
                    $product->save();
                }
            }

            // ✅ Approve transaction
            $transaction = $order->transaction;
            if ($transaction) {
                $transaction->status = 'approved';
                $transaction->save();
            }
        } elseif ($request->order_status == 'canceled') {
            $order->canceled_date = now();
        }

        $order->save();

        return back()->with("status", "Order status has been updated successfully!");
    }


    public function slides()
    {
        $slides = Slide::orderBy('id', 'DESC')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    public function slide_add()
    {
        return view('admin.slide-add');
    }
    public function slide_store(Request $request)
    {
        $request->validate([
            'tagline' =>'required',
            'title' =>'required',
            'subtitle' =>'required',
            'link' =>'required',
            'status' =>'required',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $slide = new Slide();
        $user = Auth::user();
        if ($user && $user->company_id) {
            $slide->company_id = $user->company_id;
        }
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateSlideThumbnailsImage($image, $file_name);
        $slide->image =$file_name;
        $slide->save();

        return redirect()->route('admin.slides', ['subdomain' => $this->currentSubdomain()])->with('status', 'Slide has been added successfully!');
    }

    public function GenerateSlideThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/slides');
        $image = Image::read($image->path());

        $image->cover(400, 690);
        $image->save($destinationPath . '/' . $imageName);
    }

    public function slide_edit($id)
    {
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request)
    {
        $request->validate([
            'tagline' =>'required',
            'title' =>'required',
            'subtitle' =>'required',
            'link' =>'required',
           'status' =>'required',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);

        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if($request->hasFile('image'))
        {
            if(File::exists(public_path('uploads/slides').'/'.$slide->image))
            {
                File::delete(public_path('uploads/slides').'/'.$slide->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateSlideThumbnailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('admin.slides', ['subdomain' => $this->currentSubdomain()])->with('status', 'Slide has been updated successfully!');
    }

    public function slide_delete($id)
    {
        $slide = Slide::find($id);
        if(File::exists(public_path('uploads/slides').'/'.$slide->image))
        {
            File::delete(public_path('uploads/slides').'/'.$slide->image);
        }
        $slide->delete();
        return redirect()->route('admin.slides', ['subdomain' => $this->currentSubdomain()])->with('status', 'Slide has been deleted successfully!');
    }
    public function contacts()
    {
        $contacts = Contact::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.contacts', compact('contacts'));
    }
    public function contact_delete($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('admin.contacts', ['subdomain' => $this->currentSubdomain()])->with('status', 'Contact has been deleted successfully!');
    }

    public function posCheckout(Request $request)
{
    $request->validate([
        'cart' => 'required|array',
        'payment_method' => 'required',
        'customer_id' => 'nullable'
    ]);

    $order = new Order();
    $companyId = $this->currentCompanyId();
    $order->customer_id = $request->customer_id ?? null;
    $order->cashier_id = Auth::guard('web')->id();
    // associate order to company via user relation check (orders table doesn't have company_id)
    $order->payment_method = $request->payment_method;
    $order->order_type = 'pos';
    $order->status = 'completed';
    $order->total = collect($request->cart)->sum(function($item){
        return $item['price'] * $item['qty'];
    });
    $order->save();

    foreach($request->cart as $item){
        // ensure product belongs to this company if in tenant context
        $product = Product::find($item['id']);
        if ($companyId && $product && $product->company_id != $companyId) {
            return response()->json(['message' => 'Invalid product for this company'], 403);
        }

        $order->items()->create([
            'product_id' => $item['id'],
            'price' => $item['price'],
            'quantity' => $item['qty'],
            'subtotal' => $item['price'] * $item['qty']
        ]);

        // reduce stock
        if ($product) {
            $product->quantity = max(0, $product->quantity - $item['qty']);
            $product->save();
        }
    }

    return response()->json(['message' => 'POS sale recorded successfully!']);
}

public function pos()
{
    $companyId = $this->currentCompanyId();
    if ($companyId) {
        $products = Product::where('company_id', $companyId)->get();
    } else {
        $products = Product::all(); // fetch all products for global admin
    }

    return view('admin.pos', compact('products'));
}
}
