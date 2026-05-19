<div class="main-content-inner">

    <div class="main-content-wrap">
        <div class="tf-section-2 mb-30">
            <div class="flex gap20 flex-wrap-mobile">
                <div class="w-half">

                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Total Orders</div>
                                    <h4>{{$dashboardDatas->TotalOrders}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Total Amount</div>
                                    <h4>Ksh{{ number_format($dashboardDatas->TotalAmount ?? $dashboardDatas->TotalSales ?? 0, 2) }}</h4>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Pending Orders</div>
                                    <h4>{{$dashboardDatas->TotalOrdered}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Pending Orders Amount</div>
                                    <h4>Ksh{{ number_format($dashboardDatas->TotalOrderedAmount, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="w-half">

                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Delivered Orders</div>
                                    <h4>{{$dashboardDatas->TotalDelivered}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Delivered Orders Amount</div>
                                    <h4>Ksh{{ number_format($dashboardDatas->TotalDeliveredAmount, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Canceled Orders</div>
                                    <h4>{{$dashboardDatas->TotalCanceled}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Canceled Orders Amount</div>
                                    <h4>Ksh{{ number_format($dashboardDatas->TotalCanceledAmount, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Monthly revenue</h5>
                </div>
                <div class="flex flex-wrap gap40">
                    <div>
                        <div class="mb-2">
                            <div class="block-legend">
                                <div class="dot t1"></div>
                                <div class="text-tiny">Total</div>
                            </div>
                        </div>
                        <div class="flex items-center gap10">
                            <h4>Ksh{{ number_format($TotalAmount, 2) }}</h4>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2">
                            <div class="block-legend">
                                <div class="dot t2"></div>
                                <div class="text-tiny">Pending</div>
                            </div>
                        </div>
                        <div class="flex items-center gap10">
                            <h4>Ksh{{ number_format($TotalOrderedAmount, 2) }}</h4>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2">
                            <div class="block-legend">
                                <div class="dot t2"></div>
                                <div class="text-tiny">Delivered</div>
                            </div>
                        </div>
                        <div class="flex items-center gap10">
                            <h4>Ksh{{ number_format($TotalDeliveredAmount, 2) }}</h4>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2">
                            <div class="block-legend">
                                <div class="dot t2"></div>
                                <div class="text-tiny">canceled</div>
                            </div>
                        </div>
                        <div class="flex items-center gap10">
                            <h4>Ksh{{ number_format($TotalCanceledAmount, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div id="line-chart-8"></div>
            </div>

        </div>

        {{-- additional summary widgets --}}
        @isset($topProducts)
            <div class="tf-section mb-30">
                <div class="wg-box">
                    <h5>Top Selling Products</h5>
                    <ol class="list-decimal pl-5">
                        @foreach($topProducts as $item)
                            <li>{{ $item->product->name ?? 'Unknown' }} – {{ $item->sold }} sold</li>
                        @endforeach
                        @if($topProducts->isEmpty())
                            <li class="text-muted">No sales yet.</li>
                        @endif
                    </ol>
                </div>
            </div>
        @endisset

        @isset($lowStock)
            <div class="tf-section mb-30">
                <div class="wg-box">
                    <h5>Low Stock Items</h5>
                    <ul class="list-disc pl-5">
                        @foreach($lowStock as $p)
                            <li>{{ $p->name }} ({{ $p->stock_quantity }} remaining)</li>
                        @endforeach
                        @if($lowStock->isEmpty())
                            <li class="text-muted">All products are sufficiently stocked.</li>
                        @endif
                    </ul>
                </div>
            </div>
        @endisset

        @isset($cashierRanking)
            <div class="tf-section mb-30">
                <div class="wg-box">
                    <h5>Top Cashiers (by revenue)</h5>
                    <ol class="list-decimal pl-5">
                        @foreach($cashierRanking as $row)
                            <li>{{ $row->cashier->name ?? 'Unknown' }} – Ksh {{ number_format($row->revenue,0) }} ({{ $row->txs }} txs)</li>
                        @endforeach
                        @if($cashierRanking->isEmpty())
                            <li class="text-muted">No cashier activity yet.</li>
                        @endif
                    </ol>
                </div>
            </div>
        @endisset

        {{-- continue into recent orders --}}
        <div class="tf-section mb-30">

            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Recent orders</h5>
                    <div class="dropdown default">
                        <a class="btn btn-secondary dropdown-toggle" href="{{adminRoute('admin.orders', ['subdomain' => Auth::user()->company->slug])}}">
                            <span class="view-all">View all</span>
                        </a>
                    </div>
                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:70px">OrderNo</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Subtotal</th>

                                    <th class="text-center">Total</th>

                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Total Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td class="text-center">{{$order->id}}</td>
                                    <td class="text-center">{{$order->name}}</td>
                                    <td class="text-center">{{$order->phone}}</td>
                                    <td class="text-center">Ksh{{$order->subtotal}}</td>

                                    <td class="text-center">Ksh{{$order->total}}</td>

                                    <td class="text-center">
                                        @if($order->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($order->status == 'canceled')
                                            <span class="badge bg-danger">Canceled</span>
                                        @else
                                            <span class="badge bg-warning">Ordered</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{$order->created_at}}</td>
                                    <td class="text-center">{{$order->items_count}}</td>
                                    <td class="text-center">{{$order->delivered_date}}</td>
                                    <td class="text-center">
                                        <a href="{{adminRoute('admin.order.details',['subdomain' => Auth::user()->company->slug, 'order_id'=>$order->id])}}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

                    <!-- Settings Module Card -->
                <div class="w-full">
                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-settings"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2 font-semibold">Company Settings</div>
                                    <p class="text-body-text-2">Manage branding, payments, communication & business settings</p>
                                </div>
                            </div>
                            <a href="{{ adminRoute('admin.settings') }}" class="tf-button style-1">
                                <i class="icon-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
</div>

@push('scripts')
<script>
    (function (Ksh) {

        var tfLineChart = (function () {

            var chartBar = function () {

                var options = {
                    series: [{
                        name: 'Total',
                        data: @json($AmountM)
                    }, {
                        name: 'Pending',
                        data: @json($OrderedAmountM)
                    },
                    {
                        name: 'Delivered',
                        data: @json($DeliveredAmountM)
                    }, {
                        name: 'Canceled',
                        data: @json($CanceledAmountM)
                    }],
                    chart: {
                        type: 'bar',
                        height: 325,
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '10px',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false,
                    },
                    colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
                    stroke: {
                        show: false,
                    },
                    xaxis: {
                        labels: {
                            style: {
                                colors: '#212529',
                            },
                        },
                        categories: @json($monthLabels),
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return "ksh " + val.toFixed(2)
                            }
                        }
                    }
                };

                chart = new ApexCharts(
                    document.querySelector("#line-chart-8"),
                    options
                );
                if ($("#line-chart-8").length > 0) {
                    chart.render();
                }
            };

            /* Function ============ */
            return {
                init: function () { },

                load: function () {
                    chartBar();
                },
                resize: function () { },
            };
        })();

        jQuery(document).ready(function () { });

        jQuery(window).on("load", function () {
            tfLineChart.load();
        });

        jQuery(window).on("resize", function () { });
    })(jQuery);
</script>
@endpush
