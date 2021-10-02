<?php
include_once('includes/functions.php');
include_once('includes/custom-functions.php');
$fn = new custom_functions;
// include_once('includes/crud.php');
$function = new Functions;
// $db = new Database();
if (isset($_GET['id'])) {
    $ID = $db->escapeString($fn->xss_clean($_GET['id']));
} else {
    // $ID = "";
    return false;
    exit(0);
}
// create array variable to store category data
$category_data = array();
$product_status = "";
$sql = "select id,name from category order by id asc";
$db->sql($sql);
$category_data = $db->getResult();
$sql = "select * from subcategory";
$db->sql($sql);
$subcategory = $db->getResult();
$sql = "SELECT image, other_images FROM products WHERE id =" . $ID;
$db->sql($sql);
$res = $db->getResult();
foreach ($res as $row) {
    $previous_menu_image = $row['image'];
    $other_images = $row['other_images'];
}
if (isset($_POST['btnEdit'])) {
    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        echo '<label class="alert alert-danger">This operation is not allowed in demo panel!.</label>';
        return false;
    }
    if ($permissions['products']['update'] == 1) {
        $error = array();
        $name = $fn->xss_clean($_POST['name']);
        if (strpos($name, '-') !== false) {
            $temp = (explode("-", $name)[1]);
        } else {
            $temp = $name;
        }

        $slug = $function->slugify($temp);
        $id = $db->escapeString($fn->xss_clean($_GET['id']));
        $sql = "SELECT slug FROM products where id!=" . $id;
        $db->sql($sql);
        $res = $db->getResult();
        $i = 1;
        foreach ($res as $row) {
            if ($slug == $row['slug']) {
                $slug = $slug . '-' . $i;
                $i++;
            }
        }
       /* $pincode_type = (isset($_POST['product_pincodes']) && $_POST['product_pincodes'] != '') ? $db->escapeString($fn->xss_clean($_POST['product_pincodes'])) : "";
        if ($pincode_type == "all") {
            $pincode_ids = NULL;
        } else {
            if(empty($_POST['pincode_ids_exc'])){
                $error['pincode_ids_exc'] = "<label class='alert alert-danger'>Select pincodes!.</label>";
            }else{
                $pincode_ids = $fn->xss_clean_array($_POST['pincode_ids_exc']);
                $pincode_ids = implode(",", $pincode_ids);   
            }
        }*/

        $subcategory_id = (isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['subcategory_id'])) : 0;
        $category_id = $db->escapeString($fn->xss_clean($_POST['category_id']));
        $seller_id = $db->escapeString($fn->xss_clean($_POST['seller_id']));
        $serve_for = $db->escapeString($fn->xss_clean($_POST['packate_serve_for']));
        $description = $db->escapeString($fn->xss_clean($_POST['description']));
        $pr_status = $db->escapeString($fn->xss_clean($_POST['pr_status']));
       // $manufacturer = (isset($_POST['manufacturer']) && $_POST['manufacturer'] != '') ? $db->escapeString($fn->xss_clean($_POST['manufacturer'])) : '';
       // $made_in = (isset($_POST['made_in']) && $_POST['made_in'] != '') ? $db->escapeString($fn->xss_clean($_POST['made_in'])) : '';
        $indicator = (isset($_POST['indicator']) && $_POST['indicator'] != '') ? $db->escapeString($fn->xss_clean($_POST['indicator'])) : '0';
       // $return_status = (isset($_POST['return_status']) && $_POST['return_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['return_status'])) : '0';
       // $return_days = (isset($_POST['return_days']) && $_POST['return_days'] != '') ? $db->escapeString($fn->xss_clean($_POST['return_days'])) : 0;
       // $cancelable_status = (isset($_POST['cancelable_status']) && $_POST['cancelable_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['cancelable_status'])) : '0';
        //$till_status = (isset($_POST['till_status']) && $_POST['till_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['till_status'])) : '';
       // $is_approved = (isset($_POST['is_approved']) && $_POST['is_approved'] != '') ? $db->escapeString($fn->xss_clean($_POST['is_approved'])) : '1';

        $tax_id = (isset($_POST['tax_id']) && $_POST['tax_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['tax_id'])) : 0;

        // get image info
        $image = $db->escapeString($fn->xss_clean($_FILES['image']['name']));
        $image_error = $db->escapeString($fn->xss_clean($_FILES['image']['error']));
        $image_type = $db->escapeString($fn->xss_clean($_FILES['image']['type']));
        

        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
       // if ($cancelable_status == 1 && $till_status == '') {
         //   $error['cancelable'] = " <span class='label label-danger'>Required!</span>";
       // }

        if (empty($category_id)) {
            $error['category_id'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($serve_for)) {
            $error['serve_for'] = " <span class='label label-danger'>Not choosen</span>";
        }

       // if (empty($description)) {
         //   $error['description'] = " <span class='label label-danger'>Required!</span>";
       // }


        // common image file extensions
        $allowedExts = array("gif", "jpeg", "jpg", "png");

        // get image file extension
        error_reporting(E_ERROR | E_PARSE);
        $extension = end(explode(".", $_FILES["image"]["name"]));

        if (!empty($image)) {
            // $mimetype = mime_content_type($_FILES["image"]["tmp_name"]);
            // if (!in_array($mimetype, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))) {
            //     $error['image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
            // }
            $result = $fn->validate_image($_FILES["image"]);
            if ($result) {
                $error['image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
            }
        }
        /*updating other_images if any*/

        if (isset($_FILES['other_images']) && ($_FILES['other_images']['size'][0] > 0)) {
            $file_data = array();
            $target_path = 'upload/other_images/';
            for ($i = 0; $i < count($_FILES["other_images"]["name"]); $i++) {
                if ($_FILES["other_images"]["error"][$i] > 0) {
                    $error['other_images'] = " <span class='label label-danger'>Images not uploaded!</span>";
                } else {
                    // $mimetype = mime_content_type($_FILES["other_images"]["tmp_name"][$i]);
                    // if (!in_array($mimetype, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))) {
                    //     $error['other_images'] = " <span class='label label-danger'>Images type must jpg, jpeg, gif, or png!</span>";
                    // }
                    $result = $fn->validate_other_images($_FILES["other_images"]["tmp_name"][$i], $_FILES["other_images"]["type"][$i]);
                    if ($result) {
                        $error['other_images'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
                    }
                }
                $filename = $_FILES["other_images"]["name"][$i];
                $temp = explode('.', $filename);
                $filename = microtime(true) . '-' . rand(100, 999) . '.' . end($temp);
                $file_data[] = $target_path . '' . $filename;
                if (!move_uploaded_file($_FILES["other_images"]["tmp_name"][$i], $target_path . '' . $filename))
                    echo "{$_FILES['image']['name'][$i]} not uploaded<br/>";
            }
            if (!empty($other_images)) {
                $arr_old_images = json_decode($other_images);
                $all_images = array_merge($arr_old_images, $file_data);
                $all_images = json_encode(array_values($all_images));
            } else {
                $all_images = json_encode($file_data);
            }
            if (empty($error)) {
                $sql = "update `products` set `other_images`='" . $all_images . "' where `id`=" . $ID;
                $db->sql($sql);
            }
        }
        if (!empty($name) && !empty($category_id) &&  !empty($serve_for)  && empty($error)) {
            if (strpos($name, "'") !== false) {
                $name = str_replace("'", "''", "$name");
                if (strpos($description, "'") !== false)
                    $description = str_replace("'", "''", "$description");
            }
            if (!empty($image)) {
                // create random image file name
                $string = '0123456789';
                $file = preg_replace("/\s+/", "_", $_FILES['image']['name']);
                $function = new functions;
                $image = $function->get_random_string($string, 4) . "-" . date("Y-m-d") . "." . $extension;
                // delete previous image
                $delete = unlink("$previous_menu_image");
                // upload new image
                $upload = move_uploaded_file($_FILES['image']['tmp_name'], 'upload/images/' . $image);

                $upload_image = 'upload/images/' . $image;
                $sql_query = "UPDATE products SET name = '$name' ,tax_id = '$tax_id' ,seller_id = '$seller_id' ,slug = '$slug' , subcategory_id = '$subcategory_id', image = '$upload_image', description = '$description', indicator = '$indicator', manufacturer = '', made_in = '',`status` = $pr_status WHERE id = $ID";
            } 
           /* else if ($pincode_type != "") {
                $sql_query = "UPDATE products SET name = '$name',type= '$pincode_type',pincodes = '$pincode_ids',tax_id = '$tax_id' ,seller_id = '$seller_id' ,slug = '$slug' ,category_id = '$category_id' ,subcategory_id = '$subcategory_id' ,description = '$description', indicator = '$indicator', manufacturer = '', made_in = '', `status` = $pr_status WHERE id = $ID";
            } */
            else {
                $sql_query = "UPDATE products SET name = '$name' ,tax_id = '$tax_id' ,seller_id = '$seller_id' ,slug = '$slug' ,category_id = '$category_id' ,subcategory_id = '$subcategory_id' ,description = '$description', indicator = '$indicator', manufacturer = '', made_in = '',`status` = $pr_status WHERE id = $ID";
            }
            // echo $sql_query;
            $db->sql($sql_query);
            $res = $db->getResult();
            $product_variant_id = $db->escapeString($fn->xss_clean($_POST['product_variant_id']));
           // if (isset($_POST['loose_measurement']) && isset($_POST['packate_measurement']) && $_POST['loose_measurement'] != 0 && $_POST['packate_measurement'] != 0 && $_POST['packate_measurement'] < $_POST['loose_measurement']) {
             //   $count = count($_POST['loose_measurement']);
           // } else {
             //   $count = count($_POST['packate_measurement']);
          //  }
          //  for ($i = 0; $i < $count; $i++) {
              //  if ($_POST['type'] == "packet") {
                   
                    $serve_for = $db->escapeString($fn->xss_clean($_POST['packate_serve_for']));
                    $data = array(
                        'price' => $db->escapeString($fn->xss_clean($_POST['packate_price'])),
                        'stock' => $db->escapeString($fn->xss_clean($_POST['packate_stock'])),
                        'discounted_price' => $db->escapeString($fn->xss_clean($_POST['packate_discounted_price'])),
                        'serve_for' => $serve_for,
                    );

                    $db->update('product_variant', $data, 'id=' . $fn->xss_clean($_POST['product_variant_id']));
                    $res = $db->getResult();
               // } 
          //  }
           
            $error['update_data'] = "<span class='label label-success'>Product updated Successfully</span>";
        }
    } else {
        $error['check_permission'] = " <section class='content-header'><span class='alert alert-danger'>You have no permission to update product</span></section>";
    }
}
// create array variable to store previous data
$data = array();
$sql_query = "SELECT p.*,p.type as d_type,v.*,v.id as product_variant_id FROM product_variant v JOIN products p ON p.id=v.product_id WHERE p.id=" . $ID;
$db->sql($sql_query);
$res = $db->getResult();
$product_status = $res[0]['status'];
foreach ($res as $row)
    $data = $row;
function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}
?>
<section class="content-header">
    <h1>Edit Product <small><a href='products.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Products</a></small></h1>
    <small><?php echo isset($error['update_data']) ? $error['update_data'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <br>
</section>
<section class="content">
    <!-- Main row -->
    <div class="row">
        <div class="col-md-12">
            <?php if ($permissions['products']['update'] == 0) { ?>
                <div class="alert alert-danger topmargin-sm">You have no permission to update product.</div>
            <?php } ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Product</h3>
                </div>
                <div class="box-header">
                    <?php echo isset($error['cancelable']) ? '<span class="label label-danger">Till status is required.</span>' : ''; ?>
                </div>
                <!-- form start -->
                <form id='edit_product_form' method="post" enctype="multipart/form-data">
                    <?php
                    $db->select('unit', '*');
                    $unit_data = $db->getResult();
                    ?>
                    <div class="box-body">
                        <div class="form-group">
                            <div class='col-md-6'>
                                <label >Product Name</label> <i class="text-danger asterik">*</i> <?php echo isset($error['name']) ? $error['name'] : ''; ?>
                                <input type="text" name="name" class="form-control" value="<?php echo $data['name']; ?>" />
                            </div>
                            <?php $db->sql("SET NAMES 'utf8'");
                            $sql = "SELECT * FROM `taxes` ORDER BY id DESC";
                            $db->sql($sql);
                            $taxes = $db->getResult();
                            ?>
                            <!-- <div class='col-md-4'>
                                <label class="control-label" for="seller_id">Seller</label><i class="text-danger asterik">*</i>
                                <?php // $db->sql("SET NAMES 'utf8'");
                               // $sql = "SELECT id,name FROM seller ORDER BY id + 0 ASC";
                               // $db->sql($sql);
                               // $sellers = $db->getResult();
                                ?>
                                <select id='seller_id' required name="seller_id" class='form-control'>
                                    <option value=''>Select Seller</option>
                                    <?php // foreach ($sellers as $row) { ?>
                                        <option value='<? //= $row['id'] ?>' <? //= ($data['seller_id'] == $row['id']) ? 'selected' : ''; ?> > <? //= $row['name'] ?></option>
                                    <?php // } ?>
                                </select> 
                                
                            </div>-->
                            <input type="hidden" name="seller_id" class="form-control" value="1" />
                            <div class='col-md-6'>
                                <label class="control-label " for="taxes">Tax</label>
                                <select id='tax_id' name="tax_id" class='form-control'>
                                    <option value="">Select Tax</option>
                                    <?php foreach ($taxes as $tax) { ?>
                                        <option value='<?= $tax['id'] ?>' <?= ($data['tax_id'] == $tax['id']) ? 'selected' : ''; ?>><?= $tax['title'] . " - " . $tax['percentage'] . " %" ?></option>
                                    <?php } ?>
                                </select><br>
                            </div>
                        </div>
                        <!-- <label for="type">Type</label><?php // echo isset($error['type']) ? $error['type'] : ''; ?>
                        <div class="form-group">
                            <label class="radio-inline"><input type="radio" name="type" id="packate" value="packet" <? //= ($res[0]['type'] == "packet") ? "checked" : ""; ?>>Packet</label>
                            <label class="radio-inline"><input type="radio" name="type" id="loose" value="loose" <? //= ($res[0]['type'] == "loose") ? "checked" : ""; ?>>Loose</label>
                        </div> -->
                        <hr>
                        <div id="variations">
                         <!--    <h5>Product Variations</h5>
                            <hr> -->
                            <?php
                            if (isJSON($data['price'])) {
                                $price = json_decode($data['price'], 1);
                               // $measurement = json_decode($data['measurement'], 1);
                                $discounted_price = json_decode($data['discounted_price'], 1);
                            } else {
                                $price = array('0' => $data['price']);
                              //  $measurement = array('0' => $data['measurement']);
                                $discounted_price = array('0' => $data['discounted_price']);
                            }
                            $i = 0;
                          //  if ($res[0]['type'] == "packet") {
                                foreach ($res as $row) {
                            ?>
                                    <div class="row packate_div">
                                        <input type="hidden" class="form-control" name="product_variant_id" id="product_variant_id" value='<?= $row['product_variant_id']; ?>' />
                                       <!--  <div class="col-md-2">
                                            <div class="form-group packate_div">
                                                <label for="exampleInputEmail1">Measurement</label> <i class="text-danger asterik">*</i> <input type="number" step="any" min="0" class="form-control" name="packate_measurement[]" value='<? //= $row['measurement']; ?>' required />
                                            </div>
                                        </div> -->
                                       <!--  <div class="col-md-1">
                                            <div class="form-group packate_div">
                                                <label for="unit">Unit:</label>
                                                <select class="form-control" name="packate_measurement_unit_id[]">
                                                    <?php
                                                   /* foreach ($unit_data as  $unit) {
                                                        echo "<option";
                                                        if ($unit['id'] == $row['measurement_unit_id']) {
                                                            echo " selected ";
                                                        }
                                                        echo " value='" . $unit['id'] . "'>" . $unit['short_code'] . "</option>";
                                                    }*/
                                                    ?>
                                                </select>
                                            </div>
                                        </div> -->
                                        <div class="col-md-2">
                                            <div class="form-group packate_div">
                                                <label for="price">Price (<?= $settings['currency'] ?>):</label> <i class="text-danger asterik">*</i> <input type="number" step="any" min="0" class="form-control" name="packate_price" id="packate_price" value='<?= $row['price']; ?>' required />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group packate_div">
                                                <label for="discounted_price">Discounted Price(<?= $settings['currency'] ?>):</label>
                                                <input type="number" step="any" min="0" class="form-control" name="packate_discounted_price" id="discounted_price" value='<?= $row['discounted_price']; ?>' />
                                            </div>
                                        </div>
                                         <div class="col-md-1">
                                            <div class="form-group packate_div">
                                                <label for="qty">Stock:</label> <i class="text-danger asterik">*</i>
                                                <input type="number" step="any" min="1" class="form-control" name="packate_stock" required value='<?= $row['stock']; ?>' />
                                            </div>
                                        </div>
                                      <!--   <div class="col-md-1">
                                            <div class="form-group packate_div">
                                                <label for="unit">Unit:</label>
                                                <select class="form-control" name="packate_stock_unit_id[]">
                                                    <?php
                                                    /*foreach ($unit_data as  $unit) {
                                                        echo "<option";
                                                        if ($unit['id'] == $row['stock_unit_id']) {
                                                            echo " selected ";
                                                        }
                                                        echo " value='" . $unit['id'] . "'>" . $unit['short_code'] . "</option>";
                                                    }*/
                                                    ?>
                                                </select>
                                            </div>
                                        </div> -->
                                        <div class="col-md-2">
                                            <div class="form-group packate_div">

                                                <label for="qty">Status:</label>
                                                <select name="packate_serve_for" class="form-control">
                                                    <option value="Available" <?php if (strtolower($row['serve_for']) == "availabel") {
                                                                                    echo "selected";
                                                                                } ?>>Available</option>
                                                    <option value="Sold Out" <?php if (strtolower($row['serve_for']) == "sold out") {
                                                                                    echo "selected";
                                                                                } ?>>Sold Out</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php  $i++;
                                }
                           //}  ?>
                            
                            
                            
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Category :</label> <i class="text-danger asterik">*</i> <?php echo isset($error['category_id']) ? $error['category_id'] : ''; ?>
                                        <select name="category_id" id="category_id" class="form-control">
                                            <?php
                                            if ($permissions['categories']['read'] == 1) {
                                                foreach ($category_data as $row) { ?>
                                                    <option value="<?php echo $row['id']; ?>" <?= ($row['id'] == $data['category_id']) ? "selected" : ""; ?>><?php echo $row['name']; ?></option>
                                                <?php }
                                            } else { ?>
                                                <option value="">---Select Category---</option>
                                                <?php } ?>?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Sub Category :</label>
                                        <select name="subcategory_id" id="subcategory_id" class="form-control">

                                            <?php
                                            if ($permissions['subcategories']['read'] == 1) { ?>
                                                <option value="">---Select Subcategory---</option>
                                                <?php foreach ($subcategory as $subcategories) { ?>

                                                    <option value="<?= $subcategories['id']; ?>" <?= $res[0]['subcategory_id'] == $subcategories['id'] ? 'selected' : '' ?>><?= $subcategories['name']; ?></option>
                                                <?php }
                                            } else { ?>
                                                <option value="">---Select Subcategory---</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Product Type :</label>
                                        <select name="indicator" id="indicator" class="form-control">
                                            <option value="">--Select Type--</option>
                                            <option value="1" <?php if ($res[0]['indicator'] == 1) {
                                                                    echo 'selected';
                                                                } ?>>Veg</option>
                                            <option value="2" <?php if ($res[0]['indicator'] == 2) {
                                                                    echo 'selected';
                                                                } ?>>Non Veg</option>
                                        </select>
                                    </div>
                                   
                                    <div class="row">
                                       <!--  <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="product_pincodes">Delivery Places :</label><i class="text-danger asterik">*</i>
                                                <select name="product_pincodes" id="product_pincodes" class="form-control" required>
                                                    <option value="">Select Option</option>
                                                    <option value="included" <?//= (!empty($res[0]['d_type']) && $res[0]['d_type'] == "included") ? 'selected' : ''; ?>>Pincode Included</option>
                                                    <option value="excluded" <?//= (!empty($res[0]['d_type']) && $res[0]['d_type'] == "excluded") ? 'selected' : ''; ?>>Pincode Excluded</option>
                                                    <option value="all" <?//= (!empty($res[0]['d_type']) && $res[0]['d_type'] == "all") ? 'selected' : ''; ?>>Includes All</option>
                                                </select>
                                                <br />
                                            </div>
                                        </div> -->

                                       <!--  <div class="col-md-4">
                                            <div class="form-group">
                                                <label for='pincode_ids_exc'>Selected Pincodes <small>( Ex : 100,205, 360 <comma separated>)</small></label>
                                                <select name='pincode_ids_exc[]' id='pincode_ids_exc' class='form-control' placeholder='Enter the pincode you want to allow delivery this product' multiple="multiple">
                                                    <?php
                                                    /* $sql = 'select id,pincode from `pincodes` where `status` = 1 order by id desc';
                                                    $db->sql($sql);
                                                    $result = $db->getResult();
                                                    if ($res[0]['pincodes'] != "") {
                                                        foreach ($result as $value) {*/
                                                    ?>
                                                            <option value='<?//= $value['id'] ?>' <?//= (strpos(" " . $res[0]['pincodes'], $value['id'])) ? 'selected' : ''; ?>><?//= $value['pincode']  ?></option>
                                                        <?php 
                                                   /* }
                                                    } else {
                                                        foreach ($result as $value) { */
                                                            ?>
                                                            <option value='<?//= $value['id'] ?>'><?//= $value['pincode']  ?></option>

                                                    <?php 
                                                // }
                                                //     } 
                                                    ?>

                                                </select>
                                            </div>
                                        </div> -->

                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Image <i class="text-danger asterik">*</i> &nbsp;&nbsp;&nbsp;*Please choose square image of larger than 350px*350px & smaller than 550px*550px.</label><?php echo isset($error['image']) ? $error['image'] : ''; ?>
                                        <input type="file" name="image" id="image" title="Please choose square image of larger than 350px*350px & smaller than 550px*550px." /><br />
                                        <img src="<?php echo $data['image']; ?>" width="210" height="160" />
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Other Images *Please choose square image of larger than 350px*350px & smaller than 550px*550px.</label><?php echo isset($error['other_images']) ? $error['other_images'] : ''; ?>
                                        <input type="file" name="other_images[]" id="other_images" multiple title="Please choose square image of larger than 350px*350px & smaller than 550px*550px." /><br />
                                        <?php
                                        if (!empty($data['other_images'])) {
                                            $other_images = json_decode($data['other_images']);

                                            for ($i = 0; $i < count($other_images); $i++) { ?>
                                                <img src="<?= $other_images[$i]; ?>" height="160" />
                                                <a class='btn btn-xs btn-danger delete-image' data-i='<?= $i; ?>' data-pid='<?= $_GET['id']; ?>'>Delete</a>
                                        <?php }
                                        } ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Description :</label> <?php // echo isset($error['description']) ? $error['description'] : ''; ?>
                                        <textarea name="description" id="description" class="form-control" rows="16"><?php echo $data['description']; ?></textarea>
                                        <script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
                                        <script type="text/javascript">
                                            CKEDITOR.replace('description');
                                        </script>
                                    </div>
                                  <!--   <div class="row">
                                        <div class="form-group col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Product Status</label>
                                                <div id="status" class="btn-group">
                                                    <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">

                                                        <input type="radio" name="is_approved" value="0" <? //= ($data['is_approved'] == 0) ? 'checked' : ''; ?>> Not-Processed
                                                    </label>
                                                    <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                        <input type="radio" name="is_approved" value="1" <? //= ($data['is_approved'] == 1) ? 'checked' : ''; ?>> Approoved
                                                    </label>
                                                    <label class="btn btn-danger" data-toggle-class="btn-danger" data-toggle-passive-class="btn-default">
                                                        <input type="radio" name="is_approved" value="2" <? //= ($data['is_approved'] == 2) ? 'checked' : ''; ?>> Not-Approoved
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="form-group">
                                        <label class="control-label ">Status :</label>
                                        <div id="product_status" class="btn-group">
                                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="pr_status" value="0"> Deactive
                                            </label>
                                            <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="pr_status" value="1"> Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <input type="submit" class="btn-primary btn" value="Update" name="btnEdit" />
                            </div>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>
<div class="separator"> </div>
<script>
    $(document).on('click', '.delete-image', function() {
        var pid = $(this).data('pid');
        var i = $(this).data('i');
        if (confirm('Are you sure want to delete the image?')) {
            $.ajax({
                type: 'POST',
                url: 'public/delete-other-images.php',
                data: 'i=' + i + '&pid=' + pid,
                success: function(result) {
                    if (result == '1') {
                        alert('Image deleted successfully');
                        window.location.replace("view-product-variants.php?id=" + pid);
                    } else
                        alert('Image could not be deleted!');

                }
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    var changeCheckbox = document.querySelector('#return_status_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#return_status').val(1);
            $('#return_day').show();
        } else {
            $('#return_status').val(0);
            $('#return_day').hide();
            $('#return_day').val('');
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#cancelable_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#cancelable_status').val(1);
            $('#till-status').show();

        } else {
            $('#cancelable_status').val(0);
            $('#till-status').hide();
            $('#till_status').val('');
        }
    };
    $('#pincode_ids_inc').select2({
        width: 'element',
        placeholder: 'type in category name to search',

    });

    $(document).ready(function() {
        var val = $('#product_pincodes').val();
        if (val == "all") {
            $('#pincode_ids_exc').prop('disabled', true);
        } else {
            $('#pincode_ids_exc').prop('disabled', false);
        }
    });

    $('#product_pincodes').on('change', function() {
        var val = $('#product_pincodes').val();
        if (val == "included" || val == "excluded") {
            $('#pincode_ids_exc').prop('disabled', false);
        } else {
            $('#pincode_ids_exc').prop('disabled', true);
        }
    });
    $('#pincode_ids_exc').select2({
        width: 'element',
        placeholder: 'type in category name to search',

    });
</script>
<script>
    $.validator.addMethod('lessThanEqual', function(value, element, param) {
        return this.optional(element) || parseInt(value) < parseInt($(param).val());
    }, "Discounted Price should be lesser than Price");

    $('#edit_product_form').validate({
        rules: {
            name: "required",
           // measurement: "required",
            price: "required",
            quantity: "required",
            discounted_price: "required",
           // stock: "required",
            discounted_price: {
                lessThanEqual: "#price"
            },
            /*description: {
                required: function(textarea) {
                    CKEDITOR.instances[textarea.id].updateElement();
                    var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
                    return editorcontent.length === 0;
                }
            }*/
        }
    });
</script>

<script>
    $(document).on('click', '.remove_variation', function() {
        if ($(this).data('id') == 'data_delete') {
            if (confirm('Are you sure? Want to delete this row')) {
                var id = $(this).closest('div.row').find("input[id='product_variant_id']").val();
                $.ajax({
                    url: 'public/db-operation.php',
                    type: "post",
                    data: 'id=' + id + '&delete_variant=1',
                    success: function(result) {
                        if(result){
                            location.reload();
                        }else{
                            alert("Variant not deleted!");
                        }
                    }
                });
            }
        } else {
            $(this).closest('.row').remove();
        }
    });

    $(document).on('change', '#category_id', function() {
        $.ajax({
            url: 'public/db-operation.php',
            method: 'POST',
            data: 'category_id=' + $('#category_id').val() + '&find_subcategory=1',
            success: function(data) {
                $('#subcategory_id').html("<option value=''>---Select Subcategory---</option>" + data);
            }
        });
    });
    $(document).on('change', '#packate', function() {
        $('#packate_div').show();
        $('.packate_div').show();
        $('#loose_div').hide();
        $('.loose_div').hide();
        $('#status_div').hide();
        $('#loose_stock_div').hide();
    });
    $(document).on('change', '#loose', function() {
        $('#loose_div').show();
        $('.loose_div').show();
        $('#loose_stock_div').show();
        $('#status_div').show();
        $('#packate_div').hide();
        $('.packate_div').hide();
    });
    $(document).ready(function() {
        var product_status = '<?= $product_status ?>';
        $("input[name=pr_status][value=1]").prop('checked', true);
        if (product_status == 0)
            $("input[name=pr_status][value=0]").prop('checked', true);
    });
</script>