<?php session_start();

include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$function = new custom_functions;

// set time for session timeout
$currentTime = time() + 25200;
$expired = 3600;
// if session not set go to login page
if (!isset($_SESSION['user'])) {
    header("location:index.php");
}
// if current time is more than session timeout back to login page
if ($currentTime > $_SESSION['timeout']) {
    session_destroy();
    header("location:index.php");
}
// destroy previous session timeout and create new one
unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;
$function = new custom_functions;
$permissions = $function->get_permissions($_SESSION['id']);


include "header.php";
$low_stock_limit = isset($config['low-stock-limit']) && (!empty($config['low-stock-limit'])) ? $config['low-stock-limit'] : 0;
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= $settings['app_name'] ?> - Dashboard</title>
</head>

<body>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Home</h1>
            <ol class="breadcrumb">
                <li>
                    <a href="home.php"> <i class="fa fa-home"></i> Home</a>
                </li>
            </ol>

        </section>

        <section class="content">
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3><?= $function->rows_count('orders', "id"); ?></h3>
                            <p>Orders</p>
                        </div>
                        <div class="icon"><i class="fa fa-shopping-cart"></i></div>
                        <a href="orders.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3><?= $function->rows_count('products', "id"); ?></h3>
                            <p>Products</p>
                        </div>
                        <div class="icon"><i class="fa fa-cubes"></i></div>
                        <a href="products.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
               
           
            </div>
             
             <?php if ($permissions['orders']['read'] == 1) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Latest Orders</h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">

                                <div class="table-responsive">
                                    <table class="table no-margin" id='orders_table' data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=orders" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-toolbar="#toolbar" data-show-export="true" data-export-types='["txt","excel"]' data-export-options='{"fileName": "orders-list-<?= date('d-m-Y') ?>","ignoreColumn": ["operate"] }' data-query-params="queryParams">
                                        <thead>
                                            <tr>
                                                <th data-field="id" data-sortable='true'>O.ID</th>
                                                <th data-field="user_id" data-sortable='true' data-visible="false">User ID</th>
                                                <th data-field="qty" data-sortable='true' data-visible="false">Qty</th>
                                                <th data-field="name" data-sortable='true'>U.Name</th>
                                                <th data-field="mobile" data-sortable='true' data-visible="true">Mob.</th>
                                                <th data-field="order_note" data-sortable='true' data-visible="false">Order Note</th>
                                                <th data-field="items" data-sortable='true' data-visible="false">Items</th>
                                                <th data-field="total" data-sortable='true' data-visible="true">Total(<?= $settings['currency'] ?>)</th>
                                                <th data-field="delivery_charge" data-sortable='true'>D.Chrg</th>
                                                <th data-field="tax" data-sortable='false'>Tax <?= $settings['currency'] ?>(%)</th>
                                                <th data-field="discount" data-sortable='true' data-visible="true">Disc.<?= $settings['currency'] ?>(%)</th>
                                                <th data-field="promo_code" data-sortable='true' data-visible="false">Promo Code</th>
                                                <th data-field="promo_discount" data-sortable='true' data-visible="true">Promo Disc.(<?= $settings['currency'] ?>)</th>
                                                <th data-field="wallet_balance" data-sortable='true' data-visible="false">Wallet Used(<?= $settings['currency'] ?>)</th>
                                                <th data-field="final_total" data-sortable='true'>F.Total(<?= $settings['currency'] ?>)</th>
                                                <th data-field="payment_method" data-sortable='true' data-visible="true">P.Method</th>
                                                <th data-field="address" data-sortable='true' data-visible="false">Address</th>
                                                <th data-field="delivery_time" data-sortable='true' data-visible='true'>D.Time</th>
                                                <th data-field="date_added" data-sortable='true' data-visible="false">O.Date</th>
                                                <th data-field="operate">Action</th>


                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="box-footer clearfix">
                                <a href="orders.php" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="alert alert-danger">You have no permission to view orders.</div>
            <?php } ?>




           
        </section>
        
    </div>
    <script>
        // $('#filter_order').on('change', function() {
        //     $('#orders_table').bootstrapTable('refresh');
        // });
    </script>
    <script>
        function queryParams(p) {
            return {
                // "filter_order": $('#filter_order').val(),
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }
        function queryParams_top_seller(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset
            };
        }
        function queryParams_top_cat(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset
            };
        }
    </script>
    <?php include "footer.php"; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawPieChart);

        function drawPieChart() {

            var data1 = google.visualization.arrayToDataTable([
                ['Product', 'Count'],
                <?php
                foreach ($result_products as $row) {
                    echo "['" . $db->escapeString($row['name']) . "'," . $row['product_count'] . "],";
                }
                ?>
            ]);

            var options1 = {
                title: 'Product Category Count',
                is3D: true
            };

            var chart1 = new google.visualization.PieChart(document.getElementById('piechart'));

            chart1.draw(data1, options1);
        }
    </script>

    <script>
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Date', 'Total Sale In <?= $settings['currency'] ?>'],
                <?php foreach ($result_order as $row) {
                    $date = date('d-M', strtotime($row['order_date']));
                    echo "['" . $date . "'," . $row['total_sale'] . "],";
                } ?>
            ]);
            var options = {
                chart: {
                    title: 'Weekly Sales',
                    subtitle: 'Total Sale In Last Week (Month: <?php echo date("M"); ?>)',
                }
            };
            var chart = new google.charts.Bar(document.getElementById('earning_chart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>
</body>

</html>
