@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Admin</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Admin</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1"><span id="greeting">Hello</span>, Anna!</h4>
                <p class="text-muted mb-0">Here's what's happening with your store today.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <form action="javascript:void(0);">
                    <div class="row g-3 mb-0 align-items-center">
                        <div class="col-sm-auto">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 dash-filter-picker shadow" data-provider="flatpickr" data-range-date="true" data-date-format="d M, Y" data-deafult-date="01 Jan 2022 to 31 Jan 2022">
                                <div class="input-group-text bg-primary border-primary text-white">
                                    <i class="ri-calendar-2-line"></i>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-auto">
                            <button type="button" class="btn btn-soft-success shadow-none"><i class="ri-add-circle-line align-middle me-1"></i> Add Product</button>
                        </div>
                        <!--end col-->
                        <div class="col-auto">
                            <button type="button" class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn shadow-none"><i class="ri-pulse-line"></i></button>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div><!-- end card header -->
    </div>
</div>

<div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1">
    <div class="col">
        <div class="card">
            <div class="card-body d-flex">
                <div class="flex-grow-1">
                    <h4>4751</h4>
                    <h6 class="text-muted fs-13 mb-0">ICOs Published</h6>
                </div>
                <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-soft-warning text-warning fs-22 rounded">
                        <i class="ri-upload-2-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
    <div class="col">
        <div class="card">
            <div class="card-body d-flex">
                <div class="flex-grow-1">
                    <h4>3423</h4>
                    <h6 class="text-muted fs-13 mb-0">Active ICOs</h6>
                </div>
                <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-soft-success text-success fs-22 rounded">
                        <i class="ri-remote-control-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
    <div class="col">
        <div class="card">
            <div class="card-body d-flex">
                <div class="flex-grow-1">
                    <h4>354</h4>
                    <h6 class="text-muted fs-13 mb-0">ICOs Trading</h6>
                </div>
                <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-soft-info text-info fs-22 rounded">
                        <i class="ri-flashlight-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
    <div class="col">
        <div class="card">
            <div class="card-body d-flex">
                <div class="flex-grow-1">
                    <h4>2762</h4>
                    <h6 class="text-muted fs-13 mb-0">Funded ICOs</h6>
                </div>
                <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-soft-danger text-danger fs-22 rounded">
                        <i class="ri-hand-coin-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
    <div class="col">
        <div class="card">
            <div class="card-body d-flex">
                <div class="flex-grow-1">
                    <h4>1585</h4>
                    <h6 class="text-muted fs-13 mb-0">Upcoming ICO</h6>
                </div>
                <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-soft-primary text-primary fs-22 rounded">
                        <i class="ri-donut-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Best Selling Products</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold text-uppercase fs-12">Sort by:
                            </span><span class="text-muted">Today<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Today</a>
                            <a class="dropdown-item" href="#">Yesterday</a>
                            <a class="dropdown-item" href="#">Last 7 Days</a>
                            <a class="dropdown-item" href="#">Last 30 Days</a>
                            <a class="dropdown-item" href="#">This Month</a>
                            <a class="dropdown-item" href="#">Last Month</a>
                        </div>
                    </div>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                            <img src="assets/images/products/img-1.png" alt="" class="img-fluid d-block">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1"><a href="apps-ecommerce-product-details.html" class="text-reset">Branded T-Shirts</a></h5>
                                            <span class="text-muted">24 Apr 2021</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$29.00</h5>
                                    <span class="text-muted">Price</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">62</h5>
                                    <span class="text-muted">Orders</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">510</h5>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$1,798</h5>
                                    <span class="text-muted">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                            <img src="assets/images/products/img-2.png" alt="" class="img-fluid d-block">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1"><a href="apps-ecommerce-product-details.html" class="text-reset">Bentwood Chair</a></h5>
                                            <span class="text-muted">19 Mar 2021</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$85.20</h5>
                                    <span class="text-muted">Price</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">35</h5>
                                    <span class="text-muted">Orders</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal"><span class="badge badge-soft-danger">Out of stock</span> </h5>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$2982</h5>
                                    <span class="text-muted">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                            <img src="assets/images/products/img-3.png" alt="" class="img-fluid d-block">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1"><a href="apps-ecommerce-product-details.html" class="text-reset">Borosil Paper Cup</a></h5>
                                            <span class="text-muted">01 Mar 2021</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$14.00</h5>
                                    <span class="text-muted">Price</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">80</h5>
                                    <span class="text-muted">Orders</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">749</h5>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$1120</h5>
                                    <span class="text-muted">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                            <img src="assets/images/products/img-4.png" alt="" class="img-fluid d-block">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1"><a href="apps-ecommerce-product-details.html" class="text-reset">One Seater Sofa</a></h5>
                                            <span class="text-muted">11 Feb 2021</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$127.50</h5>
                                    <span class="text-muted">Price</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">56</h5>
                                    <span class="text-muted">Orders</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal"><span class="badge badge-soft-danger">Out of stock</span></h5>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$7140</h5>
                                    <span class="text-muted">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                            <img src="assets/images/products/img-5.png" alt="" class="img-fluid d-block">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1"><a href="apps-ecommerce-product-details.html" class="text-reset">Stillbird Helmet</a></h5>
                                            <span class="text-muted">17 Jan 2021</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$54</h5>
                                    <span class="text-muted">Price</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">74</h5>
                                    <span class="text-muted">Orders</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">805</h5>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 my-1 fw-normal">$3996</h5>
                                    <span class="text-muted">Amount</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="align-items-center mt-4 pt-2 justify-content-between row text-center text-sm-start">
                    <div class="col-sm">
                        <div class="text-muted">
                            Showing <span class="fw-semibold">5</span> of <span class="fw-semibold">25</span> Results
                        </div>
                    </div>
                    <div class="col-sm-auto  mt-3 mt-sm-0">
                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                            <li class="page-item disabled">
                                <a href="#" class="page-link">←</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">1</a>
                            </li>
                            <li class="page-item active">
                                <a href="#" class="page-link">2</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">3</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">→</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Top Sellers</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Download Report</a>
                            <a class="dropdown-item" href="#">Export</a>
                            <a class="dropdown-item" href="#">Import</a>
                        </div>
                    </div>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/companies/img-1.png" alt="" class="avatar-sm p-2">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1 fw-medium">
                                                <a href="apps-ecommerce-seller-details.html" class="text-reset">iTest Factory</a>
                                            </h5>
                                            <span class="text-muted">Oliver Tyler</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">Bags and Wallets</span>
                                </td>
                                <td>
                                    <p class="mb-0">8547</p>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <span class="text-muted">$541200</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 mb-0">32%<i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i></h5>
                                </td>
                            </tr><!-- end -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/companies/img-2.png" alt="" class="avatar-sm p-2">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 my-1 fw-medium"><a href="apps-ecommerce-seller-details.html" class="text-reset">Digitech Galaxy</a></h5>
                                            <span class="text-muted">John Roberts</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">Watches</span>
                                </td>
                                <td>
                                    <p class="mb-0">895</p>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <span class="text-muted">$75030</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 mb-0">79%<i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i></h5>
                                </td>
                            </tr><!-- end -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/companies/img-3.png" alt="" class="avatar-sm p-2">
                                        </div>
                                        <div class="flex-gow-1">
                                            <h5 class="fs-14 my-1 fw-medium"><a href="apps-ecommerce-seller-details.html" class="text-reset">Nesta Technologies</a></h5>
                                            <span class="text-muted">Harley Fuller</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">Bike Accessories</span>
                                </td>
                                <td>
                                    <p class="mb-0">3470</p>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <span class="text-muted">$45600</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 mb-0">90%<i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i></h5>
                                </td>
                            </tr><!-- end -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/companies/img-8.png" alt="" class="avatar-sm p-2">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 my-1 fw-medium"><a href="apps-ecommerce-seller-details.html" class="text-reset">Zoetic Fashion</a></h5>
                                            <span class="text-muted">James Bowen</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">Clothes</span>
                                </td>
                                <td>
                                    <p class="mb-0">5488</p>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <span class="text-muted">$29456</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 mb-0">40%<i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i></h5>
                                </td>
                            </tr><!-- end -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/companies/img-5.png" alt="" class="avatar-sm p-2">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 my-1 fw-medium">
                                                <a href="apps-ecommerce-seller-details.html" class="text-reset">Meta4Systems</a>
                                            </h5>
                                            <span class="text-muted">Zoe Dennis</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">Furniture</span>
                                </td>
                                <td>
                                    <p class="mb-0">4100</p>
                                    <span class="text-muted">Stock</span>
                                </td>
                                <td>
                                    <span class="text-muted">$11260</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 mb-0">57%<i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i></h5>
                                </td>
                            </tr><!-- end -->
                        </tbody>
                    </table><!-- end table -->
                </div>

                <div class="align-items-center mt-4 pt-2 justify-content-between row text-center text-sm-start">
                    <div class="col-sm">
                        <div class="text-muted">
                            Showing <span class="fw-semibold">5</span> of <span class="fw-semibold">25</span> Results
                        </div>
                    </div>
                    <div class="col-sm-auto  mt-3 mt-sm-0">
                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                            <li class="page-item disabled">
                                <a href="#" class="page-link">←</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">1</a>
                            </li>
                            <li class="page-item active">
                                <a href="#" class="page-link">2</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">3</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">→</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div> <!-- .card-body-->
        </div> <!-- .card-->
    </div> <!-- .col-->
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Store Visits by Source</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted">Report<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Download Report</a>
                            <a class="dropdown-item" href="#">Export</a>
                            <a class="dropdown-item" href="#">Import</a>
                        </div>
                    </div>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="store-visits-source" data-colors="[&quot;--vz-primary&quot;, &quot;--vz-success&quot;, &quot;--vz-warning&quot;, &quot;--vz-danger&quot;, &quot;--vz-info&quot;]" class="apex-charts" dir="ltr" style="min-height: 300.7px;"><div id="apexchartsdwzgac12" class="apexcharts-canvas apexchartsdwzgac12 apexcharts-theme-light" style="width: 280px; height: 300.7px;"><svg id="SvgjsSvg2471" width="280" height="300.7" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><foreignObject x="0" y="0" width="280" height="300.7"><div class="apexcharts-legend apexcharts-align-center apx-legend-position-bottom" xmlns="http://www.w3.org/1999/xhtml" style="inset: auto 0px 1px; position: absolute; max-height: 166.5px;"><div class="apexcharts-legend-series" rel="1" seriesname="Direct" data:collapsed="false" style="margin: 2px 5px;"><span class="apexcharts-legend-marker" rel="1" data:collapsed="false" style="background: rgb(75, 56, 179) !important; color: rgb(75, 56, 179); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 12px;"></span><span class="apexcharts-legend-text" rel="1" i="0" data:default-text="Direct" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-weight: 400; font-family: Helvetica, Arial, sans-serif;">Direct</span></div><div class="apexcharts-legend-series" rel="2" seriesname="Social" data:collapsed="false" style="margin: 2px 5px;"><span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="background: rgb(69, 203, 133) !important; color: rgb(69, 203, 133); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 12px;"></span><span class="apexcharts-legend-text" rel="2" i="1" data:default-text="Social" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-weight: 400; font-family: Helvetica, Arial, sans-serif;">Social</span></div><div class="apexcharts-legend-series" rel="3" seriesname="Email" data:collapsed="false" style="margin: 2px 5px;"><span class="apexcharts-legend-marker" rel="3" data:collapsed="false" style="background: rgb(255, 190, 11) !important; color: rgb(255, 190, 11); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 12px;"></span><span class="apexcharts-legend-text" rel="3" i="2" data:default-text="Email" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-weight: 400; font-family: Helvetica, Arial, sans-serif;">Email</span></div><div class="apexcharts-legend-series" rel="4" seriesname="Other" data:collapsed="false" style="margin: 2px 5px;"><span class="apexcharts-legend-marker" rel="4" data:collapsed="false" style="background: rgb(240, 101, 72) !important; color: rgb(240, 101, 72); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 12px;"></span><span class="apexcharts-legend-text" rel="4" i="3" data:default-text="Other" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-weight: 400; font-family: Helvetica, Arial, sans-serif;">Other</span></div><div class="apexcharts-legend-series" rel="5" seriesname="Referrals" data:collapsed="false" style="margin: 2px 5px;"><span class="apexcharts-legend-marker" rel="5" data:collapsed="false" style="background: rgb(41, 156, 219) !important; color: rgb(41, 156, 219); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 12px;"></span><span class="apexcharts-legend-text" rel="5" i="4" data:default-text="Referrals" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-weight: 400; font-family: Helvetica, Arial, sans-serif;">Referrals</span></div></div><style type="text/css">	

