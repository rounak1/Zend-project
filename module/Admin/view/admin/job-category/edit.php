<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">BusinessType </h3>
    </div><!-- /.box-header -->
    <!-- form start -->
    <form data-toggle="validator" role="form" method="post" >
        <div class="box-body">
            <div class="form-group">
                <label for="exampleInputEmail1">Buisness Type</label>
                <input type="name" name="bname" class="form-control" id="name" value="" placeholder="Buisness Type" required>
            </div>


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Add</button>
                <a class="btn btn-default" href="<?php echo $this->serverUrl()."/admin/jcategory";?>" role="button">Back</a>
            </div>
    </form>
</div><!-- /.box -->