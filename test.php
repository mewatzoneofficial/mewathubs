<main id="main-container">
    <div class="content">
        <?php
        if (!empty($this->session->userdata['module_permissions']["$moduleID"]) && ($this->session->userdata['module_permissions']["$moduleID"]->add_permission != 0) || !empty($qualification)) {
        ?>
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="content-heading">Add & Edit Faculity Packages</h2>
                    <div class="block">
                        <div class="block-content block-content-narrow">
                            <?php
                            $attributes = array('class' => 'js-validation-bootstrap form-horizontal', 'id' => 'form-package');
                            if (!empty($package)) {
                                echo form_open_multipart('apanel/package/edit/' . $package->packID, $attributes);
                            } else {
                                echo form_open_multipart('apanel/package/add', $attributes);
                            }

                            $msg = validation_errors();
                            if (!empty($msg)) {
                                echo '<!-- Danger Alert --><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><p>' . $msg . '</p></div><!-- END Danger Alert -->';
                            }
                            ?>
                            <div class="row">
                                <div class="col-lg-6">

                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-sd">User Type<span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <select class="js-select2 form-control" style="width: 100%;" data-placeholder="Set Package Type.." id="val-usertype" name="val-usertype" required>
                                                <option value=""></option>
                                                <option value="Employer" <?php echo set_select('val-usertype', "Employer");
                                                                            echo (!empty($package) && ($package->usertype == "Employer")) ? "selected" : ""; ?>> Employer</option>
                                                <option value="Employee" <?php echo set_select('val-usertype', "Employee");
                                                                            echo (!empty($package) && ($package->usertype == "Employee")) ? "selected" : ""; ?>> Employee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-sd">Package Type<span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <select class="js-select2 form-control" style="width: 100%;" data-placeholder="Set Package Type.." id="val-type" name="val-type" required>
                                                <option value=""></option>
                                                <option value="Prepaid" <?php echo set_select('val-type', "Prepaid");
                                                                        echo (!empty($package) && ($package->type == "Prepaid")) ? "selected" : ""; ?>> Prepaid</option>
                                                <option value="Postpaid" <?php echo set_select('val-type', "Postpaid");
                                                                            echo (!empty($package) && ($package->type == "Postpaid")) ? "selected" : ""; ?>> Postpaid</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-catname">Package Name<span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input value="<?php echo set_value('val-title');
                                                            echo (!empty($package->package)) ? $package->package : ""; ?>" class="form-control" type="text" id="val-title" name="val-title" placeholder="Package Name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-catname">Package Price<span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input onkeyup=calculateDiscount(); value="<?php echo set_value('val-price');
                                                                                        echo (!empty($package->price)) ? $package->price : ""; ?>" class="form-control price" type="text" id="val-price" name="val-price" placeholder="Package Price" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-catname">Discount(%)</label>
                                        <div class="col-md-8">
                                            <input onkeyup=calculateDiscount(); value="<?php echo set_value('val-discount');
                                                                                        echo (!empty($package->discount)) ? $package->discount : "0"; ?>" class="form-control discount" type="number" min="0" max="100" id="val-discount" name="val-discount" placeholder="Package Price" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-catname">Discount Price</label>
                                        <div class="col-md-8">
                                            <input value="<?php echo set_value('val-discount_price');
                                                            echo (!empty($package->discount_price)) ? $package->discount_price : "0"; ?>" class="form-control discount_price" type="text" id="val-discount_price" readonly name="val-discount_price" placeholder="Discount Price" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-catname">Price after discount</label>
                                        <div class="col-md-8">
                                            <input value="<?php echo set_value('val-discounted_price');
                                                            echo (!empty($package->discounted_price)) ? $package->discounted_price : "0"; ?>" class="form-control discounted_price" type="text" id="val-discounted_price" readonly name="val-discounted_price" placeholder="Discounted Price" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12" style=" height: 200px; overflow-y: scroll;">
                                            <textarea class="js-simplemde" id="val-details" name="val-details" placeholder="Enter Package Description.." rows="3"><?php echo set_value('val-details');
                                                                                                                                                                    echo (!empty($package)) ? $package->details : ""; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-sd">Categories<span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <select class="js-select2 form-control" style="width: 100%;" data-placeholder="Set Package category.." id="val-category" name="val-category[]" required multiple>
                                                <option value=""></option>
                                                <?php foreach ($categories as $category) {
                                                    echo '<option value="' . $category->ID . '"';
                                                    echo set_select('val-category[]', $category->ID);
                                                    if (!empty($package->categories)) {
                                                        $cs = explode(',', $package->categories);
                                                        foreach ($cs as $c)
                                                            echo (!empty($c) && ($c == $category->ID)) ? "selected" : "";
                                                    }
                                                    echo '>' . $category->category . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-catname">Package Days<span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input value="<?php echo set_value('val-days');
                                                            echo (!empty($package->days)) ? $package->days : ""; ?>" class="form-control" type="number" id="val-days" name="val-days" placeholder="Package Days" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="range-min-max">Pack Range<span class="text-danger">*</span></label>
                                        <div class="col-md-4">
                                            <select name="range_min" class="form-control">
                                                <?php
                                                for ($i = 1; $i <= 100; $i++) {
                                                ?>
                                                    <option <?php echo ($package->range_min == $i) ? "selected" : ""; ?> value="<?php echo $i; ?>"> <?php echo $i; ?> </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="range_max" class="form-control">
                                                <?php
                                                for ($i = 1; $i <= 100; $i++) {
                                                ?>
                                                    <option <?php echo ($package->range_max == $i) ? "selected" : ""; ?> value="<?php echo $i; ?>"> <?php echo $i; ?> </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="val-catname">No of Jobs<span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input value="<?php echo set_value('no_of_jobs');
                                                            echo (!empty($package->no_of_jobs)) ? $package->no_of_jobs : ""; ?>" class="form-control" type="number" id="no_of_jobs" name="no_of_jobs" placeholder="No of Jobs" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Status</label>
                                        <div class="col-md-8">
                                            <label class="css-input css-checkbox css-checkbox-primary" for="val-status">
                                                <input type="checkbox" id="val-status" name="val-status" value="1" <?php echo set_checkbox('val-status', '1');
                                                                                                                    echo (!empty($qualification->status) && ($qualification->status == 1)) ? "checked" : "";
                                                                                                                    echo (!empty($package) && ($package->status == 1)) ? "checked" : ""; ?>><span></span> Active
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12" style=" height: 200px; overflow-y: scroll;">
                                            <textarea class="js-simplemde" id="val-terms" name="val-terms" placeholder="Enter Package Terms and Conditions.." rows="3"><?php echo set_value('val-terms');
                                            echo (!empty($package)) ? $package->terms : ""; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-7 col-md-offset-5">
                                            <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group">

                                    <?php
                                    if ($packages_benefits) {
                                        foreach ($packages_benefits as $key => $value) {
                                    ?>
                                            <div class="row add_roww">
                                                <div class="col-md-12">
                                                    <label class="col-md-2 control-label" style="text-align: left;" for="key_benefits"> <?php echo ($key == 0) ? "Key Benefits" : "&nbsp;"; ?></label>
                                                    <div class="col-md-2">
                                                        <select class="form-control" name="key_benefits[allowed][]">
                                                            <option <?php echo ($value->allowed == 1) ? "selected" : ""; ?> value="1">Yes</option>
                                                            <option <?php echo ($value->allowed == 0) ? "selected" : ""; ?> value="0">No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input class="form-control" type="text" id="key_benefits" name="key_benefits[benefit][]" value="<?php echo $value->benefit; ?>" placeholder="Key Benefits" required>
                                                    </div>
                                                    <label class="col-md-2 control-label remove_btn" style="text-align: left; color:red" for="key_benefits">Remove</label>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    } else { ?>
                                        <div class="row add_roww">
                                            <div class="col-md-12">
                                                <label class="col-md-2 control-label" style="text-align: left;" for="key_benefits">Key Benefits</label>
                                                <div class="col-md-2">
                                                    <select class="form-control" name="key_benefits[allowed][]">
                                                        <option value="1">Yes</option>
                                                        <option value="0">No</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input class="form-control" type="text" id="key_benefits" name="key_benefits[benefit][]" placeholder="Key Benefits" required>
                                                </div>
                                            </div>
                                        </div>

                                    <?php
                                    }
                                    ?>
                                    <div class="BenefitsRow" id="BenefitsRow"></div>
                                    <span onclick="BenefitsRow()" class="col-md-12 control-label" for="key_benefits">Add More</span>
                                </div>
                            </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">Packages List <small></small></h3>
            </div>
            <div class="block-content">
                <div class="table-responsive">
                    <table id="packageListDatatable" class="table remove-margin-b font-s13">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Packages</th>
                                <th>For User</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Package days</th>
                                <th>No of jobs</th>
                                <th class="text-center" style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $package) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $package->packID ?></td>
                                    <td class="font-w600"><?php echo $package->package ?></td>
                                    <td>
                                        <?php echo $package->usertype ?>
                                    </td>
                                    <td>
                                        <?php echo $package->type ?>
                                    </td>
                                    <td><?php echo $package->price;  ?></td>
                                    <td><?php echo $package->days;  ?></td>
                                    <td><?php echo $package->no_of_jobs;  ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <?php

                                            if (!empty($this->session->userdata['module_permissions']["$moduleID"]) && ($this->session->userdata['module_permissions']["$moduleID"]->edit_permission != 0)) {
                                            ?>
                                                <a href="<?php echo base_url(); ?>apanel/package/edit/<?php echo $package->packID; ?>" class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="Edit package"><i class="fa fa-pencil"></i></a>
                                            <?php }
                                            if (!empty($this->session->userdata['module_permissions']["$moduleID"]) && ($this->session->userdata['module_permissions']["$moduleID"]->delete_permission != 0)) {

                                            ?>
                                                <a href="<?php echo base_url(); ?>apanel/package/del/<?php echo $package->packID; ?>" class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="Remove package" onclick="return confirm('Are you sure you want to remove this package?');"><i class="fa fa-times"></i></a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<script>
    $(document).ready(function() {
        $('#packageListDatatable').DataTable({
            ordering: true,
            searching: true,
            lengthChange: true,
            pageLength: 10,
            pagingType: 'simple_numbers',
            language: {
                paginate: {
                    previous: 'Previous',
                    next: 'Next'
                }
            },
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                targets: 7,
                orderable: false
            }]
        });
    });
    $(document).on('click', '.remove_btn', function() {
        $this = $(this);
        $this.parents('.add_roww').remove()
    });

    function calculateDiscount() {
        var price = $(".price").val();
        var discount = $(".discount").val();
        if (discount > 0) {
            discountPrice = (price * discount) / 100;
            discountedPrice = price - discountPrice;
            $(".discount_price").val(Math.round(discountPrice));
            $(".discounted_price").val(Math.round(discountedPrice));
        } else {
            $(".discount_price").val(0);
            $(".discounted_price").val(price);
        }
    }


    function BenefitsRow() {
        var d = document.getElementById('BenefitsRow');
        var html = '<div class="row add_roww"><div class="col-md-12"><label class="col-md-2 control-label" style="text-align: left;" for="key_benefits">&nbsp</label><div class="col-md-2"><select class="form-control" name="key_benefits[allowed][]"><option value="1">Yes</option><option value="0">NO</option></select></div><div class="col-md-6"><input class="form-control" type="text" id="key_benefits" name="key_benefits[benefit][]" placeholder="Key Benefits" required></div>  <label class="col-md-2 control-label remove_btn" style="text-align: left; color:red" for="key_benefits">Remove</label></div>  </div>  ';
        $('#BenefitsRow').append(html);
    }
</script>