.apexcharts-legend {	
display: flex;	
overflow: auto;	
padding: 0 10px;	
}	
.apexcharts-legend.apx-legend-position-bottom, .apexcharts-legend.apx-legend-position-top {	
flex-wrap: wrap	
}	
.apexcharts-legend.apx-legend-position-right, .apexcharts-legend.apx-legend-position-left {	
flex-direction: column;	
bottom: 0;	
}	
.apexcharts-legend.apx-legend-position-bottom.apexcharts-align-left, .apexcharts-legend.apx-legend-position-top.apexcharts-align-left, .apexcharts-legend.apx-legend-position-right, .apexcharts-legend.apx-legend-position-left {	
justify-content: flex-start;	
}	
.apexcharts-legend.apx-legend-position-bottom.apexcharts-align-center, .apexcharts-legend.apx-legend-position-top.apexcharts-align-center {	
justify-content: center;  	
}	
.apexcharts-legend.apx-legend-position-bottom.apexcharts-align-right, .apexcharts-legend.apx-legend-position-top.apexcharts-align-right {	
justify-content: flex-end;	
}	
.apexcharts-legend-series {	
cursor: pointer;	
line-height: normal;	
}	
.apexcharts-legend.apx-legend-position-bottom .apexcharts-legend-series, .apexcharts-legend.apx-legend-position-top .apexcharts-legend-series{	
display: flex;	
align-items: center;	
}	
.apexcharts-legend-text {	
position: relative;	
font-size: 14px;	
}	
.apexcharts-legend-text *, .apexcharts-legend-marker * {	
pointer-events: none;	
}	
.apexcharts-legend-marker {	
position: relative;	
display: inline-block;	
cursor: pointer;	
margin-right: 3px;	
border-style: solid;
}	

.apexcharts-legend.apexcharts-align-right .apexcharts-legend-series, .apexcharts-legend.apexcharts-align-left .apexcharts-legend-series{	
display: inline-block;	
}	
.apexcharts-legend-series.apexcharts-no-click {	
cursor: auto;	
}	
.apexcharts-legend .apexcharts-hidden-zero-series, .apexcharts-legend .apexcharts-hidden-null-series {	
display: none !important;	
}	
.apexcharts-inactive-legend {	
opacity: 0.45;	
}</style></foreignObject><g id="SvgjsG2473" class="apexcharts-inner apexcharts-graphical" transform="translate(12, 0)"><defs id="SvgjsDefs2472"><clipPath id="gridRectMaskdwzgac12"><rect id="SvgjsRect2475" width="264" height="251" x="-3" y="-1" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMaskdwzgac12"></clipPath><clipPath id="nonForecastMaskdwzgac12"></clipPath><clipPath id="gridRectMarkerMaskdwzgac12"><rect id="SvgjsRect2476" width="262" height="253" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath></defs><g id="SvgjsG2477" class="apexcharts-pie"><g id="SvgjsG2478" transform="translate(0, 0) scale(1)"><circle id="SvgjsCircle2479" r="75.05121951219512" cx="129" cy="124.5" fill="transparent"></circle><g id="SvgjsG2480" class="apexcharts-slices"><g id="SvgjsG2481" class="apexcharts-series apexcharts-pie-series" seriesName="Direct" rel="1" data:realIndex="0"><path id="SvgjsPath2482" d="M 129 9.036585365853654 A 115.46341463414635 115.46341463414635 0 0 1 244.38638302889547 128.7169574915843 L 204.00114896878205 127.2410223695298 A 75.05121951219512 75.05121951219512 0 0 0 129 49.44878048780488 L 129 9.036585365853654 z" fill="rgba(75,56,179,1)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-0" index="0" j="0" data:angle="92.09302325581395" data:startAngle="0" data:strokeWidth="0" data:value="44" data:pathOrig="M 129 9.036585365853654 A 115.46341463414635 115.46341463414635 0 0 1 244.38638302889547 128.7169574915843 L 204.00114896878205 127.2410223695298 A 75.05121951219512 75.05121951219512 0 0 0 129 49.44878048780488 L 129 9.036585365853654 z"></path></g><g id="SvgjsG2485" class="apexcharts-series apexcharts-pie-series" seriesName="Social" rel="2" data:realIndex="1"><path id="SvgjsPath2486" d="M 244.38638302889547 128.7169574915843 A 115.46341463414635 115.46341463414635 0 0 1 76.20524003599613 227.186481288045 L 94.68340602339748 191.24621283722922 A 75.05121951219512 75.05121951219512 0 0 0 204.00114896878205 127.2410223695298 L 244.38638302889547 128.7169574915843 z" fill="rgba(69,203,133,1)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-1" index="0" j="1" data:angle="115.11627906976746" data:startAngle="92.09302325581395" data:strokeWidth="0" data:value="55" data:pathOrig="M 244.38638302889547 128.7169574915843 A 115.46341463414635 115.46341463414635 0 0 1 76.20524003599613 227.186481288045 L 94.68340602339748 191.24621283722922 A 75.05121951219512 75.05121951219512 0 0 0 204.00114896878205 127.2410223695298 L 244.38638302889547 128.7169574915843 z"></path></g><g id="SvgjsG2489" class="apexcharts-series apexcharts-pie-series" seriesName="Email" rel="3" data:realIndex="2"><path id="SvgjsPath2490" d="M 76.20524003599613 227.186481288045 A 115.46341463414635 115.46341463414635 0 0 1 22.733686999599982 79.3417134926732 L 59.92689654973999 95.14711377023758 A 75.05121951219512 75.05121951219512 0 0 0 94.68340602339748 191.24621283722922 L 76.20524003599613 227.186481288045 z" fill="rgba(255,190,11,1)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-2" index="0" j="2" data:angle="85.81395348837208" data:startAngle="207.2093023255814" data:strokeWidth="0" data:value="41" data:pathOrig="M 76.20524003599613 227.186481288045 A 115.46341463414635 115.46341463414635 0 0 1 22.733686999599982 79.3417134926732 L 59.92689654973999 95.14711377023758 A 75.05121951219512 75.05121951219512 0 0 0 94.68340602339748 191.24621283722922 L 76.20524003599613 227.186481288045 z"></path></g><g id="SvgjsG2493" class="apexcharts-series apexcharts-pie-series" seriesName="Other" rel="4" data:realIndex="3"><path id="SvgjsPath2494" d="M 22.733686999599982 79.3417134926732 A 115.46341463414635 115.46341463414635 0 0 1 68.85044946658867 25.941227231639104 L 89.90279215328263 60.43679770056542 A 75.05121951219512 75.05121951219512 0 0 0 59.92689654973999 95.14711377023758 L 22.733686999599982 79.3417134926732 z" fill="rgba(240,101,72,1)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-3" index="0" j="3" data:angle="35.58139534883719" data:startAngle="293.0232558139535" data:strokeWidth="0" data:value="17" data:pathOrig="M 22.733686999599982 79.3417134926732 A 115.46341463414635 115.46341463414635 0 0 1 68.85044946658867 25.941227231639104 L 89.90279215328263 60.43679770056542 A 75.05121951219512 75.05121951219512 0 0 0 59.92689654973999 95.14711377023758 L 22.733686999599982 79.3417134926732 z"></path></g><g id="SvgjsG2497" class="apexcharts-series apexcharts-pie-series" seriesName="Referrals" rel="5" data:realIndex="4"><path id="SvgjsPath2498" d="M 68.85044946658867 25.941227231639104 A 115.46341463414635 115.46341463414635 0 0 1 128.97984783259267 9.036587124462017 L 128.98690109118525 49.448781630900314 A 75.05121951219512 75.05121951219512 0 0 0 89.90279215328263 60.43679770056542 L 68.85044946658867 25.941227231639104 z" fill="rgba(41,156,219,1)" fill-opacity="1" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-4" index="0" j="4" data:angle="31.395348837209326" data:startAngle="328.6046511627907" data:strokeWidth="0" data:value="15" data:pathOrig="M 68.85044946658867 25.941227231639104 A 115.46341463414635 115.46341463414635 0 0 1 128.97984783259267 9.036587124462017 L 128.98690109118525 49.448781630900314 A 75.05121951219512 75.05121951219512 0 0 0 89.90279215328263 60.43679770056542 L 68.85044946658867 25.941227231639104 z"></path></g><g id="SvgjsG2483" class="apexcharts-datalabels"><text id="SvgjsText2484" font-family="Helvetica, Arial, sans-serif" x="197.57607346184244" y="58.384354313562284" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="600" fill="#ffffff" class="apexcharts-text apexcharts-pie-label" style="font-family: Helvetica, Arial, sans-serif;">25.6%</text></g><g id="SvgjsG2487" class="apexcharts-datalabels"><text id="SvgjsText2488" font-family="Helvetica, Arial, sans-serif" x="177.1300333272378" y="206.70374898931047" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="600" fill="#ffffff" class="apexcharts-text apexcharts-pie-label" style="font-family: Helvetica, Arial, sans-serif;">32.0%</text></g><g id="SvgjsG2491" class="apexcharts-datalabels"><text id="SvgjsText2492" font-family="Helvetica, Arial, sans-serif" x="39.42146704611237" y="156.89819269354157" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="600" fill="#ffffff" class="apexcharts-text apexcharts-pie-label" style="font-family: Helvetica, Arial, sans-serif;">23.8%</text></g><g id="SvgjsG2495" class="apexcharts-datalabels"><text id="SvgjsText2496" font-family="Helvetica, Arial, sans-serif" x="56.90584242830194" y="62.239347096213706" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="600" fill="#ffffff" class="apexcharts-text apexcharts-pie-label" style="font-family: Helvetica, Arial, sans-serif;">9.9%</text></g><g id="SvgjsG2499" class="apexcharts-datalabels"><text id="SvgjsText2500" font-family="Helvetica, Arial, sans-serif" x="103.22704969420987" y="32.795520891758756" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="600" fill="#ffffff" class="apexcharts-text apexcharts-pie-label" style="font-family: Helvetica, Arial, sans-serif;">8.7%</text></g></g></g></g><line id="SvgjsLine2501" x1="0" y1="0" x2="258" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine2502" x1="0" y1="0" x2="258" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line></g><g id="SvgjsG2474" class="apexcharts-annotations"></g></svg><div class="apexcharts-tooltip apexcharts-theme-dark"><div class="apexcharts-tooltip-series-group" style="order: 1;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(75, 56, 179);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group" style="order: 2;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(69, 203, 133);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group" style="order: 3;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(255, 190, 11);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group" style="order: 4;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(240, 101, 72);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group" style="order: 5;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(41, 156, 219);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div></div></div>
            </div>
        </div> <!-- .card-->
    </div> <!-- .col-->

    <div class="col-xl-8">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Recent Orders</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-soft-info btn-sm shadow-none">
                        <i class="ri-file-list-3-line align-middle"></i> Generate Report
                    </button>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">Order ID</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Product</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Status</th>
                                <th scope="col">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#VZ2112</a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle shadow">
                                        </div>
                                        <div class="flex-grow-1">Alex Smith</div>
                                    </div>
                                </td>
                                <td>Clothes</td>
                                <td>
                                    <span class="text-success">$109.00</span>
                                </td>
                                <td>Zoetic Fashion</td>
                                <td>
                                    <span class="badge badge-soft-success">Paid</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 fw-medium mb-0">5.0<span class="text-muted fs-11 ms-1">(61 votes)</span></h5>
                                </td>
                            </tr><!-- end tr -->
                            <tr>
                                <td>
                                    <a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#VZ2111</a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle shadow">
                                        </div>
                                        <div class="flex-grow-1">Jansh Brown</div>
                                    </div>
                                </td>
                                <td>Kitchen Storage</td>
                                <td>
                                    <span class="text-success">$149.00</span>
                                </td>
                                <td>Micro Design</td>
                                <td>
                                    <span class="badge badge-soft-warning">Pending</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 fw-medium mb-0">4.5<span class="text-muted fs-11 ms-1">(61 votes)</span></h5>
                                </td>
                            </tr><!-- end tr -->
                            <tr>
                                <td>
                                    <a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#VZ2109</a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle shadow">
                                        </div>
                                        <div class="flex-grow-1">Ayaan Bowen</div>
                                    </div>
                                </td>
                                <td>Bike Accessories</td>
                                <td>
                                    <span class="text-success">$215.00</span>
                                </td>
                                <td>Nesta Technologies</td>
                                <td>
                                    <span class="badge badge-soft-success">Paid</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 fw-medium mb-0">4.9<span class="text-muted fs-11 ms-1">(89 votes)</span></h5>
                                </td>
                            </tr><!-- end tr -->
                            <tr>
                                <td>
                                    <a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#VZ2108</a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/users/avatar-4.jpg" alt="" class="avatar-xs rounded-circle shadow">
                                        </div>
                                        <div class="flex-grow-1">Prezy Mark</div>
                                    </div>
                                </td>
                                <td>Furniture</td>
                                <td>
                                    <span class="text-success">$199.00</span>
                                </td>
                                <td>Syntyce Solutions</td>
                                <td>
                                    <span class="badge badge-soft-danger">Unpaid</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 fw-medium mb-0">4.3<span class="text-muted fs-11 ms-1">(47 votes)</span></h5>
                                </td>
                            </tr><!-- end tr -->
                            <tr>
                                <td>
                                    <a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#VZ2107</a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle shadow">
                                        </div>
                                        <div class="flex-grow-1">Vihan Hudda</div>
                                    </div>
                                </td>
                                <td>Bags and Wallets</td>
                                <td>
                                    <span class="text-success">$330.00</span>
                                </td>
                                <td>iTest Factory</td>
                                <td>
                                    <span class="badge badge-soft-success">Paid</span>
                                </td>
                                <td>
                                    <h5 class="fs-14 fw-medium mb-0">4.7<span class="text-muted fs-11 ms-1">(161 votes)</span></h5>
                                </td>
                            </tr><!-- end tr -->
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div>
        </div> <!-- .card-->
    </div> <!-- .col-->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row g-4 align-items-center">
                    <div class="col-sm-auto">
                        <div>
                            <h4 class="card-title mb-0 flex-grow-1">Recomended Jobs</h4>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="d-flex justify-content-sm-end">
                            <div class="search-box ms-2">
                                <input type="text" class="form-control" id="searchResultList" placeholder="Search for jobs...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
            </div>
        </div>
    </div><!--end col-->
</div>
@endsection
